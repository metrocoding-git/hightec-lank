@extends('layouts.app')
@section('title')
    @lang('finance.Invoice Payments')
@endsection

@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Invoice Method Payments</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Invoice Method Payments Confirm</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row align-items-center mb-3">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="col-lg-8 col-md-6">
                            <h3 class="mb-15">@lang('common.select_criteria')</h3>
                        </div>
                        <form class="form-horizontal" id="filter-form">
                            @csrf
                            <div class="row">
                                {{-- Branch Filter --}}
                                <div class="col-lg-12 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="branch_id">@lang('common.branch')</label>
                                        <select class="primary_select form-control" name="branch_id" id="branch-select">
                                            <option value="">Select Branch</option>
                                            @foreach ($branches ?? [] as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Payment Status Filter --}}
                                {{-- <div class="col-lg-6 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="payment_status">@lang('finance.payment_status')</label>
                                        <select class="primary_select form-control" name="payment_status"
                                            id="status-select">
                                            <option value="not_open" selected>Not Open</option>
                                            <option value="pending">Pending</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>
                                </div> --}}

                                <div class="col-lg-12 mt-1 text-right">
                                    <button type="button" class="primary-btn small fix-gr-bg" id="submit-search">
                                        <span class="ti-search pr-2"></span>@lang('common.search')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>


            {{-- Payments Table --}}
            <div class="row mt-40 full_wide_table">
                <div class="col-lg-12">

                    <div class="white-box">
                        <div class="col-lg-4 col-md-6 text-left text-sm-right mb-4 offset-lg-8 offset-md-6">
                            <button id="bulk-action-btn" class="primary-btn small">Bulk Update</button>
                        </div>

                        <div class="main-title mb-3">
                            <h3>@lang('finance.invoice_payment_list')</h3>
                        </div>

                        <x-table>
                            <table id="invoice_table"
                                class="table data-table Crm_table_active3 no-footer dtr-inline collapsed" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice Number</th>
                                        <th>Invoice Item Number</th>
                                        <th>amount</th>
                                        <th>Branch</th>
                                        <th>Student Name</th>
                                        <th>Stu Category name</th>
                                        <th>Stu Reg No</th>
                                        <th>Fee Name</th>
                                        <th>status</th>
                                        <th>date</th>

                                    </tr>
                                </thead>

                                <tfoot>
                                    <tr>
                                        <th colspan="3" style="text-align:right">Total:</th>
                                        <th id="total_amount_footer" style="text-align:center"></th>
                                        <th colspan="5"></th>

                                    </tr>
                                </tfoot>
                            </table>
                        </x-table>
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
                                <input type="number" class="form-control" id="bulk-amount" name="amount" step="0.01">
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
        $('#invoice_table').DataTable({
            responsive: false,
            scrollX: true,
            autoWidth: false,
            processing: true,
            searching: true,
            serverSide: true,
            ajax: {
                url: "{{ route('invoice-payments.not-start-datatable') }}",
                data: function(d) {
                    d.branch_id = $('#branch-select').val();
                }
            },
            dom: "<'d-flex justify-content-between align-items-center mb-2'<'me-2'l><'mx-auto'f><'ms-2'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'d-flex justify-content-between align-items-center mt-2'<'mb-0 col-4'i><'mb-0 col-8'p>>",
            buttons: ['pdf', 'csv', 'excel', 'print', 'colvis'],
            order: [
                [0, 'desc']
            ],
            columns: [{
                    data: 'select',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'invoice_no'
                },
                {
                    data: 'invoice_item_no'
                },
                {
                    data: 'amount'
                },
                {
                    data: 'branch',
                    searchable: false
                },
                {
                    data: 'student_name',
                    searchable: false
                },
                {
                    data: 'student_category',
                    searchable: false
                },
                {
                    data: 'admission_number',
                    searchable: false
                },
                {
                    data: 'fee_name',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'status',
                    searchable: false
                },
                {
                    data: 'date'
                }
            ],
        });

        let table = $('#invoice_table').DataTable();


        // ============ SUM FOOTER ============
        function updateFooter() {
            var totalAmount = 0;
            var paidAmount = 0;

            $('#invoice_table tbody tr').each(function() {
                var totalText = $(this).find('td:eq(3)').text().trim();

                // remove commas
                totalText = totalText.replace(/,/g, '');

                totalAmount += parseFloat(totalText) || 0;
            });

            $('#total_amount_footer').html(totalAmount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

        }

        // initial calculation
        updateFooter();

        // recalc on search, paging, ordering
        table.on('search.dt page.dt order.dt', function() {
            updateFooter();
        });

        $('#invoice_table_filter input')
            .off()
            .on('keyup', function() {
                clearTimeout($.data(this, 'timer'));
                let wait = setTimeout(() => {
                    table.search(this.value).draw();
                }, 500);
                $(this).data('timer', wait);
            });

        // Filter table on select change
        $('#submit-search').on('click', function() {
            $('#invoice_table').DataTable().ajax.reload();
        })

        let currentStatus = null;
        let currentStudent = null;

        $('#bulk-action-btn').on('click', function() {
            let selected = $('.payment-checkbox:checked');
            if (selected.length == 0) {
                showToast('Please select at least one payment.', 'warning');
                return;
            }

            let status = selected.first().data('status');
            let ids = selected.map(function() {
                return $(this).val();
            }).get();

            $('#bulk-payment-ids').val(ids.join(','));

            if (status === 'not_open') {
                // Show modal for customer name and note
                $('#customer-name-group, #note-group').show();
                $('#pending-fields').addClass('d-none');
                $('#bulkActionModal').modal('show');
            } else if (status === 'pending') {
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
            if (this.checked) {
                if (currentStatus && status !== currentStatus) {
                    showToast('You can only select payments with the same status!', 'warning');
                    this.checked = false;
                    return;
                }

                if (currentStudent && student != currentStudent) {
                    showToast('You can only select the same student for Not Open payments!', 'warning');
                    this.checked = false;
                    return;
                }
                currentStatus = status;
                currentStudent = student;
            } else {
                // If unchecked, check if any remain
                if ($('.payment-checkbox:checked').length === 0) {
                    currentStatus = null;
                    currentStudent = null;
                }
            }
        });


        $('#bulk-modal-submit').on('click', function(e) {
            e.preventDefault(); // prevent default form submit

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

            if (status === 'not_open') {
                data.customer_name = $('#bulk-customer-name').val();
                data.note = $('#bulk-note').val();
                if (!data.customer_name) {
                    showToast('Please enter customer name.', 'warning');
                    return;
                }
                var route = '{{ route('invoice-payments.bulk-not-open') }}';
            } else if (status === 'pending') {
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
                $('#bulkActionModal').modal('hide');
                $('#invoice_table').DataTable().ajax.reload();

                if (status === 'not_open') {
                    window.open('/student/fees/fee-invoice/invoice-payments/print/' + res.invoice_number,
                        '_blank');
                }
            });
        });
    </script>
@endpush
