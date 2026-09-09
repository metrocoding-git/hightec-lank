<?php

namespace App\Http\Controllers;

use App\Models\AssignSubjects;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\Subject;
use App\Models\SubjectAttendanceSession;
use App\Models\TrainingVehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScanAttendanceController extends Controller
{
    public function index()
    {

        $userCourseIds = $this->currentUserCourseIds();

        if (!empty($userCourseIds)) {
            $courses = Course::whereIn('id', $userCourseIds)->get();

            $subjectIds = $this->subjectIdsForCourses($userCourseIds);
            $subjects = Subject::where('type', 'T')
                ->whereIn('id', $subjectIds)
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'type']);
        } else {
            $courses = Course::all();
            $subjects = Subject::where('type', 'T')->orderBy('name')->get(['id', 'name', 'code', 'type']);
        }

        return view('pages.student.attendance.index', compact('courses', 'subjects'));
    }

    // =====================================================================
    // Section 1 — Course attendance
    // =====================================================================

    public function scanCourse(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'vehicle_id' => 'nullable|exists:training_vehicles,id',
            'note' => 'nullable|string|max:1000',
        ]);

        $student = $this->findStudent($request->input('identifier'));

        if (!$student) {
            return $this->notFoundResponse($request->input('identifier'));
        }

        $courseId = $request->input('course_id');

        $studentCourse = StudentCourse::with(['intake:id,name', 'course:id,name'])
            ->where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->where('status', 'start')
            ->latest()
            ->first();

        if (!$studentCourse) {
            return response()->json([
                'status' => 'not_enrolled',
                'message' => "{$student->student_name} has not started this course.",
                'student' => $this->studentPayload($student),
            ], 422);
        }

        // If this scan is about to CLOSE an open course session, enforce the
        // "after checking" gate first.
        $openCourse = $this->openCourseSession($student, $courseId);

        if ($openCourse) {
            $gateError = $this->afterCheckingGateError($student, $courseId);

            if ($gateError) {
                return response()->json($gateError, 422);
            }
        } elseif (!$this->hasCoursePayment($student, $courseId)) {

            return response()->json([
                'status' => 'no_payment',
                'message' => "{$student->student_name} has no payment recorded for this course yet — attendance can't be started until a payment is made.",
                'student' => $this->studentPayload($student),
            ], 422);
        }

        $result = DB::transaction(fn() => $this->markSession(
            $student,
            $studentCourse->intake_id,
            $courseId,
            null,
            $request->input('vehicle_id'),
            $request->input('note')
        ));
        $result['course_id'] = $courseId;

        $response = [
            'status' => 'ok',
            'student' => $this->studentPayload($student),
            'intake_name' => $studentCourse->intake->name ?? null,
            'results' => [$result],
        ];

        // Opening a course session auto-creates its own "before checking"
        // attendance row (max one per student/course/day) — surfaced here
        // too, so it shows up in Today's Scans immediately, not just after
        // the next page load.
        if ($result['action'] === 'in') {
            $checkpoint = $this->stampBeforeChecking(
                $student->id,
                $studentCourse->intake_id,
                $courseId,
                now(),
                Auth::id()
            );

            $response['before_checking'] = [
                'starts_at' => $checkpoint->time_in->format('h:i A'),
                'ends_at' => $checkpoint->time_out->format('h:i A'),
                'by' => optional(Auth::user())->name,
                'course' => $studentCourse->course->name ?? null,
            ];
        }

        // Fee balance check — advisory only, never blocks the scan.
        // fee_invoice.student_id -> paid_courses.invoice_id -> course_id.
        $balanceAlert = $this->courseBalanceAlert($student, $courseId);

        if ($balanceAlert) {
            $response['balance_alert'] = $balanceAlert;
        }

        return response()->json($response);
    }

    // =====================================================================
    // Section 2 — Subject attendance (one or more subjects, any student)
    // =====================================================================

    public function allSubjects()
    {

        $userCourseIds = $this->currentUserCourseIds();

        $query = Subject::where('type', 'T');

        if (!empty($userCourseIds)) {
            $query->whereIn('id', $this->subjectIdsForCourses($userCourseIds));
        }

        return response()->json([
            'subjects' => $query->orderBy('name')->get(['id', 'name', 'code', 'type']),
        ]);
    }

    public function scanSubject(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
            'vehicle_id' => 'nullable|exists:training_vehicles,id',
            'note' => 'nullable|string|max:1000',
        ]);

        $student = $this->findStudent($request->input('identifier'));

        if (!$student) {
            return $this->notFoundResponse($request->input('identifier'));
        }

        $enrolments = $this->activeEnrolments($student);

        if ($enrolments->isEmpty()) {
            return $this->noEnrolmentResponse($student);
        }

        $vehicleId = $request->input('vehicle_id');
        $note = $request->input('note');
        $results = [];

        DB::transaction(function () use ($request, $student, $enrolments, $vehicleId, $note, &$results) {
            foreach ($request->input('subject_ids') as $subjectId) {
                $matches = $this->enrolmentsAssignedSubject($enrolments, $subjectId);

                if ($matches->isEmpty()) {
                    $results[] = [
                        'action' => 'not_assigned',
                        'subject_id' => $subjectId,
                    ];
                    continue;
                }

                // Same subject can be assigned under more than one of the
                // student's active courses (e.g. two courses started
                // together) — mark attendance under all of them.
                foreach ($matches as $enrolment) {
                    $result = $this->markSession(
                        $student,
                        $enrolment->intake_id,
                        $enrolment->course_id,
                        $subjectId,
                        $vehicleId,
                        $note
                    );
                    $result['subject_id'] = $subjectId;
                    $result['course_id'] = $enrolment->course_id;
                    $results[] = $result;
                }
            }
        });

        return response()->json([
            'status' => 'ok',
            'student' => $this->studentPayload($student),
            'results' => $results,
            'balance_alerts' => $this->courseBalanceAlerts($student, $results),
        ]);
    }

    // =====================================================================
    // Section 3 — Student-first, practical subjects only
    // =====================================================================


    public function resolvePracticalSubjects(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $student = $this->findStudent($request->input('identifier'));

        if (!$student) {
            return $this->notFoundResponse($request->input('identifier'));
        }

        $enrolments = $this->activeEnrolments($student);

        if ($enrolments->isEmpty()) {
            return $this->noEnrolmentResponse($student);
        }

        $subjects = collect();
        $branchIds = [];
        $hadAssignedPractical = false;

        foreach ($enrolments as $enrolment) {
            $subjectIds = AssignSubjects::where('intake_id', $enrolment->intake_id)
                ->where('course_id', $enrolment->course_id)
                ->pluck('subject_id');

            if ($subjectIds->isEmpty()) {
                continue;
            }

            $practicalSubjects = Subject::whereIn('id', $subjectIds)
                ->where('type', 'P')
                ->get(['id', 'name']);

            if ($practicalSubjects->isEmpty()) {
                continue;
            }

            $hadAssignedPractical = true;

            if (!$this->openCourseSession($student, $enrolment->course_id)) {
                continue;
            }
            foreach ($practicalSubjects as $s) {
                $doneCount = SubjectAttendanceSession::where('student_id', $student->id)
                    ->where('course_id', $enrolment->course_id)
                    ->where('subject_id', $s->id)
                    ->notCheckpoint()
                    ->whereNotNull('time_in')
                    ->count();

                // TODAY-only count — drives the "Done Nx today" badge.
                $doneToday = SubjectAttendanceSession::where('student_id', $student->id)
                    ->where('course_id', $enrolment->course_id)
                    ->where('subject_id', $s->id)
                    ->whereDate('attendance_date', now()->toDateString())
                    ->notCheckpoint()
                    ->whereNotNull('time_in')
                    ->count();


                $isOpen = SubjectAttendanceSession::where('student_id', $student->id)
                    ->where('course_id', $enrolment->course_id)
                    ->where('subject_id', $s->id)
                    ->whereDate('attendance_date', now()->toDateString())
                    ->notCheckpoint()
                    ->open()
                    ->exists();

                $existing = $subjects->get($s->id);
                $subjects->put($s->id, [
                    'name' => $s->name,
                    'done_count' => max($existing['done_count'] ?? 0, $doneCount),
                    'done_today' => max($existing['done_today'] ?? 0, $doneToday),
                    'is_open' => ($existing['is_open'] ?? false) || $isOpen,
                ]);
            }

            if (!is_null($enrolment->branch_id)) {
                $branchIds[] = $enrolment->branch_id;
            }
        }

        if ($subjects->isEmpty()) {
            if ($hadAssignedPractical) {
                return response()->json([
                    'status' => 'practical_not_started',
                    'message' => "{$student->student_name}'s course attendance hasn't been started (or has already ended) today — start course attendance before practical attendance.",
                    'student' => $this->studentPayload($student),
                ], 422);
            }

            return response()->json([
                'status' => 'no_practical_subjects',
                'message' => "{$student->student_name} has no practical subjects assigned under their active courses.",
                'student' => $this->studentPayload($student),
            ], 422);
        }


        $vehicles = TrainingVehicle::active()
            ->forBranches($this->resolveVehicleBranchIds(array_values(array_unique($branchIds))))
            ->orderBy('name')
            ->get(['id', 'name', 'vehicle_number']);

        return response()->json([
            'status' => 'select_subject',
            'student' => $this->studentPayload($student),
            'subjects' => $subjects->map(fn($data, $id) => [
                'id' => $id,
                'name' => $data['name'],
                'done_count' => $data['done_count'],
                'done_today' => $data['done_today'],
                'is_open' => $data['is_open'],
            ])->values(),
            'vehicles' => $vehicles,
        ]);
    }

    public function scanPractical(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'vehicle_id' => 'nullable|exists:training_vehicles,id',
            'note' => 'nullable|string|max:1000',
        ]);

        $student = $this->findStudent($request->input('identifier'));

        if (!$student) {
            return $this->notFoundResponse($request->input('identifier'));
        }

        $enrolments = $this->activeEnrolments($student);

        if ($enrolments->isEmpty()) {
            return $this->noEnrolmentResponse($student);
        }

        $subjectId = $request->input('subject_id');
        $vehicleId = $request->input('vehicle_id');
        $note = $request->input('note');
        $matches = $this->enrolmentsAssignedSubject($enrolments, $subjectId);

        if ($matches->isEmpty()) {
            return response()->json([
                'status' => 'not_assigned',
                'message' => 'This subject is not assigned under any of this student\'s active courses.',
                'student' => $this->studentPayload($student),
            ], 422);
        }

        // Narrow down to enrolments whose course attendance is currently
        // open — practical attendance can't be started/continued otherwise.
        $eligible = [];

        foreach ($matches as $enrolment) {
            $courseSession = $this->openCourseSession($student, $enrolment->course_id);

            if ($courseSession) {
                $eligible[] = ['enrolment' => $enrolment, 'course_session' => $courseSession];
            }
        }

        if (empty($eligible)) {
            return response()->json([
                'status' => 'course_not_open',
                'message' => "Course attendance for this subject's course hasn't been started (or has already ended) today.",
                'student' => $this->studentPayload($student),
            ], 422);
        }

        foreach ($eligible as $item) {
            $gateError = $this->practicalGateError($student, $item['enrolment']->course_id);

            if ($gateError) {
                return response()->json(array_merge($gateError, [
                    'student' => $this->studentPayload($student),
                ]), 422);
            }

            $concurrencyError = $this->practicalConcurrencyError($student, $item['enrolment']->course_id, $subjectId);

            if ($concurrencyError) {
                return response()->json(array_merge($concurrencyError, [
                    'student' => $this->studentPayload($student),
                ]), 422);
            }

            $vehicleError = $this->practicalVehicleError($student, $item['enrolment']->course_id, $subjectId, $vehicleId);

            if ($vehicleError) {
                return response()->json(array_merge($vehicleError, [
                    'student' => $this->studentPayload($student),
                ]), 422);
            }
        }

        $results = [];
        $afterCheckingEvents = [];

        DB::transaction(function () use ($eligible, $student, $subjectId, $vehicleId, $note, &$results, &$afterCheckingEvents) {
            foreach ($eligible as $item) {
                $enrolment = $item['enrolment'];

                $result = $this->markSession(
                    $student,
                    $enrolment->intake_id,
                    $enrolment->course_id,
                    $subjectId,
                    $vehicleId,
                    $note,
                    true // allowRepeat — practical can be done multiple times a day
                );
                $result['subject_id'] = $subjectId;
                $result['course_id'] = $enrolment->course_id;
                $results[] = $result;

                // Closing a practical session (re)stamps the "after
                // checking" attendance row for this course — a real,
                // visible entry (marked by whoever just closed this
                // practical session), always reflecting only the LATEST
                // practical close for this course (max one row per
                // student/course/day, updated in place each time).
                if ($result['action'] === 'out') {
                    $checkpoint = $this->stampAfterChecking(
                        $student->id,
                        $enrolment->intake_id,
                        $enrolment->course_id,
                        now(),
                        Auth::id()
                    );

                    $afterCheckingEvents[] = [
                        'starts_at' => $checkpoint->time_in->format('h:i A'),
                        'ends_at' => $checkpoint->time_out->format('h:i A'),
                        'by' => optional(Auth::user())->name,
                        'course' => $enrolment->course->name ?? null,
                    ];
                }
            }
        });

        return response()->json([
            'status' => 'ok',
            'student' => $this->studentPayload($student),
            'results' => $results,
            'after_checking' => $afterCheckingEvents,
            'balance_alerts' => $this->courseBalanceAlerts($student, $results),
        ]);
    }

    private function practicalVehicleError(Student $student, int $courseId, int $subjectId, ?int $vehicleId): ?array
    {
        $open = SubjectAttendanceSession::with('vehicle')
            ->where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->where('subject_id', $subjectId)
            ->whereDate('attendance_date', now()->toDateString())
            ->notCheckpoint()
            ->open()
            ->first();

        if (!$open) {
            if (is_null($vehicleId)) {
                return [
                    'status' => 'vehicle_required',
                    'message' => 'Select a vehicle before starting practical attendance.',
                ];
            }
            return null;
        }

        if (!is_null($vehicleId) && (int) $vehicleId !== (int) $open->vehicle_id) {
            $startedWith = $open->vehicle->name ?? null;
            return [
                'status' => 'vehicle_mismatch',
                'message' => $startedWith
                    ? "Selected vehicle doesn't match the vehicle this session was started with ({$startedWith}) — select that vehicle, or leave it blank."
                    : "Selected vehicle doesn't match the vehicle this session was started with — select the original vehicle, or leave it blank.",
            ];
        }

        return null;
    }


    // =====================================================================
    // Today's scans (server-persisted, shared across all authenticated users)
    // =====================================================================
    public function todayLog()
    {
        $userId = Auth::id();


        $sessions = SubjectAttendanceSession::with([
            'student:id,student_name,admission_number',
            'subject:id,name,type',
            'course:id,name',
            'markedInBy:id,name',
            'markedOutBy:id,name',
        ])
            ->whereDate('attendance_date', now()->toDateString())
            ->where(function ($q) use ($userId) {
                $q->where('marked_in_by', $userId)
                    ->orWhere('marked_out_by', $userId);
            })
            ->orderBy('id')
            ->get();

        $rows = [];

        foreach ($sessions as $session) {
            if ($session->isCheckpoint()) {
                $rows[] = $this->checkpointLogRow($session);
                continue;
            }

            $studentName = $session->student->student_name ?? '-';
            $regNo = $session->student->admission_number ?? '-';
            $courseName = $session->course->name ?? '-';
            $subjectLabel = $session->subject->name ?? 'Daily Attendance';

            if ($session->time_in) {
                $rows[] = [
                    'session_id' => $session->id,
                    'sort' => $session->time_in->timestamp,
                    'time' => $session->time_in->format('h:i A'),
                    'student' => $studentName,
                    'reg_no' => $regNo,
                    'course' => $courseName,
                    'subject' => $subjectLabel,
                    'action' => 'Time In',
                    'status' => 'OK',
                    'cls' => 'action-in',
                    'pill' => 'pill-in',
                    'undoable' => is_null($session->time_out),
                ];
            }

            if ($session->time_out) {
                $rows[] = [
                    'session_id' => $session->id,
                    'sort' => $session->time_out->timestamp,
                    'time' => $session->time_out->format('h:i A'),
                    'student' => $studentName,
                    'reg_no' => $regNo,
                    'course' => $courseName,
                    'subject' => $subjectLabel,
                    'action' => 'Time Out',
                    'status' => 'OK',
                    'cls' => 'action-out',
                    'pill' => 'pill-out',
                    'undoable' => true,
                ];
            }
        }

        usort($rows, fn($a, $b) => $a['sort'] <=> $b['sort']);

        return response()->json(['rows' => $rows]);
    }

    /** One summary row for a "before checking" / "after checking" attendance row. */
    private function checkpointLogRow(SubjectAttendanceSession $session): array
    {
        $label = $session->checkpoint_type === 'before_checking' ? 'Before Checking' : 'After Checking';
        $timeRange = $session->time_in->format('h:i A') . ($session->time_out ? ' – ' . $session->time_out->format('h:i A') : '');
        $isActive = $session->time_out && now()->lt($session->time_out);

        return [
            'session_id' => $session->id,
            'sort' => $session->time_in->timestamp,
            'time' => $timeRange,
            'student' => $session->student->student_name ?? '-',
            'reg_no' => $session->student->admission_number ?? '-',
            'course' => $session->course->name ?? '-',
            'subject' => $label,
            'action' => 'By ' . ($session->markedInBy->name ?? '-'),
            'status' => $isActive ? 'Active' : 'Expired',
            'cls' => 'action-already',
            'pill' => 'pill-already',
            'undoable' => false,
        ];
    }

    // =====================================================================
    // Undo — same-day, same-user, and (for course/practical) in strict LIFO
    // order relative to each other.
    // =====================================================================

    public function undoScan(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:subject_attendance_sessions,id',
        ]);

        $session = SubjectAttendanceSession::with('subject')->findOrFail($request->session_id);

        if ($session->attendance_date->toDateString() !== now()->toDateString()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only today\'s scans can be undone.',
            ], 422);
        }

        $kind = $this->sessionKind($session);

        if ($kind === 'checkpoint') {
            return response()->json([
                'status' => 'error',
                'message' => 'This is an automatic checkpoint attendance — undo the related course/practical scan instead.',
            ], 422);
        }

        if ($kind !== 'subject') {
            $siblings = SubjectAttendanceSession::with('subject')
                ->where('student_id', $session->student_id)
                ->whereDate('attendance_date', $session->attendance_date->toDateString())
                ->notCheckpoint()
                ->where(function ($q) {
                    $q->whereNull('subject_id')
                        ->orWhereHas('subject', fn($sq) => $sq->where('type', 'P'));
                })
                ->get();

            $targetTime = $this->lastActionAt($session)->getTimestamp();
            $latestTime = $siblings->max(fn($s) => $this->lastActionAt($s)->getTimestamp());

            if ($targetTime < $latestTime) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'A more recent course/practical scan exists for this student today — undo that one first.',
                ], 422);
            }
        }

        $userId = Auth::id();
        $courseId = $session->course_id;
        $date = $session->attendance_date->toDateString();

        if ($session->time_out) {
            if ((int) $session->marked_out_by !== (int) $userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only the staff member who marked this time-out can undo it.',
                ], 403);
            }

            $session->update([
                'time_out' => null,
                'vehicle_id' => null,
                'note' => null,
                'marked_out_by' => null,
            ]);


            if ($kind === 'practical') {
                $this->recomputeAfterChecking($session->student_id, $courseId, $date, $session->id);
            }

            return response()->json(['status' => 'ok', 'message' => 'Time out undone.']);
        }

        if ((int) $session->marked_in_by !== (int) $userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only the staff member who marked this time-in can undo it.',
            ], 403);
        }

        $studentId = $session->student_id;
        $session->delete();

        // Undoing a course's opening means that day's course attendance
        // never happened anymore — its "before checking" row goes with it.
        if ($kind === 'course') {
            $this->deleteBeforeChecking($studentId, $courseId, $date);
        }

        return response()->json(['status' => 'ok', 'message' => 'Time in undone.']);
    }

    /**
     * AJAX: edit vehicle/note on a session after the fact (optional convenience).
     */
    public function confirmOut(Request $request)
    {
        $request->validate([
            'session_ids' => 'required|array',
            'session_ids.*' => 'exists:subject_attendance_sessions,id',
            'vehicle_id' => 'nullable|exists:training_vehicles,id',
            'note' => 'nullable|string|max:1000',
        ]);

        SubjectAttendanceSession::whereIn('id', $request->session_ids)
            ->update([
                'vehicle_id' => $request->vehicle_id,
                'note' => $request->note,
                'marked_out_by' => Auth::id(),
            ]);

        return response()->json(['status' => 'ok']);
    }

    // =====================================================================
    // Shared helpers
    // =====================================================================

    private function findStudent(string $identifier): ?Student
    {
        return Student::with('branch')
            ->where('admission_number', trim($identifier))
            ->first();
    }

    private function studentPayload(Student $student): array
    {
        return [
            'id' => $student->id,
            'name' => $student->student_name,
            'admission_number' => $student->admission_number,
        ];
    }

    private function notFoundResponse(string $identifier)
    {
        return response()->json([
            'status' => 'not_found',
            'message' => "No student found for \"{$identifier}\".",
        ], 404);
    }

    private function noEnrolmentResponse(Student $student)
    {
        return response()->json([
            'status' => 'no_enrolment',
            'message' => 'This student has no active (status: start) intake/course enrolment on record.',
            'student' => $this->studentPayload($student),
        ], 422);
    }

    /** This student's active ("start") intake/course enrolments. */
    private function activeEnrolments(Student $student): Collection
    {
        return StudentCourse::with(['intake:id,name', 'course:id,name'])
            ->where('student_id', $student->id)
            ->where('status', 'start')
            ->get();
    }

    /** Of the given enrolments, which have this subject assigned (via assign_subjects)? */
    private function enrolmentsAssignedSubject(Collection $enrolments, int $subjectId): Collection
    {
        return $enrolments->filter(function ($enrolment) use ($subjectId) {
            return AssignSubjects::where('intake_id', $enrolment->intake_id)
                ->where('course_id', $enrolment->course_id)
                ->where('subject_id', $subjectId)
                ->exists();
        })->values();
    }

    private function currentUserCourseIds(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        return $user->courses->pluck('id')->all();
    }


    private function subjectIdsForCourses(array $courseIds): array
    {
        return AssignSubjects::whereIn('course_id', $courseIds)
            ->pluck('subject_id')
            ->unique()
            ->values()
            ->all();
    }

    private function openCourseSession(Student $student, int $courseId): ?SubjectAttendanceSession
    {
        return SubjectAttendanceSession::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->whereNull('subject_id')
            ->whereDate('attendance_date', now()->toDateString())
            ->notCheckpoint()
            ->open()
            ->first();
    }

    private function hasAnyPracticalToday(Student $student, int $courseId): bool
    {
        return SubjectAttendanceSession::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->whereDate('attendance_date', now()->toDateString())
            ->whereHas('subject', fn($q) => $q->where('type', 'P'))
            ->exists();
    }

    private function practicalGateError(Student $student, int $courseId): ?array
    {
        if ($this->hasAnyPracticalToday($student, $courseId)) {
            return null;
        }

        $checkpoint = $this->beforeCheckingCheckpoint($student->id, $courseId, now()->toDateString());

        if (!$checkpoint || !$checkpoint->time_out) {
            return null; // no before-checking row on record — fail open rather than block indefinitely
        }

        if (now()->lt($checkpoint->time_out)) {
            $secondsLeft = $checkpoint->time_out->getTimestamp() - now()->getTimestamp();

            return [
                'status' => 'before_checking_wait',
                'message' => "Please wait {$secondsLeft} more second(s) after course attendance started before practical attendance can begin.",
                'seconds_left' => $secondsLeft,
            ];
        }

        return null;
    }
    private function afterCheckingGateError(Student $student, int $courseId): ?array
    {
        $openPractical = SubjectAttendanceSession::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->whereDate('attendance_date', now()->toDateString())
            ->whereHas('subject', fn($q) => $q->where('type', 'P'))
            ->open()
            ->exists();

        if ($openPractical) {
            return [
                'status' => 'after_checking_open',
                'message' => 'A practical session for this student is still open — end it before ending course attendance.',
                'student' => $this->studentPayload($student),
            ];
        }

        $checkpoint = $this->afterCheckingCheckpoint($student->id, $courseId, now()->toDateString());

        if (!$checkpoint || !$checkpoint->time_out) {
            return null; // no practical session has closed for this course today — nothing to wait on
        }

        if (now()->lt($checkpoint->time_out)) {
            $secondsLeft = $checkpoint->time_out->getTimestamp() - now()->getTimestamp();

            return [
                'status' => 'after_checking_wait',
                'message' => "Please wait {$secondsLeft} more second(s) after the last practical session ended before ending course attendance.",
                'seconds_left' => $secondsLeft,
                'student' => $this->studentPayload($student),
            ];
        }

        return null;
    }

    private function sessionKind(SubjectAttendanceSession $session): string
    {
        if ($session->checkpoint_type) {
            return 'checkpoint';
        }

        if (is_null($session->subject_id)) {
            return 'course';
        }

        return optional($session->subject)->type === 'P' ? 'practical' : 'subject';
    }

    private function beforeCheckingCheckpoint(int $studentId, int $courseId, string $date): ?SubjectAttendanceSession
    {
        return SubjectAttendanceSession::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->whereDate('attendance_date', $date)
            ->beforeChecking()
            ->first();
    }

    /** Today's "after checking" row for this student+course, if any (max one). */
    private function afterCheckingCheckpoint(int $studentId, int $courseId, string $date): ?SubjectAttendanceSession
    {
        return SubjectAttendanceSession::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->whereDate('attendance_date', $date)
            ->afterChecking()
            ->first();
    }

    private function stampBeforeChecking(int $studentId, int $intakeId, int $courseId, \Illuminate\Support\Carbon $startedAt, int $markedBy): SubjectAttendanceSession
    {
        return SubjectAttendanceSession::updateOrCreate(
            [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'attendance_date' => $startedAt->toDateString(),
                'checkpoint_type' => 'before_checking',
            ],
            [
                'intake_id' => $intakeId,
                'subject_id' => null,
                'time_in' => $startedAt,
                'time_out' => $startedAt->copy()->addMinutes(20),
                'marked_in_by' => $markedBy,
                'marked_out_by' => $markedBy,
            ]
        );
    }


    private function stampAfterChecking(int $studentId, int $intakeId, int $courseId, \Illuminate\Support\Carbon $endedAt, int $markedBy): SubjectAttendanceSession
    {
        return SubjectAttendanceSession::updateOrCreate(
            [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'attendance_date' => $endedAt->toDateString(),
                'checkpoint_type' => 'after_checking',
            ],
            [
                'intake_id' => $intakeId,
                'subject_id' => null,
                'time_in' => $endedAt,
                'time_out' => $endedAt->copy()->addMinutes(10),
                'marked_in_by' => $markedBy,
                'marked_out_by' => $markedBy,
            ]
        );
    }

    private function deleteBeforeChecking(int $studentId, int $courseId, string $date): void
    {
        SubjectAttendanceSession::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->whereDate('attendance_date', $date)
            ->beforeChecking()
            ->delete();
    }

    private function recomputeAfterChecking(int $studentId, int $courseId, string $date, int $excludeSessionId): void
    {
        $nextLatest = SubjectAttendanceSession::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->whereDate('attendance_date', $date)
            ->notCheckpoint()
            ->whereHas('subject', fn($q) => $q->where('type', 'P'))
            ->whereNotNull('time_out')
            ->where('id', '!=', $excludeSessionId)
            ->orderByDesc('time_out')
            ->first();

        if ($nextLatest) {
            $this->stampAfterChecking(
                $studentId,
                $nextLatest->intake_id,
                $courseId,
                $nextLatest->time_out,
                $nextLatest->marked_out_by ?? $nextLatest->marked_in_by
            );

            return;
        }

        SubjectAttendanceSession::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->whereDate('attendance_date', $date)
            ->afterChecking()
            ->delete();
    }

    private function lastActionAt(SubjectAttendanceSession $session): \Illuminate\Support\Carbon
    {
        return $session->time_out ?? $session->time_in;
    }

    private function markSession(
        Student $student,
        int $intakeId,
        int $courseId,
        ?int $subjectId,
        ?int $vehicleId,
        ?string $note,
        bool $allowRepeat = false
    ): array {
        $today = now()->toDateString();

        $open = SubjectAttendanceSession::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->where('subject_id', $subjectId)
            ->whereDate('attendance_date', $today)
            ->notCheckpoint()
            ->open()
            ->first();

        if ($open) {
            $open->update([
                'time_out' => now(),
                'note' => $note,
                'marked_out_by' => Auth::id(),
            ]);

            return [
                'session_id' => $open->id,
                'action' => 'out',
                'time' => $open->time_out->format('h:i A'),
            ];
        }

        if (!$allowRepeat) {
            $alreadyDone = SubjectAttendanceSession::where('student_id', $student->id)
                ->where('course_id', $courseId)
                ->where('subject_id', $subjectId)
                ->whereDate('attendance_date', $today)
                ->notCheckpoint()
                ->whereNotNull('time_out')
                ->exists();

            if ($alreadyDone) {
                return ['action' => 'already_completed'];
            }
        }

        $session = SubjectAttendanceSession::create([
            'student_id' => $student->id,
            'intake_id' => $intakeId,
            'course_id' => $courseId,
            'subject_id' => $subjectId,
            'attendance_date' => $today,
            'time_in' => now(),
            'marked_in_by' => Auth::id(),
            'vehicle_id' => $vehicleId,
        ]);

        return [
            'session_id' => $session->id,
            'action' => 'in',
            'time' => $session->time_in->format('h:i A'),
        ];
    }

    private function courseBalanceAlert(Student $student, int $courseId): ?array
    {
        $totals = DB::table('fee_invoices')
            ->join('paid_courses', 'paid_courses.invoice_id', '=', 'fee_invoices.id')
            ->where('fee_invoices.student_id', $student->id)
            ->where('fee_invoices.invoice_type', 'course')
            ->where('paid_courses.course_id', $courseId)
            ->selectRaw('SUM(fee_invoices.total_amount) as total_price, SUM(fee_invoices.paid_amount) as total_paid')
            ->first();

        if (!$totals || is_null($totals->total_price)) {
            return null; // no payment record for this course
        }

        $balance = round((float) $totals->total_price - (float) $totals->total_paid, 2);

        if ($balance <= 0) {
            return null;
        }

        return [
            'course_price' => (float) $totals->total_price,
            'paid_amount' => (float) $totals->total_paid,
            'balance' => $balance,
        ];
    }

    private function courseBalanceAlerts(Student $student, array $results): array
    {
        $alerts = [];

        foreach (array_unique(array_filter(array_column($results, 'course_id'))) as $courseId) {
            $alert = $this->courseBalanceAlert($student, (int) $courseId);

            if ($alert) {
                $alerts[$courseId] = $alert;
            }
        }

        return $alerts;
    }

    private function hasCoursePayment(Student $student, int $courseId): bool
    {
        $totalPaid = DB::table('paid_courses')
            ->join('fee_invoices', 'fee_invoices.id', '=', 'paid_courses.invoice_id')
            ->where('fee_invoices.student_id', $student->id)
            ->where('paid_courses.course_id', $courseId)
            ->sum('paid_courses.paid_amount');

        return $totalPaid > 0;
    }

    private function practicalConcurrencyError(Student $student, int $courseId, int $subjectId): ?array
    {
        $openOther = SubjectAttendanceSession::with('subject')
            ->where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->where('subject_id', '!=', $subjectId)
            ->whereDate('attendance_date', now()->toDateString())
            ->notCheckpoint()
            ->whereHas('subject', fn($q) => $q->where('type', 'P'))
            ->open()
            ->first();

        if (!$openOther) {
            return null;
        }

        return [
            'status' => 'practical_already_open',
            'message' => 'Only one practical subject can be open at a time for this course — close "' . ($openOther->subject->name ?? 'the current subject') . '" before starting another.',
        ];
    }

     private function resolveVehicleBranchIds(array $fallbackBranchIds): array
    {
        $user = Auth::user();

        if (!$user) {
            return $fallbackBranchIds;
        }

        $userBranchIds = DB::table('user_branches')
            ->where('user_id', $user->id)
            ->pluck('branch_id');

        if ($userBranchIds->count() === 1) {
            return [$userBranchIds->first()];
        }

        return $fallbackBranchIds;
    }
}