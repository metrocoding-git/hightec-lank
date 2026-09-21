<?php
namespace App\Http\Controllers;

use App\Models\AssessmentTemplate;
use App\Models\AssignSubjects;
use App\Models\Certificate;
use App\Models\CertificateSerialPrefix;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\StudentAssessment;
use App\Models\Subject;
use App\Models\SubjectAttendanceSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class CertificateController extends Controller
{
    /**
     * The certificate management page — lists every PASSed assessment
     * (whether a certificate has been generated yet or not) with filters
     * for course, certificate stage, and downloaded status.
     */
    public function index(Request $request)
    {
        // WIDENED this round — "list want add all assment list table
        // details all": these extra eager loads (student.verifications,
        // evaluator.branches, studentCourse.intake, studentCourse.branch)
        // are what the new Theory/Practical Subjects, Student Category,
        // verification badges, Start Date, Intake, and branch columns
        // below need — the exact same relations
        // StudentAssessmentController::index() already loads for the
        // Final Assessment Marks list.
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

        $assessments = $query->get();

        // NEW — "list want add all assment list table details all": the
        // same Theory/Practical Subjects progress figures the Final
        // Assessment Marks list already computes per row, via
        // countSubjectCompletion() below (deliberately duplicated from
        // StudentAssessmentController — see that method's doc-comment).
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

        // NEW — same "Student Category" batched lookup the Final
        // Assessment Marks list uses for its own column.
        $categoriesByPair = $this->resolveCourseCategoriesForRecent($assessments);

        $courseIds = AssessmentTemplate::pluck('course_id')->unique();
        $courseOptions = Course::whereIn('id', $courseIds)->orderBy('name')->get(['id', 'name']);

        // REMOVED — the Generate modal no longer asks the user to pick a
        // certificate template; it's auto-selected per course (see
        // generate() below), so $templateOptions is no longer needed
        // here or in the view.

        return view('pages.administration.certificate.index', compact('assessments', 'courseOptions', 'categoriesByPair'));
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
            $certificate = Certificate::find($certId);

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
                    $certificate->delete();
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
        $filename = Str::slug($studentName) . '-' . $certificate->certificate_number . '.pdf';

        return Storage::disk('local')->download($certificate->pdf_path, $filename);
    }

    /**
     * Bulk-download many approved certificates as a single ZIP. Each
     * included certificate is also marked downloaded.
     * Body: { certificate_ids: [1,2,3] }
     */
    public function bulkDownload(Request $request)
    {
        $data = $request->validate([
            'certificate_ids'   => 'required|array|min:1',
            'certificate_ids.*' => 'integer|distinct',
        ]);

        $certificates = Certificate::with('studentAssessment.student')
            ->whereIn('id', $data['certificate_ids'])
            ->get();

        $downloadable = $certificates->filter(function ($c) {
            return $c->isApproved() && $c->pdf_path && Storage::disk('local')->exists($c->pdf_path);
        });

        if ($downloadable->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'None of the selected certificates are fully approved/downloadable yet.'], 422);
        }

        $zipName = 'certificates-' . now()->format('Ymd-His') . '.zip';
        $zipPath = storage_path('app/tmp/' . $zipName);

        if (!is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $usedNames = [];
        foreach ($downloadable as $certificate) {
            $studentName = optional($certificate->studentAssessment->student)->student_name ?? 'certificate';
            $entryName = Str::slug($studentName) . '-' . $certificate->certificate_number . '.pdf';

            $suffix = 1;
            $finalName = $entryName;
            while (in_array($finalName, $usedNames, true)) {
                $finalName = Str::slug($studentName) . '-' . $certificate->certificate_number . '-' . $suffix . '.pdf';
                $suffix++;
            }
            $usedNames[] = $finalName;

            $zip->addFile(Storage::disk('local')->path($certificate->pdf_path), $finalName);
            $this->recordDownload($certificate);
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
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

        // CHANGED this round — "From:" now uses the course's real,
        // scheduled start date (studentCourse.intake.start_date, the same
        // value used to validate at generate() time and already shown as
        // "Start Date" on the Final Assessment Marks list), instead of
        // the enrollment row's created_at timestamp (which was never the
        // actual course start date, just whenever that DB row happened to
        // be inserted).
        $startDate = optional(optional($studentCourse)->intake)->start_date
            ? Carbon::parse($studentCourse->intake->start_date)
            : null;

        // CHANGED this round — "to" now uses the certificate's own
        // course_end_date (a real field entered at generate time — see
        // the "add course end date" request), replacing the previous
        // fallback to issue_date/generated_at.
        $endDate = $certificate->course_end_date ? Carbon::parse($certificate->course_end_date) : null;

        $durationWords = optional($certificate->certificateTemplate)->duration;

        $bottomImagePath = optional($certificate->certificateTemplate)->bottom_image_path;
        $bottomImageDims = $this->scaledImageDimensions($bottomImagePath, 'public', 123, 92);

        // NEW — "bottom left side qr to want add qr image": the
        // Certificate Templates screen has had a "QR Code Image" upload
        // (qr_code_image_path) for a couple of rounds now, but nothing
        // ever actually rendered it onto the certificate — the PDF was
        // still only using buildQrDataUri()'s AUTO-GENERATED barcode
        // (which also needs its own separate, optional composer package,
        // and just encodes the certificate number as a QR pattern; it's
        // not an uploaded image at all). Now the template's own uploaded
        // QR image takes priority, sized/scaled the exact same safe way
        // the bottom image already is (see scaledImageDimensions()'s
        // doc-comment for why explicit width/height beats CSS
        // max-width/max-height in dompdf). The auto-generated QR is kept
        // as a fallback ONLY for a template with no uploaded QR image at
        // all, so nothing regresses for a template that was relying on
        // it before.
        $qrImagePath = optional($certificate->certificateTemplate)->qr_code_image_path;
        $qrImageDims = $this->scaledImageDimensions($qrImagePath, 'public', 55, 55);

        // NEW — "certificate want add footer details... check intake
        // start branch withing branch change only address" — the new
        // institute footer band (name / address / website / TVEC Reg No.
        // / PV No.) is the same on every certificate EXCEPT the address
        // line, which depends on which branch the student's course ran
        // at (studentCourse->branch — the same relation already shown as
        // "Intake Branch" in the assessment/certificate lists).
        $branchName = optional(optional($studentCourse)->branch)->name;
        $footerAddress = $this->branchFooterAddress($branchName);

        // Shrink-to-fit for long names — see the class-doc comment above.
        // 41 characters is roughly the longest that fits on one line at
        // the full 20pt size inside the name's 412pt-wide blank line,
        // measured against the real embedded name font; anything longer
        // scales down proportionally instead of wrapping onto the NIC
        // line below it. Floors at 13pt so an extreme outlier still stays
        // legible rather than shrinking to nothing.
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
            'bottomImageDataUri'        => $bottomImagePath
                ? $this->fileToDataUri($bottomImagePath, 'public')
                : null,
            'bottomImageWidth'          => $bottomImageDims['width'] ?? null,
            'bottomImageHeight'         => $bottomImageDims['height'] ?? null,
            'ownerSignatureDataUri'     => $this->fileToDataUri($certificate->owner_signature_path, 'local'),
            'conductorSignatureDataUri' => $this->fileToDataUri($certificate->conductor_signature_path, 'local'),
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

    /**
     * "certificate want add footer details... check intake start branch
     * withing branch change only address. there 2 branches kurunegala
     * and kaduwela" — the institute name, website, TVEC Reg No. and PV
     * No. printed in the new footer band are the same on every
     * certificate; only this address line changes, based on which
     * branch the student's course ran at.
     *
     * *** NEEDS YOUR CONFIRMATION BEFORE THIS PRINTS ON A REAL
     * CERTIFICATE *** — see the delivery README. The Kurunegala address
     * below is my best reading of the reference photo you sent ("No.
     * 549, Kandy Road, Mawathagama, Sri Lanka.") — Mawathagama is a town
     * in the Kurunegala district, so I assumed that's the Kurunegala
     * branch's own address, but please double check the exact wording.
     * The Kaduwela address is a PLACEHOLDER — I don't have your real
     * Kaduwela branch address, so I can't invent one for an official
     * document. Replace the placeholder string below (or tell me the
     * real address and I'll do it).
     */
    private function branchFooterAddress(?string $branchName): string
    {
        $branchName = $branchName ? trim($branchName) : '';

        return match (true) {
            stripos($branchName, 'kurunegala') !== false => 'No. 549, Kandy Road, Mawathagama, Sri Lanka.',
            stripos($branchName, 'kaduwela') !== false => '404/22 Vaikkiya Waththa Rd, Kaduwela 10640',
            // Fallback for a branch that's neither of the two named ones
            // (or no branch on record at all) — same as Kurunegala for
            // now, since that's the only confirmed address I have.
            default => 'No. 549, Kandy Road, Mawathagama, Sri Lanka.',
        };
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