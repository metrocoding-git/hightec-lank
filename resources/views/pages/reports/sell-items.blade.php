@extends('layouts.app')
@section('title')
    Sell Items Report
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
                <h1>Sell Items Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Sell Items Report</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box mb-10">
                <a href="{{ route('reports.sell-items.export', request()->query()) }}"
                    class="primary-btn small fix-gr-bg col-12 p-2" style="margin-left:auto;">
                    <span class="ti-download pr-2"></span> Export CSV
                </a>
            </div>
            <div class="white-box p-20 mb-20">
                {{-- <div class="rp-section-header">
                    <span class="rp-icon"><i class="fa-solid fa-filter"></i></span>
                    <h3>Filter Sales</h3>
                    <a href="{{ route('reports.sell-items.export', request()->query()) }}"
                        class="primary-btn small fix-gr-bg" style="margin-left:auto;">
                        <span class="ti-download pr-2"></span> Export CSV
                    </a>
                </div> --}}
                <form method="GET" action="{{ route('reports.sell-items') }}" class="mb-0">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" id="date_range" name="date_range" placeholder="Date Range"
                                class="form-control" value="{{ request('date_range') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="payment_method" class="form-control primary_select ">
                                <option value="">All Payment Method</option>
                                <option value="Cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>Cash
                                </option>
                                <option value="Bank Transfer"
                                    {{ request('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer
                                </option>
                                <option value="Invoice" {{ request('payment_method') == 'Invoice' ? 'selected' : '' }}>
                                    Invoice</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-control primary_select ">
                                <option value="">All Status</option>
                                <option value="P" {{ request('status') == 'P' ? 'selected' : '' }}>@lang('inventory.paid')
                                </option>
                                <option value="PP" {{ request('status') == 'PP' ? 'selected' : '' }}>@lang('inventory.partial')
                                </option>
                                <option value="U" {{ request('status') == 'U' ? 'selected' : '' }}>@lang('inventory.unpaid')
                                </option>
                                <option value="R" {{ request('status') == 'R' ? 'selected' : '' }}>@lang('inventory.returned')
                                </option>
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <input type="text" name="search_text" class="form-control"
                                placeholder="Search Reference / buyer / reg no / invoice no."
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
                                        <a href="{{ route('reports.sell-items') }}" class="text-white">Reset</a>
                                    </button>

                                </div>

                            </div>

                        </div>


                    </div>
                </form>
            </div>

            @if ($statusBreakdown->count() > 0 || $dailyTrend->count() > 0)
                <div class="white-box p-20 mb-20">
                    <div class="rp-section-header">
                        <span class="rp-icon"><i class="fa-solid fa-chart-pie"></i></span>
                        <h3>Analytics — Filtered Results</h3>
                    </div>
                    <div class="rp-stat-row">
                        <div class="rp-stat-card">
                            <div class="rp-stat-value">Rs. {{ number_format($grandTotal, 2) }}</div>
                            <div class="rp-stat-label">Total Sales (Rs.)</div>
                        </div>
                        <div class="rp-stat-card">
                            <div class="rp-stat-value">{{ number_format($itemCount) }}</div>
                            <div class="rp-stat-label">Line items in range</div>
                        </div>
                        <div class="rp-stat-card">
                            <div class="rp-stat-value">Rs.
                                {{ number_format($itemCount > 0 ? $grandTotal / $itemCount : 0, 2) }}</div>
                            <div class="rp-stat-label">Average per Line Item (Rs.)</div>
                        </div>
                    </div>
                    <div class="rp-chart-row">
                        <div class="rp-chart-card">
                            <h5>Sales Trend</h5>
                            @if ($dailyTrend->count() > 0)
                                <div id="trend-chart" style="height:220px;"></div>
                            @else
                                <div class="rp-chart-empty">No sales in this range yet.</div>
                            @endif
                        </div>
                        <div class="rp-chart-card">
                            <h5>By Status</h5>
                            @if ($statusBreakdown->count() > 0)
                                <div id="status-chart" style="height:220px;"></div>
                                <div id="status-legend" class="chart-legend"></div>
                            @else
                                <div class="rp-chart-empty">No sales in this range yet.</div>
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
                                                    <th>@lang('common.sl')</th>
                                                    <th>@lang('inventory.reference_no')</th>
                                                    <th>Item</th>
                                                    <th>Item Category</th>
                                                    <th>@lang('inventory.buyer_name')</th>
                                                    <th>Create User</th>
                                                    <th>Reg No</th>
                                                    <th>Stu Category</th>
                                                    <th>@lang('common.date')</th>
                                                    <th>Sell Price</th>
                                                    <th>@lang('inventory.total_quantity')</th>
                                                    <th>@lang('inventory.grand_total')</th>
                                                    <th>Total Price</th>
                                                    <th>Total Qty</th>
                                                    <th>Payment Method</th>
                                                    <th>Bank Payment Amount</th>
                                                    <th>Bank Payment Date</th>
                                                    <th>Bank Payment Doc</th>
                                                    <th>Invoice Number</th>
                                                    <th>Note</th>
                                                    <th>@lang('common.status')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($itemSells as $index => $value)
                                                    <tr>
                                                        <td>{{ $itemSells->firstItem() + $index }}</td>
                                                        <td>{{ $value->itemSellBatch->reference_no }}</td>
                                                        <td>{{ $value->item->item_name }}</td>
                                                        <td>{{ $value->item->category->name ?? '-' }}</td>
                                                        <td>
                                                            @if ($value->itemSellBatch->role_id == 2 && $value->itemSellBatch->student)
                                                                {{ $value->itemSellBatch->student->student_name }}
                                                            @elseif ($value->itemSellBatch->role_id == 0 && $value->itemSellBatch->others_name)
                                                                {{ $value->itemSellBatch->others_name }}
                                                            @elseif ($value->itemSellBatch->role_id == 3 && $value->itemSellBatch->user)
                                                                {{ $value->itemSellBatch->user->name }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $value->itemSellBatch->createdBy?->name ?? '-' }}</td>
                                                        <td>
                                                            @if ($value->itemSellBatch->student)
                                                                {{ $value->itemSellBatch->student->admission_number }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($value->itemSellBatch->student)
                                                                {{ $value->itemSellBatch->student->studentCategory?->category_name ?? '-' }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $value->itemSellBatch->sell_date ? \Carbon\Carbon::parse($value->itemSellBatch->sell_date)->format('m/d/Y') : '' }}
                                                        </td>
                                                        <td>{{ number_format((float) $value->unit_price, 2, '.', '') }}
                                                        </td>
                                                        <td>{{ $value->quantity }}</td>
                                                        <td>{{ number_format((float) $value->sub_total, 2, '.', '') }}</td>
                                                        <td>{{ number_format((float) $value->itemSellBatch->total_sell_price, 2, '.', '') }}
                                                        </td>
                                                        <td>{{ $value->itemSellBatch->total_quantity }}</td>
                                                        <td>{{ $value->itemSellBatch->payment_method ?? '-' }}</td>
                                                        <td>{{ $value->itemSellBatch->bank_payment_amount ? number_format((float) $value->itemSellBatch->bank_payment_amount, 2, '.', '') : '-' }}
                                                        </td>
                                                        <td>{{ $value->itemSellBatch->bank_payment_date ? \Carbon\Carbon::parse($value->itemSellBatch->bank_payment_date)->format('m/d/Y') : '-' }}
                                                        </td>
                                                        <td>
                                                            @if ($value->itemSellBatch->bank_payment_document)
                                                                <a href="{{ asset('storage/' . $value->itemSellBatch->bank_payment_document) }}"
                                                                    target="_blank" class="text-primary">View Document</a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($value->itemSellBatch->invoice_number && $value->itemSellBatch->invoice_number != '')
                                                                {{ $value->itemSellBatch->invoice_number }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $value->itemSellBatch->description ?? '-' }}</td>
                                                        <td>
                                                            @if ($value->itemSellBatch->status == 'P')
                                                                @lang('inventory.paid')
                                                            @elseif($value->itemSellBatch->status == 'PP')
                                                                @lang('inventory.partial')
                                                            @elseif($value->itemSellBatch->status == 'U')
                                                                @lang('inventory.unpaid')
                                                            @elseif($value->itemSellBatch->status == 'R')
                                                                @lang('inventory.returned')
                                                            @else
                                                                @lang('inventory.N/A')
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="20" class="text-center">No records found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </x-table>
                                </div>
                                <div class="rp-pagination-wrapper">
                                    <div class="rp-pagination-info"> Showing
                                        <strong>{{ $itemSells->firstItem() ?? 0 }}</strong> –
                                        <strong>{{ $itemSells->lastItem() ?? 0 }}</strong> of
                                        <strong>{{ number_format($itemSells->total()) }}</strong> records </div>
                                    <div class="rp-pagination"> {{ $itemSells->links('pagination::bootstrap-4') }} </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
    </script>
@endpush

@if ($statusBreakdown->count() > 0 || $dailyTrend->count() > 0)
    @push('script')
        <script>
            (function() {
                var gradient1 = getComputedStyle(document.documentElement).getPropertyValue('--gradient_1').trim() ||
                    '#5150e9';
                var gradient2 = getComputedStyle(document.documentElement).getPropertyValue('--gradient_2').trim() ||
                    '#a55eea';
                var palette = [gradient1, gradient2, '#2ecc71', '#f39c12', '#e74c3c'];

                @if ($dailyTrend->count() > 0)
                    Morris.Line({
                        element: 'trend-chart',
                        data: @json($dailyTrend),
                        xkey: 'date',
                        ykeys: ['amount'],
                        labels: ['Sales (Rs.)'],
                        lineColors: [gradient1],
                        resize: true,
                        fillOpacity: 0.15,
                        hideHover: 'auto',
                        parseTime: false,
                    });
                @endif

                @if ($statusBreakdown->count() > 0)
                    Morris.Donut({
                        element: 'status-chart',
                        data: @json($statusBreakdown),
                        colors: palette,
                        resize: true,
                        formatter: function(y) {
                            return 'Rs. ' + Number(y).toLocaleString('en-US', {
                                minimumFractionDigits: 2
                            });
                        },
                    });
                    var legendData = @json($statusBreakdown);
                    var legendHtml = '';
                    legendData.forEach(function(row, i) {
                        legendHtml += '<span class="legend-item"><span class="legend-dot" style="background:' +
                            palette[i % palette.length] + '"></span>' + row.label + '</span>';
                    });
                    document.getElementById('status-legend').innerHTML = legendHtml;
                @endif
            })();
        </script>
    @endpush
@endif
