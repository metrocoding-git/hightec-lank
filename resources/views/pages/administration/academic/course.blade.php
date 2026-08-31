@extends('layouts.app')
@push('css')
    <style>
        #table_id thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

        .student_rec_card {
            border-radius: 6px;
            border: 1px solid var(--border_color);
            width: 100%;
        }

        .student_rec_header {
            padding: 12px;
            background: -webkit-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -moz-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -o-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -ms-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
        }

        .student_rec_footer {
            padding: 12px;
            margin-top: 16px;
            border-top: 1px solid var(--border_color);
        }

        .student_rec_content {
            padding: 16px;
            max-height: 300px;
            min-height: 300px;
        }

        /* Keeps horizontal scroll on the wide courses table from also
                   dragging the left-side Add/Edit form sideways on large screens. */
        .course-table-scroll {
            overflow-x: auto;
            max-width: 100%;
        }

        /* ---- Modern refresh: cards, table, status badges (Section 20) ---- */
        .white-box {
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
            transition: box-shadow .2s ease;
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
        select.primary_select,
        textarea.form-control {
            border-radius: 8px !important;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .primary_input_field:focus,
        select.primary_select:focus,
        textarea.form-control:focus {
            box-shadow: 0 0 0 3px rgba(var(--gradient_1-rgb, 90, 103, 216), 0.12);
        }

        .course-table-scroll table.Crm_table_active3 thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

        .course-table-scroll table.Crm_table_active3 tbody tr {
            transition: background-color .12s ease;
        }

        .course-table-scroll table.Crm_table_active3 tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        .course-table-scroll table.Crm_table_active3 tbody td {
            vertical-align: middle;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .3px;
            white-space: nowrap;
        }

        .status-badge.status-active {
            background: #e6f7ec;
            color: #1e8e4f;
        }

        .status-badge.status-inactive {
            background: #fdecec;
            color: #d64545;
        }

        .dropdown-item.text-danger:hover {
            background-color: #fdecec;
        }
    </style>
@endpush

@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Courses</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Courses</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            @php
                // Button-level permissions (Section 21) — computed once, reused for
                // both the Add/Edit form on the left and every row's dropdown below.
$canAddCourse = Auth::user()->hasPermission('course.store');
$canEditCourse = Auth::user()->hasPermission('course.update');
$canDeleteCourse = Auth::user()->hasPermission('course.destroy');
                $showCourseForm = isset($courseId) ? $canEditCourse : $canAddCourse;
            @endphp

            <div class="row">
                @if ($showCourseForm)
                    <div class="col-lg-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <form
                                    action="{{ isset($courseId) ? route('course.update', $courseId) : route('course.store') }}"
                                    method="POST" enctype="multipart/form-data" class="form-horizontal">
                                    @csrf
                                    <input type="hidden" name="dt_page" class="dt-page-field"
                                        value="{{ (int) request('dt_page', 0) }}">
                                    <div class="white-box">
                                        <div class="main-title">
                                            <h3 class="mb-20">{{ isset($courseId) ? 'Edit Course' : 'Add Course' }}</h3>
                                        </div>

                                        <div class="add-visitor">
                                            {{-- Course Name --}}
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="primary_input">
                                                        <label class="primary_input_label" for="name">Course Name
                                                            <span>*</span></label>
                                                        <input
                                                            class="primary_input_field form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                                            type="text" name="name" id="name"
                                                            value="{{ old('name', $course->name ?? '') }}"
                                                            autocomplete="off">
                                                        @if ($errors->has('name'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('name') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Course Code --}}
                                            <div class="row mt-20" hidden>
                                                <div class="col-lg-12">
                                                    <div class="primary_input">
                                                        <label class="primary_input_label" for="code">Course Code
                                                            <span>*</span></label>
                                                        <input
                                                            class="primary_input_field form-control{{ $errors->has('code') ? ' is-invalid' : '' }}"
                                                            type="text" name="code" id="code"
                                                            value="{{ old('code', $course->code ?? '') }}"
                                                            autocomplete="off">
                                                        @if ($errors->has('code'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('code') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Course Price --}}
                                            <div class="row mt-20">
                                                <div class="col-lg-12">
                                                    <div class="primary_input">
                                                        <label class="primary_input_label" for="price">Course Price
                                                            <span>*</span></label>
                                                        <input
                                                            class="primary_input_field form-control{{ $errors->has('price') ? ' is-invalid' : '' }}"
                                                            type="number" name="price" id="price"
                                                            value="{{ old('price', $course->price ?? '') }}"
                                                            placeholder="0.00" autocomplete="off">
                                                        @if ($errors->has('price'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('price') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-20">
                                                <div class="col-lg-12">
                                                    <div class="primary_input">
                                                        <label class="primary_input_label" for="price">Course Expiration
                                                            dates
                                                            <span>*</span></label>
                                                        <input
                                                            class="primary_input_field form-control{{ $errors->has('expiration_date') ? ' is-invalid' : '' }}"
                                                            type="number" name="expiration_date" id="expiration_date"
                                                            value="{{ old('expiration_date', $course->expiration_date ?? '') }}"
                                                            autocomplete="off">
                                                        @if ($errors->has('expiration_date'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('expiration_date') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Course Duration --}}
                                            <div class="row mt-20">
                                                <div class="col-lg-12">
                                                    <div class="primary_input">
                                                        <label class="primary_input_label" for="duration">Course
                                                            Duration</label>
                                                        <select name="duration" id="duration"
                                                            class="primary_select form-control{{ $errors->has('duration') ? ' is-invalid' : '' }}">
                                                            <option value="">Select Duration</option>
                                                            <option value="2 weeks"
                                                                {{ old('duration', $course->duration ?? '') == '2 weeks' ? 'selected' : '' }}>
                                                                2 Weeks</option>
                                                            <option value="1 month"
                                                                {{ old('duration', $course->duration ?? '') == '1 month' ? 'selected' : '' }}>
                                                                1 Month</option>
                                                            <option value="2 months"
                                                                {{ old('duration', $course->duration ?? '') == '2 months' ? 'selected' : '' }}>
                                                                2 Months</option>
                                                        </select>
                                                        @if ($errors->has('duration'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('duration') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- NVQ Payment Available --}}
                                            <div class="row mt-20">
                                                <div class="col-lg-12">
                                                    <div class="primary_input">
                                                        <label class="primary_input_label">NVQ Payment Available?</label>
                                                        <select name="has_nvq_payment" id="has_nvq_payment"
                                                            class="primary_select form-control">
                                                            <option value="0">No</option>
                                                            <option value="1"
                                                                {{ old('has_nvq_payment', $course->has_nvq_payment ?? '') == 1 ? 'selected' : '' }}>
                                                                Yes
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- NVQ Amount --}}
                                            <div class="row mt-20" id="nvq_amount_box" style="display:none;">
                                                <div class="col-lg-12">
                                                    <div class="primary_input">
                                                        <label class="primary_input_label">NVQ Amount</label>
                                                        <input type="number" step="0.01" name="nvq_amount"
                                                            class="primary_input_field form-control" placeholder="0.00"
                                                            value="{{ old('nvq_amount', $course->nvq_amount ?? '') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Food Fee Toggle --}}
                                            <div class="row mt-20">
                                                <div class="col-lg-12">
                                                    <div class="primary_input">
                                                        <label class="primary_input_label">Has Food Fee?</label>
                                                        <select name="has_food_fee" id="has_food_fee"
                                                            class="primary_select form-control">
                                                            <option value="0">No</option>
                                                            <option value="1"
                                                                {{ old('has_food_fee', $course->has_food_fee ?? 0) == 1 ? 'selected' : '' }}>
                                                                Yes
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Meal Amounts --}}
                                            <div id="food_fee_box" style="display:none;">

                                                {{-- Breakfast --}}
                                                <div class="row mt-20">
                                                    <div class="col-lg-12">
                                                        <div class="primary_input">
                                                            <label class="primary_input_label">Breakfast Amount</label>
                                                            <input type="number" step="0.01" name="breakfast_amount"
                                                                id="breakfast_amount"
                                                                class="primary_input_field form-control meal-amount"
                                                                placeholder="0.00"
                                                                value="{{ old('breakfast_amount', $course->breakfast_amount ?? '') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Lunch --}}
                                                <div class="row mt-20">
                                                    <div class="col-lg-12">
                                                        <div class="primary_input">
                                                            <label class="primary_input_label">Lunch Amount</label>
                                                            <input type="number" step="0.01" name="lunch_amount"
                                                                id="lunch_amount"
                                                                class="primary_input_field form-control meal-amount"
                                                                placeholder="0.00"
                                                                value="{{ old('lunch_amount', $course->lunch_amount ?? '') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Dinner --}}
                                                <div class="row mt-20">
                                                    <div class="col-lg-12">
                                                        <div class="primary_input">
                                                            <label class="primary_input_label">Dinner Amount</label>
                                                            <input type="number" step="0.01" name="dinner_amount"
                                                                id="dinner_amount"
                                                                class="primary_input_field form-control meal-amount"
                                                                placeholder="0.00"
                                                                value="{{ old('dinner_amount', $course->dinner_amount ?? '') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Auto-calculated Total --}}
                                                <div class="row mt-20">
                                                    <div class="col-lg-12">
                                                        <div class="primary_input">
                                                            <label class="primary_input_label">Total Food Fee </label>
                                                            <input type="text" id="food_total"
                                                                class="primary_input_field form-control"
                                                                placeholder="0.00" readonly
                                                                style="background:#f5f5f5; font-weight:bold;">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            {{-- Course Description --}}
                                            <div class="row mt-20">
                                                <div class="col-lg-12">
                                                    <div class="primary_input">
                                                        <label class="primary_input_label" for="description">Course
                                                            Description</label>
                                                        <textarea class="form-control" name="description" id="description">{{ old('description', $course->description ?? '') }}</textarea>
                                                        @if ($errors->has('description'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('description') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Submit Button --}}
                                            <div class="row mt-20">
                                                <div class="col-lg-12 text-center">
                                                    <button class="primary-btn fix-gr-bg col-12 submit" type="submit">
                                                        <span class="ti-check"></span>
                                                        {{ isset($courseId) ? __('Update Course') : __('Save Course') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-lg-{{ $showCourseForm ? '9' : '12' }}">

                    <div class="row white-box">
                        <div class="col-lg-12">

                            <div class="main-title">
                                <h3 class="mb-20">Courses List</h3>
                            </div>

                            <div class="course-table-scroll">
                                <x-table>
                                    <table id="table_id" class="table Crm_table_active3" cellspacing="0"
                                        width="100%">

                                        <thead>

                                            <tr>
                                                <th> @lang('common.sl')</th>
                                                <th> Courses </th>
                                                {{-- <th>Courses Code</th> --}}
                                                <th>Courses Price</th>
                                                <th>Courses durations</th>
                                                <th>Start Date</th>
                                                <th>Valid Dates</th>
                                                <th>NVQ Payment</th>
                                                <th>NVQ Amount</th>
                                                <th>Food Fee</th>
                                                <th>Breakfast</th>
                                                <th>Lunch</th>
                                                <th>Dinner</th>
                                                <th>Courses description</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($courses as $key => $course)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $course->name }}</td>
                                                    {{-- <td><a href="#">{{ $course->code }}</a></td> --}}
                                                    <td>Rs.{{ number_format($course->price, 2) }}</td>
                                                    <td>{{ $course->duration ?? '-' }}</td>
                                                    <td>{{ $course->created_at ? $course->created_at->format('Y-m-d') : '-' }}
                                                    </td>
                                                    <td>{{ $course->expiration_date ?? '-' }}</td>
                                                    <td>
                                                        @if ($course->has_nvq_payment)
                                                            Yes
                                                        @else
                                                            No
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $course->nvq_amount ? 'Rs.' . number_format($course->nvq_amount, 2) : '-' }}
                                                    </td>
                                                    <td>{{ $course->has_food_fee ? 'Yes' : 'No' }}</td>
                                                    <td>{{ $course->breakfast_amount ? 'Rs.' . number_format($course->breakfast_amount, 2) : '-' }}
                                                    </td>
                                                    <td>{{ $course->lunch_amount ? 'Rs.' . number_format($course->lunch_amount, 2) : '-' }}
                                                    </td>
                                                    <td>{{ $course->dinner_amount ? 'Rs.' . number_format($course->dinner_amount, 2) : '-' }}
                                                    </td>


                                                    <td>{{ $course->description ?? '-' }}</td>

                                                    <td valign="top">
                                                        @php
                                                            $routeList = [];
                                                            if ($canEditCourse) {
                                                                $routeList[] =
                                                                    '<a class="dropdown-item course-edit-link" href="' .
                                                                    route('course.edit', $course->id) .
                                                                    '">' .
                                                                    __('common.edit') .
                                                                    '</a>';
                                                            }
                                                            if ($canDeleteCourse) {
                                                                $routeList[] =
                                                                    '<a class="dropdown-item text-danger" data-toggle="modal" data-target="#deleteModal' .
                                                                    $course->id .
                                                                    '" href="#">Delete</a>';
                                                            }
                                                        @endphp
                                                        @if (count($routeList) > 0)
                                                            <x-drop-down-action-component :routeList="$routeList" />
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                </tr>

                                                @if ($canDeleteCourse)
                                                    <div class="modal fade admin-query"
                                                        id="deleteModal{{ $course->id }}">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Delete Course</h4>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <h4>
                                                                        Are you sure you want to delete
                                                                        "{{ $course->name }}"? This cannot be undone.
                                                                    </h4>
                                                                    <div class="mt-40 d-flex justify-content-between">
                                                                        <button type="button" class="primary-btn tr-bg"
                                                                            data-dismiss="modal">@lang('common.cancel')</button>
                                                                        <form
                                                                            action="{{ route('course.destroy', $course->id) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            @method('DELETE')

                                                                            <input type="hidden" name="dt_page"
                                                                                class="dt-page-field" value="0">
                                                                            <button class="primary-btn fix-gr-bg"
                                                                                type="submit">
                                                                                Delete
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </tbody>

                                    </table>
                                </x-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@include('components.partials.data_table_css')
