<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
        }

        .id-card {
            height: 193.07px;
            border: 1px solid #d8d8d8;
            border-radius: 14px;
            box-sizing: border-box;
            overflow: hidden;
            background: #ffffff;
        }

        /* Header */
        .header {
            background: #1e3c72;
            color: #ffffff;
            text-align: center;
            padding: 6px 4px 5px 4px;
        }

        .header img {
            height: 22px;
            background: #ffffff;
            border-radius: 4px;
            padding: 2px;
        }

        .header .org-name {
            font-size: 9.5px;
            font-weight: bold;
            letter-spacing: 0.4px;
            margin-top: 3px;
        }

        /* Body - photo left, details right, matches screen layout */
        .body-wrap {
            padding: 16px 10px 2px 10px;

        }

        .photo-box {
            float: left;
            width: 58px;
        }

        .photo-box img {
            width: 45px;
            height: 55px;
            object-fit: cover;
            border: 2px solid #2a5298;
            border-radius: 6px;
            padding: 2px;
            margin-bottom: 2px
        }

        .details {
            float: left;
            width: 256px;
            text-align: center;
        }

        .details h4 {
            font-size: 11.5px;
            margin: 2px 0 3px 0;
            color: #1e3c72;
            font-weight: bold;
        }

        .details p {
            font-size: 8.5px;
            margin: 1px 0;
            color: #333333;
        }

        .details .lbl {
            color: #2a5298;
            font-weight: bold;
        }

        /* Footer / codes */
        .codes {
            clear: both;
            background-color: #f3f6fb;
            border-top: 1px solid #dde5f0;
            padding: 8px 10px;
            text-align: center;
            overflow: hidden;
        }

        .codes .qr {
            width: 38px;
            vertical-align: middle;
            margin-right: 10px;
        }

        .codes .barcode {
            height: 26px;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="id-card">
        <div class="header">
            <img src="{{ public_path('uploads/settings/logo.png') }}">
            <div class="org-name">HIGHTEC LANKA INTERNATIONAL</div>
        </div>

        <div class="body-wrap">
            <div class="photo-box">
                @if ($photo_b64)
                    <img src="data:image/png;base64,{{ $photo_b64 }}">
                @endif

            </div>
            <div class="details">
                <h4>{{ $student->student_name }}</h4>
                <p><span class="lbl">Reg No:</span> {{ $reg_no }}</p>
                <p><span class="lbl">NIC:</span> {{ $student->national_id_number ?? '-' }}</p>
            </div>
        </div>

        <div class="codes">
            <img class="qr" src="data:image/svg+xml;base64,{{ $qr_base64 }}">
            <img class="barcode" src="data:image/png;base64,{{ $barcode_b64 }}">
        </div>

    </div>
</body>

</html>
