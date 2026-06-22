<!DOCTYPE html>
<html>
<head>
<style>
    @page { margin: 20px; }
    body { font-family: DejaVu Sans, sans-serif; }
    .grid { width: 100%; }
    .card-cell {
        display: inline-block;
        width: 242px; height: 153px;
        border: 1px solid #ccc; border-radius: 8px;
        padding: 6px; box-sizing: border-box;
        margin: 4px;
        page-break-inside: avoid;
    }
    .header { font-size: 9px; font-weight: bold; text-align: center; }
    .body-row { display: table; width: 100%; margin-top: 3px; }
    .photo, .details { display: table-cell; vertical-align: top; }
    .photo img { width: 50px; height: 60px; border: 1px solid #999; }
    .details { padding-left: 5px; font-size: 7px; }
    .details h4 { font-size: 9px; margin: 0 0 2px; }
    .codes { display: table; width: 100%; margin-top: 3px; }
    .codes div { display: table-cell; vertical-align: middle; }
    .codes img.qr { width: 32px; }
    .codes img.barcode { height: 22px; }
</style>
</head>
<body>
    <div class="grid">
        @foreach($cards as $c)
            <div class="card-cell">
                <div class="header">YOUR INSTITUTE NAME</div>
                <div class="body-row">
                    <div class="photo">
                        @if($c['photo_b64'])
                            <img src="data:image/png;base64,{{ $c['photo_b64'] }}">
                        @endif
                    </div>
                    <div class="details">
                        <h4>{{ $c['student']->student_name }}</h4>
                        Reg: {{ $c['reg_no'] }}<br>
                        NIC: {{ $c['student']->national_id_number ?? '-' }}
                    </div>
                </div>
                <div class="codes">
                    <div><img class="qr" src="data:image/png;base64,{{ $c['qr_base64'] }}"></div>
                    <div><img class="barcode" src="data:image/png;base64,{{ $c['barcode_b64'] }}"></div>
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>