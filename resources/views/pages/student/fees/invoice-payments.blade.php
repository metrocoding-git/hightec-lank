@extends('layouts.app')
@push('css')
    <style>
        .white-box {
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
        }

        .white-box .main-title h3,
        .white-box .main-title h5 {
            font-weight: 700;
            position: relative;
            padding-bottom: 10px;
        }

        .white-box .main-title h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 42px;
            height: 3px;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2));
        }


        .record-count-badge {
            font-size: 12px;
            font-weight: 600;
            color: var(--gradient_1);
            background: rgba(0, 0, 0, 0.04);
            border-radius: 999px;
            padding: 5px 14px;
            white-space: nowrap;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
            white-space: nowrap;
            line-height: 1.4;
        }

        .status-badge.status-pending {
            background: rgba(255, 159, 28, 0.14);
            color: #b3720a;
        }

        .status-badge.status-completed {
            background: rgba(40, 167, 69, 0.14);
            color: #1e7e34;
        }

        .status-badge.status-not_open {
            background: rgba(108, 117, 125, 0.14);
            color: #5a6268;
        }

        .status-badge.status-default {
            background: rgba(0, 0, 0, 0.06);
            color: #495057;
        }


        .custom-checkbox input.payment-checkbox {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            margin: 0;
        }

        .custom-checkbox-indicator {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 2px solid #c7cbd6;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color .15s ease, border-color .15s ease;
        }

        .custom-checkbox-indicator::after {
            content: '';
            width: 6px;
            height: 11px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg) scale(0);
            transition: transform .12s ease;
            margin-top: -2px;
        }

        .custom-checkbox input.payment-checkbox:checked+.custom-checkbox-indicator {
            background: linear-gradient(135deg, var(--gradient_1), var(--gradient_2));
            border-color: transparent;
        }

        .custom-checkbox input.payment-checkbox:checked+.custom-checkbox-indicator::after {
            transform: rotate(45deg) scale(1);
        }

        .custom-checkbox input.payment-checkbox:focus-visible+.custom-checkbox-indicator {
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.08);
        }

        .custom-checkbox input.payment-checkbox:disabled+.custom-checkbox-indicator {
            opacity: .5;
            cursor: not-allowed;
        }
    </style>
@endpush
@section('title')
    Invoice Payments Pending
@endsection

