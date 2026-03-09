<!DOCTYPE html>
<html>

<head>
    <title>Receipt Print</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        @media print {
            body {
                width: 10.8cm;
                /* ✅ Bill width */
                margin: 0;
                font-family: Arial, sans-serif;
                font-size: 12px;
                line-height: 1.2;
            }

            .bill-header {
                text-align: center;
                margin-bottom: 5px;
            }

            .bill-header h2 {
                margin: 0;
                font-size: 14px;
            }

            .bill-header p {
                margin: 0;
                font-size: 10px;
            }

            .bill-title {
                text-align: center;
                font-weight: bold;
                margin: 5px 0;
                font-size: 12px;
                border-bottom: 1px solid #000;
                padding-bottom: 2px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 5px;
            }

            th,
            td {
                border: 0.5px solid #000;
                padding: 3px 2px;
                text-align: left;
            }

            tfoot th {
                border-top: 1px solid #000;
            }
        }

        .bill-header img {
            display: block;
            margin: 0 auto 5px auto;
            max-width: 80px;
        }

        @media screen {

            /* Optional: smaller spacing for browser view */
            body {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="bill">

        {{-- Company Header --}}
        <div class="bill-header">
            <img src="{{ asset('uploads/settings/logo.png') }}"
                style="width:90px;filter: grayscale(100%); margin-bottom:5px;">
            <h2>HIGHTEC LANKA INTERNATIONAL VOCATIONAL & TECHNICAL TRAINING INSTITUTE (PVT) LTD</h2>
            @if ($itemSellBatch->branch_id == 1)
                <p>No. 549, Kurunegala-Kandy Road, Mawathagama, Sri Lanka.</p>
            @endif
            @if ($itemSellBatch->branch_id == 2)
                <p>No. 404/51C, Weikkiyawaththa Road, Ihala Bomairiya, Kaduwela.</p>
            @endif

            <p>Tel: +94 37 22 97 999 , +94 37 22 99 744</p>
            <p>TVEC Reg. No. P12-0113 PV 81127 | <a href="https://highteclanka.com">highteclanka.com</a> |
                info@highteclanka.com</p>
        </div>

        {{-- Receipt Title --}}
        <div class="bill-title">
            RECEIPT
        </div>

        {{-- {{ dd($itemSellBatch) }} --}}
        {{-- Customer & Invoice Info --}}
        {{-- Customer & Invoice Info --}}
        <div style="margin-bottom:5px; margin-top: 10px;">
            <table style="width:100%; font-size:12px;">
                <tr>
                    <td colspan="2"><strong>Invoice No:</strong> {{ $itemSellBatch->invoice_number }}</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>Reg No:</strong> {{ $itemSellBatch->student->admission_number ?? 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>Student:</strong> {{ $itemSellBatch->student->student_name ?? 'N/A' }}<br>
                    </td>
                </tr>

                <tr>
                    <td><strong>Payment Method:</strong>
                        {{ ucfirst($itemSellBatch->payment_method) }}
                    </td>
                    <td style="text-align:right;"><strong>Date:</strong>
                        {{ \Carbon\Carbon::parse($itemSellBatch->sell_date)->format('Y-m-d') }}</td>
                </tr>
            </table>
        </div>

        {{-- Items Table --}}
        <table style="width:100%; font-size:12px; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="border-bottom:1px dashed #000; text-align:left;">#</th>
                    <th style="border-bottom:1px dashed #000; text-align:left;">Item</th>
                    <th style="border-bottom:1px dashed #000; text-align:center;">Qty</th>
                    <th style="border-bottom:1px dashed #000; text-align:right;">Unit</th>
                    <th style="border-bottom:1px dashed #000; text-align:right;">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($itemSellBatch->itemSellItems as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->item->item_name ?? 'N/A' }}</td>
                        <td style="text-align:center;">{{ $item->quantity }}</td>
                        <td style="text-align:right;">{{ number_format($item->unit_price, 2) }}</td>
                        <td style="text-align:right;">{{ number_format($item->sub_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align:right; border-top:1px solid #000;">Total Qty:</th>
                    <th style="text-align:right; border-top:1px solid #000;">{{ $itemSellBatch->total_quantity }}</th>
                </tr>
                <tr>
                    <th colspan="4" style="text-align:right;">Total Amount:</th>
                    <th style="text-align:right;">{{ number_format($itemSellBatch->total_sell_price, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="4" style="text-align:right;">Paid:</th>
                    <th style="text-align:right;">{{ number_format($itemSellBatch->total_paid, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="4" style="text-align:right;">Due:</th>
                    <th style="text-align:right;">{{ number_format($itemSellBatch->total_due, 2) }}</th>
                </tr>
            </tfoot>
        </table>

    </div>

    <script>
        window.onload = function() {
            window.print();
        };


        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>

</html>
