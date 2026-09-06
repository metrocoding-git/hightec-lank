<?php

namespace App\Http\Controllers;

use App\Models\AssignSubjects;
use App\Models\Branch;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\Subject;
use App\Models\SubjectAttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudentPracticalCardController extends Controller
{
    /**
     * The "student attendance card list" window. Base list is Student
     * itself (so it's populated as long as the students table has rows —
     * "there default dont show any data" was the old version filtering the
     * base list down through student_courses first, which came back empty),
     * with Branch (direct column), Intake and Course (both via
     * student_courses, matching the branch->intake->course cascade the
     * disabled-students page already uses) as optional filters, plus the
     * existing name/admission/NIC search. No date-range filter — asked
     * about it, answer was "dont want date."
     *
     * "dont send data default. if has slectors only send data" — the list
     * stays empty on a plain page load (no query even runs); it only
     * fetches and shows students once at least one filter (Branch, Intake,
     * Course, or the search box) is actually set.
     */
    public function index(Request $request)
    {
        $hasFilter = $request->filled('branch_id')
            || $request->filled('intake_id')
            || $request->filled('course_id')
            || $request->filled('q');

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
        ]);
    }

    /**
     * Printable attendance card for one student under one course — two
     * cards in the same window (Practical + Theory, each printed
     * separately). Before Checking / After Checking / Course Attendance
     * are their own subject-style sections (every instance listed). Each
     * practical subject with 1+ attended instances now also shows its
     * actual total duration next to its planned Practical Time, with a
     * Completed / Not Completed badge.
     *
     * Course Attendance is special: both its own per-row durations AND its
     * group total/"Completed Time" are NET figures, not raw time-in-to-
     * time-out gaps — see applyNetDurationToCourseAttendance() below for
     * exactly what gets subtracted and why.
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

        $beforeChecking = $this->buildCheckpointGroup('පෙර පණගැවුම් පරික්ෂාව', $student->id, $selectedCourseId, 'before_checking');
        $afterChecking = $this->buildCheckpointGroup('පසු පණගැවුම් පරික්ෂාව', $student->id, $selectedCourseId, 'after_checking');
        $courseAttendance = $this->buildCheckpointGroup('නිරීක්ෂණ මිනිත්තුව', $student->id, $selectedCourseId, null);

        // "that duration want get that gap - පෙර පණගැවුම් පරික්ෂාව that
        // date - පසු පණගැවුම් පරික්ෂාව that date - theory subjects that
        // day - practical subjects that day" — each Course Attendance
        // ROW's own displayed duration (not just the Completed Time
        // total) is net of that same date's Before/After Checking +
        // theory/practical subject time, using the same per-date pairing
        // the Completed Time total already uses. This also recomputes
        // $courseAttendance['total_minutes']/['total_duration'] so the
        // group's own totals always match what its rows add up to.
        $courseAttendance = $this->applyNetDurationToCourseAttendance(
            $courseAttendance,
            $beforeChecking['minutesByDate'],
            $afterChecking['minutesByDate'],
            $theory['minutesByDate'],
            $practical['minutesByDate']
        );

        // "Completed Time" is simply Course Attendance's own (already-net)
        // total — the net calculation now lives entirely inside
        // applyNetDurationToCourseAttendance(), so there's nothing left to
        // subtract here.
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
            'theorySubjects' => $theory['rows'],
            'beforeChecking' => $beforeChecking,
            'afterChecking' => $afterChecking,
            'courseAttendance' => $courseAttendance,
            'doingTotalDemoTime' => $doingTotalDemoTime,
            'courseAttendanceCompleted' => $courseAttendanceCompleted,
        ]);
    }

    /**
     * One entry per subject of the given type ($subjectType: 'P' practical,
     * 'T' theory) ASSIGNED to this course/intake — with every attendance
     * instance recorded for it (possibly none), the total planned
     * Demonstrator Time / Practical Time summed across those subjects, its
     * own actual total duration plus whether that meets its planned
     * Practical Time or not, and (for the per-day Completed Time
     * calculation) a minutesByDate map of this group's minutes bucketed by
     * attendance_date.
     */
    private function buildSubjectRows(int $studentId, int $courseId, ?int $intakeId, string $subjectType): array
    {
        $assignments = AssignSubjects::where('course_id', $courseId)
            ->when($intakeId, fn($q) => $q->where('intake_id', $intakeId))
            ->get(['subject_id', 'demonstrator_time', 'practical_time'])
            ->keyBy('subject_id');

        $subjects = Subject::whereIn('id', $assignments->keys())
            ->where('type', $subjectType)
            ->orderBy('name')
            ->get(['id', 'name']);

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
        $minutesByDate = []; // combined across every subject of this type.

        foreach ($subjects as $subject) {
            $assignment = $assignments->get($subject->id);
            $subjectSessions = $sessionsBySubject->get($subject->id, collect());
            $subjectActualMinutes = 0;

            $instances = $subjectSessions->map(function ($s) use (&$subjectActualMinutes, &$minutesByDate) {
                $minutes = $this->sessionDurationMinutes($s->time_in, $s->time_out);
                $subjectActualMinutes += $minutes;

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

            $totalActualMinutes += $subjectActualMinutes;

            $plannedPracticalMinutes = $this->parsePlannedMinutes($assignment->practical_time ?? null);

            // "want practical subject one by one... get subject Practical
            // Time - that subject total duration" — the actual-vs-planned
            // total/Completed comparison shows for every subject with at
            // least one attended instance. completed stays null ("not
            // applicable") when there's no planned time to compare
            // against, or nothing attended yet.
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

    /**
     * A single subject-style group for something that ISN'T a real assigned
     * subject: Before Checking, After Checking, or (when $checkpointType is
     * null) general Course Attendance. Same shape as a subject row (so the
     * view can render it with the same table markup) but with no planned
     * Demo/Practical time, EVERY matching instance listed (not just the
     * first checkpoint of a day), its own actual total duration, and (for
     * the per-day Completed Time calculation) a minutesByDate map of this
     * group's minutes bucketed by attendance_date.
     *
     * NOTE for the Course Attendance case ($checkpointType === null): the
     * per-instance 'duration'/'total_minutes'/'total_duration' this method
     * returns are the RAW time-in-to-time-out figures. show() immediately
     * runs the result through applyNetDurationToCourseAttendance() to turn
     * those into the NET (minus same-day checking/subject time) figures
     * actually shown on the card — this method itself doesn't know about
     * that, it just returns the raw group like it always has.
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