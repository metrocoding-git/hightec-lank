<!DOCTYPE html>
<html>

<head>
    <title>Invoice Print</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        @page {
            size: A4;
            margin: 14mm;
        }

        :root {
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #d1d5db;
            --soft: #f8fafc;
            --brand: #0f4c81;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #eef2f7;
            color: var(--ink);
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            font-size: 13px;
            line-height: 1.45;
        }

        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 14px auto;
            background: #fff;
            padding: 14mm;
            border: 1px solid #e5e7eb;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 2px solid var(--line);
            padding-bottom: 12px;
        }

        .company h1 {
            margin: 0;
            font-size: 20px;
            color: var(--brand);
            letter-spacing: 0.4px;
        }

        .company p {
            margin: 3px 0 0;
            color: var(--muted);
            font-size: 12px;
        }

        .doc-title {
            text-align: right;
        }

        .doc-title h2 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }

        .doc-title p {
            margin: 2px 0;
            color: var(--muted);
            font-size: 12px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
        }

        .card {
            border: 1px solid var(--line);
            background: var(--soft);
            border-radius: 6px;
            padding: 10px;
        }

        .card h3 {
            margin: 0 0 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--muted);
        }

        .kv {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin: 4px 0;
        }

        .kv .k {
            color: var(--muted);
        }

        .kv .v {
            font-weight: 600;
            text-align: right;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        .table th,
        .table td {
            border: 1px solid var(--line);
            padding: 8px 10px;
            vertical-align: top;
        }

        .table th {
            background: #f1f5f9;
            text-align: left;
            font-size: 12px;
            color: #334155;
        }

        .num {
            text-align: right;
            white-space: nowrap;
        }

        .summary-wrap {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
        }

        .summary {
            width: 320px;
            border: 1px solid var(--line);
            border-radius: 6px;
            overflow: hidden;
        }

        .summary .row {
            display: flex;
            justify-content: space-between;
            padding: 8px 10px;
            border-bottom: 1px solid var(--line);
            background: #fff;
        }

        .summary .row:last-child {
            border-bottom: 0;
            font-size: 15px;
            font-weight: 700;
            background: #ecf5ff;
        }

        .notice {
            margin-top: 16px;
            padding: 10px;
            border: 1px dashed #9ca3af;
            border-radius: 6px;
            color: #374151;
            font-size: 12px;
        }

        .footer {
            margin-top: 20px;
            border-top: 1px solid var(--line);
            padding-top: 8px;
            color: var(--muted);
            font-size: 11px;
            text-align: center;
        }

        @media print {
            body {
                background: #fff;
            }

            .sheet {
                margin: 0;
                border: 0;
                width: auto;
                min-height: auto;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    @php
        $baseInvoice = $invoice->invoice;
        $student = optional($baseInvoice)->student;
        $studentCategory = optional(optional($student)->studentCategory)->category_name ?? 'N/A';

        $invoiceType = optional($baseInvoice)->invoice_type;
        $paidCourseName = optional(optional(optional($baseInvoice)->paidCourses)->course)->name;
        $feeName = optional(optional(optional($baseInvoice)->invoice_item)->first())->fee_type->name ?? null;

        $itemName = $invoiceType === 'fee'
            ? ($feeName ?? 'Fee')
            : ($invoiceType === 'course'
                ? ($paidCourseName ?? 'Course')
                : ($invoiceType === 'nvq'
                    ? 'NVQ - ' . ($paidCourseName ?? 'Course')
                    : ($invoiceType ?? '-')));

        $totalAmount = (float) ($invoice->amount ?? 0);
        $paidAmount = (float) (optional($baseInvoice)->paid_amount ?? 0);
        $dueAmount = (float) ((optional($baseInvoice)->total_amount ?? 0) - $paidAmount);
        $dueDateRaw = optional($baseInvoice)->due_date;
        $dueDate = $dueDateRaw ? \Carbon\Carbon::parse($dueDateRaw)->format('d M Y') : 'N/A';
    @endphp

    <div class="sheet">
        <div class="top-bar">
            <div class="company">
                <h1>HIGHTEC LANKA INTERNATIONAL</h1>
                <p>Vocational and Technical Training Institute</p>
                <p>Student Fee Receipt</p>
            </div>
            <div class="doc-title">
                <h2>INVOICE</h2>
                <p>Receipt No: {{ $invoice->receipt_number ?? 'N/A' }}</p>
                <p>Date: {{ \Carbon\Carbon::parse($invoice->create_date)->format('d M Y') }}</p>
            </div>
        </div>

        <div class="meta-grid">
            <div class="card">
                <h3>Student Details</h3>
                <div class="kv"><span class="k">Student Name</span><span class="v">{{ optional($student)->student_name ?? 'N/A' }}</span></div>
                <div class="kv"><span class="k">Admission No</span><span class="v">{{ optional($student)->admission_number ?? 'N/A' }}</span></div>
                <div class="kv"><span class="k">NIC</span><span class="v">{{ optional($student)->national_id_number ?? 'N/A' }}</span></div>
                <div class="kv"><span class="k">Category</span><span class="v">{{ $studentCategory }}</span></div>
                <div class="kv"><span class="k">Contact</span><span class="v">{{ optional($student)->phone_number ?? 'N/A' }}</span></div>
            </div>
            <div class="card">
                <h3>Invoice Details</h3>
                <div class="kv"><span class="k">Invoice Type</span><span class="v">{{ strtoupper($invoiceType ?? 'N/A') }}</span></div>
                <div class="kv"><span class="k">Description</span><span class="v">{{ $itemName }}</span></div>
                <div class="kv"><span class="k">Due Date</span><span class="v">{{ $dueDate }}</span></div>
                <div class="kv"><span class="k">Payment Method</span><span class="v">{{ $invoice->payment_method ?? 'N/A' }}</span></div>
                <div class="kv"><span class="k">Branch</span><span class="v">{{ optional(optional($baseInvoice)->branch)->name ?? 'N/A' }}</span></div>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th style="width:10%;">Qty</th>
                    <th>Description</th>
                    <th style="width:18%;" class="num">Rate (LKR)</th>
                    <th style="width:18%;" class="num">Amount (LKR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1.00</td>
                    <td>{{ $itemName }}</td>
                    <td class="num">{{ number_format($totalAmount, 2) }}</td>
                    <td class="num">{{ number_format($totalAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="summary-wrap">
            <div class="summary">
                <div class="row"><span>Invoice Amount</span><span>LKR {{ number_format($totalAmount, 2) }}</span></div>
                <div class="row"><span>Paid So Far</span><span>LKR {{ number_format($paidAmount, 2) }}</span></div>
                <div class="row"><span>Outstanding</span><span>LKR {{ number_format(max($dueAmount, 0), 2) }}</span></div>
            </div>
        </div>

        @if (!empty($invoice->invoice_payment_note))
            <div class="notice">
                <strong>Note:</strong> {{ $invoice->invoice_payment_note }}
            </div>
        @endif

        <div class="footer">
            This is a computer-generated receipt. No signature is required.
        </div>
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
