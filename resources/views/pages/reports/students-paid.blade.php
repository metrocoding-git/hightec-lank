@extends('layouts.app')
@section('title')
    Students Paid Report
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Students Paid Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">Students Paid</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box">
                <div class="col-lg-12 text-right">
                    <a id="downloadExcelBtn" href="#" class="primary-btn small fix-gr-bg">
                        <span class="ti-download pr-2"></span> Download Excel
                    </a>
                </div>
                <form method="GET" action="" class="mb-20" id="filterForm">
                    <div class="row">

                        {{-- Date Range --}}
                        <div class="col-md-6">
                            <label>Date Range</label>
                            <input type="text" id="date_range" name="date_range" class="form-control"
                                value="{{ request('date_range') }}">
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-md-6">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control" id="payment_method">
                                <option value="">All</option>
                                <option value="Cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>Cash
                                </option>
                                <option value="Bank Transfer"
                                    {{ request('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer
                                </option>
                                <option value="Invoice" {{ request('payment_method') == 'Invoice' ? 'selected' : '' }}>
                                    Invoice
                                </option>
                            </select>
                        </div>

                        {{-- Branch --}}
                        <div class="col-md-6">
                            <label>Branch</label>
                            <select name="branch_id" class="form-control" id="branch_id">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Fee Type --}}
                        <div class="col-md-6">
                            <label>Fee Type</label>
                            <select name="payment_type" class="form-control" id="payment_type">
                                <option value="">All</option>
                                <option value="course_fee" {{ request('payment_type') == 'course_fee' ? 'selected' : '' }}>
                                    Course & Fee
                                </option>
                                <option value="nvq" {{ request('payment_type') == 'nvq' ? 'selected' : '' }}>NVQ</option>
                                <option value="item_sell" {{ request('payment_type') == 'item_sell' ? 'selected' : '' }}>
                                    Item Sell</option>
                                <option value="food" {{ request('payment_type') == 'food' ? 'selected' : '' }}>
                                    Food fee</option>
                                <option value="others" {{ request('payment_type') == 'others' ? 'selected' : '' }}>Others
                                </option>
                            </select>
                        </div>


                        <div class="col-lg-12 mt-20 text-right">
                            <button type="submit" class="primary-btn small fix-gr-bg" id="searchBtn">
                                <span class="ti-search pr-2"></span> Search
                            </button>

                            {{-- RESET --}}
                            <button type="reset" class="primary-btn small fix-gr-bg">
                                <a href="{{ route('reports.students-paid') }}" class="text-white">
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
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">Payments Report</h3>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id" class="table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Stu No</th>
                                                <th>Stu Category</th>
                                                <th>Paid Branch</th>
                                                <th>Invoice Number</th>
                                                <th>Receipt Number</th>
                                                <th>Payment Name</th>
                                                <th>Paid Amount</th>
                                                <th>Payment Method</th>
                                                <th>Payment Date</th>
                                                <th>Payment Invoice Number</th>
                                                <th>Bank Payment Amount</th>
                                                <th>Bank Payment Date</th>
                                                <th>Note</th>
                                                <th>Create User</th>
                                                <th>Invoice Payment Status</th>
                                                <th>Invoice Payment Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Server-side processing will populate this --}}
                                        </tbody>
                                    </table>
                                </x-table>
                            </div>
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
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: true,
        });
    </script>

    <script>
        $('#downloadExcelBtn').on('click', function(e) {
            e.preventDefault();

            let params = new URLSearchParams({
                date_range: $('#date_range').val(),
                payment_method: $('#payment_method').val(),
                branch_id: $('#branch_id').val(),
                payment_type: $('#payment_type').val(),
                export: 'excel', // flag so controller knows it's an export
            });

            window.location.href = "{{ route('reports.students-paid') }}?" + params.toString();
        });
        let table = $('#table_id').DataTable({
            responsive: true,
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

            // SERVER-SIDE PROCESSING
            serverSide: true,
            ajax: {
                url: "{{ route('reports.students-paid') }}",
                type: "GET",
                data: function(d) {
                    d.date_range = $('#date_range').val();
                    d.payment_method = $('#payment_method').val();
                    d.branch_id = $('#branch_id').val();
                    d.payment_type = $('#payment_type').val();
                }
            },

            columns: [{
                    data: 'id'
                },
                {
                    data: 'stu_no'
                },
                {
                    data: 'stu_category'
                },
                {
                    data: 'paid_branch'
                },
                {
                    data: 'invoice_number'
                },
                {
                    data: 'receipt_number'
                },
                {
                    data: 'payment_name'
                },
                {
                    data: 'paid_amount'
                },
                {
                    data: 'payment_method'
                },
                {
                    data: 'payment_date'
                },
                {
                    data: 'payment_invoice_number'
                },
                {
                    data: 'bank_payment_amount'
                },
                {
                    data: 'bank_payment_date'
                },
                {
                    data: 'notes'
                },
                {
                    data: 'create_user'
                },
                {
                    data: 'invoice_payment_status'
                },
                {
                    data: 'invoice_payment_amount'
                }
            ],

            columnDefs: [{
                targets: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15], // Paid Amount column
                render: function(data, type, row) {
                    return row.amount_is_negative ?
                        '<span class="text-danger">' + data + '</span>' :
                        data;
                }
            }],

            paging: true,
            searching: true,
            ordering: true,
            info: true,
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

        // Set flag when search button is clicked
        $('#searchBtn').on('click', function(e) {
            e.preventDefault();

            table.ajax.reload(null, true); // reload and reset paging
        });

        function getFileName() {
            let now = new Date();
            let date = now.toISOString().slice(0, 10);
            let time = now.toTimeString().slice(0, 8).replace(/:/g, '-');
            return 'Students-Payment-Report_' + date + '__' + time;
        }
    </script>
@endpush
