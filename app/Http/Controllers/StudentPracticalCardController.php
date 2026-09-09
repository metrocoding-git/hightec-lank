<?php

namespace App\Http\Controllers;

use App\Models\AssignSubjects;
use App\Models\Branch;
use App\Models\Student;
use App\Models\StudentAssessment;
use App\Models\StudentCourse;
use App\Models\Subject;
use App\Models\SubjectAttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudentPracticalCardController extends Controller
{
    public function index(Request $request)
    {
        $hasFilter = $request->filled('branch_id')
            || $request->filled('intake_id')
            || $request->filled('course_id')
            || $request->filled('q')
            // NEW — an assessment filter alone is also enough to trigger a
            // query, same "only show data once at least one selector is
            // set" rule the other filters already follow.
            || $request->filled('assessment_status')
            || $request->filled('assessment_result')
            || $request->filled('assessment_date_range');

        $students = collect();

        if ($hasFilter) {
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

            // NEW — batched Final Assessment lookup (see the method below
            // for exactly how the "relevant course" is resolved), then the
            // three new filters, applied in-memory since done/pass-fail/
            // date are computed attributes, not real columns to filter in
            // SQL.
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
                        $rangeStart = \Carbon\Carbon::parse($parts[0])->startOfDay();
                        $rangeEnd = \Carbon\Carbon::parse($parts[1])->endOfDay();

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
        }

        $branches = Branch::orderBy('name')->get(['id', 'name']);

        return view('pages.student.attendance.practical-card-index', [
            'students' => $students,
            'branches' => $branches,
            'hasFilter' => $hasFilter,
            'selectedBranchId' => $request->input('branch_id'),
            'selectedIntakeId' => $request->input('intake_id'),
            'selectedCourseId' => $request->input('course_id'),
            'q' => $request->input('q'),
            // NEW
            'selectedAssessmentStatus' => $request->input('assessment_status'),
            'selectedAssessmentResult' => $request->input('assessment_result'),
            'assessmentDateRange' => $request->input('assessment_date_range'),
        ]);
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
        $student = Student::with('branch')->findOrFail($id);

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

        return view('pages.student.attendance.practical-card', [
            'student' => $student,
            'courses' => $studentCourses,
            'selectedCourseId' => $selectedCourseId,
            'selectedCourse' => $selectedCourse,
            'practicalSubjects' => $practical['rows'],
            'practicalTotalDemonstratorTime' => $practical['totalDemonstratorTime'],
            'practicalTotalPracticalTime' => $practical['totalPracticalTime'],
            // NEW — total ACTUAL (completed) duration across all practical
            // subjects, for the new "Total Completed Duration" chip on the
            // Practical Subjects heading.
            'practicalTotalActualTime' => $this->formatMinutes($practical['totalActualMinutes']),
            'theorySubjects' => $theory['rows'],
            // NEW — same, for the Theory Subjects heading.
            'theoryTotalActualTime' => $this->formatMinutes($theory['totalActualMinutes']),
            'beforeChecking' => $beforeChecking,
            'afterChecking' => $afterChecking,
            'courseAttendance' => $courseAttendance,
            'doingTotalDemoTime' => $doingTotalDemoTime,
            'courseAttendanceCompleted' => $courseAttendanceCompleted,
        ]);
    }

    private function buildSubjectRows(int $studentId, int $courseId, ?int $intakeId, string $subjectType): array
    {
        // CHANGED — ->orderBy('id') added so $assignments (and therefore
        // the assign-order we sort subjects by below) reflects the actual
        // order rows were assigned in assign_subjects, not DB-undefined
        // default order.
        $assignments = AssignSubjects::where('course_id', $courseId)
            ->when($intakeId, fn($q) => $q->where('intake_id', $intakeId))
            ->orderBy('id')
            ->get(['subject_id', 'demonstrator_time', 'practical_time'])
            ->keyBy('subject_id');

        // CHANGED — dropped ->orderBy('name') (that was the alphabetical
        // ordering the user wants replaced). whereIn() alone doesn't
        // preserve any particular order, so the fetched subjects are
        // explicitly re-sorted below to match $assignments' own order.
        $subjects = Subject::whereIn('id', $assignments->keys())
            ->where('type', $subjectType)
            ->get(['id', 'name']);

        // "subject order is want come assign order" — re-sort to match the
        // order subjects were actually assigned (assign_subjects.id asc).
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
        $totalActualMinutes = 0;
        $minutesByDate = [];

        foreach ($subjects as $subject) {
            $assignment = $assignments->get($subject->id);
            $subjectSessions = $sessionsBySubject->get($subject->id, collect());
            $subjectActualMinutes = 0;

            $instances = $subjectSessions->map(function ($s) use (&$subjectActualMinutes, &$minutesByDate) {
                $minutes = $this->sessionDurationMinutes($s->time_in, $s->time_out);
                $subjectActualMinutes += $minutes;

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

            $totalActualMinutes += $subjectActualMinutes;

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
            // Course Attendance: not tied to a specific subject, and not a
            // before/after checkpoint either.
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

            // Accumulate this session's minutes onto its date.
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

    /**
     * Pairs up each date-keyed minutes map (Course Attendance, Before
     * Checking, After Checking, Theory, Practical) and computes, for every
     * date that appears in ANY of them:
     *   thatDay(courseAttendance) - thatDay(beforeChecking)
     *                              - thatDay(afterChecking)
     *                              - thatDay(theory)
     *                              - thatDay(practical)
     * A category with no session on a given date contributes 0 for that
     * date — it doesn't skip the whole day. The per-day results are summed
     * into one grand total, which can go negative overall (a day where
     * checking/subject time exceeds that day's Course Attendance nets
     * negative, and isn't clamped to zero before being added in — only the
     * FINAL formatted value the caller displays is floored at 00:00, via
     * formatMinutes()). This method itself returns the raw signed sum.
     */
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

    /**
     * Rewrites Course Attendance's own per-instance 'duration' strings (and
     * recomputes 'total_minutes'/'total_duration' to match) so each ROW
     * reflects the net calculation:
     *   that row's own raw gap
     *     - that date's Before Checking
     *     - that date's After Checking
     *     - that date's theory subjects' duration
     *     - that date's practical subjects' duration
     *
     * The deduction for a given date is applied ONCE — to that date's
     * chronologically first Course Attendance row (buildCheckpointGroup()
     * already returns instances ordered by attendance_date/time_in, so
     * "first encountered per date" is already chronological) — never to
     * every row on that date, so a day with 2+ Course Attendance entries
     * doesn't have its checking/subject time subtracted more than once. If
     * Course Attendance is always exactly one entry per day in this data,
     * this case never actually comes up.
     *
     * A date with checking/subject activity but NO Course Attendance row
     * at all still pulls the group's total down via sumCompletedTimeByDate()
     * below (a "phantom negative day") even though there's no visible row
     * to explain why — same behavior the aggregate Completed Time total
     * already had, kept consistent here.
     *
     * $courseAttendance MUST be the array buildCheckpointGroup() returns
     * (has 'instances' and 'minutesByDate' keys); the other four params are
     * each some other group's own 'minutesByDate'.
     */
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
                // 'date' is formatted 'Y-m-d (D)' for display — the plain
                // Y-m-d is always exactly the first 10 characters.
                $dateKey = substr($instance['date'], 0, 10);

                // Recover this row's own raw gap in minutes from its
                // already-correct formatted "H:i" duration (no need to
                // redo the time_in/time_out math — buildCheckpointGroup()
                // already got that right via sessionDurationMinutes()).
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

        // Group total follows the exact same per-date net math, reusing
        // sumCompletedTimeByDate() so this can never drift from the
        // Completed Time figure it also feeds.
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
     * Parses the same raw demonstrator_time/practical_time value (either a
     * "H:i" string or a plain number of minutes) into a whole number of
     * minutes, so several subjects' planned times can be summed for the
     * "title header" totals. Unrecognized values are treated as 0 rather
     * than throwing.
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
     * Shared H:i formatter for a whole number of minutes, used by every
     * duration/total shown on the card, so they all render the same way.
     */
    private function formatMinutes(int $minutes): string
    {
        $minutes = max(0, $minutes);

        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    /**
     * The actual "durate calculation" logic, as a minute count rather than
     * a formatted string, so both a single row's duration() AND any total
     * that sums several sessions (a subject's total, a checkpoint group's
     * total) share the exact same math instead of two slightly different
     * copies of it drifting apart. Two defensive fixes:
     *  1. Both times are re-anchored to the exact same base date (by
     *     re-parsing just their H:i:s portion) before diffing, in case
     *     $start/$end ever come from casts that anchor to different dates.
     *  2. A session that crosses midnight (time out is earlier-looking
     *     than time in, e.g. 11:50 PM -> 12:10 AM) is detected and $end is
     *     pushed a day forward before diffing, instead of producing a huge
     *     wrong number.
     */
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

    /**
     * Single-session duration, formatted — thin wrapper around
     * sessionDurationMinutes() + formatMinutes() for the per-row display.
     */
    private function duration(?Carbon $start, ?Carbon $end): string
    {
        return $this->formatMinutes($this->sessionDurationMinutes($start, $end));
    }
}
