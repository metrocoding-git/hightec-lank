@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    @push('css')
        <link rel="stylesheet" href="{{ url('Modules\Fees\Resources\assets\css\feesStyle.css') }}" />
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
                                                <th>ID</th>
                                                <th>Main Invoice No.</th>
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
                                                    $balance = $amount - $paid_amount; // This is remaining balance
                                                    $available_for_return = $paid_amount; // This is what can actually be returned
                                                @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        <a href="{{ route('fees.fee-invoice-view', ['id' => $invoice->id, 'state' => 'view']) }}"
                                                            target="_blank">
                                                            {{ $invoice->main_invoice_number }}
                                                        </a>
                                                    </td>
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
                                                            {{-- <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#viewInvoiceModal{{ $invoice->id }}"
                                                                href="#">@lang('common.view')</a> --}}
                                                            <a class="dropdown-item"
                                                                href="{{ route('fees.fee-invoice-edit', $invoice->id) }}">@lang('common.edit')</a>
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#makeInvoiceModal"
                                                                data-student-id="{{ $invoice->student->id }}"
                                                                data-invoice-id="{{ $invoice->id }}"
                                                                onclick="populateInvoiceModal(this)">Make Invoice</a>
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#viewPaymentHistoryModal"
                                                                data-invoice-id="{{ $invoice->id }}" href="#">View
                                                                Payment</a>
                                                            @if ($invoice->payment_method === 'Invoice')
                                                                @if ($invoice->payment_status != 'full')
                                                                    <a class="dropdown-item" data-toggle="modal"
                                                                        data-target="#markAsPaidModal"
                                                                        data-invoice-id="{{ $invoice->id }}"
                                                                        href="#">Paid</a>
                                                                @endif
                                                            @endif
                                                            {{-- Removed the direct "Add Invoice Number" action from here as it will be handled within the payment history modal --}}
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
                                @if ($invoice->payment_status == 'full')
                                    <div class="modal fade admin-query" id="returnFeeModal{{ $invoice->id }}"
                                        style="background-color: rgba(0, 0, 0, 0.5);">
                                        <div class="modal-dialog modal-lg modal-dialog-centered"
                                            style="margin: 0 auto; max-width: 600px; display: flex; align-items: center; min-height: 100vh;">
                                            <div class="modal-content"
                                                style="border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); border: none; overflow: hidden;">
                                                <div class="modal-header"
                                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 25px; border-bottom: none;">
                                                    <h4 class="modal-title"
                                                        style="margin: 0; font-weight: 600; font-size: 1.4rem; display: flex; align-items: center;">
                                                        <i class="fas fa-undo-alt"
                                                            style="margin-right: 10px; font-size: 1.2rem;"></i>
                                                        Return Fee - Invoice #{{ $invoice->id }}
                                                    </h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        style="color: white; opacity: 0.8; font-size: 1.5rem; margin: 0; padding: 0; background: none; border: none;">
                                                        &times;
                                                    </button>
                                                </div>

                                                <form action="{{ route('return-fees.store') }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body"
                                                        style="padding: 30px 25px; background-color: #f8f9fa;">
                                                        <div class="row" style="margin-bottom: 20px;">
                                                            <div class="col-md-6" style="margin-bottom: 15px;">
                                                                <div class="form-group" style="margin-bottom: 0;">
                                                                    <label
                                                                        style="display: block; font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 0.9rem;">
                                                                        <i class="fas fa-user-graduate"
                                                                            style="margin-right: 5px; color: #667eea;"></i>
                                                                        @lang('common.student')
                                                                    </label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $invoice->student->student_name }}"
                                                                        readonly
                                                                        style="background-color: white; border: 1px solid #e1e5eb; border-radius: 8px; padding: 10px 15px; font-size: 0.95rem; color: #495057; box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);">
                                                                    <input type="hidden" name="student_id"
                                                                        value="{{ $invoice->student->id }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6" style="margin-bottom: 15px;">
                                                                <div class="form-group" style="margin-bottom: 0;">
                                                                    <label
                                                                        style="display: block; font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 0.9rem;">
                                                                        <i class="fas fa-id-card"
                                                                            style="margin-right: 5px; color: #667eea;"></i>
                                                                        @lang('admin.admission_no')
                                                                    </label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $invoice->student->admission_number }}"
                                                                        readonly
                                                                        style="background-color: white; border: 1px solid #e1e5eb; border-radius: 8px; padding: 10px 15px; font-size: 0.95rem; color: #495057; box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);">
                                                                    <input type="hidden" name="admission_number"
                                                                        value="{{ $invoice->student->admission_number }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row" style="margin-bottom: 20px;">
                                                            <div class="col-md-12">
                                                                <div class="form-group" style="margin-bottom: 0;">
                                                                    <label
                                                                        style="display: block; font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 0.9rem;">
                                                                        <i class="fas fa-coins"
                                                                            style="margin-right: 5px; color: #28a745;"></i>
                                                                        Available for Return
                                                                    </label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{ number_format($paid_amount, 2) }}"
                                                                        readonly
                                                                        style="background-color: #e8f5e8; border: 1px solid #28a745; border-radius: 8px; padding: 12px 15px; font-size: 1.1rem; font-weight: 600; color: #155724; text-align: center; box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);">
                                                                    <input type="hidden" name="balance"
                                                                        value="{{ $paid_amount }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row" style="margin-bottom: 25px;">
                                                            <div class="col-md-12">
                                                                <div class="form-group" style="margin-bottom: 0;">
                                                                    <label
                                                                        style="display: block; font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 0.9rem;">
                                                                        <i class="fas fa-money-bill-wave"
                                                                            style="margin-right: 5px; color: #dc3545;"></i>
                                                                        Return Amount <span
                                                                            style="color: #dc3545;">*</span>
                                                                    </label>
                                                                    <div class="input-group"
                                                                        style="box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;">
                                                                        <input type="number" name="return_amount"
                                                                            class="form-control" min="0.01"
                                                                            max="{{ $paid_amount }}" step="0.01"
                                                                            required placeholder="Enter return amount"
                                                                            style="border: 1px solid #dc3545; border-right: none; border-radius: 8px 0 0 8px; padding: 12px 15px; font-size: 1rem; color: #495057; height: 48px;">
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text"
                                                                                style="background-color: #dc3545; color: white; border: 1px solid #dc3545; border-left: none; border-radius: 0 8px 8px 0; padding: 12px 15px; font-weight: 600; height: 48px;">
                                                                                .00
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <small class="text-muted"
                                                                        style="display: block; margin-top: 8px; font-size: 0.85rem; color: #6c757d;">
                                                                        <i class="fas fa-info-circle"
                                                                            style="margin-right: 5px;"></i>
                                                                        Maximum return amount: <strong
                                                                            style="color: #28a745;">{{ number_format($paid_amount, 2) }}</strong>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group" style="margin-bottom: 25px;">
                                                            <label
                                                                style="display: block; font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 0.9rem;">
                                                                <i class="fas fa-sticky-note"
                                                                    style="margin-right: 5px; color: #ffc107;"></i>
                                                                @lang('fees.short_note')
                                                            </label>
                                                            <textarea name="short_note" class="form-control" rows="3" placeholder="Enter reason for return..."
                                                                style="border: 1px solid #e1e5eb; border-radius: 8px; padding: 12px 15px; font-size: 0.95rem; color: #495057; resize: vertical; box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); transition: border-color 0.3s ease;"></textarea>
                                                        </div>

                                                        <input type="hidden" name="invoice_id"
                                                            value="{{ $invoice->id }}">
                                                    </div>

                                                    <div class="modal-footer"
                                                        style="background-color: white; padding: 20px 25px; border-top: 1px solid #e1e5eb; border-radius: 0 0 12px 12px;">
                                                        <button type="button" class="primary-btn tr-bg"
                                                            data-dismiss="modal"
                                                            style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);">
                                                            <i class="fas fa-times" style="margin-right: 8px;"></i>
                                                            @lang('common.cancel')
                                                        </button>
                                                        <button type="submit" class="primary-btn fix-gr-bg"
                                                            style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(40, 167, 69, 0.3);">
                                                            <i class="fas fa-undo-alt" style="margin-right: 8px;"></i>
                                                            Process Return
                                                        </button>
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
                                                                alt="Logo" style="height: 200px; width: auto;">

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

                            <!-- Add Invoice Number Modal -->
                            <div class="modal fade admin-query" id="addInvoiceNumberModal" tabindex="-1" role="dialog"
                                aria-labelledby="addInvoiceNumberModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="addInvoiceNumberModalLabel">Add Invoice Number
                                            </h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{ route('fees.add-payment-invoice-number') }}" method="POST"
                                            id="addInvoiceNumberForm">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="payment_id" id="addInvoiceNumberPaymentId">
                                                <div class="form-group">
                                                    <label for="invoiceNumberInput">Invoice Number <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="invoiceNumberInput"
                                                        name="invoice_number" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="primary-btn tr-bg"
                                                    data-dismiss="modal">@lang('common.cancel')</button>
                                                <button type="submit" class="primary-btn fix-gr-bg">Save Invoice
                                                    Number</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Invoice Number Modal -->
                            <div class="modal fade admin-query" id="addInvoiceNumberModal" tabindex="-1" role="dialog"
                                aria-labelledby="addInvoiceNumberModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="addInvoiceNumberModalLabel">Add Invoice Number
                                            </h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="#" method="POST" id="addInvoiceNumberForm">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="invoice_id" id="addInvoiceNumberInvoiceId">
                                                <div class="form-group">
                                                    <label for="invoiceNumberInput">Invoice Number <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="invoiceNumberInput"
                                                        name="invoice_number" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="primary-btn tr-bg"
                                                    data-dismiss="modal">@lang('common.cancel')</button>
                                                <button type="submit" class="primary-btn fix-gr-bg">Save Invoice
                                                    Number</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Mark as Paid Modal -->
                            <div class="modal fade admin-query" id="markAsPaidModal" tabindex="-1" role="dialog"
                                aria-labelledby="markAsPaidModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="markAsPaidModalLabel">Mark Invoice as Paid</h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="#" method="POST" id="markAsPaidForm">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="invoice_id" id="markAsPaidInvoiceId">
                                                <div class="form-group">
                                                    <label for="totalAmountInput">Total Amount</label>
                                                    <input type="number" class="form-control" id="totalAmountInput"
                                                        name="total_amount" step="0.01" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="balanceAmountPaidModal">Balance</label>
                                                    <input type="number" class="form-control"
                                                        id="balanceAmountPaidModal" name="balance_amount" step="0.01"
                                                        readonly>
                                                </div>

                                                <div class="form-group">
                                                    <label for="paidAmountInput">Paid Amount <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="paidAmountInput"
                                                        name="paid_amount" step="0.01" required readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="paidDateInput">Paid Date <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" id="paidDateInput"
                                                        name="paid_date" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="primary-btn tr-bg"
                                                    data-dismiss="modal">@lang('common.cancel')</button>
                                                <button type="submit" class="primary-btn fix-gr-bg">Mark as Paid</button>
                                            </div>
                                        </form>
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

                            <!-- View Payment History Modal -->
                            <div class="modal fade admin-query" id="viewPaymentHistoryModal" tabindex="-1"
                                role="dialog" aria-labelledby="viewPaymentHistoryModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="viewPaymentHistoryModalLabel">Payment History for
                                                Invoice #<span id="paymentHistoryInvoiceId"></span></h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">


                                            <h5>Payment History</h5>  <span id="mainInvoiceNumber"></span>
                                            <div id="paymentHistoryContent">
                                                <!-- Payment history will be loaded here via AJAX -->
                                                <div class="text-center">
                                                    <div class="spinner-border" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                    <p>Loading payment history...</p>
                                                </div>
                                            </div>

                                            <hr class="mt-4 mb-4">
                                            <div class="row mb-4">
                                                <div class="col-md-4">
                                                    <h5>Total Amount: <span id="totalAmountDisplay"></span></h5>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5>Paid Amount: <span id="paidAmountDisplay"></span></h5>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5>Due Amount: <span id="dueAmountDisplay"></span></h5>
                                                </div>
                                            </div>

                                            <div id="addPaymentSection">
                                                <h5>Add New Payment</h5>
                                                <form action="#" method="POST" id="addPaymentForm"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="invoice_id" id="addPaymentInvoiceId">
                                                    <input type="hidden" name="create_user_id" value="{{ Auth::id() }}">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paymentAmountInput">Amount <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="number" class="form-control"
                                                                    id="paymentAmountInput" name="pay_amount" step="0.01"
                                                                    required min="0.01">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paymentMethodInput">Payment Method <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-control" id="paymentMethodInput"
                                                                    name="payment_method" required>
                                                                    <option value="">Select Method</option>
                                                                    <option value="Cash">Cash</option>
                                                                    <option value="Invoice">Invoice</option>
                                                                    <option value="Bank Transfer">Bank Transfer</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" id="paymentFileInputGroup" style="display: none;">
                                                        <label for="paymentFileInput">Payment File (Optional)</label>
                                                        <input type="file" class="form-control-file" id="paymentFileInput"
                                                            name="payment_file">
                                                    </div>
                                                    <button type="submit" class="primary-btn fix-gr-bg mt-3">Add
                                                        Payment</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="primary-btn tr-bg"
                                                data-dismiss="modal">@lang('common.close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

            // Set basic data immediately
            $('#studentName').text(studentName || '__________________________');
            $('#studentRegNo').text(admissionNo || '__________________________');
            $('#totalPayment').text(totalAmount || '__________________________');
            $('#advancePaid').text(paidAmount || '__________________________');
            $('#balanceAmount').text(balance || '__________________________');
            $('#dueDate').text(dueDate || '__________________________');
            $('#courseName').text('__________________________'); // Course name not available in table
            $('#receiptNo').text('__________________________'); // Receipt number not available in table

            // Fetch comprehensive student details via AJAX
            if (studentId) {
                var ajaxUrl = "{{ route('fees.student-details', ':studentId') }}".replace(':studentId', studentId);
                console.log('Making AJAX request to:', ajaxUrl);
                console.log('Student ID:', studentId);

                $.ajax({
                    url: ajaxUrl,
                    type: 'GET',
                    success: function(student) {
                        console.log('Student data received:', student);

                        // Populate student details from database
                        $('#studentName').text(student.student_name || studentName ||
                            '__________________________');
                        $('#studentRegNo').text(student.admission_number || admissionNo ||
                            '__________________________');
                        $('#studentNic').text(student.national_id_number || '__________________________');
                        $('#studentAddress').text(student.current_address || student.permanent_address ||
                            '__________________________');
                        $('#studentContact').text(student.phone_number || '__________________________');

                        // Set course name if available (from student courses relationship)
                        if (student.courses && student.courses.length > 0) {
                            $('#courseName').text(student.courses[0].name || '__________________________');
                        }

                        // Set receipt number (using student ID for now)
                        $('#receiptNo').text('REC-' + student.id + '-' + new Date().getFullYear() ||
                            '__________________________');

                        // Add additional student info if needed
                        console.log('Student details loaded successfully:', student);
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX Error Details:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error,
                            url: ajaxUrl
                        });

                        // Keep the basic data already set from the table
                        $('#studentNic').text('__________________________');
                        $('#studentAddress').text('__________________________');
                        $('#studentContact').text('__________________________');
                    }
                });
            } else {
                console.log('No student ID provided');
                // No student ID, use basic data only
                $('#studentNic').text('__________________________');
                $('#studentAddress').text('__________________________');
                $('#studentContact').text('__________________________');
            }
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

        // Handle mark as paid modal
        $('#markAsPaidModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var invoiceId = button.data('invoice-id'); // Extract info from data-* attributes
            var modal = $(this);

            modal.find('#markAsPaidInvoiceId').val(invoiceId);
            modal.find('#paidDateInput').val(new Date().toISOString().slice(0, 10)); // Set current date

            // Find the corresponding table row for the invoiceId
            var currentRow = $('a[data-invoice-id="' + invoiceId + '"][data-target="#markAsPaidModal"]').closest(
                'tr');
            if (currentRow.length > 0) {
                var totalAmount = currentRow.find('td:eq(4)').text().trim();
                var paidAmount = currentRow.find('td:eq(5)').text().trim();
                var balance = currentRow.find('td:eq(6)').text().trim();

                modal.find('#totalAmountInput').val(totalAmount);
                modal.find('#balanceAmountPaidModal').val(balance);
                modal.find('#paidAmountInput').val(paidAmount); // Set the current paid amount, but keep it readonly
            }
        });

        // Handle form submission for marking as paid
        $('#markAsPaidForm').on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            var invoiceId = $('#markAsPaidInvoiceId').val();
            var paidAmount = $('#paidAmountInput').val();
            var paidDate = $('#paidDateInput').val();
            var token = $('input[name="_token"]').val(); // Get CSRF token

            console.log('Submitting Paid Amount and Date:', {
                invoiceId: invoiceId,
                paidAmount: paidAmount,
                paidDate: paidDate
            });

            // Here you would typically make an AJAX call to save the paid amount and date
            // For now, we'll just log it and close the modal
            $.ajax({
                url: '/fees/mark-as-paid', // Replace with your actual route
                type: 'POST',
                data: {
                    _token: token,
                    invoice_id: invoiceId,
                    paid_amount: paidAmount,
                    paid_date: paidDate
                },
                success: function(response) {
                    console.log('Invoice marked as paid successfully:', response);
                    $('#markAsPaidModal').modal('hide');
                    // Optionally, refresh the page or update the table row
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error marking invoice as paid:', xhr.responseText);
                    alert('Error marking invoice as paid. Please try again.');
                }
            });
        });

        // Handle view payment history modal
        $('#viewPaymentHistoryModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var invoiceId = button.data('invoice-id');
            var modal = $(this);

            modal.find('#paymentHistoryInvoiceId').text(invoiceId);
            modal.find('#addPaymentInvoiceId').val(invoiceId); // Set invoice ID for add payment form

            // Fetch payment history and invoice details via AJAX
            $.ajax({
                url: `/fees/payment-history/${invoiceId}`, // New route for fetching payment history
                type: 'GET',
                beforeSend: function() {
                    modal.find('#paymentHistoryContent').html(`
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p>Loading payment history...</p>
                        </div>
                    `);
                },
                success: function(response) {
                    console.log('Payment history response:', response);
                    modal.find('#totalAmountDisplay').text(response.total_amount);
                    modal.find('#mainInvoiceNumber').text(response.main_invoice_number);
                    modal.find('#paidAmountDisplay').text(response.paid_amount_sum); // Display sum of paid amounts
                    modal.find('#dueAmountDisplay').text(response.due_amount);

                    // Set the max attribute for the payment amount input
                    modal.find('#paymentAmountInput').attr('max', response.due_amount);

                    let historyHtml =
                        '<table class="table table-bordered"><thead><tr><th>Invoice Number</th><th>Amount</th><th>Method</th><th>Type</th><th>Receipt No.</th><th>Date</th><th>Action</th></tr></thead><tbody>';
                    if (response.payments.length > 0) {
                        response.payments.forEach(function(payment) {
                            historyHtml += `
                                <tr>
                                    <td>${payment.payment_invoice_number || 'N/A'}</td>
                                    <td>${payment.amount}</td>
                                    <td>${payment.payment_method}</td>
                                    <td>${payment.payment_type}</td>
                                    <td>${payment.receipt_number}</td>
                                    <td>${new Date(payment.created_at).toLocaleDateString()}</td>
                                    <td>
                                        ${payment.payment_method === 'Invoice' && !payment.payment_invoice_number ?
                                            `<button class="primary-btn small fix-gr-bg add-payment-invoice-number-btn" data-payment-id="${payment.id}">Add Invoice No.</button>` : ''}
                                        <button class="primary-btn small fix-gr-bg refund-btn" data-payment-id="${payment.id}" data-amount="${payment.amount}">Refund</button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        historyHtml +=
                            '<tr><td colspan="7" class="text-center">No payment history found.</td></tr>';
                    }
                    historyHtml += '</tbody></table>';
                    modal.find('#paymentHistoryContent').html(historyHtml);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching payment history:', xhr.responseText);
                    modal.find('#paymentHistoryContent').html(`
                        <div class="alert alert-danger">Error loading payment history. Please try again.</div>
                    `);
                }
            });
        });

        // Handle add payment form submission
        $('#addPaymentForm').on('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);
            var invoiceId = $('#addPaymentInvoiceId').val();
            var createUserId = $('input[name="create_user_id"]').val(); // Get the create_user_id

            // Append create_user_id to formData
            formData.append('create_user_id', createUserId);

            // Clear previous error messages
            $('#addPaymentForm').find('.alert-danger').remove();

            $.ajax({
                url: `/fees/add-payment/${invoiceId}`, // Existing route for adding payments
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Payment added successfully:', response);
                    $('#addPaymentForm').find('.alert-danger').remove(); // Clear any error messages
                    $('#viewPaymentHistoryModal').modal('hide');
                    location.reload(); // Reload page to reflect changes
                },
                error: function(xhr, status, error) {
                    console.error('Error adding payment:', xhr.responseText);
                    let errorMessage = 'Error adding payment. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Handle validation errors
                        errorMessage = '<ul>';
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            errorMessage += '<li>' + value + '</li>';
                        });
                        errorMessage += '</ul>';
                    }
                    $('#addPaymentForm').prepend(`<div class="alert alert-danger">${errorMessage}</div>`);
                }
            });
        });

        // Handle add invoice number button click (delegated event)
        $(document).on('click', '.add-payment-invoice-number-btn', function() {
            var paymentId = $(this).data('payment-id');
            $('#addInvoiceNumberPaymentId').val(paymentId);
            $('#addInvoiceNumberModal').modal('show');
            // Clear any previous error messages
            $('#addInvoiceNumberForm').find('.alert-danger').remove();
            // Hide the view payment history modal
            $('#viewPaymentHistoryModal').modal('hide');
        });

        // Handle form submission for adding invoice number
        $('#addInvoiceNumberForm').on('submit', function(event) {
            event.preventDefault();

            var formData = $(this).serialize(); // Serialize form data
            var paymentId = $('#addInvoiceNumberPaymentId').val();
            var invoiceId = $('#addPaymentInvoiceId').val(); // Get the invoice ID from the parent modal

            $.ajax({
                url: $(this).attr('action'), // Use the form's action URL
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log('Invoice number added successfully:', response);
                    $('#addInvoiceNumberModal').modal('hide');
                    // Re-show the view payment history modal and refresh its content
                    $('#viewPaymentHistoryModal').modal('show');
                    // Manually trigger the AJAX call for payment history to update the content
                    $.ajax({
                        url: `/fees/payment-history/${invoiceId}`,
                        type: 'GET',
                        beforeSend: function() {
                            $('#paymentHistoryContent').html(`
                                <div class="text-center">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <p>Loading payment history...</p>
                                </div>
                            `);
                        },
                        success: function(historyResponse) {
                            $('#totalAmountDisplay').text(historyResponse.total_amount);
                            $('#mainInvoiceNumber').text(historyResponse
                                .main_invoice_number);
                            $('#paidAmountDisplay').text(historyResponse.paid_amount_sum);
                            $('#dueAmountDisplay').text(historyResponse.due_amount);

                            let historyHtml =
                                '<table class="table table-bordered"><thead><tr><th>Invoice Number</th><th>Amount</th><th>Method</th><th>Type</th><th>Receipt No.</th><th>Date</th><th>Action</th></tr></thead><tbody>';
                            if (historyResponse.payments.length > 0) {
                                historyResponse.payments.forEach(function(payment) {
                                    historyHtml += `
                                        <tr>
                                            <td>${payment.payment_invoice_number || 'N/A'}</td>
                                            <td>${payment.amount}</td>
                                            <td>${payment.payment_method}</td>
                                            <td>${payment.payment_type}</td>
                                            <td>${payment.receipt_number}</td>
                                            <td>${new Date(payment.created_at).toLocaleDateString()}</td>
                                            <td>
                                                ${payment.payment_method === 'Invoice' && !payment.payment_invoice_number ?
                                                    `<button class="primary-btn small fix-gr-bg add-payment-invoice-number-btn" data-payment-id="${payment.id}">Add Invoice No.</button>` : ''}
                                                <button class="primary-btn small bg-danger text-white border-0 refund-btn" data-payment-id="${payment.id}" data-amount="${payment.amount}">Refund</button>
                                            </td>
                                        </tr>
                                    `;
                                });
                            } else {
                                historyHtml +=
                                    '<tr><td colspan="7" class="text-center">No payment history found.</td></tr>';
                            }
                            historyHtml += '</tbody></table>';
                            $('#paymentHistoryContent').html(historyHtml);
                        },
                        error: function(xhr, status, error) {
                            console.error(
                                'Error fetching payment history after invoice number update:',
                                xhr.responseText);
                            $('#paymentHistoryContent').html(`
                                <div class="alert alert-danger">Error loading payment history after update.</div>
                            `);
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error adding invoice number:', xhr.responseText);
                    let errorMessage = 'Error adding invoice number. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    // Display error message within the modal
                    $('#addInvoiceNumberForm').prepend(
                        `<div class="alert alert-danger">${errorMessage}</div>`);
                }
            });
        });

        // Handle refund button click (delegated event for dynamically added buttons)
        $(document).on('click', '.refund-btn', function() {
            var paymentId = $(this).data('payment-id');
            var amount = $(this).data('amount');

            if (confirm(`Are you sure you want to refund ${amount}?`)) {
                $.ajax({
                    url: `/fees/refund-payment/${paymentId}`, // New route for refunding payments
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        amount: amount // Pass the amount to be refunded
                    },
                    success: function(response) {
                        console.log('Payment refunded successfully:', response);
                        $('#viewPaymentHistoryModal').modal('hide');
                        location.reload(); // Reload page to reflect changes
                    },
                    error: function(xhr, status, error) {
                        console.error('Error refunding payment:', xhr.responseText);
                        alert('Error refunding payment. Please try again.');
                    }
                });
            }
        });

        // Show/hide payment file input based on payment method
        $('#paymentMethodInput').on('change', function() {
            var selectedMethod = $(this).val();
            if (selectedMethod === 'Invoice' || selectedMethod === 'Bank Transfer') {
                $('#paymentFileInputGroup').show();
            } else {
                $('#paymentFileInputGroup').hide();
            }
        });
    </script>
@endpush
