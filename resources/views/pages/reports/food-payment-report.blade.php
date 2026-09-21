@extends('layouts.app')
@section('title')
    Food Payment Details Report
@endsection

@section('mainContent')
    <style>
        .group-header-year {
            background: #1a1a2e;
            color: #fff;
            padding: 10px 16px;
            border-radius: 6px;
            margin: 24px 0 4px;
            font-size: 16px;
            font-weight: 600;
        }

        .group-header-month {
            background: #16213e;
            color: #e0e0e0;
            padding: 8px 16px;
            border-radius: 4px;
            margin: 8px 0 4px;
            font-size: 14px;
            font-weight: 600;
        }

        .group-header-branch {
            background: #0f3460;
            color: #fff;
            padding: 6px 14px;
            border-radius: 4px;
            margin: 6px 0 4px;
            font-size: 13px;
        }

        .group-header-letter {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 5px 12px;
            border-radius: 4px;
            margin: 4px 0 2px;
            font-size: 13px;
            font-weight: 600;
            border-left: 4px solid #2e7d32;
        }

        .meal-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-right: 3px;
        }

        .meal-b {
            background: #fff3e0;
            color: #e65100;
        }

        .meal-l {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .meal-d {
            background: #e3f2fd;
            color: #1565c0;
        }

        .lunchbox-badge {
            display: inline-block;
            background: #6f42c1;
            color: #fff;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .summary-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 14px 20px;
            text-align: center;
        }

        .summary-card .val {
            font-size: 22px;
            font-weight: 700;
            color: #0f3460;
        }

        .summary-card .lbl {
            font-size: 12px;
            color: #6c757d;
            margin-top: 2px;
        }

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

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Food Payment Details Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Food Payment Details Report</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">

            {{-- ===== FILTERS ===== --}}
            <div class="white-box mb-10">
                <a href="{{ route('reports.food-payment.export', request()->query()) }}"
                    class="primary-btn small fix-gr-bg col-12 p-2" style="margin-left:auto;">
                    <span class="ti-download pr-2"></span> Export CSV
                </a>
            </div>

            <div class="white-box p-20 mb-20">
                {{-- <div class="rp-section-header">
                    <span class="rp-icon"><i class="fa-solid fa-filter"></i></span>
                    <h3>Filter</h3>
                </div> --}}
                <form method="GET" action="{{ route('reports.food-payment') }}">
                    <div class="row g-3">

                        {{-- Date Range --}}
                        <div class="col-md-3">
                            <input type="text" id="date_range" name="date_range" class="form-control"
                                value="{{ request('date_range') }}" placeholder="Select Date Range">
                        </div>

                        {{-- Month --}}
                        <div class="col-md-3">
                            <select name="month" class="form-control primary_select ">
                                <option value="">All Months</option>
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Year --}}
                        <div class="col-md-3">
                            <select name="year" class="form-control primary_select ">
                                <option value="">All Years</option>
                                @foreach (range(date('Y'), date('Y') - 3) as $y)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Branch --}}
                        <div class="col-md-3">
                            <select name="branch_id" class="form-control primary_select ">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Course --}}
                        <div class="col-md-3 mt-2">
                            <select name="course_id" class="form-control primary_select ">
                                <option value="">All Courses</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Meal --}}
                        <div class="col-md-3 mt-2">
                            <select name="meal" class="form-control primary_select ">
                                <option value="">All Meals</option>
                                <option value="breakfast" {{ request('meal') == 'breakfast' ? 'selected' : '' }}>🍳
                                    Breakfast</option>
                                <option value="lunch" {{ request('meal') == 'lunch' ? 'selected' : '' }}>🍛 Lunch
                                </option>
                                <option value="dinner" {{ request('meal') == 'dinner' ? 'selected' : '' }}>🍽️ Dinner
                                </option>
                            </select>
                        </div>

                        {{-- Lunchbox Letter --}}
                        <div class="col-md-3 mt-2">
                            <select name="letter" class="form-control primary_select ">
                                <option value="">All Lunchbox Groups</option>
                                @foreach ($letters as $l)
                                    <option value="{{ $l }}" {{ request('letter') == $l ? 'selected' : '' }}>
                                        {{ $l }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Payment Status --}}
                        <div class="col-md-3 mt-2">
                            <select name="status" class="form-control primary_select ">
                                <option value="">All Status</option>
                                <option value="1" {{ request('status', '1') == '1' ? 'selected' : '' }}>Active
                                </option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>

                            </select>
                        </div>

                        {{-- Search --}}
                        <div class="col-md-12 mt-4">
                            <input type="text" name="search_text" class="form-control"
                                placeholder="Lunchbox no. / reg no. / student / invoice no."
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
                                        <a href="{{ route('reports.food-payment') }}" class="text-white">Reset</a>
                                    </button>

                                </div>

                            </div>

                        </div>
                    </div>
                </form>
            </div>

            {{-- ===== SUMMARY CARDS ===== --}}
            @if (isset($foodPayments) && $foodPayments->count())
                <div class="white-box p-20 mb-20">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="summary-card">
                                <div class="val">{{ $foodPayments->count() }}</div>
                                <div class="lbl">Total Records</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="summary-card">
                                <div class="val">{{ $foodPayments->where('has_breakfast', 1)->count() }}</div>
                                <div class="lbl">🍳 Breakfast</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="summary-card">
                                <div class="val">{{ $foodPayments->where('has_lunch', 1)->count() }}</div>
                                <div class="lbl">🍛 Lunch</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="summary-card">
                                <div class="val">{{ $foodPayments->where('has_dinner', 1)->count() }}</div>
                                <div class="lbl">🍽️ Dinner</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="summary-card">
                                {{-- <div class="val">{{ number_format($foodPayments->sum('total_food_amount'), 2) }}</div> --}}
                                <div class="val">-</div>
                                <div class="lbl">Total Amount</div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="summary-card">
                                <div class="val">{{ number_format($foodPayments->sum('due_amount'), 2) }}</div>
                                <div class="lbl">Total Due</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($mealBreakdown->count() > 0)
                <div class="white-box p-20 mb-20">
                    <div class="rp-section-header">
                        <span class="rp-icon"><i class="fa-solid fa-chart-pie"></i></span>
                        <h3>Analytics — Filtered Results</h3>
                    </div>
                    <div class="rp-chart-row">
                        <div class="rp-chart-card">
                            <h5>Meals Served</h5>
                            <div id="meal-chart" style="height:220px;"></div>
                            <div id="meal-legend" class="chart-legend"></div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ===== GROUPED VIEW ===== --}}
            <div class="white-box">
                <div class="main-title mb-15">
                    <h3>Food Payments — Grouped by Year / Month / Branch / Lunchbox</h3>
                </div>

                @if (isset($grouped) && $grouped->count())
                    @foreach ($grouped as $year => $months)
                        <div class="group-header-year">📅 {{ $year }}</div>

                        @foreach ($months as $month => $branches)
                            <div class="group-header-month">
                                🗓 {{ \Carbon\Carbon::create()->month((int) $month)->format('F') }} {{ $year }}
                            </div>

                            @foreach ($branches as $branchName => $letters)
                                @php
                                    $branchRecords = $letters->flatten();
                                @endphp
                                <div class="group-header-branch">
                                    🏢 {{ $branchName }}
                                    &nbsp;|&nbsp;
                                    <span style="font-size:12px; font-weight:400;">
                                        👥 {{ $branchRecords->count() }} students
                                        &nbsp;&nbsp;
                                        🍳 Breakfast:
                                        <strong>{{ $branchRecords->where('has_breakfast', 1)->count() }}</strong>
                                        &nbsp;
                                        🍛 Lunch: <strong>{{ $branchRecords->where('has_lunch', 1)->count() }}</strong>
                                        &nbsp;
                                        🍽️ Dinner: <strong>{{ $branchRecords->where('has_dinner', 1)->count() }}</strong>
                                        &nbsp;&nbsp;

                                    </span>
                                </div>


                                @foreach ($letters as $letter => $records)
                                    <div class="group-header-letter">
                                        Lunchbox Group: {{ $letter }} &nbsp;|&nbsp; {{ $records->count() }}
                                        student(s)
                                    </div>

                                    <div class="table-responsive mb-10 mt-10">
                                        <div class="rp-table-wrapper">
                                            <x-table>
                                                <table id="table_id" class="table table-sm school-table-style w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Lunchbox No.</th>
                                                            <th>Student No.</th>
                                                            <th>Student Name</th>
                                                            <th>Category</th>
                                                            <th>Course</th>
                                                            <th>Food Period</th>
                                                            <th>Meals</th>

                                                            <th>Due</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($records as $i => $fp)
                                                            @php
                                                                $student = $fp->invoice?->student;
                                                                $statusClass = match ((int) $fp->status) {
                                                                    1 => 'text-success',
                                                                    0 => 'text-danger',
                                                                    default => '',
                                                                };

                                                                $statusLabel = match ((int) $fp->status) {
                                                                    1 => 'Active',
                                                                    0 => 'Inactive',
                                                                    default => '-',
                                                                };
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td><span
                                                                        class="lunchbox-badge">{{ $fp->lunchbox_number ?? '-' }}</span>
                                                                </td>
                                                                <td>{{ $student?->admission_number ?? 'N/A' }}</td>
                                                                <td>{{ $student?->student_name ?? 'N/A' }}</td>
                                                                <td>{{ $student?->studentCategory?->category_name ?? 'N/A' }}
                                                                </td>
                                                                <td>{{ $fp->course?->name ?? 'N/A' }}</td>
                                                                <td>
                                                                    {{ \Carbon\Carbon::parse($fp->from_date)->format('d/m/Y') }}
                                                                    @if ($fp->from_date != $fp->to_date)
                                                                        →
                                                                        {{ \Carbon\Carbon::parse($fp->to_date)->format('d/m/Y') }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($fp->has_breakfast)
                                                                        <span class="meal-badge meal-b">🍳 B</span>
                                                                    @endif
                                                                    @if ($fp->has_lunch)
                                                                        <span class="meal-badge meal-l">🍛 L</span>
                                                                    @endif
                                                                    @if ($fp->has_dinner)
                                                                        <span class="meal-badge meal-d">🍽 D</span>
                                                                    @endif
                                                                </td>
                                                                {{-- <td>{{ number_format($fp->total_food_amount, 2) }}</td>
                                                        <td>{{ number_format($fp->paid_amount, 2) }}</td> --}}
                                                                <td>{{ number_format($fp->due_amount, 2) }}</td>
                                                                <td class="{{ $statusClass }}">
                                                                    <strong>{{ $statusLabel }}</strong>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="font-weight-bold" style="background:#f1f8e9;">
                                                            <td colspan="8" class="text-right">Total Due:</td>

                                                            <td>{{ number_format($records->sum('due_amount'), 2) }}</td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </x-table>
                                        </div>

                                    </div>
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                @else
                    <div class="text-center text-muted py-5">
                        <i class="ti-info-alt" style="font-size:32px;"></i>
                        <p class="mt-2">No food payment records found. Use the filters above to search.</p>
                    </div>
                @endif
            </div>

            {{-- ===== FLAT TABLE (paginated) ===== --}}
            <div class="white-box mt-20">
                <div class="d-flex justify-content-between align-items-center mb-15">
                    <div class="main-title mb-0">
                        <h3>Report List</h3>
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
                                            <th>Lunchbox No.</th>
                                            <th>Group</th>
                                            <th>Year</th>
                                            <th>Month</th>
                                            <th>Branch</th>
                                            <th>Student No.</th>
                                            <th>Student Name</th>
                                            <th>Category</th>
                                            <th>Course</th>
                                            <th>Food From</th>
                                            <th>Food To</th>
                                            <th>Breakfast</th>
                                            <th>Lunch</th>
                                            <th>Dinner</th>
                                            <th>Due Amount</th>
                                            <th>Status</th>
                                            <th>Invoice No.</th>
                                            <th>Create Date</th>
                                            <th>Update Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($foodPaymentsFlat as $index => $fp)
                                            @php $student = $fp->invoice?->student; @endphp
                                            <tr>
                                                <td>{{ $foodPaymentsFlat->firstItem() + $index }}</td>
                                                <td>{{ $fp->lunchbox_number ?? '-' }}</td>
                                                <td>{{ substr($fp->lunchbox_number ?? '-', 0, 1) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($fp->from_date)->format('Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($fp->from_date)->format('F') }}</td>
                                                <td>{{ $fp->invoice?->branch?->name ?? 'N/A' }}</td>
                                                <td>{{ $student?->admission_number ?? 'N/A' }}</td>
                                                <td>{{ $student?->student_name ?? 'N/A' }}</td>
                                                <td>{{ $student?->studentCategory?->category_name ?? 'N/A' }}</td>
                                                <td>{{ $fp->course?->name ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($fp->from_date)->format('d/m/Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($fp->to_date)->format('d/m/Y') }}</td>
                                                <td>{{ $fp->has_breakfast ? '✓' : '-' }}</td>
                                                <td>{{ $fp->has_lunch ? '✓' : '-' }}</td>
                                                <td>{{ $fp->has_dinner ? '✓' : '-' }}</td>
                                                <td>{{ number_format($fp->due_amount, 2) }}</td>
                                                <td>{{ ucfirst($fp->payment_status) }}</td>
                                                <td>{{ $fp->invoice?->main_invoice_number ?? 'N/A' }}</td>
                                                <td>{{ $fp->invoice?->created_at ?? 'N/A' }}</td>
                                                <td>{{ $fp->invoice?->updated_at ?? 'N/A' }}</td>
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
                    </div>
                </div>

                <div class="rp-pagination-wrapper">
                    <div class="rp-pagination-info"> Showing
                        <strong>{{ $foodPaymentsFlat->firstItem() ?? 0 }}</strong> –
                        <strong>{{ $foodPaymentsFlat->lastItem() ?? 0 }}</strong> of
                        <strong>{{ number_format($foodPaymentsFlat->total()) }}</strong> records
                    </div>
                    <div class="rp-pagination"> {{ $foodPaymentsFlat->links('pagination::bootstrap-4') }} </div>
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

@if ($mealBreakdown->count() > 0)
    @push('script')
        <script>
            (function() {
                var gradient1 = getComputedStyle(document.documentElement).getPropertyValue('--gradient_1').trim() ||
                    '#5150e9';
                var gradient2 = getComputedStyle(document.documentElement).getPropertyValue('--gradient_2').trim() ||
                    '#a55eea';
                var palette = [gradient1, gradient2, '#2ecc71', '#f39c12', '#e74c3c'];

                Morris.Donut({
                    element: 'meal-chart',
                    data: @json($mealBreakdown),
                    colors: palette,
                    resize: true,
                });
                var legendData = @json($mealBreakdown);
                var legendHtml = '';
                legendData.forEach(function(row, i) {
                    legendHtml += '<span class="legend-item"><span class="legend-dot" style="background:' + palette[
                        i % palette.length] + '"></span>' + row.label + '</span>';
                });
                document.getElementById('meal-legend').innerHTML = legendHtml;
            })();
        </script>
    @endpush
@endif
