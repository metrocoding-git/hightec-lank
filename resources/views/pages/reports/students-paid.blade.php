@extends('layouts.app')
@section('title')
    Collection Report
@endsection
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
        </style>
    @endpush
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Collection Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Collection Report</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box mb-10">
                <a href="{{ route('reports.students-paid.export', request()->query()) }}"
                    class="primary-btn small fix-gr-bg col-12 p-2" style="margin-left:auto;">
                    <span class="ti-download pr-2"></span> Export CSV
                </a>
            </div>

            <div class="white-box p-20 mb-20">
                {{-- <div class="rp-section-header">
                    <span class="rp-icon"><i class="fa-solid fa-filter"></i></span>
                    <h3>Filter Payments</h3>
                </div> --}}
                <form method="GET" action="{{ route('reports.students-paid') }}" class="mb-0" id="filterForm">
                    <div class="row">



                        {{-- Date Range --}}
                        <div class="col-md-3">
                            <input type="text" id="date_range" name="date_range" class="form-control"
                                placeholder="Select Date Range" value="{{ request('date_range') }}">
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-md-3">
                            <select name="payment_method" class="form-control primary_select " id="payment_method">
                                <option value="">All Payment Methods</option>
                                <option value="Cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>Cash
                                </option>
                                <option value="Bank Transfer"
                                    {{ request('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer
                                </option>
                                <option value="Invoice" {{ request('payment_method') == 'Invoice' ? 'selected' : '' }}>
                                    Invoice
                                </option>
                            </select>
                        </div>

                        {{-- Branch --}}
                        <div class="col-md-3">
                            <select name="branch_id" class="form-control primary_select " id="branch_id">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Fee Type --}}
                        <div class="col-md-3">
                            <select name="payment_type" class="form-control primary_select " id="payment_type">
                                <option value="">All Payment Type</option>
                                <option value="course_fee" {{ request('payment_type') == 'course_fee' ? 'selected' : '' }}>
                                    Course & Fee
                                </option>
                                <option value="nvq" {{ request('payment_type') == 'nvq' ? 'selected' : '' }}>NVQ
                                </option>
                                <option value="item_sell" {{ request('payment_type') == 'item_sell' ? 'selected' : '' }}>
                                    Item Sell</option>
                                <option value="food" {{ request('payment_type') == 'food' ? 'selected' : '' }}>
                                    Food fee</option>
                                <option value="others" {{ request('payment_type') == 'others' ? 'selected' : '' }}>Others
                                </option>
                            </select>
                        </div>

                        {{-- Search --}}
                        <div class="col-12 mt-10">
                            <input type="text" name="search_text" class="form-control"
                                placeholder="Receipt / invoice / admission no. / student name"
                                value="{{ request('search_text') }}">
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
                                        <a href="{{ route('reports.students-paid') }}" class="text-white">Reset</a>
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>
                </form>
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
                            <div class="rp-stat-label">Net Collected (Rs.)</div>
                        </div>
                        <div class="rp-stat-card">
                            <div class="rp-stat-value">{{ number_format($count) }}</div>
                            <div class="rp-stat-label">Payments in range</div>
                        </div>
                        <div class="rp-stat-card">
                            <div class="rp-stat-value">Rs. {{ number_format($count > 0 ? $grandTotal / $count : 0, 2) }}
                            </div>
                            <div class="rp-stat-label">Average per Payment (Rs.)</div>
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

            <div class="row mt-20">
                <div class="col-lg-12">

                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">Payments Report</h3>
                                </div>
                            </div>
                            <div class="col-lg-8 d-flex align-items-center justify-content-end pr-20">
                                <small class="text-muted">Export CSV downloads every matching row — not just the page shown
                                    below.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="rp-table-wrapper">
                                    <x-table>
                                        <table id="table_id" class="table" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Stu No</th>
                                                    <th>Stu Category</th>
                                                    <th>Paid Branch</th>
                                                    <th>Invoice Number</th>
                                                    <th>Receipt Number</th>
                                                    <th>Payment Name</th>
                                                    <th>Paid Amount</th>
                                                    <th>Payment Method</th>
                                                    <th>Payment Date</th>
                                                    <th>Payment Invoice Number</th>
                                                    <th>Bank Payment Amount</th>
                                                    <th>Bank Payment Date</th>
                                                    <th>Note</th>
                                                    <th>Create User</th>
                                                    <th>Updated By</th>
                                                    <th>Invoice Payment Status</th>
                                                    <th>Invoice Payment Amount</th>
                                                    <th>Lunch Box Number</th>
                                                    <th>Created At</th>
                                                    <th>Updated At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($payments as $index => $row)
                                                    <tr>
                                                        <td>{{ $payments->firstItem() + $index }}</td>
                                                        <td>{{ $row->admission_number }}</td>
                                                        <td>{{ $row->stu_category }}</td>
                                                        <td>{{ $row->branch_name }}</td>
                                                        <td>{{ $row->invoice_number }}</td>
                                                        <td>{{ $row->receipt_number }}</td>
                                                        <td>{{ $row->payment_name }}</td>
                                                        <td class="{{ $row->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ number_format($row->amount, 2) }}</td>
                                                        <td>{{ $row->payment_method }}</td>
                                                        <td>{{ $row->payment_date ? \Carbon\Carbon::parse($row->payment_date)->format('Y-m-d h:i A') : '-' }}
                                                        </td>
                                                        <td>{{ $row->payment_invoice_number }}</td>
                                                        <td>{{ $row->bank_payment_amount ?? '-' }}</td>
                                                        <td>{{ $row->bank_payment_date ? \Carbon\Carbon::parse($row->bank_payment_date)->format('Y-m-d') : '-' }}
                                                        </td>
                                                        <td>{{ $row->notes }}</td>
                                                        <td>{{ $row->create_user }}</td>
                                                        <td>{{ $row->create_user }}</td>
                                                        <td>{{ $row->invoice_payment_status }}</td>
                                                        <td>{{ $row->invoice_payment_amount ?? '-' }}</td>
                                                        <td>{{ $row->box_number }}</td>
                                                        <td>{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('Y-m-d h:i A') : '-' }}</td>
                                                        <td>{{ $row->updated_at ? \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d h:i A') : '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="19" class="text-center">No records found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </x-table>
                                </div>
                                <div class="rp-pagination-wrapper">
                                    <div class="rp-pagination-info"> Showing
                                        <strong>{{ $payments->firstItem() ?? 0 }}</strong> –
                                        <strong>{{ $payments->lastItem() ?? 0 }}</strong> of
                                        <strong>{{ number_format($payments->total()) }}</strong> records </div>
                                    <div class="rp-pagination"> {{ $payments->links('pagination::bootstrap-4') }} </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@push('script')
    <script>
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: true,
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