<!DOCTYPE html>
<html>

<head>
    <title>Invoice Print</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        @media print {
            body {
                width: 210mm;
                margin: 0;
                font-family: Arial, sans-serif;
                font-size: 13px;
            }
        }

        .container {
            width: 210mm;
            margin: auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            width: 90px;
            margin-bottom: 5px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
            font-size: 16px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        .no-border td {
            border: none;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mb-10 {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

<div class="container">

    {{-- HEADER --}}
    <div class="header">
        <img src="{{ asset('uploads/settings/logo.png') }}">
        <h2>HIGHTEC LANKA INTERNATIONAL VOCATIONAL & TECHNICAL TRAINING INSTITUTE</h2>

        @php
            $branchId = $payments->first()->branch_id ?? null;
        @endphp

        @if ($branchId == 1)
            <p>No. 549, Kurunegala-Kandy Road, Mawathagama, Sri Lanka.</p>
        @elseif ($branchId == 2)
            <p>No. 404/51C, Weikkiyawaththa Road, Ihala Bomiriya, Kaduwela.</p>
        @endif

        <p>Tel: +94 37 22 97 999</p>
    </div>

    {{-- TITLE --}}
    <div class="title">
        INVOICE
    </div>

    {{-- CUSTOMER DETAILS --}}
    <table class="no-border mb-10">
        <tr>
            <td><strong>Invoice No:</strong> {{ $invoice_number }}</td>
            <td class="text-right"><strong>Date:</strong> {{ now()->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td><strong>Customer Name:</strong> {{ $payments->first()->customer_name ?? '-' }}</td>
            <td class="text-right"><strong>Status:</strong> Pending</td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Note:</strong> {{ $payments->first()->invoice_payment_note ?? '-' }}
            </td>
        </tr>
    </table>

    {{-- ITEMS TABLE --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student</th>
                <th>Admission No</th>
                <th>Fee / Course</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $key => $payment)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $payment->invoice->student->student_name ?? '-' }}</td>
                    <td>{{ $payment->invoice->student->admission_number ?? '-' }}</td>
                    <td>
                        @if($payment->invoice->invoice_type === 'fee')
                            {{ optional($payment->invoice->invoice_item->first()?->fee_type)->name }}
                        @else
                            {{ optional(optional($payment->invoice->paidCourses)->course)->name }}
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total Amount</th>
                <th class="text-right">{{ number_format($total, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    {{-- FOOTER --}}
    {{-- <br>
    <table class="no-border">
        <tr>
            <td>Prepared By: __________________</td>
            <td class="text-right">Signature: __________________</td>
        </tr>
    </table> --}}

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