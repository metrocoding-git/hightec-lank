@extends('layouts.app')
@section('title', 'Course Started & Course Balance Status Report')

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
                <h1>Course Started & Course Balance Status Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Course Started & Course Balance Status Report</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box mb-10">
                <a href="{{ route('reports.course-intake-payments.export', request()->query()) }}"
                    class="primary-btn small fix-gr-bg col-12 p-2" style="margin-left:auto;">
                    <span class="ti-download pr-2"></span> Export CSV
                </a>
            </div>

            <div class="white-box p-20 mb-20">
                {{-- <div class="rp-section-header">
                    <span class="rp-icon"><i class="fa-solid fa-filter"></i></span>
                    <h3>Filter</h3>
                </div> --}}
                <form method="GET" action="{{ route('reports.course-intake-payments') }}" class="mb-0" id="filter-form">
                    <div class="row">
                        <div class="col-md-12 mt-1 mb-1 text-center">
                            <small class="text-danger">
                                <i class="ti-info-alt"></i>
                                <strong>Group A</strong> (To Date + Branch) and <strong>Group B</strong> (Intake) cannot be
                                used together.
                                Selecting one group clears the other.
                            </small>
                        </div>

                        <div class="col-md-6">
                            <input type="text" placeholder="Select To Date (Group A)" id="to_date" name="to_date"
                                class="form-control group-a" value="{{ request('to_date') }}" placeholder="Select to date">
                        </div>
                        <div class="col-md-6">
                            <select name="branch_id" class="form-control group-a primary_select ">
                                <option value="">All Branches (Group A)</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mt-2">
                            <select class="form-control group-b primary_select " name="intake_id" id="search_intake_id">
                                <option value="">Select Intake (Group B)</option>
                                @foreach ($intakes as $intake)
                                    <option value="{{ $intake->id }}"
                                        {{ request('intake_id') == $intake->id ? 'selected' : '' }}>{{ $intake->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mt-4">
                            <input type="text" name="search_text" class="form-control"
                                placeholder="Reg no. / student / invoice no." value="{{ request('search_text') }}">
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
                                        <a href="{{ route('reports.course-intake-payments') }}"
                                            class="text-white">Reset</a>
                                    </button>

                                </div>

                            </div>

                        </div>
                    </div>
                </form>
            </div>

            <div class="white-box p-20 mb-20">
                <div class="rp-stat-row mb-0">
                    <div class="rp-stat-card">
                        <div class="rp-stat-value">Rs. {{ number_format($grandDue, 2) }}</div>
                        <div class="rp-stat-label">Total Due (Rs.)</div>
                    </div>
                    <div class="rp-stat-card">
                        <div class="rp-stat-value">Rs. {{ number_format($grandPaid, 2) }}</div>
                        <div class="rp-stat-label">Total Paid (Rs.)</div>
                    </div>
                    <div class="rp-stat-card">
                        <div class="rp-stat-value">{{ number_format($invoiceCount) }}</div>
                        <div class="rp-stat-label">Invoices in range</div>
                    </div>
                </div>
            </div>

            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title mb-15">
                            <h3>Report List</h3>
                        </div>
                        <div class="rp-table-wrapper">
                            <x-table>
                                <table id="table_id" class="table" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Invoice No</th>
                                            <th>Student No</th>
                                            <th>Student Category</th>
                                            <th>Branch</th>
                                            <th>Course</th>
                                            <th>Intake</th>
                                            <th>Last Payment Date</th>
                                            <th>Invoice Create Date</th>
                                            <th>Intake Date</th>
                                            <th>Status</th>
                                            <th>Total Amount</th>
                                            <th>Paid Amount</th>
                                            <th>Due Amount</th>
                                            <th>Cash</th>
                                            <th>Bank Transfer</th>
                                            <th>Invoice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($reportData as $index => $data)
                                            <tr>
                                                <td>{{ $reportData->firstItem() + $index }}</td>
                                                <td>{{ $data->main_invoice_number }}</td>
                                                <td>{{ $data->admission_number }}</td>
                                                <td>{{ $data->course_category_name ?? ($data->student_category_name ?? '-') }}
                                                </td>
                                                <td>{{ $data->branch_name }}</td>
                                                <td>{{ $data->course_name }}</td>
                                                <td>{{ $data->intake_name }}</td>
                                                <td>{{ $data->invoice_paid_date ? \Carbon\Carbon::parse($data->invoice_paid_date)->format('Y-m-d') : '-' }}
                                                </td>
                                                <td>{{ $data->invoice_create_date ? \Carbon\Carbon::parse($data->invoice_create_date)->format('Y-m-d') : '-' }}
                                                </td>
                                                <td>{{ $data->intake_start_date }}</td>
                                                <td>{{ ucfirst($data->status) }}</td>
                                                <td>{{ number_format($data->total_amount, 2) }}</td>
                                                <td>{{ number_format($data->paid_amount, 2) }}</td>
                                                <td>{{ number_format($data->due_amount, 2) }}</td>
                                                <td>{{ number_format($data->cash_amount, 2) }}</td>
                                                <td>{{ number_format($data->bank_amount, 2) }}</td>
                                                <td>{{ number_format($data->invoice_amount, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="17" class="text-center">No records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </x-table>
                        </div>

                        <div class="rp-pagination-wrapper">
                            <div class="rp-pagination-info"> Showing
                                <strong>{{ $reportData->firstItem() ?? 0 }}</strong> –
                                <strong>{{ $reportData->lastItem() ?? 0 }}</strong> of
                                <strong>{{ number_format($reportData->total()) }}</strong> records
                            </div>
                            <div class="rp-pagination"> {{ $reportData->links('pagination::bootstrap-4') }} </div>
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
        flatpickr("#to_date", {
            mode: "single",
            dateFormat: "Y-m-d",
            allowInput: true,
            defaultDate: "{{ request('to_date') }}"
        });

        $('.group-a').on('change input', function() {
            var hasGroupA = $('#to_date').val() !== '' || $('[name="branch_id"]').val() !== '';
            if (hasGroupA) {
                $('#search_intake_id').val('').prop('disabled', true);
            } else {
                $('#search_intake_id').prop('disabled', false);
            }
        });

        $('.group-b').on('change', function() {
            var hasGroupB = $(this).val() !== '';
            if (hasGroupB) {
                $('#to_date').val('');
                $('[name="branch_id"]').val('');
                $('.group-a').prop('disabled', true);
            } else {
                $('.group-a').prop('disabled', false);
            }
        });

        $(document).ready(function() {
            var hasIntake = $('#search_intake_id').val() !== '';
            var hasGroupA = $('#to_date').val() !== '' || $('[name="branch_id"]').val() !== '';

            if (hasIntake) {
                $('.group-a').prop('disabled', true);
            } else if (hasGroupA) {
                $('#search_intake_id').prop('disabled', true);
            }
        });
    </script>
@endpush
