<?php

namespace App\Http\Controllers;

use App\Models\AssessmentTemplate;
use App\Models\AssignSubjects;
use App\Models\Branch;
use App\Models\Course;
use App\Models\FeeInvoices;
use App\Models\Student;
use App\Models\StudentAssessment;
use App\Models\StudentAssessmentMark;
use App\Models\StudentCourse;
use App\Models\Subject;
use App\Models\SubjectAttendanceSession;
use App\Models\TrainingVehicle;
use App\Models\User;
use App\Models\UserBranch;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StudentAssessmentController extends Controller
{
    // Grade bands, all percentage-of-that-template's-own-Max-Marks (NOT a
    // raw score out of 100 — templates don't all add up to 100) —
    // "90-100 = A, 80-89 = B+, 70-79 = B, 60-69 = C", anything under 60% is
    // a FAIL. Kept as one ordered list so store() and the percentageToGrade()
    // helper below can never drift apart.
    private const GRADE_BANDS = [
        ['min' => 90, 'grade' => 'A'],
        ['min' => 80, 'grade' => 'B+'],
        ['min' => 70, 'grade' => 'B'],
        ['min' => 60, 'grade' => 'C'],
    ];

    public function index(Request $request)
    {
        $query = StudentAssessment::with([
            'student.verifications',
            'template.course',
            'evaluator:id,name',
            'evaluator.branches:id,name',
            'certificate',
            'studentCourse',
            'studentCourse.intake',
            'studentCourse.branch',
        ])->latest();

        if ($request->filled('course_id')) {
            $courseId = $request->course_id;
            $query->whereHas('template', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        if ($request->filled('result')) {
            $query->where('is_qualified', $request->result === 'PASS');
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%")
                    ->orWhere('national_id_number', 'like', "%{$search}%");
            });
        }
        if ($request->filled('date_range')) {
            $dateRangeParts = array_map('trim', explode(' - ', $request->date_range));

            if (count($dateRangeParts) === 2 && $dateRangeParts[0] !== '' && $dateRangeParts[1] !== '') {
                try {
                    $from = Carbon::parse($dateRangeParts[0])->startOfDay();
                    $to = Carbon::parse($dateRangeParts[1])->endOfDay();
                    $query->whereBetween('assessed_at', [$from, $to]);
                } catch (\Exception $e) {
                    // ignore silently — malformed range shouldn't break the page
                }
            } elseif (count($dateRangeParts) === 1 && $dateRangeParts[0] !== '') {
                // Defensive: a single date with no " - " separator at all.
                try {
                    $day = Carbon::parse($dateRangeParts[0]);
                    $query->whereBetween('assessed_at', [$day->copy()->startOfDay(), $day->copy()->endOfDay()]);
                } catch (\Exception $e) {
                    // ignore silently
                }
            }
        }

        if ($request->filled('start_date_range')) {
            $startDateRangeParts = array_map('trim', explode(' - ', $request->start_date_range));

            if (count($startDateRangeParts) === 2 && $startDateRangeParts[0] !== '' && $startDateRangeParts[1] !== '') {
                try {
                    $startFrom = Carbon::parse($startDateRangeParts[0])->startOfDay();
                    $startTo = Carbon::parse($startDateRangeParts[1])->endOfDay();

                    $query->whereHas('studentCourse.intake', function ($q) use ($startFrom, $startTo) {
                        $q->whereBetween('start_date', [$startFrom, $startTo]);
                    });
                } catch (\Exception $e) {
                    // ignore silently — malformed range shouldn't break the page
                }
            } elseif (count($startDateRangeParts) === 1 && $startDateRangeParts[0] !== '') {
                try {
                    $startDay = Carbon::parse($startDateRangeParts[0]);

                    $query->whereHas('studentCourse.intake', function ($q) use ($startDay) {
                        $q->whereBetween('start_date', [$startDay->copy()->startOfDay(), $startDay->copy()->endOfDay()]);
                    });
                } catch (\Exception $e) {
                    // ignore silently
                }
            }
        }

        if ($request->filled('branch_id')) {
            $branchId = $request->branch_id;
            $query->whereHas('evaluator.branches', function ($q) use ($branchId) {
                $q->where('branches.id', $branchId);
            });
        }

        $recent = $query->get();
        
        $recent->each(function ($a) {
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

        $categoriesByPair = $this->resolveCourseCategoriesForRecent($recent);

        $courseIds = AssessmentTemplate::pluck('course_id')->unique();
        $courseOptions = Course::whereIn('id', $courseIds)->orderBy('name')->get(['id', 'name']);

        $creatorIds = StudentAssessment::whereNotNull('created_by')->distinct()->pluck('created_by');
        $creatorOptions = User::whereIn('id', $creatorIds)->orderBy('name')->get(['id', 'name']);

        // NEW — branch dropdown options for the branch filter above.
        $branchOptions = Branch::orderBy('name')->get(['id', 'name']);

        return view('pages.administration.assessment.mark-entry', compact(
            'recent',
            'courseOptions',
            'creatorOptions',
            'categoriesByPair',
            'branchOptions'
        ));
    }

    /**
     * Batched "Student Category (from the course-related invoice)" lookup
     * for the whole $recent list in ONE query, keyed by "studentId-courseId"
     * so the view can do `$categoriesByPair[$a->student_id.'-'.$courseId]`.
     * Mirrors the exact join/COALESCE InvoiceController::invoiceDatatable()
     * already uses against fee_invoices/paid_courses/student_categories.
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

    /** AJAX: resolve a student by Reg No, plus the courses they've STARTED (final assessment applies once a course is underway). */
    public function findStudent(Request $request)
    {
        $request->validate(['admission_number' => 'required|string']);

        $student = Student::with('branch:id,name')->where('admission_number', trim($request->admission_number))->first();

        if (!$student) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'No student found for that Reg No.',
            ], 404);
        }

        $courses = StudentCourse::with('course:id,name')
            ->where('student_id', $student->id)
            ->where('status', 'start')
            ->get()
            ->filter(fn($sc) => $sc->course)
            ->map(fn($sc) => [
                'student_course_id' => $sc->id,
                'course_id' => $sc->course_id,
                'course_name' => $sc->course->name,
            ])
            ->values();



        if ($courses->isEmpty()) {
            return response()->json([
                'status' => 'no_started_course',
                'message' => "{$student->student_name} has no course currently in 'start' status.",
            ], 422);
        }

        $courseIds = $courses->pluck('course_id')->all();

        $paidCourseIds = FeeInvoices::where('student_id', $student->id)
            ->where('payment_status', '!=', 'returned')
            ->where('invoice_type', 'course')
            ->with(['paidCourses' => function ($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            }])
            ->get()
            ->pluck('paidCourses')
            ->flatten()
            ->pluck('course_id')
            ->unique();

        $coursesWithPayment = $courses->filter(fn($c) => $paidCourseIds->contains($c['course_id']))->values();

        if ($coursesWithPayment->isEmpty()) {
            return response()->json([
                'status' => 'no_paid_course',
                'message' => "{$student->student_name} has started course(s), but none of them have a payment on record yet.",
            ], 422);
        }

        $courses = $coursesWithPayment;


        $vehicles = TrainingVehicle::active()->orderBy('name')->get(['id', 'name', 'vehicle_number']);

        return response()->json([
            'status' => 'ok',
            'student' => [
                'id' => $student->id,
                'name' => $student->student_name,
                'admission_number' => $student->admission_number,
                'nic' => $student->national_id_number,
                'branch_id' => $student->branch_id,
                'branch_name' => $student->branch->name ?? null,
            ],
            'courses' => $courses,
            'vehicles' => $vehicles,
        ]);
    }

    /** AJAX: the active assessment template for one course, with its title/sub-title tree. */
    public function templateForCourse($courseId)
    {
        $template = AssessmentTemplate::with(['titles.subTitles'])
            ->where('course_id', $courseId)
            ->active()
            ->latest()
            ->first();

        if (!$template) {
            return response()->json([
                'status' => 'no_template',
                'message' => 'No active assessment template exists for this course yet — create one first.',
            ], 422);
        }

        return response()->json(['status' => 'ok', 'template' => $template]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'student_course_id' => 'required|exists:student_courses,id',
            'assessment_template_id' => 'required|exists:assessment_templates,id',
            'vehicle_id' => 'nullable|exists:training_vehicles,id',
            'equipment_model' => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'assessed_at' => 'nullable|date',
            'comments' => 'nullable|string',
            'signature_data' => 'nullable|string',
            'marks' => 'required|array|min:1',
            'marks.*.sub_title_id' => 'required|exists:assessment_template_sub_titles,id',
            'marks.*.title_id' => 'required|exists:assessment_template_titles,id',
            'marks.*.competency' => 'nullable|in:C,NC',
            'marks.*.marks' => 'nullable|numeric|min:0',
            'marks.*.remarks' => 'nullable|string|max:500',
        ]);

        $alreadyExists = StudentAssessment::where('student_course_id', $validated['student_course_id'])->exists();

        if ($alreadyExists) {
            return response()->json([
                'status' => 'duplicate',
                'message' => 'This course already has a saved assessment for this student. Undo it first (within 2 days, by the same user who created it) if you need to redo it.',
            ], 422);
        }

        $template = AssessmentTemplate::with('titles')->findOrFail($validated['assessment_template_id']);

        // "mark subtitle total can't be go more title mark. sub title marks
        // total leas than or equal to title total marks" — enforced here
        // too (not just client-side in the popup), by summing the
        // submitted marks per title_id and comparing against that title's
        // own max_marks from the template itself (never trusting a
        // client-sent max).
        $marksByTitle = collect($validated['marks'])->groupBy('title_id');
        $overLimitTitles = [];

        foreach ($template->titles as $title) {
            $submittedForTitle = $marksByTitle->get($title->id, collect());
            $titleTotal = $submittedForTitle->sum(fn($m) => (float) ($m['marks'] ?? 0));

            if ($titleTotal > $title->max_marks) {
                $overLimitTitles[] = "{$title->title} ({$titleTotal} > {$title->max_marks})";
            }
        }

        if (!empty($overLimitTitles)) {
            return response()->json([
                'status' => 'over_limit',
                'message' => 'Sub Title marks total can\'t be more than the Title\'s Max Marks: ' . implode(', ', $overLimitTitles),
            ], 422);
        }

        $maxMarks = $template->titles->sum('max_marks');
        $totalMarks = collect($validated['marks'])->sum(fn($m) => (float) ($m['marks'] ?? 0));

        // Grade/pass-fail are both PERCENTAGE-based against this template's
        // OWN max marks (templates don't all add up to 100), not the raw
        // total_marks number — see percentageToGrade() below.
        $percentage = $maxMarks > 0 ? ($totalMarks / $maxMarks) * 100 : 0;
        $grade = $this->percentageToGrade($percentage);
        $isQualified = $grade !== 'FAIL';

        $signaturePath = !empty($validated['signature_data'])
            ? $this->storeSignature($validated['signature_data'])
            : null;

        $assessment = DB::transaction(function () use ($validated, $template, $maxMarks, $totalMarks, $grade, $isQualified, $signaturePath) {
            $assessment = StudentAssessment::create([
                'student_id' => $validated['student_id'],
                'student_course_id' => $validated['student_course_id'],
                'assessment_template_id' => $template->id,
                'vehicle_id' => $validated['vehicle_id'] ?? null,
                'equipment_model' => $validated['equipment_model'] ?? null,
                'branch_id' => $validated['branch_id'] ?? null,
                'assessed_at' => $validated['assessed_at'] ?? now(),
                'total_marks' => $totalMarks,
                'max_marks' => $maxMarks,
                'grade' => $grade,
                'is_qualified' => $isQualified,
                'comments' => $validated['comments'] ?? null,
                'evaluator_id' => Auth::id(),
                'operator_signature' => $signaturePath,
                'status' => 'submitted',
                'created_by' => Auth::id(),
            ]);

            foreach ($validated['marks'] as $mark) {
                StudentAssessmentMark::create([
                    'student_assessment_id' => $assessment->id,
                    'assessment_template_title_id' => $mark['title_id'],
                    'assessment_template_sub_title_id' => $mark['sub_title_id'],
                    'competency' => $mark['competency'] ?? null,
                    'marks' => $mark['marks'] ?? null,
                    'remarks' => $mark['remarks'] ?? null,
                ]);
            }

            return $assessment;
        });

        return response()->json([
            'status' => 'ok',
            'message' => 'Assessment saved successfully.',
            'assessment_id' => $assessment->id,
            'total_marks' => $totalMarks,
            'max_marks' => $maxMarks,
            'grade' => $assessment->grade,
            'pdf_url' => route('student-assessment.pdf', $assessment->id),
        ]);
    }

    /**
     * Percentage (0-100) -> letter grade, per self::GRADE_BANDS ("90-100 = A,
     * 80-89 = B+, 70-79 = B, 60-69 = C"), else 'FAIL'. Shared by store()
     * here; the marks-entry popup mirrors this exact banding client-side
     * for the live preview, so the two can never disagree.
     */
    private function percentageToGrade(float $percentage): string
    {
        foreach (self::GRADE_BANDS as $band) {
            if ($percentage >= $band['min']) {
                return $band['grade'];
            }
        }

        return 'FAIL';
    }

    public function show($id)
    {
        $assessment = $this->loadAssessment($id);

        return view('pages.administration.assessment.mark-entry', [
            'viewOnly' => $assessment,
            'recent' => collect(),
            'categoriesByPair' => collect(),
        ]);
    }

    public function pdf($id)
    {
        $assessment = $this->loadAssessment($id);

        $pdf = Pdf::loadView('pages.administration.assessment.assessment-pdf', ['assessment' => $assessment])
            ->setPaper('a4', 'portrait');
        $fileName = 'Final_Assessment_' . $assessment->student->admission_number . '_' . $assessment->id . '.pdf';

        return $pdf->download($fileName);
    }

    private function loadAssessment($id): StudentAssessment
    {
        return StudentAssessment::with([
            'student',
            'studentCourse.intake',
            'template.course',
            'template.titles.subTitles',
            'vehicle',
            'branch',
            'evaluator:id,name',
            'marks',
        ])->findOrFail($id);
    }

    private function storeSignature(string $signatureData): string
    {
        $folder = storage_path('app/public/signatures/assessments/');

        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0777, true);
        }

        $image = str_replace('data:image/png;base64,', '', $signatureData);
        $image = str_replace(' ', '+', $image);

        $imageName = 'assessment_signature_' . time() . '_' . uniqid() . '.png';
        File::put($folder . $imageName, base64_decode($image));

        return 'signatures/assessments/' . $imageName;
    }


    public function existingForCourse($studentCourseId)
    {
        $assessment = StudentAssessment::with(['template.titles.subTitles', 'marks', 'evaluator:id,name', 'certificate'])
            ->where('student_course_id', $studentCourseId)
            ->latest()
            ->first();

        if (!$assessment) {
            return response()->json(['status' => 'none']);
        }

        $canUndo = $assessment->created_by === Auth::id()
            && !$assessment->certificate;

        return response()->json([
            'status' => 'ok',
            'assessment' => $assessment,
            'can_undo' => $canUndo,
        ]);
    }

    public function destroy($id)
    {
        $assessment = StudentAssessment::with('certificate')->findOrFail($id);

        if ((int) $assessment->created_by !== (int) Auth::id()) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'Only the staff member who created this assessment can undo it.',
            ], 403);
        }

        if ($assessment->certificate) {
            return response()->json([
                'status' => 'certificate_exists',
                'message' => 'This assessment already has a certificate generated for it and can no longer be undone. Contact an administrator if this needs to be corrected.',
            ], 403);
        }

        $assessment->delete();

        return response()->json([
            'status' => 'ok',
            'message' => 'Assessment undone. You can now enter a fresh one for this course.',
        ]);
    }
    public function marksPopup($studentCourseId)
    {
        $studentCourse = StudentCourse::with(['student.branch:id,name', 'student.verifications', 'course:id,name'])
            ->findOrFail($studentCourseId);

        $user = Auth::user();
        $branchId = UserBranch::where('user_id', $user->id)->first()?->branch_id;
        $vehicles = TrainingVehicle::where('branch_id', $branchId)->active()->orderBy('name')->get(['id', 'name', 'vehicle_number']);

        $invoices = FeeInvoices::where('student_id', $studentCourse->student_id)
            ->where('payment_status', '!=', 'returned')
            ->whereHas('paidCourses', fn($q) => $q->where('course_id', $studentCourse->course_id))
            ->get(['id', 'total_amount', 'paid_amount', 'payment_status', 'invoice_type']);

        $paymentIncomplete = $invoices->contains(fn($inv) => $inv->payment_status !== 'full');

        $practicalProgress = $this->countSubjectCompletion(
            $studentCourse->student_id,
            $studentCourse->course_id,
            $studentCourse->intake_id,
            'P'
        );
        $theoryProgress = $this->countSubjectCompletion(
            $studentCourse->student_id,
            $studentCourse->course_id,
            $studentCourse->intake_id,
            'T'
        );

        return view('pages.administration.assessment.marks-popup', [
            'studentCourse' => $studentCourse,
            'vehicles' => $vehicles,
            'invoices' => $invoices,
            'paymentIncomplete' => $paymentIncomplete,
            'practicalProgress' => $practicalProgress,
            'theoryProgress' => $theoryProgress,
        ]);
    }

    /**
     * How many of the subjects of $subjectType ('P' or 'T') assigned to
     * this course/intake has the student COMPLETED, out of how many are
     * assigned in total — e.g. ['completed' => 4, 'total' => 10].
     * "Completed" uses the exact same rule
     * StudentPracticalCardController::buildSubjectRows() already applies
     * for the attendance card's per-subject badges: the student's total
     * attended minutes for that subject (every whereNull('checkpoint_type')
     * session, real subject attendance — not a Before/After/Course
     * Attendance checkpoint) meet or exceed that subject's planned
     * practical_time. A subject with no planned time set can never count
     * as "completed" (nothing to compare against) but still counts toward
     * the total — same as an unattended subject.
     *
     * Deliberately duplicated here rather than calling into
     * StudentPracticalCardController (the two controllers don't share a
     * base/trait in this app) — sessionDurationMinutes()/
     * parsePlannedMinutes() below are byte-for-byte copies of that
     * controller's own private helpers, kept identical on purpose so the
     * duration math can't drift between the two features.
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
     * StudentPracticalCardController::sessionDurationMinutes() — both times
     * re-anchored to the same base date before diffing, and a session that
     * crosses midnight gets $end pushed a day forward instead of producing
     * a huge wrong number. Copied rather than shared (see the doc-comment
     * on countSubjectCompletion() above).
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
     * Same parser as StudentPracticalCardController::parsePlannedMinutes()
     * — a subject's raw practical_time (either a "H:i" string or a plain
     * number of minutes) into whole minutes. Unrecognized values are
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
}