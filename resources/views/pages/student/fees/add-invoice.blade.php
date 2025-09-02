@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    @push('css')
        <link rel="stylesheet" href="{{ url('Modules\Fees\Resources\assets\css\feesStyle.css') }}" />
        <style>
            .ti-calendar:before {
                position: relative !important;
                top: 5px !important;
            }

            .school-table-style tr th {
                padding: 10px 18px 10px 10px !important;
            }

            .school-table-style tr td {
                padding: 20px 10px 20px 10px !important;
            }

            .school-table-style tfoot tr td {
                padding: 10px 10px 10px 10px !important;
            }

            html[dir="rtl"] #main-content {
                overflow: initial !important;
            }
        </style>
    @endpush

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>
                    @if (isset($invoiceInfo))
                        @lang('common.edit')
                    @else
                        @lang('common.add')
                    @endif
                    @lang('fees.fees_invoice')
                </h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees')</a>
                    {{-- <a href="{{ route('fees.fees-invoice-list') }}">@lang('fees.fees_invoice')</a> --}}
                    <a href="#">
                        @if (isset($invoiceInfo))
                            @lang('common.edit')
                        @else
                            @lang('common.add')
                        @endif
                        @lang('fees.fees_invoice')
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            {{-- @if (isset($invoiceInfo))
                {{ Form::open(['class' => 'form-horizontal', 'method' => 'POST', 'route' => 'fees.fees-invoice-update']) }}
                <input type="hidden" name="id" class="editValue" value="{{ $invoiceInfo->id }}">
            @else
                {{ Form::open(['class' => 'form-horizontal', 'method' => 'POST', 'route' => 'fees.fees-invoice-store']) }}
            @endif --}}
            <form class="form-horizontal">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                                <div class="white-box">

                                    <div class="main-title">
                                        <h3 class="mb-15">
                                            @if (isset($invoiceInfo))
                                                @lang('common.edit')
                                            @else
                                                @lang('common.add')
                                            @endif
                                            @lang('fees.fees_invoice')
                                        </h3>
                                    </div>

                                    <div class="add-visitor">
                                        <div class="row">
                                            <div class="col-lg-12 d-flex">
                                                <div>@lang('fees.invoice')- &nbsp;</div>
                                                <div class="d-flex" id="showValue"></div>
                                                <input type="hidden" id="fees_invoice_prefix"
                                                    value="{{ @$invoiceSettings->prefix }}">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12 mt-30-md" id="id-card-div">
                                                <label for="class"> {{ __('common.branch') }} <span
                                                        class="text-danger">*</span></label>
                                                <select
                                                    class="primary_select form-control{{ $errors->has('branch') ? ' is-invalid' : '' }}"
                                                    id="branch_id" name="branch">
                                                    <option data-display="@lang('common.select_class') *" value="">
                                                        @lang('common.select_class')</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}"
                                                            {{ isset($invoiceInfo) ? ($invoiceInfo->branch_id == $branch->id ? 'selected' : '') : (old('branch') == $branch->id ? 'selected' : '') }}>
                                                            {{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('class'))
                                                    <span class="text-danger invalid-select" role="alert">
                                                        {{ $errors->first('class') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-15">
                                            <div class="col-lg-12" id="selectStudentDiv">
                                                <label class="primary_input_label" for="">
                                                    {{ __('common.select_student') }}
                                                    <span class="text-danger"> *</span>
                                                </label>
                                                <select
                                                    class="primary_select form-control{{ $errors->has('student') ? ' is-invalid' : '' }}"
                                                    id="student_id" name="student">
                                                    <option data-display="@lang('common.select_student') *" value="">
                                                        @lang('common.select_student')*</option>
                                                    @if (isset($invoiceInfo))
                                                        @foreach ($students as $student)
                                                            <option value="{{ $student->id }}"
                                                                {{ $student->id == $invoiceInfo->record_id ? 'selected' : '' }}>
                                                                {{ $student->studentDetail->full_name }}
                                                                ({{ $student->section->section_name }} -
                                                                {{ $student->roll_no }})
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <div class="pull-right loader" id="selectStudentLoader"
                                                    style="margin-top: -30px;padding-right: 21px;">
                                                    <img src="{{ asset('Modules/Fees/Resources/assets/img/pre-loader.gif') }}"
                                                        alt="" style="width: 28px;height:28px;">
                                                </div>
                                                @if ($errors->has('student'))
                                                    <span class="text-danger invalid-select" role="alert">
                                                        {{ $errors->first('student') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-15">

                                            <div class="col-lg-12">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="">@lang('fees.create_date')
                                                        <span class="text-danger"> *</span></label>
                                                    <div class="primary_datepicker_input">
                                                        <div class="no-gutters input-right-icon">
                                                            <div class="col">
                                                                <div class="">
                                                                    <input
                                                                        class="primary_input_field  primary_input_field date form-control form-control{{ $errors->has('create_date') ? ' is-invalid' : '' }}"
                                                                        id="create_date" type="text" name="create_date"
                                                                        value="{{ isset($invoiceInfo) ? date('m/d/Y', strtotime($invoiceInfo->create_date)) : date('m/d/Y') }}">
                                                                </div>
                                                            </div>
                                                            <button class="btn-date" data-id="#create_date" type="button">
                                                                <label for="create_date">
                                                                    <i class="ti-calendar" id="create_date"></i>
                                                                </label>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('create_date'))
                                                        <span class="text-danger invalid-select" role="alert">
                                                            {{ $errors->first('create_date') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row mt-15">

                                            <div class="col-lg-12">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="">@lang('fees.due_date')
                                                        <span class="text-danger"> *</span></label>
                                                    <div class="primary_datepicker_input">
                                                        <div class="no-gutters input-right-icon">
                                                            <div class="col">
                                                                <div class="">
                                                                    <input
                                                                        class="primary_input_field  primary_input_field date form-control form-control{{ $errors->has('due_date') ? ' is-invalid' : '' }}"
                                                                        id="due_date" type="text" name="due_date"
                                                                        value="{{ isset($invoiceInfo) ? date('m/d/Y', strtotime($invoiceInfo->due_date)) : date('m/d/Y') }}">
                                                                </div>
                                                            </div>
                                                            <button class="btn-date" data-id="#due_date" type="button">
                                                                <label for="due_date">
                                                                    <i class="ti-calendar" id="due_date"></i>
                                                                </label>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @if ($errors->has('due_date'))
                                                        <span class="text-danger invalid-select" role="alert">
                                                            {{ $errors->first('due_date') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-15 {{ isset($invoiceInfo) ? 'd-none' : '' }}">
                                            <div class="col-lg-12">
                                                <label class="primary_input_label" for="">
                                                    {{ __('fees.payment_status') }}
                                                    <span class="text-danger"> *</span>
                                                </label>
                                                <select
                                                    class="primary_select  form-control{{ $errors->has('payment_status') ? ' is-invalid' : '' }}"
                                                    name="payment_status" id="paymentStatus">
                                                    <option data-display="@lang('fees.payment_status') *" value="">
                                                        @lang('fees.payment_status') *</option>
                                                    <option value="not"
                                                        {{ isset($invoiceInfo) ? ($invoiceInfo->payment_status == 'not' ? 'selected' : '') : (old('payment_status') == 'not' ? 'selected' : '') }}>
                                                        @lang('fees.not_paid')</option>
                                                    <option value="partial"
                                                        {{ isset($invoiceInfo) ? ($invoiceInfo->payment_status == 'partial' ? 'selected' : '') : (old('payment_status') == 'partial' ? 'selected' : '') }}>
                                                        @lang('fees.partial_paid')</option>
                                                    <option value="full"
                                                        {{ isset($invoiceInfo) ? ($invoiceInfo->payment_status == 'full' ? 'selected' : '') : (old('payment_status') == 'full' ? 'selected' : '') }}>
                                                        @lang('fees.full_paid')</option>
                                                </select>
                                                @if ($errors->has('payment_status'))
                                                    <span class="text-danger invalid-select" role="alert">
                                                        {{ $errors->first('payment_status') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-15 d-none" id="paymentMethod">
                                            <div class="col-lg-12">
                                                <label class="primary_input_label" for="">
                                                    {{ __('fees.payment_method') }}
                                                    <span class="text-danger"> *</span>
                                                </label>

                                                <?php
                                                $paymentMethods = collect([(object) ['method' => 'Cash'], (object) ['method' => 'Card'], (object) ['method' => 'Bank Transfer'], (object) ['method' => 'Cheque']]);
                                                
                                                ?>
                                                <select
                                                    class="primary_select  form-control{{ $errors->has('payment_method') ? ' is-invalid' : '' }}"
                                                    name="payment_method" id="paymentMethodName">
                                                    <option data-display="@lang('fees.payment_method')*" value="">
                                                        @lang('fees.payment_method')*</option>
                                                    @foreach ($paymentMethods as $paymentMethod)
                                                        <option value="{{ $paymentMethod->method }}"
                                                            {{ isset($invoiceInfo) ? ($invoiceInfo->payment_method == $paymentMethod->method ? 'selected' : '') : (old('payment_method') == $paymentMethod->method ? 'selected' : '') }}>
                                                            {{ $paymentMethod->method }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('payment_method'))
                                                    <span class="text-danger invalid-select" role="alert">
                                                        {{ $errors->first('payment_method') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-15 d-none" id="bankPayment">
                                            <?php
                                            $bankAccounts = collect([(object) ['id' => 1, 'bank_name' => 'Commercial Bank', 'account_number' => '1234567890'], (object) ['id' => 2, 'bank_name' => 'BOC', 'account_number' => '9876543210']]);
                                            ?>
                                            <div class="col-lg-12">
                                                <label class="primary_input_label" for="">
                                                    {{ __('fees.bank') }}
                                                    <span class="text-danger"> *</span>
                                                </label>
                                                <select
                                                    class="primary_select  form-control{{ $errors->has('bank') ? ' is-invalid' : '' }}"
                                                    name="bank">
                                                    <option data-display="@lang('fees.select_bank')*" value="">
                                                        @lang('fees.select_bank')*</option>
                                                    @foreach ($bankAccounts as $bankAccount)
                                                        <option value="{{ $bankAccount->id }}"
                                                            {{ isset($invoiceInfo) ? ($invoiceInfo->bank_id == $bankAccount->id ? 'selected' : '') : (old('bank') == $bankAccount->id ? 'selected' : '') }}>
                                                            {{ $bankAccount->bank_name }}
                                                            ({{ $bankAccount->account_number }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('bank'))
                                                    <span class="text-danger invalid-select" role="alert">
                                                        {{ $errors->first('bank') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        @php
                                            $tooltip = '';

                                        @endphp
                                        <input type="hidden" value="{{ @$invoiceInfo->id }}" id="newFeesEditId">

                                        <div class="row mt-40">
                                            <div class="col-lg-12 text-center">
                                                <button type="submit" class="primary-btn fix-gr-bg submit fmInvoice"
                                                    data-tooltip="tooltip" title="{{ $tooltip }}">
                                                    <span class="ti-check"></span>
                                                    @if (isset($invoiceInfo))
                                                        @lang('common.update')
                                                    @else
                                                        @lang('common.save')
                                                    @endif
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-9">
                        <div class="white-box">
                            <div class="row">
                                <div class="col-lg-4 no-gutters">
                                    <div class="main-title">
                                        <h3 class="mb-15">@lang('fees.fees_type_list')</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-15">
                                <div class="col-lg-12">
                                    <div class="pb-0 fees_invoice_type_div">
                                        <div class="row">
                                            <div class="col-lg-12">

                                                {{-- Fee Group Select --}}
                                                <select
                                                    class="primary_select form-control {{ $errors->has('fee_group_id') ? 'is-invalid' : '' }}"
                                                    id="fee_group_id" name="fee_group_id">
                                                    <option value="" disabled selected>-- Select Fee Group --
                                                    </option>
                                                    @foreach ($feesgroups as $feesGroup)
                                                        <option value="{{ $feesGroup->id }}"
                                                            value="{{ $feesGroup->id }}" disabled>
                                                            {{ $feesGroup->name }}
                                                        </option>

                                                        {{-- Fee Types as sub-options --}}
                                                        @foreach ($feesGroup->feeType as $feeType)
                                                            <option value="{{ $feeType->id }}">
                                                                &nbsp;&nbsp;&nbsp;{{ $feeType->name }}
                                                            </option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('fee_group_id'))
                                                    <span class="text-danger invalid-select">
                                                        {{ $errors->first('fee_group_id') }}
                                                    </span>
                                                @endif

                                                {{-- Fee Type Select (dynamic via AJAX if needed) --}}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                         
                            <div class="row mt-20">
                                <div class="col-lg-12 justify-content-end d-flex">
                                    <div class="text-right">
                                        <input type="checkbox" name="singleInvoice" id="singleInvoice"
                                            class="common-checkbox form-control" value="1">
                                        <label for="singleInvoice">@lang('fees::feesModule.group_fees_generate_seperate_invoice')</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-20">
                                <div class="col-lg-12 justify-content-end d-flex">
                                    <div class="text-right">
                                        <input type="checkbox" id="cloneAmount"
                                            class="common-checkbox form-control permission-checkAll">
                                        <label for="cloneAmount">@lang('fees::feesModule.clone_amount')</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" class="weaverType" value="amount">

                         </form>
                        <div class="big-table">
                            <table class="table school-table-style fees_invoice_type_table" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('common.sl')</th>
                                        <th>@lang('student.name')</th>
                                        <th>@lang('fees.fees_type')</th>
                                        <th>@lang('accounts.amount')</th>
                                        <th>@lang('create date')</th>
                                        <th>@lang('Due date')</th>

 
                                        @if (!isset($invoiceInfo))
                                            <th>@lang('fees.paid_amount')</th>
                                        @endif
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody class="allFeesTypes">
                                    @if (isset($invoiceInfo))
                                        @foreach ($invoiceDetails as $key => $invoiceDetail)
                                            <tr>
                                                <td></td>
                                                <td>{{ $invoiceDetail->feesType->name }}</td>
                                                <input type="hidden" name="feesType[]"
                                                    value="{{ $invoiceDetail->fees_type }}">
                                                <td>
                                                    <div class="primary_input">
                                                        <input
                                                            class="primary_input_field form-control amount{{ $errors->has('amount') ? ' is-invalid' : '' }}"
                                                            type="number" name="amount[]" autocomplete="off"
                                                            value="{{ isset($invoiceDetail) ? $invoiceDetail->amount : old('amount') }}">

                                                        @if ($errors->has('amount'))
                                                            <span class="text-danger">
                                                                {{ $errors->first('amount') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="primary_input">
                                                        <input
                                                            class="primary_input_field form-control weaver{{ $errors->has('weaver') ? ' is-invalid' : '' }}"
                                                            type="number" name="weaver[]" autocomplete="off"
                                                            value="{{ isset($invoiceDetail) ? $invoiceDetail->weaver : old('weaver') }}">

                                                        @if ($errors->has('weaver'))
                                                            <span class="text-danger">
                                                                {{ $errors->first('weaver') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="subTotal">
                                                    {{ isset($invoiceDetail) ? $invoiceDetail->sub_total : '' }}
                                                </td>
                                                <input type="hidden" name="sub_total[]" class="inputSubTotal"
                                                    value="{{ isset($invoiceDetail) ? $invoiceDetail->sub_total : '' }}">
                                                @if (!isset($invoiceDetail))
                                                    <td>
                                                        <input
                                                            class="primary_input_field form-control paidAmount{{ $errors->has('paid_amount') ? ' is-invalid' : '' }}"
                                                            type="number" name="paid_amount[]" autocomplete="off"
                                                            disabled
                                                            value="{{ isset($invoiceDetail) ? $invoiceDetail->paid_amount : old('paid_amount') }}">
                                                    </td>
                                                @endif
                                                <td>
                                                    <button class="primary-btn icon-only fix-gr-bg" data-toggle="modal"
                                                        data-tooltip="tooltip"
                                                        data-target="#addNotesModal{{ $invoiceDetail->fees_type }}"
                                                        type="button" title="@lang('common.edit_note')">
                                                        <span class="ti-pencil-alt"></span>
                                                    </button>
                                                    <button class="primary-btn icon-only fix-gr-bg" type="button"
                                                        data-tooltip="tooltip" title="@lang('common.delete')"
                                                        id="deleteField">
                                                        <span class="ti-trash"></span>
                                                    </button>
                                                    {{-- Notes Modal Start --}}
                                                    <div class="modal fade admin-query"
                                                        id="addNotesModal{{ $invoiceDetail->fees_type }}">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">@lang('common.edit_note')
                                                                    </h4>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <div class="primary_input">
                                                                        <input
                                                                            class="primary_input_field form-control has-content"
                                                                            type="text" name="note[]"
                                                                            autocomplete="off"
                                                                            value="{{ isset($invoiceDetail) ? $invoiceDetail->note : '' }}">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('common.note')</label>

                                                                    </div>
                                                                    </br>
                                                                    <div class="mt-40 d-flex justify-content-between">
                                                                        <button type="button" class="primary-btn tr-bg"
                                                                            data-dismiss="modal">@lang('common.cancel')</button>
                                                                        <button type="button"
                                                                            class="primary-btn fix-gr-bg"
                                                                            data-dismiss="modal">@lang('common.update')</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- Notes Modal End --}}
                                                    <input type="hidden" class="fees"
                                                        value="grp{{ $invoiceDetail->feesType->fees_group_id }}">
                                                    <input type="hidden" class="fees"
                                                        value="typ{{ $invoiceDetail->fees_type }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>@lang('exam.result')</td>
                                        <td></td>
                                        <td class="showTotalAmount">0.00</td>
                                        
                                        @if (!isset($invoiceInfo))
                                            <td class="showPaidAmount">0.00</td>
                                        @endif
                                        <td></td>
                                        <input class="totalPaidAmount" type="hidden" name="total_paid_amount">
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
        </div>
        </div>
        </form>
        </div>
    </section>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {



                $('#branch_id').change(function() {
                    let branchId = $(this).val();
                    console.log('Selected branchId:', branchId);

                    $('#selectStudentLoader').show();
                    $('#student_id').html('<option value="">Loading...</option>');

                    if (branchId) {
                        $.ajax({
                            url: "{{ url('student/invoice/get-student') }}/" + branchId,
                            type: 'GET',
                            success: function(data) {
                                console.log('AJAX response:', data);

                                $('#selectStudentLoader').hide();
                                $('#student_id').empty().append(
                                    '<option value="">Select Student</option>');

                                $.each(data, function(index, student) {
                                    $('#student_id').append('<option value="' + student.id +
                                        '">' +
                                        student.name + '</option>');
                                });

                                $('#student_id').niceSelect('update');
                            },
                            error: function(xhr, status, error) {
                                $('#selectStudentLoader').hide();
                                console.error('AJAX Error:', status, error, xhr.responseText);
                                alert('Failed to load students.');
                            }
                        });
                    } else {
                        $('#selectStudentLoader').hide();
                        $('#student_id').html('<option value="">Select Student</option>');
                    }
                });
                });


                //    $('#fee_group_id').on('change', function(){


                //         let groupId = $(this).val();
                //         $('#fee_type_id').html('<option value="">-- Select Fee Type --</option>'); // reset

                //         if(groupId){
                //              console.log('Selected groupId:', groupId);

                //             $.ajax({
                //                 url: "{{ url('student/invoice/types_get_by_group') }}/" + groupId,
                //                 type: "GET",
                //                 success: function(data){
                //                     console.log('fee types:', data);
                //                     if(data.length > 0){
                //                         $.each(data, function(key, feeTypes){
                //                             $('#fee_type_id').append('<option value="'+ feeTypes.id +'">'+ feeTypes.name +'</option>');
                //                         });
                //                         $('#fee_type_id').niceSelect('update');
                //                     } else {
                //                         $('#fee_type_id').append('<option value="">No Fee Types found</option>');
                //                     }
                //                 }
                //             });
                //         }
                //     });

                // $('#fee_group_id').on('change', function() {
                //     let groupId = $(this).val();
                //     $('#fee_type_id').html('<option value="">-- Select Fee Type --</option>'); // reset

                //     if (groupId) {
                //         console.log('Selected groupId:', groupId);

                //         // Check if fee_type_id is currently open
                //         let isOpen = $('#fee_type_id').parent().hasClass('open');

                //         $.ajax({
                //             url: "{{ url('student/invoice/types_get_by_group') }}/" + groupId,
                //             type: "GET",
                //             success: function(data) {
                //                 console.log('fee types:', data);

                //                 if (data.length > 0) {
                //                     $.each(data, function(key, feeTypes) {
                //                         $('#fee_type_id').append('<option value="' + feeTypes.id + '">' + feeTypes.name + '</option>');
                //                     });
                //                 } else {
                //                     $('#fee_type_id').append('<option value="">No Fee Types found</option>');
                //                 }

                //                 // Update niceSelect
                //                 $('#fee_type_id').niceSelect('update');

                //                 // Reopen dropdown if it was open
                //                 if (isOpen) {
                //                     $('#fee_type_id').parent().addClass('open');
                //                 }
                //             }
                //         });
                //     }
                // });


                // });

let rowCount = 0;

    // === Add Row to Table ===
    $('#fee_group_id').on('change', function () {
        let studentName = $('#student_id option:selected').text();
        let feeType = $('#fee_group_id option:selected').text();
        let amount = 0; // default amount
        let createDate = $('#create_date').val();
        let dueDate = $('#due_date').val();

        if (!studentName || !feeType) {
            alert("Please select student and fee type.");
            return;
        }

        rowCount++;

        let newRow = `
            <tr>
                <td>${rowCount}</td>
                <td>${studentName}</td>
                <td>${feeType}</td>
                <td>${amount}</td>
                <td>${createDate}</td>
                <td>${dueDate}</td>
                <td>
                    <button type="button" class="primary-btn icon-only fix-gr-bg deleteRow">
                        <span class="ti-trash"></span>
                    </button>
                </td>
            </tr>
        `;

        $('.allFeesTypes').append(newRow);

        // Reset fee_group select
        $('#fee_group_id').val("").niceSelect('update');
    });

    // === Delete Row from Table ===
    $(document).on('click', '.deleteRow', function () {
        $(this).closest('tr').remove();
        updateSL();
    });

    // === Update SL Numbers ===
    function updateSL() {
        rowCount = 0;
        $('.allFeesTypes tr').each(function () {
            rowCount++;
            $(this).find('td:first').text(rowCount);
        });
    }
                
</script>


@include('components.partials.date_picker_css_js')
