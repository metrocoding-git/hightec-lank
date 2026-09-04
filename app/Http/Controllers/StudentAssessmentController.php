<?php

namespace App\Http\Controllers;

use App\Models\AssessmentTemplate;
use App\Models\Course;
use App\Models\FeeInvoices;
use App\Models\Student;
use App\Models\StudentAssessment;
use App\Models\StudentAssessmentMark;
use App\Models\StudentCourse;
use App\Models\TrainingVehicle;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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
            'certificate',
            'studentCourse',
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

        $recent = $query->get();

        // "Student Category (from the course-related invoice)" — NOT
        // students.student_category_id directly; the fee_invoices row tied
        // to this student+course can override the category, falling back
        // to the student's own when it doesn't. Same COALESCE
        // InvoiceController::invoiceDatatable() already uses. Batched into
        // one query for the whole $recent list rather than N+1 per row.
        $categoriesByPair = $this->resolveCourseCategoriesForRecent($recent);

        $courseIds = AssessmentTemplate::pluck('course_id')->unique();
        $courseOptions = Course::whereIn('id', $courseIds)->orderBy('name')->get(['id', 'name']);

        $creatorIds = StudentAssessment::whereNotNull('created_by')->distinct()->pluck('created_by');
        $creatorOptions = User::whereIn('id', $creatorIds)->orderBy('name')->get(['id', 'name']);

        return view('pages.administration.assessment.mark-entry', compact('recent', 'courseOptions', 'creatorOptions', 'categoriesByPair'));
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
        $assessment = StudentAssessment::with(['template.titles.subTitles', 'marks', 'evaluator:id,name'])
            ->where('student_course_id', $studentCourseId)
            ->latest()
            ->first();

        if (!$assessment) {
            return response()->json(['status' => 'none']);
        }

        $canUndo = $assessment->created_by === Auth::id()
            && $assessment->created_at->gt(now()->subDays(2));

        return response()->json([
            'status' => 'ok',
            'assessment' => $assessment,
            'can_undo' => $canUndo,
        ]);
    }

    public function destroy($id)
    {
        $assessment = StudentAssessment::findOrFail($id);

        if ((int) $assessment->created_by !== (int) Auth::id()) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'Only the staff member who created this assessment can undo it.',
            ], 403);
        }

        if ($assessment->created_at->lt(now()->subDays(2))) {
            return response()->json([
                'status' => 'expired',
                'message' => 'This assessment can no longer be undone — the 2-day window has passed.',
            ], 403);
        }

        $assessment->delete(); // soft delete (deleted_at) — see SQL/model changes below

        return response()->json([
            'status' => 'ok',
            'message' => 'Assessment undone. You can now enter a fresh one for this course.',
        ]);
    }

    public function marksPopup($studentCourseId)
    {
        $studentCourse = StudentCourse::with(['student.branch:id,name', 'student.verifications', 'course:id,name'])
            ->findOrFail($studentCourseId);

        $vehicles = TrainingVehicle::active()->orderBy('name')->get(['id', 'name', 'vehicle_number']);

        // "before student assesment mark. want display student related all
        // payments if not complete want notify" — every non-returned,
        // course-related invoice for this student+course (via paid_courses,
        // same relationship InvoiceController already uses), so the popup
        // can show a "payment not complete" notice. This is informational
        // only — it does NOT block entering marks.
        $invoices = FeeInvoices::where('student_id', $studentCourse->student_id)
            ->where('payment_status', '!=', 'returned')
            ->whereHas('paidCourses', fn($q) => $q->where('course_id', $studentCourse->course_id))
            ->get(['id', 'total_amount', 'paid_amount', 'payment_status', 'invoice_type']);

        $paymentIncomplete = $invoices->contains(fn($inv) => $inv->payment_status !== 'full');

        return view('pages.administration.assessment.marks-popup', [
            'studentCourse' => $studentCourse,
            'vehicles' => $vehicles,
            'invoices' => $invoices,
            'paymentIncomplete' => $paymentIncomplete,
        ]);
    }
}