@section('mainContent')
    @php
        // Permission keys from config_permissions.php ("Fees" category, Payment for Credit Invoice window).
        $canUpdateInvoicePayment = Auth::user()->hasPermission('invoice-payments.bulk-pending');
        $canBackInvoicePayment = Auth::user()->hasPermission('invoice-payments.bulk-back');
    @endphp
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Invoice Method Payments</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Invoice Method Payments</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">

            <div class="row align-items-center mb-20">
                <div class="col-lg-12">

                    <div class="white-box">
                        <form class="form-horizontal mb-0" id="filter-form">
                            @csrf
                            <div class="row">
                                {{-- Date Range Filter --}}
                                <div class="col-lg-4">
                                        <input type="text" class="form-control" id="date_range" name="date_range"
                                            placeholder="Select date range">
                                </div>

                                {{-- Branch Filter --}}
                                <div class="col-lg-4">
                                    <div class="primary_input">
                                        <select class="form-control" name="branch_id" id="branch-select">
                                            <option value="">Select Branch</option>
                                            @foreach ($branches ?? [] as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Payment Status Filter --}}
                                <div class="col-lg-4">
                                    <div class="primary_input">
                                        <select class="form-control" name="payment_status"
                                            id="status-select">
                                            <option value="all" selected>All</option>
                                            <option value="pending">Pending</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-10 text-right">
                                    <button type="button" class="primary-btn small fix-gr-bg col-12 p-2"
                                        id="submit-search">
                                        <span class="ti-search pr-2"></span>@lang('common.search')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if ($canUpdateInvoicePayment || $canBackInvoicePayment)
                <div class="white-box">
                    <div class="col-12">
                        <div class="row align-items-center mb-10 ">

                            @if ($canUpdateInvoicePayment)
                                <div @class([
                                    'col-12' => !$canBackInvoicePayment,
                                    'col-lg-6' => $canBackInvoicePayment,
                                ])> <button id="bulk-action-btn"
                                        class="primary-btn small fix-gr-bg col-12 p-2"> <span
                                            class="ti-check pr-2"></span>Payment Update </button> </div>
                            @endif

                            @if ($canBackInvoicePayment)
                                <div @class([
                                    'col-12' => !$canUpdateInvoicePayment,
                                    'col-lg-6' => $canUpdateInvoicePayment,
                                ])> <button id="bulk-back-btn"
                                        class="small primary-btn btn-warning col-12 p-2"> <span
                                            class="ti-arrow-left pr-2"></span>Back </button> </div>
                            @endif

                        </div>

                    </div>
                </div>
            @endif


            <div class="row mt-10 full_wide_table">
                <div class="col-lg-12">
                    <div class="white-box">

                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">Invoice Method Payments List</h3>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id"
                                        class="table data-table Crm_table_active3 no-footer dtr-inline collapsed"
                                        cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Invoice Receipt No</th>
                                                <th>Customer</th>
                                                <th>Branch</th>
                                                <th>Student Name</th>
                                                <th>Student Category</th>
                                                <th>Admission Number</th>
                                                <th>Total Amount</th>
                                                <th>Confirmed</th>
                                                <th>Status</th>
                                                <th>Create Date</th>
                                                <th>Complete date</th>

                                                <th>Note</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                    </table>
                                </x-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Bulk Update Modal -->
    <div class="modal fade admin-query" id="bulkActionModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bulk Update Payments</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="bulk-action-form">
                        <input type="hidden" name="payment_ids" id="bulk-payment-ids">
                        <div class="form-group" id="customer-name-group">
                            <label for="bulk-customer-name">Customer Name</label>
                            <input type="text" class="form-control" id="bulk-customer-name" name="customer_name"
                                required>
                        </div>
                        <div class="form-group" id="note-group">
                            <label for="bulk-note">Note</label>
                            <textarea class="form-control" id="bulk-note" name="note"></textarea>
                        </div>
                        <div id="pending-fields" class="d-none">
                            <div class="form-group">
                                <label>Cheque Number</label>
                                <input type="text" class="form-control" id="bulk-cheque-number" name="cheque_number">
                            </div>

                            <div class="form-group">
                                <label>Amount</label>
                                <input type="number" class="form-control" id="bulk-amount" name="amount"
                                    step="0.01">
                            </div>

                            <div class="form-group">
                                <label>Note</label>
                                <textarea class="form-control" id="bulk-confirm-note" name="confirm_note"></textarea>
                            </div>
                            {{-- <p id="confirm-text" class="text-center d-none">Are you sure you want to confirm these pending
                                payments?</p> --}}
                        </div>

                        <div class="mt-3 text-center">
                            <button type="submit" class="btn btn-primary col-12" id="bulk-modal-submit">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast container -->
    <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 1080;">
        <div id="bulk-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
@endsection

@include('components.partials.data_table_js')
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')

