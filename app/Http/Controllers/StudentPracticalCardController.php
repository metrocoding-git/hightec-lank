<?php

namespace App\Http\Controllers;

use App\Models\AssignSubjects;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentAssessment;
use App\Models\StudentCourse;
use App\Models\Subject;
use App\Models\SubjectAttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class StudentPracticalCardController extends Controller
{
    public function index(Request $request)
    {
        $hasFilter = $this->hasAnyFilter($request);

        $students = $hasFilter ? $this->filteredStudents($request) : collect();

        $branches = Branch::orderBy('name')->get(['id', 'name']);

        return view('pages.student.attendance.practical-card-index', [
            'students' => $students,
            'branches' => $branches,
            'hasFilter' => $hasFilter,
            'selectedBranchId' => $request->input('branch_id'),
            'selectedIntakeId' => $request->input('intake_id'),
            'selectedCourseId' => $request->input('course_id'),
            'q' => $request->input('q'),
            'selectedAssessmentStatus' => $request->input('assessment_status'),
            'selectedAssessmentResult' => $request->input('assessment_result'),
            'assessmentDateRange' => $request->input('assessment_date_range'),
        ]);
    }

    /**
     * Whether any of the index page's filters are set.
     */
    private function hasAnyFilter(Request $request): bool
    {
        return $request->filled('branch_id')
            || $request->filled('intake_id')
            || $request->filled('course_id')
            || $request->filled('q')
            || $request->filled('assessment_status')
            || $request->filled('assessment_result')
            || $request->filled('assessment_date_range');
    }

    /**
     * The actual filtered-students query + in-memory assessment filters.
     */
    private function filteredStudents(Request $request)
    {
        $query = Student::with('branch');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', (int) $request->input('branch_id'));
        }

        if ($request->filled('intake_id') || $request->filled('course_id')) {
            $query->whereIn('id', function ($sub) use ($request) {
                $sub->select('student_id')->from('student_courses');

                if ($request->filled('intake_id')) {
                    $sub->where('intake_id', (int) $request->input('intake_id'));
                }

                if ($request->filled('course_id')) {
                    $sub->where('course_id', (int) $request->input('course_id'));
                }
            });
        }

        if ($request->filled('q')) {
            $term = trim($request->input('q'));
            $query->where(function ($q) use ($term) {
                $q->where('student_name', 'like', "%{$term}%")
                    ->orWhere('admission_number', 'like', "%{$term}%")
                    ->orWhere('national_id_number', 'like', "%{$term}%");
            });
        }

        $students = $query->orderBy('student_name')->get();

        $students = $this->attachAssessmentStatus($students, $request->input('course_id'));

        if ($request->filled('assessment_status')) {
            $wantDone = $request->input('assessment_status') === 'done';
            $students = $students->filter(fn($s) => (bool) $s->final_assessment_done === $wantDone)->values();
        }

        if ($request->filled('assessment_result')) {
            $wantPass = $request->input('assessment_result') === 'pass';
            $students = $students->filter(
                fn($s) => $s->final_assessment_done && (bool) $s->final_assessment_passed === $wantPass
            )->values();
        }

        if ($request->filled('assessment_date_range')) {
            $parts = array_map('trim', explode(' - ', $request->input('assessment_date_range')));

            if (count($parts) === 2) {
                try {
                    $rangeStart = Carbon::parse($parts[0])->startOfDay();
                    $rangeEnd = Carbon::parse($parts[1])->endOfDay();

                    $students = $students->filter(function ($s) use ($rangeStart, $rangeEnd) {
                        if (!$s->final_assessment_date_raw) {
                            return false;
                        }

                        return $s->final_assessment_date_raw->between($rangeStart, $rangeEnd);
                    })->values();
                } catch (\Exception $e) {
                    // ignore silently — malformed range shouldn't break the page
                }
            }
        }

        return $students;
    }

    private function attachAssessmentStatus($students, $filterCourseId = null)
    {
        if ($students->isEmpty()) {
            return $students;
        }

        $studentIds = $students->pluck('id');

        $courseQuery = StudentCourse::whereIn('student_id', $studentIds)->orderBy('id');

        if ($filterCourseId) {
            $courseQuery->where('course_id', (int) $filterCourseId);
        }

        $relevantCourseByStudent = $courseQuery
            ->get(['id', 'student_id', 'course_id'])
            ->groupBy('student_id')
            ->map(fn($rows) => $rows->unique('course_id')->last());

        $studentCourseIds = $relevantCourseByStudent->pluck('id')->filter()->values();

        $assessmentsByStudentCourseId = StudentAssessment::whereIn('student_course_id', $studentCourseIds)
            ->orderBy('created_at')
            ->get(['student_course_id', 'is_qualified', 'assessed_at'])
            ->groupBy('student_course_id')
            ->map(fn($rows) => $rows->last());

        return $students->map(function ($student) use ($relevantCourseByStudent, $assessmentsByStudentCourseId) {
            $relevantCourse = $relevantCourseByStudent->get($student->id);
            $assessment = $relevantCourse ? $assessmentsByStudentCourseId->get($relevantCourse->id) : null;

            $student->final_assessment_done = (bool) $assessment;
            $student->final_assessment_passed = $assessment ? (bool) $assessment->is_qualified : null;
            $student->final_assessment_date_raw = $assessment ? $assessment->assessed_at : null;
            $student->final_assessment_date = ($assessment && $assessment->assessed_at)
                ? $assessment->assessed_at->format('Y-m-d')
                : null;

            return $student;
        });
    }

    public function show($id, Request $request)
    {
        try {
            $data = $this->buildCardData($id, $request);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return view('pages.student.attendance.practical-card', $data);
    }

    /**
     * NEW — the "View Template" page: a course-scoped (NOT student-scoped)
     * preview of the Practical/Theory Subjects a course covers, with no
     * attendance data at all — just subject names + planned times, plus
     * print/export buttons. Reached from a new button at the top of the
     * Student Attendance Cards list page. The real per-student card
     * (show()/buildCardData() below) is untouched by this feature.
     */
    public function template(Request $request)
    {
        $courses = Course::orderBy('name')->get(['id', 'name']);

        $selectedCourseId = null;

        if ($request->filled('course_id') && $courses->contains('id', (int) $request->input('course_id'))) {
            $selectedCourseId = (int) $request->input('course_id');
        } elseif ($courses->isNotEmpty()) {
            $selectedCourseId = $courses->first()->id;
        }

        return view('pages.student.attendance.practical-card-template', [
            'courses' => $courses,
            'selectedCourseId' => $selectedCourseId,
            'practicalSubjects' => $selectedCourseId ? $this->templateSubjectRows($selectedCourseId, 'P') : [],
            'theorySubjects' => $selectedCourseId ? $this->templateSubjectRows($selectedCourseId, 'T') : [],
        ]);
    }

    /**
     * NEW — Excel export of the Practical template (Subject / Demonstrator
     * Time / Practical Time) for a selected course.
     */
    public function exportTemplatePractical(Request $request)
    {
        $course = $this->requireTemplateCourse($request);

        return Excel::download(
            $this->templateExport($this->templateSubjectRows($course->id, 'P'), true),
            'practical-template-' . Str::slug($course->name) . '-' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * NEW — Excel export of the Theory template (Subject only) for a
     * selected course.
     */
    public function exportTemplateTheory(Request $request)
    {
        $course = $this->requireTemplateCourse($request);

        return Excel::download(
            $this->templateExport($this->templateSubjectRows($course->id, 'T'), false),
            'theory-template-' . Str::slug($course->name) . '-' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    private function requireTemplateCourse(Request $request): Course
    {
        $courseId = (int) $request->input('course_id');
        $course = $courseId ? Course::find($courseId) : null;

        abort_if(!$course, 404, 'Please select a course.');

        return $course;
    }

    /**
     * Subject list for the template page/export — just the subjects a
     * course is assigned (name + planned demonstrator/practical time, from
     * assign_subjects), with NO student and NO attendance data at all.
     * Deliberately separate from buildSubjectRows() below (which is
     * per-student and stays exactly as it was) rather than reusing it.
     */
    private function templateSubjectRows(int $courseId, string $subjectType): array
    {
        $assignments = AssignSubjects::where('course_id', $courseId)
            ->orderBy('id')
            ->get(['subject_id', 'demonstrator_time', 'practical_time'])
            ->keyBy('subject_id');

        if ($assignments->isEmpty()) {
            return [];
        }

        $subjects = Subject::whereIn('id', $assignments->keys())
            ->where('type', $subjectType)
            ->get(['id', 'name']);

        $assignOrder = array_flip($assignments->keys()->all());
        $subjects = $subjects->sortBy(fn($s) => $assignOrder[$s->id] ?? PHP_INT_MAX)->values();

        $rows = [];

        foreach ($subjects as $subject) {
            $assignment = $assignments->get($subject->id);

            $rows[] = [
                'subject' => $subject->name,
                'demonstrator_time' => $this->formatPlannedTime($assignment->demonstrator_time ?? null),
                'practical_time' => $this->formatPlannedTime($assignment->practical_time ?? null),
            ];
        }

        return $rows;
    }

    /**
     * Single-sheet Excel export for the template page — an anonymous class
     * implementing the Laravel Excel concerns directly, so this stays in
     * the controller with no separate Export class file.
     */
    private function templateExport(array $rows, bool $includeTimes)
    {
        return new class($rows, $includeTimes) implements FromArray, WithHeadings, ShouldAutoSize {
            protected array $rows;
            protected bool $includeTimes;

            public function __construct(array $rows, bool $includeTimes)
            {
                $this->rows = $rows;
                $this->includeTimes = $includeTimes;
            }

            public function headings(): array
            {
                return $this->includeTimes
                    ? ['No', 'Subject', 'Demonstrator Time', 'Practical Time']
                    : ['No', 'Subject'];
            }

            public function array(): array
            {
                $out = [];

                foreach ($this->rows as $index => $row) {
                    $out[] = $this->includeTimes
                        ? [$index + 1, $row['subject'], $row['demonstrator_time'], $row['practical_time']]
                        : [$index + 1, $row['subject']];
                }

                return $out;
            }
        };
    }

    /**
     * All of show()'s original body, pulled into its own method. Behavior
     * is unchanged from the original: throws a RuntimeException when the
     * student has no assigned courses, which show() catches and turns into
     * the same back()->with('error', ...) response as before.
     */
    private function buildCardData($id, Request $request): array
    {
        $student = Student::with('branch')->findOrFail($id);

        $studentCourses = StudentCourse::with('course:id,name')
            ->where('student_id', $student->id)
            ->orderBy('id')
            ->get()
            ->filter(fn($sc) => $sc->course)
            ->unique('course_id')
            ->values();

        if ($studentCourses->isEmpty()) {
            throw new \RuntimeException('This student has no assigned courses.');
        }

        $requestedCourseId = $request->input('course_id');
        $validCourseIds = $studentCourses->pluck('course_id');

        $selectedCourseId = $validCourseIds->contains((int) $requestedCourseId)
            ? (int) $requestedCourseId
            : $studentCourses->last()->course_id;

        $selectedEnrolment = $studentCourses->firstWhere('course_id', $selectedCourseId);
        $selectedCourse = $selectedEnrolment->course;

        $practical = $this->buildSubjectRows($student->id, $selectedCourseId, $selectedEnrolment->intake_id, 'P');
        $theory = $this->buildSubjectRows($student->id, $selectedCourseId, $selectedEnrolment->intake_id, 'T');

        $beforeChecking = $this->buildCheckpointGroup('පෙර පණගැවුම් පරික්ෂාව', $student->id, $selectedCourseId, 'before_checking');
        $afterChecking = $this->buildCheckpointGroup('පසු පණගැවුම් පරික්ෂාව', $student->id, $selectedCourseId, 'after_checking');
        $courseAttendance = $this->buildCheckpointGroup('නිරීක්ෂණ මිනිත්තුව', $student->id, $selectedCourseId, null);

        $courseAttendance = $this->applyNetDurationToCourseAttendance(
            $courseAttendance,
            $beforeChecking['minutesByDate'],
            $afterChecking['minutesByDate'],
            $theory['minutesByDate'],
            $practical['minutesByDate']
        );

        $doingTotalDemoMinutes = $courseAttendance['total_minutes'];
        $doingTotalDemoTime = $this->formatMinutes($doingTotalDemoMinutes);

        $courseAttendanceCompleted = $practical['totalDemonstratorMinutes'] > 0
            ? $doingTotalDemoMinutes >= $practical['totalDemonstratorMinutes']
            : null;

        return [
            'student' => $student,
            'courses' => $studentCourses,
            'selectedCourseId' => $selectedCourseId,
            'selectedCourse' => $selectedCourse,
            'practicalSubjects' => $practical['rows'],
            'practicalTotalDemonstratorTime' => $practical['totalDemonstratorTime'],
            'practicalTotalPracticalTime' => $practical['totalPracticalTime'],
            'practicalTotalActualTime' => $this->formatMinutes($practical['totalActualMinutes']),
            'theorySubjects' => $theory['rows'],
            'theoryTotalActualTime' => $this->formatMinutes($theory['totalActualMinutes']),
            'beforeChecking' => $beforeChecking,
            'afterChecking' => $afterChecking,
            'courseAttendance' => $courseAttendance,
            'doingTotalDemoTime' => $doingTotalDemoTime,
            'courseAttendanceCompleted' => $courseAttendanceCompleted,
        ];
    }

    private function buildSubjectRows(int $studentId, int $courseId, ?int $intakeId, string $subjectType): array
    {
        $assignments = AssignSubjects::where('course_id', $courseId)
            ->when($intakeId, fn($q) => $q->where('intake_id', $intakeId))
            ->orderBy('id')
            ->get(['subject_id', 'demonstrator_time', 'practical_time'])
            ->keyBy('subject_id');

        $subjects = Subject::whereIn('id', $assignments->keys())
            ->where('type', $subjectType)
            ->get(['id', 'name']);

        $assignOrder = array_flip($assignments->keys()->all());
        $subjects = $subjects->sortBy(fn($s) => $assignOrder[$s->id] ?? PHP_INT_MAX)->values();

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
            'totalDemonstratorMinutes' => $totalDemonstratorMinutes,
            'totalPracticalMinutes' => $totalPracticalMinutes,
        ];

        if ($subjects->isEmpty()) {
            return array_merge(['rows' => [], 'totalActualMinutes' => 0, 'minutesByDate' => []], $totals);
        }

        $sessions = SubjectAttendanceSession::with([
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
            ->get();

        $sessionsBySubject = $sessions->groupBy('subject_id');

        $rows = [];

        foreach ($subjects as $subject) {
            $assignment = $assignments->get($subject->id);
            $subjectSessions = $sessionsBySubject->get($subject->id, collect());
            $subjectActualMinutes = 0;

            // Every attendance mark still shows up here, under its own
            // subject — this per-subject list is untouched by the dedup
            // below.
            $instances = $subjectSessions->map(function ($s) use (&$subjectActualMinutes) {
                $minutes = $this->sessionDurationMinutes($s->time_in, $s->time_out);
                $subjectActualMinutes += $minutes;

                return [
                    'date' => $s->attendance_date->format('Y-m-d (D)'),
                    'time_in' => $s->time_in,
                    'time_out' => $s->time_out,
                    'duration' => $this->formatMinutes($minutes),
                    'vehicle' => $s->vehicle->name ?? '-',
                    'marked_by' => optional($s->markedOutBy)->name ?? optional($s->markedInBy)->name ?? '-',
                ];
            })->values();

            $plannedPracticalMinutes = $this->parsePlannedMinutes($assignment->practical_time ?? null);
            $showTotal = $instances->count() >= 1;

            $rows[] = [
                'subject' => $subject->name,
                'demonstrator_time' => $this->formatPlannedTime($assignment->demonstrator_time ?? null),
                'practical_time' => $this->formatPlannedTime($assignment->practical_time ?? null),
                'attended_count' => $instances->count(),
                'instances' => $instances,
                'total_duration' => $showTotal ? $this->formatMinutes($subjectActualMinutes) : null,
                'completed' => ($showTotal && $plannedPracticalMinutes > 0)
                    ? ($subjectActualMinutes >= $plannedPracticalMinutes)
                    : null,
            ];
        }

        // Update 3 fix — the course-wide "Total Completed ... Time" (and
        // the per-date minutesByDate used later to net Course Attendance)
        // is now computed from a DEDUPED session list, not by summing every
        // subject's sessions independently. If two subjects were both
        // marked present for the exact same attendance_date + time_in +
        // time_out, that slot now counts ONCE toward the total instead of
        // once per subject, per your request. Each subject's own row above
        // is unaffected — every mark still shows under its own subject;
        // only this course-wide total is deduped.
        $dedupedSessions = $sessions->unique(function ($s) {
            return $s->attendance_date->format('Y-m-d') . '|'
                . optional($s->time_in)->format('H:i:s') . '|'
                . optional($s->time_out)->format('H:i:s');
        });

        $totalActualMinutes = 0;
        $minutesByDate = [];

        foreach ($dedupedSessions as $s) {
            $minutes = $this->sessionDurationMinutes($s->time_in, $s->time_out);
            $totalActualMinutes += $minutes;

            $dateKey = $s->attendance_date->format('Y-m-d');
            $minutesByDate[$dateKey] = ($minutesByDate[$dateKey] ?? 0) + $minutes;
        }

        return array_merge(['rows' => $rows, 'totalActualMinutes' => $totalActualMinutes, 'minutesByDate' => $minutesByDate], $totals);
    }


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
            $query->whereNull('subject_id')->whereNull('checkpoint_type');
        } else {
            $query->where('checkpoint_type', $checkpointType);
        }

        $sessions = $query->orderBy('attendance_date')->orderBy('time_in')->get();

        $totalMinutes = 0;
        $minutesByDate = [];

        $instances = $sessions->map(function ($s) use (&$totalMinutes, &$minutesByDate) {
            $minutes = $this->sessionDurationMinutes($s->time_in, $s->time_out);
            $totalMinutes += $minutes;

            $dateKey = $s->attendance_date->format('Y-m-d');
            $minutesByDate[$dateKey] = ($minutesByDate[$dateKey] ?? 0) + $minutes;

            return [
                'date' => $s->attendance_date->format('Y-m-d (D)'),
                'time_in' => $s->time_in,
                'time_out' => $s->time_out,
                'duration' => $this->formatMinutes($minutes),
                'vehicle' => $s->vehicle->name ?? '-',
                'marked_by' => optional($s->markedOutBy)->name ?? optional($s->markedInBy)->name ?? '-',
            ];
        })->values();

        return [
            'title' => $title,
            'attended_count' => $instances->count(),
            'total_minutes' => $totalMinutes,
            'total_duration' => $this->formatMinutes($totalMinutes),
            'minutesByDate' => $minutesByDate,
            'instances' => $instances,
        ];
    }

    private function sumCompletedTimeByDate(
        array $courseAttendanceByDate,
        array $beforeCheckingByDate,
        array $afterCheckingByDate,
        array $theoryByDate,
        array $practicalByDate
    ): int {
        $dates = array_unique(array_merge(
            array_keys($courseAttendanceByDate),
            array_keys($beforeCheckingByDate),
            array_keys($afterCheckingByDate),
            array_keys($theoryByDate),
            array_keys($practicalByDate)
        ));

        $total = 0;

        foreach ($dates as $date) {
            $total += ($courseAttendanceByDate[$date] ?? 0)
                - ($beforeCheckingByDate[$date] ?? 0)
                - ($afterCheckingByDate[$date] ?? 0)
                - ($theoryByDate[$date] ?? 0)
                - ($practicalByDate[$date] ?? 0);
        }

        return $total;
    }

    private function applyNetDurationToCourseAttendance(
        array $courseAttendance,
        array $beforeCheckingByDate,
        array $afterCheckingByDate,
        array $theoryByDate,
        array $practicalByDate
    ): array {
        $deductedDates = [];

        $instances = collect($courseAttendance['instances'])
            ->map(function ($instance) use (&$deductedDates, $beforeCheckingByDate, $afterCheckingByDate, $theoryByDate, $practicalByDate) {
                $dateKey = substr($instance['date'], 0, 10);

                [$h, $m] = array_map('intval', explode(':', $instance['duration']));
                $net = ($h * 60) + $m;

                if (!in_array($dateKey, $deductedDates, true)) {
                    $net -= ($beforeCheckingByDate[$dateKey] ?? 0)
                        + ($afterCheckingByDate[$dateKey] ?? 0)
                        + ($theoryByDate[$dateKey] ?? 0)
                        + ($practicalByDate[$dateKey] ?? 0);
                    $deductedDates[] = $dateKey;
                }

                $instance['duration'] = $this->formatMinutes($net);

                return $instance;
            })
            ->values()
            ->all();

        $courseAttendance['instances'] = $instances;

        $netTotal = $this->sumCompletedTimeByDate(
            $courseAttendance['minutesByDate'],
            $beforeCheckingByDate,
            $afterCheckingByDate,
            $theoryByDate,
            $practicalByDate
        );
        $courseAttendance['total_minutes'] = $netTotal;
        $courseAttendance['total_duration'] = $this->formatMinutes($netTotal);

        return $courseAttendance;
    }

    private function formatPlannedTime($value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return is_numeric($value) ? $value . ' min' : (string) $value;
    }

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

    private function formatMinutes(int $minutes): string
    {
        $minutes = max(0, $minutes);

        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    private function sessionDurationMinutes(?Carbon $start, ?Carbon $end): int
    {
        if (!$start || !$end) {
            return 0;
        }

        $start = Carbon::parse($start->format('H:i:s'));
        $end = Carbon::parse($end->format('H:i:s'));

        if ($end->lessThan($start)) {
            $end->addDay();
        }

        return (int) $start->diffInMinutes($end);
    }

    private function duration(?Carbon $start, ?Carbon $end): string
    {
        return $this->formatMinutes($this->sessionDurationMinutes($start, $end));
    }
}