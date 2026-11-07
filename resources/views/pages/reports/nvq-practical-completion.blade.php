@extends('layouts.app')
@section('title', 'NVQ Paid — Practical Completion')

@section('mainContent')
    @push('css')
        <style>
            .rp-stat-row { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
            .rp-stat-card { flex: 1 1 180px; padding: 16px 20px; border-radius: 14px; background: #fff; border: 1px solid #eceef3; box-shadow: 0 2px 14px rgba(20, 20, 43, 0.05); }
            .rp-stat-card .rp-stat-value { font-size: 22px; font-weight: 700; color: #1c1f2b; font-variant-numeric: tabular-nums; }
            .rp-stat-card .rp-stat-label { font-size: 12px; color: #8a8fa3; margin-top: 4px; }
            .white-box { border-radius: 14px; box-shadow: 0 2px 14px rgba(20, 20, 43, 0.05); border: 1px solid #eceef3; }
            #nvq_practical_table thead tr th { background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%); color: #fff; font-weight: 600; white-space: nowrap; border: none; }
            #nvq_practical_table td { vertical-align: middle; }
            .nvq-pct { min-width: 110px; }
            .nvq-pct .bar { height: 6px; border-radius: 3px; background: #eceef3; overflow: hidden; margin-top: 4px; }
            .nvq-pct .bar span { display: block; height: 100%; background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2)); }
            .nvq-pct.full strong { color: #1e7a43; }
            .text-num { font-variant-numeric: tabular-nums; text-align: right; white-space: nowrap; }
            .rp-step { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 50%; background: var(--gradient_1, #7c32ff); color: #fff; font-size: 11px; font-weight: 700; margin-right: 6px; }
            .rp-filter-label { font-size: 12px; color: #8a8fa3; margin-bottom: 4px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .rp-range-hint { font-size: 12px; color: #8a8fa3; }
        </style>
    @endpush

    @php
        $fmtPct = fn($v) => rtrim(rtrim(number_format($v, 1), '0'), '.');
    @endphp

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>NVQ Paid — Practical Completion</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">NVQ Paid — Practical Completion</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            @if (Auth::user()->hasPermission('reports.nvq-practical-completion.export'))
                <div class="white-box mb-10">
                    <a href="{{ route('reports.nvq-practical-completion.export', request()->query()) }}"
                        class="primary-btn small fix-gr-bg col-12 p-2">
                        <span class="ti-download pr-2"></span> Export Excel
                    </a>
                </div>
            @endif

            <div class="white-box p-20 mb-20">
                <form method="GET" action="{{ route('reports.nvq-practical-completion') }}" class="mb-0" id="nvq_practical_filter">
                    <div class="row align-items-end">
                        <div class="col-md-6 col-lg-3 mb-2">
                            <label class="rp-filter-label"><span class="rp-step">1</span>NVQ paid from (date &amp; time)</label>
                            <input type="datetime-local" name="paid_from" class="form-control" required
                                value="{{ $from->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-md-6 col-lg-3 mb-2">
                            <label class="rp-filter-label">NVQ paid to (date &amp; time)</label>
                            <input type="datetime-local" name="paid_to" class="form-control" required
                                value="{{ $to->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-md-4 col-lg-2 mb-2">
                            <label class="rp-filter-label"><span class="rp-step">2</span>Practical ≥ %</label>
                            <input type="number" name="min_percent" min="0" max="100" step="1" class="form-control"
                                value="{{ $fmtPct($minPercent) }}">
                        </div>
                        <div class="col-md-4 col-lg-2 mb-2">
                            <label class="rp-filter-label"><span class="rp-step">3</span>Start branch</label>
                            <select name="branch_id" class="form-control primary_select">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-2 mb-2">
                            <label class="rp-filter-label"><span class="rp-step">4</span>Course</label>
                            <select name="course_id" class="form-control primary_select">
                                <option value="">All Courses</option>
                                @foreach ($courseOptions as $course)
                                    <option value="{{ $course['id'] }}" {{ request('course_id') == $course['id'] ? 'selected' : '' }}>
                                        {{ $course['name'] }} ({{ $course['count'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-10">
                            <div class="row align-items-center">
                                <div class="col-8 col-md-10">
                                    <button type="submit" class="primary-btn small fix-gr-bg col-12 p-2">
                                        <span class="ti-search pr-2"></span> Search
                                    </button>
                                </div>
                                <div class="col-4 col-md-2">
                                    <a href="{{ route('reports.nvq-practical-completion') }}"
                                        class="primary-btn small fix-gr-bg col-12 p-2 text-white text-center d-block">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="rp-stat-row">
                <div class="rp-stat-card">
                    <div class="rp-stat-value">{{ number_format($summary['all_in_range']) }}</div>
                    <div class="rp-stat-label">Students who paid NVQ in this range{{ request('branch_id') ? ' (this branch)' : '' }}</div>
                </div>
                <div class="rp-stat-card">
                    <div class="rp-stat-value text-success">{{ number_format($summary['students']) }}</div>
                    <div class="rp-stat-label">Practical completion ≥ {{ $fmtPct($minPercent) }}%{{ request('course_id') ? ' (this course)' : '' }}</div>
                </div>
                <div class="rp-stat-card">
                    <div class="rp-stat-value">Rs. {{ number_format($summary['nvq_paid'], 2) }}</div>
                    <div class="rp-stat-label">NVQ paid in range (listed students)</div>
                </div>
                <div class="rp-stat-card">
                    <div class="rp-stat-value">{{ number_format($summary['courses']) }}</div>
                    <div class="rp-stat-label">Courses</div>
                </div>
            </div>

            <div class="white-box mb-20">
                <div class="main-title">
                    <h3 class="mb-15">Report List</h3>
                </div>
                <p class="small text-muted mb-10">
                    Students with an NVQ payment between <strong>{{ $from->format('Y-m-d h:i A') }}</strong> and
                    <strong>{{ $to->format('Y-m-d h:i A') }}</strong>. Practical completion = practice time done ÷ required
                    practical time, over every practical subject of the student's intake + course (time on a subject
                    counts up to that subject's required time). Returned invoices and refunded payments are not counted.
                </p>
                <div class="rp-table-wrapper">
                    <x-table>
                        <table id="nvq_practical_table" class="table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student No</th>
                                    <th>Student Name</th>
                                    <th>NIC</th>
                                    <th>Phone</th>
                                    <th>Course</th>
                                    <th>Intake</th>
                                    <th>Start Branch</th>
                                    <th>Practical Subjects</th>
                                    <th>Practice Time (done / required)</th>
                                    <th>Practical Completion</th>
                                    <th>NVQ Paid (range)</th>
                                    <th>Last NVQ Payment</th>
                                    <th>Receipt No(s)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $row)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $row['admission_number'] }}</td>
                                        <td>{{ $row['student_name'] }}</td>
                                        <td>{{ $row['nic'] ?? '-' }}</td>
                                        <td>{{ $row['phone'] ?? '-' }}</td>
                                        <td>{{ $row['course_name'] }}</td>
                                        <td>{{ $row['intake_name'] }}<div class="small text-muted">{{ $row['intake_start'] ?? '' }}</div></td>
                                        <td>{{ $row['branch_name'] }}</td>
                                        <td>{{ $row['practical_done'] }}/{{ $row['practical_total'] }}</td>
                                        <td class="text-nowrap">{{ $row['practice_done'] }} / {{ $row['practice_required'] }}</td>
                                        <td class="nvq-pct {{ $row['percent'] >= 100 ? 'full' : '' }}">
                                            <strong>{{ number_format($row['percent'], 1) }}%</strong>
                                            <div class="bar"><span style="width: {{ min(100, $row['percent']) }}%"></span></div>
                                        </td>
                                        <td class="text-num">{{ number_format($row['nvq_paid_in_range'], 2) }}</td>
                                        <td class="text-nowrap">{{ $row['last_paid_at'] ? \Carbon\Carbon::parse($row['last_paid_at'])->format('Y-m-d h:i A') : '-' }}</td>
                                        <td>{{ $row['receipts'] ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="text-center">No students found for these filters.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </x-table>
                </div>
                <div class="rp-pagination-wrapper d-flex justify-content-between align-items-center flex-wrap mt-10">
                    <div class="rp-pagination-info">Showing <strong>{{ $data->firstItem() ?? 0 }}</strong> –
                        <strong>{{ $data->lastItem() ?? 0 }}</strong> of <strong>{{ number_format($data->total()) }}</strong>
                        records</div>
                    <div class="rp-pagination">{{ $data->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        // A different date range / branch makes a different set, so the
        // course picked from the old set may not exist any more — clear it.
        $(function() {
            $('#nvq_practical_filter').on('change', '[name="paid_from"], [name="paid_to"], [name="branch_id"], [name="min_percent"]', function() {
                const $course = $('#nvq_practical_filter [name="course_id"]');
                if ($course.val()) {
                    $course.val('');
                    if ($course.next('.nice-select').length && typeof $course.niceSelect === 'function') {
                        $course.niceSelect('update');
                    }
                }
            });
        });
    </script>
@endpush
