@extends('layouts.app')
@section('title')
    Issue Items Report
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
                <h1>Issue Items Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Issue Items Report</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box mb-10">
                <a href="{{ route('reports.issue-items.export', request()->query()) }}"
                    class="primary-btn small fix-gr-bg col-12 p-2" style="margin-left:auto;">
                    <span class="ti-download pr-2"></span> Export CSV
                </a>
            </div>
            <div class="white-box p-20 mb-20">
                {{-- <div class="rp-section-header">
                    <span class="rp-icon"><i class="fa-solid fa-filter"></i></span>
                    <h3>Filter Issues</h3>
                </div> --}}
                <form method="GET" action="{{ route('reports.issue-items') }}" class="mb-0">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" id="date_range" name="date_range" class="form-control"
                                placeholder="Select Date Range " value="{{ request('date_range') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="issue_status" class="form-control primary_select ">
                                <option value="">All Status</option>
                                <option value="I" {{ request('issue_status') == 'I' ? 'selected' : '' }}>
                                    @lang('inventory.issued')</option>
                                <option value="R" {{ request('issue_status') == 'R' ? 'selected' : '' }}>
                                    @lang('inventory.returned')</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="course_id" class="form-control primary_select ">
                                <option value="">All Courses</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="intake_id" class="form-control primary_select ">
                                <option value="">All Intakes</option>
                                @foreach ($intakes as $intake)
                                    <option value="{{ $intake->id }}"
                                        {{ request('intake_id') == $intake->id ? 'selected' : '' }}>{{ $intake->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mt-2">
                            <input type="text" name="search_text" class="form-control"
                                placeholder="Reg no. / student name" value="{{ request('search_text') }}">
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
                                        <a href="{{ route('reports.issue-items') }}" class="text-white">Reset</a>
                                    </button>

                                </div>

                            </div>

                        </div>
                    </div>
                </form>
            </div>



            <div class="row mb-20">
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
                                                    <th>#</th>
                                                    <th>@lang('inventory.item_name')</th>
                                                    <th>@lang('inventory.item_category')</th>
                                                    <th>@lang('inventory.issue_to')</th>
                                                    <th>Reg No</th>
                                                    <th>Category</th>
                                                    <th>@lang('inventory.issue_date')</th>
                                                    <th>@lang('inventory.quantity')</th>
                                                    <th>Intake Name</th>
                                                    <th>Course Name</th>
                                                    <th>Create user</th>
                                                    <th>Course Expiration</th>
                                                    <th>@lang('common.status')</th>
                                                    <th>Created At</th>
                                                    <th>Updated At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($itemIssues as $index => $batchData)
                                                    <tr>
                                                        <td>{{ $itemIssues->firstItem() + $index }}</td>
                                                        <td>{{ $batchData->item->item_name ?? 'No Items' }}</td>
                                                        <td>{{ $batchData->item->category->name ?? '-' }}</td>
                                                        <td>{{ $batchData->batch->student->student_name ?? '-' }}</td>
                                                        <td>{{ $batchData->batch->student->admission_number ?? '-' }}</td>
                                                        <td>{{ $batchData->batch->student->studentCategory->category_name ?? '' }}
                                                        </td>
                                                        <td>{{ $batchData->batch->issue_date != '' ? date('m/d/Y', strtotime($batchData->batch->issue_date)) : '' }}
                                                        </td>
                                                        <td>{{ $batchData->quantity }}</td>
                                                        <td>{{ $batchData->batch->intake->name ?? '-' }}</td>
                                                        <td>{{ $batchData->batch->course->name ?? '-' }}</td>
                                                        <td>{{ $batchData->batch->createdBy?->name ?? '-' }}</td>
                                                        <td>
                                                            <span
                                                                class="badge {{ $batchData->expire_class }}">{{ $batchData->expire_status }}</span>
                                                        </td>
                                                        <td>
                                                            @if ($batchData->batch->issue_status == 'I')
                                                                @lang('inventory.issued')
                                                            @else
                                                                @lang('inventory.returned')
                                                            @endif
                                                        </td>
                                                        <td>{{ $batchData->batch->created_at ? $batchData->batch->created_at->format('Y-m-d h:i A') : '-' }}
                                                        </td>
                                                        <td>{{ $batchData->batch->updated_at ? $batchData->batch->updated_at->format('Y-m-d h:i A') : '-' }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="15" class="text-center">No records found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </x-table>
                                </div>
                                <div class="rp-pagination-wrapper">
                                    <div class="rp-pagination-info"> Showing
                                        <strong>{{ $itemIssues->firstItem() ?? 0 }}</strong> –
                                        <strong>{{ $itemIssues->lastItem() ?? 0 }}</strong> of
                                        <strong>{{ number_format($itemIssues->total()) }}</strong> records
                                    </div>
                                    <div class="rp-pagination"> {{ $itemIssues->links('pagination::bootstrap-4') }} </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($statusBreakdown->count() > 0 || $dailyTrend->count() > 0)
                <div class="white-box p-20 mb-20">
                    <div class="rp-section-header">
                        <span class="rp-icon"><i class="fa-solid fa-chart-pie"></i></span>
                        <h3>Analytics — Filtered Results</h3>
                    </div>
                    <div class="rp-stat-row">
                        <div class="rp-stat-card">
                            <div class="rp-stat-value">{{ number_format($itemCount) }}</div>
                            <div class="rp-stat-label">Issue records in range</div>
                        </div>
                    </div>
                    <div class="rp-chart-row">
                        <div class="rp-chart-card">
                            <h5>Issues Over Time</h5>
                            @if ($dailyTrend->count() > 0)
                                <div id="trend-chart" style="height:220px;"></div>
                            @else
                                <div class="rp-chart-empty">No issues in this range yet.</div>
                            @endif
                        </div>
                        <div class="rp-chart-card">
                            <h5>By Status</h5>
                            @if ($statusBreakdown->count() > 0)
                                <div id="status-chart" style="height:220px;"></div>
                                <div id="status-legend" class="chart-legend"></div>
                            @else
                                <div class="rp-chart-empty">No issues in this range yet.</div>
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
                        ykeys: ['count'],
                        labels: ['Issues'],
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
