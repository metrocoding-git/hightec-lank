<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        @page {
            size: A4;
            margin-top: 7cm;
            margin-bottom: 3cm;
            margin-left: 1cm;
            margin-right: 1cm;
        }

        body {
            margin: auto;
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            height: 100%;
            /* overflow: hidden; */
        }

        .header {
            display: flex;
            justify-content: space-between;
        }

        .box {
            border: 1px solid #000;
            padding: 10px;
        }

        .invoice-title {
            font-size: 40px;
            font-weight: bold;
            text-align: right;
        }

        table,
        tr,
        td,
        th {
            page-break-inside: avoid;
        }

        .main-table {
            page-break-inside: auto;
        }

        .main-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .total-box {
            page-break-inside: avoid;
        }

        .payment-section {
            page-break-inside: avoid;
        }

        .info-table,
        .main-table,
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .info-table td,
        .main-table td,
        .main-table th,
        .payment-table td {
            border: 1px solid #000;
            padding: 6px;
        }

        .main-table th {
            background: #ddd;
        }

        .main-table tbody tr:nth-child(even) {
            background: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .total-box {
            width: 300px;
            float: right;
            margin-top: 10px;
            border: 1px solid #000;
            padding: 10px;
            font-weight: bold;
        }

        .payment-section {
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-style: italic;
        }

        .note {
            margin-top: 10px;
            font-size: 11px;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>

    @php
        $payment = $payments->first();
        $invoice = $payment->invoice;
        $student = $invoice->student ?? null;
    @endphp

    {{-- Header --}}
    <div class="header">
        <div class="box" style="width: 50%;">
            <strong>Customer Name & Address</strong><br><br>
            {!! nl2br(str_replace([' / ', '/', ', '], "\n", $payment->customer_name)) !!}

        </div>

        <div style="width: 45%;">
            <div class="invoice-title">Invoice</div>

            <table class="info-table">
                <tr>
                    <td><strong>Date</strong></td>
                    <td><strong>Invoice No.</strong></td>
                </tr>
                <tr>
                    <td>{{ $payment->payment_invoice_open_date ? \Carbon\Carbon::parse($payment->payment_invoice_open_date)->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ $invoice_number }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Items Table --}}
    <table class="main-table">
        <thead>
            <tr>
                <th width="10%">Qty</th>
                <th width="60%">Description</th>
                <th width="15%">Rate</th>
                <th width="15%">Amount</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($payments as $item)
                <tr>
                    <td style="text-align: center">1</td>


                    <td>
                        @if ($item->invoice->invoice_type === 'fee')
                            {{ optional($item->invoice->invoice_item->first()?->fee_type)->name }}
                        @elseif ($item->invoice->invoice_type === 'item_sell')
                            Uniform Fee
                        @elseif ($item->invoice->invoice_type === 'course')
                            {{ 'Course fee - ' . optional(optional($item->invoice->paidCourses)->course)->name }}
                        @elseif ($item->invoice->invoice_type === 'nvq')
                            {{ 'NVQ fee - ' . optional(optional($item->invoice->paidCourses)->course)->name }}
                        @elseif ($item->invoice->invoice_type === 'food')
                            {{ 'Food fee - ' . optional(optional($item->invoice->paidCourses)->course)->name }}
                        @else
                            {{ $item->invoice->invoice_type . ' fee - ' . optional(optional($item->invoice->paidCourses)->course)->name }}
                        @endif
                    </td>


                    <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                    <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="4"></td>
            </tr>
            {{-- Student Info --}}
            <tr>
                <td></td>
                <td colspan="3" style="padding:0;">
                    <table style="width:100%; border-collapse: collapse;">



                        <tr>
                            <td style="padding:6px;">Register Number</td>
                            <td style="padding:6px; text-align:right;">{{ $student->admission_number ?? '' }}</td>
                        </tr>

                        <tr style="background:#f2f2f2;">
                            <td style="padding:6px;">NIC</td>
                            <td style="padding:6px; text-align:right;">{{ $student->national_id_number ?? '' }}</td>
                        </tr>

                        <tr>
                            <td style="padding:6px;">Student Name</td>
                            <td style="padding:6px; text-align:right;">{{ $student->student_name ?? '' }}</td>
                        </tr>


                    </table>
                </td>
            </tr>

        </tbody>
    </table>

    {{-- Total --}}
    <div class="total-box">
        Total : LKR {{ number_format($total, 2) }}
    </div>

    <div style="clear: both;"></div>

    {{-- Payment --}}
    <div class="payment-section">
        <strong>Payment Methods</strong>

        <table class="payment-table">
            <tr>
                <td colspan="3"><strong>Bank Deposit or Online Fund Transfers</strong></td>
            </tr>
            <tr>
                <td colspan="3">
                    Payee Name : Hightec Lanka International Vocational & Technical Training Institute (Pvt) Ltd
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    SWIFT Address : BSAMLKLX
                </td>
            </tr>
            <tr>
                <td>Bank</td>
                <td>Branch</td>
                <td>Account Number</td>
            </tr>
            <tr>
                <td>Sampath Bank</td>
                <td>Mawathagama</td>
                <td>010910000506</td>
            </tr>
        </table>
    </div>

    @if (!empty($payment->invoice_payment_note))
        <div class="box" style="width: 97%; margin-top: 0.2cm;">
            <strong>Note:</strong><br>
            {!! nl2br(str_replace([' / ', '/', ', '], "\n", $payment->invoice_payment_note)) !!}



        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        It's been a pleasure working with you!
    </div>

    <div class="note">
        This is a computer-generated document. No signature is required.
    </div>

</body>

<script>
    window.onload = function() {
        window.print();
    };

    window.onafterprint = function() {
        window.close();
    };
</script>

</html>
