{{--
    ============================================================================
    REPLACES the certificate-pdf.blade.php delivered last round — place at:
    resources/views/pages/administration/certificate/pdf.blade.php

    Only change from last round: the duration line is gone (you said you
    don't want that field). Signature blocks now render independently —
    the Conductor's signature appears here as soon as they've signed (even
    before the Owner has), and the Owner's appears once they've signed too.

    *** STILL A PLACEHOLDER LAYOUT — NOT your final design yet ***
    Your uploaded .cdr file could only be converted through LibreOffice
    (no CorelDRAW available here), and that conversion dropped the top ~40%
    of the page — the header, logo, title artwork and border never came
    through. This reproduces only what was recoverable. Once you send a
    clean export (PDF/PNG/JPG/SVG — anything but the raw .cdr), I'll rebuild
    this 100% to match it, including the currently-blank header/border area.
    ============================================================================
--}}
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page { margin: 0; }
    body {
        font-family: 'DejaVu Sans', sans-serif;
        margin: 0;
        padding: 0;
        color: #1a1a1a;
    }
    .sheet {
        width: 100%;
        padding: 36px 56px;
        box-sizing: border-box;
        border: 3px solid #0b3d91;
        position: relative;
    }

    /* ---- PLACEHOLDER header — replace once clean export arrives ---- */
    .placeholder-header {
        text-align: center;
        border: 1px dashed #999;
        padding: 10px;
        margin-bottom: 18px;
        color: #999;
        font-size: 10px;
    }
    .institute-name {
        text-align: center;
        font-size: 22px;
        font-weight: bold;
        color: #0b3d91;
        margin-bottom: 2px;
    }
    .cert-title {
        text-align: center;
        font-size: 16px;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #444;
        margin-bottom: 24px;
    }

    .cert-number {
        position: absolute;
        top: 14px;
        right: 20px;
        font-size: 10px;
        color: #555;
    }

    .body-text {
        text-align: center;
        font-size: 13px;
        line-height: 1.8;
        margin: 0 40px;
    }
    .student-name {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        border-bottom: 1px dashed #444;
        display: inline-block;
        padding: 4px 30px 8px 30px;
        margin: 14px auto;
    }
    .name-wrap { text-align: center; }
    .course-name {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin: 10px 0 4px 0;
    }
    .course-sub {
        text-align: center;
        font-size: 13px;
        color: #444;
        margin-bottom: 20px;
    }

    .sign-table {
        width: 100%;
        margin-top: 60px;
        border-collapse: collapse;
    }
    .sign-table td {
        width: 50%;
        text-align: center;
        vertical-align: bottom;
        padding: 0 20px;
    }
    .sign-img {
        height: 46px;
        margin-bottom: 4px;
    }
    .sign-line {
        border-top: 1px solid #333;
        margin: 0 30px 6px 30px;
    }
    .sign-name { font-size: 12px; font-weight: bold; }
    .sign-role { font-size: 10px; color: #555; }

    .footer-table {
        width: 100%;
        margin-top: 30px;
        border-collapse: collapse;
    }
    .footer-left { font-size: 9px; color: #666; vertical-align: middle; }
    .footer-right { text-align: right; vertical-align: middle; }
    .qr-placeholder {
        display: inline-block;
        width: 56px;
        height: 56px;
        border: 1px dashed #999;
        text-align: center;
        font-size: 7px;
        color: #999;
        line-height: 56px;
    }
    .iso-text { font-size: 8px; color: #666; text-align: right; margin-top: 2px; }
</style>
</head>
<body>
<div class="sheet">

    <div class="cert-number">{{ $certificate->certificate_number }}</div>

    <div class="placeholder-header">
        PLACEHOLDER — header / logo / title artwork from your certificate template did not
        survive the .cdr → PDF conversion. Will be replaced 1:1 once you send a clean export.
    </div>

    <div class="institute-name">Hightec Lanka</div>
    <div class="cert-title">Certificate of Completion</div>

    <div class="body-text">This is to certify that</div>

    <div class="name-wrap">
        <div class="student-name">{{ $certificate->studentAssessment->student->student_name ?? '' }}</div>
    </div>

    <div class="body-text">
        has successfully completed the training and maintenance programme of
    </div>

    <div class="course-name">
        {{ optional(optional($certificate->studentAssessment->template)->course)->name ?? '' }}
    </div>

    <table class="sign-table">
        <tr>
            <td>
                @if($certificate->owner_signature_path)
                    <img class="sign-img" src="data:image/png;base64,{{ base64_encode(\Illuminate\Support\Facades\Storage::disk('local')->get($certificate->owner_signature_path)) }}">
                @endif
                <div class="sign-line"></div>
                <div class="sign-name">Dr. A.N.C. Saman Kumara</div>
                <div class="sign-role">Owner</div>
            </td>
            <td>
                @if($certificate->conductor_signature_path)
                    <img class="sign-img" src="data:image/png;base64,{{ base64_encode(\Illuminate\Support\Facades\Storage::disk('local')->get($certificate->conductor_signature_path)) }}">
                @endif
                <div class="sign-line"></div>
                <div class="sign-name">{{ optional($certificate->studentAssessment->evaluator)->name ?? '' }}</div>
                <div class="sign-role">Assessment Conductor</div>
            </td>
        </tr>
    </table>

    <table class="footer-table">
        <tr>
            <td class="footer-left">
                No. 549, Kurunegala-Kandy Road, Mawathagama, Sri Lanka.&nbsp; https://highteclanka.com<br>
                TVEC Reg. No. P12/0113
            </td>
            <td class="footer-right">
                <div class="qr-placeholder">QR</div>
                <div class="iso-text">ISO 9001:2015 &nbsp; CERTIFIED TRAINING INSTITUTE<br>CER. NO:LK22/00000113</div>
            </td>
        </tr>
    </table>

</div>
</body>
</html>