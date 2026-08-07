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
        $courses = Course::all();
        $subjects = Subject::orderBy('name')->get(['id', 'name', 'code', 'type']);
        $vehicles = TrainingVehicle::active()->orderBy('name')->get();

        return view('pages.student.attendance.index', compact('courses', 'subjects', 'vehicles'));
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

        $studentCourse = StudentCourse::with('intake:id,name')
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

        $result = DB::transaction(fn () => $this->markSession(
            $student,
            $studentCourse->intake_id,
            $courseId,
            null,
            $request->input('vehicle_id'),
            $request->input('note')
        ));

        return response()->json([
            'status' => 'ok',
            'student' => $this->studentPayload($student),
            'intake_name' => $studentCourse->intake->name ?? null,
            'results' => [$result],
        ]);
    }

    // =====================================================================
    // Section 2 — Subject attendance (one or more subjects, any student)
    // =====================================================================


    public function allSubjects()
    {
        return response()->json(['subjects' => Subject::orderBy('name')->get(['id', 'name', 'code', 'type'])]);
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

        foreach ($enrolments as $enrolment) {
            $subjectIds = AssignSubjects::where('intake_id', $enrolment->intake_id)
                ->where('course_id', $enrolment->course_id)
                ->pluck('subject_id');

            if ($subjectIds->isEmpty()) {
                continue;
            }

            Subject::whereIn('id', $subjectIds)
                ->where('type', 'P')
                ->get(['id', 'name'])
                ->each(fn ($s) => $subjects->put($s->id, $s->name));
        }

        if ($subjects->isEmpty()) {
            return response()->json([
                'status' => 'no_practical_subjects',
                'message' => "{$student->student_name} has no practical subjects assigned under their active courses.",
                'student' => $this->studentPayload($student),
            ], 422);
        }

        return response()->json([
            'status' => 'select_subject',
            'student' => $this->studentPayload($student),
            'subjects' => $subjects->map(fn ($name, $id) => ['id' => $id, 'name' => $name])->values(),
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
        $matches = $this->enrolmentsAssignedSubject($enrolments, $subjectId);

        if ($matches->isEmpty()) {
            return response()->json([
                'status' => 'not_assigned',
                'message' => 'This subject is not assigned under any of this student\'s active courses.',
                'student' => $this->studentPayload($student),
            ], 422);
        }

        $vehicleId = $request->input('vehicle_id');
        $note = $request->input('note');
        $results = [];

        DB::transaction(function () use ($matches, $student, $subjectId, $vehicleId, $note, &$results) {
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
        });

        return response()->json([
            'status' => 'ok',
            'student' => $this->studentPayload($student),
            'results' => $results,
        ]);
    }

    // =====================================================================
    // Undo — same-day, same-user only
    // =====================================================================

  
    public function undoScan(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:subject_attendance_sessions,id',
        ]);

        $session = SubjectAttendanceSession::findOrFail($request->session_id);

        if ($session->attendance_date->toDateString() !== now()->toDateString()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only today\'s scans can be undone.',
            ], 422);
        }

        $userId = Auth::id();

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

            return response()->json(['status' => 'ok', 'message' => 'Time out undone.']);
        }

        if ((int) $session->marked_in_by !== (int) $userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only the staff member who marked this time-in can undo it.',
            ], 403);
        }

        $session->delete();

        return response()->json(['status' => 'ok', 'message' => 'Time in undone.']);
    }

    
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

    
    private function markSession(
        Student $student,
        int $intakeId,
        int $courseId,
        ?int $subjectId,
        ?int $vehicleId,
        ?string $note
    ): array {
        $today = now()->toDateString();

        $open = SubjectAttendanceSession::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->where('subject_id', $subjectId)
            ->whereDate('attendance_date', $today)
            ->open()
            ->first();

        if ($open) {
            $open->update([
                'time_out' => now(),
                'vehicle_id' => $vehicleId,
                'note' => $note,
                'marked_out_by' => Auth::id(),
            ]);

            return [
                'session_id' => $open->id,
                'action' => 'out',
                'time' => $open->time_out->format('h:i A'),
            ];
        }

        $alreadyDone = SubjectAttendanceSession::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->where('subject_id', $subjectId)
            ->whereDate('attendance_date', $today)
            ->whereNotNull('time_out')
            ->exists();

        if ($alreadyDone) {
            return ['action' => 'already_completed'];
        }

        $session = SubjectAttendanceSession::create([
            'student_id' => $student->id,
            'intake_id' => $intakeId,
            'course_id' => $courseId,
            'subject_id' => $subjectId,
            'attendance_date' => $today,
            'time_in' => now(),
            'marked_in_by' => Auth::id(),
        ]);

        return [
            'session_id' => $session->id,
            'action' => 'in',
            'time' => $session->time_in->format('h:i A'),
        ];
    }
}