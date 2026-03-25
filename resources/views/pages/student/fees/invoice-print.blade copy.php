<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            width: 210mm;
            margin: auto;
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #000;
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
            font-size: 48px;
            font-weight: bold;
            text-align: right;
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
            margin-top: 40px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-style: italic;
        }

        .note {
            margin-top: 10px;
            font-size: 12px;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <div class="header">
        <div class="box" style="width: 50%;">
            <strong>Customer Name & Address</strong><br><br>
            {{ $customer_name }}<br>
            {{ $address_line1 }}<br>
            {{ $address_line2 }}<br>
            {{ $city }}
        </div>

        <div style="width: 45%;">
            <div class="invoice-title">Invoice</div>

            <table class="info-table">
                <tr>
                    <td><strong>Date</strong></td>
                    <td><strong>Invoice No.</strong></td>
                    <td><strong>P.O. No.</strong></td>
                </tr>
                <tr>
                    <td>{{ $date }}</td>
                    <td>{{ $invoice_no }}</td>
                    <td>{{ $po_no ?? '' }}</td>
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
            @foreach($items as $item)
            <tr>
                <td>{{ number_format($item['qty'], 2) }}</td>
                <td>{{ $item['description'] }}</td>
                <td class="text-right">{{ number_format($item['rate'], 2) }}</td>
                <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach

            {{-- Extra Info --}}
            <tr>
                <td></td>
                <td colspan="3">
                    Student ID - {{ $student_id }}<br>
                    Register Number - {{ $register_number }}<br>
                    NIC - {{ $nic }}<br>
                    Student Name - {{ $student_name }}<br>
                    Training Centre - {{ $training_center }}
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
                    Payee Name : {{ $payee_name }}
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    SWIFT Address : {{ $swift }}
                </td>
            </tr>
            <tr>
                <td>Bank</td>
                <td>Branch</td>
                <td>Account Number</td>
            </tr>
            <tr>
                <td>{{ $bank }}</td>
                <td>{{ $branch }}</td>
                <td>{{ $account_number }}</td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        It's been a pleasure working with you!
    </div>

    <div class="note">
        This is a computer-generated document. No signature is required.
    </div>

</body>

</html>