@extends('layouts.app')
@section('title')
    Food Payment Report
@endsection

@section('mainContent')
<style>
    .group-header-year   { background: #1a1a2e; color: #fff; padding: 10px 16px; border-radius: 6px; margin: 24px 0 4px; font-size: 16px; font-weight: 600; }
    .group-header-month  { background: #16213e; color: #e0e0e0; padding: 8px 16px; border-radius: 4px; margin: 8px 0 4px; font-size: 14px; font-weight: 600; }
    .group-header-branch { background: #0f3460; color: #fff; padding: 6px 14px; border-radius: 4px; margin: 6px 0 4px; font-size: 13px; }
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
    .meal-b { background: #fff3e0; color: #e65100; }
    .meal-l { background: #e8f5e9; color: #2e7d32; }
    .meal-d { background: #e3f2fd; color: #1565c0; }
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
    .summary-card .val { font-size: 22px; font-weight: 700; color: #0f3460; }
    .summary-card .lbl { font-size: 12px; color: #6c757d; margin-top: 2px; }
</style>

<section class="sms-breadcrumb mb-20">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>Food Payment Report</h1>
            <div class="bc-pages">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="#">Reports</a>
                <a href="#">Food Payment</a>
            </div>
        </div>
    </div>
</section>

<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">

        {{-- ===== FILTERS ===== --}}
        <div class="white-box mb-20">
            <form method="GET" action="">
                <div class="row g-3">

                    {{-- Date Range --}}
                    <div class="col-md-4">
                        <label>Food Date Range</label>
                        <input type="text" id="date_range" name="date_range"
                            class="form-control" value="{{ request('date_range') }}"
                            placeholder="From date — To date">
                    </div>

                    {{-- Month --}}
                    <div class="col-md-2">
                        <label>Month</label>
                        <select name="month" class="form-control">
                            <option value="">All Months</option>
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Year --}}
                    <div class="col-md-2">
                        <label>Year</label>
                        <select name="year" class="form-control">
                            <option value="">All Years</option>
                            @foreach(range(date('Y'), date('Y') - 3) as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Branch --}}
                    <div class="col-md-3">
                        <label>Branch</label>
                        <select name="branch_id" class="form-control">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Course --}}
                    <div class="col-md-3">
                        <label>Course</label>
                        <select name="course_id" class="form-control">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Meal --}}
                    <div class="col-md-2">
                        <label>Meal</label>
                        <select name="meal" class="form-control">
                            <option value="">All Meals</option>
                            <option value="breakfast" {{ request('meal') == 'breakfast' ? 'selected' : '' }}>🍳 Breakfast</option>
                            <option value="lunch"     {{ request('meal') == 'lunch'     ? 'selected' : '' }}>🍛 Lunch</option>
                            <option value="dinner"    {{ request('meal') == 'dinner'    ? 'selected' : '' }}>🍽️ Dinner</option>
                        </select>
                    </div>

                    {{-- Lunchbox Letter --}}
                    <div class="col-md-2">
                        <label>Lunchbox Group</label>
                        <select name="letter" class="form-control">
                            <option value="">All Groups</option>
                            @foreach($letters as $l)
                                <option value="{{ $l }}" {{ request('letter') == $l ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Payment Status --}}
                    <div class="col-md-2">
                        <label>Payment Status</label>
                        <select name="payment_status" class="form-control">
                            <option value="">All</option>
                            <option value="full"    {{ request('payment_status') == 'full'    ? 'selected' : '' }}>Full Paid</option>
                            <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="not"     {{ request('payment_status') == 'not'     ? 'selected' : '' }}>Not Paid</option>
                        </select>
                    </div>

                    <div class="col-lg-12 mt-10 text-right">
                        <button type="submit" class="primary-btn small fix-gr-bg">
                            <span class="ti-search pr-2"></span> Search
                        </button>
                        <a href="{{ route('reports.food-payment') }}" class="primary-btn small fix-gr-bg ml-2">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- ===== SUMMARY CARDS ===== --}}
        @if(isset($foodPayments) && $foodPayments->count())
        <div class="row mb-20">
            <div class="col-md-2">
                <div class="summary-card">
                    <div class="val">{{ $foodPayments->count() }}</div>
                    <div class="lbl">Total Records</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="summary-card">
                    <div class="val">{{ $foodPayments->where('has_breakfast',1)->count() }}</div>
                    <div class="lbl">🍳 Breakfast</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="summary-card">
                    <div class="val">{{ $foodPayments->where('has_lunch',1)->count() }}</div>
                    <div class="lbl">🍛 Lunch</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="summary-card">
                    <div class="val">{{ $foodPayments->where('has_dinner',1)->count() }}</div>
                    <div class="lbl">🍽️ Dinner</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="summary-card">
                    <div class="val">{{ number_format($foodPayments->sum('total_food_amount'), 2) }}</div>
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
        @endif

        {{-- ===== GROUPED VIEW ===== --}}
        <div class="white-box">
            <div class="main-title mb-15">
                <h3>Food Payments — Grouped by Year / Month / Branch / Lunchbox</h3>
            </div>

            @if(isset($grouped) && $grouped->count())
                @foreach($grouped as $year => $months)
                    <div class="group-header-year">📅 {{ $year }}</div>

                    @foreach($months as $month => $branches)
                        <div class="group-header-month">
                            🗓 {{ \Carbon\Carbon::create()->month((int)$month)->format('F') }} {{ $year }}
                        </div>

                        @foreach($branches as $branchName => $letters)
                            <div class="group-header-branch">🏢 {{ $branchName }}</div>

                            @foreach($letters as $letter => $records)
                                <div class="group-header-letter">
                                    Lunchbox Group: {{ $letter }} &nbsp;|&nbsp; {{ $records->count() }} student(s)
                                </div>

                                <div class="table-responsive mb-10">
                                    <table class="table table-sm school-table-style w-100">
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
                                                <th>Total Amount</th>
                                                <th>Paid</th>
                                                <th>Due</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($records as $i => $fp)
                                            @php
                                                $student = $fp->invoice?->student;
                                                $statusClass = match($fp->payment_status) {
                                                    'full'    => 'text-success',
                                                    'partial' => 'text-warning',
                                                    default   => 'text-danger',
                                                };
                                                $statusLabel = match($fp->payment_status) {
                                                    'full'    => 'Full Paid',
                                                    'partial' => 'Partial',
                                                    default   => 'Not Paid',
                                                };
                                            @endphp
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td><span class="lunchbox-badge">{{ $fp->lunchbox_number ?? '-' }}</span></td>
                                                <td>{{ $student?->admission_number ?? 'N/A' }}</td>
                                                <td>{{ $student?->student_name ?? 'N/A' }}</td>
                                                <td>{{ $student?->studentCategory?->category_name ?? 'N/A' }}</td>
                                                <td>{{ $fp->course?->name ?? 'N/A' }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($fp->from_date)->format('d/m/Y') }}
                                                    @if($fp->from_date != $fp->to_date)
                                                        → {{ \Carbon\Carbon::parse($fp->to_date)->format('d/m/Y') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($fp->has_breakfast)
                                                        <span class="meal-badge meal-b">🍳 B</span>
                                                    @endif
                                                    @if($fp->has_lunch)
                                                        <span class="meal-badge meal-l">🍛 L</span>
                                                    @endif
                                                    @if($fp->has_dinner)
                                                        <span class="meal-badge meal-d">🍽 D</span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($fp->total_food_amount, 2) }}</td>
                                                <td>{{ number_format($fp->paid_amount, 2) }}</td>
                                                <td>{{ number_format($fp->due_amount, 2) }}</td>
                                                <td class="{{ $statusClass }}"><strong>{{ $statusLabel }}</strong></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-weight-bold" style="background:#f1f8e9;">
                                                <td colspan="8" class="text-right">Group Total:</td>
                                                <td>{{ number_format($records->sum('total_food_amount'), 2) }}</td>
                                                <td>{{ number_format($records->sum('paid_amount'), 2) }}</td>
                                                <td>{{ number_format($records->sum('due_amount'), 2) }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
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

        {{-- ===== FLAT TABLE (for export) ===== --}}
        <div class="white-box mt-20">
            <div class="main-title mb-15">
                <h3>Full List (Exportable)</h3>
            </div>
            <x-table>
                <table id="food_report_table" class="table" cellspacing="0" width="100%">
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
                            <th>Breakfast Amt</th>
                            <th>Lunch Amt</th>
                            <th>Dinner Amt</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th>Status</th>
                            <th>Payment Method</th>
                            <th>Invoice No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($foodPayments as $i => $fp)
                        @php $student = $fp->invoice?->student; @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
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
                            <td>{{ $fp->has_lunch     ? '✓' : '-' }}</td>
                            <td>{{ $fp->has_dinner    ? '✓' : '-' }}</td>
                            <td>{{ $fp->has_breakfast ? number_format($fp->breakfast_amount, 2) : '-' }}</td>
                            <td>{{ $fp->has_lunch     ? number_format($fp->lunch_amount, 2)     : '-' }}</td>
                            <td>{{ $fp->has_dinner    ? number_format($fp->dinner_amount, 2)    : '-' }}</td>
                            <td>{{ number_format($fp->total_food_amount, 2) }}</td>
                            <td>{{ number_format($fp->paid_amount, 2) }}</td>
                            <td>{{ number_format($fp->due_amount, 2) }}</td>
                            <td>{{ ucfirst($fp->payment_status) }}</td>
                            <td>{{ $fp->payment_method ?? '-' }}</td>
                            <td>{{ $fp->invoice?->main_invoice_number ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-table>
        </div>

    </div>
</section>
@endsection

@include('components.partials.data_table_js')
@include('components.partials.data_table_css')
@include('components.partials.date_picker_css_js')

@push('script')
<script>
    flatpickr("#date_range", {
        mode: "range",
        dateFormat: "Y-m-d",
        allowInput: true,
    });

    $('#food_report_table').DataTable({
        responsive: true,
        dom: 'Blfrtip',
        buttons: [
            { extend: 'copy',  title: getFileName() },
            { extend: 'csv',   title: getFileName() },
            { extend: 'excel', title: getFileName() },
            { extend: 'pdf',   title: getFileName() },
            { extend: 'print', title: getFileName() },
        ],
        paging: true,
        searching: true,
        ordering: true,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 25,
        language: {
            search: '<i class="fa-solid fa-magnifying-glass me-2"></i>',
            searchPlaceholder: 'Search...',
        }
    });

    function getFileName() {
        let now  = new Date();
        let date = now.toISOString().slice(0, 10);
        let time = now.toTimeString().slice(0, 8).replace(/:/g, '-');
        return 'Food-Payment-Report_' + date + '__' + time;
    }
</script>
@endpush