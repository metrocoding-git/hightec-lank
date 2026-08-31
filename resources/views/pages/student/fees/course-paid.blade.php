@extends('layouts.app')

{{-- BUG FIX (Section 27): the browser-tab title called @lang('academics.About') —
     a leftover placeholder — instead of naming this page. Also adding the
     standard modern-refresh + breadcrumb CSS, which this page never defined
     despite using the sms-breadcrumb/white-box markup. --}}
@section('title')
    Course Payment Details
@endsection

@section('mainContent')
    @php
        // Permission key from config_permissions.php ("Fees" category, Course, NVQ & Food Payment window).
        $canAddCoursePayment = Auth::user()->hasPermission('fees.course-payment.store');
    @endphp
    @push('css')
        <link rel="stylesheet" href="{{ url('Modules/Fees/Resources/assets/css/feesStyle.css') }}" />
        <style>
            .white-box {
                border-radius: 12px;
                box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
                border: 1px solid var(--border_color);
            }

            .white-box .main-title h3,
            .white-box .main-title h5 {
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

            .text-muted {
                color: #9aa1ac !important;
            }

            .sms-breadcrumb {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
                border: 1px solid var(--border_color);
                padding: 18px 24px;
                position: relative;
                overflow: hidden;
            }

            .sms-breadcrumb::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                width: 4px;
                background: linear-gradient(180deg, var(--gradient_1), var(--gradient_2));
            }

            .sms-breadcrumb .row {
                align-items: center;
            }

            .sms-breadcrumb h1 {
                font-size: 20px;
                font-weight: 700;
                margin: 0;
            }

            .sms-breadcrumb .bc-pages {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
            }

            .sms-breadcrumb .bc-pages a {
                font-size: 13px;
                font-weight: 500;
                color: #8a90a2;
                text-decoration: none;
                transition: color .15s ease;
            }

            .sms-breadcrumb .bc-pages a:hover {
                color: var(--gradient_1);
            }

            .sms-breadcrumb .bc-pages a:not(:last-child)::after {
                content: '\203A';
                margin: 0 8px;
                color: #c7cbd6;
                font-weight: 400;
            }

            .sms-breadcrumb .bc-pages a:last-child {
                color: var(--gradient_1);
                font-weight: 700;
                pointer-events: none;
            }

            /* ---- Payment History card polish ---- */
            .main-title.invoice-history-card {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 10px;
                padding-bottom: 14px;
                border-bottom: 1px solid var(--border_color);
            }

            .main-title.invoice-history-card h3::after {
                display: none;
            }

            .main-title.invoice-history-card .title-group {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .main-title.invoice-history-card .title-icon {
                width: 38px;
                height: 38px;
                border-radius: 10px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, var(--gradient_1), var(--gradient_2));
                color: #fff;
                font-size: 16px;
                flex-shrink: 0;
            }

            .record-count-badge {
                font-size: 12px;
                font-weight: 600;
                color: var(--gradient_1);
                background: rgba(0, 0, 0, 0.04);
                border-radius: 999px;
                padding: 5px 14px;
                white-space: nowrap;
            }

            .status-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 600;
                text-transform: capitalize;
                white-space: nowrap;
                line-height: 1.4;
            }

            .status-badge.status-course {
                background: rgba(0, 123, 255, 0.14);
                color: #0056b3;
            }

            .status-badge.status-nvq {
                background: rgba(111, 66, 193, 0.14);
                color: #59339d;
            }

            .status-badge.status-food {
                background: rgba(40, 167, 69, 0.14);
                color: #1e7e34;
            }

            .fw-600 {
                font-weight: 600;
            }

            #paymentHistoryTable tbody tr:hover {
                background: rgba(0, 0, 0, 0.02);
            }
        </style>
    @endpush
    <style>
        .school-table-style tr th,
        .school-table-style tr td,
        .school-table-style tfoot tr td {
            padding: 10px 15px !important;
        }

        html[dir="rtl"] #main-content {
            overflow: initial !important;
        }

        .left-section-card {
            height: 100%;
        }

        /* ---- Mode toggle button styles ---- */
        /* ---- Mode toggle button styles ---- */
        #toggleMode {
            transition: background .2s, border-color .2s;
        }

        #toggleMode.mode-normal {
            background: #007bff !important;
            border-color: #007bff !important;
            color: #fff !important;
        }

        #toggleMode.mode-nvq {
            background: #6f42c1 !important;
            border-color: #6f42c1 !important;
            color: #fff !important;
        }

        #toggleMode.mode-food {
            background: #28a745 !important;
            border-color: #28a745 !important;
            color: #fff !important;
        }

        #leftPanelTitle,
        #rightPanelTitle {
            transition: color .2s;
        }

        .title-normal {
            color: #007bff;
        }

        .title-nvq {
            color: #6f42c1;
        }

        .title-food {
            color: #28a745;
        }



        /* ---- Food-specific fields ---- */
        #foodPaymentFields .meal-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        #foodPaymentFields .meal-row label {
            min-width: 90px;
            margin: 0;
            font-weight: 500;
        }

        #foodPaymentFields .meal-row input[type="number"] {
            width: 110px;
        }

        .meal-card {
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            background: #fafafa;
            transition: all .2s ease;
        }

        .meal-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .meal-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .meal-header label {
            font-weight: 600;
            cursor: pointer;
        }

        .meal-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .meal-amount {
            width: 100%;
        }
    </style>
    <!-- Breadcrumb -->
    <section class="sms-breadcrumb mb-20">
        <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 11"></div>
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1 id="TopPanelTitle">Course, NVQ & Food Fee Payments</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Course, NVQ & Food Fee Payments</a>
                </div>
            </div>
        </div>
        <span class="text-danger d-block">
            <strong id="result"></strong>
        </span>
    </section>

    <!-- Main Content -->
    <section class="admin-visitor-area up_st_admin_visitor">

            <div class="white-box mb-10">
                <div class="p-0 col-12 ">
                    <button type="button" class="col-12 primary-btn mode-normal" id="toggleMode">
                        Switch to NVQ Payment
                    </button>
                </div>
            </div>


            <form method="POST" enctype="multipart/form-data" id="invoiceForm">
                @csrf

                {{-- Store URL is set dynamically by JS based on mode --}}
                <input type="hidden" id="formActionUrl" value="{{ route('fees.course-payment.store') }}">

                <div class="row g-4">
                    <!-- ==================== Left Section ==================== -->
                    <div class="col-12 col-lg-3">
                        <div class="white-box left-section-card">
                            <div class="main-title mb-3">
                                <h3 id="leftPanelTitle">Course Payment Details</h3>
                            </div>

                            <div class="add-visitor">
                                <input type="hidden" id="url" value="{{ URL::to('/') }}">

                                {{-- Branch --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('common.branch') <span class="text-danger">*</span></label>
                                        <select class="primary_select form-control" id="branch_id" name="branch_id">
                                            @if (count($branches) > 1)
                                                <option value="">@lang('common.select_class')</option>
                                            @endif
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ count($branches) == 1 ? 'selected' : '' }}>{{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Student --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <label>@lang('common.select_student') <span class="text-danger">*</span></label>
                                        <select class="primary_select form-control" id="student_id" name="student_id">
                                            <option value="">@lang('common.select_student')</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}">{{ $student->admission_number }} -
                                                    {{ $student->student_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Dates --}}
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

                                {{-- Payment Status (hidden, set by JS) --}}
                                <div class="row mt-15" hidden>
                                    <div class="col-lg-12">
                                        <label>@lang('fees.payment_status') <span class="text-danger">*</span></label>
                                        <select class="primary_select form-control" id="status" name="payment_status">
                                            <option value="">@lang('fees.payment_status')</option>
                                            <option value="not">@lang('fees.not_paid')</option>
                                            <option value="partial">@lang('fees.partial_paid')</option>
                                            <option value="full">@lang('fees.full_paid')</option>
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
                                            <option value="Invoice">Invoice</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Bank Upload Section --}}
                                <div class="row mt-15 d-none" id="bankPaymentSection">
                                    <div class="col-lg-12" id="bankPaidAmountRow">
                                        <label>Bank Paid Amount <span class="text-danger">*</span></label>
                                        <input type="number" class="primary_input_field form-control" id="bank_paid_amount"
                                            name="bank_paid_amount" value="0" step="0.01">
                                    </div>

                                    <div class="col-lg-12 mt-15" id="bankPaidDateRow">
                                        <label>Bank Paid Date <span class="text-danger">*</span></label>
                                        <input type="date" class="primary_input_field date form-control"
                                            id="bank_paid_date" name="bank_paid_date" value="{{ date('Y-m-d') }}"
                                            max="{{ date('Y-m-d') }}">
                                    </div>

                                    <div class="col-lg-12 mt-15" id="uploadDocumentRow">
                                        <label>Upload Document</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="file" name="payment_file" id="payment_file" class="d-none"
                                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                            <button type="button" id="uploadBtn"
                                                class="primary-btn fix-gr-bg flex-grow-1">
                                                <span class="ti-upload"></span> Browse
                                            </button>
                                            <span id="fileName" class="text-muted small"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===== FOOD PAYMENT FIELDS (hidden by default) ===== --}}
                                <div id="foodPaymentFields" class="d-none">
                                    <hr>
                                    <div class="main-title mb-2">
                                        <h5 class="text-success">🍽 Food Details</h5>
                                    </div>

                                    {{-- From / To Date --}}
                                    <div class="row mt-10">
                                        <div class="col-lg-12">
                                            <label>Food From Date <span class="text-danger">*</span></label>
                                            <input type="date" class="primary_input_field form-control"
                                                id="food_from_date" name="food_from_date" value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="row mt-10">
                                        <div class="col-lg-12">
                                            <label>Food To Date <span class="text-danger">*</span></label>
                                            <input type="date" class="primary_input_field form-control"
                                                id="food_to_date" name="food_to_date" value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>

                                    {{-- Meal Toggles + Amounts --}}
                                    <div class="row mt-10">
                                        <div class="col-lg-12">
                                            <label class="d-block mb-3">Meals Included</label>

                                            <div class="meal-card">
                                                <div class="meal-header">
                                                    <input type="checkbox" id="has_breakfast" name="has_breakfast"
                                                        value="1" class="meal-checkbox" data-meal="breakfast">
                                                    <label for="has_breakfast" class="mb-0">🍳 Breakfast</label>
                                                </div>
                                                <input type="number" class="form-control meal-amount"
                                                    id="breakfast_amount" name="breakfast_amount"
                                                    placeholder="Breakfast Amount" step="0.01" min="0"
                                                    readonly>
                                            </div>

                                            <div class="meal-card">
                                                <div class="meal-header">
                                                    <input type="checkbox" id="has_lunch" name="has_lunch"
                                                        value="1" class="meal-checkbox" data-meal="lunch">
                                                    <label for="has_lunch" class="mb-0">🍛 Lunch</label>
                                                </div>
                                                <input type="number" class="form-control meal-amount" id="lunch_amount"
                                                    name="lunch_amount" placeholder="Lunch Amount" step="0.01"
                                                    min="0" readonly>
                                            </div>

                                            <div class="meal-card">
                                                <div class="meal-header">
                                                    <input type="checkbox" id="has_dinner" name="has_dinner"
                                                        value="1" class="meal-checkbox" data-meal="dinner">
                                                    <label for="has_dinner" class="mb-0">🍽️ Dinner</label>
                                                </div>
                                                <input type="number" class="form-control meal-amount" id="dinner_amount"
                                                    name="dinner_amount" placeholder="Dinner Amount" step="0.01"
                                                    min="0" readonly>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                {{-- END food fields --}}

                                {{-- Notes --}}
                                <div class="row mt-15">
                                    <div class="col-lg-12 ">
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="modal_notes">Notes</label>
                                            <textarea class="primary_input_field form-control{{ $errors->has('notes') ? ' is-invalid' : '' }}" name="notes"
                                                id="modal_notes" rows="4" placeholder="Add notes here">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Save --}}
                                @if ($canAddCoursePayment)
                                    <div class="row mt-15">
                                        <div class="col-lg-12 text-center">
                                            <button id="submit" class="primary-btn fix-gr-bg fmInvoice w-100 py-1">
                                                @lang('common.save')
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="row mt-15">
                                        <div class="col-lg-12 text-center text-muted">
                                            You do not have permission to record course payments.
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- ==================== Right Section ==================== -->
                    <div class="col-12 col-lg-9">
                        <div class="white-box h-100">
                            <div class="main-title mb-3">
                                <h3 id="rightPanelTitle">Course Details</h3>
                            </div>

                            {{-- Select Course --}}
                            <div class="row mt-15">
                                <div class="col-lg-12">
                                    <label>Select Course <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control mb-2" id="course_id" name="course_id">
                                        <option value="">select course</option>
                                    </select>
                                    <div class="pull-right loader" id="selectCourseLoader" style="display:none;">
                                        <img src="{{ asset('Modules/Fees/Resources/assets/img/pre-loader.gif') }}"
                                            style="width:28px;height:28px;">
                                    </div>

                                    {{-- Course table --}}
                                    <div class="big-table table-responsive mt-4">
                                        <table class="table school-table-style fees_invoice_type_table w-100">
                                            <thead>
                                                <tr>
                                                    <th>@lang('common.sl')</th>
                                                    <th>Course Name</th>
                                                    <th>Intake Name</th>
                                                    <th>Amount</th>
                                                    <th>@lang('fees.paid_amount')</th>
                                                    <th>Due Amount</th>
                                                    <th id="typeColHeader">Type</th>
                                                    <th>@lang('common.action')</th>
                                                </tr>
                                            </thead>
                                            <tbody class="allCourseDetails"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Payment History --}}
                            {{-- BUG FIX (Section 27 addendum): this column was labelled "Status" but the
                                 row-builder JS below has only ever populated it with payment.payment_type
                                 (course / nvq / food) — there is no separate paid/partial/pending status in
                                 the history payload. Relabelled to "Payment Type" to match what is actually
                                 shown, and the value now renders as a colour-coded pill instead of plain text. --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="white-box">
                                        <div class="main-title mb-3 invoice-history-card">
                                            <div class="title-group">
                                                {{-- <span class="title-icon"><span class="ti-money"></span></span> --}}
                                                <h3 class="mb-0">Payment History</h3>
                                            </div>
                                            <span class="record-count-badge" id="paymentHistoryCount">0 records</span>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table school-table-style w-100" id="paymentHistoryTable">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Paid Amount</th>
                                                        <th>Payment Method</th>
                                                        <th>Payment Type</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div><!-- /.row -->
            </form>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @include('components.partials.date_picker_css_js')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // ================================================================
            // MODE MANAGEMENT
            // ================================================================
            const MODES = ['normal', 'nvq', 'food'];
            const MODE_LABELS = {
                normal: 'Switch to NVQ Payment',
                nvq: 'Switch to Food Payment',
                food: 'Switch to Normal Course Payment',
            };
            const MODE_CLASSES = {
                normal: 'mode-normal',
                nvq: 'mode-nvq',
                food: 'mode-food'
            };

            let currentMode = localStorage.getItem('coursePaymentMode') || 'normal';
            let addedCourseId = null;

            function setMode(mode) {
                currentMode = mode;
                localStorage.setItem('coursePaymentMode', mode);


                // Button label + colour
                $('#toggleMode')
                    .text(MODE_LABELS[mode])
                    .removeClass('mode-normal mode-nvq mode-food')
                    .addClass(MODE_CLASSES[mode]);

                // ── FIX 2: Panel title text + color to match button ──────────
                const titles = {
                    normal: {
                        left: 'Course Payment Details',
                        right: 'Course Details'
                    },
                    nvq: {
                        left: 'NVQ Payment Details',
                        right: 'NVQ Course Details'
                    },
                    food: {
                        left: 'Food Payment Details',
                        right: 'Food Course Details'
                    },
                };
                const titleColors = {
                    normal: '#007bff',
                    nvq: '#6f42c1',
                    food: '#28a745'
                };

                $('#leftPanelTitle').text(titles[mode].left).css('color', titleColors[mode]);
                $('#TopPanelTitle').text(titles[mode].left);


                $('#rightPanelTitle').text(titles[mode].right).css('color', titleColors[mode]);
                // ─────────────────────────────────────────────────────────────

                // Show/hide food-specific fields
                if (mode === 'food') {
                    $('#foodPaymentFields').removeClass('d-none');
                } else {
                    $('#foodPaymentFields').addClass('d-none');
                }

                const urls = {
                    normal: '{{ route('fees.course-payment.store') }}',
                    nvq: '{{ route('fees.course-payment.store') }}',
                    food: '{{ route('fees.course-payment.store') }}',
                };
                $('#formActionUrl').val(urls[mode]);

                toastr.success(
                    mode === 'normal' ? 'Normal Course Payment Mode' :
                    mode === 'nvq' ? 'NVQ Payment Mode Activated' :
                    'Food Payment Mode Activated'
                );

                $('#food_from_date').prop('readonly', true);
                $('#has_breakfast, #has_lunch, #has_dinner').prop('disabled', true);
                $('#breakfast_amount, #lunch_amount, #dinner_amount').prop('readonly', true);
                applyPriceLockState($('.allCourseDetails tr'));

                $('#course_id').trigger('change');
            }

            $('#toggleMode').on('click', function() {
                const nextIndex = (MODES.indexOf(currentMode) + 1) % MODES.length;
                setMode(MODES[nextIndex]);
            });

            // Initialize title color on load
            setMode(currentMode);

            // ================================================================
            // BRANCH CHANGE → load students
            // ================================================================
            if ({{ count($branches) }} == 1) {
                $('#branch_id').trigger('change');
            }
            toggleBankPayment('');

            $('#branch_id').change(function() {
                let branchId = $(this).val();
                $('#selectStudentLoader').show();
                $('#student_id').html('<option value="">Loading...</option>');

                if (branchId) {
                    let filteredStudents = @json($students);
                    $('#student_id').empty().append('<option value="">Select Student</option>');
                    $.each(filteredStudents, function(index, student) {
                        $('#student_id').append(
                            '<option value="' + student.id + '">' +
                            student.admission_number + ' - ' + student.student_name +
                            '</option>'
                        );
                    });
                    $('#student_id').niceSelect('update');
                    $('#selectStudentLoader').hide();
                } else {
                    $('#selectStudentLoader').hide();
                    $('#student_id').html('<option value="">Select Student</option>');
                    $('#course_id').empty().append('<option value="">select course</option>');
                    $('#course_id').niceSelect('update');
                    $('.allCourseDetails').empty();
                    addedCourseId = null;
                }
            });

            // ================================================================
            // STUDENT CHANGE → load courses
            // ================================================================
            $('#student_id').change(function() {
                $('#course_id').trigger('change');

                let studentId = $(this).val();
                if (!studentId) {
                    $('#course_id').empty().append('<option value="">select course</option>');
                    $('#course_id').niceSelect('update');
                    $('.allCourseDetails').empty();
                    addedCourseId = null;
                    return;
                }

                let allStudents = @json($students);
                let selectedStudent = allStudents.find(s => s.id == studentId);
                if (selectedStudent && selectedStudent.is_admission == 0) {
                    alert("This student has not paid the admission fee.");
                    $(this).val("").niceSelect('update');
                    return;
                }

                $('#selectCourseLoader').show();
                $('.allCourseDetails').empty();
                addedCourseId = null;

                $.ajax({
                    url: '{{ route('get-courses-by-student', ':studentId') }}'.replace(
                        ':studentId', studentId),
                    type: 'GET',
                    success: function(data) {
                        $('#course_id').empty().append(
                            '<option value="">select course</option>');
                        if (data.success && data.courses.length > 0) {
                            data.courses.forEach(function(course) {
                                let changeCourseText = course.change_course ?
                                    ' (Changed to: ' + course.change_course + ')' : '';
                                $('#course_id').append(
                                    '<option value="' + course.course_id + '"' +
                                    ' data-price="' + course.course_price + '"' +
                                    ' data-name="' + course.course_name + '"' +
                                    ' data-intake="' + course.intake_name + '"' +
                                    ' data-has-nvq="' + course.has_nvq_payment +
                                    '"' +
                                    ' data-nvq-amount="' + (course.nvq_amount ??
                                        0) + '"' +
                                    ' data-has-food="' + course.has_food_fee + '"' +
                                    ' data-breakfast-amount="' + (course
                                        .breakfast_amount ?? 0) + '"' +
                                    ' data-lunch-amount="' + (course.lunch_amount ??
                                        0) + '"' +
                                    ' data-dinner-amount="' + (course
                                        .dinner_amount ?? 0) + '"' +

                                    ' data-intake-start="' + (course
                                        .intake_start_date ?? '') + '"' +
                                    ' data-intake-duration="' + (course
                                        .intake_duration ?? '') + '"' +
                                    ' data-status="' + course.status + '">' +
                                    course.course_name + ' - ' + course
                                    .intake_name + changeCourseText +
                                    '</option>'
                                );
                            });
                        }
                        $('#course_id').niceSelect('update');
                        $('#selectCourseLoader').hide();
                    },
                    error: function() {
                        $('#selectCourseLoader').hide();
                    }
                });
            });

            // ================================================================
            // COURSE CHANGE → add row to table
            // ================================================================
            $('#course_id').change(function() {
                let selectedOption = $(this).find('option:selected');
                let courseId = selectedOption.val();
                let courseName = selectedOption.data('name');
                let intakeName = selectedOption.data('intake');
                let hasNVQ = selectedOption.data('has-nvq');
                let hasFood = selectedOption.data('has-food');
                let nvqAmount = parseFloat(selectedOption.data('nvq-amount')) || 0;
                let status = selectedOption.data('status');
                let coursePrice = parseFloat(selectedOption.data('price')) || 0;

                let courseBreakfast = parseFloat(selectedOption.data('breakfast-amount')) || 0;
                let courseLunch = parseFloat(selectedOption.data('lunch-amount')) || 0;
                let courseDinner = parseFloat(selectedOption.data('dinner-amount')) || 0;

                if (currentMode == 'nvq') {
                    if (!hasNVQ && courseId) {
                        toastr.warning('This course has no NVQ payment', null, {
                            timeOut: 10000
                        });
                        $(this).val('').niceSelect('update');
                        return;
                    }
                    if (status !== 'start' && status) {
                        toastr.warning('This course status: ' + status, null, {
                            timeOut: 20000,
                            extendedTimeOut: 10000
                        });
                    }
                    coursePrice = nvqAmount;
                }

                if (currentMode === 'food') {
                    $('.course_price_input').data('manually-edited', false);
                    if (!hasFood && courseId) {
                        toastr.warning('This course does not have food fee enabled.', null, {
                            timeOut: 10000
                        });
                        $(this).val('').niceSelect('update');
                        return;
                    }
                    $('#has_breakfast').prop('checked', courseBreakfast > 0).prop('disabled', false);
                    $('#breakfast_amount').val(courseBreakfast > 0 ? courseBreakfast : '');
                    $('#has_lunch').prop('checked', courseLunch > 0).prop('disabled', false);
                    $('#lunch_amount').val(courseLunch > 0 ? courseLunch : '');
                    $('#has_dinner').prop('checked', courseDinner > 0).prop('disabled', false);
                    $('#dinner_amount').val(courseDinner > 0 ? courseDinner : '');
                    coursePrice = courseBreakfast + courseLunch + courseDinner;



                    recalcFoodTotal();
                }

                if (!courseId) {
                    $('.allCourseDetails').empty();
                    addedCourseId = null;
                    $('#paymentHistoryTable tbody').empty();
                    $('#paymentHistoryCount').text('0 records');
                    return;
                }

                if (addedCourseId !== null && addedCourseId !== courseId) {
                    $('.allCourseDetails').empty();
                }
                if (addedCourseId === courseId) return;

                addedCourseId = courseId;
                let studentId = $('#student_id').val();

                if (studentId && courseId) {
                    $.ajax({
                        url: '{{ route('fees.course-payment-history', [':studentId', ':courseId']) }}'
                            .replace(':studentId', studentId).replace(':courseId', courseId),
                        type: 'GET',
                        success: function(data) {
                            if (data.success) {
                                let effectivePrice = coursePrice;

                                if (currentMode === 'nvq' && data.nvq_amount) {
                                    effectivePrice = parseFloat(data.nvq_amount);
                                } else if (currentMode === 'normal' && data.course_amount) {
                                    effectivePrice = parseFloat(data.course_amount);
                                } else if (currentMode === 'food') {
                                    var foodHasExisting = !data.is_new_food_payment;
                                    if (data.food_amount && parseFloat(data.food_amount) > 0 &&
                                        !data.is_new_food_payment) {
                                        effectivePrice = parseFloat(data.food_amount);
                                        $('#food_from_date').val(data.from_date).prop(
                                            'readonly', true);
                                        $('#food_to_date').val(data.to_date);
                                        $('#has_breakfast').prop('checked', data
                                            .has_breakfast == 1).prop('disabled', true);
                                        $('#breakfast_amount').val(data.breakfast_amount ??
                                            courseBreakfast).prop('disabled', true).prop(
                                            'readonly', true);
                                        $('#has_lunch').prop('checked', data.has_lunch == 1)
                                            .prop('disabled', true);
                                        $('#lunch_amount').val(data.lunch_amount ?? courseLunch)
                                            .prop('disabled', true).prop('readonly', true);
                                        $('#has_dinner').prop('checked', data.has_dinner == 1)
                                            .prop('disabled', true);
                                        $('#dinner_amount').val(data.dinner_amount ??
                                            courseDinner).prop('disabled', true).prop(
                                            'readonly', true);


                                    } else {
                                        $('#food_from_date').prop('readonly', false);
                                        $('#food_to_date').prop('readonly', false);
                                        $('#has_breakfast').prop('disabled', false);
                                        $('#has_lunch').prop('disabled', false);
                                        $('#has_dinner').prop('disabled', false);
                                        $('#breakfast_amount').prop('readonly', false);
                                        $('#lunch_amount').prop('readonly', false);
                                        $('#dinner_amount').prop('readonly', false);
                                        effectivePrice = courseBreakfast + courseLunch +
                                            courseDinner;

                                        let intakeStart = selectedOption.data('intake-start');
                                        let intakeDuration = selectedOption.data(
                                            'intake-duration');

                                        if (intakeStart && intakeDuration) {
                                            let today = new Date();
                                            today.setHours(0, 0, 0, 0);
                                            let startDate = new Date(intakeStart);
                                            startDate.setHours(0, 0, 0, 0);

                                            let fromDate = today >= startDate ? today :
                                                startDate;

                                            console.log('ssssd', fromDate, startDate, today)

                                            let toDate = new Date(startDate);
                                            if (intakeDuration === '1 week') toDate.setDate(
                                                toDate.getDate() + 7);
                                            else if (intakeDuration === '2 weeks') toDate
                                                .setDate(toDate.getDate() + 14);
                                            else if (intakeDuration === '1 month') toDate
                                                .setMonth(toDate.getMonth() + 1);
                                            else if (intakeDuration === '2 month') toDate
                                                .setMonth(toDate.getMonth() + 2);

                                            const fmt = d => {
                                                const year = d.getFullYear();
                                                const month = String(d.getMonth() + 1)
                                                    .padStart(2, '0');
                                                const day = String(d.getDate()).padStart(2,
                                                    '0');
                                                return `${year}-${month}-${day}`;
                                            };
                                            console.log('2323', fromDate, fmt(fromDate))

                                            $('#food_from_date').val(fmt(fromDate));
                                            $('#food_to_date').val(fmt(toDate));

                                            if (toDate <= today) {
                                                $('#food_to_date').val(null);
                                                toastr.warning(
                                                    'Food To Date (' + fmt(toDate) +
                                                    ') is not match.',
                                                    'Date Notice', {
                                                        timeOut: 8000
                                                    }
                                                );
                                            }
                                        }

                                    }
                                }

                                addCourseRow(courseId, courseName, effectivePrice);
                                if (currentMode === 'food') {
                                    $('.allCourseDetails tr').data('has-existing-payment',
                                        foodHasExisting);
                                }
                                applyPriceLockState($('.allCourseDetails tr'));

                                $('.allCourseDetails tr[data-id="' + courseId + '"]')
                                    .find('.intake_name_cell').text(intakeName);

                                let historyTable = $('#paymentHistoryTable tbody');
                                historyTable.empty();
                                if (data.history.length > 0) {
                                    data.history.forEach(function(payment) {
                                        // Payment type drives the pill colour: course/nvq/food match the
                                        // same blue/purple/green used for the mode-toggle button above.
                                        let typeLabel = payment.payment_type ||
                                        'course';
                                        let typeClass = 'status-' + typeLabel.toString()
                                            .toLowerCase();
                                        let paidAmount = parseFloat(payment.amount) ||
                                        0;
                                        historyTable.append(
                                            '<tr>' +
                                            '<td>' + new Date(payment.created_at)
                                            .toLocaleDateString() + '</td>' +
                                            '<td class="fw-600">' + paidAmount
                                            .toFixed(2) +
                                            '</td>' +
                                            '<td>' + payment.payment_method +
                                            '</td>' +
                                            '<td><span class="status-badge ' +
                                            typeClass +
                                            '">' + typeLabel + '</span></td>' +
                                            '</tr>'
                                        );
                                    });
                                    $('#paymentHistoryCount').text(
                                        data.history.length + (data.history.length === 1 ?
                                            ' record' : ' records'));
                                } else {
                                    historyTable.append(
                                        '<tr><td colspan="4" class="text-center text-muted py-3">No payment history found.</td></tr>'
                                    );
                                    $('.allCourseDetails tr').addClass('first-payment');
                                    $('#paymentHistoryCount').text('0 records');
                                }

                                // ── FIX 3: Set due amount from history response ──
                                let dueAmount = 0;
                                if (currentMode === 'nvq') {
                                    dueAmount = parseFloat(data.nvq_due_amount) || 0;
                                } else if (currentMode === 'food') {
                                    dueAmount = parseFloat(data.food_due_amount) || 0;
                                } else {
                                    dueAmount = parseFloat(data.course_due_amount) || 0;
                                }

                                // Store the authoritative due from the server and display it
                                $('.allCourseDetails tr').find('.due_amount_display')
                                    .val(dueAmount.toFixed(2))
                                    .data('initial-due', dueAmount);
                                $('.allCourseDetails tr').find('.paid_amount_input').attr('max',
                                    dueAmount);
                                // ────────────────────────────────────────────────
                            }
                        }
                    });
                }

                $(this).val('').niceSelect('update');
            });

            // ================================================================
            // Add course row to table
            // ================================================================
            function addCourseRow(id, name, price) {
                let typeLabel = currentMode === 'nvq' ? 'NVQ Payment' :
                    currentMode === 'food' ? 'Food Payment' :
                    'Course Payment';

                let row = `<tr data-id="${id}">
                    <td class="sl">1</td>
                    <td class="course_name">${name}</td>
                    <td class="intake_name_cell">N/A</td>
                    <td class="course_price_cell">
                        <div class="input-group input-group-sm" style="max-width:120px;">
                            <input type="number" class="form-control course_price_input"
                                name="course_price[${id}]" value="${price}" step="0.01">
                            <div class="input-group-append"><span class="input-group-text">.00</span></div>
                        </div>
                    </td>
                    <td class="paid_amount_cell">
                        <div class="input-group input-group-sm" style="max-width:120px;">
                            <input type="number" class="form-control paid_amount_input"
                                name="paid_amount[${id}]" value="" step="0.01" max="${price}"
                                data-original-price="${price}">
                            <div class="input-group-append"><span class="input-group-text">.00</span></div>
                        </div>
                    </td>
                    <td class="due_amount_cell">
                        <div class="input-group input-group-sm" style="max-width:100px;">
                            <input type="number" class="form-control due_amount_display"
                                value="${price.toFixed(2)}" step="0.01" readonly>
                        </div>
                    </td>
                    <td class="course_name">
                        ${name}<br>
                        <small class="text-info">${typeLabel}</small>
                    </td>
                    <td>
                        <button type="button"
                            class="primary-btn small bg-danger text-white border-0 remove_course_btn">
                            Delete
                        </button>
                    </td>
                </tr>`;

                $('.allCourseDetails').append(row);
                $('.course_price_input').data('manually-edited', false);
                applyPriceLockState($('.allCourseDetails tr'));
                updatePaymentStatusAndDue();
            }


            function applyPriceLockState($row) {
                if (!$row || !$row.length) return;
                let hasExisting = $row.data('has-existing-payment');
                console.log('applyPriceLockState', currentMode, hasExisting);
                let editable = (currentMode === 'food' && !hasExisting);
                $row.find('.course_price_input').prop('readonly', !editable);
            }

            // ================================================================
            // Recalculate due + payment status
            // ================================================================
            function updatePaymentStatusAndDue() {
                let coursePrice = parseFloat($('.course_price_input').val()) || 0;
                let paidAmount = parseFloat($('.paid_amount_input').val()) || 0;
                let paymentMethod = $('#paymentMethodName').val();
                let bankPaidAmount = parseFloat($('#bank_paid_amount').val()) || 0;

                let initialDue = parseFloat($('.allCourseDetails tr').find('.due_amount_display').data(
                    'initial-due'));

                if (isNaN(initialDue)) initialDue = coursePrice;

                let newDue = initialDue - paidAmount;
                if (newDue < 0) newDue = 0;
                $('.allCourseDetails tr').find('.due_amount_display').val(newDue.toFixed(2));

                let totalPaidAfterThis = (coursePrice - initialDue) + paidAmount;

                if (paymentMethod === 'Bank Transfer') {
                    if (bankPaidAmount >= coursePrice && coursePrice > 0) $('#status').val('full').niceSelect(
                        'update');
                    else if (bankPaidAmount > 0 && bankPaidAmount < coursePrice) $('#status').val('partial')
                        .niceSelect('update');
                    else $('#status').val('not').niceSelect('update');
                } else {
                    if (totalPaidAfterThis >= coursePrice && coursePrice > 0) $('#status').val('full').niceSelect(
                        'update');
                    else if (totalPaidAfterThis > 0) $('#status').val('partial').niceSelect('update');
                    else $('#status').val('not').niceSelect('update');
                }
            }

            // mark price as manually edited when user types directly into it
            $(document).on('input', '.course_price_input', function() {
                $(this).data('manually-edited', true);

                let $row = $(this).closest('tr');
                let $dueDisplay = $row.find('.due_amount_display');
                let newPrice = parseFloat($(this).val()) || 0;
                let hasExistingPayment = $row.data('has-existing-payment');

                console.log('course_price_input changed', newPrice, hasExistingPayment);

                if (!hasExistingPayment) {
                    $dueDisplay.data('initial-due', newPrice);
                    $row.find('.paid_amount_input').attr('max', newPrice);
                }

                updatePaymentStatusAndDue();
            });

            $(document).on('input', '.course_price_input, .paid_amount_input', function() {
                updatePaymentStatusAndDue();
                validatePaidAmount();
            });

            function validatePaidAmount() {
                $('.paid_amount_input').each(function() {
                    let max = parseFloat($(this).attr('max')) || 0;
                    let paid = parseFloat($(this).val()) || 0;
                    if (paid > max) {
                        $(this).val(max.toFixed(2));
                        toastr.warning('Paid amount cannot exceed the due amount');
                        updatePaymentStatusAndDue();
                    }
                });
            }

            $(document).on('click', '.remove_course_btn', function() {
                $(this).closest('tr').remove();
                addedCourseId = null;
                updatePaymentStatusAndDue();
            });

            // ================================================================
            // Payment method toggle
            // ================================================================
            function toggleBankPayment(method) {
                const bankSection = $('#bankPaymentSection');
                const amountRow = $('#bankPaidAmountRow');
                const dateRow = $('#bankPaidDateRow');
                const uploadRow = $('#uploadDocumentRow');

                if (method === 'Bank Transfer') {
                    bankSection.removeClass('d-none');
                    amountRow.removeClass('d-none');
                    dateRow.removeClass('d-none');
                    uploadRow.removeClass('d-none');
                    $('#status').val('full').niceSelect('update');
                } else if (method === 'Invoice') {
                    bankSection.removeClass('d-none');
                    amountRow.addClass('d-none');
                    dateRow.addClass('d-none');
                    uploadRow.removeClass('d-none');
                    $('.paid_amount_input').prop('disabled', false);
                    updatePaymentStatusAndDue();
                } else {
                    bankSection.addClass('d-none');
                    $('.paid_amount_input').prop('disabled', false);
                    updatePaymentStatusAndDue();
                }
            }

            $('#paymentMethodName').on('change', function() {
                toggleBankPayment($(this).val());
            });

            // ================================================================
            // FOOD: meal checkboxes + recalc
            // ================================================================
            $('.meal-checkbox').on('change', function() {
                recalcFoodTotal();
            });

            $('#food_from_date, #food_to_date, #breakfast_amount, #lunch_amount, #dinner_amount')
                .on('change input', function() {
                    recalcFoodTotal();
                });



            function recalcFoodTotal() {
                let breakfast = $('#has_breakfast').is(':checked') ? (parseFloat($('#breakfast_amount').val()) ||
                    0) : 0;
                let lunch = $('#has_lunch').is(':checked') ? (parseFloat($('#lunch_amount').val()) || 0) : 0;
                let dinner = $('#has_dinner').is(':checked') ? (parseFloat($('#dinner_amount').val()) || 0) : 0;
                let total = breakfast + lunch + dinner;

                $('#foodTotalDisplay').val(total > 0 ? total.toFixed(2) : '0.00');

                let $row = $('.allCourseDetails tr');
                let hasExisting = $row.data('has-existing-payment');
                let manuallyEdited = $('.course_price_input').data('manually-edited');

                if (!hasExisting && !manuallyEdited) {
                    $('.course_price_input').val(total.toFixed(2));
                    $('.paid_amount_input').attr('max', total);
                    $('.due_amount_display').val(total.toFixed(2)).data('initial-due', total);
                }
                // if hasExisting OR manuallyEdited, leave price/due alone

                updatePaymentStatusAndDue();
            }

            // ================================================================
            // File upload
            // ================================================================
            $('#uploadBtn').on('click', function() {
                $('#payment_file').click();
            });
            $('#payment_file').on('change', function() {
                $('#fileName').text(this.files[0] ? this.files[0].name : 'No file chosen');
            });

            // ================================================================
            // FORM SUBMIT
            // ================================================================
            $('#invoiceForm').on('submit', function(e) {
                e.preventDefault();

                // ── FIX 1: Disable button immediately to prevent double submit ──
                let $submitBtn = $('#submit');
                $submitBtn.prop('disabled', true).text('Saving...');
                // ───────────────────────────────────────────────────────────────

                let branch = $('#branch_id').val();
                let student = $('#student_id').val();
                let createDate = $('#create_date').val();
                let dueDate = $('#due_date').val();
                let status = $('#status').val();
                let paymentMethod = $('#paymentMethodName').val();
                let notes = $('#modal_notes').val();

                const courseRow = $('.allCourseDetails tr');
                if (courseRow.length === 0) {
                    toastr.error('Please select a course');
                    $submitBtn.prop('disabled', false).text('{{ __('common.save') }}');
                    return;
                }

                let courseId = courseRow.data('id');
                let coursePrice = parseFloat(courseRow.find('.course_price_input').val());
                let paidAmount = parseFloat(courseRow.find('.paid_amount_input').val());

                // Validation — re-enable button on any early return
                const fail = (msg) => {
                    toastr.error(msg);
                    $submitBtn.prop('disabled', false).text('{{ __('common.save') }}');
                };

                if (!courseId) return fail('Course is required');
                if (isNaN(coursePrice) || coursePrice <= 0) return fail(
                    'Course price must be a valid number greater than 0');
                if (isNaN(paidAmount) || paidAmount <= 0) return fail(
                    'Paid amount must be a valid number greater than 0');
                if (paymentMethod !== 'Bank Transfer' && paidAmount > coursePrice) return fail(
                    'Paid amount cannot exceed the course price');
                if (!branch) return fail('Branch is required');
                if (!student) return fail('Student is required');
                if (!createDate) return fail('Create date is required');
                if (!dueDate) return fail('Due date is required');
                if (!status) return fail('Payment status is required');
                if (!paymentMethod) return fail('Payment method is required');

                if (currentMode === 'food') {
                    if (!$('#food_from_date').val()) return fail('Food from date is required');
                    if (!$('#food_to_date').val()) return fail('Food to date is required');
                    if (!$('#has_breakfast').is(':checked') &&
                        !$('#has_lunch').is(':checked') &&
                        !$('#has_dinner').is(':checked')) return fail('Please select at least one meal');
                }

                if (paymentMethod === 'Bank Transfer') {
                    let bankPaidAmount = parseFloat($('#bank_paid_amount').val());
                    let bankPaidDate = $('#bank_paid_date').val();
                    if (isNaN(bankPaidAmount) || bankPaidAmount <= 0) return fail(
                        'Bank Paid Amount is required and must be greater than 0');
                    if (bankPaidAmount < paidAmount) return fail('Bank Paid Amount must be >= Paid Amount');
                    if (!bankPaidDate) return fail('Bank Paid Date is required');
                    const today = new Date().toISOString().split('T')[0];

                    if (bankPaidDate > today) {
                        return fail('Bank Paid Date cannot be greater than today');
                    }

                }

                // Build FormData
                let formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('branch_id', $('#branch_id').val());
                formData.append('student_id', $('#student_id').val());
                formData.append('create_date', $('#create_date').val());
                formData.append('due_date', $('#due_date').val());
                formData.append('payment_status', $('#status').val());
                formData.append('payment_method', $('#paymentMethodName').val());
                formData.append('notes', notes);
                formData.append('course_id', courseId);
                formData.append('course_price', coursePrice);
                formData.append('paid_amount', paidAmount);

                if (currentMode === 'food') {
                    formData.append('food_from_date', $('#food_from_date').val());
                    formData.append('food_to_date', $('#food_to_date').val());
                    formData.append('has_breakfast', $('#has_breakfast').is(':checked') ? 1 : 0);
                    formData.append('has_lunch', $('#has_lunch').is(':checked') ? 1 : 0);
                    formData.append('has_dinner', $('#has_dinner').is(':checked') ? 1 : 0);
                    formData.append('payment_type', 'food');
                } else {
                    formData.append('payment_type', currentMode === 'nvq' ? 'nvq' : 'course');
                }

                if (paymentMethod === 'Bank Transfer') {
                    formData.append('bank_paid_amount', parseFloat($('#bank_paid_amount').val()));
                    formData.append('bank_paid_date', $('#bank_paid_date').val());
                }

                if ($('#payment_file')[0].files.length > 0) {
                    formData.append('payment_file', $('#payment_file')[0].files[0]);
                }

                let actionUrl = $('#formActionUrl').val();
                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            if (currentMode === 'food' && response.lunchbox_number) {
                                toastr.success('Lunchbox number assigned: ' + response
                                    .lunchbox_number, null, {
                                        timeOut: 8000
                                    });
                            }
                            const reprintUrl =
                                `/student/fees/invoice-item-print/${response.invoice_id}/${response.payment_id}`;
                            window.open(reprintUrl, '_blank');
                            const message = encodeURIComponent('successfully!');
                            window.location.href = '{{ route('fees.course-payment') }}' +
                                '?message-success=' + message;
                        }
                        // Note: button stays disabled on success because page redirects anyway
                    },
                    error: function(xhr) {
                        // ── FIX 1: Re-enable on error so user can retry ──
                        $submitBtn.prop('disabled', false).text('{{ __('common.save') }}');
                        // ─────────────────────────────────────────────────

                        let msg = 'An unexpected error occurred';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.values(xhr.responseJSON.errors).forEach(messages => {
                                messages.forEach(message => toastr.error(message));
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

        }); // end ready
    </script>
@endpush
