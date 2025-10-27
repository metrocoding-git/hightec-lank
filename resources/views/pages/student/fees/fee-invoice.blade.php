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
                                                {{-- <th>Course</th> --}}
                                                <th>@lang('common.student')</th>
                                                <th>Branch</th>
                                                <th>@lang('admin.admission_no')</th>
                                                <th>@lang('accounts.amount')(Rs)</th>
                                                {{-- <th>@lang('fees.paid')(Rs)</th> --}}
                                                <th>@lang('accounts.balance')(Rs)</th>
                                                <th>Payment Method</th>
                                                <th>Payment Status</th>
                                                <th>Payment Date</th>
                                                <th>Due Date</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>


                                        <tbody>
                                            @foreach ($fee_invoices as $key => $invoice)
                                            {{-- {{ dd($invoice) }} --}}
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
                                                    {{-- <td>{{ $invoice->paidCourses->course->name }}</td> --}}
                                                    {{-- <td>
                                                        {{ $invoice->paidCourses->first() ? $invoice->paidCourses->first()->course->name : 'N/A' }} ({{ $invoice->paidCourses->first() ? $invoice->paidCourses->first()->course_id : 'N/A' }})
                                                    </td> --}}
                                                    
                                                    <td>
                                                        <a href="{{ route('fees.fee-invoice-view', ['id' => $invoice->id, 'state' => 'view']) }}"
                                                            target="_blank">
                                                            {{ $invoice->student->student_name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $invoice->branch->name }}</td>
                                                    <td>{{ $invoice->student->admission_number }}</td>

                                                    <td>{{ $amount }}</td>
                                                    {{-- <td>{{ $paid_amount }}</td> --}}
                                                    <td>{{ $balance }}.00</td>
                                                    <td>{{ $invoice->payment_method }}</td>

                                                    <td>
                                                        @if ($invoice->payment_status == 'partial')
                                                            <button
                                                                class="primary-btn small bg-warning text-white border-0">@lang('fees.partial')</button>
                                                        @elseif ($invoice->payment_status == 'full')
                                                            <button
                                                                class="primary-btn small bg-success text-white border-0">@lang('fees.paid')</button>
                                                        @elseif ($invoice->payment_status == 'returned')
                                                            <button
                                                                class="primary-btn small bg-info text-white border-0">Returned</button>
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

                                                            {{-- Only show Payment Details and Delete for returned invoices --}}
                                                            @if ($invoice->payment_status == 'returned')
                                                                <a class="dropdown-item" data-toggle="modal"
                                                                    data-target="#viewPaymentDetailsModal"
                                                                    data-invoice-id="{{ $invoice->id }}"
                                                                    href="#">Payment Details</a>
                                                            @else
                                                                {{-- Show View Payment for non-returned invoices --}}
                                                                <a class="dropdown-item" data-toggle="modal"
                                                                    data-target="#viewPaymentHistoryModal"
                                                                    data-invoice-id="{{ $invoice->id }}"
                                                                    data-course-name="{{ $invoice->paidCourses->first() ? $invoice->paidCourses->first()->course->name : 'N/A' }}"
                                                                    href="#">View Payment</a>

                                                                {{-- Show Make Invoice option for non-returned invoices --}}
                                                                <a class="dropdown-item" data-toggle="modal"
                                                                    data-target="#makeInvoiceModal"
                                                                    data-student-id="{{ $invoice->student->id }}"
                                                                    data-invoice-id="{{ $invoice->id }}"
                                                                    onclick="populateInvoiceModal(this)">Make Invoice</a>

                                                                {{-- Show Return option only if there's paid amount and not returned --}}
                                                                @if ($invoice->paid_amount > 0)
                                                                    <a class="dropdown-item" data-toggle="modal"
                                                                        data-target="#returnFeeModal{{ $invoice->id }}"
                                                                        href="#">Return</a>
                                                                @endif
                                                            @endif

                                                            {{-- Show Report option --}}
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#invoiceReportModal"
                                                                data-invoice-id="{{ $invoice->id }}"
                                                                href="#">Report</a>

                                                            {{-- Always show Delete option --}}
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#deletecategoryModal{{ $invoice->id }}"
                                                                href="#">@lang('common.delete')</a>
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
                                                                <form action="{{ route('fees.fee-delete', $invoice->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="primary-btn fix-gr-bg" type="submit">
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
                                @if ($invoice->paid_amount > 0)
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

                            <!-- View Payment History Modal (Full Featured) -->
                            <div class="modal fade admin-query" id="viewPaymentHistoryModal" tabindex="-1"
                                role="dialog" aria-labelledby="viewPaymentHistoryModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="viewPaymentHistoryModalLabel">Payment History for
                                                Invoice #<span id="paymentHistoryInvoiceId"></span> </h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <h5>Payment History</h5> <span id="mainInvoiceNumber"></span>
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
                                                    <input type="hidden" name="create_user_id"
                                                        value="{{ Auth::id() }}">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paymentAmountInput">Amount <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="number" class="form-control"
                                                                    id="paymentAmountInput" name="pay_amount"
                                                                    step="0.01" required min="0.01">
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

                                                    <!-- Bank Payment Fields -->
                                                    <div id="bankPaymentFields" style="display: none;">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="bankPaymentDateInput">Bank Payment
                                                                        Date</label>
                                                                    <input type="date" class="form-control"
                                                                        id="bankPaymentDateInput"
                                                                        name="bank_payment_date">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="bankPaymentAmountInput">Bank Payment
                                                                        Amount</label>
                                                                    <input type="number" class="form-control"
                                                                        id="bankPaymentAmountInput"
                                                                        name="bank_payment_amount" step="0.01">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group" id="paymentFileInputGroup"
                                                        style="display: none;">
                                                        <label for="paymentFileInput">Payment File (Optional)</label>
                                                        <input type="file" class="form-control-file"
                                                            id="paymentFileInput" name="payment_file">
                                                    </div>
                                                    <button type="submit" class="primary-btn fix-gr-bg mt-3"
                                                        style="width: 100%; padding: 12px; font-size: 16px; font-weight: 600;">Add
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

                            <!-- View Payment Details Modal (Read Only for Returned Invoices) -->
                            <div class="modal fade admin-query" id="viewPaymentDetailsModal" tabindex="-1"
                                role="dialog" aria-labelledby="viewPaymentDetailsModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="viewPaymentDetailsModalLabel">Payment Details for
                                                Invoice #<span id="paymentDetailsInvoiceId"></span></h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                This invoice has been marked as returned. Payment history and return details are shown below.
                                            </div>

                                            <!-- Return Information Section -->
                                            <div id="returnInfoSection" style="display: none;">
                                                <div class="alert alert-warning" style="border-left: 4px solid #ffc107;">
                                                    <h5><i class="fas fa-undo-alt"></i> Return Information</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Return Amount:</strong> <span id="returnAmountDisplay" class="text-danger"></span></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Return Date:</strong> <span id="returnDateDisplay"></span></p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <p><strong>Return Reason:</strong> <span id="returnReasonDisplay"></span></p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Returned By:</strong> <span id="returnedByDisplay"></span></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Original Paid Amount:</strong> <span id="originalPaidAmountDisplay"></span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <h5>Payment History</h5> <span id="detailsMainInvoiceNumber"></span>
                                            <div id="paymentDetailsContent">
                                                <!-- Payment details will be loaded here via AJAX -->
                                                <div class="text-center">
                                                    <div class="spinner-border" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                    <p>Loading payment details...</p>
                                                </div>
                                            </div>

                                            <hr class="mt-4 mb-4">
                                            <div class="row mb-4">
                                                <div class="col-md-3">
                                                    <h5>Total Amount: <span id="detailsTotalAmountDisplay"></span></h5>
                                                </div>
                                                <div class="col-md-3">
                                                    <h5>Paid Amount: <span id="detailsPaidAmountDisplay"></span></h5>
                                                </div>
                                                <div class="col-md-3">
                                                    <h5>Returned Amount: <span id="detailsReturnedAmountDisplay" class="text-danger">0.00</span></h5>
                                                </div>
                                                <div class="col-md-3">
                                                    <h5>Net Amount: <span id="detailsNetAmountDisplay" class="text-success"></span></h5>
                                                </div>
                                            </div>

                                            <!-- No action buttons or add payment section for returned invoices -->
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="primary-btn tr-bg"
                                                data-dismiss="modal">@lang('common.close')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- View Bank Details Modal -->
                            <div class="modal fade admin-query" id="viewBankDetailsModal" tabindex="-1"
                                role="dialog" aria-labelledby="viewBankDetailsModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="viewBankDetailsModalLabel">Bank Payment Details</h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="bankPaymentDateDisplay">Bank Payment Date</label>
                                                        <input type="text" class="form-control" id="bankPaymentDateDisplay" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="bankPaymentAmountDisplay">Bank Payment Amount</label>
                                                        <input type="text" class="form-control" id="bankPaymentAmountDisplay" readonly>
                                                    </div>
                                                </div>
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

    <!-- Invoice Report Modal -->
    <div class="modal fade admin-query" id="invoiceReportModal" tabindex="-1" role="dialog"
        aria-labelledby="invoiceReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="invoiceReportModalLabel">Invoice Report for Invoice #<span id="reportInvoiceId"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="report-container" id="invoiceReportContent">
                        <!-- Report content will be loaded here via AJAX -->
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p>Loading invoice report...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.close')</button>
                    <button type="button" class="primary-btn fix-gr-bg" onclick="printReport()">Print Report</button>
                </div>
            </div>
        </div>
    </div>

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

        // Handle view payment history modal (full featured) - for non-returned invoices
        $('#viewPaymentHistoryModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var invoiceId = button.data('invoice-id');
            var courseName = button.data('course-name'); // Get course name
            var modal = $(this);

            modal.find('#paymentHistoryInvoiceId').text(invoiceId);
            modal.find('#paymentHistoryCourseName').text(courseName || 'N/A'); // Display course name
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
                    modal.find('#paidAmountDisplay').text(response
                    .paid_amount_sum); // Display sum of paid amounts
                    modal.find('#dueAmountDisplay').text(response.due_amount);

                    // Set the max attribute for the payment amount input
                    modal.find('#paymentAmountInput').attr('max', response.due_amount);

                    let historyHtml =
                        '<table class="table table-bordered" style="text-align: center; width: 100%;"><thead style="background-color: #f8f9fa;"><tr><th style="text-align: center; padding: 12px;">Invoice Number</th><th style="text-align: center; padding: 12px;">Course Name</th><th style="text-align: center; padding: 12px;">Amount</th><th style="text-align: center; padding: 12px;">Method</th><th style="text-align: center; padding: 12px;">Type</th><th style="text-align: center; padding: 12px;">Receipt No.</th><th style="text-align: center; padding: 12px;">Date</th><th style="text-align: center; padding: 12px;">Action</th></tr></thead><tbody>';
                    if (response.payments.length > 0) {
                        response.payments.forEach(function(payment) {
                            let actionDropdown = '';
                            if (payment.payment_method === 'Invoice' && !payment
                                .payment_invoice_number) {
                                actionDropdown +=
                                    `<a style="padding:4px;margin: 2px; background-color: #007bff; color: #fff; text-align: center;" class="dropdown-item add-payment-invoice-number-btn" href="#" data-payment-id="${payment.id}">Add Invoice No.</a>`;
                            }
                            if (payment.payment_method === 'Bank Transfer') {
                                actionDropdown +=
                                    `<a style="padding:4px;margin: 2px; background-color: #28a745; color: #fff; text-align: center;" class="dropdown-item view-bank-details-btn" href="#" data-payment-id="${payment.id}" data-bank-date="${payment.bank_payment_date}" data-bank-amount="${payment.bank_payment_amount}">View Bank Details</a>`;
                            }
                            if (payment.payment_method !== 'Refund' && payment
                                .payment_status !== 'refunded' && payment.amount > 0) {
                                actionDropdown +=
                                    `<a style="padding:4px;margin: 2px; background-color: #dc3545; color: #fff; text-align: center;" class="dropdown-item refund-btn" href="#" data-payment-id="${payment.id}" data-amount="${payment.amount}">Refund</a>`;
                            }

                            historyHtml += `
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px; text-align: center;">${payment.payment_invoice_number || 'N/A'}</td>
                                    <td style="padding: 12px; text-align: center;">${courseName || 'N/A'}</td>
                                    <td style="padding: 12px; text-align: center;">${payment.amount}</td>
                                    <td style="padding: 12px; text-align: center;">${payment.payment_method}</td>
                                    <td style="padding: 12px; text-align: center;">${payment.payment_type}</td>
                                    <td style="padding: 12px; text-align: center;">${payment.receipt_number}</td>
                                    <td style="padding: 12px; text-align: center;">${new Date(payment.created_at).toLocaleDateString()}</td>
                                    <td style="padding: 12px; text-align: center;">
                                        ${actionDropdown ? `
                                                <div class="dropdown">
                                                    <button class="primary-btn small fix-gr-bg dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false" style="border-radius: 6px; padding: 6px 12px; font-size: 12px;">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu" style="min-width: 150px; border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                                        ${actionDropdown}
                                                    </div>
                                                </div>
                                            ` : '<span class="text-muted">No actions</span>'}
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        historyHtml +=
                            '<tr><td colspan="8" style="text-align: center; padding: 20px; color: #6c757d;">No payment history found.</td></tr>';
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

        // Handle view payment details modal (read-only for returned invoices)
        $('#viewPaymentDetailsModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var invoiceId = button.data('invoice-id');
            var modal = $(this);

            modal.find('#paymentDetailsInvoiceId').text(invoiceId);

            // Fetch both payment history and return fee data
            $.ajax({
                url: `/fees/payment-history/${invoiceId}`,
                type: 'GET',
                beforeSend: function() {
                    modal.find('#paymentDetailsContent').html(`
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p>Loading payment details...</p>
                        </div>
                    `);
                },
                success: function(response) {
                    console.log('Payment details response:', response);
                    modal.find('#detailsTotalAmountDisplay').text(response.total_amount);
                    modal.find('#detailsMainInvoiceNumber').text(response.main_invoice_number);
                    modal.find('#detailsPaidAmountDisplay').text(response.paid_amount_sum);
                    modal.find('#detailsDueAmountDisplay').text(response.due_amount);

                    // Fetch return fee data from return_fees table
                    $.ajax({
                        url: `/fees/return-fee-data/${invoiceId}`,
                        type: 'GET',
                        success: function(returnResponse) {
                            console.log('Return fee data:', returnResponse);

                            var returnAmount = 0;
                            var returnDate = '';
                            var returnReason = '';
                            var returnedBy = '';
                            var originalPaidAmount = response.paid_amount_sum;

                            if (returnResponse.return_fees && returnResponse.return_fees.length > 0) {
                                // Sum all return amounts for this invoice
                                returnResponse.return_fees.forEach(function(returnFee) {
                                    returnAmount += parseFloat(returnFee.return_amount) || 0;
                                    if (!returnDate && returnFee.created_at) {
                                        returnDate = new Date(returnFee.created_at).toLocaleDateString();
                                    }
                                    if (returnFee.short_note) {
                                        returnReason = returnFee.short_note;
                                    }
                                    if (returnFee.return_user && returnFee.return_user.first_name) {
                                        returnedBy = returnFee.return_user.first_name + ' ' + (returnFee.return_user.last_name || '');
                                    }
                                });
                            }

                            // Show return information section if there are returns
                            if (returnAmount > 0) {
                                modal.find('#returnInfoSection').show();
                                modal.find('#returnAmountDisplay').text(returnAmount.toFixed(2));
                                modal.find('#returnDateDisplay').text(returnDate || 'N/A');
                                modal.find('#returnReasonDisplay').text(returnReason || 'N/A');
                                modal.find('#returnedByDisplay').text(returnedBy || 'N/A');
                                modal.find('#originalPaidAmountDisplay').text(originalPaidAmount.toFixed(2));

                                // Calculate and display net amount
                                var netAmount = originalPaidAmount - returnAmount;
                                modal.find('#detailsReturnedAmountDisplay').text(returnAmount.toFixed(2));
                                modal.find('#detailsNetAmountDisplay').text(netAmount.toFixed(2));

                                // Add color coding for net amount
                                if (netAmount < 0) {
                                    modal.find('#detailsNetAmountDisplay').addClass('text-danger').removeClass('text-success');
                                } else {
                                    modal.find('#detailsNetAmountDisplay').addClass('text-success').removeClass('text-danger');
                                }
                            } else {
                                modal.find('#returnInfoSection').hide();
                                modal.find('#detailsReturnedAmountDisplay').text('0.00');
                                modal.find('#detailsNetAmountDisplay').text(originalPaidAmount.toFixed(2));
                            }

                            // Create read-only payment history table (no action buttons)
                            let detailsHtml =
                                '<table class="table table-bordered" style="text-align: center; width: 100%;"><thead style="background-color: #f8f9fa;"><tr><th style="text-align: center; padding: 12px;">Invoice Number</th><th style="text-align: center; padding: 12px;">Amount</th><th style="text-align: center; padding: 12px;">Method</th><th style="text-align: center; padding: 12px;">Type</th><th style="text-align: center; padding: 12px;">Receipt No.</th><th style="text-align: center; padding: 12px;">Date</th></tr></thead><tbody>';
                            if (response.payments.length > 0) {
                                response.payments.forEach(function(payment) {
                                    // Highlight return/refund payments
                                    var rowStyle = '';
                                    var amountStyle = '';
                                    if (payment.payment_method === 'Refund' || payment.payment_type === 'return') {
                                        rowStyle = 'style="background-color: #fff3cd; border-left: 4px solid #ffc107;"';
                                        amountStyle = 'style="color: #856404; font-weight: bold;"';
                                    }

                                    detailsHtml += `
                                        <tr ${rowStyle}>
                                            <td style="padding: 12px; text-align: center;">${payment.payment_invoice_number || 'N/A'}</td>
                                            <td style="padding: 12px; text-align: center;" ${amountStyle}>${payment.amount}</td>
                                            <td style="padding: 12px; text-align: center;">${payment.payment_method}</td>
                                            <td style="padding: 12px; text-align: center;">${payment.payment_type}</td>
                                            <td style="padding: 12px; text-align: center;">${payment.receipt_number}</td>
                                            <td style="padding: 12px; text-align: center;">${new Date(payment.created_at).toLocaleDateString()}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                detailsHtml +=
                                    '<tr><td colspan="6" style="text-align: center; padding: 20px; color: #6c757d;">No payment history found.</td></tr>';
                            }
                            detailsHtml += '</tbody></table>';
                            modal.find('#paymentDetailsContent').html(detailsHtml);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching return fee data:', xhr.responseText);
                            // Continue with payment details even if return fee fetch fails
                            modal.find('#returnInfoSection').hide();
                            modal.find('#detailsReturnedAmountDisplay').text('0.00');
                            modal.find('#detailsNetAmountDisplay').text(response.paid_amount_sum);

                            // Still show payment history
                            let detailsHtml =
                                '<table class="table table-bordered" style="text-align: center; width: 100%;"><thead style="background-color: #f8f9fa;"><tr><th style="text-align: center; padding: 12px;">Invoice Number</th><th style="text-align: center; padding: 12px;">Amount</th><th style="text-align: center; padding: 12px;">Method</th><th style="text-align: center; padding: 12px;">Type</th><th style="text-align: center; padding: 12px;">Receipt No.</th><th style="text-align: center; padding: 12px;">Date</th></tr></thead><tbody>';
                            if (response.payments.length > 0) {
                                response.payments.forEach(function(payment) {
                                    detailsHtml += `
                                        <tr>
                                            <td style="padding: 12px; text-align: center;">${payment.payment_invoice_number || 'N/A'}</td>
                                            <td style="padding: 12px; text-align: center;">${payment.amount}</td>
                                            <td style="padding: 12px; text-align: center;">${payment.payment_method}</td>
                                            <td style="padding: 12px; text-align: center;">${payment.payment_type}</td>
                                            <td style="padding: 12px; text-align: center;">${payment.receipt_number}</td>
                                            <td style="padding: 12px; text-align: center;">${new Date(payment.created_at).toLocaleDateString()}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                detailsHtml +=
                                    '<tr><td colspan="6" style="text-align: center; padding: 20px; color: #6c757d;">No payment history found.</td></tr>';
                            }
                            detailsHtml += '</tbody></table>';
                            modal.find('#paymentDetailsContent').html(detailsHtml);
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching payment details:', xhr.responseText);
                    modal.find('#paymentDetailsContent').html(`
                        <div class="alert alert-danger">Error loading payment details. Please try again.</div>
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
            var paymentMethod = $('#paymentMethodInput').val();

            // Append create_user_id to formData
            formData.append('create_user_id', createUserId);

            // Clear previous error messages
            $('#addPaymentForm').find('.alert-danger').remove();

            // Validate bank transfer fields if Bank Transfer is selected
            if (paymentMethod === 'Bank Transfer') {
                var bankDate = $('#bankPaymentDateInput').val();
                var bankAmount = $('#bankPaymentAmountInput').val();
                var paymentAmount = $('#paymentAmountInput').val();

                // Validate bank payment date
                if (!bankDate) {
                    $('#addPaymentForm').prepend(
                        '<div class="alert alert-danger">Bank payment date is required for Bank Transfer</div>');
                    $('#bankPaymentDateInput').focus();
                    return;
                }

                // Validate bank payment amount
                // if (!bankAmount || parseFloat(bankAmount) <= 0) {
                //     $('#addPaymentForm').prepend(
                //         '<div class="alert alert-danger">Bank payment amount must be greater than 0</div>');
                //     $('#bankPaymentAmountInput').focus();
                //     return;
                // }

                // Validate bank amount is at least the due amount
                var dueAmount = parseFloat($('#dueAmountDisplay').text()) || 0;
                if (parseFloat(bankAmount) < dueAmount) {
                    $('#addPaymentForm').prepend(
                        // '<div class="alert alert-danger">Bank payment amount must be at least the due amount (' +
                        dueAmount + ')</div>');
                    $('#bankPaymentAmountInput').focus();
                    return;
                }
            }

            // Disable submit button and show loading state
            var submitBtn = $('#addPaymentForm button[type="submit"]');
            var originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding Payment...');

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

                    // Show error message
                    $('#addPaymentForm').prepend(
                        `<div class="alert alert-danger">${errorMessage}</div>`);

                    // Scroll to error message
                    $('.alert-danger').get(0).scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                },
                complete: function() {
                    // Re-enable submit button and restore original text
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Handle add invoice number button click (delegated event for dropdown items)
        $(document).on('click', '.add-payment-invoice-number-btn', function(e) {
            e.preventDefault();
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

        // Handle refund button click (delegated event for dropdown items)
        $(document).on('click', '.refund-btn', function(e) {
            e.preventDefault();
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

        // Show/hide payment file input and bank payment fields based on payment method
        $('#paymentMethodInput').on('change', function() {
            var selectedMethod = $(this).val();
            if (selectedMethod === 'Bank Transfer') {
                $('#paymentFileInputGroup').show();
                $('#bankPaymentFields').show();

                // Add required validation for bank transfer fields
                $('#bankPaymentDateInput').attr('required', true);
                $('#bankPaymentAmountInput').attr('required', true);

                // Add real-time validation
                $('#bankPaymentDateInput, #bankPaymentAmountInput').on('input change', function() {
                    validateBankTransferFields();
                });

            } else {
                $('#paymentFileInputGroup').hide();
                $('#bankPaymentFields').hide();

                // Remove required validation for bank transfer fields
                $('#bankPaymentDateInput').removeAttr('required');
                $('#bankPaymentAmountInput').removeAttr('required');

                // Clear any bank transfer validation errors
                $('#bankPaymentDateInput').removeClass('is-invalid');
                $('#bankPaymentAmountInput').removeClass('is-invalid');
                $('.bank-field-error').remove();
            }
        });

        // Function to validate bank transfer fields
        function validateBankTransferFields() {
            var isValid = true;
            var paymentMethod = $('#paymentMethodInput').val();

            if (paymentMethod === 'Bank Transfer') {
                var bankDate = $('#bankPaymentDateInput').val();
                var bankAmount = $('#bankPaymentAmountInput').val();

                // Clear previous errors
                $('#bankPaymentDateInput').removeClass('is-invalid');
                $('#bankPaymentAmountInput').removeClass('is-invalid');
                $('.bank-field-error').remove();

                // Validate bank payment date
                if (!bankDate) {
                    $('#bankPaymentDateInput').addClass('is-invalid');
                    $('#bankPaymentDateInput').after(
                        '<div class="invalid-feedback bank-field-error">Bank payment date is required for Bank Transfer</div>'
                        );
                    isValid = false;
                }

                // Validate bank payment amount
                if (!bankAmount || parseFloat(bankAmount) <= 0) {
                    $('#bankPaymentAmountInput').addClass('is-invalid');
                    $('#bankPaymentAmountInput').closest('.form-group').after(
                        '<div class="alert alert-danger bank-field-error" style="margin-top: 10px;">Bank payment amount must be greater than 0</div>'
                        );
                    isValid = false;
                }

                // Additional validation: bank amount should be larger than due amount
                var paymentAmount = parseFloat($('#paymentAmountInput').val()) || 0;
                var dueAmount = parseFloat($('#dueAmountDisplay').text()) || 0;
                if (bankAmount && dueAmount && parseFloat(bankAmount) < dueAmount) {
                    $('#bankPaymentAmountInput').addClass('is-invalid');
                    $('#bankPaymentAmountInput').closest('.form-group').after(
                        '<div class="alert alert-danger bank-field-error" style="margin-top: 10px;">Bank payment amount must be at least the due amount (' +
                        dueAmount + ')</div>');
                    isValid = false;
                }
            }

            return isValid;
        }

        // Handle view bank details button click (delegated event for dropdown items)
        $(document).on('click', '.view-bank-details-btn', function(e) {
            e.preventDefault();
            var bankDate = $(this).data('bank-date');
            var bankAmount = $(this).data('bank-amount');

            // Format the date if it's a valid date
            if (bankDate) {
                var dateObj = new Date(bankDate);
                if (!isNaN(dateObj.getTime())) {
                    bankDate = dateObj.toLocaleDateString();
                }
            } else {
                bankDate = 'N/A';
            }

            // Set the values in the modal
            $('#bankPaymentDateDisplay').val(bankDate);
            $('#bankPaymentAmountDisplay').val(bankAmount || 'N/A');

            // Show the modal
            $('#viewBankDetailsModal').modal('show');
        });

        // Handle invoice report modal
        $('#invoiceReportModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var invoiceId = button.data('invoice-id');
            var modal = $(this);

            modal.find('#reportInvoiceId').text(invoiceId);

            // Fetch invoice report data via AJAX
            $.ajax({
                url: `/student/fees/invoice-report/${invoiceId}`, // New route for fetching invoice report
                type: 'GET',
                beforeSend: function() {
                    modal.find('#invoiceReportContent').html(`
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p>Loading invoice report...</p>
                        </div>
                    `);
                },
                success: function(response) {
                    console.log('Invoice report response:', response);
                    // Format date to show only date, month, year
                    const date = new Date(response.invoice.create_date);
                    const formattedDate = date.toLocaleDateString('en-US', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    

                    // Generate report HTML based on the provided style
                    let reportHtml = `
                        <div class="invoice-report" style="font-family: Arial, sans-serif; max-width:800px; padding:40px; margin: 0 auto; padding: 20px; background: white;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; margin-top:40px;">
                                <div style="flex: 1;">
                                    <h3 style="margin: 0; font-size: 14px; font-weight: bold; color: #333;">${response.course_name}</h3>
                                </div>
                                <div style="text-align: right;">
                                    <p style="margin: 0; font-size: 14px; color: #666;">${formattedDate}</p>
                                    <p style="margin: 5px 0 0 0; font-size: 14px; font-weight: bold; color: #333;">${response.invoice.main_invoice_number}</p>
                                    <p style="margin: 5px 0 0 0; font-size: 14px; font-weight: bold; color: #333;">${response.invoice.student.admission_number}</p>
                                </div>
                            </div>

                            <div style="margin-bottom: 40px; margin-left: 80px;">
                                <p style="margin: 0; font-size: 14px; font-weight: bold; color: #333;">${response.invoice.student.student_name}</p>
                                <p style="margin: 5px 0 0 0; font-size: 14px; color: #666;">${response.invoice.student.national_id_number || 'N/A'}</p>
                                <p style="margin: 5px 0 0 0; font-size: 14px; color: #666;">${response.invoice.student.permanent_address || 'N/A'}</p>
                                <p style="margin: 5px 0 0 0; font-size: 14px; color: #666;">${response.invoice.student.phone_number || 'N/A'}</p>
                            </div>

                            <div style=" align-items: center;  padding-top: 15px;">
                                <div style="font-size: 14px; font-weight: bold; color: #333;">${response.invoice.total_amount}</div>
                                <div style="font-size: 14px; font-weight: bold; color: #333;">${response.invoice.paid_amount}</div>
                                <div style="font-size: 14px; font-weight: bold; color: #333;">${response.invoice.due_amount}</div>
                                <div style="font-size: 14px; font-weight: bold; color: #333;">${response.created_at}</div>
                                <div style="font-size: 14px; font-weight: bold; color: #333;">${response.expiration_date}</div>
                                
                            </div>
                        </div>
                    `;
                    modal.find('#invoiceReportContent').html(reportHtml);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching invoice report:', xhr.responseText);
                    modal.find('#invoiceReportContent').html(`
                        <div class="alert alert-danger">Error loading invoice report. Please try again.</div>
                    `);
                }
            });
        });

        // Function to print the report
        function printReport() {
            var printContent = document.getElementById('invoiceReportContent').innerHTML;
            var originalContent = document.body.innerHTML;

            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;

            // Reinitialize any necessary scripts after printing
            location.reload();
        }
    </script>
@endpush
