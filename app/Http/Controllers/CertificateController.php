<?php
/**
 * ============================================================================
 * REPLACES the CertificateController.php delivered last round — place at:
 * app/Http/Controllers/CertificateController.php
 *
 * Changes from last round:
 *   - generate() no longer accepts/stores a duration_text.
 *   - approve() is GONE, replaced by two separate steps matching the new
 *     two-stage signing flow: signConductor() then signOwner(). Owner
 *     signing is what finally sets status='approved' and unlocks download.
 *   - download()/bulkDownload() now record who/when a certificate was
 *     downloaded and bump a counter, so the new certificate management
 *     page (certificate-index.blade.php) can filter "not downloaded yet"
 *     vs "already downloaded".
 *   - New index() — the "new window" for certificate generation/management,
 *     with the filters requested: by course, by stage (not generated /
 *     pending Conductor sign / pending Owner sign / approved), and by
 *     downloaded yes/no, plus the existing name/reg-no/NIC search.
 * ============================================================================
 */

namespace App\Http\Controllers;

use App\Models\AssessmentTemplate;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\StudentAssessment;
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
        $query = StudentAssessment::with(['student', 'template.course', 'evaluator:id,name', 'certificate'])
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

        $courseIds = AssessmentTemplate::pluck('course_id')->unique();
        $courseOptions = Course::whereIn('id', $courseIds)->orderBy('name')->get(['id', 'name']);

        return view('pages.administration.certificate.index', compact('assessments', 'courseOptions'));
    }

    /**
     * Generate certificate(s) for one or many PASSed student_assessments.
     * Body: { student_assessment_ids: [1,2,3] }
     */
    public function generate(Request $request)
    {
        $data = $request->validate([
            'student_assessment_ids'   => 'required|array|min:1',
            'student_assessment_ids.*' => 'integer|distinct',
        ]);

        $created = [];
        $skipped = [];

        foreach ($data['student_assessment_ids'] as $assessmentId) {
            $assessment = StudentAssessment::with('certificate')->find($assessmentId);

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

            $certificate = DB::transaction(function () use ($assessment) {
                $certificate = Certificate::create([
                    'student_assessment_id' => $assessment->id,
                    'certificate_number'    => $this->nextCertificateNumber(),
                    'status'                => 'generated',
                    'generated_by'          => Auth::id(),
                    'generated_at'          => now(),
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
     * STEP 1 of signing — Assessment Conductor signs first. Only certificates
     * still in 'generated' status are eligible. Moves status to
     * 'conductor_signed' (still NOT downloadable — Owner still has to sign).
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
            return response()->json(['success' => false, 'message' => 'Assessment Conductor signature is required (draw or upload).'], 422);
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
                $skipped[] = ['id' => $certId, 'reason' => 'Not awaiting the Conductor signature (already signed, or not generated)'];
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
     * STEP 2 of signing — Owner signs second, after the Conductor. Only
     * certificates in 'conductor_signed' status are eligible. This is what
     * finally moves status to 'approved' and unlocks download.
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
            return response()->json(['success' => false, 'message' => 'Owner signature is required (draw or upload).'], 422);
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
                $skipped[] = ['id' => $certId, 'reason' => 'Not awaiting the Owner signature (Conductor must sign first, or it is already approved)'];
                continue;
            }

            $certificate->owner_signature_path = $path;
            $certificate->owner_signed_by = Auth::id();
            $certificate->owner_signed_at = now();
            $certificate->status = 'approved';
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
     * Download a single approved certificate PDF. Records who/when it was
     * downloaded and bumps download_count, so the management page's
     * "already downloaded" filter has something to key off.
     */
    public function download($id)
    {
        $certificate = Certificate::with('studentAssessment.student')->findOrFail($id);

        if (!$certificate->isApproved() || !$certificate->pdf_path || !Storage::disk('local')->exists($certificate->pdf_path)) {
            abort(403, 'Certificate is not fully approved yet (both Conductor and Owner must sign first).');
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
     * HTL-2026-00001 style numbering — my own default, adjust if you have
     * an existing convention.
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

    /**
     * Render (or re-render) the certificate PDF and return its stored path.
     * A4 landscape — placeholder layout, see certificate-pdf.blade.php header
     * comment for what's still pending your clean CorelDRAW export.
     */
    private function renderCertificatePdf(Certificate $certificate): string
    {
        $certificate->loadMissing(['studentAssessment.student', 'studentAssessment.template.course', 'studentAssessment.evaluator']);

        $pdf = Pdf::loadView('pages.administration.certificate.pdf', [
            'certificate' => $certificate,
        ])->setPaper('a4', 'landscape');

        $path = 'certificates/pdf/' . $certificate->certificate_number . '.pdf';
        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }
}