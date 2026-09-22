@extends('layouts.app')
@section('title', 'Change Course Report')

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
                <h1>Change Course Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">Change Course</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box mb-10">
                <a href="{{ route('reports.change-course.export', request()->query()) }}"
                    class="primary-btn small fix-gr-bg col-12 p-2" style="margin-left:auto;">
                    <span class="ti-download pr-2"></span> Export CSV
                </a>
            </div>
            <div class="white-box p-20 mb-20">
                {{-- <div class="rp-section-header">
                    <span class="rp-icon"><i class="fa-solid fa-filter"></i></span>
                    <h3>Filter</h3>
                </div> --}}
                <form method="GET" action="{{ route('reports.change-course') }}" class="mb-0">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" placeholder="Select Date Range" id="date_range" name="date_range"
                                class="form-control" value="{{ request('date_range') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="branch_id" class="form-control primary_select ">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="new_course_id" class="form-control primary_select ">
                                <option value="">All Courses</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ request('new_course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}
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
                                        <a href="{{ route('reports.change-course') }}" class="text-white">Reset</a>
                                    </button>

                                </div>

                            </div>

                        </div>
                    </div>
                </form>
            </div>

            @if ($courseBreakdown->count() > 0 || $dailyTrend->count() > 0)
                <div class="white-box p-20 mb-20">
                    <div class="rp-section-header">
                        <span class="rp-icon"><i class="fa-solid fa-chart-pie"></i></span>
                        <h3>Analytics — Filtered Results</h3>
                    </div>
                    <div class="rp-stat-row">
                        <div class="rp-stat-card">
                            <div class="rp-stat-value">{{ number_format($changeCount) }}</div>
                            <div class="rp-stat-label">Course changes in range</div>
                        </div>
                    </div>
                    <div class="rp-chart-row">
                        <div class="rp-chart-card">
                            <h5>Changes Over Time</h5>
                            @if ($dailyTrend->count() > 0)
                                <div id="trend-chart" style="height:220px;"></div>
                            @else
                                <div class="rp-chart-empty">No changes in this range yet.</div>
                            @endif
                        </div>
                        <div class="rp-chart-card">
                            <h5>Top New Courses</h5>
                            @if ($courseBreakdown->count() > 0)
                                <div id="course-chart" style="height:220px;"></div>
                                <div id="course-legend" class="chart-legend"></div>
                            @else
                                <div class="rp-chart-empty">No changes in this range yet.</div>
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
                                                    <th>#</th>
                                                    <th>Student No</th>
                                                    <th>Student Name</th>
                                                    <th>Old Course</th>
                                                    <th>New Course</th>
                                                    <th>Intake</th>
                                                    <th>Branch</th>
                                                    <th>Changed By</th>
                                                    <th>Changed Date</th>
                                                    <th>Created At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($data as $index => $row)
                                                    <tr>
                                                        <td>{{ $data->firstItem() + $index }}</td>
                                                        <td>{{ $row->admission_number }}</td>
                                                        <td>{{ $row->student_name }}</td>
                                                        <td>{{ $row->old_course_name }}</td>
                                                        <td>{{ $row->new_course_name }}</td>
                                                        <td>{{ $row->intake_name }}</td>
                                                        <td>{{ $row->branch_name ?? '-' }}</td>
                                                        <td>{{ $row->changed_by ?? '-' }}</td>
                                                        <td>{{ $row->changed_date ? \Carbon\Carbon::parse($row->changed_date)->format('Y-m-d') : '-' }}
                                                        </td>
                                                        <td>{{ $row->created_date ? \Carbon\Carbon::parse($row->created_date)->format('Y-m-d h:i A') : '-' }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center">No records found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </x-table>
                                </div>
                                <div class="rp-pagination-wrapper">
                                    <div class="rp-pagination-info"> Showing
                                        <strong>{{ $data->firstItem() ?? 0 }}</strong> –
                                        <strong>{{ $data->lastItem() ?? 0 }}</strong> of
                                        <strong>{{ number_format($data->total()) }}</strong> records
                                    </div>
                                    <div class="rp-pagination"> {{ $data->links('pagination::bootstrap-4') }} </div>
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

@if ($courseBreakdown->count() > 0 || $dailyTrend->count() > 0)
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
                        labels: ['Changes'],
                        lineColors: [gradient1],
                        resize: true,
                        fillOpacity: 0.15,
                        hideHover: 'auto',
                        parseTime: false,
                    });
                @endif

                @if ($courseBreakdown->count() > 0)
                    Morris.Donut({
                        element: 'course-chart',
                        data: @json($courseBreakdown),
                        colors: palette,
                        resize: true,
                    });
                    var legendData = @json($courseBreakdown);
                    var legendHtml = '';
                    legendData.forEach(function(row, i) {
                        legendHtml += '<span class="legend-item"><span class="legend-dot" style="background:' +
                            palette[i % palette.length] + '"></span>' + row.label + '</span>';
                    });
                    document.getElementById('course-legend').innerHTML = legendHtml;
                @endif
            })();
        </script>
    @endpush
@endif