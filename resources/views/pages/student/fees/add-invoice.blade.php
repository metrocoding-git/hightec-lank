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
                                                    name="payment_status" id="status">
                                                    <option data-display="@lang('fees.payment_status') *" value="">
                                                        @lang('fees.payment_status') *</option>
                                                    <option value="not" data-status="not"
                                                        {{ isset($invoiceInfo) ? ($invoiceInfo->payment_status == 'not' ? 'selected' : '') : (old('payment_status') == 'not' ? 'selected' : '') }}>
                                                        @lang('fees.not_paid')</option>
                                                    <option value="partial" data-status="partial"
                                                        {{ isset($invoiceInfo) ? ($invoiceInfo->payment_status == 'partial' ? 'selected' : '') : (old('payment_status') == 'partial' ? 'selected' : '') }}>
                                                        @lang('fees.partial_paid')</option>
                                                    <option value="full" data-status="full"
                                                        {{ isset($invoiceInfo) ? ($invoiceInfo->payment_status == 'full' ? 'selected' : '') : (old('payment_status') == 'full' ? 'selected' : '') }}>
                                                        @lang('fees.full_paid')</option>
                                                </select>
                                                @if ($errors->has('payment_status'))
                                                    <span class="text-danger invalid-select" role="alert">
                                                        {{ $errors->first('payment_status') }}</span>
                                                @endif
                                            </div>
                                        </div>


                                        <div class="row mt-15" id="priceSection">
                                            <div class="col-lg-12" id="priceWrapper">
                                                <label class="primary_input_label" for="payment_price">
                                                    {{ __('fees.payment_amount') }}
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <input
                                                    class="primary_input_field form-control{{ $errors->has('price') ? ' is-invalid' : '' }}"
                                                    type="number" name="price" id="payment_price"
                                                    value="{{ old('price', isset($invoiceInfo) ? $invoiceInfo->price : '') }}"
                                                    autocomplete="off">
                                            </div>
                                        </div>


                                        <div class="row mt-15 " id="paymentMethod">
                                            <div class="col-lg-12">
                                                <label class="primary_input_label" for="">
                                                    {{ __('fees.payment_method') }}
                                                    <span class="text-danger"> *</span>
                                                </label>

                                                <?php
                                                $paymentMethods = collect([(object) ['method' => 'Cash'], (object) ['method' => 'Card'], (object) ['method' => 'Bank Transfer']]);
                                                
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

                                            <div class="col-lg-12">
                                                <label class="primary_input_label" for="">

                                                </label>
                                                <div class="row mt-15" id="fileUploadSection">
                                                    <div class="col-lg-12">
                                                        <label class="primary_input_label" for="payment_file">
                                                            {{ __('fees.upload_document') }}
                                                            <span class="text-danger">*</span>
                                                        </label>

                                                        <div class="d-flex align-items-center">
                                                            <div class="primary_input">
                                                                <!-- Hidden file input -->
                                                                <input type="file" name="payment_file"
                                                                    id="payment_file" class="d-none"
                                                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">

                                                                <!-- Custom button -->
                                                                <button type="button" id="uploadBtn"
                                                                    class="primary-btn small fix-gr-bg">
                                                                    <span class="ti-upload"></span>
                                                                    {{ __('common.browse') }}
                                                                </button>
                                                            </div>

                                                            <!-- Show selected file name -->
                                                            <span id="fileName" class="ml-3 text-muted"></span>
                                                        </div>
                                                        @if ($errors->has('payment_file'))
                                                            <span class="text-danger" role="alert">
                                                                {{ $errors->first('payment_file') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>









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
                                                <a href="" type="submit"
                                                    class="primary-btn fix-gr-bg submit fmInvoice" data-tooltip="tooltip"
                                                    title="{{ $tooltip }}">
                                                    <span class="ti-check"></span>
                                                    @if (isset($invoiceInfo))
                                                        @lang('common.update')
                                                    @else
                                                        @lang('common.save')
                                                    @endif
                                                </a>
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
                                                    <option value="" selected>-- Select Fee Group --
                                                    </option>
                                                    @foreach ($feesgroups as $feesGroup)
                                                        <option value="{{ $feesGroup->id }}" disabled>
                                                            {{ $feesGroup->name }}
                                                        </option>

                                                        {{-- Fee Types as sub-options --}}
                                                        @foreach ($feesGroup->feeType as $feeType)
                                                            <option value="{{ $feeType->id }}"
                                                                data-price="{{ $feeType->price }}">
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
                                            <th> @lang ('status')</th>
                                            <th>@lang('fees.payment')</th>
                                            <th>@lang('Due date')</th>
                                            <th>@lang('payment price')</th>


                                            {{-- @if (!isset($invoiceInfo))
                                <th>@lang('fees.paid_amount')</th>
                            @endif --}}
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody class="allFeesTypes">
                                        <td id="sl"> </td>
                                        <td id="tdName"> </td>
                                        <td id="fees_type"> </td>
                                        <td id="amount_cell">
                                            <span id="amount_text"></span>
                                            <input class="primary_input_field form-control" type="number" name="price"
                                                autocomplete="off" value="" id="input_price"
                                                style="display:none;">



            </form>
            </td>





            <td id="tdcreate_date">
                {{ isset($invoiceInfo) ? date('m/d/Y', strtotime($invoiceInfo->create_date)) : date('m/d/Y') }}
            </td>
            </td>

            <td id="tdstatus"> </td>
            <td id="tdpayment_method"> </td>
            <td id="tddue_date">
                {{ isset($invoiceInfo) ? date('m/d/Y', strtotime($invoiceInfo->create_date)) : date('m/d/Y') }}
            </td>



            <td id="payment_cell">
                 <span id="amount_text"></span>
                <span id="tdpayment_price"></span>





            </td>


            <td>

                <button id="clear_btn" type="button"
                    class="primary-btn small bg-danger text-white border-0 ">@lang('common.delete')</button>




            </td>



            {{-- @if (!isset($invoiceInfo))
                            <td> </td>
                        @endif --}}

            </tbody>

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
</script>
<script>
    // real time forme data set to table js code
    $(document).ready(function() {

        // Student name update

$(document).ready(function () {
    // Function to toggle label + input visibility
    function togglePriceSection(status) {
        if (status === 'partial') {
            $('#priceWrapper').removeClass('d-none'); // show input
        } else if (status === 'full') {
            $('#priceWrapper').addClass('d-none'); // hide input

            // get price from hidden span OR directly from select box
            let price = $('#amount_text').text().trim(); 

            if (price) {
                $('#tdpayment_price').text(price); // ✅ set fee price
            } else {
                $('#tdpayment_price').text('0');
            }
        } else {
            $('#priceWrapper').addClass('d-none'); // hide input
            $('#tdpayment_price').text('no payment');
        }
    }

    // Run once on page load (important if editing)
    togglePriceSection($('#status').find("option:selected").data('status'));

    // On status change
    $('#status').on('change', function () {
        let selectedOption = $(this).find("option:selected");
        let selectedStatus = selectedOption.data('status');
        let selectedText = selectedOption.text().trim();

        $('#tdstatus').text(selectedText);
        togglePriceSection(selectedStatus);
    });

    // On fee group change (to update amount_text + maybe re-run toggle)
    $('#fee_group_id').on('change', function () {
        let selectedOption = $(this).find("option:selected");
        let selectedText = selectedOption.text().trim();
        let amount = selectedOption.data('price');

        $('#fees_type').text(selectedText);

        if (amount !== undefined && amount !== null && amount !== '') {
            $('#amount_text').text(amount).show();
            $('#input_price').hide();
        } else {
            $('#amount_text').hide();
            $('#input_price').val('').show();
        }

        // re-apply price section logic (in case status=full)
        let currentStatus = $('#status').find("option:selected").data('status');
        togglePriceSection(currentStatus);
    });
});









        // Amount update


        // Create date update
        $('#create_date').on('change input', function() {
            $('#tdcreate_date').text($(this).val());
        });

        // Due date update
        $('#due_date').on('change input', function() {
            $('#tddue_date').text($(this).val());
        });




         $('#payment_price').on('change input', function() {
            $('#tdpayment_price').text($(this).val());
        });

        // Status update




        //payemt set tabel
        $('#paymentMethodName').on('change', function() {
            let selectedText = $(this).find("option:selected").text();
            $('#tdpayment_method').text(selectedText);
        });

        // Auto-generate SL (serial number)
        $('#sl').text("1");



        //button to cler table and input feelds
        $('#clear_btn').on('click', function() {

            const today = new Date();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const year = today.getFullYear();

            // Reset the select box
            $('#fee_group_id').val('').trigger('change');
            $('#student_id').val(''); // set value to empty, which matches the default option
            $('#student_id').niceSelect('update');
            $('#branch_id').val('');
            $('#branch_id').niceSelect('update');


            $('#fee_group_id').val('');
            $('#fee_group_id').niceSelect('update');
            $('#create_date').val(`${month}/${day}/${year}`).trigger('change');
            $('#due_date').val(`${month}/${day}/${year}`).trigger('change');


            $('#status').val('');
            $('#status').niceSelect('update');

            $('#paymentMethodName').val("");
            $('#paymentMethodName').niceSelect('update');

            $('#priceWrapper').addClass('d-none');

            $('#payment_price').val('');




            // Clear table and input fields
            $('#fees_type').text('');
            $('#amount_text').text('');
            $('#input_price').hide();
            $('#tdpayment_price').text('');
            $('#tdName').text('');
            $('#tdpayment_method').text('');
            $('#tdcreate_date').text(
                ' {{ isset($invoiceInfo) ? date('m/d/Y', strtotime($invoiceInfo->create_date)) : date('m/d/Y') }}'
            );
            $('#tddue_date').text(
                ' {{ isset($invoiceInfo) ? date('m/d/Y', strtotime($invoiceInfo->create_date)) : date('m/d/Y') }}'
            );
            $('#tdstatus').text('');


        });





    });
    $(document).ready(function() {
        // Show payment method section on page load
        $('#paymentMethod').removeClass('d-none');

        // Trigger once on page load (important if editing an invoice)
        toggleBankPayment($('#paymentMethodName').val());

        // When payment method changes
        $('#paymentMethodName').on('change', function() {
            let selectedMethod = $(this).val();
            console.log('Selected payment method:', selectedMethod);
            toggleBankPayment(selectedMethod);
        });

        function toggleBankPayment(method) {
            if (method === 'Bank Transfer' || method === 'Card') {
                $('#bankPayment').removeClass('d-none'); // show bank selection
            } else {
                $('#bankPayment').addClass('d-none'); // hide it
            }
        }
    });






    //file fetch script
    $(document).ready(function() {
        // When clicking the custom button, trigger hidden input
        $('#uploadBtn').on('click', function() {
            $('#payment_file').click();
        });

        // Show file name after selecting
        $('#payment_file').on('change', function() {
            let fileName = this.files[0] ? this.files[0].name : '';
            $('#fileName').text(fileName);
            console.log('Selected file:', fileName);
        });
    });
</script>


@include('components.partials.date_picker_css_js')
