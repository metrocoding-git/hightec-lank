@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    @push('css')
        <link rel="stylesheet" href="{{ url('Modules\Fees\Resources\assets\css\feesStyle.css') }}" />
    @endpush

    @push('css')
        <style>
            div#table_id_wrapper {
                margin-top: 40px;
            }
        </style>
    @endpush

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Fees Invoice List</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees')</a>
                    <a href="#">@lang('fees::feesModule.fees_invoice')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box">

                <div class="row">
                    <div class="col-lg-12 text-left col-md-12">
                        <a href="{{ route('fees.add-invoice') }}" class="primary-btn small fix-gr-bg">
                            <span class="ti-plus pr-2"></span>
                            @lang('common.add')
                        </a>
                    </div>
                </div>

                <div class="row mt-40">


                    <div class="col-lg-12 student-details up_admin_visitor mt-0">

                        <div class="tab-content">
                            {{-- @foreach ($records as $key => $record) --}}
                            <div role="tabpanel">
                                <x-table>
                                    <table id="table_id" class="table" cellspacing="0" width="100%">

                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>@lang('common.student')</th>
                                                <th>Branch</th>
                                                <th>@lang('admin.admission_no')</th>
                                                <th>@lang('accounts.amount')</th>
                                                <th>@lang('fees.paid')</th>
                                                <th>@lang('accounts.balance')</th>
                                                <th>Payment Method</th>
                                                <th>Payment Status</th>
                                                <th>Payment Date</th>
                                                <th>Due Date</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>


                                        <tbody>
                                            @foreach ($fee_invoices as $key => $invoice)
                                                @php
                                                    $amount = $invoice->total_amount;
                                                    $paid_amount = $invoice->paid_amount;
                                                    $balance = $amount - $paid_amount;
                                                @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        <a href="{{ route('fees.fee-invoice-view', ['id' => $invoice->id, 'state' => 'view']) }}"
                                                            target="_blank">
                                                            {{ $invoice->student->student_name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $invoice->branch->name }}</td>
                                                    <td>{{ $invoice->student->admission_number }}</td>

                                                    <td>{{ $amount }}</td>
                                                    <td>{{ $paid_amount }}</td>
                                                    <td>{{ $balance }}</td>
                                                    <td>{{ $invoice->payment_method }}</td>

                                                    <td>
                                                        @if ($invoice->payment_status == 'partial')
                                                            <button
                                                                class="primary-btn small bg-warning text-white border-0">@lang('fees.partial')</button>
                                                        @elseif ($invoice->payment_status == 'full')
                                                            <button
                                                                class="primary-btn small bg-success text-white border-0">@lang('fees.paid')</button>
                                                        @elseif ($invoice->payment_status == 'not')
                                                            <button
                                                                class="primary-btn small bg-danger text-white border-0">@lang('fees.unpaid')</button>
                                                        @endif

                                                    </td>
                                                    <td>{{ $invoice->create_date }}</td>
                                                    <td>{{ $invoice->due_date }}</td>
                                                    <td>
                                                        <x-drop-down>
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#viewInvoiceModal{{ $invoice->id }}"
                                                                href="#">@lang('common.view')</a>
                                                            <a class="dropdown-item"
                                                                href="{{ route('fees.fee-invoice-edit', $invoice->id) }}">@lang('common.edit')</a>
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#makeInvoiceModal"
                                                                data-student-id="{{ $invoice->student->id }}"
                                                                data-invoice-id="{{ $invoice->id }}"
                                                                onclick="populateInvoiceModal(this)">Make Invoice</a>
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#deletecategoryModal{{ $invoice->id }}"
                                                                href="#">@lang('common.delete')</a>
                                                            @if ($invoice->payment_status == 'full')
                                                                <a class="dropdown-item" data-toggle="modal"
                                                                    data-target="#returnFeeModal{{ $invoice->id }}"
                                                                    href="#">Return</a>
                                                            @endif
                                                        </x-drop-down>
                                                    </td>
                                                </tr>

                                                <div class="modal fade admin-query"
                                                    id="deletecategoryModal{{ $invoice->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">@lang('fees.delete_fees_type')</h4>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="text-center">
                                                                    <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                                </div>
                                                                <div class="mt-40 d-flex justify-content-between">
                                                                    <button type="button" class="primary-btn tr-bg"
                                                                        data-dismiss="modal">
                                                                        @lang('common.cancel')
                                                                    </button>
                                                                    <form
                                                                        action="{{ route('fees.fee-delete', $invoice->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">
                                                                            @lang('common.delete')
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Add Payment Modal -->
                                            @endforeach
                                        </tbody>
                                    </table>
                                </x-table>
                            </div>

                            {{-- @endforeach --}}

                            <!-- Return Fee Modals -->
                            @foreach ($fee_invoices as $invoice)
                                @php
                                    $amount = $invoice->total_amount;
                                    $paid_amount = $invoice->paid_amount;
                                    $balance = $amount - $paid_amount;
                                @endphp
                                @if ($balance > 0)
                                    <div class="modal fade admin-query" id="returnFeeModal{{ $invoice->id }}">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">@lang('fees.return_fee') - Invoice
                                                        #{{ $invoice->id }}</h4>
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                </div>
                                                <form action="{{ route('return-fees.store') }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>@lang('common.student')</label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $invoice->student->student_name }}"
                                                                        readonly>
                                                                    <input type="hidden" name="student_id"
                                                                        value="{{ $invoice->student->id }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>@lang('admin.admission_no')</label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $invoice->student->admission_number }}"
                                                                        readonly>
                                                                    <input type="hidden" name="admission_number"
                                                                        value="{{ $invoice->student->admission_number }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Father Name</label>
                                                                    <input type="text" class="form-control"
                                                                        name="father_name"
                                                                        value="{{ $invoice->student->guardians_name ?? '' }}"
                                                                        required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>@lang('accounts.balance')</label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{ number_format($balance, 2) }}" readonly>
                                                                    <input type="hidden" name="balance"
                                                                        value="{{ $balance }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>@lang('fees.return_amount') <span
                                                                            class="text-danger">*</span></label>
                                                                    <div class="input-group">
                                                                        <input type="number" name="return_amount"
                                                                            class="form-control" min="0.01"
                                                                            max="{{ $balance }}" step="0.01"
                                                                            required placeholder="Enter return amount">
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text">.00</span>
                                                                        </div>
                                                                    </div>
                                                                    <small class="text-muted">Maximum return amount:
                                                                        {{ number_format($balance, 2) }}</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Role</label>
                                                                    <select name="role_id" class="form-control" required>
                                                                        <option value="">Select Role</option>
                                                                        @foreach ($roles as $role)
                                                                            <option value="{{ $role->id }}">
                                                                                {{ $role->role_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>@lang('fees.short_note')</label>
                                                            <textarea name="short_note" class="form-control" rows="3" placeholder="Enter reason for return..."></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <div class="form-check">
                                                                <input type="checkbox" name="status" value="1"
                                                                    class="form-check-input"
                                                                    id="statusCheck{{ $invoice->id }}" checked>
                                                                <label class="form-check-label"
                                                                    for="statusCheck{{ $invoice->id }}">
                                                                    Active
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ $invoice->id }}">
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="primary-btn tr-bg"
                                                            data-dismiss="modal">@lang('common.cancel')</button>
                                                        <button type="submit"
                                                            class="primary-btn fix-gr-bg">Return</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <!-- Make Invoice Modal -->
                            <div class="modal fade admin-query" id="makeInvoiceModal" tabindex="-1" role="dialog"
                                aria-labelledby="makeInvoiceModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="makeInvoiceModalLabel">Create New Invoice</h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="invoice-container" id="invoicePreview"
                                                style="background: rgb(248, 250, 253); padding: 20px; font-family: Arial, sans-serif;">
                                                <div class="invoice">
                                                    <div class="header"
                                                        style="display: flex; align-items: center; border-bottom: 2px solid #91c4eb; background-color: #a7d0f0; padding: 15px; margin-bottom: 15px;">
                                                        <div class="logo" style="width: 120px;">
                                                            <img src="https://highteclanka.com/static/33163709fc5bc293bd2c78a319b64f22/e27c8/Hoghtec-Logo.webp"
                                                                alt="Logo" style="max-width: 100%;">

                                                            {{-- <img src="{{ asset('images/logo.png') }}" alt="Logo"
                                                                style="max-width: 100%;"> --}}
                                                        </div>
                                                        <div class="company p-4"
                                                            style="flex: 1; text-align: center; color: #fff;">
                                                            <h2
                                                                style="margin: 0; color: #000000; font-size: 26px; font-weight: bold;">
                                                                HIGHTEC LANKA INTERNATIONAL
                                                            </h2>
                                                            <p style="margin: 4px 0; font-size: 16px; color: #000000;">
                                                                Vocational &
                                                                Technical Training Institute (Pvt) Ltd.</p>
                                                            <p style="margin: 4px 0; font-size: 16px; color: #000000;">
                                                                No.549,
                                                                Kurunegala-Kandy Road, Mawatagama, Sri Lanka.</p>
                                                            <p style="margin: 4px 0; font-size: 16px; color: #070000;">Tel:
                                                                +94 37 22 97
                                                                999 | +94 37 22 99 744</p>
                                                            <p style="margin: 4px 0; font-size: 16px; color: #000000;">
                                                                https://highteclanka.com | info@highteclanka.com</p>
                                                        </div>
                                                    </div>
                                                    <div class="p-2 mb-4"
                                                        style="color: #fff; background-color: #000; align-items: center; text-align: center;font-size: 16px; font-weight: bold;">
                                                        RECEPIT</div>


                                                    <div class="section" style="margin-bottom: 15px;">
                                                        <label
                                                            style="display: inline-block; width: 150px; font-weight: bold; font-size: 14px;">Course
                                                            Name:</label>
                                                        <span id="courseName">__________________________</span> <br>
                                                        <label
                                                            style="display: inline-block; width: 150px; font-weight: bold; font-size: 14px;">Date:</label>
                                                        <span id="invoiceDate">{{ date('Y-m-d') }}</span> <br>
                                                        <label
                                                            style="display: inline-block; width: 150px; font-weight: bold; font-size: 14px;">Receipt
                                                            No:</label>
                                                        <span id="receiptNo">__________________________</span> <br>
                                                        <label
                                                            style="display: inline-block; width: 150px; font-weight: bold; font-size: 14px;">Student
                                                            Reg. No:</label>
                                                        <span id="studentRegNo">__________________________</span>
                                                    </div>

                                                    <table class="details"
                                                        style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                                                        <tr>
                                                            <td
                                                                style="border: 1px solid #000; padding: 8px; font-size: 14px;">
                                                                <strong>Name:</strong>
                                                            </td>
                                                            <td style="border: 1px solid #000; padding: 8px; font-size: 14px;"
                                                                id="studentName"></td>
                                                        </tr>
                                                        <tr>

                                                            <td
                                                                style="border: 1px solid #000; padding: 8px; font-size: 14px;">
                                                                <strong>NIC No.:</strong>
                                                            </td>
                                                            <td style="border: 1px solid #000; padding: 8px; font-size: 14px;"
                                                                id="studentNic"></td>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style="border: 1px solid #000; padding: 8px; font-size: 14px;">
                                                                <strong>Address:</strong>
                                                            </td>
                                                            <td style="border: 1px solid #000; padding: 8px; font-size: 14px;"
                                                                colspan="3" id="studentAddress"></td>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style="border: 1px solid #000; padding: 8px; font-size: 14px;">
                                                                <strong>Contact No.:</strong>
                                                            </td>
                                                            <td style="border: 1px solid #000; padding: 8px; font-size: 14px;"
                                                                colspan="3" id="studentContact"></td>
                                                        </tr>
                                                    </table>

                                                    <table class="payment"
                                                        style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                                                        <tr>
                                                            <td style="border: 1px solid #000; padding: 8px; font-size: 14px; font-weight: bold; background: #f1f1f1; width: 200px;"
                                                                class="label">Total Payment</td>
                                                            <td style="border: 1px solid #000; padding: 8px; font-size: 14px;"
                                                                id="totalPayment"></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="border: 1px solid #000; padding: 8px; font-size: 14px; font-weight: bold; background: #f1f1f1;"
                                                                class="label">Advance Paid</td>
                                                            <td style="border: 1px solid #000; padding: 8px; font-size: 14px;"
                                                                id="advancePaid"></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="border: 1px solid #000; padding: 8px; font-size: 14px; font-weight: bold; background: #f1f1f1;"
                                                                class="label">Balance</td>
                                                            <td style="border: 1px solid #000; padding: 8px; font-size: 14px;"
                                                                id="balanceAmount"></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="border: 1px solid #000; padding: 8px; font-size: 14px; font-weight: bold; background: #f1f1f1;"
                                                                class="label">Due Date</td>
                                                            <td style="border: 1px solid #000; padding: 8px; font-size: 14px;"
                                                                id="dueDate"></td>
                                                        </tr>
                                                    </table>

                                                    <div class="footer"
                                                        style="margin-top: 25px; display: flex; justify-content: space-between;">
                                                        <div
                                                            style="width: 45%; border-top: 1px solid #000; text-align: center; padding-top: 5px; font-size: 14px;">
                                                            Authorized Officer</div>
                                                        <div
                                                            style="width: 45%; border-top: 1px solid #000; text-align: center; padding-top: 5px; font-size: 14px;">
                                                            Paid by Signature</div>
                                                    </div>

                                                    <p class="note"
                                                        style="font-size: 12px; margin-top: 10px; color: #333; border-top: 1px solid #0b4e82; padding-top: 8px;">
                                                        (Please note that the course fee will not be refunded)</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="primary-btn tr-bg"
                                                data-dismiss="modal">Close</button>
                                            <button type="button" class="primary-btn fix-gr-bg"
                                                onclick="printInvoice()">Print Invoice</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- View Invoice Modals -->
                            @foreach ($fee_invoices as $invoice)
                                @php
                                    $amount = $invoice->total_amount;
                                    $paid_amount = $invoice->paid_amount;
                                    $balance = $amount - $paid_amount;
                                @endphp
                                <div class="modal fade admin-query" id="viewInvoiceModal{{ $invoice->id }}"
                                    tabindex="-1" role="dialog"
                                    aria-labelledby="viewInvoiceModalLabel{{ $invoice->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="viewInvoiceModalLabel{{ $invoice->id }}">
                                                    View Invoice - Invoice #{{ $invoice->id }}</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body" id="invoiceContent{{ $invoice->id }}">
                                                <!-- Content will be loaded via AJAX -->
                                                <div class="text-center">
                                                    <div class="spinner-border" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                    <p>Loading invoice details...</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="primary-btn tr-bg"
                                                    data-dismiss="modal">@lang('common.close')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>


    {{-- View Fees Modal End --}}
