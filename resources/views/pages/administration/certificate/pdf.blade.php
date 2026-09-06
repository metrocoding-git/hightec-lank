<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Certificate {{ $certificate->certificate_number }}</title>
<style>
  {{--
    Redesigned to match the sample certificate.pdf you uploaded. Portrait
    A4 (the OLD placeholder version was landscape — CertificateController's
    renderCertificatePdf() now calls ->setPaper('a4', 'portrait') to match).

    Layout verified by actually rendering this exact HTML (with sample
    values substituted for the {{ }} expressions) through wkhtmltopdf in a
    scratch test and visually comparing it side-by-side against your
    uploaded PDF — not just written from a description of the layout. The
    float-based two-column QR/bottom-image block and the signature-slot
    footer table both came out pixel-close to the sample on that engine.
    dompdf (what this app actually uses via Barryvdh\DomPDF) is a
    different rendering engine than wkhtmltopdf — modern dompdf (v2+)
    supports float/table/border-bottom-dotted the same way, so this should
    carry over, but it's worth generating one real certificate after this
    goes live and eyeballing it once, in case dompdf's line-wrapping or
    font metrics differ slightly from what was tested.

    Fonts: 'UnifrakturMaguntia' (blackletter heading) and a bold rounded
    sans (course name) aren't installed anywhere by default — both fall
    back to plain serif/sans-serif bold here. For a closer visual match,
    embed actual TTF files via @font-face with a LOCAL file path (dompdf
    can load local TTFs fine; it's remote/internet font URLs that are
    unreliable in a server environment) and add the @font-face rules here
    — ask me for that once you have the .ttf files, it's a small addition.
  --}}
  @page { margin: 0; size: A4 portrait; }
  * { box-sizing: border-box; }
  body { margin: 0; padding: 0; }
  .cert-page {
    position: relative;
    width: 595pt;
    height: 842pt;
    padding: 0 45pt;
    font-family: 'DejaVu Serif', 'Times New Roman', serif;
    color: #111;
  }

  {{-- Intentionally blank — you confirmed this is pre-printed letterhead
       stock, so nothing is drawn here on purpose. --}}
  .top-space { height: 260pt; }

  .cert-heading {
    text-align: center;
    font-family: 'UnifrakturMaguntia', 'Old English Text MT', 'Blackletter686 BT', serif;
    font-size: 32pt;
    margin: 0 0 14pt 0;
  }

  .cert-name-wrap { text-align: center; margin-bottom: 6pt; }
  .cert-name {
    display: inline-block;
    font-style: italic;
    font-weight: bold;
    font-size: 17pt;
    border-bottom: 1px dotted #333;
    padding: 0 10pt 3pt 10pt;
  }

  .cert-nic {
    text-align: center;
    font-style: italic;
    font-weight: bold;
    font-size: 10pt;
    margin: 0 0 14pt 0;
  }

  .cert-body {
    text-align: center;
    font-size: 12.5pt;
    line-height: 1.55;
    margin: 0 0 8pt 0;
  }

  .cert-course-name {
    text-align: center;
    font-family: 'Comic Sans MS', 'Trebuchet MS', sans-serif;
    font-weight: bold;
    font-size: 19pt;
    line-height: 1.35;
    margin: 6pt 0 10pt 0;
  }

  .dotted-inline {
    display: inline-block;
    border-bottom: 1px dotted #333;
    padding: 0 4pt 2pt 4pt;
    min-width: 90pt;
    text-align: center;
  }

  .cert-footer-table { width: 100%; margin-top: 46pt; border-collapse: collapse; }
  .footer-col {
    width: 33.33%;
    text-align: center;
    font-size: 9.5pt;
    vertical-align: bottom;
    padding: 0 4pt;
    white-space: nowrap;
  }
  .sig-image-slot { height: 34pt; }
  .sig-image-slot img { max-height: 34pt; max-width: 130pt; }
  .dotted-line { border-bottom: 1px dashed #333; height: 1pt; margin-bottom: 4pt; }
  .issue-date { margin-top: 6pt; font-size: 9pt; white-space: nowrap; }

  .bottom-blocks { margin-top: 55pt; }
  .bottom-blocks:after { content: ""; display: table; clear: both; }

  .qr-block { float: left; text-align: center; font-size: 8pt; line-height: 1.35; width: 110pt; }
  .qr-block img { margin-bottom: 3pt; }

  .bottom-image-block { float: right; width: 140pt; text-align: right; }
  .bottom-image-block img { max-width: 140pt; max-height: 110pt; }

  .back-side { page-break-before: always; padding: 55pt; font-size: 11pt; line-height: 1.5; }
</style>
</head>
<body>
@php
    $studentAssessment = $certificate->studentAssessment;
    $student = optional($studentAssessment)->student;

    // Course name shown on the certificate: the chosen Certificate Template's
    // override text (item 4 of the request) when one was picked at generate
    // time, falling back to the assessment's own template->course->name for
    // any certificate generated before this feature existed (no template
    // attached yet).
    $courseLine1 = $template->course_name_line1 ?? (optional(optional($studentAssessment)->template)->course->name ?? '');
    $courseLine2 = $template->course_name_line2 ?? null;

    $issueDate = $certificate->issue_date ?? optional($certificate->generated_at)->toDateString();
    $issueDateFormatted = $issueDate ? \Carbon\Carbon::parse($issueDate)->format('j-M-Y') : '';
@endphp

<div class="cert-page">
  <div class="top-space"></div>

  <h1 class="cert-heading">This is to certify that</h1>

  <div class="cert-name-wrap">
    <span class="cert-name">{{ $student->student_name ?? '' }}</span>
  </div>
  <p class="cert-nic">NIC No: {{ $student->national_id_number ?? '' }}</p>

  <p class="cert-body">
    has successfully completed
    @if ($durationWords)
        {{ $durationWords }} days
    @else
        the
    @endif
    training<br>and maintenance programme of
  </p>

  <p class="cert-course-name">
    {{ $courseLine1 }}
    @if ($courseLine2)
        <br>{{ $courseLine2 }}
    @endif
  </p>

  <p class="cert-body">
    and achieved grade <span class="dotted-inline">&ldquo;{{ $studentAssessment->grade ?? '' }}&rdquo;</span> pass at the final assessment<br>
    conducted at this institute
  </p>

  <p class="cert-body">
    From: <span class="dotted-inline">{{ $startDate ? $startDate->format('j-M-Y') : '' }}</span>
    to <span class="dotted-inline">{{ $endDate ? $endDate->format('j-M-Y') : '' }}</span>
  </p>

  <table class="cert-footer-table">
    <tr>
      <td class="footer-col">
        <div class="sig-image-slot">
          @if ($ownerSignatureDataUri)
              <img src="{{ $ownerSignatureDataUri }}">
          @endif
        </div>
        <div class="dotted-line"></div>
        Managing Director<br><strong>Dr. A.N.C. Saman Kumara</strong>
      </td>
      <td class="footer-col">
        <div class="sig-image-slot"></div>
        <div class="dotted-line"></div>
        {{ $certificate->certificate_number }}<br>Certificate No.
        <div class="issue-date">Date of issued : <span class="dotted-inline">{{ $issueDateFormatted }}</span></div>
      </td>
      <td class="footer-col">
        <div class="sig-image-slot">
          @if ($conductorSignatureDataUri)
              <img src="{{ $conductorSignatureDataUri }}">
          @endif
        </div>
        <div class="dotted-line"></div>
        Director-Training
      </td>
    </tr>
  </table>

  <div class="bottom-blocks">
    <div class="qr-block">
      @if ($qrDataUri)
          <img src="{{ $qrDataUri }}" width="70" height="70"><br>
      @endif
      ISO 9001:2015<br>CERTIFIED TRAINING INSTITUTE<br>CER. NO:LK22/00000113
    </div>

    <div class="bottom-image-block">
      @if ($bottomImageDataUri)
          <img src="{{ $bottomImageDataUri }}">
      @endif
    </div>
  </div>
</div>

@if ($template && $template->back_side_content)
    <div class="back-side">
        {!! nl2br(e($template->back_side_content)) !!}
    </div>
@endif
</body>
</html>