@extends('layouts.app')
@section('title', 'Credit Invoice Payments')

@section('mainContent')
    @push('css')
        <style>
            .rp-section-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 18px;
            }

            .rp-icon {
                width: 40px;
                height: 40px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 17px;
                flex-shrink: 0;
                background: color-mix(in srgb, var(--gradient_1) 12%, #fff);
                color: var(--gradient_1);
            }

            .rp-section-header h3 {
                margin: 0;
                font-weight: 700;
                font-size: 18px;
            }

            .rp-stat-row {
                display: flex;
                gap: 16px;
                flex-wrap: wrap;
                margin-bottom: 20px;
            }

            .rp-stat-card {
                flex: 1 1 200px;
                padding: 16px 20px;
                border-radius: 14px;
                background: #fff;
                border: 1px solid #eceef3;
                box-shadow: 0 2px 14px rgba(20, 20, 43, 0.05);
            }

            .rp-stat-card .rp-stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1c1f2b;
                font-variant-numeric: tabular-nums;
            }

            .rp-stat-card .rp-stat-label {
                font-size: 12px;
                color: #8a8fa3;
                margin-top: 4px;
            }

            .rp-chart-row {
                display: flex;
                gap: 20px;
                flex-wrap: wrap;
                margin-bottom: 10px;
            }

            .rp-chart-card {
                flex: 1 1 340px;
                background: #fff;
                border: 1px solid #eceef3;
                border-radius: 14px;
                padding: 18px 20px;
                box-shadow: 0 2px 14px rgba(20, 20, 43, 0.05);
            }

            .rp-chart-card h5 {
                font-size: 14px;
                font-weight: 700;
                margin: 0 0 12px;
                color: #1c1f2b;
            }

            .rp-chart-empty {
                padding: 30px 10px;
                text-align: center;
                color: #9aa1ac;
                font-size: 13px;
            }

            .chart-legend {
                display: flex;
                flex-wrap: wrap;
                gap: 10px 18px;
                margin-top: 12px;
            }

            .legend-item {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                color: #565d6d;
            }

            .legend-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                display: inline-block;
            }

            .white-box {
                border-radius: 14px;
                box-shadow: 0 2px 14px rgba(20, 20, 43, 0.05);
                border: 1px solid #eceef3;
            }

            #table_id thead tr th {
                background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
                color: #fff;
                font-weight: 600;
                white-space: nowrap;
                border: none;
            }

            tr.receipt-main-row:hover {
                background: #f5f6fb;
            }

            table.child-item-table {
                width: 100%;
            }

            table.child-item-table tr.sub-header-row td {
                background: #fff3cd;
                font-weight: 600;
                font-size: 0.8em;
                color: #856404;
                padding: 4px 8px;
                border-top: 2px solid #ffc107;
            }

            table.child-item-table tr.sub-item-row td {
                background: #f8f9fa;
                font-size: 0.9em;
                border-bottom: 1px solid #e9ecef;
                padding: 4px 8px;
            }

            .items-toggle-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 7px;
                min-width: 78px;
                height: 34px;
                padding: 0 12px;
                border: 1px solid #e5e7ed;
                border-radius: 9px;
                background: #fff;
                color: var(--gradient_1);
                font-size: 12px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .items-toggle-btn i {
                font-size: 11px;
                transition: transform 0.2s ease;
            }

            .items-toggle-btn:hover {
                background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2));
                color: #fff;
                border-color: transparent;
                box-shadow: 0 4px 10px rgba(81, 80, 233, 0.20);
                transform: translateY(-1px);
            }

            .items-toggle-btn:active {
                transform: translateY(0);
            }

            .items-toggle-btn .item-count {
                font-weight: 700;
            }
        </style>
    @endpush

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Credit Invoice Payments</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Credit Invoice Payments</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box mb-10">
                <a href="{{ route('reports.invoice-payments.export', request()->query()) }}"
                    class="primary-btn small fix-gr-bg col-12 p-2" style="margin-left:auto;">
                    <span class="ti-download pr-2"></span> Export CSV
                </a>
            </div>
            <div class="white-box p-20 mb-20">
                {{-- <div class="rp-section-header">
                    <span class="rp-icon"><i class="fa-solid fa-filter"></i></span>
                    <h3>Filter Payments</h3>
                </div> --}}
                <form method="GET" action="{{ route('reports.invoice-payments') }}" id="filter-form" class="mb-0">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" placeholder="Select Date Range" id="date_range" name="date_range"
                                class="form-control" value="{{ request('date_range') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="branch_id" id="branch_id" class="form-control primary_select ">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="payment_method" id="payment_method" class="form-control primary_select ">
                                <option value="">All Payment Methods</option>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method }}"
                                        {{ request('payment_method') == $method ? 'selected' : '' }}>
                                        {{ $method }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mt-2">
                            <input type="text" id="search_text" name="search_text" class="form-control"
                                placeholder="Receipt / Invoice / Student / Reg No" value="{{ request('search_text') }}">
                        </div>
                        <div class="col-12 mt-10">
                            <div class="row align-items-center">

                                <div class="col-10">
                                    <button type="submit" class="primary-btn small fix-gr-bg col-12 p-2">
                                        <span class="ti-search pr-2"></span> Search
                                    </button>
                                </div>

                                <div class="col-2">
                                    <button class="primary-btn small fix-gr-bg col-12 p-2">
                                        <a href="{{ route('reports.invoice-payments') }}" class="text-white">Reset</a>
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>
                </form>
            </div>



            <div class="row mb-20">
                <div class="col-lg-12">
                    <div class="white-box p-20">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">Report List</h3>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="rp-table-wrapper">
                                    <x-table>
                                        <table id="table_id" class="table" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Receipt No</th>
                                                    <th>Created Date</th>
                                                    <th>Cheque No</th>
                                                    <th>Total Amount</th>
                                                    <th>Payment Method(s)</th>
                                                    <th>Note</th>
                                                    <th>Created By</th>
                                                    <th>Inv. Payment No</th>
                                                    <th>Student Name</th>
                                                    <th>Reg. No</th>
                                                    <th>Branch</th>
                                                    <th>Updated At</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($receipts as $receipt)
                                                    @php
                                                        $firstPayment = $receipt->items->first()?->payment;
                                                        $invoice = $firstPayment?->invoice;
                                                        $student = $invoice?->student;
                                                        $branch = $invoice?->branch;
                                                        $methods = $receipt->items
                                                            ->pluck('payment.payment_method')
                                                            ->filter()
                                                            ->unique()
                                                            ->values()
                                                            ->implode(', ');
                                                        $createdBy =
                                                            trim(
                                                                ($receipt->user->first_name ?? '') .
                                                                    ' ' .
                                                                    ($receipt->user->last_name ?? ''),
                                                            ) ?:
                                                            'N/A';
                                                    @endphp
                                                    <tr class="receipt-main-row">
                                                        <td>{{ $receipts->firstItem() + $loop->index }}</td>
                                                        <td>{{ $receipt->receipt_number }}</td>
                                                        <td>{{ optional($receipt->created_at)->format('Y-m-d h:i A') }}
                                                        </td>
                                                        <td>{{ $receipt->cheque_number ?? '-' }}</td>
                                                        <td>{{ number_format((float) $receipt->total_amount, 2) }}</td>
                                                        <td>{{ $methods ?: '-' }}</td>
                                                        <td>{{ $receipt->note ?? '-' }}</td>
                                                        <td>{{ $createdBy }}</td>
                                                        <td>{{ $firstPayment->payment_invoice_number ?? '-' }}</td>
                                                        <td>{{ $student->student_name ?? '-' }}</td>
                                                        <td>{{ $student->admission_number ?? '-' }}</td>
                                                        <td>{{ $branch->name ?? '-' }}</td>
                                                        <td>{{ optional($receipt->updated_at)->format('Y-m-d h:i A') ?? '-' }}</td>
                                                        <td>
                                                            <button type="button" class="items-toggle-btn toggle-items"
                                                                data-receipt-id="{{ $receipt->id }}"> <i
                                                                    class="ti-angle-down"></i> <span class="item-count">
                                                                    {{ $receipt->items_count }} </span> <span>Items</span>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr class="child-item-row" data-child-for="{{ $receipt->id }}"
                                                        style="display:none;">
                                                        <td colspan="14"></td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="14" class="text-center">No records found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </x-table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-20">
                                    <small class="text-muted">
                                        Showing {{ $receipts->firstItem() ?? 0 }}–{{ $receipts->lastItem() ?? 0 }}
                                        of {{ number_format($receipts->total()) }}
                                    </small>
                                    {{ $receipts->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                        @if ($methodBreakdown->count() > 0 || $dailyTrend->count() > 0)
                <div class="white-box p-20 mb-20">
                    <div class="rp-section-header">
                        <span class="rp-icon"><i class="fa-solid fa-chart-pie"></i></span>
                        <h3>Analytics — Filtered Results</h3>
                    </div>
                    <div class="rp-stat-row">
                        <div class="rp-stat-card">
                            <div class="rp-stat-value">Rs. {{ number_format($grandTotal, 2) }}</div>
                            <div class="rp-stat-label">Total Collected (Rs.)</div>
                        </div>
                        <div class="rp-stat-card">
                            <div class="rp-stat-value">{{ number_format($receiptCount) }}</div>
                            <div class="rp-stat-label">Receipts in range</div>
                        </div>
                        <div class="rp-stat-card">
                            <div class="rp-stat-value">Rs.
                                {{ number_format($receiptCount > 0 ? $grandTotal / $receiptCount : 0, 2) }}</div>
                            <div class="rp-stat-label">Average per Receipt (Rs.)</div>
                        </div>
                    </div>
                    <div class="rp-chart-row">
                        <div class="rp-chart-card">
                            <h5>Collection Trend</h5>
                            @if ($dailyTrend->count() > 0)
                                <div id="trend-chart" style="height:220px;"></div>
                            @else
                                <div class="rp-chart-empty">No payments in this range yet.</div>
                            @endif
                        </div>
                        <div class="rp-chart-card">
                            <h5>By Payment Method</h5>
                            @if ($methodBreakdown->count() > 0)
                                <div id="method-chart" style="height:220px;"></div>
                                <div id="method-legend" class="chart-legend"></div>
                            @else
                                <div class="rp-chart-empty">No payments in this range yet.</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@include('components.partials.date_picker_css_js')

@push('script')
    <script>
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: true,
        });

        $(document).ready(function() {

            function formatChild(items) {
                let html = '<table class="child-item-table">';
                html +=
                    '<tr class="sub-header-row"><td>Item</td><td>Payment Receipt No</td><td>Main Invoice No</td><td>Paid Amount</td><td>Inv. Payment No</td><td>Method</td></tr>';
                items.forEach((item) => {
                    html += `<tr class="sub-item-row">
                        <td>#${item.item_no}</td>
                        <td>${item.receipt_no}</td>
                        <td>${item.main_invoice_no}</td>
                        <td><strong>${item.paid_amount}</strong></td>
                        <td>${item.inv_payment_no}</td>
                        <td>${item.payment_method}</td>
                    </tr>`;
                });
                html += '</table>';
                return html;
            }

            $('#table_id tbody').on('click', '.toggle-items', function(e) {
                e.stopPropagation();
                var button = $(this);
                var receiptId = button.data('receipt-id');
                var childRow = $('tr[data-child-for="' + receiptId + '"]');
                var childTd = childRow.find('td');
                var icon = button.find('i');

                if (childRow.is(':visible')) {
                    childRow.hide();
                    icon.removeClass('ti-angle-up').addClass('ti-angle-down');
                    return;
                }

                if (childTd.data('loaded')) {
                    childRow.show();
                    icon.removeClass('ti-angle-down').addClass('ti-angle-up');
                    return;
                }

                var itemsUrl = "{{ url('reports/invoice-payments') }}/" + receiptId + "/items";
                $.getJSON(itemsUrl, function(resp) {
                    childTd.html(formatChild(resp.items));
                    childTd.data('loaded', true);
                    childRow.show();
                    icon.removeClass('ti-angle-down').addClass('ti-angle-up');
                }).fail(function() {
                    childTd.html('<span class="text-danger">Failed to load items.</span>');
                    childRow.show();
                });
            });

        });
    </script>
@endpush

@if ($methodBreakdown->count() > 0 || $dailyTrend->count() > 0)
    @push('script')
        <script>
            (function() {
                var gradient1 = getComputedStyle(document.documentElement).getPropertyValue('--gradient_1').trim() ||
                    '#5150e9';
                var gradient2 = getComputedStyle(document.documentElement).getPropertyValue('--gradient_2').trim() ||
                    '#a55eea';
                var methodPalette = [gradient1, gradient2, '#2ecc71', '#f39c12', '#e74c3c', '#3498db'];

                @if ($dailyTrend->count() > 0)
                    Morris.Line({
                        element: 'trend-chart',
                        data: @json($dailyTrend),
                        xkey: 'date',
                        ykeys: ['amount'],
                        labels: ['Collected (Rs.)'],
                        lineColors: [gradient1],
                        resize: true,
                        fillOpacity: 0.15,
                        hideHover: 'auto',
                        parseTime: false,
                    });
                @endif

                @if ($methodBreakdown->count() > 0)
                    Morris.Donut({
                        element: 'method-chart',
                        data: @json($methodBreakdown),
                        colors: methodPalette,
                        resize: true,
                        formatter: function(y) {
                            return 'Rs. ' + Number(y).toLocaleString('en-US', {
                                minimumFractionDigits: 2
                            });
                        },
                    });
                    var legendData = @json($methodBreakdown);
                    var legendHtml = '';
                    legendData.forEach(function(row, i) {
                        legendHtml += '<span class="legend-item"><span class="legend-dot" style="background:' +
                            methodPalette[i % methodPalette.length] + '"></span>' + row.label + '</span>';
                    });
                    document.getElementById('method-legend').innerHTML = legendHtml;
                @endif
            })();
        </script>
    @endpush
@endif