@push('script')
    <script>
        $(document).ready(function() {

            // Initialize date range picker
            flatpickr("#date_range", {
                mode: "range",
                dateFormat: "Y-m-d",
                allowInput: true,
                defaultDate: [
                "{{ \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}",
                "{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
            ]
            });

            function showToast(message, type = 'success') {
                let toastEl = $('#bulk-toast');
                toastEl.removeClass('text-bg-success text-bg-danger text-bg-warning');

                // Set type: success / danger / warning
                if (type === 'success') toastEl.addClass('text-bg-success');
                if (type === 'error') toastEl.addClass('text-bg-danger');
                if (type === 'warning') toastEl.addClass('text-bg-warning');

                toastEl.find('.toast-body').text(message);

                // Show the toast
                let toast = new bootstrap.Toast(toastEl[0], {
                    delay: 3000
                });
                toast.show();
            }

            // Toggle all checkboxes
            $('#select-all').on('click', function() {
                $('.payment-checkbox').prop('checked', this.checked);
            });

            $('#table_id').DataTable({

                processing: true,
                serverSide: true,
                scrollX: true,

                dom: "<'d-flex justify-content-between align-items-center mb-2'<'me-2'l><'mx-auto'f><'ms-2'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'d-flex justify-content-between align-items-center mt-2'<'mb-0 col-4'i><'mb-0 col-8'p>>",

                lengthChange: true,
                buttons: [{
                        extend: 'pdf',
                        title: getFileName(),
                        text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        title: getFileName(),
                        text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        title: getFileName(),
                        text: '<i title="export to excel" class="fa fa-file-excel-o"></i>',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        title: getFileName(),
                        text: '<i title="print" class="fa fa-print"></i>',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'colvis',
                        title: getFileName(),
                        text: '<i title="column visibility" class="fa fa-eye"></i>',
                        columns: ':gt(0)'
                    }
                ],
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                order: [
                    [0, 'desc']
                ],

                ajax: {
                    url: "{{ route('invoice-payments-datatable') }}",
                    data: function(d) {
                        d.branch_id = $('#branch-select').val();
                        d.date_range = $('#date_range').val();
                        d.payment_status = $('#status-select').val();
                    }
                },

                columnDefs: [{
                    targets: 9,
                    render: function(data) {
                        if (!data) {
                            return data;
                        }
                        var key = String(data).toLowerCase().trim().replace(/\s+/g, '_');
                        var known = ['pending', 'completed', 'not_open'];
                        var cls = known.indexOf(key) !== -1 ? 'status-' + key :
                            'status-default';
                        return '<span class="status-badge ' + cls + '">' + data + '</span>';
                    }
                }],
                columns: [{
                        data: 'select',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'invoice_receipt_no'
                    },
                    {
                        data: 'customer_name'
                    },
                    {
                        data: 'branch',
                        searchable: true
                    },
                    {
                        data: 'student_name',
                        searchable: true
                    },
                    {
                        data: 'student_category',
                        searchable: true
                    },
                    {
                        data: 'admission_number',
                        searchable: true
                    },
                    {
                        data: 'amount'
                    },
                    {
                        data: 'confirm_amount'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'create_date'
                    },
                    {
                        data: 'complete_date'
                    },
                    {
                        data: 'note'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: true
                    }
                ]
            });

            let table = $('#table_id').DataTable();

            $('#submit-search').on('click', function() {
                $('#table_id').DataTable().ajax.reload();
            })

            // Reset filter
            $('#reset-filter').on('click', function() {
                $('#date_range').val('');
                $('#branch-select').val('');
                $('#status-select').val('all');
                $('#table_id').DataTable().ajax.reload();
            })

            let currentStatus = null;
            let currentStudent = null;
            $('#bulk-action-btn').on('click', function() {
                let selected = $('.payment-checkbox:checked');
                if (selected.length == 0) {
                    showToast('Please select at least one payment.', 'warning');
                    return;
                }

                let ids = selected.map(function() {
                    return $(this).val();
                }).get();

                $('#bulk-payment-ids').val(ids.join(','));

                let status = selected.first().data('status');
                if (status === 'pending') {
                    // Show modal to confirm pending payments
                    $('#customer-name-group, #note-group').hide();
                    $('#pending-fields').removeClass('d-none');
                    $('#bulkActionModal').modal('show');
                } else {
                    showToast('Cannot bulk update completed payments.', 'warning');
                }
            });


            $(document).on('change', '.payment-checkbox', function() {
                let status = $(this).data('status');
                let student = $(this).data('student');
                let selected = $('.payment-checkbox:checked');


                if (this.checked) {
                    if (selected.length == 1) {
                        if (currentStatus && status !== currentStatus) {
                            showToast('You can only select payments with the same status!', 'warning');
                            this.checked = false;
                            return;
                        }

                        if (currentStudent && student != currentStudent) {
                            showToast('You can only select the same student for Not Open payments!',
                                'warning');
                            this.checked = false;
                            return;
                        }
                        currentStatus = status;
                        currentStudent = student;
                    } else {
                        showToast('You can only select one payment at a time!', 'warning');
                    }

                } else {
                    // If unchecked, check if any remain
                    if ($('.payment-checkbox:checked').length === 0) {
                        currentStatus = null;
                        currentStudent = null;
                    }
                }
            });

            $(document).on('change', '.payment-checkbox', function() {

                // If this checkbox is checked, uncheck all others
                if (this.checked) {
                    $('.payment-checkbox').not(this).prop('checked', false);
                }

                // Reset current tracking
                let selected = $('.payment-checkbox:checked');

                if (selected.length > 0) {
                    currentStatus = selected.first().data('status');
                    currentStudent = selected.first().data('student');
                } else {
                    currentStatus = null;
                    currentStudent = null;
                }
            });


            // =========================
            // SUBMIT BULK
            // =========================

            $('#bulk-modal-submit').on('click', function(e) {
                e.preventDefault(); // prevent default form submit

                let selected = $('.payment-checkbox:checked').first();
                let total = parseFloat(selected.data('total'));
                let confirm = parseFloat(selected.data('confirm')) ? parseFloat(selected.data('confirm')) :
                    0;
                let amount = parseFloat($('#bulk-amount').val());

                let maxAllowed = total - confirm;

                console.log('Total:', total, 'Confirmed:', confirm, 'Max Allowed:', maxAllowed,
                    'Entered Amount:',
                    amount);

                if (amount > maxAllowed) {
                    showToast('Amount cannot be greater than remaining balance: ' + maxAllowed.toFixed(
                        2), 'warning');
                    return;
                }

                // Get all selected payment IDs
                let paymentIds = $('#bulk-payment-ids').val().split(',');
                if (paymentIds.length === 0) {
                    showToast('No payments selected!', 'warning');
                    return;
                }

                // Determine current bulk status
                let status = $('.payment-checkbox:checked').first().data('status');

                // Prepare POST data
                let data = {
                    payment_ids: paymentIds,
                    _token: '{{ csrf_token() }}'
                };

                if (status === 'pending') {
                    data.cheque_number = $('#bulk-cheque-number').val();
                    data.amount = $('#bulk-amount').val();
                    data.note = $('#bulk-confirm-note').val();

                    if (!data.amount) {
                        showToast('Please enter amount.', 'warning');
                        return;
                    }
                    var route = '{{ route('invoice-payments.bulk-pending') }}';
                } else {
                    showToast('Cannot update completed payments.', 'warning');
                    return;
                }

                // Send AJAX POST
                $.post(route, data, function(res) {
                    showToast(res.message, 'success');
                    currentStatus = null;
                    currentStudent = null;

                    $('#bulk-action-form')[0].reset();

                    $('#bulk-payment-ids').val('');

                    $('.payment-checkbox').prop('checked', false);
                    $('#select-all').prop('checked', false);

                    $('#bulkActionModal').modal('hide');

                    $('#table_id').DataTable().ajax.reload();


                    $('#bulkActionModal').modal('hide');
                    $('#table_id').DataTable().ajax.reload();


                    $('#bulkActionModal').modal('hide');
                    $('#table_id').DataTable().ajax.reload();

                    if (status === 'not_open') {
                        window.open('/student/fees/fee-invoice/invoice-payments/print/' + res
                            .invoice_number,
                            '_blank');
                    }
                });
            });



            $('#bulk-back-btn').on('click', function() {
                let selected = $('.payment-checkbox:checked');

                if (selected.length == 0) {
                    showToast('Please select at least one payment.', 'warning');
                    return;
                }

                let status = selected.first().data('status');
                console.log('sss', status)
                let ids = selected.map(function() {
                    return $(this).val();
                }).get();

                if (status === 'not_open') {
                    showToast('Already in Not Open state.', 'warning');
                    return;
                }

                if (!confirm('Are you sure you want to revert selected payments?')) return;

                $.post("{{ route('invoice-payments.bulk-back') }}", {
                    payment_ids: ids,
                    status: status,
                    _token: '{{ csrf_token() }}'
                }, function(res) {
                    showToast(res.message, 'success');
                    $('#table_id').DataTable().ajax.reload();
                    currentStatus = null;
                });
            });




        });
    </script>
@endpush
