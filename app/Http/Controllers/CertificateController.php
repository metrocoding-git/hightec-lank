<?php
namespace App\Http\Controllers;

use App\Models\AssessmentTemplate;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\StudentAssessment;
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

        // NEW — for the Generate modal's template dropdown (item 5 of the
        // certificate expansion request).
        $templateOptions = CertificateTemplate::active()->orderBy('name')->get(['id', 'name']);

        return view('pages.administration.certificate.index', compact('assessments', 'courseOptions', 'templateOptions'));
    }

    /**
     * Generate certificate(s) for one or many PASSed student_assessments.
     * Body: { student_assessment_ids: [1,2,3], certificate_template_id, issue_date }
     *
     * UPDATED this round (item 5): certificate_template_id and issue_date
     * are now required inputs from the Generate modal instead of being
     * implicit. issue_date defaults to today when not sent (belt-and-
     * braces — the UI already defaults its date input to today, this is
     * just the server-side fallback for the same rule).
     */
    public function generate(Request $request)
    {
        $data = $request->validate([
            'student_assessment_ids'   => 'required|array|min:1',
            'student_assessment_ids.*' => 'integer|distinct',
            'certificate_template_id'  => 'required|integer|exists:certificate_templates,id',
            'issue_date'               => 'nullable|date',
        ]);

        $issueDate = $data['issue_date'] ?? now()->toDateString();

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

            $certificate = DB::transaction(function () use ($assessment, $data, $issueDate) {
                $certificate = Certificate::create([
                    'student_assessment_id'   => $assessment->id,
                    'certificate_template_id' => $data['certificate_template_id'],
                    'issue_date'              => $issueDate,
                    'certificate_number'      => $this->nextCertificateNumber(),
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
            'studentAssessment.studentCourse',
            'certificateTemplate',
        ]);

        $studentCourse = optional($certificate->studentAssessment)->studentCourse;
        $startDate = optional($studentCourse)->created_at ? Carbon::parse($studentCourse->created_at) : null;

        // CHANGED this round ("to date is cetrificate genarate date"): the
        // "to" date on the "From: ... to ..." line used to come from
        // studentCourse->course_end_date, which is why it printed blank on
        // the real certificate you sent (that column wasn't set). Now it's
        // just the certificate's own generated/issue date — the same value
        // "Date of issued" below it already uses — so it's never blank for
        // any certificate that's actually been generated.
        $generatedDate = $certificate->issue_date ?? optional($certificate->generated_at)->toDateString();
        $endDate = $generatedDate ? Carbon::parse($generatedDate) : null;

        $durationWords = optional($certificate->certificateTemplate)->duration;

        $bottomImagePath = optional($certificate->certificateTemplate)->bottom_image_path;
        $bottomImageDims = $this->scaledImageDimensions($bottomImagePath, 'public', 123, 92);

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
            'qrDataUri'                 => $this->buildQrDataUri($certificate),
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

        $path = 'certificates/pdf/' . $certificate->certificate_number . '.pdf';
        Storage::disk('local')->put($path, $pdf->output());

        return $path;
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