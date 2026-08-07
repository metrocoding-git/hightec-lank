@extends('layouts.app')

@section('mainContent')
<div class="container py-4">
    <div class="mb-3 text-center no-print col-12">
        {{-- <button onclick="window.print()" class="primary-btn fix-gr-bg me-2">
            <span class="ti-printer pr-2"></span> Print
        </button> --}}
        <a href="{{ route('student-card.pdf', $student->id) }}" class="primary-btn small fix-gr-bg col-12">
            <span class="ti-download pr-2"></span> Download PDF
        </a>
    </div>

    <div class="card-stack">

        {{-- FRONT --}}
        <div class="id-card-wrap">
            <div class="id-card">

                <div class="corner-tri"></div>
                <div class="side-stripe"></div>
                <div class="tri tri-a"></div>
                <div class="tri tri-b"></div>
                <div class="tri tri-c"></div>

                <div class="id-card-header">
                    <div class="header-line1">
                        <img src="{{ asset('uploads/settings/logo.png') }}" class="company-logo">
                        <span class="org-name">HIGHTEC LANKA INTERNATIONAL</span>
                    </div>
                    {{-- <div class="header-line2">VOCATIONAL &amp; TECHNICAL TRAINING INSTITUTE(PVT)LTD</div> --}}
                </div>

                <div class="id-card-body">
                    <div class="photo-box">
                        @if($student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}">
                        @else
                            <img src="{{ asset('uploads/settings/default-avatar.png') }}">
                        @endif
                    </div>

                    <div class="details">
                        <h4>{{ $student->student_name }}</h4>
                        <p><span class="lbl">Reg No</span><span class="sep">:</span> {{ $reg_no }}</p>
                        <p><span class="lbl">NIC No</span><span class="sep">:</span> {{ $student->national_id_number ?? '-' }}</p>
                        <p><span class="lbl">DOB</span><span class="sep">:</span> {{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d-M-Y') : '-' }}</p>
                        <p class="addr"><span class="lbl">Address</span><span class="sep">:</span> {{ $student->permanent_address ?? '-' }}</p>
                    </div>
                </div>

                <div class="id-card-codes">
                    <img src="data:image/svg+xml;base64,{{ $qr_base64 }}" class="qr">
                    <div class="code-divider"></div>
                    <img src="data:image/png;base64,{{ $barcode_b64 }}" class="barcode">
                </div>
            </div>
        </div>

        {{-- BACK --}}
        <div class="id-card-wrap">
            <div class="id-card id-card-back">
                <div class="back-statement">
                    This ID card is property of<br>
                    <span class="back-org">HIGHTEC LANKA INTERNATIONAL</span><br>
                    Vocational &amp; Technical Training Institute (Pvt) Ltd.<br>
                    If found please return to,
                </div>

                <div class="back-contact">
                    <span class="lbl">Address</span><span class="sep">:</span> No 549, Kandy Road, Mawathagama.<br>
                    <span class="lbl">Call</span><span class="sep">:</span> +94 37 22 97 999 &nbsp;|&nbsp; <span class="lbl">Fax</span><span class="sep">:</span> +94 37 22 96029<br>
                    <span class="lbl">Web</span><span class="sep">:</span> www.highteclanka.com &nbsp;|&nbsp; <span class="lbl">Email</span><span class="sep">:</span> info@highteclanka.com
                </div>

                <div class="back-sig">
                    <img src="data:image/svg+xml;base64,{{ $qr_base64 }}" class="back-qr">
                    <div class="sig-box">
                        <div class="sig-line"></div>
                        <div class="sig-label">SIGNATURE OF CARD HOLDER</div>
                    </div>
                </div>

                <div class="back-tagline">*** This card is a reflection of original certificate ***</div>
            </div>
        </div>
    </div>
</div>

<style>
.card-stack {
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    justify-content: center;
}

.id-card-wrap { display: flex; }

.id-card {
    position: relative;
    width: 3.375in;
    height: 2.125in;
    border-radius: 16px;
    padding: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    box-shadow: 0 6px 20px rgba(30,60,114,.25);
    overflow: hidden;
    background: #ffffff;
    border: 1.5px solid #d9b94a;
}

/* decorative gold triangle, top-right corner */
.corner-tri {
    position: absolute;
    top: 0; right: 0;
    width: 0; height: 0;
    border-top: 40px solid #f2b90c;
    border-left: 40px solid transparent;
    z-index: 1;
}

/* thin accent stripe down the left edge */
.side-stripe {
    position: absolute;
    top: 0; left: 0;
    width: 5px; height: 100%;
    background: linear-gradient(180deg, #f2b90c 0%, #e07b1e 100%);
    z-index: 1;
}

/* soft confetti triangles, low opacity, purely decorative */
.tri { position: absolute; width: 0; height: 0; opacity: .35; z-index: 0; }
.tri-a { left: 14px; bottom: 34px; border-left: 8px solid transparent; border-right: 8px solid transparent; border-bottom: 12px solid #a9c9e8; }
.tri-b { left: 28px; bottom: 26px; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 10px solid #f4b6c2; }
.tri-c { left: 42px; bottom: 36px; border-left: 7px solid transparent; border-right: 7px solid transparent; border-bottom: 10px solid #a8d8b9; }

.id-card-header {
    position: relative;
    z-index: 1;
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: #fff;
    text-align: center;
    padding: 8px 10px 7px;
}
.header-line1 {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.id-card-header .company-logo {
    height: 22px;
    background: #fff;
    border-radius: 4px;
    padding: 2px;
    flex-shrink: 0;
}
.org-name {
    font-weight: 700;
    font-size: 14.5px;
    letter-spacing: .4px;
    white-space: nowrap;
}
.header-line2 {
    margin-top: 3px;
    font-size: 7px;
    letter-spacing: .5px;
    font-weight: 600;
    color: #f2c94c;
    white-space: nowrap;
}

.id-card-body {
    position: relative;
    z-index: 1;
    display: flex;
    gap: 7px;
    padding: 5px 10px 0 12px;
}
.photo-box img {
    width: 50px;
    height: 58px;
    object-fit: cover;
    border: 2px solid #2a5298;
    border-radius: 6px;
    padding: 3px;
    background: #fff;
}
.details {
    flex: 1;
    text-align: left;
    padding-top: 1px;
}
.details h4 {
    font-size: 11.5px;
    line-height: 1.15;
    margin: 0 0 3px;
    color: #1e3c72;
    font-weight: 700;
}
.details p {
    font-size: 12px;
    line-height: 1.3;
    margin: 1.5px 0;
    color: #333;
}
.details p.addr {
    max-height: 10px;
    /* overflow: hidden; */
}
.details .lbl {
    color: #b8860b;
    font-weight: 700;
    display: inline-block;
    min-width: 38px;
}
.details .sep { color: #999; margin-right: 2px; }

.id-card-codes {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    /* background: #f3f6fb; */
    padding: 11px 10px;
    /* border-top: 1px solid #e3d9ae; */
    margin-top: 20px;
}
.id-card-codes .qr { width: 36px; height: 36px; }
.code-divider { width: 1px; height: 24px; background: #d3dbe8; }
.id-card-codes .barcode { height: 24px; }

/* Back of card */
.id-card-back {
    background: #fdfaf2;
    padding: 14px 16px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.back-statement {
    font-size: 8.5px;
    line-height: 1.4;
    color: #333;
}
.back-org {
    font-size: 9.5px;
    font-weight: 700;
    color: #1e3c72;
}
.back-contact {
    font-size: 8.5px;
    line-height: 1.5;
    color: #333;
    margin-top: 8px;
}
.back-contact .lbl {
    color: #b8860b;
    font-weight: 700;
}
.back-contact .sep { color: #999; margin-right: 1px; }
.back-sig {
    margin-top: 10px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    gap: 16px;
}
.back-qr { width: 38px; height: 38px; }
.sig-box { text-align: center; }
.sig-line {
    border-top: 1px solid #999;
    width: 150px;
    margin: 0 auto 3px;
}
.sig-label {
    font-size: 6.5px;
    letter-spacing: .5px;
    color: #666;
}
.back-tagline {
    margin-top: 8px;
    font-size: 6.5px;
    font-style: italic;
    color: #999;
}

@media print {
    .no-print { display: none !important; }
    body * { visibility: hidden; }
    .card-stack, .card-stack * { visibility: visible; }
    .card-stack { display: block; }
    .id-card-wrap { page-break-after: always; }
    .id-card-wrap:last-child { page-break-after: auto; }
    .id-card { box-shadow: none; }
}
</style>
@endsection
