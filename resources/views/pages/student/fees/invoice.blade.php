<!DOCTYPE html>
<html>

<head>
    <title>Invoice Print</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        @media print {
            @page {
                size: portrait;   /* Force browser portrait */
                margin: 0;
            }

            html, body {
                margin: 0;
                padding: 0;
                background: #fff;
            }

            /* WRAPPER: Rotate full layout back to landscape */
            .rotate-container {
                width: 21.5cm;
                height: 14cm;
                position: absolute;
                top: 0;
                left: 0;

                transform-origin: top left;
                transform: rotate(90deg) translate(0, -21.5cm);
            }

            .invoice-report {
                width: 21.5cm;
                height: 14cm;
                position: relative;
                font-family: 'Arial Black', 'Courier New', monospace;
                font-size: 16px;
                color: #000;
            }

            .field {
                position: absolute;
                font-size: 16px;
                font-weight: bold;
            }

            .field2 {
                position: absolute;
                font-size: 14.5px;
            }

            .course-name { left: 4.2cm; top: 4.7cm; font-size: 18px; }
            .invoice-date { left: 19cm; top: 4.8cm; }
            .invoice-no { left: 19cm; top: 5.5cm; }
            .admission-no { left: 19cm; top: 6.1cm; }

            .student-name { left: 9.7cm; top: 6.58cm; }
            .student-nic { left: 9.7cm; top: 7.3cm; }
            .student-address { left: 9.7cm; top: 8.1cm; }
            .student-phone { left: 9.7cm; top: 8.9cm; }

            .total-amount { left: 7.2cm; top: 9.72cm; }
            .paid-amount { left: 7.2cm; top: 10.42cm; }
            .due-amount { left: 7.2cm; top: 11.2cm; }
            .due-date { left: 7.2cm; top: 12cm; }
        }
    </style>
</head>

<body>
    <div class="rotate-container">
        <div class="invoice-report">

            {{-- Course Name --}}
            <div class="field course-name">
                {{ $invoice->invoice_type == 'fee'
                    ? $invoice->invoice_item[0]->fee_type->name
                    : ($invoice->invoice_type == 'course'
                        ? $invoice->paidCourses->course->name
                        : '-') }}
            </div>

            {{-- Invoice Info --}}
            <div class="field2 invoice-date">{{ $invoice->formatted_date ?? '' }}</div>
            <div class="field2 invoice-no">{{ $invoice->main_invoice_number ?? '' }}</div>
            <div class="field2 admission-no">{{ $invoice->student->admission_number ?? '' }}</div>

            {{-- Student Info --}}
            <div class="field student-name">{{ $invoice->student->student_name ?? '' }}</div>
            <div class="field student-nic">{{ $invoice->student->national_id_number ?? 'N/A' }}</div>
            <div class="field student-address">{{ $invoice->student->permanent_address ?? 'N/A' }}</div>
            <div class="field student-phone">{{ $invoice->student->phone_number ?? 'N/A' }}</div>

            {{-- Payment Info --}}
            <div class="field total-amount">{{ number_format($invoice->total_amount, 2) }}</div>
            <div class="field paid-amount">--</div>
            <div class="field due-amount">{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}</div>
            <div class="field due-date">{{ $invoice->created_to_due ?? '' }}</div>

        </div>
    </div>

    <script>
        window.onload = () => window.print();
        window.onafterprint = () => window.close();
    </script>
</body>

</html>
