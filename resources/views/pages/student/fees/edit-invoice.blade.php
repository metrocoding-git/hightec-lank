@extends('layouts.app')
@section('title')
    @lang('fees.edit_invoice')
@endsection
@section('mainContent')
    @push('css')
        <link rel="stylesheet" href="{{ url('Modules/Fees/Resources/assets/css/feesStyle.css') }}" />
        <style>
            .school-table-style tr th,
            .school-table-style tr td,
            .school-table-style tfoot tr td {
                padding: 10px 15px !important;
            }

            /* Disable fee group selection */
            #fee_group_id option[data-type="group"] {
                color: #999 !important;
                font-style: italic;
                pointer-events: none;
                cursor: not-allowed;
            }

            #fee_group_id option[data-type="group"]:hover {
                background-color: transparent !important;
                cursor: not-allowed;
            }

            /* Hide disabled options in niceSelect dropdown */
            .nice-select .list option[data-type="group"] {
                display: none !important;
            }
        </style>
    @endpush

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Edit Invoice</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees')</a>
                    <a href="#">Edit Invoice</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <form id="invoiceForm" enctype="multipart/form-data" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="invoice_id" value="{{ $invoice->id }}">
                <div class="row">
                    <!-- Left Section: Invoice Info -->
                    <div class="col-lg-3">
                        <div class="white-box">
                            <div class="main-title">
                                <h3>Edit Invoice</h3>
                            </div>
                            <div class="add-visitor">
                                <input type="hidden" id="url" value="{{ URL::to('/') }}">

                                {{-- Branch --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('common.branch') <span class="text-danger">*</span></label>
                                        <select class="primary_select form-control" id="branch_id" name="branch_id">
                                            <option value="">@lang('common.select_class')</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ $invoice->branch_id == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="pull-right loader" id="selectStudentLoader" style="display:none;">
                                            <img src="{{ asset('Modules/Fees/Resources/assets/img/pre-loader.gif') }}" style="width:28px;height:28px;">
                                        </div>
                                    </div>
                                </div>

                                {{-- Student --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('common.select_student') <span class="text-danger">*</span></label>
                                        <select class="primary_select form-control" id="student_id" name="student_id">
                                            <option value="{{ $invoice->student_id }}">{{ $invoice->student->student_name }}</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Dates --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('fees.create_date') <span class="text-danger">*</span></label>
                                        <input type="text" class="primary_input_field date form-control" id="create_date" name="create_date" value="{{ date('m/d/Y', strtotime($invoice->create_date)) }}">
                                    </div>
                                </div>
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('fees.due_date') <span class="text-danger">*</span></label>
                                        <input type="text" class="primary_input_field date form-control" id="due_date" name="due_date" value="{{ date('m/d/Y', strtotime($invoice->due_date)) }}">
                                    </div>
                                </div>

                                {{-- Payment Status --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('fees.payment_status') <span class="text-danger">*</span></label>
                                        <select class="primary_select form-control" id="status" name="payment_status">
                                            <option value="not" {{ $invoice->payment_status == 'not' ? 'selected' : '' }}>@lang('fees.not_paid')</option>
                                            <option value="partial" {{ $invoice->payment_status == 'partial' ? 'selected' : '' }}>@lang('fees.partial_paid')</option>
                                            <option value="full" {{ $invoice->payment_status == 'full' ? 'selected' : '' }}>@lang('fees.full_paid')</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Payment Method --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('fees.payment_method') <span class="text-danger">*</span></label>
                                        <select class="primary_select form-control" id="paymentMethodName" name="payment_method">
                                            <option value="Cash" {{ $invoice->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="Card" {{ $invoice->payment_method == 'Card' ? 'selected' : '' }}>Card</option>
                                            <option value="Bank Transfer" {{ $invoice->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Bank Upload --}}
                                <div class="row mt-15 {{ in_array($invoice->payment_method, ['Card','Bank Transfer']) ? '' : 'd-none' }}" id="bankPayment">
                                    <div class="col-lg-12">
                                        <label>Upload Document</label>
                                        <div class="d-flex align-items-center">
                                            <input type="file" name="payment_file" id="payment_file" class="d-none" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                            <button type="button" id="uploadBtn" class="primary-btn fix-gr-bg col-12"><span class="ti-upload"></span> Browse</button>
                                            <span id="fileName" class="ml-3 text-muted">{{ $invoice->payment_file ? basename($invoice->payment_file) : '' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button type="submit" class="primary-btn fix-gr-bg fmInvoice col-12">@lang('common.update')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section: Fee Type List -->
                    <div class="col-lg-9">
                        <div class="white-box">
                            <div class="main-title">
                                <h3>@lang('fees.fees_type_list')</h3>
                            </div>

                            {{-- Fee Group / Fee Type --}}
                            <div class="row mt-15">
                                <div class="col-lg-12">
                                    <select class="primary_select form-control" id="fee_group_id" name="fee_group_id">
                                        <option value="">Select Fee Type</option>
                                        @foreach ($feesgroups as $feesGroup)
                                            <option data-type="group" class="font-weight-bolder" disabled>{{ $feesGroup->name }}</option>
                                            @foreach ($feesGroup->feeType as $feeType)
                                                <option value="{{ $feeType->id }}" data-type="fee" data-price="{{ $feeType->price }}">&nbsp;&nbsp;&nbsp;{{ $feeType->name }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Fees Table --}}
                            <div class="big-table mt-10">
                                <table class="table school-table-style fees_invoice_type_table" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>@lang('common.sl')</th>
                                            <th>@lang('student.name')</th>
                                            <th>@lang('fees.fees_type')</th>
                                            <th>@lang('accounts.amount')</th>
                                            <th>@lang('payment price')</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody class="allFeesTypes">
                                        @foreach($invoice->invoice_item as $item)
                                            <tr data-id="{{ $item->fee_type_id }}">
                                                <td class="sl"></td>
                                                <td class="tdName">{{ $item->fee_type->name ?? 'N/A' }}</td>
                                                <td class="fees_type">{{ $item->fee_type->name ?? 'N/A' }}</td>
                                                <td class="amount_cell">
                                                    <div class="input-group input-group-sm" style="max-width: 120px;">
                                                        <input type="number" class="form-control price_input" name="price[{{ $item->fee_type_id }}]" value="{{ $item->amount }}">
                                                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                                                    </div>
                                                </td>
                                                <td class="payment_cell">
                                                    <div class="input-group input-group-sm" style="max-width: 120px;">
                                                        <input type="number" class="form-control payment_input" name="paidPrice[{{ $item->fee_type_id }}]" value="{{ $item->paid_amount }}">
                                                        <div class="input-group-append"><span class="input-group-text">.00</span></div>
                                                    </div>
                                                </td>
                                                <td><button type="button" class="primary-btn small bg-danger text-white border-0 clear_btn">Delete</button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    @include('components.partials.date_picker_css_js')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let addedFeeTypes = new Set();
    $('.allFeesTypes tr').each(function(){
        addedFeeTypes.add(String($(this).data('id')));
    });

    // Update serial numbers
    function updateSerialNumbers() {
        $('.allFeesTypes tr').each(function(index) {
            $(this).find('.sl').text(index + 1);
        });
    }
    updateSerialNumbers();

    // Delete fee row
    $(document).on('click', '.clear_btn', function() {
        let feeId = String($(this).closest('tr').data('id'));
        addedFeeTypes.delete(feeId);
        $(this).closest('tr').remove();
        updateSerialNumbers();
    });

    // Fee type selection - disable group selection
    $('#fee_group_id').on('change', function() {
        let selectedOption = $(this).find('option:selected');
        if (!selectedOption.val()) return;

        // Check if the selected option is disabled
        if (selectedOption.is(':disabled') || selectedOption.data('type') === 'group') {
            toastr.warning('Please select individual fee types, not fee groups.');
            $(this).val('').niceSelect('update');
            return false;
        }

        let feeId = selectedOption.val();
        if (!addedFeeTypes.has(feeId)) {
            addFeeTypeRow(feeId, selectedOption.text().trim(), selectedOption.data('price'));
            addedFeeTypes.add(feeId);
        }
        $(this).val('').niceSelect('update');
    });

    function addFeeTypeRow(id, name, price) {
        let paymentStatus = $('#status').val() || 'not';
        let paymentVal = paymentStatus === 'full' ? price : 0;
        let readonly = paymentStatus === 'full' ? 'readonly' : '';
        let row = `<tr data-id="${id}">
            <td class="sl"></td>
            <td class="tdName">${name}</td>
            <td class="fees_type">${name}</td>
            <td class="amount_cell">
                <div class="input-group input-group-sm" style="max-width: 120px;">
                    <input type="number" class="form-control price_input" name="price[${id}]" value="${price}">
                    <div class="input-group-append"><span class="input-group-text">.00</span></div>
                </div>
            </td>
            <td class="payment_cell">
                <div class="input-group input-group-sm" style="max-width: 120px;">
                    <input type="number" class="form-control payment_input" name="paidPrice[${id}]" value="${paymentVal}" ${readonly}>
                    <div class="input-group-append"><span class="input-group-text">.00</span></div>
                </div>
            </td>
            <td><button type="button" class="primary-btn small bg-danger text-white border-0 clear_btn">Delete</button></td>
        </tr>`;
        $('.allFeesTypes').append(row);
        updateSerialNumbers();
    }
    // Fee type selection
    // $('#fee_group_id').on('change', function() {
    //     let selectedOption = $(this).find('option:selected');
    //     if (!selectedOption.val()) return;

    //     // Disable group selection - only allow individual fee types
    //     if (selectedOption.data('type') === 'group') {
    //         toastr.warning('Please select individual fee types, not fee groups.');
    //         $(this).val('').niceSelect('update');
    //         return;
    //     }

    //     let feeId = selectedOption.val();
    //     if (!addedFeeTypes.has(feeId)) {
    //         addFeeTypeRow(feeId, selectedOption.text().trim(), selectedOption.data('price'));
    //         addedFeeTypes.add(feeId);
    //     }
    //     $(this).val('').niceSelect('update');
    // });

    // Payment status change
    $('#status').on('change', function() {
        let status = $(this).val();
        $('.allFeesTypes tr').each(function(){
            let price = parseFloat($(this).find('.price_input').val()) || 0;
            let rowId = $(this).data('id');
            let readonly = status === 'full' ? 'readonly' : '';
            $(this).find('.payment_cell').html(`
                <div class="input-group input-group-sm" style="max-width: 120px;">
                    <input type="number" class="form-control payment_input" name="paidPrice[${rowId}]" value="${status === 'full' ? price : 0}" ${readonly}>
                    <div class="input-group-append"><span class="input-group-text">.00</span></div>
                </div>
            `);
        });
    });

    // Payment method toggle
    $('#paymentMethodName').on('change', function() {
        if (['Bank Transfer','Card'].includes($(this).val())) $('#bankPayment').removeClass('d-none');
        else $('#bankPayment').addClass('d-none');
    });

    // File Upload
    $('#uploadBtn').on('click', function() { $('#payment_file').click(); });
    $('#payment_file').on('change', function() { $('#fileName').text(this.files[0]?.name || ''); });

    // Submit edit invoice
    $('#invoiceForm').on('submit', function(e){
        e.preventDefault();

        // Basic form validation
        let isValid = true;
        let errorMessages = [];

        // Check required fields
        if (!$('#branch_id').val()) {
            errorMessages.push('Please select a branch');
            isValid = false;
        }

        if (!$('#create_date').val()) {
            errorMessages.push('Please select create date');
            isValid = false;
        }

        if (!$('#due_date').val()) {
            errorMessages.push('Please select due date');
            isValid = false;
        }

        if (!$('#status').val()) {
            errorMessages.push('Please select payment status');
            isValid = false;
        }

        // if (!$('#paymentMethodName').val()) {
        //     errorMessages.push('Please select payment method');
        //     isValid = false;
        // }

        // Check if there are any fee items
        if ($('.allFeesTypes tr').length === 0) {
            errorMessages.push('Please add at least one fee type');
            isValid = false;
        }

        // Show validation errors
        if (!isValid) {
            errorMessages.forEach(function(msg) {
                toastr.error(msg);
            });
            return false;
        }

        let invoiceId = $('#invoice_id').val();
        let formData = new FormData(this);

        // Collect fee data from the table
        $('.allFeesTypes tr').each(function(){
            let rowId = $(this).data('id');
            let price = $(this).find('.price_input').val();
            let paidPrice = $(this).find('.payment_input').val();

            formData.append('price[' + rowId + ']', price);
            formData.append('paidPrice[' + rowId + ']', paidPrice);
        });

        // Show loading state
        $('.fmInvoice').prop('disabled', true).text('Updating...');

        $.ajax({
            url: "{{ route('fees.fee-update-invoice', ':id') }}".replace(':id', invoiceId),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response){
                if(response.success){
                    toastr.success('Invoice updated successfully!');
                    window.location.href = '{{ route('fees.fee-invoice') }}';
                } else {
                    toastr.error(response.message || 'Something went wrong');
                    $('.fmInvoice').prop('disabled', false).text('Update');
                }
            },
            error: function(xhr){
                $('.fmInvoice').prop('disabled', false).text('Update');
                if(xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors){
                    $.each(xhr.responseJSON.errors, function(key, val){
                        val.forEach(msg => toastr.error(msg));
                    });
                } else {
                    toastr.error('Something went wrong');
                }
            }
        });
    });

});
</script>
@endpush
