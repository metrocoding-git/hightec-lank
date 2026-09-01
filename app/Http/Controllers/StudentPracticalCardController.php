<?php

namespace App\Http\Controllers;

use App\Models\AssignSubjects;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\Subject;
use App\Models\SubjectAttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudentPracticalCardController extends Controller
{
    /**
     * Printable "practical card" for one student under one course — EVERY
     * practical subject assigned to that course/intake is listed (not just
     * the ones they've attended): attended subjects show every time they
     * were done (a subject done 3 times gets 3 instance rows under it),
     * subjects never done show a single "Not Attended" row.
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

        $subjects = $this->buildSubjectRows($student->id, $selectedCourseId, $selectedEnrolment->intake_id);

        return view('pages.student.attendance.practical-card', [
            'student' => $student,
            'courses' => $studentCourses,
            'selectedCourseId' => $selectedCourseId,
            'selectedCourse' => $selectedCourse,
            'subjects' => $subjects,
        ]);
    }

    /**
     * One entry per practical subject ASSIGNED to this course/intake — with
     * every attendance instance recorded for it (possibly none).
     */
    private function buildSubjectRows(int $studentId, int $courseId, ?int $intakeId): array
    {
        // Every practical subject assigned to this course (scoped to the
        // student's own intake when we know it, same as the scan-attendance
        // flow's resolvePracticalSubjects()) — keyed by subject_id so each
        // subject can look up its own planned demonstrator_time/practical_time.
        $assignments = AssignSubjects::where('course_id', $courseId)
            ->when($intakeId, fn($q) => $q->where('intake_id', $intakeId))
            ->get(['subject_id', 'demonstrator_time', 'practical_time'])
            ->keyBy('subject_id');

        $subjects = Subject::whereIn('id', $assignments->keys())
            ->where('type', 'P')
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($subjects->isEmpty()) {
            return [];
        }

        // Every real (non-checkpoint) attendance session for those subjects,
        // grouped by subject so each subject can list all its instances.
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

        // Every before/after-checking checkpoint for this student+course,
        // grouped by date, so each instance row can look up "that day's"
        // checkpoint without re-querying per row.
        $checkpointsByDate = SubjectAttendanceSession::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->whereNotNull('checkpoint_type')
            ->get()
            ->groupBy(fn($s) => $s->attendance_date->toDateString());

        $rows = [];

        foreach ($subjects as $subject) {
            $assignment = $assignments->get($subject->id);
            $subjectSessions = $sessionsBySubject->get($subject->id, collect());

            $instances = $subjectSessions->map(function ($s) use ($checkpointsByDate) {
                $dayCheckpoints = $checkpointsByDate->get($s->attendance_date->toDateString(), collect());
                $before = $dayCheckpoints->first(fn($c) => $c->checkpoint_type === 'before_checking');
                $after = $dayCheckpoints->first(fn($c) => $c->checkpoint_type === 'after_checking');

                return [
                    'date' => $s->attendance_date->format('Y-m-d (D)'),
                    'time_in' => $s->time_in,
                    'time_out' => $s->time_out,
                    'duration' => $this->duration($s->time_in, $s->time_out),
                    'vehicle' => $s->vehicle->name ?? '-',
                    'marked_by' => optional($s->markedOutBy)->name ?? optional($s->markedInBy)->name ?? '-',
                    'before' => $before ? [
                        'time_in' => $before->time_in,
                        'time_out' => $before->time_out,
                        'duration' => $this->duration($before->time_in, $before->time_out),
                    ] : null,
                    'after' => $after ? [
                        'time_in' => $after->time_in,
                        'time_out' => $after->time_out,
                        'duration' => $this->duration($after->time_in, $after->time_out),
                    ] : null,
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

        return $rows;
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

    private function duration(?Carbon $start, ?Carbon $end): string
    {
        if (!$start || !$end) {
            return '-';
        }

        $totalMinutes = $end->diffInMinutes($start);

        return sprintf('%02d:%02d', intdiv($totalMinutes, 60), $totalMinutes % 60);
    }
}