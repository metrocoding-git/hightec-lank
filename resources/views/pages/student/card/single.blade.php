@extends('layouts.app')

@section('mainContent')
<div class="container py-4">
    <div class="mb-3 text-center no-print">
        <button onclick="window.print()" class="primary-btn fix-gr-bg me-2">
            <span class="ti-printer pr-2"></span> Print
        </button>
        <a href="{{ route('student-card.pdf', $student->id) }}" class="primary-btn small fix-gr-bg">
            <span class="ti-download pr-2"></span> Download PDF
        </a>
    </div>

    <div class="id-card mx-auto">
        <div class="id-card-header">
            <img src="{{ asset('uploads/settings/logo.png') }}" class="company-logo">
            <div class="org-name">HIGHTEC LANKA INTERNATIONAL</div>
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
                <p><strong>Reg No:</strong> {{ $reg_no }}</p>
                <p><strong>NIC:</strong> {{ $student->national_id_number ?? '-' }}</p>
            </div>
        </div>

        <div class="id-card-codes">
            <img src="data:image/svg+xml;base64,{{ $qr_base64 }}" class="qr">
            <img src="data:image/png;base64,{{ $barcode_b64 }}" class="barcode">
        </div>
    </div>
</div>

<style>
.id-card {
    width: 3.375in;
    height: 2.125in;
    border-radius: 14px;
    padding: 0;
    font-family: Arial, sans-serif;
    box-shadow: 0 4px 14px rgba(0,0,0,.25);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
    background: #fff;
    border: 1px solid #d8d8d8;
}

.id-card-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 6px 4px;
    gap: 2px;
}
.id-card-header .company-logo {
    height: 26px;
    background: #fff;
    border-radius: 4px;
    padding: 2px;
}
.org-name {
    font-weight: bold;
    font-size: 11px;
    letter-spacing: 0.5px;
}

.id-card-body {
    display: flex;
    gap: 8px;
    padding: 6px 10px 0 10px;
}
.photo-box img {
    width: 58px;
    height: 68px;
    object-fit: cover;
    border: 2px solid #2a5298;
    border-radius: 6px;
     padding: 4px;
}
.details {
    flex: 1;
    text-align: center;
}
.details h4 {
    font-size: 13px;
    margin: 0 0 3px;
    color: #1e3c72;
    font-weight: bold;
}
.details p {
    font-size: 9.5px;
    margin: 1px 0;
    color: #333;
}
.details p strong {
    color: #2a5298;
}

.id-card-codes {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f3f6fb;
    padding: 4px 10px;
    border-top: 1px solid #dde5f0;
}
.id-card-codes .qr { width: 42px; height: 42px; }
.id-card-codes .barcode { height: 28px; }

@media print {
    .no-print { display: none !important; }
    body * { visibility: hidden; }
    .id-card, .id-card * { visibility: visible; }
    .id-card { position: absolute; top: 0; left: 0; }
}
</style>
@endsection