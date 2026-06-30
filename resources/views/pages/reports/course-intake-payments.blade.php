@extends('layouts.app')
@section('title', 'Course Intake Payments Report')

@section('mainContent')
    @php
        $defaultFrom = request('date_range')
            ? explode(' to ', request('date_range'))[0]
            : \Carbon\Carbon::now()->subDays(30)->format('Y-m-d');
        $defaultTo = request('date_range')
            ? explode(' to ', request('date_range'))[1] ?? $defaultFrom
            : \Carbon\Carbon::now()->format('Y-m-d');
    @endphp


    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Course Started & Course blance Status Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">Course Intake Payments</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box">
                <form method="GET" action="" class="mb-20">
                    <div class="row">

                        <div class="col-md-12 mb-2">
                            <small class="text-danger">
                                <i class="ti-info-alt"></i>
                                <strong>Group A</strong> (To Date + Branch) and <strong>Group B</strong> (Intake)
                                cannot be used together. Selecting one group clears the other.
                            </small>
                        </div>

                        {{-- GROUP A --}}
                        <div class="col-md-4">
                            <label>To Date <small class="text-muted">(Group A)</small></label>
                            <input type="text" id="to_date" name="to_date" class="form-control group-a"
                                value="{{ request('to_date') }}" placeholder="Select to date">
                        </div>

                        <div class="col-md-4">
                            <label>Branch <small class="text-muted">(Group A)</small></label>
                            <select name="branch_id" class="form-control group-a">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- GROUP B --}}
                        <div class="col-md-4">
                            <label>Intake <small class="text-muted">(Group B)</small></label>
                            <select class="primary_select form-control group-b" name="intake_id" id="intake_id">
                                <option value="">Select Intake</option>
                                @foreach ($intakes as $intake)
                                    <option value="{{ $intake->id }}"
                                        {{ request('intake_id') == $intake->id ? 'selected' : '' }}>
                                        {{ $intake->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-12 mt-20 text-right">
                            <button type="submit" class="primary-btn small fix-gr-bg">
                                <span class="ti-search pr-2"></span> Search
                            </button>
                            <button class="primary-btn small fix-gr-bg">
                                <a href="{{ route('reports.course-intake-payments') }}" class="text-white">
                                    Reset
                                </a>
                            </button>

                        </div>
                    </div>
                </form>
            </div>

            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title mb-15">
                            <h3>Payments Report</h3>
                        </div>
                        <div style="overflow-x: auto; width: 100%;">

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
                                    <tfoot>
                                        <tr>
                                            <th colspan="11" style="text-align:right">Total:</th>
                                            <th id="total_amount_footer"></th>
                                            <th id="paid_amount_footer"></th>
                                            <th id="cash_footer"></th>
                                            <th id="bank_footer"></th>
                                            <th id="invoice_footer"></th>

                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($reportData as $key => $data)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
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
                                        @endforeach
                                    </tbody>
                                </table>
                            </x-table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@include('components.partials.data_table_js')
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')

@push('script')
    <script>
        flatpickr("#to_date", {
            mode: "single",
            dateFormat: "Y-m-d",
            allowInput: true,
            defaultDate: "{{ request('to_date') }}" || null
        });

        // Mutual exclusivity
        $('.group-a').on('change input', function() {
            var hasGroupA = $('#to_date').val() !== '' || $('[name="branch_id"]').val() !== '';
            if (hasGroupA) {
                $('#intake_id').val('').prop('disabled', true);
            } else {
                $('#intake_id').prop('disabled', false);
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



        // Restore state on page load
        $(document).ready(function() {
            var hasIntake = $('#intake_id').val() !== '';
            var hasGroupA = $('#to_date').val() !== '' || $('[name="branch_id"]').val() !== '';

            if (hasIntake) {
                $('.group-a').prop('disabled', true);
            } else if (hasGroupA) {
                $('#intake_id').prop('disabled', true);
            }

            $(document).ready(function() {
                var table = $('#table_id').DataTable({
                    responsive: false,
                    autoWidth: false,
                    dom: 'Blfrtip',
                    buttons: [{
                            extend: 'copy',
                            title: getFileName()
                        },
                        {
                            extend: 'csv',
                            title: getFileName()
                        },
                        {
                            extend: 'excel',
                            title: getFileName()
                        },
                        {
                            extend: 'pdf',
                            title: getFileName()
                        },
                        {
                            extend: 'print',
                            title: getFileName()
                        }
                    ],
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    scrollX: true,
                    lengthChange: true,
                    order: [
                        [0, 'asc']
                    ],
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    pageLength: 25,
                    language: {
                        search: '<i class="fa-solid fa-magnifying-glass me-2"></i>',
                        searchPlaceholder: 'Search...',
                        paginate: {
                            previous: '<i class="fa-solid fa-angle-left"></i>',
                            next: '<i class="fa-solid fa-angle-right"></i>'
                        }
                    }
                });

                function getFileName() {
                    let now = new Date();

                    let date = now.toISOString().slice(0, 10);
                    let time = now.toTimeString().slice(0, 8).replace(/:/g, '-'); // HH-MM-SS

                    return 'Course-Intake-Payments-Report_' + date + '__' + time;
                }
                table.on('length.dt page.dt search.dt order.dt draw.dt', function() {
                    updateFooter();
                });

                // ============ SUM FOOTER ============
                function updateFooter() {
                    var totalAmount = 0,
                        paidAmount = 0,
                        dueAmount = 0;
                    var cashAmount = 0,
                        bankAmount = 0,
                        invoiceAmount = 0;

                    $('#table_id tbody tr').each(function() {
                        totalAmount += parseFloat($(this).find('td:eq(11)').text().replace(/,/g,
                            '')) || 0;
                        paidAmount += parseFloat($(this).find('td:eq(12)').text().replace(/,/g,
                            '')) || 0;
                        dueAmount += parseFloat($(this).find('td:eq(13)').text().replace(/,/g,
                            '')) || 0;
                        cashAmount += parseFloat($(this).find('td:eq(14)').text().replace(/,/g,
                            '')) || 0;
                        bankAmount += parseFloat($(this).find('td:eq(15)').text().replace(/,/g,
                            '')) || 0;
                        invoiceAmount += parseFloat($(this).find('td:eq(16)').text().replace(/,/g,
                            '')) || 0;
                    });

                    const fmt = v => v.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $('#total_amount_footer').html(fmt(totalAmount));
                    $('#paid_amount_footer').html(fmt(paidAmount));
                    $('#due_footer').html(fmt(dueAmount));
                    $('#cash_footer').html(fmt(cashAmount));
                    $('#bank_footer').html(fmt(bankAmount));
                    $('#invoice_footer').html(fmt(invoiceAmount));
                }

                updateFooter();

                // recalc on search, paging, ordering
                table.on('search.dt page.dt order.dt', function() {
                    updateFooter();
                });
            });
        });
    </script>
@endpush
