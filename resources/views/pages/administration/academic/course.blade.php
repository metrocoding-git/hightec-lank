@extends('layouts.app')
@push('css')
    <style>
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
                {{-- {{Auth::user()->name}} {{Auth::user()->last_name}} role->id {{ Auth::user()->role_id}} --}}

                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('academics.academics')</a>
                    <a href="#">Courses</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <div class="row">
                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">

                            {{-- @if (isset($sectionId))
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'global_class_update', 'method' => 'POST']) }}
                            @else
                                @if (userPermission(266))
                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'global_class_store', 'method' => 'POST']) }}
                                @endif
                            @endif --}}
                            <form
                                action="{{ isset($courseId) ? route('course.update', $courseId) : route('course.store') }}"
                                method="POST" enctype="multipart/form-data" class="form-horizontal">
                                @csrf
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
                                                        value="{{ old('name', $course->name ?? '') }}" autocomplete="off">
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
                                                        value="{{ old('code', $course->code ?? '') }}"  autocomplete="off">
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
                                                        value="{{ old('price', $course->price ?? '') }}" placeholder="0.00"
                                                        autocomplete="off">
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
                                                        class="primary_input_field form-control"
                                                        placeholder="0.00"
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
                                                            class="primary_input_field form-control" placeholder="0.00"
                                                            readonly style="background:#f5f5f5; font-weight:bold;">
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
                                                <button class="primary-btn fix-gr-bg submit" type="submit">
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

                <div class="col-lg-9">

                    <div class="row white-box">
                        <div class="col-lg-12">

                            <div class="main-title">
                                <h3 class="mb-20">Courses List</h3>
                            </div>

                            <x-table>
                                <table id="table_id" class="table Crm_table_active3" cellspacing="0" width="100%">

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
                                                        $routeList = [
                                                            '<a class="dropdown-item" href="' .
                                                            route('course.edit', $course->id) .
                                                            '">' .
                                                            __('common.edit') .
                                                            '</a>',
                                                            '<a class="dropdown-item" data-toggle="modal" data-target="#deleteClassModal' .
                                                            $course->id .
                                                            '" href="#">' .
                                                            __('common.delete') .
                                                            '</a>',
                                                        ];
                                                    @endphp
                                                    <x-drop-down-action-component :routeList="$routeList" />
                                                </td>
                                            </tr>

                                            {{-- Delete Modal --}}
                                            <div class="modal fade admin-query" id="deleteClassModal{{ $course->id }}">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">@lang('academics.delete_class')</h4>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                            <div class="mt-40 d-flex justify-content-between">
                                                                <button type="button" class="primary-btn tr-bg"
                                                                    data-dismiss="modal">@lang('common.cancel')</button>
                                                                <form action="{{ route('course.destroy', $course->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="primary-btn fix-gr-bg"
                                                                        type="submit">@lang('common.delete')</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>

                                </table>
                            </x-table>
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
        $('#table_id').DataTable({
            responsive: true,
            dom: 'Bfrtip',

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