@endsection


@include('components.partials.data_table_css')
@include('components.partials.data_table_js')


@push('script')
    <script>
        $('#table_id').DataTable({
            responsive: true, // ✅ Enable responsive extension
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            // scrollX: true,
        });

        // Handle view invoice modal
        $('[id^="viewInvoiceModal"]').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            var contentDiv = modal.find('.modal-body');

            // Extract invoice ID from modal ID
            var modalId = modal.attr('id');
            var idMatch = modalId.match(/viewInvoiceModal(\d+)/);
            var invoiceId = null;

            if (idMatch) {
                invoiceId = idMatch[1];
            }

            if (!invoiceId) {
                contentDiv.html('<div class="alert alert-danger">Error: Invoice ID not found.</div>');
                return;
            }

            // Load invoice content via AJAX
            $.ajax({
                url: "{{ route('fees.fee-invoice-view', ['id' => ':id', 'state' => 'view']) }}".replace(
                    ':id', invoiceId),
                type: 'GET',
                beforeSend: function() {
                    contentDiv.html(`
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p>Loading invoice details...</p>
                        </div>
                    `);
                },
                success: function(response) {
                    contentDiv.html(response);
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', xhr, status, error);
                    contentDiv.html(`
                        <div class="alert alert-danger">
                            <h5>Error loading invoice details</h5>
                            <p>Please try again. If the problem persists, contact support.</p>
                            <small>Error: ${error}</small>
                        </div>
                    `);
                }
            });
        });

        // Function to populate invoice modal with student data
        function populateInvoiceModal(element) {
            var studentId = $(element).data('student-id');
            var invoiceId = $(element).data('invoice-id');

            // Find the invoice data from the current row
            var currentRow = $(element).closest('tr');
            var studentName = currentRow.find('td:eq(1) a').text().trim();
            var admissionNo = currentRow.find('td:eq(3)').text().trim();
            var totalAmount = currentRow.find('td:eq(4)').text().trim();
            var paidAmount = currentRow.find('td:eq(5)').text().trim();
            var balance = currentRow.find('td:eq(6)').text().trim();
            var dueDate = currentRow.find('td:eq(10)').text().trim();

            // Populate modal fields
            $('#studentName').text(studentName || '__________________________');
            $('#studentRegNo').text(admissionNo || '__________________________');
            $('#studentNic').text('__________________________'); // NIC not available in table
            $('#studentAddress').text('__________________________'); // Address not available in table
            $('#studentContact').text('__________________________'); // Contact not available in table
            $('#totalPayment').text(totalAmount || '__________________________');
            $('#advancePaid').text(paidAmount || '__________________________');
            $('#balanceAmount').text(balance || '__________________________');
            $('#dueDate').text(dueDate || '__________________________');
            $('#courseName').text('__________________________'); // Course name not available in table
            $('#receiptNo').text('__________________________'); // Receipt number not available in table
        }

        // Function to print invoice
        function printInvoice() {
            var printContent = document.getElementById('invoicePreview').innerHTML;
            var originalContent = document.body.innerHTML;

            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;

            // Reinitialize any necessary scripts after printing
            location.reload();
        }
    </script>
@endpush
