@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    @push('css')
        <style type="text/css">
            #productTable tbody tr {
                border-bottom: 1px solid #FFFFFF !important;
            }

            #selectStaffsDiv {
                display: none;
            }

            .available_quantity input {
                width: 100px !important;
                text-align: center;
            }
        </style>
    @endpush
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('inventory.item_sell')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="{{ route('item-sell') }}">@lang('inventory.item_sell')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">

            <form class="form-horizontal" id="item-sell-form" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="white-box">
                                    <div class="add-visitor">
                                        <div class="row">

                                            <div class="col-lg-12 mb-15">
                                                <label class="primary_input_label" for="">@lang('common.branch') <span
                                                        class="text-danger"> *</span> </label>
                                                <select class="primary_select  form-control" name="branch_id"
                                                    id="branch_id">
                                                    @if (count($branches) > 1)
                                                        <option data-display="Select Branch*" value="">
                                                            Select Branch*</option>
                                                    @endif
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}"
                                                            {{ count($branches) == 1 ? 'selected' : '' }}>
                                                            {{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="text-danger" id="branchError"></div>
                                            </div>



                                            <div class="col-lg-12 mb-15">
                                                <label class="primary_input_label" for="">@lang('inventory.sell_to') <span
                                                        class="text-danger"> *</span> </label>
                                                <select
                                                    class="primary_select  form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}"
                                                    name="role_id" id="buyer_type">
                                                    <option data-display="@lang('inventory.sell_to') *" value="">
                                                        @lang('inventory.sell_to') *</option>
                                                    <option value="2">Student</option>
                                                    <option value="0">Other</option>
                                                </select>
                                                <div class="text-danger" id="buyerError"></div>
                                                @if ($errors->has('role_id'))
                                                    <span class="text-danger invalid-select" role="alert">
                                                        {{ $errors->first('role_id') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="col-lg-12" id="student_section" style="display: none;">
                                                <div class="row">
                                                    <div class="col-lg-12 mb-15">
                                                        <label class="primary_input_label" for="">@lang('common.student')
                                                            <span class="text-danger"> *</span> </label>
                                                        <select
                                                            class="primary_select form-control{{ $errors->has('student') ? ' is-invalid' : '' }}"
                                                            id="select_student" name="student">
                                                            <option data-display="@lang('common.select_student')" value="">
                                                                @lang('common.select_student')</option>
                                                            @foreach ($students as $value)
                                                                <option value="{{ $value->id }}"
                                                                    {{ old('student') == $value->id ? 'selected' : '' }}>
                                                                    {{ $value->admission_number }} -
                                                                    {{ $value->student_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="text-danger" id="studentError"></div>
                                                        @if ($errors->has('student'))
                                                            <span class="text-danger invalid-select" role="alert">
                                                                {{ $errors->first('student') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12" id="other_section" style="display: none;">
                                                <div class="row">
                                                    <div class="col-lg-12 mb-15">
                                                        <label class="primary_input_label" for="">@lang('common.name')
                                                            <span class="text-danger"> *</span> </label>
                                                        <input
                                                            class="primary_input_field form-control{{ $errors->has('customer_name') ? ' is-invalid' : '' }}"
                                                            type="text" name="customer_name" id="customer_name"
                                                            autocomplete="off" value="{{ old('customer_name') }}"
                                                            placeholder="Enter customer name">
                                                        <div class="text-danger" id="customerNameError"></div>
                                                        @if ($errors->has('customer_name'))
                                                            <span class="text-danger invalid-select" role="alert">
                                                                {{ $errors->first('customer_name') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12 mb-15">
                                                <div class="primary_input">
                                                    <label class="primary_input_label"
                                                        for="">@lang('inventory.reference_no')</label>
                                                    <input class="primary_input_field form-control" type="text"
                                                        name="reference_no" autocomplete="off"
                                                        value="{{ isset($editData) ? $editData->reference_no : '' }}">



                                                </div>
                                            </div>


                                            <div class="col-lg-12 mb-15">
                                                <div class="primary_input ">
                                                    <label class="primary_input_label" for="">@lang('inventory.sell_date')
                                                        <span></span> </label>
                                                    <div class="primary_datepicker_input">
                                                        <div class="no-gutters input-right-icon">
                                                            <div class="col">
                                                                <div class="">

                                                                    <input
                                                                        class="primary_input_field  primary_input_field date form-control form-control{{ $errors->has('sell_date') ? ' is-invalid' : '' }}"
                                                                        id="sell_date" type="date" name="sell_date"
                                                                        value="{{ old('sell_date', isset($editData) ? $editData->sell_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                                                        autocomplete="off" readonly>
                                                                </div>
                                                            </div>
                                                            {{-- <button class="btn-date" data-id="#sell_date" type="button">
                                                                <label for="sell_date" class="m-0 p-0">
                                                                    <i class="ti-calendar" id="start-date-icon"></i>
                                                                </label>
                                                            </button> --}}
                                                        </div>
                                                    </div>
                                                    <span class="text-danger">{{ $errors->first('sell_date') }}</span>
                                                </div>
                                            </div>

                                            <div class="col-lg-12 mb-15">
                                                <label class="primary_input_label" for="">@lang('inventory.payment_method') <span
                                                        class="text-danger"> *</span> </label>
                                                <select class="primary_select  form-control" name="payment_method"
                                                    id="item_sell_payment_method_id">
                                                    <option data-display="@lang('inventory.payment_method')*" value="">
                                                        @lang('inventory.payment_method')*</option>
                                                    <option value="Cash">Cash</option>

                                                    <option value="Invoice">Invoice</option>
                                                    <option value="Bank Transfer">Bank Transfer</option>
                                                </select>
                                                <div class="text-danger" id="paymentError"></div>
                                            </div>

                                            {{-- Invoice Number Field --}}
                                            {{-- <div class="col-lg-12 mb-15 d-none" id="invoiceNumberField">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="invoice_number">Invoice Number
                                                        <span class="text-danger"> *</span> </label>
                                                    <input class="primary_input_field form-control" type="text"
                                                        name="invoice_number" id="invoice_number" autocomplete="off"
                                                        value="{{ isset($editData) ? $editData->invoice_number : '' }}">
                                                    @if ($errors->has('invoice_number'))
                                                        <span class="text-danger">
                                                            {{ $errors->first('invoice_number') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div> --}}

                                            {{-- Bank Transfer Fields --}}
                                            <div class="col-lg-12 mb-15 d-none" id="bankPaymentAmountField">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="bank_payment_amount">Bank
                                                        Payment Amount
                                                        <span class="text-danger"> *</span> </label>
                                                    <input class="primary_input_field form-control" type="number"
                                                        step="0.01" name="bank_payment_amount"
                                                        id="bank_payment_amount" autocomplete="off"
                                                        value="{{ isset($editData) ? $editData->bank_payment_amount : '' }}">
                                                    @if ($errors->has('bank_payment_amount'))
                                                        <span class="text-danger">
                                                            {{ $errors->first('bank_payment_amount') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-12 mb-15 d-none" id="bankPaymentDateField">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="bank_payment_date">Bank
                                                        Payment
                                                        Date
                                                        <span class="text-danger"> *</span> </label>
                                                    <div class="primary_datepicker_input">
                                                        <div class="no-gutters input-right-icon">
                                                            <div class="col">
                                                                <div class="">
                                                                    <input
                                                                        class="primary_input_field primary_input_field date form-control"
                                                                        id="bank_payment_date" type="date"
                                                                        name="bank_payment_date"
                                                                        value="{{ isset($editData) ? date('m/d/Y', strtotime($editData->bank_payment_date)) : date('m/d/Y') }}"
                                                                        autocomplete="off">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="text-danger">{{ $errors->first('bank_payment_date') }}</span>
                                                </div>
                                            </div>

                                            <div class="col-lg-12 mb-15 d-none" id="bankPaymentDocumentField">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="bank_payment_document">Bank
                                                        Payment Document
                                                        <span></span> </label>
                                                    <input class="primary_input_field form-control" type="file"
                                                        name="bank_payment_document" id="bank_payment_document">
                                                    @if ($errors->has('bank_payment_document'))
                                                        <span class="text-danger">
                                                            {{ $errors->first('bank_payment_document') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- <div class="col-lg-12 mb-30 d-none" id="add_item_sell_bankAccount">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="">hh
                                                        <span></span> </label>
                                                    <select
                                                        class="primary_select  form-control{{ $errors->has('bank_id') ? ' is-invalid' : '' }}"
                                                        name="bank_id" id="account_id">
                                                        @if (isset($account_id))
                                                            @foreach ($account_id as $key => $value)
                                                                <option value="{{ $value->id }}">
                                                                    {{ $value->account_name }}
                                                                    ({{ $value->bank_name }})
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <div class="text-danger" id="accountError"></div>

                                                    @if ($errors->has('bank_id'))
                                                        <span class="text-danger invalid-select" role="alert">
                                                            <strong>{{ $errors->first('bank_id') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div> --}}

                                            <div class="col-lg-12 mb-20">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="">@lang('common.description')
                                                        <span></span> </label>
                                                    <textarea class="primary_input_field form-control" cols="0" rows="4" name="description"
                                                        id="description">{{ isset($editData) ? $editData->description : '' }}</textarea>



                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-9">
                        <div class="white-box">
                            <div class="row xm_3 aling-tiems-center">
                                <div class="col-lg-6 no-gutters col-6 xs_mt_0">
                                    <div class="main-title">
                                        <h3 class="mb-15">Item Sell</h3>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-6 text-right col-md-6 col-5">
                                    <button type="button" class="primary-btn small fix-gr-bg" id="addRowBtn">
                                        <span class="ti-plus pr-2"></span>
                                        @lang('common.add')
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="white-box">
                                        {{-- <div class="alert alert-danger" id="errorMessage2">
                                            <div id="itemError"></div>
                                            <div id="priceError"></div>
                                            <div id="quantityError"></div>
                                        </div> --}}
                                        <div class="table-responsive">
                                            <table class="table table-bordered school-table-style" id="productTable"
                                                style="width:100%; table-layout:fixed;">
                                                <thead>
                                                    <tr>
                                                        <th style="width:35%;">@lang('inventory.product_name')*</th>
                                                        <th style="width:15%;">@lang('inventory.sell_price')*</th>
                                                        <th style="width:10%;">Available Qty.</th>
                                                        <th style="width:10%;">@lang('inventory.quantity')*</th>
                                                        <th style="width:15%;">@lang('inventory.sub_total')</th>
                                                        <th style="width:8%;" class="text-center">@lang('common.action')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                                <tfoot>
                                                    <tr class="align-middle text-center">
                                                        <th class="border-top-0 text-center align-middle" colspan="3"
                                                            style="vertical-align: middle; text-align: center;">
                                                            @lang('inventory.total')
                                                        </th>
                                                        <th class="border-top-0">
                                                            <input type="text" class="primary_input_field form-control"
                                                                readonly="" id="subTotalQuantity"
                                                                name="subTotalQuantity" placeholder="0.00" />

                                                            <input type="hidden" class="form-control"
                                                                id="subTotalQuantityValue" name="subTotalQuantityValue" />

                                                        </th>

                                                        <th class="border-top-0">
                                                            <input type="number"
                                                                class="primary_input_field form-control{{ $errors->has('subTotal') ? ' is-invalid' : '' }}"
                                                                id="subTotal" name="subTotal" placeholder="0.00"
                                                                readonly="" />

                                                            <input type="hidden" class="form-control" id="subTotalValue"
                                                                name="subTotalValue" />
                                                            @if ($errors->has('subTotal'))
                                                                <span class="text-danger">
                                                                    {{ $errors->first('subTotal') }}
                                                                </span>
                                                            @endif
                                                        </th>
                                                        <th class="border-top-0"></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-lg-12">

                                    <div class="white-box">
                                        <div class="d-none">
                                            <input type="checkbox" id="full_paid"
                                                class="common-checkbox form-control{{ $errors->has('full_paid') ? ' is-invalid' : '' }}"
                                                name="full_paid" value="1" checked>
                                            <label for="full_paid">@lang('inventory.full_paid')</label>


                                            <div class="primary_input md_mb_20">
                                                <label class="primary_input_label"
                                                    for="">@lang('inventory.total_paid')</label>
                                                <input class="primary_input_field" type="number" step="0.1"
                                                    value="0" name="totalPaid" id="totalPaid"
                                                    onkeyup="paidAmount();">
                                                <input type="hidden" id="totalPaidValue" name="totalPaidValue">


                                            </div>


                                            <div class="primary_input md_mb_10">
                                                <label class="primary_input_label"
                                                    for="">@lang('inventory.total_due')</label>
                                                <input class="primary_input_field" type="text" name="totalDue"
                                                    value="0.00" id="totalDue" readonly>
                                                <input type="hidden" id="totalDueValue" name="totalDueValue">


                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-12  text-center">
                                                <button class="primary-btn fix-gr-bg w-100" data-toggle="tooltip">
                                                    <span class="ti-check"></span>
                                                    @lang('inventory.sell')
                                                </button>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            if ({{ count($branches) }} == 1) {
                $('#branch_id').trigger('change');
            }

            $('#branch_id').on('change', function() {

                let branchId = $(this).val();

                $('tbody').empty();
                rowCount = 0;

                // reset totals
                $('#subTotalQuantity').val(0);
                $('#subTotal').val(0);
                $('#totalPaid').val(0);
                $('#totalDue').val(0);

                if (!branchId) return;

                addEmptyRow();

            });

            // Configure toastr options
            toastr.options = {
                "timeOut": "5000",
                "extendedTimeOut": "1000"
            };
            let rowCount = 0;

            function addEmptyRow() {
                rowCount++;

                let newRow = `
                    <tr id="row${rowCount}">
                        <td class="border-top-0">
                            <div class="primary_input">
                                <select class="primary_select form-control" name="item_id[]" id="productName${rowCount}" onchange="fetchPrice(this, ${rowCount})">
                                    <option value="">Select*</option>
                                    @foreach ($items as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->item_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>

                        <td>
                            <input class="primary_input_field form-control" type="text"
                                id="unit_price${rowCount}" name="unit_price[]">
                        </td>

                        <td>
                            <input class="primary_input_field form-control" type="text"
                                id="available_quantity${rowCount}" name="available_quantity[]" readonly>
                        </td>

                        <td>
                            <input class="primary_input_field form-control" type="text"
                                id="quantity${rowCount}" name="quantity[]"
                                onkeyup="validateAndGetTotal(${rowCount});">
                        </td>

                        <td>
                            <input class="primary_input_field form-control" type="text"
                                id="total${rowCount}" name="totalValue[]" value="0.00">
                        </td>

                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowCount})">
                                <i class="ti-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('tbody').append(newRow);
            }


            function addRow() {

                rowCount++;

                let newRow = `
                    <tr id="row${rowCount}" class="0">
                        <td class="border-top-0">
                            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                            <div class="primary_input">
                                <select class="primary_select form-control" name="item_id[]" id="productName${rowCount}" onchange="fetchPrice(this, ${rowCount})">
                                    <option value="">@lang('common.select')*</option>
                                    @foreach ($items as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->item_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td class="border-top-0">
                            <div class="primary_input">
                                <input oninput="numberCheckWithDot(this)" class="primary_input_field form-control"
                                    type="text" step="0.1" id="unit_price${rowCount}" name="unit_price[]"
                                    autocomplete="off" value="" onkeyup="getTotalByPrice(${rowCount})">
                            </div>
                        </td>
                        <td class="border-top-0 available_quantity">
                            <div class="primary_input">
                                <input class="primary_input_field form-control" type="text" id="available_quantity${rowCount}"
                                    name="available_quantity[]" autocomplete="off" value="" readonly="readonly">
                            </div>
                        </td>
                        <td class="border-top-0">
                            <div class="primary_input">
                                <input oninput="numberCheckWithDot(this)" class="primary_input_field form-control"
                                    type="text" id="quantity${rowCount}" name="quantity[]" autocomplete="off"
                                    onkeyup="validateAndGetTotal(${rowCount});" value="">
                            </div>
                        </td>
                        <td class="border-top-0">
                            <div class="primary_input">
                                <input oninput="numberCheckWithDot(this)" class="primary_input_field form-control"
                                    type="text" name="total[]" id="total${rowCount}" autocomplete="off" value="0.00">
                            </div>
                            <input type="hidden" name="totalValue[]" id="totalValue${rowCount}" autocomplete="off" class="form-control" />
                        </td>
                
                        <td class="text-center align-middle p-1">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowCount})">
                                <i class="ti-trash"></i>
                            </button>
                        </td>

                    </tr>`;

                $('tbody').append(newRow);
            }

            $('#addRowBtn').on('click', function() {
                addEmptyRow();
            });

            addEmptyRow();
        });

        // Remove row function
        function removeRow(rowNum) {
            $('#row' + rowNum).remove();
            calculateGrandTotal(); // Recalculate totals after removing a row
        }

        // Fetch price for selected item
        function fetchPrice(selectElement, rowNum) {
            const itemId = selectElement.value;
            let branchId = $('#branch_id').val();

            if (!branchId) {
                toastr.error('Please select a branch first', 'Warning');

                $('#branch_id').focus();
                return;
            }

            if (itemId) {
                $.ajax({
                    url: '{{ url('/') }}/account/inventory/get-item-price/' + itemId + '/' + branchId,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#unit_price' + rowNum).val(response.price);
                            $('#available_quantity' + rowNum).val(response.available_quantity);
                            getTotalByPrice(rowNum); // Update total after setting price
                        } else {
                            toastr.error(response.message, 'Error');
                            $('#unit_price' + rowNum).val('');
                            $('#available_quantity' + rowNum).val('');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to fetch item price.', 'Error');
                        $('#unit_price' + rowNum).val('');
                        $('#available_quantity' + rowNum).val('');
                    }
                });
            } else {
                $('#unit_price' + rowNum).val('');
                $('#available_quantity' + rowNum).val('');
                getTotalByPrice(rowNum);
            }
        }



        // Update total for a row
        function getTotalByPrice(row) {
            const price = parseFloat($("#unit_price" + row).val()) || 0;
            const qty = parseFloat($("#quantity" + row).val()) || 0;
            const total = price * qty;

            $("#total" + row).val(total.toFixed(2));
            $("#totalValue" + row).val(total.toFixed(2));

            calculateGrandTotal();
        }

        function getTotal(row) {
            getTotalByPrice(row); // Same calculation works for quantity change
        }

        function validateAndGetTotal(row) {
            const qty = parseFloat($("#quantity" + row).val()) || 0;
            const available = parseFloat($("#available_quantity" + row).val()) || 0;
            if (qty > available) {
                toastr.error('Quantity cannot exceed available quantity (' + available + ')', 'Validation Error');
                $("#quantity" + row).val('');
                return;
            }
            getTotalByPrice(row);
        }

        // Calculate subtotal & grand total
        function calculateGrandTotal() {
            let totalQty = 0,
                totalPrice = 0;

            $("input[name='quantity[]']").each(function() {
                totalQty += parseFloat($(this).val()) || 0;
            });

            $("input[name='total[]']").each(function() {
                totalPrice += parseFloat($(this).val()) || 0;
            });

            $("#subTotalQuantity").val(totalQty);
            $("#subTotalQuantityValue").val(totalQty);
            $("#subTotal").val(totalPrice.toFixed(2));
            $("#subTotalValue").val(totalPrice.toFixed(2));

            updateDue();
        }

        // Paid/Due calculations
        function updateDue() {
            const total = parseFloat($("#subTotal").val()) || 0;
            const paid = parseFloat($("#totalPaid").val()) || 0;
            const due = total - paid;
            $("#totalDue").val(due.toFixed(2));
            $("#totalDueValue").val(due.toFixed(2));
        }

        function paidAmount() {
            updateDue();
        }

        // Full paid checkbox
        $("#full_paid").on("change", function() {
            if ($(this).is(":checked")) {
                $("#totalPaid").val($("#subTotal").val());
            } else {
                $("#totalPaid").val(0);
            }
            updateDue();
        });

        // Handle buyer type selection
        $(document).ready(function() {
            // Handle payment method change using event delegation
            $(document).on('change', '#item_sell_payment_method_id', function() {
                const selectedMethod = $(this).val();

                // Hide all conditional fields first
                $('#invoiceNumberField').addClass('d-none');
                $('#bankPaymentAmountField').addClass('d-none');
                $('#bankPaymentDateField').addClass('d-none');
                $('#bankPaymentDocumentField').addClass('d-none');

                // Clear required attributes when hidden
                $('#invoice_number').removeAttr('required');
                $('#bank_payment_amount').removeAttr('required');
                $('#bank_payment_date').removeAttr('required');

                // Show fields based on selected method
                if (selectedMethod === 'Bank Transfer') {
                    $('#bankPaymentAmountField').removeClass('d-none');
                    $('#bankPaymentDateField').removeClass('d-none');
                    $('#bankPaymentDocumentField').removeClass('d-none');
                    $('#bank_payment_amount').attr('required', 'required');
                    $('#bank_payment_date').attr('required', 'required');
                }
            });

            // Trigger change on load to set initial state
            $('#item_sell_payment_method_id').trigger('change');

            $('#buyer_type').on('change', function() {
                var selectedValue = $(this).val();
                var selectedText = $(this).find('option:selected').text();
                console.log('Buyer type changed - Value:', selectedValue, 'Text:', selectedText);

                // Hide all sections first
                $('#student_section').hide();
                $('#other_section').hide();

                // Clear validation errors and values when sections are hidden
                $('#select_student').val('').trigger('change'); // Clear student selection
                $('#studentError').text(''); // Clear student error
                $('#customer_name').val(''); // Clear customer name
                $('#customerNameError').text(''); // Clear customer name error


                // Show appropriate section based on selection
                if (selectedValue == '2') {
                    $('#student_section').show();
                    console.log('Student section shown');
                } else if (selectedValue == '0') {
                    $('#other_section').show();
                    console.log('Other section shown');
                } else {
                    console.log('No valid selection');
                }
            });

            // Test initial state
            console.log('Initial buyer_type value:', $('#buyer_type').val());
            console.log('Student section display:', $('#student_section').css('display'));
            console.log('Other section display:', $('#other_section').css('display'));
        });

        // Initial row total calculation
        $(document).ready(function() {
            calculateGrandTotal();
        });

        $('#item-sell-form').on('submit', function(e) {
            e.preventDefault();

            // Client-side validation
            let isValid = true;
            $('#studentError').text('');
            $('#customerNameError').text('');

            const buyerType = $('#buyer_type').val();

            if (buyerType === '2') { // Student selected
                if (!$('#select_student').val()) {
                    $('#studentError').text('Student name is required.');
                    isValid = false;
                }
            } else if (buyerType === '0') { // Other selected
                if (!$('#customer_name').val()) {
                    $('#customerNameError').text('Customer name is required.');
                    isValid = false;
                }
            } else { // No buyer type selected
                $('#buyerError').text('Sell to field is required.');
                isValid = false;
            }

            if (!isValid) {
                toastr.error('Please fill in all required fields.', 'Validation Error');
                return;
            }

            let formData = new FormData(this);

            $.ajax({
                url: '{{ route('save-item-sell-data') }}',
                type: 'POST',
                data: formData,
                processData: false, // Important for FormData
                contentType: false, // Important for FormData
                success: function(response) {
                    if (response.success) {
                        // toastr.success(response.message, 'Success');
                        // $('#item-sell-form')[0].reset();
                        // $('tbody').empty(); // Clear table body
                        // addRow(); // Add a fresh row
                        // calculateGrandTotal(); // Reset totals
                        toastr.success(response.message, 'Success');

                        // OPEN PRINT PAGE
                        let printUrl = "/account/inventory/item-sell/bill/" + response.id;
                        window.open(printUrl, '_blank');

                        setTimeout(function() {
                            window.location.href = '{{ route('item-sell-list') }}';
                        }, 2000);
                    } else {
                        toastr.error(response.message || "Something went wrong", 'Error');
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            toastr.error(value[0], 'Validation Error');
                        });
                    } else {
                        toastr.error("An unexpected error occurred.", 'Error');
                    }
                }
            });

        });
    </script>
@endsection
@include('components.partials.date_picker_css_js')
