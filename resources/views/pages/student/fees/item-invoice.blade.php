<!DOCTYPE html>
<html>

<head>
    <title>Invoice Print</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        @media print {
            @page {
                size: 21.5cm 14cm portrait;
                margin: 0;
            }

            html,
            body {
                margin: 0;
                padding: 0;
                background: #fff;
            }


            .invoice-report {
                width: 100%;
                /* Full paper width */
                height: 14cm;
                /* Full paper height */
                position: relative;
                font-family: 'Arial Black', 'Courier New', monospace;
                font-size: 16px;
                color: #000;
            }

            .field {
                position: absolute;
                font-family: 'Arial Black', 'Courier New', monospace;
                color: #000;
                line-height: 1;
                font-size: 16px;
                font-weight: bold;
            }

            .field2 {
                position: absolute;
                font-family: 'Arial Black', 'Courier New', monospace;
                color: #000;
                line-height: 1;
                font-size: 14.5px;
            }

            .course-name {
                left: 4.2cm;
                top: 4.7cm;
                font-weight: bold;
                font-size: 18px;
            }

            .invoice-date {
                left: 19.0cm;
                top: 4.8cm;
            }

            .invoice-no {
                left: 19.0cm;
                top: 5.5cm;
                font-weight: bold;
            }

            .admission-no {
                left: 19.0cm;
                top: 6.1cm;
                font-weight: bold;
            }

            .student-name {
                left: 9.7cm;
                top: 6.58cm;
                font-weight: bold;
            }

            .student-nic {
                left: 9.7cm;
                top: 7.3cm;
                font-weight: bold;
            }

            .student-address {
                left: 9.7cm;
                top: 8.1cm;
                font-weight: bold;
            }

            .student-phone {
                left: 9.7cm;
                top: 8.9cm;
                font-weight: bold;
            }

            .total-amount {
                left: 7.2cm;
                top: 9.72cm;
                font-weight: bold;
            }

            .paid-amount {
                left: 7.2cm;
                top: 10.42cm;
                font-weight: bold;
            }

            .due-amount {
                left: 7.2cm;
                top: 11.2cm;
                font-weight: bold;
            }

            .due-date {
                left: 7.2cm;
                top: 12.0cm;
                font-weight: bold;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-report">


        {{-- Course Name --}}
        <div class="field course-name">
            {{-- {{ $invoice->invoice->paidCourses ?? ($invoice->invoice->paidCourses->course->name )?? 'Fee Details' }} --}}
            {{ $invoice->invoice->paidCourses->course->name ?? 'Admition fee' }}
        </div>

        {{-- Invoice Info --}}
        <div class="field2 invoice-date">
            {{ \Carbon\Carbon::parse($invoice->created_at)->format('d F Y') }}
        </div>
        <div class="field2 invoice-no">{{ $invoice->invoice->main_invoice_number ?? '' }}</div>
        <div class="field2 admission-no">{{ $invoice->invoice->student->admission_number ?? '' }}</div>

        {{-- Student Info --}}
        <div class="field student-name">{{ $invoice->invoice->student->student_name ?? '' }}</div>
        <div class="field student-nic">{{ $invoice->invoice->student->national_id_number ?? 'N/A' }}</div>
        <div class="field student-address">{{ $invoice->invoice->student->permanent_address ?? 'N/A' }}</div>
        <div class="field student-phone">{{ $invoice->invoice->student->phone_number ?? 'N/A' }}</div>

        {{-- Payment Info --}}
        <div class="field total-amount">{{ number_format($invoice->amount, 2) }}</div>
        <div class="field paid-amount">--</div>
        <div class="field due-amount"> {{ number_format($invoice->invoice->total_amount - $invoice->invoice->paid_amount, 2) }}</div>
        <div class="field due-date"> {{ \Carbon\Carbon::parse($invoice->invoice->due_date)->format('Y-m-d') }} </div>

    </div>

    {{-- ✅ Auto print on page load --}}
    <script>
        window.onload = function() {
            window.print();
        };
        window.onafterprint = () => window.close();
    </script>
</body>

</html>
