<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Certificate {{ $certificate->certificate_number }}</title>
    {{-- font --}}

    <style>
        @page {
            margin: 0;
            size: 612pt 792pt;
        }

        * {
            box-sizing: border-box;
        }

        @font-face {
            font-family: 'UnifrakturCookPDF';
            src: url('{{ storage_path('app/fonts/UnifrakturCook-Bold.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'OrbitronVariableFont_wght';
            src: url('{{ storage_path('app/fonts/Orbitron-VariableFont_wght.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        body {
            margin: 0;
            padding: 0;
        }

        @if ($nameFontDataUri)
            @font-face {
                font-family: 'CertNameFont';
                src: url({{ $nameFontDataUri }}) format('truetype');
            }
        @endif
        @if ($gradeFontDataUri)
            @font-face {
                font-family: 'CertGradeFont';
                src: url({{ $gradeFontDataUri }}) format('truetype');
            }
        @endif
        .cert-page {
            position: relative;
            width: 612pt;
            height: 792pt;
            font-family: 'EB Garamond', 'DejaVu Serif', 'Times New Roman', serif;
            color: #111;
        }

        .cert-heading {
            position: absolute;
            top: 216pt;
            left: 48pt;
            width: 512pt;
            margin: 0;
            text-align: center;
            font-family: 'UnifrakturCookPDF';
            font-weight: 700;
            font-style: normal;
            font-size: 36pt;
            line-height: 1;
        }

        .cert-name-wrap {
            position: absolute;
            top: 268pt;
            left: 100pt;
            width: 412pt;
            text-align: center;
            border-bottom: 1px dotted #333;
            padding-bottom: 3pt;
        }

        .cert-name {
            font-family: 'CertNameFont', 'DejaVu Serif', 'Times New Roman', serif;
            font-style: italic;
            font-weight: bold;
            font-size: 14pt;
            line-height: 1;
            white-space: nowrap;
        }

        .cert-nic {
            position: absolute;
            top: 293pt;
            left: 48pt;
            width: 512pt;
            margin: 0;
            text-align: center;
            font-style: italic;
            font-weight: bold;
            font-size: 10pt;
            line-height: 1;
        }

        .cert-nic-label {
            font-family: 'CertGradeFont', 'DejaVu Serif', 'Times New Roman', serif;
        }

        .cert-nic-value {
            font-family: 'CertNameFont', 'DejaVu Serif', 'Times New Roman', serif;
        }

        .cert-body-line1 {
            position: absolute;
            top: 306pt;
            left: 48pt;
            width: 512pt;
            margin: 0;
            text-align: center;
            font-size: 12.5pt;
            line-height: 1;
        }

        .cert-body-line2 {
            position: absolute;
            top: 326pt;
            left: 48pt;
            width: 512pt;
            margin: 0;
            text-align: center;
            font-size: 12.5pt;
            line-height: 1;
        }

        .cert-course-line1 {
            position: absolute;
            top: 348.7pt;
            left: 48pt;
            width: 512pt;
            margin: 0;
            text-align: center;
            font-family: 'OrbitronVariableFont_wght';
            font-style: normal;
            font-weight: bold;
            font-size: 20pt;
            line-height: 1;
        }

        .cert-course-line2 {
            position: absolute;
            top: 370.5pt;
            left: 48pt;
            width: 512pt;
            margin: 0;
            text-align: center;
            font-family: 'OrbitronVariableFont_wght';
            font-style: normal;
            font-weight: bold;
            font-size: 20pt;
            line-height: 1;
        }

        .cert-grade-line {
            position: absolute;
            top: 402pt;
            left: 48pt;
            width: 512pt;
            margin: 0;
            text-align: center;
            font-size: 13pt;
            line-height: 1;
        }

        .cert-grade-line .dotted-inline {
            font-family: 'CertGradeFont', 'DejaVu Serif', 'Times New Roman', serif;
            font-size: 19pt;
            font-weight: bold;
            line-height: 1;
            vertical-align: middle;
        }

        .cert-conducted-line {
            position: absolute;
            top: 432.0pt;
            left: 48pt;
            width: 512pt;
            margin: 0;
            text-align: center;
            font-size: 13pt;
            line-height: 1;
        }

        .cert-dates-line {
            position: absolute;
            top: 457.8pt;
            left: 48pt;
            width: 512pt;
            margin: 0;
            text-align: center;
            font-family: 'Montserrat', 'Helvetica', 'DejaVu Sans', Arial, sans-serif;
            font-size: 12pt;
            line-height: 1;
        }

        .cert-dates-line .dotted-inline {
            padding-bottom: 0.5pt;
        }

        .dotted-inline {
            display: inline-block;
            border-bottom: 1px dotted #333;
            padding: 0 2pt 2pt 2pt;
            min-width: 90pt;
            text-align: center;
        }

        .cert-footer {
            position: absolute;
            top: 485pt;
            left: 48pt;
            width: 512pt;
        }

        .footer-row {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-col {
            width: 33.33%;
            text-align: center;
            font-family: 'Helvetica', 'DejaVu Sans', Arial, sans-serif;
            font-size: 8pt;
            padding: 0 4pt;
        }

        .footer-value-row .footer-col {
            height: 34pt;
            vertical-align: bottom;
        }

        .footer-value-row .footer-col img {
            max-height: 32pt;
            max-width: 130pt;
        }

        .footer-cert-number {
            font-size: 10pt;
        }

        .footer-label-row .footer-col {
            padding-top: 4pt;
            vertical-align: top;
            white-space: nowrap;
        }

        .footer-divider-seg {
            border-top: 1px dashed #333;
            width: 88%;
            margin: 0 auto 4pt auto;
        }

        .footer-signer-name {
            display: block;
            margin-top: 0pt;
            font-weight: bold;
            font-size: 8pt;
        }

        .footer-issue-date {
            margin-top: 9pt;
            font-family: 'Montserrat', 'Helvetica', 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            white-space: nowrap;
        }

        .footer-issue-date .dotted-inline {
            font-size: 11pt;
            min-width: 60pt;
            padding-bottom: 0.5pt;
        }

        .qr-image {
            position: absolute;
            top: 656.6pt;
            left: 100.8pt;
        }

        .qr-caption {
            position: absolute;
            top: 712.6pt;
            left: 56pt;
            width: 150pt;
            text-align: center;
            font-family: 'Helvetica', 'DejaVu Sans', Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.21;
        }

        .bottom-image-block {
            position: absolute;
            top: 658.6pt;
            left: 442.9pt;
            width: 123pt;
            text-align: left;
        }

        .bottom-image-block img {
            max-width: 123pt;
            max-height: 90pt;
        }

        .back-side {
            page-break-before: always;
            padding: 55pt;
            font-size: 11pt;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    @php
        $studentAssessment = $certificate->studentAssessment;
        $student = optional($studentAssessment)->student;
        $courseLine1 =
            $template->course_name_line1 ?? (optional(optional($studentAssessment)->template)->course->name ?? '');
        $courseLine2 = $template->course_name_line2 ?? null;

        $issueDate = $certificate->issue_date ?? optional($certificate->generated_at)->toDateString();
        $issueDateFormatted = $issueDate ? \Carbon\Carbon::parse($issueDate)->format('j-M-Y') : '';
    @endphp

    <div class="cert-page">
        <h1 class="cert-heading">This is to certify that</h1>

        <div class="cert-name-wrap">
            <span class="cert-name" style="font-size: {{ $nameFontSize }}pt;">{{ $student->student_name ?? '' }}</span>
        </div>
        <p class="cert-nic"><span class="cert-nic-label">NIC No:</span> <span
                class="cert-nic-value">{{ $student->national_id_number ?? '' }}</span></p>

        <p class="cert-body-line1">
            has successfully completed
            @if ($durationWords)
                {{ $durationWords }} days
            @else
                the
            @endif
            training
        </p>
        <p class="cert-body-line2">and maintenance programme of</p>

        <p class="cert-course-line1">{{ $courseLine1 }}</p>
        @if ($courseLine2)
            <p class="cert-course-line2">{{ $courseLine2 }}</p>
        @endif

        <p class="cert-grade-line">
            and achieved grade <span class="dotted-inline">&ldquo;{{ $studentAssessment->grade ?? '' }}&rdquo;</span>
            pass at the final assessment
        </p>
        <p class="cert-conducted-line">conducted at this institute</p>

        <p class="cert-dates-line">
            From: <span class="dotted-inline">{{ $startDate ? $startDate->format('j-M-Y') : '' }}</span>
            to <span class="dotted-inline">{{ $endDate ? $endDate->format('j-M-Y') : '' }}</span>
        </p>

        <div class="cert-footer">
            <table class="footer-row footer-value-row">
                <tr>
                    <td class="footer-col">
                        @if ($ownerSignatureDataUri)
                            <img src="{{ $ownerSignatureDataUri }}">
                        @endif
                    </td>
                    <td class="footer-col footer-cert-number">{{ $certificate->certificate_number }}</td>
                    <td class="footer-col">
                        @if ($conductorSignatureDataUri)
                            <img src="{{ $conductorSignatureDataUri }}">
                        @endif
                    </td>
                </tr>
            </table>
            <table class="footer-row footer-label-row">
                <tr>
                    <td class="footer-col">
                        <div class="footer-divider-seg"></div>
                        Managing Director
                        <span class="footer-signer-name">Dr. A.N.C. Saman Kumara</span>
                    </td>
                    <td class="footer-col">
                        <div class="footer-divider-seg"></div>
                        Certificate No.
                    </td>
                    <td class="footer-col">
                        <div class="footer-divider-seg"></div>
                        Director-Training
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="footer-col">
                        <div class="footer-issue-date">Date of issued : <span
                                class="dotted-inline">{{ $issueDateFormatted }}</span></div>
                    </td>
                </tr>
            </table>
        </div>

        @if ($qrDataUri)
            <img class="qr-image" src="{{ $qrDataUri }}" width="55" height="55"
                style="width: 55pt; height: 55pt;">
        @endif
        <div class="qr-caption">
            ISO 9001:2015<br>CERTIFIED TRAINING INSTITUTE<br>CER. NO:LK22/00000113
        </div>

        <div class="bottom-image-block">
            @if ($bottomImageDataUri)
                <img src="{{ $bottomImageDataUri }}"
                    @if ($bottomImageWidth && $bottomImageHeight) width="{{ $bottomImageWidth }}" height="{{ $bottomImageHeight }}" style="width: {{ $bottomImageWidth }}pt; height: {{ $bottomImageHeight }}pt;" @endif>
            @endif
        </div>
    </div>

    {{-- @if ($template && $template->back_side_content)
        <div class="back-side">
            {!! nl2br(e($template->back_side_content)) !!}
        </div>
    @endif --}}
</body>

</html>
