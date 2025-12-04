<!DOCTYPE html>
<html>

<head>
    <title>Invoice Print</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        @media print {
            @page {
                /* size: 13.97cm 21.6cm portrait;
                height: 13.97cm;
                width: 21.6cm; */
                margin: 0;
            }
    
            html, body {
                height: 13.97cm;
                width: 21.6cm;
                margin: 0;
                padding: 0;
                background: #fff;
            }
    
            .invoice-report {
                height: 13.97cm;
                width: 21.6cm;
                position: relative;
                font-family: 'Arial Black', 'Courier New', monospace;
                font-size: 12px;
                color: #000;
            }
    
            .field {
                position: absolute;
                font-family: 'Arial Black', 'Courier New', monospace;
                font-size: 12px;
                color: #000;
                line-height: 1;
            }
    
            .field2 {
                position: absolute;
                font-family: 'Arial Black', 'Courier New', monospace;
                font-size: 11.5px;
                color: #000;
                line-height: 1;
            }


            .course-name {
                left: 3.5cm;
                top: 4.5cm;
                font-size: 12.5px;
            }

            .stu-category {
                left: 3.5cm;
                top: 5.0cm;
                font-size: 12.5px;
            }

            .invoice-date {
                left: 16.1cm;
                top: 4.5cm;
            }

            .invoice-no {
                left: 16.1cm;
                top: 5.0cm;
            }

            .admission-no {
                left: 16.1cm;
                top: 5.5cm;
            }

            .student-name {
                left:8.4cm;
                top: 6.0cm;
            }

            .student-nic {
                left:8.4cm;
                top: 6.6cm;
            }

            .student-address {
                left:8.4cm;
                top: 7.2cm;
            }

            .student-phone {
                left:8.4cm;
                top: 7.8cm;
            }

            .total-amount {
                left: 5.6cm;
                top: 8.5cm;
            }

            .paid-amount {
                left: 5.6cm;
                top: 9.1cm;
            }

            .due-amount {
                left: 5.6cm;
                top: 9.8cm;
            }

            .due-date {
                left: 5.6cm;
                top: 10.4cm;
            }
        }
    </style>
    
</head>

<body>
    <div class="invoice-report">

        {{-- Course Name --}}
        <div class="field course-name">

            {{ $invoice->invoice_type == 'fee'
                ? $invoice->invoice_item[0]->fee_type->name
                : ($invoice->invoice_type == 'course'
                    ? $invoice->paidCourses->course->name
                    : '-') }}
        </div>
        <div class="field stu-category">
            {{ $invoice->student->studentCategory->category_name ?? 'N/A' }}
        </div>

        {{-- Invoice Info --}}
        <div class="field2 invoice-date">{{ \Carbon\Carbon::parse($invoice->create_date)->format('F j, Y') }}</div>
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

    {{-- ✅ Auto print on page load --}}
    <script>
        window.onload = function() {
            window.print();
        };
        window.onafterprint = () => window.close();
    </script>
</body>

</html>
