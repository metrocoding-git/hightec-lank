<?php

namespace App\Http\Controllers;

use App\Models\AssignSubjects;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\Subject;
use App\Models\SubjectAttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudentPracticalCardController extends Controller
{
    /**
     * NEW (this round) — the "student attendance card list" window. Lists
     * every student who has at least one course assignment, with a Course
     * filter + name/admission/NIC search, each row linking to that
     * student's card (show()) instead of needing a direct URL with an id.
     */
    public function index(Request $request)
    {
        $studentCourseQuery = StudentCourse::query();

        if ($request->filled('course_id')) {
            $studentCourseQuery->where('course_id', (int) $request->input('course_id'));
        }

        $studentIds = $studentCourseQuery->pluck('student_id')->unique();

        $studentsQuery = Student::with('branch')
            ->whereIn('id', $studentIds)
            ->orderBy('student_name');

        if ($request->filled('q')) {
            $term = trim($request->input('q'));
            $studentsQuery->where(function ($q) use ($term) {
                $q->where('student_name', 'like', "%{$term}%")
                    ->orWhere('admission_number', 'like', "%{$term}%")
                    ->orWhere('national_id_number', 'like', "%{$term}%");
            });
        }

        $students = $studentsQuery->get();

        $courseOptions = Course::whereIn('id', StudentCourse::pluck('course_id')->unique())
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('pages.student.attendance.practical-card-index', [
            'students' => $students,
            'courseOptions' => $courseOptions,
            'selectedCourseId' => $request->input('course_id'),
            'q' => $request->input('q'),
        ]);
    }

    /**
     * Printable attendance card for one student under one course — now
     * TWO cards in the same window (Practical + Theory, each printed
     * separately), and Before Checking / After Checking / Course
     * Attendance are their own subject-style sections (every instance
     * listed, not just the first checkpoint of the day).
     */
    public function show($id, Request $request)
    {
        $student = Student::with('branch')->findOrFail($id);

        // Distinct courses this student is/was assigned to, oldest -> newest,
        // used to populate the course selector.
        $studentCourses = StudentCourse::with('course:id,name')
            ->where('student_id', $student->id)
            ->orderBy('id')
            ->get()
            ->filter(fn($sc) => $sc->course)
            ->unique('course_id')
            ->values();

        if ($studentCourses->isEmpty()) {
            return back()->with('error', 'This student has no assigned courses.');
        }

        // Selected course: ?course_id= if valid for this student, otherwise
        // default to the LAST assigned course (e.g. 3 courses -> the 3rd).
        $requestedCourseId = $request->input('course_id');
        $validCourseIds = $studentCourses->pluck('course_id');

        $selectedCourseId = $validCourseIds->contains((int) $requestedCourseId)
            ? (int) $requestedCourseId
            : $studentCourses->last()->course_id;

        $selectedEnrolment = $studentCourses->firstWhere('course_id', $selectedCourseId);
        $selectedCourse = $selectedEnrolment->course;

        $practical = $this->buildSubjectRows($student->id, $selectedCourseId, $selectedEnrolment->intake_id, 'P');
        $theory = $this->buildSubjectRows($student->id, $selectedCourseId, $selectedEnrolment->intake_id, 'T');

        $beforeChecking = $this->buildCheckpointGroup('Before Checking', $student->id, $selectedCourseId, 'before_checking');
        
        $afterChecking = $this->buildCheckpointGroup('After Checking', $student->id, $selectedCourseId, 'after_checking');

        $courseAttendance = $this->buildCheckpointGroup('Course Attendance', $student->id, $selectedCourseId, null);

        return view('pages.student.attendance.practical-card', [
            'student' => $student,
            'courses' => $studentCourses,
            'selectedCourseId' => $selectedCourseId,
            'selectedCourse' => $selectedCourse,
            'practicalSubjects' => $practical['rows'],
            'practicalTotalDemonstratorTime' => $practical['totalDemonstratorTime'],
            'practicalTotalPracticalTime' => $practical['totalPracticalTime'],
            'theorySubjects' => $theory['rows'],
            'totalBeforeChecking' => $beforeChecking['total_duration'],
            'totalAfterChecking' => $afterChecking['total_duration'],
            'beforeChecking' => $beforeChecking,
            'afterChecking' => $afterChecking,
            'courseAttendance' => $courseAttendance,
        ]);
    }

    /**
     * One entry per subject of the given type ($subjectType: 'P' practical,
     * 'T' theory) ASSIGNED to this course/intake — with every attendance
     * instance recorded for it (possibly none) — plus the total planned
     * Demonstrator Time / Practical Time summed across those subjects
     * (used for the two new "title header" totals).
     */
    private function buildSubjectRows(int $studentId, int $courseId, ?int $intakeId, string $subjectType): array
    {
        // Every subject assigned to this course (scoped to the student's
        // own intake when we know it, same as the scan-attendance flow's
        // resolvePracticalSubjects()) — keyed by subject_id so each subject
        // can look up its own planned demonstrator_time/practical_time.
        $assignments = AssignSubjects::where('course_id', $courseId)
            ->when($intakeId, fn($q) => $q->where('intake_id', $intakeId))
            ->get(['subject_id', 'demonstrator_time', 'practical_time'])
            ->keyBy('subject_id');

        $subjects = Subject::whereIn('id', $assignments->keys())
            ->where('type', $subjectType)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Total planned time across the subjects of THIS type only — the
        // $assignments collection includes every assigned subject regardless
        // of type, so this is scoped down to just the ones in $subjects.
        $totalDemonstratorMinutes = 0;
        $totalPracticalMinutes = 0;

        foreach ($subjects as $subject) {
            $assignment = $assignments->get($subject->id);
            $totalDemonstratorMinutes += $this->parsePlannedMinutes($assignment->demonstrator_time ?? null);
            $totalPracticalMinutes += $this->parsePlannedMinutes($assignment->practical_time ?? null);
        }

        $totals = [
            'totalDemonstratorTime' => $this->formatMinutes($totalDemonstratorMinutes),
            'totalPracticalTime' => $this->formatMinutes($totalPracticalMinutes),
        ];

        if ($subjects->isEmpty()) {
            return array_merge(['rows' => []], $totals);
        }

        // Every real attendance session for those subjects (checkpoints are
        // handled separately now, by buildCheckpointGroup()), grouped by
        // subject so each subject can list all its instances.
        $sessionsBySubject = SubjectAttendanceSession::with([
            'vehicle:id,name,vehicle_number',
            'markedInBy:id,name',
            'markedOutBy:id,name',
        ])
            ->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->whereIn('subject_id', $subjects->pluck('id'))
            ->whereNull('checkpoint_type')
            ->orderBy('attendance_date')
            ->orderBy('time_in')
            ->get()
            ->groupBy('subject_id');

        $rows = [];

        foreach ($subjects as $subject) {
            $assignment = $assignments->get($subject->id);
            $subjectSessions = $sessionsBySubject->get($subject->id, collect());

            $instances = $subjectSessions->map(function ($s) {
                return [
                    'date' => $s->attendance_date->format('Y-m-d (D)'),
                    'time_in' => $s->time_in,
                    'time_out' => $s->time_out,
                    'duration' => $this->duration($s->time_in, $s->time_out),
                    'vehicle' => $s->vehicle->name ?? '-',
                    'marked_by' => optional($s->markedOutBy)->name ?? optional($s->markedInBy)->name ?? '-',
                ];
            })->values();

            $rows[] = [
                'subject' => $subject->name,
                'demonstrator_time' => $this->formatPlannedTime($assignment->demonstrator_time ?? null),
                'practical_time' => $this->formatPlannedTime($assignment->practical_time ?? null),
                'attended_count' => $instances->count(),
                'instances' => $instances,
            ];
        }

        return array_merge(['rows' => $rows], $totals);
    }

    /**
     * NEW (this round) — a single subject-style group for something that
     * ISN'T a real assigned subject: Before Checking, After Checking, or
     * (when $checkpointType is null) general Course Attendance. Same shape
     * as a subject row (so the view can render it with the same table
     * markup) but with no planned Demo/Practical time, and EVERY matching
     * instance listed — the old code only kept the first checkpoint of a
     * given day, silently dropping any extra ones; this lists them all.
     */

    
    private function buildCheckpointGroup(string $title, int $studentId, int $courseId, ?string $checkpointType): array
    {
        $query = SubjectAttendanceSession::with([
            'vehicle:id,name,vehicle_number',
            'markedInBy:id,name',
            'markedOutBy:id,name',
        ])
            ->where('student_id', $studentId)
            ->where('course_id', $courseId);

        if ($checkpointType === null) {
            // Course Attendance: not tied to a specific subject, and not a
            // before/after checkpoint either.
            $query->whereNull('subject_id')->whereNull('checkpoint_type');
        } else {
            $query->where('checkpoint_type', $checkpointType);
        }

        $sessions = $query->orderBy('attendance_date')->orderBy('time_in')->get();

        $totalMinutes = $sessions->sum(function ($s) {
            if (!$s->time_in || !$s->time_out) {
                return 0;
            }
    
            $timeIn = \Carbon\Carbon::parse($s->time_in);
            $timeOut = \Carbon\Carbon::parse($s->time_out);
    
            return $timeIn->diffInMinutes($timeOut);
        });

        $instances = $sessions->map(function ($s) {
            return [
                'date' => $s->attendance_date->format('Y-m-d (D)'),
                'time_in' => $s->time_in,
                'time_out' => $s->time_out,
                'duration' => $this->duration($s->time_in, $s->time_out),
                'vehicle' => $s->vehicle->name ?? '-',
                'marked_by' => optional($s->markedOutBy)->name ?? optional($s->markedInBy)->name ?? '-',
            ];
        })->values();

        return [
            'title' => $title,
            'attended_count' => $instances->count(),
            'total_duration' => $this->formatMinutes($totalMinutes),
            'instances' => $instances,
        ];
    }

    /**
     * assign_subjects.demonstrator_time / practical_time — the planned time
     * budget for the subject. Displayed as-is if it's already a time-ish
     * string (e.g. "00:30"), or suffixed with "min" if it's a plain number.
     */
    private function formatPlannedTime($value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return is_numeric($value) ? $value . ' min' : (string) $value;
    }

    /**
     * NEW (this round) — parses the same raw demonstrator_time/practical_time
     * value (either a "H:i" string or a plain number of minutes) into a
     * whole number of minutes, so several subjects' planned times can be
     * summed for the new "title header" totals. Unrecognized values are
     * treated as 0 rather than throwing.
     */
    private function parsePlannedMinutes($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        if (preg_match('/^(\d{1,3}):(\d{2})$/', trim((string) $value), $m)) {
            return ((int) $m[1] * 60) + (int) $m[2];
        }

        return 0;
    }

    /**
     * NEW (this round) — shared H:i formatter for a whole number of
     * minutes, used by both duration() (a single session's time in/out)
     * and the new planned-time totals, so they always render the same way.
     */
    private function formatMinutes(int $minutes): string
    {
        $minutes = max(0, $minutes);

        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    /**
     * HARDENED (this round) — "durate calculation is wrong" (per-session
     * time in -> time out). Two defensive fixes applied, since no specific
     * wrong example was available to reproduce against:
     *  1. Both times are re-anchored to the exact same base date (by
     *     re-parsing just their H:i:s portion) before diffing, in case
     *     $start/$end ever come from casts that anchor to different dates.
     *  2. A session that crosses midnight (time out is earlier-looking
     *     than time in, e.g. 11:50 PM -> 12:10 AM) is detected and $end is
     *     pushed a day forward before diffing, instead of producing a huge
     *     wrong number (or a wrong-looking near-24h value).
     * If a duration still looks wrong after this, the exact times shown
     * would help pin down anything this doesn't already cover.
     */
    private function duration(?Carbon $start, ?Carbon $end): string
    {
        if (!$start || !$end) {
            return '-';
        }

        $start = Carbon::parse($start->format('H:i:s'));
        $end = Carbon::parse($end->format('H:i:s'));

        if ($end->lessThan($start)) {
            $end->addDay();
        }

        $totalMinutes = (int) $start->diffInMinutes($end);

        return $this->formatMinutes($totalMinutes);
    }
}