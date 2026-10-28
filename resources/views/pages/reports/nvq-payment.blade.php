@extends('layouts.app')
@section('title', 'NVQ Payment Report')

@section('mainContent')
    @push('css')
        <style>
            .rp-stat-row { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
            .rp-stat-card { flex: 1 1 180px; padding: 16px 20px; border-radius: 14px; background: #fff; border: 1px solid #eceef3; box-shadow: 0 2px 14px rgba(20, 20, 43, 0.05); }
            .rp-stat-card .rp-stat-value { font-size: 22px; font-weight: 700; color: #1c1f2b; font-variant-numeric: tabular-nums; }
            .rp-stat-card .rp-stat-label { font-size: 12px; color: #8a8fa3; margin-top: 4px; }
            .white-box { border-radius: 14px; box-shadow: 0 2px 14px rgba(20, 20, 43, 0.05); border: 1px solid #eceef3; }
            #table_id thead tr th { background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%); color: #fff; font-weight: 600; white-space: nowrap; border: none; }
            #table_id td { vertical-align: middle; }
            .nvq-pct { min-width: 110px; }
            .nvq-pct .bar { height: 6px; border-radius: 3px; background: #eceef3; overflow: hidden; margin-top: 4px; }
            .nvq-pct .bar span { display: block; height: 100%; background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2)); }
            .nvq-badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; white-space: nowrap; }
            .nvq-paid { background: #e8f6ee; color: #1e7a43; }
            .nvq-partial { background: #fff6e0; color: #9a7300; }
            .nvq-not { background: #fdecea; color: #a12a2a; }
            .text-num { font-variant-numeric: tabular-nums; text-align: right; white-space: nowrap; }
        </style>
    @endpush

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>NVQ Payment Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">NVQ Payment</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            @if (Auth::user()->hasPermission('reports.nvq-payment.export'))
                <div class="white-box mb-10">
                    <a href="{{ route('reports.nvq-payment.export', request()->query()) }}"
                        class="primary-btn small fix-gr-bg col-12 p-2">
                        <span class="ti-download pr-2"></span> Export CSV
                    </a>
                </div>
            @endif

            <div class="white-box p-20 mb-20">
                <form method="GET" action="{{ route('reports.nvq-payment') }}" class="mb-0">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="small text-muted mb-1">Course completion above (%)</label>
                            <input type="number" name="min_percent" min="0" max="100" step="1" class="form-control"
                                value="{{ request('min_percent', $minPercent) }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small text-muted mb-1">Branch</label>
                            <select name="branch_id" class="form-control primary_select">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small text-muted mb-1">Course (NVQ courses)</label>
                            <select name="course_id" class="form-control primary_select">
                                <option value="">All NVQ Courses</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small text-muted mb-1">Intake</label>
                            <select name="intake_id" class="form-control primary_select">
                                <option value="">All Intakes</option>
                                @foreach ($intakes as $intake)
                                    <option value="{{ $intake->id }}" {{ request('intake_id') == $intake->id ? 'selected' : '' }}>
                                        {{ $intake->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small text-muted mb-1">NVQ Payment</label>
                            <select name="nvq_status" class="form-control primary_select">
                                <option value="">All</option>
                                @foreach (['Paid', 'Partial', 'Not Paid'] as $st)
                                    <option value="{{ $st }}" {{ request('nvq_status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small text-muted mb-1">Enrolment Status</label>
                            <select name="status" class="form-control primary_select">
                                <option value="">Started + Completed</option>
                                <option value="start" {{ request('status') === 'start' ? 'selected' : '' }}>Started</option>
                                <option value="complete" {{ request('status') === 'complete' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="small text-muted mb-1">Search</label>
                            <input type="text" name="search_text" class="form-control"
                                placeholder="Reg no. / name / NIC / phone" value="{{ request('search_text') }}">
                        </div>
                        <div class="col-12 mt-10">
                            <div class="row align-items-center">
                                <div class="col-10">
                                    <button type="submit" class="primary-btn small fix-gr-bg col-12 p-2">
                                        <span class="ti-search pr-2"></span> Search
                                    </button>
                                </div>
                                <div class="col-2">
                                    <a href="{{ route('reports.nvq-payment') }}"
                                        class="primary-btn small fix-gr-bg col-12 p-2 text-white text-center d-block">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="rp-stat-row">
                <div class="rp-stat-card">
                    <div class="rp-stat-value">{{ number_format($summary['students']) }}</div>
                    <div class="rp-stat-label">Students above {{ rtrim(rtrim(number_format($minPercent, 1), '0'), '.') }}% completion</div>
                </div>
                <div class="rp-stat-card">
                    <div class="rp-stat-value">Rs. {{ number_format($summary['nvq_total'], 2) }}</div>
                    <div class="rp-stat-label">NVQ amount</div>
                </div>
                <div class="rp-stat-card">
                    <div class="rp-stat-value text-success">Rs. {{ number_format($summary['nvq_paid'], 2) }}</div>
                    <div class="rp-stat-label">NVQ paid</div>
                </div>
                <div class="rp-stat-card">
                    <div class="rp-stat-value text-danger">Rs. {{ number_format($summary['nvq_due'], 2) }}</div>
                    <div class="rp-stat-label">NVQ due</div>
                </div>
                <div class="rp-stat-card">
                    <div class="rp-stat-value">{{ $summary['paid'] }} / {{ $summary['partial'] }} / {{ $summary['not_paid'] }}</div>
                    <div class="rp-stat-label">Paid / Partial / Not paid</div>
                </div>
            </div>

            <div class="white-box mb-20">
                <div class="main-title">
                    <h3 class="mb-15">Report List</h3>
                </div>
                <p class="small text-muted mb-10">
                    Completion % = completed subjects ÷ assigned subjects (theory + practical). A practical subject is
                    complete when its practice time is done; a theory subject when it has been attended.
                </p>
                <x-table>
                    <table id="table_id" class="table" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student No</th>
                                <th>Student Name</th>
                                <th>NIC</th>
                                <th>Phone</th>
                                <th>Course</th>
                                <th>Intake</th>
                                <th>Branch</th>
                                <th>Theory</th>
                                <th>Practical</th>
                                <th>Practice Time</th>
                                <th>Completion</th>
                                <th>NVQ Amount</th>
                                <th>NVQ Paid</th>
                                <th>NVQ Due</th>
                                <th>NVQ Status</th>
                                <th>Last NVQ Payment</th>
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
                                    <td>{{ $row['intake_name'] }}<div class="small text-muted">{{ $row['intake_start'] }}</div></td>
                                    <td>{{ $row['branch_name'] }}</td>
                                    <td>{{ $row['theory_done'] }}/{{ $row['theory_total'] }}</td>
                                    <td>{{ $row['practical_done'] }}/{{ $row['practical_total'] }}</td>
                                    <td class="text-nowrap">{{ $row['practice_done'] }} / {{ $row['practice_required'] }}</td>
                                    <td class="nvq-pct">
                                        <strong>{{ number_format($row['percent'], 1) }}%</strong>
                                        <div class="bar"><span style="width: {{ min(100, $row['percent']) }}%"></span></div>
                                    </td>
                                    <td class="text-num">{{ number_format($row['nvq_amount'], 2) }}</td>
                                    <td class="text-num">{{ number_format($row['nvq_paid'], 2) }}</td>
                                    <td class="text-num">{{ number_format($row['nvq_due'], 2) }}</td>
                                    <td>
                                        <span class="nvq-badge {{ $row['nvq_status'] === 'Paid' ? 'nvq-paid' : ($row['nvq_status'] === 'Partial' ? 'nvq-partial' : 'nvq-not') }}">
                                            {{ $row['nvq_status'] }}</span>
                                    </td>
                                    <td>{{ $row['last_payment'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="17" class="text-center">No students found for these filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-table>
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