@include('components.partials.data_table_js')


@push('script')
    <script>
        var table = $('#table_id').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            stateSave: true,

            buttons: [{
                    extend: 'copyHtml5',
                    text: '<i class="fa-regular fa-copy"></i>',
                    titleAttr: 'Copy'
                },
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa-solid fa-file-csv"></i>',
                    titleAttr: 'CSV'
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel"></i>',
                    titleAttr: 'Excel'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa-solid fa-file-pdf"></i>',
                    titleAttr: 'PDF'
                },
                {
                    extend: 'print',
                    text: '<i class="fa-solid fa-print"></i>',
                    titleAttr: 'Print'
                }
            ],

            paging: true,
            searching: true,
            ordering: true,
            info: true,

            // 👇 This part adds the search and pagination icons
            language: {
                search: '<i class="fa-solid fa-magnifying-glass me-2"></i>',
                searchPlaceholder: 'Search...',
                paginate: {
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            }
        });

        // Land on the right page if the URL says so (see the big comment above).
        var initialDtPage = parseInt('{{ (int) request('dt_page', 0) }}', 10);
        if (initialDtPage > 0) {
            table.page(initialDtPage).draw(false);
        }

        // Intercept Edit clicks purely to stamp the current page onto the URL.
        $(document).on('click', '.course-edit-link', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            var sep = href.indexOf('?') === -1 ? '?' : '&';
            window.location.href = href + sep + 'dt_page=' + table.page();
        });


        $(document).on('submit', 'form', function() {
            $(this).find('.dt-page-field').val(table.page());
        });

        $(document).ready(function() {

            function toggleNVQ() {
                let nvq = $('#has_nvq_payment').val();
                if (nvq == 1) {
                    $('#nvq_amount_box').show();
                } else {
                    $('#nvq_amount_box').hide();
                }
            }

            toggleNVQ();

            $('#has_nvq_payment').on('change', function() {
                toggleNVQ();
            });


            function toggleFoodFee() {
                if ($('#has_food_fee').val() == 1) {
                    $('#food_fee_box').show();
                } else {
                    $('#food_fee_box').hide();
                }
            }

            toggleFoodFee();
            $('#has_food_fee').on('change', toggleFoodFee);

            // Auto-calculate total food fee
            function calcFoodTotal() {
                var breakfast = parseFloat($('#breakfast_amount').val()) || 0;
                var lunch = parseFloat($('#lunch_amount').val()) || 0;
                var dinner = parseFloat($('#dinner_amount').val()) || 0;
                var total = breakfast + lunch + dinner;

                $('#food_total').val(total > 0 ? 'Rs. ' + total.toFixed(2) : '');
            }

            $(document).on('input', '.meal-amount', calcFoodTotal);

            // Run on page load for edit mode
            calcFoodTotal();

        });
    </script>
@endpush
