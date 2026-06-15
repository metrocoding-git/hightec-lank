<!DOCTYPE html>
<html>

<head>
    <title>Invoice Print</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        @media print {
            @page {
                /* size: landscape;
                height: 13.97cm;
                width: 21.6cm; */
                margin: 0;
            }

            html,
            body {
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

            /*
            .course-name {
                left: 4cm;
                top: 4.1cm;
                font-size: 12.5px;
            }

            .invoice-date {
                left: 16.6cm;
                top: 4.1cm;
            }

            .invoice-no {
                left: 16.6cm;
                top: 4.6cm;
            }

            .admission-no {
                left: 16.6cm;
                top: 5.1cm;
            }

            .student-name {
                left:9.0cm;
                top: 5.6cm;
            }

            .student-nic {
                left:9.0cm;
                top: 6.2cm;
            }

            .student-address {
                left:9.0cm;
                top: 6.8cm;
            }

            .student-phone {
                left:9.0cm;
                top: 7.4cm;
            }

            .total-amount {
                left: 6.1cm;
                top: 8.1cm;
            }

            .paid-amount {
                left: 6.1cm;
                top: 8.7cm;
            }

            .due-amount {
                left: 6.1cm;
                top: 9.4cm;
            }

            .due-date {
                left: 6.1cm;
                top: 10.0cm;
            } */


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
                left: 8.4cm;
                top: 6.0cm;
            }

            .student-nic {
                left: 8.4cm;
                top: 6.6cm;
            }

            .student-address {
                left: 8.4cm;
                top: 7.2cm;
            }

            .student-phone {
                left: 8.4cm;
                top: 7.8cm;
            }

            .lunchbo-number {
                left: 16.1cm;
                top: 6.6cm;
            }

            .meals {
                left: 16.1cm;
                top: 7.2cm;
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
            {{-- {{ $invoice->invoice->paidCourses ?? ($invoice->invoice->paidCourses->course->name )?? 'Fee Details' }} --}}
            {{
                $invoice->invoice?->invoice_type == 'fee'
                    ? ($invoice->invoice?->invoice_item[0]?->fee_type?->name ?? '-')
                    : (
                        $invoice->invoice?->invoice_type == 'course'
                            ? ($invoice->invoice?->paidCourses?->course?->name ?? '-')
                            : (
                                $invoice->invoice?->invoice_type == 'nvq'
                                    ? 'NVQ - ' . ($invoice->invoice?->paidCourses?->course?->name ?? '-')
                                    : (
                                        $invoice->invoice?->invoice_type == 'food'
                                            ? 'Food - ' . ($invoice->invoice?->paidCourses?->course?->name ?? '-')
                                            : '-'
                                    )
                            )
                    )
            }}
        </div>
        <div class="field stu-category">
            {{ $invoice->invoice->student->studentCategory->category_name ?? 'N/A' }}
        </div>

        {{-- Invoice Info --}}
        <div class="field2 invoice-date">{{ \Carbon\Carbon::parse($invoice->create_date)->format('F j, Y') }}</div>
        <div class="field2 invoice-no">{{ $invoice->receipt_number ?? '' }}</div>
        <div class="field2 admission-no">{{ $invoice->invoice->student->admission_number ?? '' }}</div>

        {{-- Student Info --}}
        <div class="field student-name">{{ $invoice->invoice->student->student_name ?? '' }}</div>
        <div class="field student-nic">{{ $invoice->invoice->student->national_id_number ?? 'N/A' }}</div>
        <div class="field student-address">{{ $invoice->invoice->student->permanent_address ?? 'N/A' }}</div>
        <div class="field student-phone">{{ $invoice->invoice->student->phone_number ?? 'N/A' }}</div>

        @if ($is_food_payment)
            <div class="field lunchbo-number">BOX :- {{ $lunchbox_number ?? 'N/A' }}</div>
            <div class="field meals">Meals :- {{ $has_breakfast ? ' B /' : '' }}
                {{ $has_lunch ? ' L /' : '' }} {{ $has_dinner ? ' D' : '' }}</div>
        @endif


        {{-- Payment Info --}}
        <div class="field total-amount">{{ number_format($invoice->amount, 2) }}</div>
        <div class="field paid-amount">--</div>
        <div class="field due-amount">
            {{ number_format($invoice->invoice->total_amount - $invoice->invoice->paid_amount, 2) }}</div>
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
