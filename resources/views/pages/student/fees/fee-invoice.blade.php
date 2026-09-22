@extends('layouts.app')
@section('title')
    Fees Invoice List
@endsection
@section('mainContent')
    @php

        $canAddFeesInvoice = Auth::user()->hasPermission('fees.invoice.store');
        $canAddFeesPayment = Auth::user()->hasPermission('fees.add-payment');
        $canRefundFeesPayment = Auth::user()->hasPermission('fees.refund-payment');
        $canPrintFeesInvoiceItem = Auth::user()->hasPermission('fees.invoice-item-print');
    @endphp
    @push('css')
        <style>
            .white-box {
                border-radius: 12px;
                box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
                border: 1px solid var(--border_color);
            }

            .white-box .main-title h3 {
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

            .primary_input_field,
            select.primary_select {
                border-radius: 8px !important;
                transition: border-color .15s ease, box-shadow .15s ease;
            }

            #table_id thead tr th {
                background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
                color: #fff;
                font-weight: 600;
                white-space: nowrap;
                border: none;
            }

            #table_id tbody tr {
                transition: background-color .12s ease;
            }

            #table_id tbody tr:hover {
                background-color: rgba(0, 0, 0, 0.025);
            }

            #table_id tbody td {
                vertical-align: middle;
            }

            .text-muted {
                color: #9aa1ac !important;
            }

            .modern-row-actions .btn-row-action {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                border: 1px solid var(--border_color);
                background: #fff;
                color: #8a90a2;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: background-color .15s ease, color .15s ease, border-color .15s ease;
            }

            /* ============================================================
               Payment History modal — this block was referenced by the
               HTML/JS (invoice-history-card, record-count-badge,
               payment-summary-stats/stat-chip, payment-history-table,
               method-badge, payment-type-pill, certLockedBanner, and the
               read-only row-returned/amount-highlight pair used in the
               View Payment Details modal) but was never actually defined
               anywhere in this file, so the modal rendered unstyled.
               ============================================================ */

            .invoice-history-card {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 10px;
                padding: 14px 18px;
                border-radius: 12px;
                background: linear-gradient(135deg, rgba(102, 126, 234, 0.08), rgba(118, 75, 162, 0.08));
                border: 1px solid rgba(102, 126, 234, 0.15);
            }

            .invoice-history-card .title-group {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .invoice-history-card .title-icon {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, var(--gradient_1), var(--gradient_2));
                color: #fff;
                font-size: 1.1rem;
                flex-shrink: 0;
            }

            .invoice-history-card h3 {
                font-weight: 700;
                font-size: 1.15rem;
                margin: 0;
            }

            .record-count-badge {
                display: inline-block;
                padding: 6px 14px;
                border-radius: 999px;
                background: rgba(102, 126, 234, 0.1);
                color: #5a4fcf;
                font-weight: 600;
                font-size: 0.85rem;
                white-space: nowrap;
            }

            /* Certificate-lock banner (uses Bootstrap's alert/alert-warning as its
               base — this just tightens spacing/icon alignment to match the rest
               of this modal's polish). */
            #certLockedBanner {
                display: flex;
                align-items: center;
                gap: 8px;
                border-radius: 8px;
                font-size: 0.9rem;
                font-weight: 500;
                margin-top: 14px;
            }

            .payment-summary-stats {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin: 18px 0 22px;
            }

            .stat-chip {
                flex: 1 1 180px;
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 14px 16px;
                border-radius: 10px;
                border: 1px solid var(--border_color);
                background: #fff;
            }

            .stat-chip .stat-icon {
                width: 38px;
                height: 38px;
                border-radius: 9px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1rem;
                flex-shrink: 0;
            }

            .stat-chip .stat-label {
                display: block;
                font-size: 0.75rem;
                color: #8a90a2;
                text-transform: uppercase;
                letter-spacing: .03em;
                margin-bottom: 2px;
            }

            .stat-chip .stat-value {
                display: block;
                font-size: 1.05rem;
                font-weight: 700;
                color: #2b2f3a;
            }

            .stat-chip.stat-total .stat-icon {
                background: rgba(102, 126, 234, 0.12);
                color: #667eea;
            }

            .stat-chip.stat-paid .stat-icon {
                background: rgba(40, 167, 69, 0.12);
                color: #28a745;
            }

            .stat-chip.stat-due .stat-icon {
                background: rgba(255, 193, 7, 0.15);
                color: #b8860b;
            }

            .payment-history-table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                margin-bottom: 18px;
                font-size: 0.9rem;
            }

            .payment-history-table thead th {
                background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
                color: #fff;
                font-weight: 600;
                text-align: left;
                padding: 10px 12px;
                white-space: nowrap;
            }

            .payment-history-table thead th:first-child {
                border-top-left-radius: 8px;
            }

            .payment-history-table thead th:last-child {
                border-top-right-radius: 8px;
            }

            .payment-history-table tbody td {
                padding: 10px 12px;
                border-bottom: 1px solid var(--border_color);
                vertical-align: middle;
            }

            .payment-history-table tbody tr:hover {
                background-color: rgba(0, 0, 0, 0.025);
            }

            .payment-history-table .payment-history-empty {
                text-align: center;
                padding: 24px;
                color: #9aa1ac;
            }

            .method-badge {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 999px;
                font-size: 0.78rem;
                font-weight: 600;
                white-space: nowrap;
            }

            .method-badge.method-cash {
                background: rgba(40, 167, 69, 0.12);
                color: #1e7e34;
            }

            .method-badge.method-invoice {
                background: rgba(102, 126, 234, 0.12);
                color: #4c4ec4;
            }

            .method-badge.method-bank_transfer {
                background: rgba(23, 162, 184, 0.12);
                color: #117a8b;
            }

            .method-badge.method-default {
                background: rgba(108, 117, 125, 0.12);
                color: #495057;
            }

            .payment-type-pill {
                display: inline-block;
                padding: 3px 9px;
                border-radius: 999px;
                background: #f1f3f9;
                color: #5b6270;
                font-size: 0.75rem;
                font-weight: 500;
                text-transform: capitalize;
            }

            /* View Payment Details (read-only) modal — highlights a return/refund row */
            .row-returned {
                background-color: rgba(220, 53, 69, 0.05);
            }

            .amount-highlight {
                color: #dc3545;
                font-weight: 700;
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
                </div>
            </div>
        </div>
    </section>
    @php
        $branches = \App\Models\Branch::all();
        $defaultFrom = \Carbon\Carbon::now()->subDays(30)->format('Y-m-d');
        $defaultTo = \Carbon\Carbon::now()->format('Y-m-d');
    @endphp

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box mb-10">
                @if ($canAddFeesInvoice)
                    <div class="row">
                        <div class="col-lg-12 text-left col-md-12">
                            <a href="{{ route('fees.add-invoice') }}" class="primary-btn small fix-gr-bg col-12 p-2">
                                <span class="ti-plus pr-2"></span>
                                @lang('common.add') Registration & Other Payments
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            <div class="white-box mb-10">
                @if ($canAddFeesInvoice)
                    <div class="row">
                        <div class="col-lg-12 text-left col-md-12">
                            <a href="{{ route('fees.add-invoice') }}" class="primary-btn small fix-gr-bg col-12 p-2">
                                <span class="ti-plus pr-2"></span>
                                @lang('common.add') Course, NVQ & Food Payments
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            <div class="white-box mb-0">
                <form id="filterForm" class="mb-0">
                    <div class="row">
                        {{-- Date Range --}}
                        <div class="col-md-4">
                            <input type="text" id="filter_date_range" name="date_range" class="form-control"
                                value="{{ $defaultFrom }} to {{ $defaultTo }}">
                        </div>

                        {{-- Branch --}}
                        <div class="col-md-4">
                            <select name="branch_id" id="filter_branch" class="form-control">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Fee / Payment Type --}}
                        <div class="col-md-4">
                            <select name="payment_type" id="filter_payment_type" class="form-control">
                                <option value="">All</option>
                                <option value="course_fee">Course & Fee</option>
                                <option value="nvq">NVQ</option>
                                <option value="item_sell">Item Sell</option>
                                <option value="food">Food</option>

                                <option value="others">Others</option>
                            </select>
                        </div>

                        {{-- Buttons --}}

                        <div class="col-lg-12 mt-10 text-right">
                            <button type="button" id="applyFilter" class="primary-btn small fix-gr-bg col-12 p-2">
                                <span class="ti-search pr-2"></span> Search
                            </button>
                        </div>

                    </div>
                </form>
            </div>

            <div class="row mt-20 full_wide_table">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">Fee Invoice List</h3>
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
                                                <th>Main Invoice No.</th>
                                                <th>Fee Name</th>
                                                <th>@lang('common.student')</th>
                                                <th>NIC No.</th>
                                                <th>Stu Category</th>
                                                <th>Branch</th>
                                                <th>@lang('admin.admission_no')</th>
                                                <th>@lang('accounts.amount')(Rs)</th>
                                                <th>@lang('fees.paid')(Rs)</th>
                                                <th>@lang('accounts.balance')(Rs)</th>

                                                <th>Payment Status</th>
                                                <th>Payment Date</th>
                                                <th>Updated Date</th>
                                                <th>Due Date</th>
                                                <th>Notes</th>
                                                <th>Inv. Pay No</th>
                                                <th>Invoice Amount</th>
                                                <th>Inv Paid Amount</th>

                                                <th>Inv. Pay Status</th>
                                                <th>Created At</th>
                                                    <th>Updated At</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>

                                    </table>
                                </x-table>
                            </div>

                            {{-- @endforeach --}}

                            <!-- Return Fee Modal -->
                            <div class="modal fade admin-query" id="returnFeeModal"
                                style="background-color: rgba(0, 0, 0, 0.5);">
                                <div class="modal-dialog modal-lg modal-dialog-centered"
                                    style="margin: 0 auto; max-width: 600px; display: flex; align-items: center; min-height: 100vh;">
                                    <div class="modal-content"
                                        style="border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); border: none; overflow: hidden;">
                                        <div class="modal-header"
                                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 25px; border-bottom: none;">
                                            <h4 class="modal-title" id="returnFeeModalTitle"
                                                style="margin: 0; font-weight: 600; font-size: 1.4rem; display: flex; align-items: center;">
                                                <i class="fas fa-undo-alt"
                                                    style="margin-right: 10px; font-size: 1.2rem;"></i>
                                                Return Fee - Invoice #
                                            </h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                style="color: white; opacity: 0.8; font-size: 1.5rem; margin: 0; padding: 0; background: none; border: none;">
                                                &times;
                                            </button>
                                        </div>

                                        <form action="{{ route('return-fees.store') }}" method="POST">
                                            @csrf
                                            <div class="modal-body" style="padding: 30px 25px; background-color: #f8f9fa;">
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
                                                                id="returnStudentName" readonly
                                                                style="background-color: white; border: 1px solid #e1e5eb; border-radius: 8px; padding: 10px 15px; font-size: 0.95rem; color: #495057; box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);">
                                                            <input type="hidden" name="student_id" id="returnStudentId">
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
                                                                id="returnAdmissionNumber" readonly
                                                                style="background-color: white; border: 1px solid #e1e5eb; border-radius: 8px; padding: 10px 15px; font-size: 0.95rem; color: #495057; box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);">
                                                            <input type="hidden" name="admission_number"
                                                                id="returnAdmissionNumberHidden">
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
                                                                id="returnAvailableDisplay" readonly
                                                                style="background-color: #e8f5e8; border: 1px solid #28a745; border-radius: 8px; padding: 12px 15px; font-size: 1.1rem; font-weight: 600; color: #155724; text-align: center; box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);">
                                                            <input type="hidden" name="balance" id="returnBalance">
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
                                                                Return Amount <span style="color: #dc3545;">*</span>
                                                            </label>
                                                            <div class="input-group"
                                                                style="box-shadow: 0 2px 5px rgba(0, 0, 0, .1); border-radius: 8px; overflow: hidden;">
                                                                <input type="number" name="return_amount"
                                                                    id="returnAmountInput" class="form-control"
                                                                    min="0.00" step="0.01" required
                                                                    placeholder="Enter return amount"
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
                                                                Maximum return amount: <strong style="color: #28a745;"
                                                                    id="returnMaxAmountText">0.00</strong>
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
                                                        style="border: 1px solid #e1e5eb; border-radius: 8px; padding: 12px 15px; font-size: 0.95rem; color: #495057; resize: vertical; box-shadow: inset 0 1px 3px rgba(0, 0, 0, .1); transition: border-color 0.3s ease;"></textarea>
                                                </div>

                                                <input type="hidden" name="invoice_id" id="returnInvoiceId">
                                            </div>

                                            <div class="modal-footer"
                                                style="background-color: white; padding: 20px 25px; border-top: 1px solid #e1e5eb; border-radius: 0 0 12px 12px;">
                                                <button type="button" class="primary-btn tr-bg" data-dismiss="modal"
                                                    style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(0, 0, 0, .2);">
                                                    <i class="fas fa-times" style="margin-right: 8px;"></i>
                                                    @lang('common.cancel')
                                                </button>
                                                <button type="submit" class="primary-btn fix-gr-bg"
                                                    style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(40, 167, 69, .3);">
                                                    <i class="fas fa-undo-alt" style="margin-right: 8px;"></i>
                                                    Process Return
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

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
                                                onclick="printInvoice()">Print
                                                Invoice</button>
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
                                            {{-- VISUAL POLISH (Section 30): swapped the plain "Payment History"
                                                 <h5> + bare invoice-number span for the same
                                                 invoice-history-card header pattern used elsewhere
                                                 (course-paid.blade.php, invoice-payments*.blade.php) —
                                                 icon badge + a pill on the right, here showing the main
                                                 invoice number instead of a record count. --}}
                                            <div class="main-title mb-3 invoice-history-card">
                                                <div class="title-group">
                                                    <span class="title-icon"><i
                                                            class="fa-solid fa-clock-rotate-left"></i></span>
                                                    <h3 class="mb-0">Payment History</h3>
                                                </div>
                                                <span class="record-count-badge" id="mainInvoiceNumber">&nbsp;</span>
                                            </div>
                                            {{-- CERTIFICATE LOCK: shown when getPaymentHistory() reports
                                                 certificate_locked = true for this invoice's course — a
                                                 certificate has already been generated, so Return and
                                                 Refund are hidden below and this explains why. --}}
                                            <div id="certLockedBanner" class="alert alert-warning d-none" role="alert">
                                                <i class="fa-solid fa-lock"></i>
                                                A certificate has already been generated for this course — return and refund are locked.
                                            </div>
                                            <div id="paymentHistoryContent">
                                                <!-- Payment history will be loaded here via AJAX -->
                                                <div class="text-center">
                                                    <div class="spinner-border" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                    <p>Loading payment history...</p>
                                                </div>
                                            </div>

                                            <div class="payment-summary-stats">
                                                <div class="stat-chip stat-total">
                                                    <span class="stat-icon"><i
                                                            class="fa-solid fa-file-invoice-dollar"></i></span>
                                                    <span>
                                                        <span class="stat-label">Total Amount</span>
                                                        <span class="stat-value" id="totalAmountDisplay"></span>
                                                    </span>
                                                </div>
                                                <div class="stat-chip stat-paid">
                                                    <span class="stat-icon"><i
                                                            class="fa-solid fa-circle-check"></i></span>
                                                    <span>
                                                        <span class="stat-label">Paid Amount</span>
                                                        <span class="stat-value" id="paidAmountDisplay"></span>
                                                    </span>
                                                </div>
                                                <div class="stat-chip stat-due">
                                                    <span class="stat-icon"><i
                                                            class="fa-solid fa-triangle-exclamation"></i></span>
                                                    <span>
                                                        <span class="stat-label">Due Amount</span>
                                                        <span class="stat-value" id="dueAmountDisplay"></span>
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- PERMISSION GATE (Section 28): fees.add-payment. This form is
                                                 the real "Add Payment" action — separate from
                                                 "fees.payment-history" (the View Payment link that opens this
                                                 whole modal), so a role can view history without being able
                                                 to add a new payment. --}}
                                            @if ($canAddFeesPayment)
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
                                                                        <option value="Bank Transfer">Bank Transfer
                                                                        </option>
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
                                            @else
                                                <div class="text-center text-muted py-2">
                                                    You do not have permission to add payments to this invoice.
                                                </div>
                                            @endif
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
                                                This invoice has been marked as returned. Payment history and return details
                                                are shown below.
                                            </div>

                                            <!-- Return Information Section -->
                                            <div id="returnInfoSection" style="display: none;">
                                                <div class="alert alert-warning" style="border-left: 4px solid #ffc107;">
                                                    <h5><i class="fas fa-undo-alt"></i> Return Information</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Return Amount:</strong> <span
                                                                    id="returnAmountDisplay" class="text-danger"></span>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Return Date:</strong> <span
                                                                    id="returnDateDisplay"></span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <p><strong>Return Reason:</strong> <span
                                                                    id="returnReasonDisplay"></span></p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Returned By:</strong> <span
                                                                    id="returnedByDisplay"></span>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Original Paid Amount:</strong> <span
                                                                    id="originalPaidAmountDisplay"></span></p>
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
                                                    <h5>Returned Amount: <span id="detailsReturnedAmountDisplay"
                                                            class="text-danger">0.00</span></h5>
                                                </div>
                                                <div class="col-md-3">
                                                    <h5>Net Amount: <span id="detailsNetAmountDisplay"
                                                            class="text-success"></span></h5>
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
                            <div class="modal fade admin-query" id="viewBankDetailsModal" tabindex="-1" role="dialog"
                                aria-labelledby="viewBankDetailsModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="viewBankDetailsModalLabel">Bank Payment Details
                                            </h4>
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
                                                        <input type="text" class="form-control"
                                                            id="bankPaymentDateDisplay" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="bankPaymentAmountDisplay">Bank Payment Amount</label>
                                                        <input type="text" class="form-control"
                                                            id="bankPaymentAmountDisplay" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="paymentFileDisplay">Payment File</label>
                                                        <input type="text" class="form-control"
                                                            id="paymentFileDisplay" readonly>
                                                        <a href="#" id="paymentFileLink" target="_blank"
                                                            style="display: none;">View File</a>
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


            <!-- Invoice Report Modal -->
            <div class="modal fade admin-query" id="invoiceReportModal" tabindex="-1" role="dialog"
                aria-labelledby="invoiceReportModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="invoiceReportModalLabel">Invoice Report for Invoice #<span
                                    id="reportInvoiceId"></span></h4>
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
                            <button type="button" class="primary-btn tr-bg"
                                data-dismiss="modal">@lang('common.close')</button>
                            <button type="button" class="primary-btn fix-gr-bg" onclick="printReport()">Print
                                Report</button>
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
        const canRefundFeesPayment = {{ $canRefundFeesPayment ? 'true' : 'false' }};

        const canPrintFeesInvoiceItem = {{ $canPrintFeesInvoiceItem ? 'true' : 'false' }};

        function paymentMethodBadge(method) {
            var key = String(method || '').toLowerCase().trim().replace(/\s+/g, '_');
            var known = ['cash', 'invoice', 'bank_transfer'];
            var cls = known.indexOf(key) !== -1 ? 'method-' + key : 'method-default';
            return '<span class="method-badge ' + cls + '">' + (method || 'N/A') + '</span>';
        }

        flatpickr("#filter_date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: true,
            defaultDate: [
                "{{ \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}",
                "{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
            ]
        });

        var table = $('#table_id').DataTable({
            scrollX: true,
            serverSide: true,
            dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3'" +
                "<'dt-toolbar-title'>" +
                "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-length'l><'dt-search'f><'dt-buttons-wrap'B>>" +
                ">" +
                "<'row'<'col-sm-12'tr>>" +
                "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>>",
            lengthChange: true,
            buttons: [{
                    extend: 'pdf',
                    title: getFileName(),
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    title: getFileName(),
                    text: '<i class="fa fa-file-text-o"></i>',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    title: getFileName(),
                    text: '<i class="fa fa-file-excel-o"></i>',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    title: getFileName(),
                    text: '<i class="fa fa-print"></i>',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'colvis',
                    title: getFileName(),
                    text: '<i class="fa fa-eye"></i>',
                    columns: ':gt(0)'
                }
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            order: [
                [1, 'desc']
            ],
            pageLength: 100,
            lengthMenu: [
                [25, 50, 100, -1],
                [25, 50, 100, "All"]
            ],
            language: {
                search: '<i class=""></i>',
                searchPlaceholder: 'Search...',
                paginate: {
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            },

            // ── Pass filter values with every ajax request ──────────
            ajax: {
                url: "{{ route('fee.invoice.datatable') }}",
                data: function(d) {
                    d.date_range = $('#filter_date_range').val();
                    d.branch_id = $('#filter_branch').val();
                    d.payment_type = $('#filter_payment_type').val();
                }
            },

            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'main_invoice_number',
                    name: 'fee_invoices.main_invoice_number',
                    searchable: true
                },
                {
                    data: 'fee_name',
                    name: 'fee_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'student_name',
                    name: 'students.student_name',
                    searchable: true
                },
                {
                    data: 'nic_number',
                    name: 'nic_number',
                    searchable: true
                },
                {
                    data: 'category_name',
                    name: 'student_categories.category_name',
                    searchable: true
                },
                {
                    data: 'branch_name',
                    name: 'branches.name'
                },
                {
                    data: 'admission_number',
                    name: 'students.admission_number',
                    searchable: true

                },
                {
                    data: 'total_amount',
                    name: 'fee_invoices.total_amount',
                    searchable: false
                }, // ← numeric
                {
                    data: 'paid_amount',
                    name: 'paid_amount',
                    orderable: false,
                    searchable: false
                }, // ← computed
                {
                    data: 'balance',
                    name: 'balance',
                    orderable: false,
                    searchable: false
                }, // ← computed
                {
                    data: 'payment_status',
                    name: 'fee_invoices.payment_status',
                    searchable: false
                },
                {
                    data: 'payment_date',
                    name: 'payment_date',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'updated_date',
                    name: 'updated_date',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'due_date',
                    name: 'due_date',
                    orderable: false,
                    searchable: false
                }, // ← computed
                {
                    data: 'notes',
                    name: 'fee_invoices.notes',
                    searchable: false
                },
                {
                    data: 'inv_payment_number',
                    name: 'inv_payment_number',
                    orderable: false,
                    searchable: false
                }, // ← computed
                {
                    data: 'inv_total_amount',
                    name: 'inv_total_amount',
                    orderable: false,
                    searchable: false
                }, // ← computed
                {
                    data: 'inv_paid_amount',
                    name: 'inv_paid_amount',
                    orderable: false,
                    searchable: false
                }, // ← computed

                {
                    data: 'inv_payment_status',
                    name: 'inv_payment_status',
                    orderable: false,
                    searchable: false
                }, 
                {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'updated_at',
                    name: 'updated_at',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },

            ],
        });


        // ── Filter button ────────────────────────────────────────────
        $('#applyFilter').on('click', function() {
            table.ajax.reload();
        });

        // ── Reset button ─────────────────────────────────────────────
        $('#resetFilter').on('click', function() {
            $('#filter_date_range').value = '';
            // Reset flatpickr to default 30-day range
            var fp = document.querySelector('#filter_date_range')._flatpickr;
            fp.setDate([
                "{{ \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}",
                "{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
            ]);
            $('#filter_branch').val('');
            $('#filter_payment_type').val('');
            table.ajax.reload();
        });

        function getFileName() {
            let now = new Date();
            let date = now.toISOString().slice(0, 10);
            let time = now.toTimeString().slice(0, 8).replace(/:/g, '-');
            return 'Fee-Invoice_' + date + '__' + time;
        }


        // Handle return modal with row data from datatable action button
        $('#returnFeeModal').on('show.bs.modal', function(event) {
            console.log('Return Fee Modal triggered');
            var button = $(event.relatedTarget);
            var invoiceId = button.data('invoice-id') || '';
            var studentId = button.data('student-id') || '';
            var studentName = button.data('student-name') || '';
            var admissionNumber = button.data('admission-number') || '';
            var paidAmountRaw = parseFloat(button.data('paid-amount')) || 0;
            var paidAmount = paidAmountRaw.toFixed(2);

            console.log('Data extracted for Return Modal:', {
                invoiceId: invoiceId,
                studentId: studentId,
                studentName: studentName,
                admissionNumber: admissionNumber,
                paidAmount: paidAmount
            });

            $('#returnFeeModalTitle').html(
                '<i class="fas fa-undo-alt" style="margin-right: 10px; font-size: 1.2rem;"></i>Return Fee - Invoice #' +
                invoiceId);
            $('#returnInvoiceId').val(invoiceId);
            $('#returnStudentId').val(studentId);
            $('#returnStudentName').val(studentName);
            $('#returnAdmissionNumber').val(admissionNumber);
            $('#returnAdmissionNumberHidden').val(admissionNumber);
            $('#returnAvailableDisplay').val(paidAmount);
            $('#returnBalance').val(paidAmountRaw);
            $('#returnAmountInput').val('');
            $('#returnAmountInput').attr('max', paidAmountRaw);
            $('#returnMaxAmountText').text(paidAmount);
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

        // BUG FIX (Section 28): populateInvoiceModal(element) above was a fully-built,
        // working function with no caller anywhere in this file — the row's "Invoice"
        // dropdown link only had data-toggle/data-target to auto-open #makeInvoiceModal
        // via Bootstrap, but nothing ever called populateInvoiceModal() to fill it in.
        // The modal opened with every field showing its blank "____" placeholder
        // every single time, for every invoice. Fixed the same way #returnFeeModal and
        // #viewPaymentHistoryModal already populate themselves (Bootstrap passes the
        // clicked element as event.relatedTarget on show.bs.modal) rather than adding
        // an onclick to the link itself, to match this file's own existing convention.
        $('#makeInvoiceModal').on('show.bs.modal', function(event) {
            populateInvoiceModal(event.relatedTarget);
        });

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
                url: '/student/fees/invoice-list/mark-as-paid', // Replace with your actual route
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
                url: `/student/fees/invoice-list/payment-history/${invoiceId}`, // New route for fetching payment history
                type: 'GET',
                beforeSend: function() {
                    modal.find('#certLockedBanner').addClass('d-none');
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
                    modal.find('#mainInvoiceNumber').text(response.main_invoice_number ?
                        ('Invoice: ' + response.main_invoice_number) : ' ');
                    modal.find('#paidAmountDisplay').text(response
                        .paid_amount_sum); // Display sum of paid amounts
                    modal.find('#dueAmountDisplay').text(response.due_amount);

                    // Set the max attribute for the payment amount input
                    modal.find('#paymentAmountInput').attr('max', response.due_amount);


                    modal.find('#paymentAmountInput').attr('max', response.due_amount);
                    var isItemSell = response.invoice_type === 'item_sell';

                    // CERTIFICATE LOCK: getPaymentHistory() now reports certificate_locked when a
                    // Certificate already exists for this invoice's course(s) (see
                    // InvoiceController::invoiceHasCertificateLock()). When locked, the Refund
                    // action is hidden below (mirrors the server-side block in refundPayment())
                    // and the banner near the top of the modal is shown.
                    var certLocked = !!response.certificate_locked;
                    modal.find('#certLockedBanner').toggleClass('d-none', !certLocked);

                    let historyHtml =
                        '<table class="payment-history-table"><thead><tr><th>Invoice Number</th><th>Course Name</th><th>Amount</th><th>Method</th><th>Type</th><th>Receipt No.</th><th>Date</th><th>Action</th></tr></thead><tbody>';
                    if (response.payments.length > 0) {
                        let baseUrl = "{{ url('/student/fees/invoice-item-print') }}";
                        response.payments.forEach(function(payment) {

                            // BUG FIX (Section 30): this Re-print link had no permission check at
                            // all — see $canPrintFeesInvoiceItem in the PHP block above.
                            let actionDropdown = '';
                            if (canPrintFeesInvoiceItem) {
                                actionDropdown +=
                                    `<a class="dropdown-item" href="${baseUrl}/${invoiceId}/${payment.id}" target="_blank"><i class="fa-solid fa-print text-muted"></i> Re-print</a>`;
                            }

                            // BUG FIX (Section 28): this "Add Invoice No." link was commented out
                            // here but was live (uncommented) in the near-duplicate copy of this
                            // same row-renderer inside loadPaymentHistory() further down this file
                            // — so the button appeared or not depending on which of the two
                            // identical-looking code paths happened to render the row (present
                            // after a refresh, missing on first open). Re-enabled here to match.
                            if (payment.payment_method === 'Invoice' && !payment
                                .payment_invoice_number) {
                                actionDropdown +=
                                    `<a class="dropdown-item add-payment-invoice-number-btn" href="#" data-payment-id="${payment.id}"><i class="fa-solid fa-hashtag text-primary"></i> Add Invoice No.</a>`;
                            }
                            // FLAGGED, NOT ENABLED (Section 28): "Confirm Payment Invoice" has a
                            // real permission key (fees.confirm-invoice-payment, already in
                            // config_permissions.php) and a working click handler
                            // (.confirm-payment-invoice-btn, further down this file, posts to
                            // /confirm-invoice-payment/{paymentId}) — but the trigger link below is
                            // commented out in BOTH copies of this row-renderer, so this feature is
                            // currently unreachable for every role regardless of permissions. Left
                            // disabled rather than guessing this should be turned on as part of a
                            // permission-gating pass — that's a feature decision, not a permission
                            // one. Uncomment (with a permission check, same pattern as Refund below)
                            // if you want it live:
                            // if (payment.payment_method === 'Invoice' && payment
                            //     .payment_invoice_number && !payment.invoice_payment_confirm) {
                            //     actionDropdown +=
                            //         `<a class="dropdown-item confirm-payment-invoice-btn" href="#" data-payment-id="${payment.id}"><i class="fa-solid fa-check text-success"></i> Confirm Payment Invoice</a>`;
                            // }

                            if (payment.payment_method === 'Bank Transfer') {
                                actionDropdown +=
                                    `<a class="dropdown-item view-bank-details-btn" href="#" data-payment-id="${payment.id}" data-bank-date="${payment.bank_payment_date}" data-bank-amount="${payment.bank_payment_amount}" data-payment-file="${payment.payment_file || ''}"><i class="fa-solid fa-building-columns text-success"></i> View Bank Details</a>`;
                            }

                            const paymentDate = new Date(payment.bank_payment_date || payment
                                .created_at);
                            const today = new Date();
                            const isBankTransfer = payment.payment_method === 'Bank Transfer';

                            const isCurrentMonth =
                                paymentDate.getFullYear() === today.getFullYear() &&
                                paymentDate.getMonth() === today.getMonth();

                            const isRefundable = (isBankTransfer && isCurrentMonth && !
                                isItemSell) || (!isBankTransfer && !isItemSell)

                            console.log('Payment ID:', payment.id, isRefundable, isBankTransfer,
                                isCurrentMonth, isItemSell);

                            if (
                                !certLocked &&
                                canRefundFeesPayment &&
                                isRefundable &&
                                payment.payment_invoice_number == null
                            ) {
                                if (
                                    payment.payment_status !== 'refunded' &&
                                    payment.amount > 0
                                ) {
                                    actionDropdown += `
                                    <a class="dropdown-item refund-btn"
                                    href="#"
                                    data-payment-id="${payment.id}"
                                    data-amount="${payment.amount}">
                                    <i class="fa-solid fa-rotate-left text-danger"></i> Refund
                                    </a>`;
                                }
                            }


                            historyHtml += `
                                <tr>
                                    <td>
                                        ${payment.payment_invoice_number || 'N/A'}
                                        ${payment.invoice_payment_confirm == 1 ? '- C' : ''}
                                    </td>
                                    <td>${courseName || 'N/A'}</td>
                                    <td>${payment.amount}</td>
                                    <td>${paymentMethodBadge(payment.payment_method)}</td>
                                    <td><span class="payment-type-pill">${(payment.payment_type || 'N/A').toString().replace(/_/g, ' ')}</span></td>
                                    <td>${payment.receipt_number}</td>
                                    <td>${new Date(payment.created_at).toLocaleDateString()}</td>
                                    <td>
                                        ${actionDropdown ? `
                                                                                        <div class="dropdown modern-row-actions">
                                                                                            <button class="btn-row-action" type="button" data-toggle="dropdown" aria-expanded="false">
                                                                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                                                                            </button>
                                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                                ${actionDropdown}
                                                                                            </div>
                                                                                        </div>
                                                                                    ` : '<span class="text-muted">&mdash;</span>'}
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        historyHtml +=
                            '<tr><td colspan="8" class="payment-history-empty">No payment history found.</td></tr>';
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


        // invoice payment confirmation for invoice payment method
        $(document).on('click', '.confirm-payment-invoice-btn', function(e) {
            e.preventDefault();
            var paymentId = $(this).data('payment-id');
            var button = $(this);

            if (!confirm('Are you sure you want to confirm this payment invoice?')) return;

            $.ajax({
                url: `/student/fees/invoice-list/confirm-invoice-payment/${paymentId}`, // Your route
                type: 'GET', // or 'POST' if you prefer
                success: function(response) {
                    console.log('Invoice payment confirmed:', response);

                    // Option 1: Disable the button and show "Confirmed"
                    button.replaceWith(
                        `<span class="badge badge-success" style="padding:4px;margin:2px;">Confirmed</span>`
                    );

                    // Option 2: Refresh payment history table
                    var invoiceId = $('#addPaymentInvoiceId').val(); // Or get dynamically if needed
                    loadPaymentHistory(invoiceId, response.course_name || 'N/A');
                },
                error: function(xhr) {
                    alert('Error confirming payment invoice. Please try again.');
                    console.error(xhr.responseText);
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
                url: `/student/fees/invoice-list/payment-history/${invoiceId}`,
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
                        url: `/student/fees/invoice-list/return-fee-data/${invoiceId}`,
                        type: 'GET',
                        success: function(returnResponse) {
                            console.log('Return fee data:', returnResponse);

                            var returnAmount = 0;
                            var returnDate = '';
                            var returnReason = '';
                            var returnedBy = '';
                            var originalPaidAmount = response.paid_amount_sum;

                            if (returnResponse.return_fees && returnResponse.return_fees
                                .length > 0) {
                                // Sum all return amounts for this invoice
                                returnResponse.return_fees.forEach(function(returnFee) {
                                    returnAmount += parseFloat(returnFee
                                        .return_amount) || 0;
                                    if (!returnDate && returnFee.created_at) {
                                        returnDate = new Date(returnFee.created_at)
                                            .toLocaleDateString();
                                    }
                                    if (returnFee.short_note) {
                                        returnReason = returnFee.short_note;
                                    }
                                    if (returnFee.return_user && returnFee
                                        .return_user.first_name) {
                                        returnedBy = returnFee.return_user
                                            .first_name + ' ' + (returnFee
                                                .return_user.last_name || '');
                                    }
                                });
                            }

                            // Show return information section if there are returns
                            if (returnAmount > 0) {
                                modal.find('#returnInfoSection').show();
                                modal.find('#returnAmountDisplay').text(returnAmount
                                    .toFixed(2));
                                modal.find('#returnDateDisplay').text(returnDate || 'N/A');
                                modal.find('#returnReasonDisplay').text(returnReason ||
                                    'N/A');
                                modal.find('#returnedByDisplay').text(returnedBy || 'N/A');
                                modal.find('#originalPaidAmountDisplay').text(
                                    originalPaidAmount.toFixed(2));

                                // Calculate and display net amount
                                var netAmount = originalPaidAmount - returnAmount;
                                modal.find('#detailsReturnedAmountDisplay').text(
                                    returnAmount.toFixed(2));
                                modal.find('#detailsNetAmountDisplay').text(netAmount
                                    .toFixed(2));

                                // Add color coding for net amount
                                if (netAmount < 0) {
                                    modal.find('#detailsNetAmountDisplay').addClass(
                                        'text-danger').removeClass('text-success');
                                } else {
                                    modal.find('#detailsNetAmountDisplay').addClass(
                                        'text-success').removeClass('text-danger');
                                }
                            } else {
                                modal.find('#returnInfoSection').hide();
                                modal.find('#detailsReturnedAmountDisplay').text('0.00');
                                modal.find('#detailsNetAmountDisplay').text(
                                    originalPaidAmount.toFixed(2));
                            }

                            // Create read-only payment history table (no action buttons)
                            // VISUAL POLISH (Section 30): same .payment-history-table / badge
                            // classes as the editable Payment History modal, instead of the
                            // heavy inline style="" attributes this read-only twin table used.
                            let detailsHtml =
                                '<table class="payment-history-table"><thead><tr><th>Invoice Number</th><th>Amount</th><th>Method</th><th>Type</th><th>Receipt No.</th><th>Date</th></tr></thead><tbody>';
                            if (response.payments.length > 0) {
                                response.payments.forEach(function(payment) {
                                    // Highlight return/refund payments
                                    var isReturn = payment.amount < 0 || payment
                                        .payment_type === 'return';
                                    var rowClass = isReturn ? 'row-returned' : '';
                                    var amountClass = isReturn ?
                                        'amount-highlight' : '';

                                    detailsHtml += `
                                        <tr class="${rowClass}">
                                            <td>${payment.payment_invoice_number || 'N/A'}</td>
                                            <td class="${amountClass}">${payment.amount}</td>
                                            <td>${paymentMethodBadge(payment.payment_method)}</td>
                                            <td><span class="payment-type-pill">${(payment.payment_type || 'N/A').toString().replace(/_/g, ' ')}</span></td>
                                            <td>${payment.receipt_number}</td>
                                            <td>${new Date(payment.created_at).toLocaleDateString()}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                detailsHtml +=
                                    '<tr><td colspan="6" class="payment-history-empty">No payment history found.</td></tr>';
                            }
                            detailsHtml += '</tbody></table>';
                            modal.find('#paymentDetailsContent').html(detailsHtml);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching return fee data:', xhr
                                .responseText);
                            // Continue with payment details even if return fee fetch fails
                            modal.find('#returnInfoSection').hide();
                            modal.find('#detailsReturnedAmountDisplay').text('0.00');
                            modal.find('#detailsNetAmountDisplay').text(response
                                .paid_amount_sum);

                            // Still show payment history
                            let detailsHtml =
                                '<table class="payment-history-table"><thead><tr><th>Invoice Number</th><th>Amount</th><th>Method</th><th>Type</th><th>Receipt No.</th><th>Date</th></tr></thead><tbody>';
                            if (response.payments.length > 0) {
                                response.payments.forEach(function(payment) {
                                    detailsHtml += `
                                        <tr>
                                            <td>${payment.payment_invoice_number || 'N/A'}</td>
                                            <td>${payment.amount}</td>
                                            <td>${paymentMethodBadge(payment.payment_method)}</td>
                                            <td><span class="payment-type-pill">${(payment.payment_type || 'N/A').toString().replace(/_/g, ' ')}</span></td>
                                            <td>${payment.receipt_number}</td>
                                            <td>${new Date(payment.created_at).toLocaleDateString()}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                detailsHtml +=
                                    '<tr><td colspan="6" class="payment-history-empty">No payment history found.</td></tr>';
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
                url: `/student/fees/invoice-list/add-payment/${invoiceId}`, // Existing route for adding payments
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

            // Hide view modal if visible
            var viewModal = $('#viewPaymentHistoryModal');
            if (viewModal.is(':visible')) {
                console.log('Hiding view modal for payment ID:', paymentId);
                viewModal.modal('hide');
            }

            // Set payment id in Add Invoice modal
            $('#addInvoiceNumberPaymentId').val(paymentId);

            // Open Add Invoice modal
            $('#addInvoiceNumberModal').modal('show');
        });

        function loadPaymentHistory(invoiceId, courseName) {
            var modal = $('#viewPaymentHistoryModal');

            modal.find('#paymentHistoryInvoiceId').text(invoiceId);
            modal.find('#paymentHistoryCourseName').text(courseName || 'N/A'); // optional
            modal.find('#addPaymentInvoiceId').val(invoiceId); // set invoice ID for add payment form

            $.ajax({
                url: `/student/fees/invoice-list/payment-history/${invoiceId}`,
                type: 'GET',
                beforeSend: function() {
                    modal.find('#certLockedBanner').addClass('d-none');
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
                    modal.find('#totalAmountDisplay').text(response.total_amount);
                    modal.find('#mainInvoiceNumber').text(response.main_invoice_number ?
                        ('Invoice: ' + response.main_invoice_number) : ' ');
                    modal.find('#paidAmountDisplay').text(response.paid_amount_sum);
                    modal.find('#dueAmountDisplay').text(response.due_amount);
                    modal.find('#paymentAmountInput').attr('max', response.due_amount);

                    var isItemSell = response.invoice_type === 'item_sell';

                    // CERTIFICATE LOCK: same flag as the show.bs.modal handler above — see the
                    // comment there. Kept in both places since this is the near-duplicate
                    // row-renderer this file already has for the modal's re-render path.
                    var certLocked = !!response.certificate_locked;
                    modal.find('#certLockedBanner').toggleClass('d-none', !certLocked);

                    // Build the history table HTML (reuse your existing code)
                    let historyHtml =
                        '<table class="payment-history-table"><thead><tr><th>Invoice Number</th><th>Course Name</th><th>Amount</th><th>Method</th><th>Type</th><th>Receipt No.</th><th>Date</th><th>Action</th></tr></thead><tbody>';

                    if (response.payments.length > 0) {
                        let baseUrl = "{{ url('/student/fees/invoice-item-print') }}";
                        response.payments.forEach(function(payment) {
                            // BUG FIX (Section 30): this Re-print link had no permission check
                            // at all — see $canPrintFeesInvoiceItem in the PHP block above.
                            let actionDropdown = '';
                            if (canPrintFeesInvoiceItem) {
                                actionDropdown +=
                                    `<a class="dropdown-item" href="${baseUrl}/${invoiceId}/${payment.id}" target="_blank"><i class="fa-solid fa-print text-muted"></i> Re-print</a>`;
                            }

                            if (payment.payment_method === 'Invoice' && !payment
                                .payment_invoice_number) {
                                actionDropdown +=
                                    `<a class="dropdown-item add-payment-invoice-number-btn" href="#" data-payment-id="${payment.id}"><i class="fa-solid fa-hashtag text-primary"></i> Add Invoice No.</a>`;
                            }
                            if (payment.payment_method === 'Bank Transfer') {
                                actionDropdown +=
                                    `<a class="dropdown-item view-bank-details-btn" href="#" data-payment-id="${payment.id}" data-bank-date="${payment.bank_payment_date}" data-bank-amount="${payment.bank_payment_amount}" data-payment-file="${payment.payment_file || ''}"><i class="fa-solid fa-building-columns text-success"></i> View Bank Details</a>`;
                            }
                            if (!certLocked && canRefundFeesPayment && !isItemSell && payment
                                .payment_status !==
                                'refunded' && payment.amount >
                                0) { // added !isItemSell check
                                actionDropdown +=
                                    `<a class="dropdown-item refund-btn" href="#" data-payment-id="${payment.id}" data-amount="${payment.amount}"><i class="fa-solid fa-rotate-left text-danger"></i> Refund</a>`;
                            }

                            historyHtml += `
                        <tr>
                            <td>
                                        ${payment.payment_invoice_number || 'N/A'}
                                        ${payment.invoice_payment_confirm == 1 ? '- C' : ''}
                             </td>
                            <td>${courseName || 'N/A'}</td>
                            <td>${payment.amount}</td>
                            <td>${paymentMethodBadge(payment.payment_method)}</td>
                            <td><span class="payment-type-pill">${(payment.payment_type || 'N/A').toString().replace(/_/g, ' ')}</span></td>
                            <td>${payment.receipt_number}</td>
                            <td>${new Date(payment.created_at).toLocaleDateString()}</td>
                            <td>${actionDropdown ? `<div class="dropdown modern-row-actions"><button class="btn-row-action" type="button" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></button><div class="dropdown-menu dropdown-menu-right">${actionDropdown}</div></div>` : '<span class="text-muted">&mdash;</span>'}</td>
                        </tr>
                    `;
                        });
                    } else {
                        historyHtml +=
                            '<tr><td colspan="8" class="payment-history-empty">No payment history found.</td></tr>';
                    }

                    historyHtml += '</tbody></table>';
                    modal.find('#paymentHistoryContent').html(historyHtml);
                },
                error: function(xhr) {
                    modal.find('#paymentHistoryContent').html(
                        `<div class="alert alert-danger">Error loading payment history. Please try again.</div>`
                    );
                }
            });
        }
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
                    $('#viewPaymentHistoryModal').modal('show');

                    // Refresh the payment history table inside the modal
                    loadPaymentHistory(invoiceId, response.course_name || 'N/A');


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
                    url: `/student/fees/invoice-list/refund-payment/${paymentId}`, // New route for refunding payments
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

                        if (xhr.status === 403) {
                            let message = JSON.parse(xhr.responseText).message;
                            alert(message, "Permission Denied");
                            // toastr.error(message, "Permission Denied");
                        } else {

                            alert("Error refunding payment. Please try again.");
                            // toastr.error("Error refunding payment. Please try again.");
                        }
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
            var paymentFile = $(this).data('payment-file');

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

            // Handle payment file display
            if (paymentFile) {
                $('#paymentFileDisplay').val(paymentFile.split('/').pop()); // Display only the file name
                $('#paymentFileLink').attr('href', `{{ url('storage') }}/${paymentFile}`).show();
            } else {
                $('#paymentFileDisplay').val('N/A');
                $('#paymentFileLink').hide();
            }

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
                url: `/student/fees/invoice-print/${invoiceId}`, // New route for fetching invoice report
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

                    // Format created_to_due to YMD

                    // Generate report HTML based on the provided style
                    let reportHtml = `
<style>
@media print {
  @page {
    size: 21.5cm 14cm portrait;
    margin: 0;
  }

  html, body {
    margin: 0;
    padding: 0;
    background: #fff;
  }


  .invoice-report {
    width: 100%;  /* Full paper width */
    height: 14cm;   /* Full paper height */
    position: relative;
    font-family: 'Arial Black', 'Courier New', monospace;
    font-size: 16px;
    color: #000;
  }

  .field {
    position: absolute;
    font-family: 'Arial Black', 'Courier New', monospace;
    color: #000;
    line-height: 1;
    font-size: 16px;
    font-weight: bold;
  }

  .field2 {
    position: absolute;
    font-family: 'Arial Black', 'Courier New', monospace;
    color: #000;
    line-height: 1;
    font-size: 14.5px;
  }

.course-name   { left: 4.2cm;  top: 4.7cm; font-weight: bold; font-size: 18px; }
  .invoice-date  { left: 19.0cm; top: 4.8cm; }
  .invoice-no    { left: 19.0cm; top: 5.5cm; font-weight: bold; }
  .admission-no  { left: 19.0cm; top: 6.1cm; font-weight: bold; }

  .student-name      { left: 9.7cm; top: 6.58cm; font-weight: bold; }
  .student-nic       { left: 9.7cm; top: 7.3cm; font-weight: bold; }
  .student-address   { left: 9.7cm; top: 8.1cm; font-weight: bold; }
  .student-phone     { left: 9.7cm; top: 8.9cm; font-weight: bold; }

  .total-amount  { left: 7.2cm; top: 9.72cm; font-weight: bold; }
  .paid-amount   { left: 7.2cm; top: 10.42cm; font-weight: bold; }
  .due-amount    { left: 7.2cm; top: 11.2cm; font-weight: bold; }
  .due-date      { left: 7.2cm; top: 12.0cm; font-weight: bold; }
}
</style>

<div class="invoice-report">
  <div class="field course-name">
    ${response.course_name && response.course_name !== 'N/A'
      ? response.course_name
      : (response.invoice_items && response.invoice_items.length > 0
        ? response.invoice_items[0].fee_type.name
        : 'Fee Details')}
  </div>

  <div class="field2 invoice-date">${formattedDate}</div>
  <div class="field2 invoice-no">${response.invoice.main_invoice_number}</div>
  <div class="field2 admission-no">${response.invoice.student.admission_number}</div>

  <div class="field student-name">${response.invoice.student.student_name}</div>
  <div class="field student-nic">${response.invoice.student.national_id_number || 'N/A'}</div>
  <div class="field student-address">${response.invoice.student.permanent_address || 'N/A'}</div>
  <div class="field student-phone">${response.invoice.student.phone_number || 'N/A'}</div>

  <div class="field total-amount">${response.invoice.total_amount}</div>
  <div class="field paid-amount">${response.invoice.paid_amount}</div>
  <div class="field due-amount">${response.invoice.total_amount - response.invoice.paid_amount}</div>
  <div class="field due-date">${response.created_to_due}</div>
</div>
`;

                    modal.find('#invoiceReportContent').html(reportHtml);
                    // Automatically print the report after content is loaded
                    printReport();
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