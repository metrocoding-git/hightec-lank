@extends('layouts.app')
@section('title', 'Invoice Payment Report')

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
                <h1>Invoice Payment Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">Invoice Payment</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box">
                <form method="GET" action="{{ route('reports.invoice-payments') }}" id="filter-form" class="mb-20">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Date Range</label>
                            <input type="text" id="date_range" name="date_range" class="form-control"
                                value="{{ $defaultFrom }} to {{ $defaultTo }}">
                        </div>
                        <div class="col-md-3">
                            <label>Branch</label>
                            <select name="branch_id" id="branch_id" class="form-control">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-control">
                                <option value="">All Methods</option>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method }}"
                                        {{ request('payment_method') == $method ? 'selected' : '' }}>
                                        {{ $method }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Search</label>
                            <input type="text" id="search_text" name="search_text" class="form-control"
                                placeholder="Receipt / Invoice / Student / Reg No"
                                value="{{ request('search_text') }}">
                        </div>
                        <div class="col-lg-12 mt-20 text-right">
                            <button type="button" id="search-btn" class="primary-btn small fix-gr-bg">
                                <span class="ti-search pr-2"></span> Search
                            </button>
                            <a href="{{ route('reports.invoice-payments') }}" class="primary-btn small fix-gr-bg text-white">
                                Reset
                            </a>
                            <button type="button" id="export-csv" class="primary-btn small fix-gr-bg">
                                <span class="ti-export pr-2"></span> Export CSV
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

                        <style>
                            tr.receipt-main-row {
                                background: #e8f0fe;
                            }

                            tr.receipt-main-row:hover {
                                background: #d2e3fc;
                            }

                            table.child-item-table {
                                width: 100%;
                            }

                            table.child-item-table tr.sub-header-row td {
                                background: #fff3cd;
                                font-weight: 600;
                                font-size: 0.8em;
                                color: #856404;
                                padding: 4px 8px;
                                border-top: 2px solid #ffc107;
                            }

                            table.child-item-table tr.sub-item-row td {
                                background: #f8f9fa;
                                font-size: 0.9em;
                                border-bottom: 1px solid #e9ecef;
                                padding: 4px 8px;
                            }
                        </style>

                        <x-table>
                            <table id="table_id" class="table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>                    {{-- 0 --}}
                                        <th>Receipt No</th>           {{-- 1 --}}
                                        <th>Created Date</th>         {{-- 2 --}}
                                        <th>Cheque No</th>            {{-- 3 --}}
                                        <th>Total Amount</th>         {{-- 4 --}}
                                        <th>Payment Method(s)</th>    {{-- 5 --}}
                                        <th>Note</th>                 {{-- 6 --}}
                                        <th>Created By</th>           {{-- 7 --}}
                                        <th>Inv. Payment No</th>      {{-- 8 --}}
                                        <th>Student Name</th>         {{-- 9 --}}
                                        <th>Reg. No</th>              {{-- 10 --}}
                                        <th>Branch</th>               {{-- 11 --}}
                                        <th>Actions</th>              {{-- 12 --}}
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </x-table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@include('components.partials.data_table_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')

@push('script')
    <script>
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: true,
            defaultDate: ["{{ $defaultFrom }}", "{{ $defaultTo }}"]
        });

        $(document).ready(function() {

            var LOG_PREFIX = '[InvoicePayments]';

            function currentFilters() {
                return {
                    date_range: $('#date_range').val(),
                    branch_id: $('#branch_id').val(),
                    payment_method: $('#payment_method').val(),
                    search_text: $('#search_text').val(),
                };
            }

            // Guard against double-initialisation (e.g. if a global layout
            // script also touches tables with class "table"). Without this,
            // the SECOND DataTable() call on the same element silently no-ops
            // and every future reload()/filter click does nothing.
            if ($.fn.DataTable.isDataTable('#table_id')) {
                $('#table_id').DataTable().destroy();
            }

            var table = $('#table_id').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                dom: 'lfrtip',
                paging: true,
                searching: false, // custom search field above replaces the built-in box
                ordering: true,
                info: true,
                scrollX: true,
                lengthChange: true,
                order: [
                    [2, 'desc']
                ],
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                pageLength: 25,
                ajax: {
                    url: "{{ route('reports.invoice-payments.data') }}",
                    data: function(d) {
                        var merged = $.extend({}, d, currentFilters());
                        console.debug(LOG_PREFIX, 'requesting', merged);
                        return merged;
                    },
                    error: function(xhr) {
                        console.error(LOG_PREFIX, 'ajax error', xhr.status, xhr.responseText);
                        alert('Failed to load report data (HTTP ' + xhr.status + '). Check the console for details.');
                    }
                },
                columns: [
                    { data: 'id', orderable: false },
                    { data: 'receipt_number' },
                    { data: 'created_date' },
                    { data: 'cheque_number' },
                    { data: 'total_amount' },
                    { data: 'payment_methods' },
                    { data: 'note' },
                    { data: 'created_by' },
                    { data: 'payment_invoice_number' },
                    { data: 'student_name' },
                    { data: 'admission_number' },
                    { data: 'branch_name' },
                    {
                        data: null,
                        orderable: false,
                        render: function(row) {
                            return '<button class="btn btn-xs btn-outline-primary toggle-items" data-receipt-id="' +
                                row.receipt_id + '"><i class="ti-angle-down"></i> ' + row.item_count + ' item(s)</button>';
                        }
                    }
                ],
                language: {
                    search: '<i class="fa-solid fa-magnifying-glass me-2"></i>',
                    searchPlaceholder: 'Search...',
                    paginate: {
                        previous: '<i class="fa-solid fa-angle-left"></i>',
                        next: '<i class="fa-solid fa-angle-right"></i>'
                    }
                }
            });

            // Single entry point for re-running the query, called from every
            // trigger below so there's exactly one code path to debug.
            function runSearch() {
                console.debug(LOG_PREFIX, 'runSearch triggered');
                table.ajax.reload(null, false); // false = stay on current page
            }

            // Prevent the <form> from ever doing a native GET navigation
            // (e.g. pressing Enter inside a text field still submits a form
            // by default even with no submit button focused).
            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                runSearch();
            });

            // Explicit click handler — does not depend on native form submit
            // behaviour at all, so it can't be swallowed by another script.
            $(document).on('click', '#search-btn', function(e) {
                e.preventDefault();
                runSearch();
            });

            $(document).on('change', '#branch_id, #payment_method', runSearch);

            $(document).on('keydown', '#search_text', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    runSearch();
                }
            });

            function formatChild(items) {
                let html = '<table class="child-item-table">';
                html += '<tr class="sub-header-row"><td>Item</td><td>Payment Receipt No</td><td>Main Invoice No</td><td>Paid Amount</td><td>Inv. Payment No</td><td>Method</td></tr>';
                items.forEach((item) => {
                    html += `<tr class="sub-item-row">
                        <td>#${item.item_no}</td>
                        <td>${item.receipt_no}</td>
                        <td>${item.main_invoice_no}</td>
                        <td><strong>${item.paid_amount}</strong></td>
                        <td>${item.inv_payment_no}</td>
                        <td>${item.payment_method}</td>
                    </tr>`;
                });
                html += '</table>';
                return html;
            }

            $('#table_id tbody').on('click', '.toggle-items', function(e) {
                e.stopPropagation();
                var button = $(this);
                var tr = button.closest('tr');
                var row = table.row(tr);
                var icon = button.find('i');
                var receiptId = button.data('receipt-id');

                if (row.child.isShown()) {
                    row.child.hide();
                    icon.removeClass('ti-angle-up').addClass('ti-angle-down');
                    return;
                }

                var itemsUrl = "{{ url('reports/invoice-payments') }}/" + receiptId + "/items";

                $.getJSON(itemsUrl, function(resp) {
                    row.child(formatChild(resp.items)).show();
                    icon.removeClass('ti-angle-down').addClass('ti-angle-up');
                });
            });

            $('#export-csv').on('click', function() {
                var params = $.param(currentFilters());
                window.location.href = "{{ route('reports.invoice-payments.export') }}?" + params;
            });

        });
    </script>
@endpush