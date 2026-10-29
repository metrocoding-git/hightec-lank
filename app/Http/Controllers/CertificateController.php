<?php

namespace App\Http\Controllers;

use App\Models\AssessmentTemplate;
use App\Models\AssignSubjects;
use App\Models\Certificate;
use App\Models\CertificateSerialPrefix;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\StudentAssessment;
use App\Models\StudentCourse;
use App\Models\Subject;
use App\Models\SubjectAttendanceSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * The certificate management page — lists every PASSed assessment
     * (whether a certificate has been generated yet or not) with filters
     * for course, certificate stage, and downloaded status.
     */
    public function index(Request $request)
    {
        $assessments = $this->filteredAssessments($request);

        // NEW — same "Student Category" batched lookup the Final
        // Assessment Marks list uses for its own column.
        $categoriesByPair = $this->resolveCourseCategoriesForRecent($assessments);

        $courseIds = AssessmentTemplate::pluck('course_id')->unique();
        $courseOptions = Course::whereIn('id', $courseIds)->orderBy('name')->get(['id', 'name']);

        // Filter dropdowns: branches (used for both evaluator branch and
        // course start branch) and the evaluators who have done assessments.
        $branchOptions = \App\Models\Branch::orderBy('name')->get(['id', 'name']);
        $evaluatorOptions = \App\Models\User::whereIn(
            'id',
            StudentAssessment::whereNotNull('evaluator_id')->distinct()->pluck('evaluator_id')
        )->orderBy('name')->get(['id', 'name']);

        return view('pages.administration.certificate.index', compact(
            'assessments',
            'courseOptions',
            'categoriesByPair',
            'branchOptions',
            'evaluatorOptions'
        ));
    }

    /**
     * Excel export of the Certificate Management list — EVERY row that
     * matches the current filters (not just the rows on screen), with the
     * same columns as the table.
     */
    public function exportExcel(Request $request)
    {
        abort_unless(optional(Auth::user())->hasPermission('certificate.index'), 403, 'Unauthorized action.');

        $assessments = $this->filteredAssessments($request);
        $categoriesByPair = $this->resolveCourseCategoriesForRecent($assessments);

        $stageLabel = fn($cert) => match (optional($cert)->status) {
            null => 'Not generated',
            'generated' => 'Awaiting Director Training',
            'conductor_signed' => 'Awaiting Managing Director',
            'approved' => 'Approved',
            default => ucfirst((string) $cert->status),
        };
        $yesNo = fn($v) => $v ? 'YES' : 'NO';

        $rows = [];
        foreach ($assessments->values() as $i => $a) {
            $cert = $a->certificate;
            $theory = $a->theoryProgress ?? ['completed' => 0, 'total' => 0];
            $practical = $a->practicalProgress ?? ['completed' => 0, 'total' => 0];
            $categoryKey = $a->student_id . '-' . optional($a->template->course)->id;

            $rows[] = [
                $i + 1,
                $a->student->student_name ?? '-',
                $a->student->admission_number ?? '-',
                $stageLabel($cert),
                $a->student->national_id_number ?? '-',
                $a->template->course->name ?? '-',
                $a->template->module_name ?? '-',
                $theory['completed'] . '/' . $theory['total'],
                $practical['completed'] . '/' . $practical['total'],
                optional($a->assessed_at)->format('Y-m-d') ?? '-',
                $a->total_marks . ' / ' . $a->max_marks,
                $a->is_qualified ? 'PASS' : 'FAIL',
                $a->grade ?? '-',
                optional($categoriesByPair->get($categoryKey))->category_name ?? '-',
                $a->student->permanent_address ?? '-',
                $yesNo($a->student && $a->student->isVerified('Full Name Verified')),
                $yesNo($a->student && $a->student->isVerified('NIC Verified')),
                $yesNo($a->student && $a->student->isVerified('Address Verified')),
                optional(optional($a->studentCourse)->intake)->start_date ?? '-',
                $a->evaluator->name ?? '-',
                optional(optional($a->studentCourse)->intake)->name ?? '-',
                optional(optional(optional($a->evaluator)->branches)->first())->name ?? '-',
                optional(optional($a->studentCourse)->branch)->name ?? '-',
                $cert->certificate_number ?? '-',
                $cert && $cert->conductor_signed_at ? 'Yes ' . $cert->conductor_signed_at->format('Y-m-d') : 'No',
                $cert && $cert->owner_signed_at ? 'Yes ' . $cert->owner_signed_at->format('Y-m-d') : 'No',
                $cert && $cert->downloaded_at ? 'Yes (' . $cert->download_count . 'x) ' . $cert->downloaded_at->format('Y-m-d') : 'No',
            ];
        }

        $headings = [
            'No', 'Student', 'Reg No', 'Stage', 'NIC Number', 'Course', 'Module',
            'Theory Subjects', 'Practical Subjects', 'Created Date', 'Total', 'Result', 'Grade',
            'Student Category', 'Address', 'Name Verified', 'NIC / ID No. Verified', 'Address Verified',
            'Start Date', 'Evaluator (Created By)', 'Intake', 'FS Conduct Branch (Evaluator)',
            'Intake Branch (Course Start)', 'Certificate #', 'Director Training Signed',
            'Managing Director Signed', 'Downloaded',
        ];

        $export = new class($rows, $headings) implements
            \Maatwebsite\Excel\Concerns\FromArray,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\ShouldAutoSize {
            public function __construct(private array $rows, private array $headings) {}

            public function array(): array
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return $this->headings;
            }
        };

        return \Maatwebsite\Excel\Facades\Excel::download(
            $export,
            'Certificate_Management_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * The filtered list shared by index() and exportExcel(): every PASSed
     * assessment matching the request's filters, with Theory/Practical
     * progress attached.
     *
     * Filters (same meaning as on Final Assessment Marks):
     *  - date_range          "Created Date Range"  -> assessed_at
     *  - start_date_range    "Course Start Date Range" -> intake start_date
     *  - evaluator_branch_id evaluator's branch ("FS Conduct Branch")
     *  - evaluator_id        evaluator name ("Created By")
     *  - course_branch_id    branch the course was started in ("Intake Branch")
     */
    private function filteredAssessments(Request $request)
    {
        // WIDENED earlier — "list want add all assment list table details
        // all": these eager loads are what the Theory/Practical Subjects,
        // Student Category, verification badges, Start Date, Intake, and
        // branch columns need.
        $query = StudentAssessment::with([
            'student.verifications',
            'template.course',
            'evaluator:id,name',
            'evaluator.branches:id,name',
            'certificate',
            'studentCourse',
            'studentCourse.intake',
            'studentCourse.branch',
        ])
            ->where('is_qualified', true)
            ->latest();

        if ($request->filled('course_id')) {
            $courseId = $request->course_id;
            $query->whereHas('template', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        if ($request->filled('cert_status')) {
            switch ($request->cert_status) {
                case 'not_generated':
                    $query->whereDoesntHave('certificate');
                    break;
                case 'pending_conductor':
                    $query->whereHas('certificate', function ($q) {
                        $q->where('status', 'generated');
                    });
                    break;
                case 'pending_owner':
                    $query->whereHas('certificate', function ($q) {
                        $q->where('status', 'conductor_signed');
                    });
                    break;
                case 'approved':
                    $query->whereHas('certificate', function ($q) {
                        $q->where('status', 'approved');
                    });
                    break;
            }
        }

        if ($request->filled('downloaded')) {
            if ($request->downloaded === 'yes') {
                $query->whereHas('certificate', function ($q) {
                    $q->whereNotNull('downloaded_at');
                });
            } elseif ($request->downloaded === 'no') {
                $query->whereHas('certificate', function ($q) {
                    $q->where('status', 'approved')->whereNull('downloaded_at');
                });
            }
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%")
                    ->orWhere('national_id_number', 'like', "%{$search}%");
            });
        }

        if ($range = $this->parseDateRange($request->input('date_range'))) {
            $query->whereBetween('assessed_at', $range);
        }

        if ($range = $this->parseDateRange($request->input('start_date_range'))) {
            $query->whereHas('studentCourse.intake', function ($q) use ($range) {
                $q->whereBetween('start_date', [$range[0]->toDateString(), $range[1]->toDateString()]);
            });
        }

        if ($request->filled('evaluator_branch_id')) {
            $branchId = (int) $request->evaluator_branch_id;
            $query->whereHas('evaluator.branches', function ($q) use ($branchId) {
                $q->where('branches.id', $branchId);
            });
        }

        if ($request->filled('evaluator_id')) {
            $query->where('evaluator_id', (int) $request->evaluator_id);
        }

        if ($request->filled('course_branch_id')) {
            $courseBranchId = (int) $request->course_branch_id;
            $query->whereHas('studentCourse', function ($q) use ($courseBranchId) {
                $q->where('branch_id', $courseBranchId);
            });
        }

        $assessments = $query->get();

        // Same Theory/Practical Subjects progress figures the Final
        // Assessment Marks list computes per row.
        $assessments->each(function ($a) {
            if ($a->studentCourse) {
                $a->theoryProgress = $this->countSubjectCompletion(
                    $a->student_id,
                    $a->studentCourse->course_id,
                    $a->studentCourse->intake_id,
                    'T'
                );
                $a->practicalProgress = $this->countSubjectCompletion(
                    $a->student_id,
                    $a->studentCourse->course_id,
                    $a->studentCourse->intake_id,
                    'P'
                );
            } else {
                $a->theoryProgress = ['completed' => 0, 'total' => 0];
                $a->practicalProgress = ['completed' => 0, 'total' => 0];
            }
        });

        return $assessments;
    }

    /**
     * "2026-10-01 to 2026-10-31" (flatpickr range), "2026-10-01 - 2026-10-31",
     * or a single date -> [from startOfDay, to endOfDay]. Null if empty or
     * unreadable (a bad value never breaks the page).
     */
    private function parseDateRange(?string $value): ?array
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $parts = array_values(array_filter(
            array_map('trim', preg_split('/\s+(?:to|-)\s+/', $value)),
            fn($p) => $p !== ''
        ));

        try {
            $from = Carbon::parse($parts[0])->startOfDay();
            $to = Carbon::parse($parts[1] ?? $parts[0])->endOfDay();
        } catch (\Exception $e) {
            return null;
        }

        return $from->lte($to) ? [$from, $to] : [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
    }

    /**
     * Batched "Student Category (from the course-related invoice)" lookup
     * for the whole $assessments list in ONE query — copied verbatim from
     * StudentAssessmentController::resolveCourseCategoriesForRecent().
     * Deliberately duplicated rather than calling into that controller
     * (this app has no shared base/trait between them; see the
     * doc-comment on countSubjectCompletion() below for the same
     * reasoning) — kept byte-for-byte identical on purpose so the two
     * lists' "Student Category" column can never silently drift apart.
     */
    private function resolveCourseCategoriesForRecent($recent)
    {
        $pairs = $recent
            ->map(fn($a) => ['student_id' => $a->student_id, 'course_id' => optional($a->template->course)->id])
            ->filter(fn($p) => $p['course_id']);

        if ($pairs->isEmpty()) {
            return collect();
        }

        $rows = DB::table('fee_invoices')
            ->join('paid_courses', 'paid_courses.invoice_id', '=', 'fee_invoices.id')
            ->leftJoin('students', 'students.id', '=', 'fee_invoices.student_id')
            ->leftJoin('student_categories', function ($join) {
                $join->on(
                    'student_categories.id',
                    '=',
                    DB::raw('COALESCE(fee_invoices.student_category_id, students.student_category_id)')
                );
            })
            ->whereIn('fee_invoices.student_id', $pairs->pluck('student_id')->unique())
            ->whereIn('paid_courses.course_id', $pairs->pluck('course_id')->unique())
            ->where('fee_invoices.payment_status', '!=', 'returned')
            ->select('fee_invoices.student_id', 'paid_courses.course_id', 'student_categories.category_name')
            ->get();

        return $rows->keyBy(fn($r) => $r->student_id . '-' . $r->course_id);
    }

    /**
     * How many of the subjects of $subjectType ('P' or 'T') assigned to
     * this course/intake has the student COMPLETED, out of how many are
     * assigned in total — copied verbatim from
     * StudentAssessmentController::countSubjectCompletion() (see that
     * method's own doc-comment for the exact completion rule). Deliberately
     * duplicated here rather than calling into StudentAssessmentController
     * (this app has no shared base/trait between these two controllers) so
     * this page's Theory/Practical Subjects columns can never silently
     * drift out of sync with the Final Assessment Marks list's own
     * columns.
     */
    private function countSubjectCompletion(int $studentId, int $courseId, ?int $intakeId, string $subjectType): array
    {
        $assignments = AssignSubjects::where('course_id', $courseId)
            ->when($intakeId, fn($q) => $q->where('intake_id', $intakeId))
            ->get(['subject_id', 'practical_time'])
            ->keyBy('subject_id');

        $subjects = Subject::whereIn('id', $assignments->keys())
            ->where('type', $subjectType)
            ->get(['id']);

        if ($subjects->isEmpty()) {
            return ['completed' => 0, 'total' => 0];
        }

        $minutesBySubject = SubjectAttendanceSession::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->whereIn('subject_id', $subjects->pluck('id'))
            ->whereNull('checkpoint_type')
            ->get(['subject_id', 'time_in', 'time_out'])
            ->groupBy('subject_id')
            ->map(function ($sessions) {
                return $sessions->sum(fn($s) => $this->sessionDurationMinutes($s->time_in, $s->time_out));
            });

        $completed = 0;

        foreach ($subjects as $subject) {
            $planned = $this->parsePlannedMinutes($assignments->get($subject->id)->practical_time ?? null);
            $actual = $minutesBySubject->get($subject->id, 0);

            if ($subjectType == 'P') {
                if ($planned > 0 && $actual >= $planned) {
                    $completed++;
                }
            }
            if ($subjectType == 'T') {
                if ($actual > 0) {
                    $completed++;
                }
            }
        }

        return ['completed' => $completed, 'total' => $subjects->count()];
    }

    /**
     * Same hardened per-session minute calculation as
     * StudentAssessmentController::sessionDurationMinutes() — copied
     * verbatim (see the doc-comment on countSubjectCompletion() above).
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
     * Same parser as StudentAssessmentController::parsePlannedMinutes() —
     * copied verbatim (see the doc-comment on countSubjectCompletion()
     * above).
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
     * Generate certificate(s) for one or many PASSed student_assessments.
     * Body: { student_assessment_ids: [1,2,3], issue_date, course_end_date }
     *
     * UPDATED this round:
     *  - certificate_template_id is GONE from the request — "course
     *    template want auto select (because course has only one
     *    template)". Each assessment's own course is resolved and its
     *    ONE active CertificateTemplate (via the certificate_template_course
     *    pivot) is looked up automatically. If a course has no template
     *    linked yet, that assessment is skipped with a clear reason
     *    rather than failing the whole batch.
     *  - course_end_date is now a required input (see the new column on
     *    certificates) — "genarate certficate want add course end date".
     *  - "course start date is future date, then can't be genarate
     *    certificate" and "course start date and course end date duration
     *    want check" are both enforced PER ASSESSMENT below, since a bulk
     *    selection can span different students/intakes/courses with
     *    different start dates and required durations.
     */
    public function generate(Request $request)
    {
        $data = $request->validate([
            'student_assessment_ids'   => 'required|array|min:1',
            'student_assessment_ids.*' => 'integer|distinct',
            'issue_date'               => 'nullable|date',
            'course_end_date'          => 'required|date',
        ]);

        $issueDate = $data['issue_date'] ?? now()->toDateString();
        $courseEndDate = Carbon::parse($data['course_end_date'])->startOfDay();

        $created = [];
        $skipped = [];

        foreach ($data['student_assessment_ids'] as $assessmentId) {
            $assessment = StudentAssessment::with([
                'certificate',
                'student:id,student_name,admission_number',
                'studentCourse.intake',
                'studentCourse.course',
                'template.course',
            ])->find($assessmentId);

            if (!$assessment) {
                $skipped[] = ['id' => $assessmentId, 'reason' => 'Assessment not found'];
                continue;
            }
            if (!$assessment->is_qualified) {
                $skipped[] = ['id' => $assessmentId, 'reason' => 'Not a PASS result'];
                continue;
            }
            if ($assessment->certificate) {
                $skipped[] = ['id' => $assessmentId, 'reason' => 'Certificate already generated'];
                continue;
            }

            // "course template want auto select (because course has only
            // one templete)" — resolve the course, then the ONE active
            // CertificateTemplate linked to it via certificate_template_course.
            $course = optional($assessment->studentCourse)->course ?: optional($assessment->template)->course;

            if (!$course) {
                $skipped[] = ['id' => $assessmentId, 'reason' => 'Could not determine the course for this assessment'];
                continue;
            }

            $certificateTemplate = CertificateTemplate::active()
                ->whereHas('courses', fn($q) => $q->where('courses.id', $course->id))
                ->first();

            if (!$certificateTemplate) {
                $skipped[] = [
                    'id' => $assessmentId,
                    'reason' => "No certificate template is linked to course \"{$course->name}\" yet — link one under Certificate Templates first.",
                ];
                continue;
            }

            // "that student can be have that course only one stated in
            // inatke.. if has 2 or more ... cant be genarate" — the start
            // date comes from Student Intake & Courses, so it's only reliable
            // when the student has exactly ONE started/completed enrolment in
            // this course (returned/pending ones don't count).
            //
            // FIX: the old check only looked at enrolments with exactly the
            // same course_id, so it missed two start dates coming from
            // LINKED courses — courses linked to the same certificate
            // template (e.g. "Crawler Excavator Operator" and "Excavator
            // Operator (Discount Package)") or an enrolment whose course was
            // changed into this one (change_course_id). Those all end up on
            // the same certificate, so every one of them is counted now.
            $linkedCourseIds = $this->linkedCourseIds(
                $certificateTemplate,
                $course->id,
                optional($assessment->studentCourse)->change_course_id
            );

            $activeEnrolments = StudentCourse::with(['intake:id,name,start_date', 'course:id,name', 'change_course:id,name'])
                ->where('student_id', $assessment->student_id)
                ->whereIn('status', ['start', 'complete'])
                ->where(function ($q) use ($linkedCourseIds) {
                    $q->whereIn('course_id', $linkedCourseIds)
                        ->orWhereIn('change_course_id', $linkedCourseIds);
                })
                ->get();

            if ($activeEnrolments->count() > 1) {
                $details = $activeEnrolments
                    ->map(function ($e) {
                        $courseName = optional($e->change_course)->name ?? optional($e->course)->name ?? ('course #' . $e->course_id);
                        $intakeName = optional($e->intake)->name ?? ('intake #' . $e->intake_id);
                        $start = optional($e->intake)->start_date
                            ? Carbon::parse($e->intake->start_date)->format('Y-m-d')
                            : 'no start date';
                        return "{$courseName} — {$intakeName}, start {$start}";
                    })
                    ->implode('; ');

                $distinctStarts = $activeEnrolments
                    ->map(fn($e) => optional($e->intake)->start_date ? Carbon::parse($e->intake->start_date)->toDateString() : null)
                    ->unique()
                    ->count();

                $what = $distinctStarts > 1
                    ? "{$distinctStarts} different start dates"
                    : "{$activeEnrolments->count()} started enrolments";

                $skipped[] = [
                    'id' => $assessmentId,
                    'reason' => "{$this->studentLabel($assessment)} has {$what} across linked courses for this certificate ({$details}) — the certificate can't be generated. Keep only one started enrolment in Student Intake & Courses (return the other).",
                ];
                continue;
            }

            // "cant be genarate has some course balance (only course
            // balance)" — course invoices only (not other fee types).
            $balance = $this->courseBalance($assessment->student_id, $course->id);

            if ($balance === null) {
                $skipped[] = [
                    'id' => $assessmentId,
                    'reason' => "{$this->studentLabel($assessment)} has no course invoice for \"{$course->name}\" — cannot confirm the course fee is paid.",
                ];
                continue;
            }

            if ($balance > 0) {
                $skipped[] = [
                    'id' => $assessmentId,
                    'reason' => "{$this->studentLabel($assessment)} still has a course balance of Rs. " . number_format($balance, 2) . " for \"{$course->name}\".",
                ];
                continue;
            }

            // "genarate issue date ... next genarate cant be do past date" —
            // per TEMPLATE: courses sharing one certificate template share one
            // date order; another template's dates don't affect it.
            $lastIssueDate = $this->lastIssueDate($certificateTemplate->id);

            if ($lastIssueDate && Carbon::parse($issueDate)->startOfDay()->lt($lastIssueDate)) {
                $skipped[] = [
                    'id' => $assessmentId,
                    'reason' => "{$this->studentLabel($assessment)}: Date of Issue can't be earlier than the last \"{$certificateTemplate->name}\" certificate's issue date ({$lastIssueDate->format('Y-m-d')}).",
                ];
                continue;
            }

            // "course start date is future date, then can't be genarate
            // certificate" — the real, scheduled start date is the
            // intake's start_date (the same value the Final Assessment
            // Marks list's "Start Date" column already reads), not when
            // the enrollment row happened to be created in the DB.
            $intakeStartDate = optional(optional($assessment->studentCourse)->intake)->start_date;

            if (!$intakeStartDate) {
                $skipped[] = ['id' => $assessmentId, 'reason' => 'Course start date (intake) is not set for this student — cannot validate.'];
                continue;
            }

            $courseStartDate = Carbon::parse($intakeStartDate)->startOfDay();

            if ($courseStartDate->isFuture()) {
                $skipped[] = [
                    'id' => $assessmentId,
                    'reason' => "Course start date ({$courseStartDate->format('Y-m-d')}) is in the future — the course hasn't started yet.",
                ];
                continue;
            }

            if ($courseEndDate->lessThan($courseStartDate)) {
                $skipped[] = [
                    'id' => $assessmentId,
                    'reason' => "Course End Date can't be before the course start date ({$courseStartDate->format('Y-m-d')}).",
                ];
                continue;
            }

            // "course start date and course end date durattion want check"
            // — the gap between the two must cover the course's own
            // official duration (courses.duration, e.g. "2 Months" -> 60
            // days). See courseDurationInDays() below for the exact
            // mapping. A duration string that can't be parsed skips this
            // check entirely rather than blocking generation.
            $actualDays = $courseStartDate->diffInDays($courseEndDate);
            $requiredDays = $this->courseDurationInDays($course->duration ?? null);

            if ($requiredDays !== null && $actualDays < $requiredDays) {
                $skipped[] = [
                    'id' => $assessmentId,
                    'reason' => "Course \"{$course->name}\" needs at least {$requiredDays} day(s) ({$course->duration}) between start and end date — {$courseStartDate->format('Y-m-d')} to {$courseEndDate->format('Y-m-d')} is only {$actualDays} day(s).",
                ];
                continue;
            }

            $certificate = DB::transaction(function () use ($assessment, $certificateTemplate, $issueDate, $courseEndDate) {
                // "certificate want serial number. template has serial
                // then add number order" — a template with a Serial
                // Number Prefix configured (Certificate Templates screen)
                // now drives the certificate's actual number via that
                // series (e.g. "STB-NW-0001", next one "STB-NW-0002", ...
                // — CertificateSerialPrefix::nextSerialFor() is the exact
                // atomically-incrementing helper that screen already
                // built for this, just not wired in until now). A
                // template with NO prefix configured keeps the original
                // global "HTL-YYYY-NNNNN" numbering, unchanged.
                $certificateNumber = $certificateTemplate->serial_prefix_id
                    ? CertificateSerialPrefix::nextSerialFor($certificateTemplate->serial_prefix_id)
                    : $this->nextCertificateNumber();

                $certificate = Certificate::create([
                    'student_assessment_id'   => $assessment->id,
                    'certificate_template_id' => $certificateTemplate->id,
                    'issue_date'              => $issueDate,
                    'course_end_date'         => $courseEndDate->toDateString(),
                    'certificate_number'      => $certificateNumber,
                    'status'                  => 'generated',
                    'generated_by'            => Auth::id(),
                    'generated_at'            => now(),
                ]);

                // "certificate genarate time want auto that StudentCourse
                // status complete" — the enrolment a certificate is
                // generated for is done at that point; auto-complete it
                // the same way the Student Add Intake list's own
                // "Complete" row-action already does (same 'complete'
                // status value, StudentController::updateStatus()).
                // Guarded on ->studentCourse existing since $course above
                // can fall back to $assessment->template->course when
                // there's no linked enrolment at all.
                if ($assessment->studentCourse) {
                    $assessment->studentCourse->status = 'complete';
                    $assessment->studentCourse->save();
                }

                $certificate->pdf_path = $this->renderCertificatePdf($certificate->fresh(['studentAssessment']));
                $certificate->save();

                return $certificate;
            });

            $created[] = $certificate->id;
        }

        return response()->json([
            'success' => true,
            'created' => $created,
            'skipped' => $skipped,
        ]);
    }

    /**
     * Every course that ends up on the same certificate as $courseId: the
     * course itself, the enrolment's changed course (if any), and all
     * courses linked to the same certificate template.
     */
    private function linkedCourseIds(CertificateTemplate $template, int $courseId, $changeCourseId = null): array
    {
        $ids = DB::table('certificate_template_course')
            ->where('certificate_template_id', $template->id)
            ->pluck('course_id')
            ->map(fn($id) => (int) $id)
            ->all();

        $ids[] = $courseId;
        if ($changeCourseId) {
            $ids[] = (int) $changeCourseId;
        }

        return array_values(array_unique($ids));
    }

    /** Latest issue date used so far by certificates of this template (null if none). */
    private function lastIssueDate(int $templateId): ?Carbon
    {
        $last = Certificate::where('certificate_template_id', $templateId)->max('issue_date');

        return $last ? Carbon::parse($last)->startOfDay() : null;
    }

    /**
     * Course-fee balance for this student + course: total minus paid over
     * every course invoice (invoice_type 'course', not returned) linked to
     * the course via paid_courses. Null when there is no such invoice at all.
     */
    private function courseBalance(int $studentId, int $courseId): ?float
    {
        $invoiceIds = DB::table('fee_invoices')
            ->join('paid_courses', 'paid_courses.invoice_id', '=', 'fee_invoices.id')
            ->where('fee_invoices.student_id', $studentId)
            ->where('fee_invoices.invoice_type', 'course')
            ->where('fee_invoices.payment_status', '!=', 'returned')
            ->where('paid_courses.course_id', $courseId)
            ->distinct()
            ->pluck('fee_invoices.id');

        if ($invoiceIds->isEmpty()) {
            return null;
        }

        $totals = DB::table('fee_invoices')
            ->whereIn('id', $invoiceIds)
            ->selectRaw('SUM(total_amount) as total, SUM(paid_amount) as paid')
            ->first();

        return round((float) $totals->total - (float) $totals->paid, 2);
    }

    private function studentLabel(StudentAssessment $assessment): string
    {
        $student = $assessment->student;

        return $student ? ($student->admission_number . ' ' . $student->student_name) : 'This student';
    }

    /**
     * "course start date and course end date durattion want check (course
     * table has duration. duration: 2 weeks , 1 month , 2 month .. if 2
     * weeks days 14, if 1 month 30 days , if 2 month 60 dates" — courses
     * .duration is a free-text string; this pulls the leading number and
     * unit out of it and converts to a flat day count using 7-day weeks /
     * 30-day months, matching the examples given exactly ("2 weeks" ->
     * 14, "1 month" -> 30, "2 month"/"2 months" -> 60). Returns null if
     * it can't be parsed (empty, or an unrecognized format), so the
     * caller skips the duration check rather than wrongly blocking
     * generation on a value it can't understand.
     */
    private function courseDurationInDays(?string $duration): ?int
    {
        if (!$duration) {
            return null;
        }

        if (!preg_match('/(\d+(?:\.\d+)?)\s*(day|week|month)/i', trim($duration), $m)) {
            return null;
        }

        $amount = (float) $m[1];
        $unit = strtolower($m[2]);

        $daysPerUnit = match ($unit) {
            'day' => 1,
            'week' => 7,
            'month' => 30,
            default => null,
        };

        return $daysPerUnit ? (int) round($amount * $daysPerUnit) : null;
    }

    /**
     * STEP 1 of signing — Director Training signs first (was labeled
     * "Assessment Conductor" — renamed in the UI only, see the earlier
     * round's chat message; the underlying status value 'conductor_signed',
     * column names, route names and permission key are all UNCHANGED on
     * purpose). Only certificates still in 'generated' status are eligible.
     * Moves status to 'conductor_signed' (still NOT downloadable — Managing
     * Director still has to sign).
     * Body: { certificate_ids: [1,2,3], conductor_signature_data | conductor_signature_file }
     */
    public function signConductor(Request $request)
    {
        $data = $request->validate([
            'certificate_ids'          => 'required|array|min:1',
            'certificate_ids.*'        => 'integer|distinct',
            'conductor_signature_data' => 'nullable|string',
            'conductor_signature_file' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        if (empty($data['conductor_signature_data']) && !$request->hasFile('conductor_signature_file')) {
            return response()->json(['success' => false, 'message' => 'Director Training signature is required (draw or upload).'], 422);
        }

        $path = $request->hasFile('conductor_signature_file')
            ? $this->storeUploadedSignature($request->file('conductor_signature_file'), 'conductor')
            : $this->storeSignatureData($data['conductor_signature_data'], 'conductor');

        $signed = [];
        $skipped = [];

        foreach ($data['certificate_ids'] as $certId) {
            $certificate = Certificate::with('studentAssessment')->find($certId);

            if (!$certificate) {
                $skipped[] = ['id' => $certId, 'reason' => 'Certificate not found'];
                continue;
            }
            if (!$certificate->needsConductorSignature()) {
                $skipped[] = ['id' => $certId, 'reason' => 'Not awaiting the Director Training signature (already signed, or not generated)'];
                continue;
            }

            $certificate->conductor_signature_path = $path;
            $certificate->conductor_signed_by = Auth::id();
            $certificate->conductor_signed_at = now();
            $certificate->status = 'conductor_signed';
            $certificate->pdf_path = $this->renderCertificatePdf($certificate);
            $certificate->save();

            $signed[] = $certificate->id;
        }

        return response()->json([
            'success' => true,
            'signed'  => $signed,
            'skipped' => $skipped,
        ]);
    }

    /**
     * STEP 2 of signing — Managing Director signs second, after Director
     * Training (was labeled "Owner" — renamed in the UI only; the
     * underlying status value 'approved', column names ('owner_*'), route
     * name and permission key are all UNCHANGED). Only certificates in
     * 'conductor_signed' status are eligible. This is what finally moves
     * status to 'approved' and unlocks download.
     * Body: { certificate_ids: [1,2,3], owner_signature_data | owner_signature_file }
     */
    public function signOwner(Request $request)
    {
        $data = $request->validate([
            'certificate_ids'      => 'required|array|min:1',
            'certificate_ids.*'    => 'integer|distinct',
            'owner_signature_data' => 'nullable|string',
            'owner_signature_file' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        if (empty($data['owner_signature_data']) && !$request->hasFile('owner_signature_file')) {
            return response()->json(['success' => false, 'message' => 'Managing Director signature is required (draw or upload).'], 422);
        }

        $path = $request->hasFile('owner_signature_file')
            ? $this->storeUploadedSignature($request->file('owner_signature_file'), 'owner')
            : $this->storeSignatureData($data['owner_signature_data'], 'owner');

        $approved = [];
        $skipped = [];

        foreach ($data['certificate_ids'] as $certId) {
            $certificate = Certificate::with('studentAssessment')->find($certId);

            if (!$certificate) {
                $skipped[] = ['id' => $certId, 'reason' => 'Certificate not found'];
                continue;
            }
            if (!$certificate->needsOwnerSignature()) {
                $skipped[] = ['id' => $certId, 'reason' => 'Not awaiting the Managing Director signature (Director Training must sign first, or it is already approved)'];
                continue;
            }

            $certificate->owner_signature_path = $path;
            $certificate->owner_signed_by = Auth::id();
            $certificate->owner_signed_at = now();
            $certificate->status = 'approved';
            $certificate->pv_number = $this->nextPvNumber();
            $certificate->pdf_path = $this->renderCertificatePdf($certificate);
            $certificate->save();

            $approved[] = $certificate->id;
        }

        return response()->json([
            'success'  => true,
            'approved' => $approved,
            'skipped'  => $skipped,
        ]);
    }

    /**
     * "want to can be go back one like if only do genarate can be do undo
     * to that if do sign conductor can be do undo to genarate level if do
     * sign owner sign can be do undo to sign conductor if complete sign
     * owner can be undow that" — steps a certificate back exactly ONE
     * stage from wherever it currently is:
     *   approved          -> conductor_signed
     *   conductor_signed  -> generated
     *   generated         -> not generated at all (deletes the row + PDF)
     *
     * Deliberately does NOT clear downloaded_at/downloaded_by/download_count
     * — "if download want to aleret that donwloded" is handled client-side
     * (a warning before the confirm, see certificate-index.blade.php),
     * not by erasing that a download happened.
     *
     * "if genarate undo status go back in StudentCourse?" — yes, but only
     * in the 'generated' case below, where the certificate is deleted
     * outright. Stepping back from approved->conductor_signed or
     * conductor_signed->generated still leaves a real Certificate row in
     * place, so the enrolment is still genuinely done — only undoing the
     * ORIGINAL generate (the one that set status = 'complete' in
     * generate() above) reverses that side effect too.
     *
     * Body: { certificate_ids: [1,2,3] }
     */
    public function undo(Request $request)
    {
        $data = $request->validate([
            'certificate_ids'   => 'required|array|min:1',
            'certificate_ids.*' => 'integer|distinct',
        ]);

        $reverted = [];
        $skipped = [];

        foreach ($data['certificate_ids'] as $certId) {
            $certificate = Certificate::with('studentAssessment.studentCourse')->find($certId);

            if (!$certificate) {
                $skipped[] = ['id' => $certId, 'reason' => 'Certificate not found'];
                continue;
            }

            switch ($certificate->status) {
                case 'approved':
                    if ($certificate->owner_signature_path) {
                        Storage::disk('local')->delete($certificate->owner_signature_path);
                    }
                    $certificate->owner_signature_path = null;
                    $certificate->owner_signed_by = null;
                    $certificate->owner_signed_at = null;
                    $certificate->status = 'conductor_signed';
                    $certificate->pv_number = null;
                    $certificate->pdf_path = $this->renderCertificatePdf($certificate);
                    $certificate->save();
                    $reverted[] = ['id' => $certificate->id, 'to' => 'conductor_signed'];
                    break;

                case 'conductor_signed':
                    if ($certificate->conductor_signature_path) {
                        Storage::disk('local')->delete($certificate->conductor_signature_path);
                    }
                    $certificate->conductor_signature_path = null;
                    $certificate->conductor_signed_by = null;
                    $certificate->conductor_signed_at = null;
                    $certificate->status = 'generated';
                    $certificate->pdf_path = $this->renderCertificatePdf($certificate);
                    $certificate->save();
                    $reverted[] = ['id' => $certificate->id, 'to' => 'generated'];
                    break;

                case 'generated':
                    if ($certificate->pdf_path) {
                        Storage::disk('local')->delete($certificate->pdf_path);
                    }

                    // "if genarate undo status go back in StudentCourse?" —
                    // undoing the original generate() reverses its
                    // status = 'complete' side effect too, back to
                    // 'start' (the status an enrolment has for the
                    // entire time it's actively being trained — the same
                    // state Student Add Intake's own Start row-action
                    // sets). Guarded the same way generate() itself was
                    // — a certificate with no linked studentCourse simply
                    // has nothing to revert. Wrapped in a transaction,
                    // same as generate() itself, so the status revert and
                    // the certificate delete either both happen or
                    // neither does.
                    DB::transaction(function () use ($certificate) {
                        $studentCourse = optional($certificate->studentAssessment)->studentCourse;
                        if ($studentCourse) {
                            $studentCourse->status = 'start';
                            $studentCourse->save();
                        }

                        $certificate->delete();
                    });

                    $reverted[] = ['id' => $certId, 'to' => 'not_generated'];
                    break;

                default:
                    $skipped[] = ['id' => $certId, 'reason' => 'Unrecognized certificate status: ' . $certificate->status];
            }
        }

        return response()->json([
            'success'  => true,
            'reverted' => $reverted,
            'skipped'  => $skipped,
        ]);
    }

    /**
     * Download a single approved certificate PDF. Records who/when it was
     * downloaded and bumps download_count, so the management page's
     * "already downloaded" filter has something to key off.
     */
    public function download($id)
    {
        $certificate = Certificate::with('studentAssessment.student')->findOrFail($id);

        if (!$certificate->isApproved() || !$certificate->pdf_path || !Storage::disk('local')->exists($certificate->pdf_path)) {
            abort(403, 'Certificate is not fully approved yet (both Director Training and Managing Director must sign first).');
        }

        $this->recordDownload($certificate);

        $studentName = optional($certificate->studentAssessment->student)->student_name ?? 'certificate';
        $filename = Str::slug($studentName) . '-' . $this->safeFilenameSegment($certificate->certificate_number) . '.pdf';

        return Storage::disk('local')->download($certificate->pdf_path, $filename);
    }

    /**
     * Bulk download: every selected, fully approved certificate merged into
     * ONE PDF (front + back pages of each, in the order they were selected),
     * instead of a ZIP of separate files. Each included certificate is
     * marked downloaded.
     * Body: { certificate_ids: [1,2,3] }
     */
    public function bulkDownload(Request $request)
    {
        $data = $request->validate([
            'certificate_ids'   => 'required|array|min:1',
            'certificate_ids.*' => 'integer|distinct',
        ]);

        $order = array_flip(array_map('intval', $data['certificate_ids']));

        $downloadable = Certificate::with('studentAssessment.student')
            ->whereIn('id', $data['certificate_ids'])
            ->get()
            ->filter(fn($c) => $c->isApproved() && $c->pdf_path && Storage::disk('local')->exists($c->pdf_path))
            ->sortBy(fn($c) => $order[$c->id] ?? PHP_INT_MAX)
            ->values();

        if ($downloadable->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'None of the selected certificates are fully approved/downloadable yet.'], 422);
        }

        if (!class_exists(\setasign\Fpdi\Fpdi::class)) {
            return response()->json(['success' => false, 'message' => 'PDF merge library missing. Run: composer require setasign/fpdi-fpdf'], 500);
        }

        // Large batches: give the merge enough time/memory.
        @set_time_limit(300);

        $merged = new \setasign\Fpdi\Fpdi();
        $merged->SetAutoPageBreak(false);
        $included = [];
        $failed = [];

        foreach ($downloadable as $certificate) {
            try {
                $pageCount = $merged->setSourceFile(Storage::disk('local')->path($certificate->pdf_path));

                for ($i = 1; $i <= $pageCount; $i++) {
                    $templateId = $merged->importPage($i);
                    $size = $merged->getTemplateSize($templateId);
                    $merged->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $merged->useTemplate($templateId);
                }

                $included[] = $certificate;
            } catch (\Throwable $e) {
                // One unreadable PDF shouldn't stop the rest.
                \Illuminate\Support\Facades\Log::warning("Bulk certificate merge skipped #{$certificate->id} ({$certificate->certificate_number}): " . $e->getMessage());
                $failed[] = $certificate->certificate_number;
            }
        }

        if (empty($included)) {
            return response()->json(['success' => false, 'message' => 'The selected certificate PDFs could not be read.'], 500);
        }

        foreach ($included as $certificate) {
            $this->recordDownload($certificate);
        }

        $filename = 'certificates-' . count($included) . '-' . now()->format('Ymd-His') . '.pdf';

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        if ($failed) {
            $headers['X-Skipped-Certificates'] = implode(', ', $failed);
        }

        return response($merged->Output('S'), 200, $headers);
    }

    /**
     * HTL-2026-00001 style numbering — kept as-is this round. You confirmed
     * (answer to the numbering question) that this global scheme should
     * stay rather than switching to the sample's course-prefixed
     * "FL/OP/5255" style — that format was just this particular course's
     * own paper stock, not a numbering convention to copy.
     */
    private function nextCertificateNumber(): string
    {
        $year = now()->format('Y');
        $count = Certificate::whereYear('created_at', $year)->count() + 1;

        do {
            $number = sprintf('HTL-%s-%05d', $year, $count);
            $count++;
        } while (Certificate::where('certificate_number', $number)->exists());

        return $number;
    }


    private function nextPvNumber(): string
    {
        do {
            $number = 'PV ' . random_int(100000, 999999);
        } while (Certificate::where('pv_number', $number)->exists());

        return $number;
    }

    private function safeFilenameSegment(?string $value): string
    {
        return str_replace(['/', '\\'], '-', (string) $value);
    }

    private function recordDownload(Certificate $certificate): void
    {
        if (!$certificate->downloaded_at) {
            $certificate->downloaded_at = now();
        }
        $certificate->downloaded_by = Auth::id();
        $certificate->download_count = $certificate->download_count + 1;
        $certificate->save();
    }

    /**
     * Store a base64 data: URL (from a signature_pad canvas) as a PNG file.
     */
    private function storeSignatureData(string $dataUrl, string $role): string
    {
        if (!preg_match('/^data:image\/(png|jpe?g);base64,(.+)$/', $dataUrl, $matches)) {
            abort(422, "Invalid {$role} signature data.");
        }

        $extension = $matches[1] === 'jpg' ? 'jpeg' : $matches[1];
        $binary = base64_decode($matches[2]);

        $path = 'certificates/signatures/' . $role . '-' . Str::uuid() . '.' . $extension;
        Storage::disk('local')->put($path, $binary);

        return $path;
    }

    /**
     * Store an uploaded PNG/JPG signature file.
     */
    private function storeUploadedSignature($file, string $role): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'png';
        $path = 'certificates/signatures/' . $role . '-' . Str::uuid() . '.' . $extension;

        Storage::disk('local')->put($path, file_get_contents($file->getRealPath()));

        return $path;
    }

    private function renderCertificatePdf(Certificate $certificate): string
    {
        $certificate->loadMissing([
            'studentAssessment.student',
            'studentAssessment.template.course',
            'studentAssessment.evaluator',
            'studentAssessment.studentCourse.intake',
            'studentAssessment.studentCourse.branch',
            'certificateTemplate',
        ]);

        $studentCourse = optional($certificate->studentAssessment)->studentCourse;


        $startDate = optional(optional($studentCourse)->intake)->start_date
            ? Carbon::parse($studentCourse->intake->start_date)
            : null;

        $endDate = $certificate->course_end_date ? Carbon::parse($certificate->course_end_date) : null;

        $durationWords = optional($certificate->certificateTemplate)->duration;

        $bottomImagePath = optional($certificate->certificateTemplate)->bottom_image_path;
        $bottomImageDims = $this->scaledImageDimensions($bottomImagePath, 'public', 123, 92);


        $qrImagePath = optional($certificate->certificateTemplate)->qr_code_image_path;
        $qrImageDims = $this->scaledImageDimensions($qrImagePath, 'public', 55, 55);

        $branchName = optional(optional($studentCourse)->branch)->name;
        $footerAddress = $this->branchFooterAddress($branchName);
        $tvecRegNo = $this->branchTvecNumber($branchName);

        $studentName = optional(optional($certificate->studentAssessment)->student)->student_name ?? '';
        $nameLength = mb_strlen($studentName);
        $nameFontSize = $nameLength > 41 ? max(10, round(20 * (41 / $nameLength))) : 14;

        $pdf = Pdf::loadView('pages.administration.certificate.pdf', [
            'certificate'               => $certificate,
            'template'                  => $certificate->certificateTemplate,
            'startDate'                 => $startDate,
            'endDate'                   => $endDate,
            'durationWords'             => $durationWords,
            'nameFontSize'              => $nameFontSize,
            // Fallback only — see the comment above $qrImagePath.
            'qrDataUri'                 => $qrImagePath ? null : $this->buildQrDataUri($certificate),
            'qrImageDataUri'            => $qrImagePath
                ? $this->fileToDataUri($qrImagePath, 'public')
                : null,
            'qrImageWidth'              => $qrImageDims['width'] ?? null,
            'qrImageHeight'             => $qrImageDims['height'] ?? null,
            'footerAddress'             => $footerAddress,
            'tvecRegNo'                 => $tvecRegNo,
            'headingFontDataUri'        => $this->publicFontToDataUri('UnifrakturCook-Bold.ttf'),
            'courseFontDataUri'         => $this->publicFontToDataUri('Orbitron-VariableFont_wght.ttf'),
            'bottomImageDataUri'        => $bottomImagePath
                ? $this->fileToDataUri($bottomImagePath, 'public')
                : null,
            'bottomImageWidth'          => $bottomImageDims['width'] ?? null,
            'bottomImageHeight'         => $bottomImageDims['height'] ?? null,
            // "signs are want come blue color like orginal pen sign"
            'ownerSignatureDataUri'     => $this->signatureToBlueDataUri($certificate->owner_signature_path),
            'conductorSignatureDataUri' => $this->signatureToBlueDataUri($certificate->conductor_signature_path),
            'nameFontDataUri'           => $this->fontToDataUri('certificate-name-font.ttf'),
            'gradeFontDataUri'          => $this->fontToDataUri('certificate-grade-font.ttf'),
        ])->setPaper('letter', 'portrait');

        // "certificate want join back side page to next page" — the
        // template's uploaded back-side PDF (certificate_templates
        // .back_side_pdf_path, added a couple rounds ago) is appended as
        // real extra page(s) after the front page dompdf just rendered,
        // rather than being left out entirely (dompdf can only render
        // HTML/CSS, it can't embed an already-existing PDF's pages —
        // that needs a separate PDF-merge step, see mergeBackSide()).
        $finalPdfBinary = $this->mergeBackSide(
            $pdf->output(),
            optional($certificate->certificateTemplate)->back_side_pdf_path
        );

        $path = 'certificates/pdf/' . $certificate->certificate_number . '.pdf';
        Storage::disk('local')->put($path, $finalPdfBinary);

        return $path;
    }

    private function branchFooterAddress(?string $branchName): string
    {
        $branchName = $branchName ? trim($branchName) : '';

        return match (true) {
            stripos($branchName, 'kurunegala') !== false => 'No. 549, Kandy Road, Mawathagama, Sri Lanka.',
            stripos($branchName, 'kaduwela') !== false => 'No. 404/51C, Velkkiyawattha Road,Ihala Bomiriya, Kaduwela, Sri Lanka.',
            // Fallback for a branch that's neither of the two named ones
            // (or no branch on record at all) — same as Kurunegala for
            // now, since that's the only confirmed address I have.
            default => 'No. 549, Kandy Road, Mawathagama, Sri Lanka.',
        };
    }

    private function branchTvecNumber(?string $branchName): string
    {
        $branchName = $branchName ? trim($branchName) : '';

        return match (true) {
            stripos($branchName, 'kurunegala') !== false => 'TVEC Re. No. P12/0113',
            stripos($branchName, 'kaduwela') !== false   => 'TVEC Re. No. P01/1255',
            // Other branch or no branch: same fallback as the address (Kurunegala)
            default => 'TVEC Re. No. P12/1255',
        };
    }

    private function publicFontToDataUri(string $filename): ?string
    {
        $path = public_path('fonts/' . $filename);

        return is_file($path)
            ? 'data:font/ttf;base64,' . base64_encode(file_get_contents($path))
            : null;
    }

    /**
     * Appends the template's uploaded back-side PDF (stored on the
     * 'public' disk, same as the bottom image / QR code) as extra page(s)
     * after $frontPdfBinary (the dompdf-rendered certificate front page),
     * producing one combined PDF. Uses setasign/fpdi, which can import
     * pages from an existing PDF file and place them into a new one —
     * dompdf itself has no such capability (it only renders HTML/CSS).
     *
     * OPTIONAL, guarded by class_exists — same pattern as
     * buildQrDataUri()'s optional QR package check below. If the package
     * isn't installed yet, or there's no back-side PDF on this
     * certificate's template, the certificate is generated with just the
     * front page (i.e. exactly what happened before this round) rather
     * than failing outright.
     *
     * CHANGED this round — every "give up and return the front page only"
     * path now writes a Log::warning() first. Previously these were
     * silent, so there was no way to tell, from a generated certificate
     * alone, WHY its back side didn't show up — a missing composer
     * package and a missing/unreadable file both looked identical
     * ("nothing happened"). Check storage/logs/laravel.log after
     * generating a certificate whose template has a back-side PDF set;
     * if the back side still isn't appearing, the log line says exactly
     * which of the three guards below stopped it.
     *
     * REQUIRES ONE COMPOSER PACKAGE NOT YET IN THIS APP — see the
     * delivery README for the exact command to run.
     */
    private function mergeBackSide(string $frontPdfBinary, ?string $backSidePdfPath): string
    {
        if (!$backSidePdfPath) {
            return $frontPdfBinary; // this template has no back-side PDF configured — nothing to merge, not an error
        }

        if (!Storage::disk('public')->exists($backSidePdfPath)) {
            \Illuminate\Support\Facades\Log::warning("Certificate back-side merge skipped: back-side PDF not found on the 'public' disk at path \"{$backSidePdfPath}\".");
            return $frontPdfBinary;
        }

        if (!class_exists(\setasign\Fpdi\Fpdi::class)) {
            \Illuminate\Support\Facades\Log::warning('Certificate back-side merge skipped: the setasign/fpdi-fpdf composer package is not installed. Run: composer require setasign/fpdi-fpdf');
            return $frontPdfBinary;
        }

        $frontTmpPath = tempnam(sys_get_temp_dir(), 'cert_front_');
        file_put_contents($frontTmpPath, $frontPdfBinary);

        try {
            $merged = new \setasign\Fpdi\Fpdi();

            // Front page(s) — whatever dompdf just rendered for the
            // certificate itself.
            $frontPageCount = $merged->setSourceFile($frontTmpPath);
            for ($i = 1; $i <= $frontPageCount; $i++) {
                $templateId = $merged->importPage($i);
                $size = $merged->getTemplateSize($templateId);
                $merged->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $merged->useTemplate($templateId);
            }

            // Back side page(s) — the template's uploaded PDF, appended as-is.
            $backSideAbsolutePath = Storage::disk('public')->path($backSidePdfPath);
            $backPageCount = $merged->setSourceFile($backSideAbsolutePath);
            for ($i = 1; $i <= $backPageCount; $i++) {
                $templateId = $merged->importPage($i);
                $size = $merged->getTemplateSize($templateId);
                $merged->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $merged->useTemplate($templateId);
            }

            return $merged->Output('S');
        } catch (\Throwable $e) {
            // A corrupt/encrypted/unreadable back-side PDF (FPDI's free
            // tier can't open password-protected files) shouldn't take
            // the whole certificate down with it — log the real reason
            // and fall back to the front page only.
            \Illuminate\Support\Facades\Log::warning('Certificate back-side merge failed: ' . $e->getMessage());
            return $frontPdfBinary;
        } finally {
            @unlink($frontTmpPath);
        }
    }

    /**
     * Reads a stored image's real dimensions and scales them down (never
     * up) to fit inside a $maxWidth x $maxHeight box in points, preserving
     * aspect ratio — so the certificate PDF can set explicit width/height
     * HTML attributes on the <img> tag rather than relying on CSS
     * max-width/max-height, which dompdf does not reliably honor on
     * inline base64 images (confirmed: your uploaded bottom-image
     * rendered oversized and bled off the page edge with only CSS
     * constraints in place). Returns null if the file is missing or its
     * dimensions can't be read (the view then falls back to plain CSS
     * max-width/max-height as a safety net).
     */
    private function scaledImageDimensions(?string $path, string $disk, int $maxWidth, int $maxHeight): ?array
    {
        if (!$path || !Storage::disk($disk)->exists($path)) {
            return null;
        }

        $size = @getimagesize(Storage::disk($disk)->path($path));

        if (!$size || $size[0] <= 0 || $size[1] <= 0) {
            return null;
        }

        [$originalWidth, $originalHeight] = $size;
        $scale = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);

        return [
            'width'  => (int) round($originalWidth * $scale),
            'height' => (int) round($originalHeight * $scale),
        ];
    }

    /**
     * QR code — OPTIONAL, guarded by class_exists so this doesn't error if
     * the simplesoftwareio/simple-qrcode package isn't installed. Content
     * is just the certificate number (no public verification URL exists in
     * this app that I know of) — if you have (or want) a real verification
     * link/route, tell me and this can encode that instead.
     */
    private function buildQrDataUri(Certificate $certificate): ?string
    {
        if (!class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            return null; // package not installed — the view just omits the QR image
        }

        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(140)->generate($certificate->certificate_number);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Signature as a blue "pen ink" PNG data: URI. Works for drawn and
     * uploaded signatures alike (black, grey or any dark ink on a white or
     * transparent background): the background becomes transparent and the
     * strokes become blue ink, keeping their soft edges. Falls back to the
     * original image if GD isn't available or the file can't be read.
     */
    private function signatureToBlueDataUri(?string $path): ?string
    {
        if (!$path || !Storage::disk('local')->exists($path)) {
            return null;
        }

        if (!function_exists('imagecreatefromstring')) {
            return $this->fileToDataUri($path, 'local');
        }

        $source = @imagecreatefromstring(Storage::disk('local')->get($path));

        if (!$source) {
            return $this->fileToDataUri($path, 'local');
        }

        // Keep it light — a phone photo of a signature can be huge.
        $width = imagesx($source);
        $height = imagesy($source);
        $maxWidth = 900;

        if ($width > $maxWidth) {
            $scaled = imagescale($source, $maxWidth);
            imagedestroy($source);
            $source = $scaled;
            $width = imagesx($source);
            $height = imagesy($source);
        }

        $out = imagecreatetruecolor($width, $height);
        imagealphablending($out, false);
        imagesavealpha($out, true);
        imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));

        [$inkR, $inkG, $inkB] = [14, 47, 150]; // ballpoint blue

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($source, $x, $y);
                $alpha = ($rgba >> 24) & 0x7F;          // 0 = opaque, 127 = transparent
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                $opacity = 1 - ($alpha / 127);
                $lightness = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
                $ink = (1 - $lightness) * $opacity;     // how much "ink" this pixel holds

                if ($ink < 0.12) {
                    continue;                           // background / paper noise
                }

                $ink = min(1, ($ink - 0.12) / 0.6);     // stretch so strokes look solid
                $outAlpha = (int) round(127 * (1 - $ink));
                imagesetpixel($out, $x, $y, imagecolorallocatealpha($out, $inkR, $inkG, $inkB, $outAlpha));
            }
        }

        imagedestroy($source);

        ob_start();
        imagepng($out);
        $png = ob_get_clean();
        imagedestroy($out);

        return 'data:image/png;base64,' . base64_encode($png);
    }

    /**
     * Reads a stored file (signature or template bottom-image) and returns
     * it as a data: URI, so dompdf embeds the actual bytes at render time
     * instead of needing filesystem/remote access to resolve a path or URL
     * (dompdf's remote-file support is inconsistent depending on server
     * config — inlining avoids that entirely). Returns null quietly if the
     * path is empty or the file no longer exists.
     */
    private function fileToDataUri(?string $path, string $disk): ?string
    {
        if (!$path || !Storage::disk($disk)->exists($path)) {
            return null;
        }

        $mime = Storage::disk($disk)->mimeType($path) ?: 'image/png';
        $contents = Storage::disk($disk)->get($path);

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }

    /**
     * Reads a .ttf font file bundled with the app (NOT user-uploaded, so
     * this reads from resources/fonts rather than a Storage disk) and
     * returns it as a data: URI for an @font-face rule in pdf.blade.php.
     * Returns null quietly if the file hasn't been placed there yet —
     * pdf.blade.php falls back to its normal font stack in that case, so
     * this is safe to ship before the .ttf files are actually deployed.
     *
     * Put the two files I sent at:
     *   resources/fonts/certificate-name-font.ttf
     *   resources/fonts/certificate-grade-font.ttf
     */
    private function fontToDataUri(string $filename): ?string
    {
        $path = resource_path('fonts/' . $filename);

        if (!is_file($path)) {
            return null;
        }

        return 'data:font/ttf;base64,' . base64_encode(file_get_contents($path));
    }
}
