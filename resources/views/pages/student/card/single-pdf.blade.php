<!DOCTYPE html>
<html>

<head>

    <style>
        /*
        |--------------------------------------------------------------------------
        | RESET
        |--------------------------------------------------------------------------
        */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        /*
        |--------------------------------------------------------------------------
        | PDF PAGE SIZE
        |--------------------------------------------------------------------------
        |
        | EXACT CARD SIZE
        |
        | Width  = 3.5 inch
        | Height = 2 inch
        |
        */

        @page {
            margin: 0;
            padding: 0;
            size: 3.5in 2in;
        }


        /*
        |--------------------------------------------------------------------------
        | HTML / BODY
        |--------------------------------------------------------------------------
        */

        html {
            width: 3.5in;
            height: 2in;
            margin: 0;
            padding: 0;
        }

        body {
            width: 3.5in;
            height: 2in;
            margin: 0;
            padding: 0;

            font-family: DejaVu Sans, sans-serif;
        }



        .page:last-child {
            page-break-after: avoid;
        }


        /*
        |--------------------------------------------------------------------------
        | FRONT CARD
        |--------------------------------------------------------------------------
        */

        .id-card {
            width: 3.48in;
            border-radius: 0.15in;
            background: #ffffff;
        }




        /*
        |--------------------------------------------------------------------------
        | LEFT GOLD STRIPE
        |--------------------------------------------------------------------------
        */

        .side-stripe {

            position: absolute;

            top: 0;
            left: 0;

            width: 0.045in;

            height: 2in;

            background: #f2b90c;

            z-index: 10;
        }


        /*
        |--------------------------------------------------------------------------
        | DECORATIVE TRIANGLES
        |--------------------------------------------------------------------------
        */

        .tri {

            position: absolute;

            width: 0;
            height: 0;

            opacity: 0.35;
        }

        .tri-a {

            left: 0.14in;
            bottom: 0.30in;

            border-left: 0.08in solid transparent;
            border-right: 0.08in solid transparent;

            border-bottom: 0.125in solid #a9c9e8;
        }

        .tri-b {

            left: 0.28in;
            bottom: 0.22in;

            border-left: 0.07in solid transparent;
            border-right: 0.07in solid transparent;

            border-top: 0.10in solid #f4b6c2;
        }

        .tri-c {

            left: 0.42in;
            bottom: 0.32in;

            border-left: 0.07in solid transparent;
            border-right: 0.07in solid transparent;

            border-bottom: 0.11in solid #a8d8b9;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .header {

            width: 100%;
            z-index: 1000;

            background-color: #1e3c72;

            text-align: center;

            padding: 0.085in 0.11in;

            white-space: nowrap;
        }


        .header img {

            display: inline-block;

            vertical-align: middle;

            height: 0.22in;

            width: auto;

            background: #ffffff;

            border-radius: 0.04in;

            padding: 0.02in;

            margin-right: 0.08in;
        }


        .header .org-name {

            display: inline-block;

            vertical-align: middle;

            font-size: 0.12in;

            line-height: 1;

            font-weight: bold;

            letter-spacing: 0.003in;

            color: #ffffff;
        }


        /*
        |--------------------------------------------------------------------------
        | BODY
        |--------------------------------------------------------------------------
        */

        .body-wrap {

            width: 100%;

            padding-top: 0.03in;
            padding-right: 0.08in;
            padding-bottom: 0in;
            padding-left: 0.11in;

            text-align: left;

            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | PHOTO
        |--------------------------------------------------------------------------
        */

        .photo-box {

            display: inline-block;

            vertical-align: top;

            width: 0.53in;
        }


        .photo-box img {

            width: 0.47in;

            height: 0.57in;

            object-fit: cover;

            border: 0.016in solid #2a5298;

            border-radius: 0.04in;

            padding: 0.02in;

            background: #ffffff;
        }


        /*
        |--------------------------------------------------------------------------
        | STUDENT DETAILS
        |--------------------------------------------------------------------------
        */

        .details {

            display: inline-block;

            vertical-align: top;

            width: 2.62in;

            white-space: normal;
        }


        .details h4 {

            font-size: 0.118in;

            line-height: 0.90;

            margin: 0 0 0.04in 0;

            color: #1e3c72;

            font-weight: bold;

            /* max-height: 0.28in; */

            overflow: hidden;
        }


        .details p {

            font-size: 0.095in;

            line-height: 1.0;

            margin: 0 0 0.02in 0;

            color: #333333;
        }


        .details .addr {

            max-height: 0.21in;

            /* overflow: hidden; */
        }


        .details .lbl {

            color: #b8860b;

            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | QR + BARCODE AREA
        |--------------------------------------------------------------------------
        */

        .codes {

            width: 100%;

            /* background-color: #f3f6fb; */

            /* border-top: 0.01in solid #e3d9ae; */

            padding: 0.06in 0.1in;

            margin-top: 0.078in;

            text-align: center;

            white-space: nowrap;
        }


        .codes .qr {

            display: inline-block;

            vertical-align: middle;

            width: 0.36in;

            height: 0.36in;
        }


        .codes .divider {

            display: inline-block;

            vertical-align: middle;

            width: 0.01in;

            height: 0.28in;

            background: #d3dbe8;

            margin-left: 0.14in;

            margin-right: 0.14in;
        }


        .codes .barcode {

            display: inline-block;

            vertical-align: middle;

            width: 1.95in;

            height: 0.25in;
        }


        /*
        |--------------------------------------------------------------------------
        | BACK CARD
        |--------------------------------------------------------------------------
        */

        .id-card-back {

            position: relative;
            height: 100%;

            margin: 0;



            background: #fdfaf2;

            text-align: center;
        }

        .id-card-back-section{
            padding: 0.2in;
        }

        /*
        |--------------------------------------------------------------------------
        | BACK STATEMENT
        |--------------------------------------------------------------------------
        */

        .back-statement {
            

            font-size: 0.083in;
            line-height: 1.3;

            color: #333333;
        }


        .back-org {

            font-size: 0.09in;

            font-weight: bold;

            color: #1e3c72;
        }


        /*
        |--------------------------------------------------------------------------
        | BACK CONTACT
        |--------------------------------------------------------------------------
        */

        .back-contact {

            font-size: 0.083in;

            line-height: 1.35;

            color: #333333;

            
        }


        .back-contact .lbl {

            color: #b8860b;

            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | BACK SIGNATURE
        |--------------------------------------------------------------------------
        */

        .back-sig {

            margin-top: 0.11in;

            text-align: center;

            white-space: nowrap;
        }


        .back-sig .qr-cell {

            display: inline-block;

            vertical-align: bottom;

            width: 0.47in;
        }


        .back-sig .qr-cell img {

            width: 0.36in;

            height: 0.36in;
        }


        .back-sig .sig-cell {

            display: inline-block;

            vertical-align: bottom;

            text-align: center;
        }


        .sig-line {
            margin: 0.3in auto 0.03in auto;

            border-top: 0.01in solid #999999;

            width: 1.53in;

        }


        .sig-label {

            font-size: 0.07in;

            letter-spacing: 0.007in;

            color: #666666;
        }


        /*
        |--------------------------------------------------------------------------
        | BACK TAGLINE
        |--------------------------------------------------------------------------
        */

        .back-tagline {

            margin-top: 0.08in;

            font-size: 0.07in;

            font-style: italic;

            color: #999999;
        }
    </style>

</head>


<body>


    {{-- =========================================================
         FRONT
         EXACT SIZE = 3.5in × 2in
         ========================================================= --}}

    <div class="page">

        <div class="id-card">


            {{-- Gold decoration --}}

            <div class="corner-tri"></div>

            <div class="side-stripe"></div>


            {{-- Decorative triangles --}}

            <div class="tri tri-a"></div>

            <div class="tri tri-b"></div>

            <div class="tri tri-c"></div>


            {{-- Header --}}

            <div class="header">

                <img src="{{ public_path('uploads/settings/logo.png') }}">

                <span class="org-name">
                    HIGHTEC LANKA INTERNATIONAL
                </span>

            </div>


            {{-- Student information --}}

            <div class="body-wrap">


                {{-- Photo --}}

                <div class="photo-box">

                    @if ($photo_b64)
                        <img src="data:image/png;base64,{{ $photo_b64 }}">
                    @endif

                </div>


                {{-- Details --}}

                <div class="details">


                    <h4>
                        {{ $student->student_name }}
                    </h4>


                    <p>

                        <span class="lbl">
                            Reg No:
                        </span>

                        {{ $reg_no }}

                    </p>


                    <p>

                        <span class="lbl">
                            NIC:
                        </span>

                        {{ $student->national_id_number ?? '-' }}

                    </p>


                    <p>

                        <span class="lbl">
                            DOB:
                        </span>

                        {{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d-M-Y') : '-' }}

                    </p>


                    <p class="addr">

                        <span class="lbl">
                            Address:
                        </span>

                        {{ $student->permanent_address ?? '-' }}

                    </p>


                </div>

            </div>


            {{-- QR + Barcode --}}

            <div class="codes">


                <img class="qr" src="data:image/svg+xml;base64,{{ $qr_base64 }}">


                <span class="divider"></span>


                <img class="barcode" src="data:image/png;base64,{{ $barcode_b64 }}">


            </div>


        </div>

    </div>



    {{-- =========================================================
         BACK
         EXACT SIZE = 3.5in × 2in
         ========================================================= --}}

    <div class="page">

        <div class="id-card-back">


            <div class="id-card-back-section">
            {{-- Statement --}}

            <div class="back-statement">

                This ID card is property of

                <br>

                <span class="back-org">
                    HIGHTEC LANKA INTERNATIONAL
                </span>

                <br>

                Vocational &amp; Technical Training Institute (Pvt) Ltd.

                <br>

                If found please return to,

            </div>


            {{-- Contact --}}

            <div class="back-contact">

                <span class="lbl">
                    Address:
                </span>

                No 549, Kandy Road, Mawathagama.

                <br>


                <span class="lbl">
                    Call:
                </span>

                +94 37 22 97 999

                <br>


                <span class="lbl">
                    Web:
                </span>

                www.highteclanka.com

                &nbsp;|&nbsp;


                <span class="lbl">
                    Email:
                </span>

                info@highteclanka.com

            </div>


            {{-- QR + Signature --}}

            <div class="back-sig">


                <span class="sig-cell">

                    <div class="sig-line"></div>
                    <div class="sig-label">
                        SIGNATURE OF CARD HOLDER
                    </div>

                </span>


            </div>


            {{-- Bottom tagline --}}

            </div>


        </div>

    </div>


</body>

</html>
