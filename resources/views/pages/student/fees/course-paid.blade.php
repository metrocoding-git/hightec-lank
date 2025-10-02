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
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('fees.course_payment_details')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees')</a>
                    <a href="#">@lang('fees.course_payment_details')</a>
                </div>
            </div>
        </div>
        <span class="text-danger d-block">
            <strong id="result"></strong>
        </span>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <form action="{{ route('fees.invoice.store') }}" class="form-horizontal" id="invoiceForm"
                enctype="multipart/form-data" method="POST">
                @csrf
                <div class="row">
                    <!-- Left Section: Course Payment Details -->
                    <div class="col-lg-12"> {{-- Changed from col-lg-3 to col-lg-12 --}}
                        <div class="white-box">
                            <div class="main-title">
                                <h3>@lang('fees.course_payment_details')</h3>
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
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}">{{ $student->student_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Course --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('common.select_course') <span class="text-danger">*</span></label>
                                        <select class="primary_select form-control" id="course_id" name="course_id">
                                            <option value="">@lang('common.select_course')</option>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->id }}" data-price="{{ $course->price }}">{{ $course->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Course Price --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('fees.course_price') <span class="text-danger">*</span></label>
                                        <input type="number" class="primary_input_field form-control" id="course_price"
                                            name="course_price" value="0" step="0.01">
                                    </div>
                                </div>

                                {{-- Paid Amount --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('fees.paid_amount') <span class="text-danger">*</span></label>
                                        <input type="number" class="primary_input_field form-control" id="paid_amount"
                                            name="paid_amount" value="0" step="0.01">
                                    </div>
                                </div>

                                {{-- Due Amount --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('fees.due_amount')</label>
                                        <input type="number" class="primary_input_field form-control" id="due_amount"
                                            name="due_amount" value="0" step="0.01" readonly>
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
                                        <label>Upload Document</label>
                                        <div class="d-flex align-items-center">
                                            <input type="file" name="payment_file" id="payment_file" class="d-none"
                                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                            <button type="button" id="uploadBtn" class="primary-btn fix-gr-bg col-12"><span
                                                    class="ti-upload"></span>
                                                Browse</button>
                                            <span id="fileName" class="ml-3 text-muted"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button id="submit"
                                            class="primary-btn fix-gr-bg fmInvoice col-12">@lang('common.save')</button>
                                    </div>
                                </div>
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

            // AJAX: Load students based on branch (simplified as students are passed directly)
            $('#branch_id').change(function() {
                let branchId = $(this).val();
                $('#selectStudentLoader').show();
                $('#student_id').html('<option value="">Loading...</option>');

                if (branchId) {
                    // Filter students already available in the view
                    let filteredStudents = @json($students).filter(student => student.branch_id == branchId);
                    $('#student_id').empty().append('<option value="">Select Student</option>');
                    $.each(filteredStudents, function(index, student) {
                        $('#student_id').append('<option value="' + student.id + '">' +
                            student.student_name + '</option>');
                    });
                    $('#student_id').niceSelect('update');
                    $('#selectStudentLoader').hide();
                } else {
                    $('#selectStudentLoader').hide();
                    $('#student_id').html('<option value="">Select Student</option>');
                }
            });

            // Course selection change
            $('#course_id').change(function() {
                let selectedCourse = $(this).find('option:selected');
                let coursePrice = selectedCourse.data('price') || 0;
                $('#course_price').val(coursePrice).trigger('input'); // Trigger input to update due amount
            });

            // Calculate due amount
            function calculateDueAmount() {
                let coursePrice = parseFloat($('#course_price').val()) || 0;
                let paidAmount = parseFloat($('#paid_amount').val()) || 0;
                let dueAmount = coursePrice - paidAmount;
                $('#due_amount').val(dueAmount.toFixed(2));

                // Update payment status based on amounts
                if (paidAmount >= coursePrice && coursePrice > 0) {
                    $('#status').val('full').niceSelect('update');
                } else if (paidAmount > 0 && paidAmount < coursePrice) {
                    $('#status').val('partial').niceSelect('update');
                } else {
                    $('#status').val('not').niceSelect('update');
                }
            }

            // Event listeners for price and paid amount changes
            $('#course_price, #paid_amount').on('input', calculateDueAmount);

            // Initial calculation on page load
            calculateDueAmount();

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

            //form submit ajex
            $('#invoiceForm').on('submit', function(e) {
                e.preventDefault(); // prevent form submit
                let branch = $('#branch_id').val();
                let student = $('#student_id').val();
                let course = $('#course_id').val(); // New field
                let coursePrice = parseFloat($('#course_price').val()); // New field
                let paidAmount = parseFloat($('#paid_amount').val()); // New field
                let createDate = $('#create_date').val();
                let dueDate = $('#due_date').val();
                let status = $('#status').val();
                let paymentMethod = $('#paymentMethodName').val();

                // Validate new fields
                if (!course) {
                    toastr.error('Course is required');
                    return;
                }
                if (isNaN(coursePrice) || coursePrice <= 0) {
                    toastr.error('Course price must be a valid number greater than 0');
                    return;
                }
                if (isNaN(paidAmount) || paidAmount < 0) {
                    toastr.error('Paid amount must be a valid number (0 or greater)');
                    return;
                }

                // Validate existing fields
                if (!branch) {
                    toastr.error('Branch is required');
                    return;
                }

                if (!student) {
                    toastr.error('Student is required');
                    return;
                }

                if (!createDate) {
                    toastr.error('Create date is required');
                    return;
                }

                if (!dueDate) {
                    toastr.error('Due date is required');
                    return;
                }

                if (!status) {
                    toastr.error('Payment status is required');
                    return;
                }

                if (!paymentMethod) {
                    toastr.error('Payment method is required');
                    return;
                }

                let formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('branch_id', $('#branch_id').val());
                formData.append('student_id', $('#student_id').val() || '');
                formData.append('course_id', $('#course_id').val()); // Append new field
                formData.append('course_price', coursePrice); // Append new field
                formData.append('paid_amount', paidAmount); // Append new field
                formData.append('create_date', $('#create_date').val());
                formData.append('due_date', $('#due_date').val());
                formData.append('payment_status', $('#status').val());
                formData.append('payment_method', $('#paymentMethodName').val());

                if ($('#payment_file')[0].files.length > 0) {
                    formData.append('payment_file', $('#payment_file')[0].files[0]);
                }

                $.ajax({
                    url: '{{ route('fees.invoice.store') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            const message = encodeURIComponent('successfully!');
                            window.location.href = '{{ route('fees.add-invoice') }}' +
                                '?message-success=' + message;
                        }
                    },
                    error: function(xhr) {
                        let msg = 'An unexpected error occurred';

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            Object.values(errors).forEach((messages) => {
                                messages.forEach((message) => {
                                    toastr.error(message);
                                });
                            });
                            return;
                        }

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }

                        toastr.error(msg);
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
