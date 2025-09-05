@extends('layouts.app')
@section('title')
    @lang('academics.About')
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

            html[dir="rtl"] #main-content {
                overflow: initial !important;
            }
        </style>
    @endpush

    <section class="sms-breadcrumb mb-20">
{{-- <div id="toastContainer" class="position-fixed top-0 end-0 p-3" ></div> --}}

        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('fees.fees_invoice')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees')</a>
                    <a href="#">@lang('common.add') @lang('fees.fees_invoice')</a>
                </div>
            </div>
        </div>
        <span class="text-danger d-block">
            <strong id="result"></strong>

        </span>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <form class="form-horizontal" id="invoiceForm" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="row">
                    <!-- Left Section: Invoice Info -->
                    <div class="col-lg-3">
                        <div class="white-box">
                            <div class="main-title">
                                <h3>@lang('common.add') @lang('fees.fees_invoice')</h3>
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
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="pull-right loader" id="selectStudentLoader" style="display:none;">
                                            <img src="{{ asset('Modules/Fees/Resources/assets/img/pre-loader.gif') }}"
                                                style="width:28px;height:28px;">
                                        </div>
                                    </div>
                                </div>

                                {{-- Student --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('common.select_student') <span class="text-danger">*</span></label>

                                        <select class="primary_select form-control" id="student_id" name="student_id">
                                            <option value="">@lang('common.select_student')</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Create / Due Dates --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('fees.create_date') <span class="text-danger">*</span></label>

                                        <input type="text" class="primary_input_field date form-control" id="create_date"
                                            name="create_date" value="{{ date('m/d/Y') }}">
                                    </div>
                                </div>
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('fees.due_date') <span class="text-danger">*</span></label>
                                        <input type="text" class="primary_input_field date form-control" id="due_date"
                                            name="due_date" value="{{ date('m/d/Y') }}">
                                    </div>
                                </div>

                                {{-- Payment Status --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('fees.payment_status') <span class="text-danger">*</span></label>
                                        <select class="primary_select form-control" id="status" name="payment_status">
                                            <option value="">@lang('fees.payment_status')</option>
                                            <option value="not" data-status="not">@lang('fees.not_paid')</option>
                                            <option value="partial" data-status="partial">@lang('fees.partial_paid')</option>
                                            <option value="full" data-status="full">@lang('fees.full_paid')</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Payment Amount --}}
                                <div class="row mt-15 d-none" id="priceWrapper">
                                    <div class="col-lg-12">
                                        <label>@lang('fees.payment_amount') <span class="text-danger">*</span></label>
                                        <input type="number" class="primary_input_field form-control" id="payment_price"
                                            name="payment_amount">
                                    </div>
                                </div>

                                {{-- Payment Method --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('fees.payment_method') <span class="text-danger">*</span></label>
                                        <select class="primary_select form-control" id="paymentMethodName"
                                            name="payment_method">
                                            <option value="">@lang('fees.payment_method')</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Card">Card</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Bank Payment Section --}}
                                <div class="row mt-15 d-none" id="bankPayment">
                                    <div class="col-lg-12">
                                        <label>@lang('fees.upload_document')</label>
                                        <div class="d-flex align-items-center">
                                            <input type="file" name="payment_file" id="payment_file" class="d-none"
                                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                            <button type="button" id="uploadBtn"
                                                class="primary-btn small fix-gr-bg"><span class="ti-upload"></span>
                                                Browse</button>
                                            <span id="fileName" class="ml-3 text-muted"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button type="submit"
                                            class="primary-btn fix-gr-bg fmInvoice">@lang('common.save')</button>
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
                                        <option value="">-- Select Fee Group --</option>
                                        @foreach ($feesgroups as $feesGroup)
                                            <option data-type="group">
                                                {{ $feesGroup->name }}</option>
                                            @foreach ($feesGroup->feeType as $feeType)
                                                <option value="{{ $feeType->id }}" data-type="fee"
                                                    data-price="{{ $feeType->price }}">
                                                    &nbsp;&nbsp;&nbsp;{{ $feeType->name }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>

                                </div>
                            </div>

                            {{-- Fees Table --}}
                            <div class="big-table mt-10">
                                <table class="table school-table-style fees_invoice_type_table" cellspacing="0"
                                    width="100%">
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
                                    <tbody class="allFeesTypes"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>



            </form>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @include('components.partials.date_picker_css_js')
@endsection

@push('scripts')
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

            // Track added fee types
            let addedFeeTypes = new Set();

            // Fee Group / Fee Type selection
            $('#fee_group_id').on('change', function() {
                let selectedOption = $(this).find("option:selected");
                if (!selectedOption.val()) return;

                if (selectedOption.data('type') === 'group') {
                    selectedOption.nextUntil('option[data-type="group"]').each(function() {
                        let feeId = $(this).val();
                        if (!addedFeeTypes.has(feeId)) {
                            addFeeTypeRow(feeId, $(this).text().trim(), $(this).data('price'));
                            addedFeeTypes.add(feeId);
                        }
                    });
                } else {
                    let feeId = selectedOption.val();
                    if (!addedFeeTypes.has(feeId)) {
                        addFeeTypeRow(feeId, selectedOption.text().trim(), selectedOption.data('price'));
                        addedFeeTypes.add(feeId);
                    }
                }

                $(this).val('').niceSelect('update');
            });

            function addFeeTypeRow(id, name, price) {
                let paymentStatus = $('#status').find("option:selected").data('status') || 'not';
                let paymentCell = '';

                if (paymentStatus === 'full') {
                    paymentCell = `<span class="amount_text">${price}</span>`;
                } else if (paymentStatus === 'partial') {
                    paymentCell =
                        `<input type="number" class="primary_input_field form-control payment_input" name="paid_amount" value="${price}" autocomplete="off">`;
                } else {
                    paymentCell = `<span class="amount_text">0</span>`;
                }

                let row = `<tr data-id="${id}">
        <td class="sl"></td>
        <td class="tdName">${name}</td>
        <td class="fees_type">${name}</td>
        <td class="amount_cell">
            ${price ? `<span class="amount_text">${price}</span>` : `<input type="number" class="primary_input_field form-control price_input" name="price[${id}]" id="price[${id}]" value="${price}" autocomplete="off">`}
        </td>
        <td class="payment_cell">${paymentCell}</td>
        <td><button type="button" class="primary-btn small bg-danger text-white border-0 clear_btn">Delete</button></td>
    </tr>`;
                $('.allFeesTypes').append(row);
                updateSerialNumbers();
            }

            // Update all payment cells when payment status changes
            $('#status').on('change', function() {
                let selectedStatus = $(this).find("option:selected").data('status');
                $('.allFeesTypes tr').each(function() {
                    let price = parseFloat($(this).find('.amount_text').text()) || 0;
                    if (selectedStatus === 'full') {
                        $(this).find('.payment_cell').html(
                            `<span class="amount_text">${price}</span>`);
                    } else if (selectedStatus === 'partial') {
                        $(this).find('.payment_cell').html(
                            `<input type="number" class="primary_input_field form-control payment_input" name="price[${$(this).data('id')}]" value="${price}" autocomplete="off">`
                        );
                    } else {
                        $(this).find('.payment_cell').html(`<span class="amount_text">0</span>`);
                    }
                });

                // Show/hide manual price input at top if partial
                if (selectedStatus === 'partial') $('#priceWrapper').removeClass('d-none');
                else $('#priceWrapper').addClass('d-none');
            });

            function updateSerialNumbers() {
                $('.allFeesTypes tr').each(function(index) {
                    $(this).find('.sl').text(index + 1);
                });
            }

            // Delete row
            $(document).on('click', '.clear_btn', function() {
                let feeId = $(this).closest('tr').data('id');
                addedFeeTypes.delete(feeId);
                $(this).closest('tr').remove();
                updateSerialNumbers();
            });

            // Payment Status toggle
            function togglePriceSection(status) {
                if (status === 'partial') $('#priceWrapper').removeClass('d-none');
                else $('#priceWrapper').addClass('d-none');
            }
            $('#status').on('change', function() {
                let selectedStatus = $(this).find("option:selected").data('status');
                togglePriceSection(selectedStatus);
            });

            // Payment Method toggle
            function toggleBankPayment(method) {
                if (method === 'Bank Transfer' || method === 'Card') $('#bankPayment').removeClass('d-none');
                else $('#bankPayment').addClass('d-none');
            }
            $('#paymentMethodName').on('change', function() {
                toggleBankPayment($(this).val());
            });

            // File Upload
            $('#uploadBtn').on('click', function() {
                $('#payment_file').click();
            });
            $('#payment_file').on('change', function() {
                $('#fileName').text(this.files[0] ? this.files[0].name : '');
            });

            // AJAX: Load students based on branch
            $('#branch_id').change(function() {
                let branchId = $(this).val();
                $('#selectStudentLoader').show();
                $('#student_id').html('<option>Loading...</option>');
                if (branchId) {
                    $.get("{{ url('student/invoice/get-student') }}/" + branchId, function(data) {
                        $('#selectStudentLoader').hide();
                        $('#student_id').empty().append('<option value="">Select Student</option>');
                        $.each(data, function(i, student) {
                            $('#student_id').append('<option value="' + student.id + '">' +
                                student.name + '</option>');
                        });
                    }).fail(function() {
                        alert('Failed to load students');
                        $('#selectStudentLoader').hide();
                    });
                } else $('#student_id').html('<option value="">Select Student</option>');
            });



            //form submit ajex

            $('#invoiceForm').on('submit', function(e) {
                e.preventDefault(); // prevent form submit

                let formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('branch_id', $('#branch_id').val());
                formData.append('student_id', $('#student_id').val() || '');
                formData.append('create_date', $('#create_date').val());
                formData.append('due_date', $('#due_date').val());
                formData.append('payment_status', $('#status').val());
                // Loop over all payment inputs
                $('.payment_input').each(function() {
                    let inputName = $(this).attr('name'); // e.g., "price[5]"
                    let value = $(this).val() || '';
                    formData.append(inputName, value);
                });

                $('.price_input').each(function() {
                    let inputName = $(this).attr('name'); // e.g., "price[5]"
                    let value = $(this).val() || '';
                    formData.append(inputName, value);
                });




                $('.allFeesTypes tr').each(function() {
                    let feeId = $(this).data('id');
                    if (feeId) formData.append('fee_type_id[]', feeId);
                });






                formData.append('payment_method', $('#paymentMethodName').val());

                if ($('#payment_file')[0].files.length > 0) {
                    formData.append('payment_file', $('#payment_file')[0].files[0]);
                }

                // Log all FormData entries
                console.log("FormData contents:");
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ':', pair[1]);
                }

                $.ajax({
                    url: '{{ route('fees.invoice.store') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            showToast(response.message, 'success');
                            console.log('Invoice ID:', response.invoice_id);
                        } else {
                            showToast('Something went wrong', 'danger');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'An unexpected error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showToast(msg, 'danger');
                    }
                });
            });

            function showToast(message, type = 'success') {
                let toastHTML = `
    <div class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert">
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>`;

                $('#toastContainer').append(toastHTML);

                let toastEl = document.querySelector('#toastContainer .toast:last-child');
                let toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
    </script>
@endpush
