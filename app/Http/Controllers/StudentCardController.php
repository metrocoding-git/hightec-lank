<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class StudentCardController extends Controller
{
    /**
     * Build the QR + barcode base64 images for a student.
     * Dompdf can't hit routes/facades at render time, so we
     * pre-bake everything into base64 data URIs.
     */
    private function buildCardData(Student $student): array
    {
        $regNo = $student->admission_number ?? (string) $student->id;

        $regNo = $student->admission_number ?? (string) $student->id;

        // QR code as SVG — no Imagick extension needed
        $qrSvg = base64_encode(
            QrCode::format('svg')->size(120)->margin(0)->errorCorrection('H')->generate($regNo)
        );

        // Barcode (Code128) — uses GD, works fine
        $barcodeGenerator = new BarcodeGeneratorPNG();
        $barcodePng = base64_encode(
            $barcodeGenerator->getBarcode($regNo, $barcodeGenerator::TYPE_CODE_128, 2, 50)
        );

        // Student photo (fallback to default avatar)
        $photoPath = $student->photo
            ? storage_path('app/public/' . $student->photo)
            : public_path('uploads/settings/default-avatar.png'); // match your actual default avatar path

        $photoBase64 = file_exists($photoPath)
            ? base64_encode(file_get_contents($photoPath))
            : null;

        return [
            'student'    => $student,
            'reg_no'     => $regNo,
            'qr_base64'  => $qrSvg,
            'barcode_b64' => $barcodePng,
            'photo_b64'  => $photoBase64,
            // static org assets — point these at your real files
            'company_logo'      => public_path('images/company-logo.png'),
            'company_signature' => public_path('images/company-signature.png'),
        ];
    }

    private function loadStudent($id): Student
    {
        return Student::with(['branch', 'district'])->findOrFail($id);
    }

    /** Single card — on-screen preview with print button */
    public function show($id)
    {
        $data = $this->buildCardData($this->loadStudent($id));
        return view('pages.student.card.single', $data);
    }

    /** Single card — PDF download */
    public function downloadPdf($id)
    {
        $data = $this->buildCardData($this->loadStudent($id));
        $pdf = Pdf::loadView('pages.student.card.single-pdf', $data)
            ->setPaper([0, 0, 242.65, 153.07], 'portrait'); // exact CR80 card size

        return $pdf->download('student-card-' . $data['reg_no'] . '.pdf');
    }


    /** Shared filter logic for bulk generation */
    private function filteredStudents(Request $request)
    {
        $branchId = $request->get('branch_id');
        $intakeId = $request->get('intake_id');
        $courseId = $request->get('course_id');

        return Student::with(['branch', 'district'])
            ->active()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($intakeId || $courseId, function ($q) use ($intakeId, $courseId) {
                $q->whereHas('courseWithIntake', function ($sc) use ($intakeId, $courseId) {
                    $sc->when($intakeId, fn($q2) => $q2->where('intake_id', $intakeId))
                        ->when($courseId, fn($q2) => $q2->where('course_id', $courseId));
                });
            })
            ->get();
    }

    /** Bulk — on-screen preview, all cards in a print-friendly grid */
    public function bulkPreview(Request $request)
    {
        $students = $this->filteredStudents($request);
        $cards = $students->map(fn($s) => $this->buildCardData($s));

        return view('pages.student.card.bulk', ['cards' => $cards]);
    }

    /** Bulk — single PDF with every card, one per page (or grid) */
    public function bulkPdf(Request $request)
    {
        $students = $this->filteredStudents($request);

        if ($students->isEmpty()) {
            return back()->with('error', 'No students matched the selected filters.');
        }

        $cards = $students->map(fn($s) => $this->buildCardData($s));

        $pdf = Pdf::loadView('pages.student.card.bulk-pdf', ['cards' => $cards])
            ->setPaper('a4', 'portrait');

        return $pdf->download('student-cards-bulk-' . now()->format('Y-m-d_His') . '.pdf');
    }
}
