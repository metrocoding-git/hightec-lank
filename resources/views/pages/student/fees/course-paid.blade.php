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

    <!-- Breadcrumb -->
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Course Payment Details</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees')</a>
                    <a href="#">course payment details</a>
                </div>
            </div>
        </div>
        <span class="text-danger d-block">
            <strong id="result"></strong>
        </span>
    </section>

    <!-- Main Content -->
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid">
            <form action="{{ route('fees.course-payment.store') }}" method="POST" enctype="multipart/form-data" id="invoiceForm">
                @csrf

                <div class="row g-4">
                    <!-- Left Section: Payment Details -->
                    <div class="col-12 col-lg-4">
                        <div class="white-box ">
                            <div class="main-title">
                                <h3>Course Payment Details</h3>
                            </div>

                            <div class="add-visitor">
                                <input type="hidden" id="url" value="{{ URL::to('/') }}">

                                {{-- Branch --}}
                                <div class="mt-15 p-1 m-2">
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

                                {{-- Student --}}
                                <div class="mt-15 p-1 m-2" style="">
                                    <label class="mt-4">@lang('common.select_student') <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" id="student_id" name="student_id">
                                        <option value="">@lang('common.select_student')</option>
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}">{{ $student->student_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Dates --}}
                                <div class="mt-15 p-1 m-2">
                                    <label class="mt-4">@lang('fees.create_date') <span class="text-danger">*</span></label>
                                    <input type="text" class="primary_input_field date form-control" id="create_date"
                                        name="create_date" value="{{ date('m/d/Y') }}">
                                </div>
                                <div class="mt-15 p-1 m-2">
                                    <label class="mt-4">@lang('fees.due_date') <span class="text-danger">*</span></label>
                                    <input type="text" class="primary_input_field date form-control" id="due_date"
                                        name="due_date" value="{{ date('m/d/Y') }}">
                                </div>

                                {{-- Status --}}
                                <div class="mt-15 p-1 m-2">
                                    <label class="mt-4">@lang('fees.payment_status') <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" id="status" name="payment_status">
                                        <option value="">@lang('fees.payment_status')</option>
                                        <option value="not">@lang('fees.not_paid')</option>
                                        <option value="partial">@lang('fees.partial_paid')</option>
                                        <option value="full">@lang('fees.full_paid')</option>
                                    </select>
                                </div>

                                {{-- Payment Method --}}
                                <div class="mt-15 p-1 m-2">
                                    <label class="mt-4">@lang('fees.payment_method') <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" id="paymentMethodName"
                                        name="payment_method">
                                        <option value="">@lang('fees.payment_method')</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                    </select>
                                </div>

                                {{-- Bank Upload --}}
                                <div class="mt-15 d-none" id="bankPayment">
                                    <div class="row mt-15">
                                        <div class="col-lg-12">
                                            <label>Bank Paid Amount <span class="text-danger">*</span></label>
                                            <input type="number" class="primary_input_field form-control" id="bank_paid_amount"
                                                name="bank_paid_amount" value="0" step="0.01">
                                        </div>
                                    </div>
                                    <div class="row mt-15">
                                        <div class="col-lg-12">
                                            <label>bank Paid date <span class="text-danger">*</span></label>
                                            <input type="date" class="primary_input_field date form-control" id="bank_paid_date"
                                                name="bank_paid_date" value="{{ date('m/d/Y') }}">
                                        </div>
                                    </div>
                                    <div class="row mt-15">
                                        <div class="col-lg-12">
                                            <label>Upload Document</label>
                                            <div class="d-flex align-items-center">
                                                <input type="file" name="payment_file" id="payment_file" class="d-none"
                                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                                <button type="button" id="uploadBtn" class="primary-btn fix-gr-bg col-12">
                                                    <span class="ti-upload"></span> Browse
                                                </button>
                                                <span id="fileName" class="ml-3 text-muted"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Save Button --}}
                                <div class="mt-4 text-center" >
                                    <button id="submit" class="mt-4 primary-btn fix-gr-bg fmInvoice col-12">
                                        @lang('common.save')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section: Course Table -->
                    <div class="col-12 col-lg-8">
                        <div class="white-box h-100">
                            <div class="main-title">
                                <h3>Course Details</h3>
                            </div>

                            {{-- Select Course --}}
                            <div class="mt-15">
                                <label>Select Course <span class="text-danger">*</span></label>
                                <select class="primary_select form-control" id="course_id" name="course_id">
                                    <option value="">select course</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}" data-price="{{ $course->price }}">
                                            {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Table --}}
                            <div class="big-table mt-10 table-responsive">
                                <table class="table school-table-style fees_invoice_type_table w-100">
                                    <thead>
                                        <tr>
                                            <th>@lang('common.sl')</th>
                                            <th>@lang('fees.course_name')</th>
                                            <th>@lang('fees.course_price')</th>
                                            <th>@lang('fees.paid_amount')</th>
                                            <th>@lang('fees.due_amount')</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody class="allCourseDetails">
                                        {{-- Rows added by JS --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- /.row -->
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
                    let filteredStudents = @json($students).filter(student => student
                        .branch_id == branchId);
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

            // Track added course
            let addedCourseId = null;

            // Course selection change
            $('#course_id').change(function() {
                let selectedOption = $(this).find('option:selected');
                let courseId = selectedOption.val();
                let courseName = selectedOption.text().trim();
                let coursePrice = parseFloat(selectedOption.data('price')) || 0;

                if (!courseId) {
                    $('.allCourseDetails').empty();
                    addedCourseId = null;
                    return;
                }

                // Clear old course if a new one is selected
                if (addedCourseId !== null && addedCourseId !== courseId) {
                    $('.allCourseDetails').empty();
                }

                if (addedCourseId === courseId) {
                    // If the same course is selected again, do nothing or update if needed
                    // For now, we'll just return to avoid re-adding
                    return;
                }

                addCourseRow(courseId, courseName, coursePrice);
                addedCourseId = courseId;

                $(this).val('').niceSelect('update'); // Clear selection after adding
            });

            function addCourseRow(id, name, price) {
                let row = `<tr data-id="${id}">
                    <td class="sl">1</td>
                    <td class="course_name">${name}</td>
                    <td class="course_price_cell">
                        <div class="input-group input-group-sm" style="max-width: 120px;">
                            <input type="number"
                                class="form-control course_price_input"
                                name="course_price[${id}]"
                                value="${price}"
                                step="0.01"
                            >
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </td>
                    <td class="paid_amount_cell">
                        <div class="input-group input-group-sm" style="max-width: 120px;">
                            <input type="number"
                                class="form-control paid_amount_input"
                                name="paid_amount[${id}]"
                                value="0"
                                step="0.01"
                            >
                            <div class="input-group-append">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                    </td>
                    <td class="due_amount_cell">
                        <div class="input-group input-group-sm" style="max-width: 100px;">
                            <input type="number"
                                class="form-control due_amount_display"
                                value="${price.toFixed(2)}"
                                step="0.01"
                                readonly
                            >
                            
                        </div>
                    </td>
                    <td><button type="button" class="primary-btn small bg-danger text-white border-0 remove_course_btn">Delete</button></td>
                </tr>`;
                $('.allCourseDetails').append(row);
                updatePaymentStatusAndDue();
            }

            // Recalculate due amount and update payment status
            function updatePaymentStatusAndDue() {
                let coursePrice = parseFloat($('.course_price_input').val()) || 0;
                let paidAmount = parseFloat($('.paid_amount_input').val()) || 0;
                let dueAmount = coursePrice - paidAmount;

                $('.due_amount_display').val(dueAmount.toFixed(2));

                if (paidAmount >= coursePrice && coursePrice > 0) {
                    $('#status').val('full').niceSelect('update');
                } else if (paidAmount > 0 && paidAmount < coursePrice) {
                    $('#status').val('partial').niceSelect('update');
                } else {
                    $('#status').val('not').niceSelect('update');
                }
            }

            // Event listeners for price and paid amount changes in the table
            $(document).on('input', '.course_price_input, .paid_amount_input', updatePaymentStatusAndDue);

            // Remove course row
            $(document).on('click', '.remove_course_btn', function() {
                $(this).closest('tr').remove();
                addedCourseId = null;
                updatePaymentStatusAndDue(); // Recalculate if needed (e.g., if only one course was there)
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

            // AJAX: Load students based on branch (simplified as students are passed directly)
            $('#branch_id').change(function() {
                let branchId = $(this).val();
                $('#selectStudentLoader').show();
                $('#student_id').html('<option value="">Loading...</option>');

                if (branchId) {
                    let filteredStudents = @json($students).filter(student => student
                        .branch_id == branchId);
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

            //form submit ajex
            $('#invoiceForm').on('submit', function(e) {
                e.preventDefault(); // prevent form submit
                let branch = $('#branch_id').val();
                let student = $('#student_id').val();
                let createDate = $('#create_date').val();
                let dueDate = $('#due_date').val();
                let status = $('#status').val();
                let paymentMethod = $('#paymentMethodName').val();

                // Get course details from the table
                const courseRow = $('.allCourseDetails tr');
                if (courseRow.length === 0) {
                    toastr.error('Please select a course');
                    return;
                }

                let courseId = courseRow.data('id');
                let courseName = courseRow.find('.course_name').text(); // Get course name
                let coursePrice = parseFloat(courseRow.find('.course_price_input').val());
                let paidAmount = parseFloat(courseRow.find('.paid_amount_input').val());

                // Validate course details
                if (!courseId) {
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

                // Validate bank payment fields if visible
                if (paymentMethod === 'Bank Transfer' || paymentMethod === 'Card') {
                    let bankPaidAmount = parseFloat($('#bank_paid_amount').val());
                    let bankPaidDate = $('#bank_paid_date').val();

                    if (isNaN(bankPaidAmount) || bankPaidAmount <= 0) {
                        toastr.error('Bank Paid Amount is required and must be greater than 0');
                        return;
                    }
                    if (!bankPaidDate) {
                        toastr.error('Bank Paid Date is required');
                        return;
                    }
                }


                let formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('branch_id', $('#branch_id').val());
                formData.append('student_id', $('#student_id').val() || '');
                formData.append('create_date', $('#create_date').val());
                formData.append('due_date', $('#due_date').val());
                formData.append('payment_status', $('#status').val());
                formData.append('payment_method', $('#paymentMethodName').val());

                // Append course details from the table
                formData.append('course_id', courseId);
                formData.append('course_name', courseName); // Also append course name for potential use
                formData.append('course_price', coursePrice);
                formData.append('paid_amount', paidAmount);

                // Append bank payment details if applicable
                if (paymentMethod === 'Bank Transfer' || paymentMethod === 'Card') {
                    formData.append('bank_paid_amount', parseFloat($('#bank_paid_amount').val()));
                    formData.append('bank_paid_date', $('#bank_paid_date').val());
                }

                if ($('#payment_file')[0].files.length > 0) {
                    formData.append('payment_file', $('#payment_file')[0].files[0]);
                }

                $.ajax({
                    url: '{{ route('fees.course-payment.store') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            const message = encodeURIComponent('successfully!');
                            window.location.href = '{{ route('fees.course-payment') }}' +
                                '?message-success=' + message;
                        }
                    },
                    error: function(xhr) {
                        let msg = 'An unexpected error occurred';
                        console.error('AJAX Error:', xhr); // Log the full XHR object

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            console.error('Validation Errors:', errors); // Log validation errors
                            Object.values(errors).forEach((messages) => {
                                messages.forEach((message) => {
                                    toastr.error(message);
                                });
                            });
                            return;
                        }

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                            console.error('Server Error Message:', msg); // Log server error message
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
