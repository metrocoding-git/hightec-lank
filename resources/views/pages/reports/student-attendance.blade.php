@extends('layouts.app')
@section('title')
    Students Attendance Report
@endsection

@section('mainContent')
    <style>
        .summary-cards {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .summary-card {
            flex: 1 1 200px;
            padding: 16px 18px;
            border-radius: 12px;
            background: #f5f5ff;
            border: 1px solid #e9ecf2;
        }

        .summary-card .summary-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--tp-theme-primary, #5150e9);
        }

        .summary-card .summary-label {
            font-size: 12px;
            color: #8a8fa3;
            margin-top: 2px;
        }

        .status-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-pill.pill-out {
            background: #ffe6bf;
            color: #a25a00;
        }

        .status-pill.pill-in {
            background: #d3f5df;
            color: #1a7f45;
        }

                    #table_id thead tr th {
                background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
                color: #fff;
                font-weight: 600;
                white-space: nowrap;
                border: none;
            }

        /* Analytics section (new) */
        .analytics-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .analytics-card-header .title-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: color-mix(in srgb, var(--gradient_1) 12%, #fff);
            color: var(--gradient_1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .analytics-subtitle {
            font-size: 12px;
            color: #8a8fa3;
            display: block;
            margin-top: 2px;
        }

        .chart-subtitle {
            font-size: 13px;
            font-weight: 600;
            color: #4a4f63;
            margin-bottom: 10px;
        }

        .analytics-empty {
            padding: 30px;
            text-align: center;
            color: #8a8fa3;
            font-size: 13px;
        }

        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 16px;
            margin-top: 12px;
            justify-content: center;
        }

        .chart-legend .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #4a4f63;
        }

        .chart-legend .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
    </style>

    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Students Attendance Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Students Attendance Report</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            {{-- Summary --}}


            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box mb-10">
                        <a href="{{ route('reports.student-attendance.export', request()->query()) }}"
                            class="primary-btn small fix-gr-bg col-12 p-2" style="margin-left:auto;">
                            <span class="ti-download pr-2"></span> Export CSV
                        </a>
                    </div>

                    <div class="white-box">
                        <form method="GET" action="{{ route('reports.student-attendance') }}">
                            <div class="row">

                                <div class="col-lg-5 mb-2">
                                    <div class="primary_input">
                                        <input type="date" name="date_from" placeholder="From Date"
                                            class="primary_input_field form-control" value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-lg-5 mb-2">
                                    <div class="primary_input">
                                        <input type="date" name="date_to" placeholder="To Date"
                                            class="primary_input_field form-control" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-2">
                                    <div class="primary_input">
                                        <select name="status" class="primary_select form-control">
                                            <option value="">All Status</option>
                                            <option value="in_progress"
                                                {{ request('status') == 'in_progress' ? 'selected' : '' }}>Still checked in
                                            </option>
                                            <option value="completed"
                                                {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="primary_input">
                                        <select name="branch_id" class="primary_select form-control">
                                            <option value="">All Branches</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="primary_input">
                                        <select name="intake_id" class="primary_select form-control">
                                            <option value="">All Intakes</option>
                                            @foreach ($intakes as $intake)
                                                <option value="{{ $intake->id }}"
                                                    {{ request('intake_id') == $intake->id ? 'selected' : '' }}>
                                                    {{ $intake->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-lg-3 mb-2">
                                    <div class="primary_input">
                                        <select name="course_id" class="primary_select form-control">
                                            <option value="">All Courses</option>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->id }}"
                                                    {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                                    {{ $course->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="primary_input">
                                        <select name="subject_id" class="primary_select form-control">
                                            <option value="">All Subjects</option>
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->id }}"
                                                    {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                                    {{ $subject->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-4">
                                    <div class="primary_input">
                                        <select name="type" class="primary_select form-control">
                                            <option value="">All Types</option>
                                            <option value="course" {{ request('type') == 'course' ? 'selected' : '' }}>
                                                Course</option>
                                            <option value="subject" {{ request('type') == 'subject' ? 'selected' : '' }}>
                                                Subject</option>
                                            <option value="practical"
                                                {{ request('type') == 'practical' ? 'selected' : '' }}>Practical</option>
                                            <option value="before_checking"
                                                {{ request('type') == 'before_checking' ? 'selected' : '' }}>Before
                                                Checking</option>
                                            <option value="after_checking"
                                                {{ request('type') == 'after_checking' ? 'selected' : '' }}>After Checking
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-4">
                                    <div class="primary_input">
                                        <select name="vehicle_id" class="primary_select form-control">
                                            <option value="">All Vehicles</option>
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}"
                                                    {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                                    {{ $vehicle->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-4">
                                    <div class="primary_input">
                                        <select name="marked_in_by" class="primary_select form-control">
                                            <option value="">In User</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ request('marked_in_by') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-4">
                                    <div class="primary_input">
                                        <select name="marked_out_by" class="primary_select form-control">
                                            <option value="">Out User</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ request('marked_out_by') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div class="primary_input">
                                        <input type="text" name="student_name" class="primary_input_field form-control"
                                            value="{{ request('student_name') }}" placeholder="Student name">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="primary_input">
                                        <input type="text" name="admission_number"
                                            class="primary_input_field form-control"
                                            value="{{ request('admission_number') }}"
                                            placeholder="Admission / register no.">
                                    </div>
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
                                                <a href="{{ route('reports.student-attendance') }}"
                                                    class="text-white">Reset</a>
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>
                        </form>
                    </div>

                    <div class="white-box mt-20">
                        <div class="summary-cards">
                            <div class="summary-card">
                                <div class="summary-value">{{ number_format($totalCount) }}</div>
                                <div class="summary-label">Sessions matching filter</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-value">{{ number_format($inProgressCount) }}</div>
                                <div class="summary-label">Still checked in (no time out yet)</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-value">{{ intdiv($totalMinutes, 60) }}h {{ $totalMinutes % 60 }}m
                                </div>
                                <div class="summary-label">Total duration (completed sessions)</div>
                            </div>
                        </div>
                    </div>

                    {{-- Analytics (new) --}}
                    <div class="white-box mt-20">
                        <div class="row">
                            <div class="col-lg-12 no-gutters">
                                <div class="main-title analytics-card-header">
                                    <span class="title-icon"><i class="ti-bar-chart"></i></span>
                                    <div>
                                        <h3 class="mb-0">Analytics &mdash; Filtered Results</h3>
                                        <span class="analytics-subtitle">Based on the {{ number_format($totalCount) }} session(s) matching your current filters</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($typeBreakdown->isEmpty() && $dailyTrend->isEmpty())
                            <div class="analytics-empty">No attendance data to chart for the current filters.</div>
                        @else
                            <div class="row mt-15">
                                <div class="col-lg-5 mb-4">
                                    <h4 class="chart-subtitle">Sessions by Type</h4>
                                    @if ($typeBreakdown->isEmpty())
                                        <div class="analytics-empty">No data.</div>
                                    @else
                                        <div id="attendanceTypeChart" style="height:220px;"></div>
                                        <div id="attendanceTypeLegend" class="chart-legend"></div>
                                    @endif
                                </div>
                                <div class="col-lg-7 mb-4">
                                    <h4 class="chart-subtitle">Sessions Over Time
                                        @if (!request()->filled('date_from') && !request()->filled('date_to'))
                                            (last 30 days)
                                        @endif
                                    </h4>
                                    @if ($dailyTrend->isEmpty())
                                        <div class="analytics-empty">No data.</div>
                                    @else
                                        <div id="attendanceTrendChart" style="height:220px;"></div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

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
                                                    <th>Date</th>
                                                    <th>Register No.</th>
                                                    <th>Student</th>
                                                    <th>Branch</th>
                                                    <th>Intake</th>
                                                    <th>Course</th>
                                                    <th>Type</th>
                                                    <th>Subject</th>
                                                    <th>Time In</th>
                                                    <th>In By</th>
                                                    <th>Time Out</th>
                                                    <th>Out By</th>
                                                    <th>Duration</th>
                                                    <th>Vehicle</th>
                                                    <th>Note</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($sessions as $session)
                                                    <tr>
                                                        <td>{{ $session->attendance_date->format('Y-m-d') }}</td>
                                                        <td>{{ $session->student->admission_number ?? '-' }}</td>
                                                        <td>{{ $session->student->student_name ?? '-' }}</td>
                                                        <td>{{$session->attendance_branch?->name ?? '-' }}</td>
                                                        <td>{{ $session->intake->name ?? '-' }}</td>
                                                        <td>{{ $session->course->name ?? '-' }}</td>
                                                        <td>
                                                            @if ($session->checkpoint_type)
                                                                <span
                                                                    class="badge badge-secondary">{{ $session->type_label }}</span>
                                                            @else
                                                                {{ $session->type_label }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $session->checkpoint_type ? '—' : $session->subject->name ?? 'Course-wise' }}
                                                        </td>
                                                        <td>{{ $session->time_in ? $session->time_in->format('h:i A') : '-' }}
                                                        </td>
                                                        <td>{{ $session->markedInBy->name ?? '-' }}</td>
                                                        <td>
                                                            @if ($session->time_out)
                                                                {{ $session->time_out->format('h:i A') }}
                                                            @else
                                                                <span class="status-pill pill-in">Still in</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $session->markedOutBy->name ?? '-' }}</td>
                                                        <td>{{ $session->duration_label }}</td>
                                                        <td>{{ $session->vehicle->name ?? '-' }}</td>
                                                        <td>{{ $session->note ?? '-' }}</td>
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
                                        <strong>{{ $sessions->firstItem() ?? 0 }}</strong> –
                                        <strong>{{ $sessions->lastItem() ?? 0 }}</strong> of
                                        <strong>{{ number_format($sessions->total()) }}</strong> records
                                    </div>
                                    <div class="rp-pagination"> {{ $sessions->links('pagination::bootstrap-4') }} </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </section>
@endsection

@push('script')
    <script>
        $(function () {
            var typeData = @json($typeBreakdown);
            var trendData = @json($dailyTrend);

            var rootStyles = getComputedStyle(document.documentElement);
            var gradient1 = (rootStyles.getPropertyValue('--gradient_1') || '#5150e9').trim() || '#5150e9';
            var gradient2 = (rootStyles.getPropertyValue('--gradient_2') || '#8481f5').trim() || '#8481f5';
            var donutPalette = [gradient1, gradient2, '#1a7f45', '#a25a00', '#c0392b'];

            if (typeData.length && $('#attendanceTypeChart').length) {
                Morris.Donut({
                    element: 'attendanceTypeChart',
                    data: typeData,
                    colors: donutPalette,
                    resize: true,
                    formatter: function (y) {
                        return y;
                    }
                });

                var legendHtml = '';
                typeData.forEach(function (row, i) {
                    var color = donutPalette[i % donutPalette.length];
                    legendHtml += '<span class="legend-item"><span class="legend-dot" style="background:' + color + ';"></span>' + row.label + ' (' + row.value + ')</span>';
                });
                $('#attendanceTypeLegend').html(legendHtml);
            }

            if (trendData.length && $('#attendanceTrendChart').length) {
                Morris.Line({
                    element: 'attendanceTrendChart',
                    data: trendData,
                    xkey: 'date',
                    ykeys: ['count'],
                    labels: ['Sessions'],
                    lineColors: [gradient1],
                    pointSize: 3,
                    smooth: false,
                    resize: true,
                    parseTime: false
                });
            }
        });
    </script>
@endpush