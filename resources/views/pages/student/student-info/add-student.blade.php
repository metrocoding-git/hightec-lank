@extends('layouts.app')
@section('title')
    @lang('student.student_category')
@endsection
@section('mainContent')
    @php
        $branchCount = isset($branches) ? count($branches) : 0;
        $singleBranchId = $branchCount === 1 && isset($branches) ? $branches->first()->id : null;
    @endphp
    <style>
        #video,
        #canvas {
            border: 1px solid #ccc;
            background-color: #eee;
            margin-top: 10px;
        }

        #imagePreview {
            border: 1px solid #ccc;
            background-color: #eee;
            margin-top: 10px;
        }
    </style>
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('student.student_admission')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('student.student_information')</a>
                    <a href="#">@lang('student.student_admission')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <form method="POST" action="{{ route('add-student.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-12">

                        <div class="white-box">
                            <div class="row align-items-center">
                                <div class="col-lg-6 col-sm-6 col-5">
                                    <div class="main-title my-0 xs_mt_0 mt_0_sm">
                                        <h3 class="mb-15">@lang('student.add_student')</h3>
                                    </div>
                                </div>


                                <div class=" col-lg-6 col-sm-6 col-7">
                                    <div class="d-flex align-items-center justify-content-end gap-3">

                                        <a href="#" class="primary-btn small fix-gr-bg mr-3" data-bs-toggle="modal"
                                            data-bs-target="#importStudentModal">
                                            <span class="ti-plus pr-2"></span>
                                            @lang('student.import_student')
                                        </a>

                                        <div class="form-group d-flex align-items-center gap-2 mb-0">
                                            <label class="form-label fw-bold mb-0 pr-2">
                                                Existing Student?
                                            </label>

                                            <label class="toggle-btn mb-0">
                                                <input type="checkbox" id="isExistingStudent" name="is_existing_student"
                                                    value="1"
                                                    {{ old('is_existing_student', $student->is_old_stu ?? 0) == 1 ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                        </div>



                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-lg-12 student-add-form">
                                    <ul class="nav nav-tabs tabs_scroll_nav px-0 no-scroll" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#personal_info" role="tab"
                                                data-toggle="tab">@lang('student.personal_info')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#parents_and_guardian_info" role="tab"
                                                data-toggle="tab">@lang('student.parents_and_guardian_info')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#document_info" role="tab"
                                                data-toggle="tab">@lang('student.document_info')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#previous_school_info" role="tab"
                                                data-toggle="tab">Previous Institute Information</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#Other_info" role="tab"
                                                data-toggle="tab">@lang('student.Other_info')</a>
                                        </li>

                                        <li class="nav-item flex-grow-1 text-right">
                                            {{-- <div class="row">
                                <div class="col-lg-12 text-center"> --}}

                                            {{-- <div class="">
                                            @php
                                                $tooltip = '';
                                                if (userPermission('student_store')) {
                                                    $tooltip = '';
                                                } else {
                                                    $tooltip = 'You have no permission to add';
                                                }
                                            @endphp
                                        </div> --}}

                                            <button class="primary-btn fix-gr-bg submit" id="_submit_btn_admission"
                                                data-toggle="tooltip">
                                                <span class="ti-check"></span>
                                                @lang('student.save_student')
                                            </button>
                                            {{-- </div>
                            </div> --}}
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-lg-12">
                                    <div class="student-add-form-container">
                                        <div class="tab-content">
                                            {{-- <div class="row">
                                                <div class="col-lg-12 text-center">
                                                   

                                                </div>
                                            </div> --}}

                                            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

                                            <div role="tabpanel" class="tab-pane fade show active" id="personal_info">
                                                <div class="row pt-4 row-gap-12">

                                                    {{-- ====================acadamic info========== --}}
                                                    <input type="hidden" name="admission_number" id="hiddenAdmissionNumber"
                                                        value="{{ old('admission_number') }}">

                                                    <div class="col-lg-6">
                                                        <div class="form-section">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="main-title">
                                                                        <h4 class="stu-sub-head">@lang('student.academic_info')</h4>
                                                                    </div>
                                                                </div>





                                                                <div class="col-lg-12 mt-4">
                                                                    <div class="primary_input">
                                                                        <label class="primary_input_label" for="branch">
                                                                            @lang('common.branch') <span
                                                                                class="text-danger">*</span>
                                                                        </label>
                                                                        <select
                                                                            class="primary_select form-control{{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                                                                            name="branch_id" id="branch_id">
                                                                            <option value="">Select Branch</option>
                                                                            @foreach ($branches as $branch)
                                                                                <option value="{{ $branch->id }}"
                                                                                    @if (old('branch_id', isset($student) ? $student->branch_id : '') == $branch->id) selected @endif>
                                                                                    {{ $branch->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>

                                                                        @error('branch_id')
                                                                            <span class="text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>


                                                                {{-- <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input " id="class-div">
                                                                        <label class="primary_input_label" for="">
                                                                            @lang('common.class')
                                                                            <span class="text-danger">
                                                                                {{ is_required('class') == true ? '*' : '' }}</span>
                                                                        </label>
                                                                        <select
                                                                            class="primary_select form-control{{ $errors->has('class') ? ' is-invalid' : '' }}"
                                                                            name="class" id="classSelectStudent">
                                                                            <option
                                                                                data-display="@lang('common.class') @if (is_required('class') == true) * @endif"
                                                                                value="">@lang('common.class')
                                                                                @if (is_required('class') == true)
                                                                                    *
                                                                                @endif
                                                                            </option>
                                                                            @foreach ($classes as $class)
                                                                                <option value="{{ $class->id }}"
                                                                                    {{ old('class') == $class->id ? 'selected' : '' }}>
                                                                                    {{ $class->class_name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <div class="pull-right loader loader_style"
                                                                            id="select_class_loader">
                                                                            <img class="loader_img_style"
                                                                                src="{{ asset('public/backEnd/img/demo_wait.gif') }}"
                                                                                alt="loader">
                                                                        </div>

                                                                        @if ($errors->has('class'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('class') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div> --}}



                                                                {{-- <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.admission_number')

                                                                            <span class="text-danger"> *</span>

                                                                        </label>
                                                                        <input
                                                                            class="primary_input_field  form-control{{ $errors->has('admission_number') ? ' is-invalid' : '' }}"
                                                                            type="text" onkeyup="GetAdmin(this.value)"
                                                                            name="admission_number"
                                                                            value="{{ $nextAdmissionNumber ?? 'stu-001' }}"
                                                                            readonly>


                                                                        @if ($errors->has('admission_number'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('admission_number') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div> --}}



                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input mb-15">
                                                                        <label class="primary_input_label"
                                                                            for="admission_date">@lang('student.admission_date')

                                                                            <span class="text-danger"> *</span>

                                                                        </label>
                                                                        <div class="">
                                                                            <div class="no-gutters input-right-icon">
                                                                                <div class="col">
                                                                                    <div class="">
                                                                                        <input
                                                                                            class="primary_input_field  form-control"
                                                                                            id="admission_date"
                                                                                            type="date"
                                                                                            name="admission_date"
                                                                                            value="{{ old('admission_date') ?: \Carbon\Carbon::today()->format('Y-m-d') }}"
                                                                                            autocomplete="off" readonly>
                                                                                    </div>
                                                                                </div>
                                                                                <button class="btn-date"
                                                                                    {{-- style="top: 55% !important;" --}}
                                                                                    data-id="#admission_date"
                                                                                    type="button">
                                                                                    <label class="m-0 p-0"
                                                                                        for="admission_date">
                                                                                        {{-- <i class="ti-calendar"
                                                                                            id="start-date-icon"></i> --}}
                                                                                    </label>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        <span
                                                                            class="text-danger">{{ $errors->first('admission_date') }}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input">
                                                                        <div class="primary_input">
                                                                            <label class="primary_input_label"
                                                                                for="student_category_id">
                                                                                @lang('student.category') <span
                                                                                    class="text-danger"> *</span>
                                                                            </label>
                                                                            <select
                                                                                class="primary_select form-control{{ $errors->has('student_category_id') ? ' is-invalid' : '' }}"
                                                                                name="student_category_id"
                                                                                id="student_category_id">
                                                                                <option value="">Select Student
                                                                                    Category</option>
                                                                                @foreach ($categories as $category)
                                                                                    <option value="{{ $category->id }}"
                                                                                        @if (old('student_category_id', isset($student) ? $student->student_category_id : '') == $category->id) selected @endif>
                                                                                        {{ $category->category_name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>

                                                                            @if ($errors->has('student_category_id'))
                                                                                <span class="text-danger">
                                                                                    {{ $errors->first('student_category_id') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>



                                                            </div>
                                                            {{-- ===================contact info ============================ --}}
                                                            {{-- <div class="">
                                                                <div class="form-section"> --}}
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="main-title">
                                                                        <h4 class="stu-sub-head">@lang('student.contact_info')
                                                                        </h4>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('common.email_address')

                                                                            <span class="text-danger"> </span>

                                                                        </label>
                                                                        <input oninput="emailCheck(this)"
                                                                            class="primary_input_field email_address form-control{{ $errors->has('email_address') ? ' is-invalid' : '' }}"
                                                                            id="email_address" type="text"
                                                                            name="email_address"
                                                                            value="{{ old('email_address') }}">


                                                                        @if ($errors->has('email_address'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('email_address') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input">
                                                                        <label class="primary_input_label"
                                                                            for="district_id">
                                                                            District
                                                                        </label>
                                                                        <select
                                                                            class="primary_select form-control{{ $errors->has('district_id') ? ' is-invalid' : '' }}"
                                                                            name="district_id" id="district_id">
                                                                            <option value="">Select District</option>
                                                                            @foreach ($districts as $district)
                                                                                <option value="{{ $district->id }}"
                                                                                    @if (old('district_id', isset($student) ? $student->district_id : '') == $district->id) selected @endif>
                                                                                    {{ $district->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>

                                                                        @error('district_id')
                                                                            <span
                                                                                class="text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-12 mt-4">
                                                                    <div class="primary_input">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.permanent_address')

                                                                            {{-- <span class="text-danger">
                                                                                                *</span> --}}

                                                                        </label>
                                                                        <textarea class="primary_input_field form-control{{ $errors->has('current_address') ? ' is-invalid' : '' }}"
                                                                            cols="0" rows="3" name="permanent_address" id="permanent_address">{{ old('permanent_address') }}</textarea>


                                                                        @if ($errors->has('permanent_address'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('permanent_address') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>



                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.phone_number') 1

                                                                            <span class="text-danger"> *</span>

                                                                        </label>
                                                                        <input oninput="phoneCheck(this)"
                                                                            class="primary_input_field phone_number form-control{{ $errors->has('phone_number') ? ' is-invalid' : '' }}"
                                                                            type="tel" name="phone_number"
                                                                            id="phone_number"
                                                                            value="{{ old('phone_number') }}">




                                                                        @if ($errors->has('phone_number'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('phone_number') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                {{-- Extra Phone Number --}}
                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">Phone Number 2
                                                                            (optional)
                                                                        </label>
                                                                        <input oninput="phoneCheck(this)"
                                                                            class="primary_input_field phone_number form-control{{ $errors->has('phone_number_2') ? ' is-invalid' : '' }}"
                                                                            type="tel" name="phone_number_2"
                                                                            id="phone_number_2"
                                                                            value="{{ old('phone_number_2', isset($student) ? $student->phone_number_2 : '') }}">
                                                                        @if ($errors->has('phone_number_2'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('phone_number_2') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>


                                                                <div class="row mb-40 d-none" id="exitStudent">
                                                                    <div class="col-lg-12">
                                                                        <input type="checkbox" id="edit_info"
                                                                            value="yes" class="common-checkbox"
                                                                            name="edit_info">
                                                                        <label for="edit_info"
                                                                            class="text-danger">@lang('student.student_already_exit_this_phone_number/email_are_you_to_edit_student_parent_info')</label>
                                                                    </div>
                                                                </div>

                                                                {{-- <div class="col-lg-12">


                                                                            <div class="row mt-40">
                                                                                <div class="col-lg-12">
                                                                                    <div class="main-title">
                                                                                        <h4 class="stu-sub-head">
                                                                                            @lang('student.student_address_info')
                                                                                        </h4>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row">


                                                                                <div class="col-lg-6 mt-4" hidden>
                                                                                    <div class="primary_input ">
                                                                                        <label class="primary_input_label"
                                                                                            for="">@lang('student.current_address')

                                                                                            <span class="text-danger">
                                                                                                *</span>

                                                                                        </label>
                                                                                        <textarea class="primary_input_field form-control{{ $errors->has('current_address') ? ' is-invalid' : '' }}"
                                                                                            cols="0" rows="3" name="current_address" id="current_address">{{ old('current_address') }}</textarea>


                                                                                        @if ($errors->has('current_address'))
                                                                                            <span class="text-danger">
                                                                                                {{ $errors->first('current_address') }}
                                                                                            </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-lg-6 mt-4">
                                                                                    <div class="primary_input">
                                                                                        <label class="primary_input_label"
                                                                                            for="">@lang('student.permanent_address')

                                                                                            <span class="text-danger">
                                                                                                *</span>

                                                                                        </label>
                                                                                        <textarea class="primary_input_field form-control{{ $errors->has('current_address') ? ' is-invalid' : '' }}"
                                                                                            cols="0" rows="3" name="permanent_address" id="permanent_address">{{ old('permanent_address') }}</textarea>


                                                                                        @if ($errors->has('permanent_address'))
                                                                                            <span class="text-danger">
                                                                                                {{ $errors->first('permanent_address') }}
                                                                                            </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>

                                                                            </div>

                                                                        </div> --}}
                                                            </div>
                                                            {{-- </div>
                                                            </div> --}}


                                                        </div>

                                                    </div>

                                                    {{-- ==============personal info ============= --}}
                                                    <div class="col-lg-6">
                                                        <div class="form-section">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="main-title">
                                                                        <h4 class="stu-sub-head">@lang('student.personal_info')</h4>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-12 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">Full Name

                                                                            <span class="text-danger"> *</span>

                                                                        </label>
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('student_name') ? ' is-invalid' : '' }}"
                                                                            type="text" name="student_name"
                                                                            id="full_name"
                                                                            value="{{ old('student_name') }}">


                                                                        @if ($errors->has('student_name'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('student_name') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('common.gender')

                                                                            <span class="text-danger"> *</span>

                                                                        </label>
                                                                        <select
                                                                            class="primary_select form-control{{ $errors->has('gender_id') ? ' is-invalid' : '' }}"
                                                                            name="gender_id">
                                                                            <option value="">Select Gender</option>
                                                                            @foreach ($genders as $gender)
                                                                                <option value="{{ $gender->id }}"
                                                                                    {{ old('gender_id') == $gender->id ? 'selected' : '' }}>
                                                                                    {{ $gender->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>

                                                                        @if ($errors->has('gender_id'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('gender_id') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input">
                                                                        <label class="primary_input_label"
                                                                            for="date_of_birth">
                                                                            {{ __('common.date_of_birth') }}
                                                                            <span class="text-danger">*</span>
                                                                        </label>
                                                                        <div class="">
                                                                            <div class="no-gutters input-right-icon">
                                                                                <div class="col">
                                                                                    <div class="">
                                                                                        <input
                                                                                            class="primary_input_field date form-control{{ $errors->has('date_of_birth') ? ' is-invalid' : '' }}"
                                                                                            id="date_of_birth"
                                                                                            type="date"
                                                                                            name="date_of_birth"
                                                                                            value="{{ old('date_of_birth') != '' ? old('date_of_birth') : '' }}"
                                                                                            autocomplete="off">
                                                                                    </div>
                                                                                </div>
                                                                                <button class="btn-date"
                                                                                    {{-- style="top: 55% !important;" --}}
                                                                                    data-id="#date_of_birth"
                                                                                    type="button">
                                                                                    <label class="m-0 p-0"
                                                                                        for="date_of_birth">
                                                                                        {{-- <i class="ti-calendar"
                                                                                            id="start-date-icon"></i> --}}
                                                                                    </label>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        <span
                                                                            class="text-danger">{{ $errors->first('date_of_birth') }}</span>
                                                                    </div>
                                                                </div>

                                                                {{-- Age Display --}}
                                                                <div class="col-lg-6 mt-2">
                                                                    <div class="primary_input">
                                                                        <label class="primary_input_label"
                                                                            for="age_display">Age</label>
                                                                        <input class="primary_input_field form-control"
                                                                            type="text" id="age_display" readonly>
                                                                        <input type="hidden" name="stu_age"
                                                                            id="stu_age_hidden">
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-2">
                                                                    <div class="primary_input">
                                                                        <label class="primary_input_label"
                                                                            for="student_photo">Photo</label>
                                                                        <div class="primary_file_uploader">
                                                                            <input
                                                                                class="primary_input_field form-control{{ $errors->has('photo') ? ' is-invalid' : '' }}"
                                                                                type="text" id="placeholderPhoto"
                                                                                placeholder="@lang('common.student_photo') "
                                                                                readonly="">
                                                                            <button class="" type="button">
                                                                                <label class="primary-btn small fix-gr-bg"
                                                                                    for="addStudentImage">{{ __('common.browse') }}</label>

                                                                                <input type="file" class="d-none"
                                                                                    name="photo" id="addStudentImage"
                                                                                    accept="image/*">
                                                                            </button>

                                                                        </div>
                                                                        <!-- Image preview container -->
                                                                        <div id="imagePreviewContainer" class="mt-2"
                                                                            style="display: none;">
                                                                            <img id="imagePreview" src="#"
                                                                                alt="Preview"
                                                                                style="max-width: 200px; max-height: 200px;" />
                                                                        </div>
                                                                    </div>
                                                                    @if ($errors->has('photo'))
                                                                        <span class="text-danger">
                                                                            {{ $errors->first('photo') }}
                                                                        </span>
                                                                    @endif
                                                                </div>

                                                                <div class="col-lg-12 mt-4">
                                                                    <div class="primary_input">
                                                                        <label class="primary_input_label"
                                                                            for="">Capture Photo</label>
                                                                        <div class="d-flex flex-column align-items-center">
                                                                            <video id="video" width="320"
                                                                                height="240" autoplay
                                                                                style="display: none;"></video>
                                                                            <canvas id="canvas" width="320"
                                                                                height="240"
                                                                                style="display: none;"></canvas>
                                                                            <div class="mt-2">
                                                                                <button type="button"
                                                                                    class="primary-btn small fix-gr-bg"
                                                                                    id="startCameraButton">Start
                                                                                    Camera</button>
                                                                                <button type="button"
                                                                                    class="primary-btn small fix-gr-bg"
                                                                                    id="captureButton"
                                                                                    style="display: none;">Capture
                                                                                    Photo</button>
                                                                                <button type="button"
                                                                                    class="primary-btn small fix-gr-bg"
                                                                                    id="saveCapturedButton"
                                                                                    style="display: none;">Save
                                                                                    Photo</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12 mt-15">
                                                                    <img class="d-none previewImageSize" src=""
                                                                        alt="" id="studentImageShow"
                                                                        height="100%" width="100%">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    {{-- ===================contact info ============================ --}}


                                                    {{-- <div class="col-lg-6" hidden>
                                                        <div class="form-section">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="main-title">
                                                                        <h4 class="stu-sub-head">@lang('student.medical_record')</h4>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label" for="">
                                                                            @lang('common.blood_group')
                                                                        </label>


                                                                        <select
                                                                            class="primary_select form-control{{ $errors->has('blood_group_id') ? ' is-invalid' : '' }}"
                                                                            name="blood_group_id">
                                                                            <option value=""> Select Blood Group
                                                                            </option>

                                                                            @foreach ($bloodGroups as $group)
                                                                                <option value="{{ $group->id }}"
                                                                                    {{ old('blood_group_id') == $group->id ? 'selected' : '' }}>
                                                                                    {{ $group->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>

                                                                        @if ($errors->has('blood_group_id'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('blood_group_id') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>





                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label" for="">
                                                                            @lang('student.height_in')
                                                                        </label>
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('height') ? ' is-invalid' : '' }}"
                                                                            type="text" name="height"
                                                                            value="{{ old('height') }}">
                                                                        @if ($errors->has('height'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('height') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.weight_kg')
                                                                        </label>
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('weight') ? ' is-invalid' : '' }}"
                                                                            type="text" name="weight"
                                                                            value="{{ old('weight') }}">


                                                                        @if ($errors->has('weight'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('weight') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>


                                                            </div>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane fade" id="parents_and_guardian_info">
                                                <div class="row pt-4 row-gap-24">
                                                    <div class="col-lg-12">
                                                        <div class="form-section">
                                                            <div class="row">

                                                                <div class="col-lg-12 text-right">
                                                                    <div class="row">
                                                                        <div class="col-lg-7 text-left" id="parent_info">
                                                                            <input type="hidden" name="parent_id"
                                                                                value="">

                                                                        </div>
                                                                        {{-- <div class="col-lg-5">
                                                                            <button
                                                                                class="primary-btn-small-input primary-btn small fix-gr-bg"
                                                                                type="button" data-toggle="modal"
                                                                                data-target="#editStudent">
                                                                                <span class="ti-plus pr-2"></span>
                                                                                @lang('student.add_parents')
                                                                            </button>
                                                                        </div> --}}
                                                                    </div>

                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>



                                                    <div class="col-lg-12 guardian_section">
                                                        <div class="form-section">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="main-title">
                                                                        <h4 class="stu-sub-head">@lang('common.guardian_info')
                                                                        </h4>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-12">
                                                                    <div class="row mt-4">
                                                                        <div class="col-lg-12 d-flex gap-20 flex-wrap">
                                                                            <p class="text-uppercase fw-500 mb-10">
                                                                                @lang('student.relation_with_guardian')</p>
                                                                            <div class="d-flex radio-btn-flex flex-wrap">
                                                                                <div class="mr-30">
                                                                                    <input type="radio"
                                                                                        name="relation_guardian_id"
                                                                                        id="relationFather" value="1"
                                                                                        class="common-radio relationButton"
                                                                                        {{ old('relation_guardian_id', isset($student) ? $student->relation_guardian_id : '') == '1' ? 'checked' : '' }}>
                                                                                    <label
                                                                                        for="relationFather">@lang('student.father')</label>
                                                                                </div>
                                                                                <div class="mr-30">
                                                                                    <input type="radio"
                                                                                        name="relation_guardian_id"
                                                                                        id="relationMother" value="2"
                                                                                        class="common-radio relationButton"
                                                                                        {{ old('relation_guardian_id', isset($student) ? $student->relation_guardian_id : '') == '2' ? 'checked' : '' }}>
                                                                                    <label
                                                                                        for="relationMother">@lang('student.mother')</label>
                                                                                </div>
                                                                                <div>
                                                                                    <input type="radio"
                                                                                        name="relation_guardian_id"
                                                                                        id="relationOther" value="3"
                                                                                        class="common-radio relationButton"
                                                                                        {{ old('relation_guardian_id', isset($student) ? $student->relation_guardian_id : '') == '3' ? 'checked' : '' }}>
                                                                                    <label
                                                                                        for="relationOther">@lang('student.Other')</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.guardian_name')

                                                                            <span class="text-danger">
                                                                                *</span>

                                                                        </label>
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('guardians_name') ? ' is-invalid' : '' }}"
                                                                            type="text" name="guardians_name"
                                                                            id="guardians_name"
                                                                            value="{{ old('guardians_name') }}">


                                                                        @if ($errors->has('guardians_name'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('guardians_name') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.relation_with_guardian')

                                                                            <span class="text-danger">
                                                                            </span>

                                                                        </label>
                                                                        <input class="primary_input_field read-only-input"
                                                                            type="text" placeholder="Relation"
                                                                            name="relation" id="relation" value="Other"
                                                                            readonly>

                                                                        <input type="hidden" name="relation_guardian_id"
                                                                            id="relationButtonHidden"
                                                                            value="{{ old('relation_guardian_id', isset($student) ? $student->relation_guardian_id : '3') }}">

                                                                        @if ($errors->has('relation'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('relation') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.guardian_email')

                                                                            <span class="text-danger">
                                                                            </span>

                                                                        </label>
                                                                        <input oninput="emailCheck(this)"
                                                                            class="primary_input_field form-control{{ $errors->has('guardians_email') ? ' is-invalid' : '' }}"
                                                                            type="text" name="guardians_email"
                                                                            id="guardians_email"
                                                                            value="{{ old('guardians_email') }}">


                                                                        @if ($errors->has('guardians_email'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('guardians_email') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.guardians_photo')


                                                                        </label>
                                                                        <div class="primary_file_uploader">
                                                                            <input class="primary_input_field"
                                                                                type="text"
                                                                                id="placeholderGuardiansName"
                                                                                placeholder="@lang('student.photo')"
                                                                                readonly="">
                                                                            <button class="" type="button">
                                                                                <label class="primary-btn small fix-gr-bg"
                                                                                    for="addGuardianImage">{{ __('common.browse') }}</label>
                                                                                <input type="file" class="d-none"
                                                                                    name="guardians_photo"
                                                                                    id="addGuardianImage">
                                                                            </button>
                                                                        </div>
                                                                        <span
                                                                            class="text-danger">{{ $errors->first('guardians_photo') }}</span>
                                                                    </div>
                                                                    <div class="col-md-12 mt-15">
                                                                        <img class="d-none previewImageSize"
                                                                            src="" alt=""
                                                                            id="guardianImageShow" height="100%"
                                                                            width="100%">
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.guardian_phone')

                                                                            <span class="text-danger">
                                                                            </span>

                                                                        </label>
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('guardians_phone') ? ' is-invalid' : '' }}"
                                                                            type="text" name="guardians_phone"
                                                                            id="guardians_phone"
                                                                            value="{{ old('guardians_phone') }}">


                                                                        @if ($errors->has('guardians_phone'))
                                                                            <span class="text-danger">
                                                                                {{ @$errors->first('guardians_phone') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.guardian_occupation')

                                                                            <span class="text-danger">
                                                                            </span>

                                                                        </label>
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('guardians_occupation') ? ' is-invalid' : '' }}"
                                                                            type="text" name="guardians_occupation"
                                                                            id="guardians_occupation"
                                                                            value="{{ old('guardians_occupation') }}">


                                                                        @if ($errors->has('guardians_occupation'))
                                                                            <span class="text-danger">
                                                                                {{ @$errors->first('guardians_occupation') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-12 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.guardian_address')

                                                                            <span class="text-danger">
                                                                            </span>

                                                                        </label>
                                                                        <textarea class="primary_input_field form-control{{ $errors->has('guardians_address') ? ' is-invalid' : '' }}"
                                                                            cols="0" rows="3" name="guardians_address" id="guardians_address">{{ old('guardians_address') }}</textarea>


                                                                        @if ($errors->has('guardians_address'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('guardians_address') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col-lg-6 order-2 order-lg-3">
                                                    <div class="form-section">
                                                        
                                                    </div>
                                                </div> --}}
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane fade" id="document_info">
                                                <div class="row pt-4 row-gap-24">
                                                    <div class="col-lg-12">
                                                        <div class="form-section">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="main-title">
                                                                        <h4 class="stu-sub-head">@lang('student.document_info')</h4>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-12 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('common.national_id_number')

                                                                            <span class="text-danger"> *</span>

                                                                        </label>
                                                                        {{-- <input
                                                                            class="primary_input_field form-control{{ $errors->has('national_id_number') ? ' is-invalid' : '' }}"
                                                                            type="text" name="national_id_number"
                                                                            value="{{ old('national_id_number') }}"
                                                                            maxlength="12" pattern="[0-9]{12}"
                                                                            title="Please enter a valid 12-digit Sri Lankan ID number"
                                                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                                             --}}
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('national_id_number') ? ' is-invalid' : '' }}"
                                                                            type="text" name="national_id_number"
                                                                            value="{{ old('national_id_number') }}"
                                                                            id="national_id_number">



                                                                        @if ($errors->has('national_id_number'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('national_id_number') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>



                                                                <div class="col-lg-12 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.additional_notes')
                                                                        </label>
                                                                        <textarea class="primary_input_field form-control{{ $errors->has('additional_notes') ? ' is-invalid' : '' }}"
                                                                            cols="0" rows="3" name="additional_notes">{{ old('additional_notes') }}</textarea>


                                                                        @if ($errors->has('additional_notes'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('additional_notes') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>



                                                    <div class="col-lg-12">
                                                        <div class="form-section">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="main-title">
                                                                        <h4 class="stu-sub-head">@lang('student.document_attachment')</h4>
                                                                    </div>
                                                                </div>

                                                                <div class="col-xl-3 col-lg-4 col-md-6 mt-4">
                                                                    <div class="row">

                                                                        <div class="col-lg-12">
                                                                            <div class="primary_input ">
                                                                                <label class="primary_input_label"
                                                                                    for="">@lang('student.document_01_title')



                                                                                </label>
                                                                                <input
                                                                                    class="primary_input_field form-control{{ $errors->has('document_title_1') ? ' is-invalid' : '' }}"
                                                                                    type="text" name="document_title_1"
                                                                                    value="{{ old('document_title_1', isset($student) ? $student->document_title_1 : '') }}">
                                                                                @if ($errors->has('document_title_1'))
                                                                                    <span class="text-danger">
                                                                                        {{ $errors->first('document_title_1') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 mt-2 mb-3 mb-lg-0">
                                                                            <div class="primary_input">
                                                                                <div class="primary_file_uploader">
                                                                                    <input class="primary_input_field"
                                                                                        type="text"
                                                                                        id="placeholderFileOneName"
                                                                                        placeholder="01  document_file_1 * "
                                                                                        readonly=""
                                                                                        value="{{ isset($student) ? $student->document_file_1 : '' }}">
                                                                                    <button class="" type="button">
                                                                                        <label
                                                                                            class="primary-btn small fix-gr-bg"
                                                                                            for="document_file_1">{{ __('common.browse') }}</label>
                                                                                        <input type="file"
                                                                                            class="d-none"
                                                                                            name="document_file_1"
                                                                                            id="document_file_1">
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-xl-3 col-lg-4 col-md-6 mt-4">
                                                                    <div class="row">

                                                                        <div class="col-lg-12">
                                                                            <div class="primary_input ">
                                                                                <label class="primary_input_label"
                                                                                    for="">@lang('student.document_02_title')

                                                                                </label>
                                                                                <input
                                                                                    class="primary_input_field form-control{{ $errors->has('document_title_2') ? ' is-invalid' : '' }}"
                                                                                    type="text" name="document_title_2"
                                                                                    value="{{ old('document_title_2', isset($student) ? $student->document_title_2 : '') }}">


                                                                                @if ($errors->has('document_title_2'))
                                                                                    <span class="text-danger">
                                                                                        {{ $errors->first('document_title_2') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 mt-2 mb-3 mb-lg-0">
                                                                            <div class="primary_input">
                                                                                <div class="primary_file_uploader">
                                                                                    <input class="primary_input_field"
                                                                                        type="text"
                                                                                        id="placeholderFileTwoName"
                                                                                        placeholder="01  document_file 2 * "
                                                                                        readonly="">
                                                                                    <button class="" type="button">
                                                                                        <label
                                                                                            class="primary-btn small fix-gr-bg"
                                                                                            for="document_file_2">{{ __('common.browse') }}</label>
                                                                                        <input type="file"
                                                                                            class="d-none"
                                                                                            name="document_file_2"
                                                                                            id="document_file_2">
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-xl-3 col-lg-4 col-md-6 mt-4">
                                                                    <div class="row">

                                                                        <div class="col-lg-12">
                                                                            <div class="primary_input ">
                                                                                <label class="primary_input_label"
                                                                                    for="">@lang('student.document_03_title')

                                                                                </label>
                                                                                <input
                                                                                    class="primary_input_field form-control{{ $errors->has('document_title_3') ? ' is-invalid' : '' }}"
                                                                                    type="text" name="document_title_3"
                                                                                    value="{{ old('document_title_3') }}">


                                                                                @if ($errors->has('document_title_3'))
                                                                                    <span class="text-danger">
                                                                                        {{ $errors->first('document_title_3') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 mt-2 mb-3 mb-lg-0">
                                                                            <div class="primary_input">
                                                                                <div class="primary_file_uploader">
                                                                                    <input class="primary_input_field"
                                                                                        type="text"
                                                                                        id="placeholderFileThreeName"
                                                                                        placeholder="01  document_file_1 * "
                                                                                        readonly="">
                                                                                    <button class="" type="button">
                                                                                        <label
                                                                                            class="primary-btn small fix-gr-bg"
                                                                                            for="document_file_3">{{ __('common.browse') }}</label>
                                                                                        <input type="file"
                                                                                            class="d-none"
                                                                                            name="document_file_3"
                                                                                            id="document_file_3">
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-xl-3 col-lg-4 col-md-6 mt-4">
                                                                    <div class="row">

                                                                        <div class="col-lg-12">
                                                                            <div class="primary_input ">
                                                                                <label class="primary_input_label"
                                                                                    for="">@lang('student.document_04_title')

                                                                                </label>

                                                                                <input
                                                                                    class="primary_input_field form-control{{ $errors->has('document_title_4') ? ' is-invalid' : '' }}"
                                                                                    type="text" name="document_title_4"
                                                                                    value="{{ old('document_title_4') }}">

                                                                                @if ($errors->has('document_title_4'))
                                                                                    <span class="text-danger">
                                                                                        {{ $errors->first('document_title_4') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 mt-2 mb-3 mb-lg-0">

                                                                            <div class="primary_input">
                                                                                <div class="primary_file_uploader">
                                                                                    <input class="primary_input_field"
                                                                                        type="text"
                                                                                        id="placeholderFileFourName"
                                                                                        placeholder="04  document_file_1 * "
                                                                                        readonly="">
                                                                                    <button class="" type="button">
                                                                                        <label
                                                                                            class="primary-btn small fix-gr-bg"
                                                                                            for="document_file_4">{{ __('common.browse') }}</label>
                                                                                        <input type="file"
                                                                                            class="d-none"
                                                                                            name="document_file_4"
                                                                                            id="document_file_4">
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane fade" id="previous_school_info">
                                                <div class="row pt-4 row-gap-24">
                                                    <div class="col-lg-12">
                                                        <div class="form-section">
                                                            <div class="row">

                                                                <div class="col-lg-12">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">Previous Institute Information
                                                                        </label>
                                                                        <textarea
                                                                            class="primary_input_field form-control{{ $errors->has('previous_institute_details') ? ' is-invalid' : '' }}"
                                                                            cols="0" rows="5" name="previous_institute_details">{{ old('previous_institute_details') }}</textarea>


                                                                        @if ($errors->has('previous_institute_details'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('previous_institute_details') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane fade" id="Other_info">
                                                <div class="row pt-4 row-gap-24">

                                                    <div class="col-lg-12">
                                                        <div class="form-section">

                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="main-title">
                                                                        <h4 class="stu-sub-head">
                                                                            @lang('student.dormitory_info')
                                                                        </h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-15 mt-30">

                                                                <div class="col-lg-12">
                                                                    <div class="primary_input ">
                                                                        <label for="primary_input_label">@lang('dormitory.dormitory')
                                                                            <span>

                                                                            </span>
                                                                        </label>
                                                                        <select
                                                                            class="primary_select form-control{{ $errors->has('dormitory_id') ? ' is-invalid' : '' }}"
                                                                            name="dormitory_id" id="SelectDormitory">
                                                                            <option data-display="@lang('dormitory.dormitory_name') "
                                                                                value="">
                                                                                @lang('dormitory.dormitory_name')

                                                                            </option>
                                                                            <option value="1"
                                                                                {{ old('dormitory_id', isset($student) ? $student->dormitory_id : '') == '1' ? 'selected' : '' }}>
                                                                                1 </option>
                                                                            <option value="2"
                                                                                {{ old('dormitory_id', isset($student) ? $student->dormitory_id : '') == '2' ? 'selected' : '' }}>
                                                                                2 </option>
                                                                        </select>

                                                                    </div>
                                                                    @if ($errors->has('dormitory_id'))
                                                                        <span class="text-danger">
                                                                            {{ $errors->first('dormitory_id') }}
                                                                        </span>
                                                                    @endif
                                                                </div>




                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Exit student admission number --}}
            <div class="modal fade" id="admissionModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Existing Student Verification</h5>
                            <button type="button" class="btn-close" id="model-cancel" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <label class="fw-bold">Enter Admission Number</label>
                            <input type="text" class="form-control" id="modalAdmissionNumber"
                                placeholder="Admission Number">
                            <small class="text-danger d-none" id="admissionError">
                                Admission number not found
                            </small>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="model-cancel-btn"
                                data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-primary" id="verifyAdmission">
                                Verify & Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- student import --}}
            <div class="modal fade" id="importStudentModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Import Students</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <form id="importStudentForm" enctype="multipart/form-data" method="POST"
                                action="{{ route('students.import') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Upload Excel / CSV file</label>
                                    <input type="file" name="students_file" id="studentsFile" class="form-control"
                                        accept=".xlsx,.xls" required>
                                </div>

                                <div id="previewContainer" style="display: none;">
                                    <h6>Preview</h6>
                                    <table class="table table-bordered" id="previewTable">
                                        <thead>
                                            <tr>
                                                <th>Full Name</th>
                                                <th>Permanent Address</th>
                                                <th>Phone Number</th>
                                                <th>Guardian Name</th>
                                                <th>Guardian Phone</th>
                                                <th>Date of Birth</th>
                                                <th>NIC Number</th>
                                                <th>Nearest Police Station</th>
                                                <th>Divisional Secretariat</th>
                                                <th>District</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="previewImport">Preview</button>
                            <button type="submit" form="importStudentForm" class="btn btn-success" id="confirmImport"
                                style="display:none;">Import Students</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <style>
        .toggle-btn {
            position: relative;
            width: 60px;
            height: 30px;
        }

        .toggle-btn input {
            display: none;
        }

        .slider {
            position: absolute;
            background-color: #ccc;
            border-radius: 30px;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            transition: 0.3s;
        }

        .slider::before {
            content: "";
            position: absolute;
            height: 24px;
            width: 24px;
            left: 3px;
            bottom: 3px;
            background: white;
            border-radius: 50%;
            transition: 0.3s;
        }

        input:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider::before {
            transform: translateX(30px);
        }
    </style>
@endsection


@include('components.partials.date_picker_css_js')
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- <script src="{{ asset('public/backEnd/js/croppie.js') }}"></script>
    <script src="{{ asset('public/backEnd/js/st_addmision.js') }}"></script> --}}
    <script>
        let previewStudents = [];

        document.addEventListener('DOMContentLoaded', function() {

            const toggle = document.getElementById('isExistingStudent');
            const modalEl = document.getElementById('admissionModal');
            const modal = new bootstrap.Modal(modalEl);

            const modalInput = document.getElementById('modalAdmissionNumber');
            const errorText = document.getElementById('admissionError');
            const hiddenAdmission = document.getElementById('hiddenAdmissionNumber');

            // Toggle click → open modal
            toggle.addEventListener('change', function() {
                if (this.checked) {
                    modal.show();
                } else {
                    toggle.checked = false;
                    hiddenAdmission.value = '';
                    modalInput.value = '';
                    errorText.classList.add('d-none');
                }
            });

            document.getElementById('model-cancel').onclick = function() {
                modal.hide();
                toggle.checked = false;
                hiddenAdmission.value = '';
                modalInput.value = '';
                errorText.classList.add('d-none');
            };

            document.getElementById('model-cancel-btn').onclick = function() {
                modal.hide();
                toggle.checked = false;
                hiddenAdmission.value = '';
                modalInput.value = '';
                errorText.classList.add('d-none');
            };
            // Verify admission number
            document.getElementById('verifyAdmission').addEventListener('click', function() {

                const admissionNo = modalInput.value.trim();

                if (!admissionNo) {
                    errorText.textContent = 'Admission number required';
                    errorText.classList.remove('d-none');
                    return;
                }

                fetch("{{ route('check-admission-number') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            admission_number: admissionNo
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.exists) {
                            errorText.textContent = 'Admission number not found';
                            errorText.classList.remove('d-none');
                        } else {
                            hiddenAdmission.value = admissionNo;

                            errorText.classList.add('d-none');
                            modal.hide();
                        }
                    });

            });

        });

        // student import
        document.getElementById('previewImport').addEventListener('click', function() {
            const fileInput = document.getElementById('studentsFile');
            const file = fileInput.files[0];

            if (!file) {
                alert('Please select a file.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {
                    type: 'array'
                });
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];

                // Raw JSON
                const jsonDataRaw = XLSX.utils.sheet_to_json(worksheet, {
                    defval: ''
                });

                // Normalize keys
                const jsonDataClean = jsonDataRaw.map(row => {
                    const cleanRow = {};
                    for (let key in row) {
                        const normalizedKey = key.replace(/[:\s]/g, '').replace(/\u00A0/g, '')
                            .toLowerCase();
                        cleanRow[normalizedKey] = row[key];
                    }
                    return cleanRow;
                });
                previewStudents = jsonDataClean;

                // Collect all NICs from file
                const nicList = jsonDataClean.map(r => r['nicnumber']).filter(Boolean);

                // AJAX check existing NICs
                fetch("{{ route('students.check-nic') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            nics: nicList
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        const existingNics = data.existing; // array of NICs in DB

                        const previewContainer = document.getElementById('previewContainer');
                        previewContainer.innerHTML = ''; // clear old preview

                        let hasDuplicate = false;

                        jsonDataClean.forEach((row, index) => {
                            // Convert Excel DOB number to date
                            let dob = row['dateofbirth'] || '';
                            if (typeof dob === 'number') {
                                const date = XLSX.SSF.parse_date_code(dob);
                                dob =
                                    `${date.y}-${date.m.toString().padStart(2,'0')}-${date.d.toString().padStart(2,'0')}`;

                            }

                            const isDuplicate = existingNics.includes(row['nicnumber']);
                            if (isDuplicate) hasDuplicate = true;

                            const div = document.createElement('div');
                            div.classList.add('mb-2', 'border', 'p-2', 'rounded');
                            div.innerHTML = `
                                <strong>Student :</strong> ${row['studentfullname'] || ''} 
                                ${isDuplicate ? '<span class="text-danger">(NIC exists!)</span>' : ''}<br>
                                <strong>DOB:</strong> ${dob}<br>
                                <strong>Phone:</strong> ${row['phonenumber'] || ''}<br>
                                <strong>Permanent Address:</strong> ${row['permanentaddress'] || ''}<br>
                                <strong>Guardian Name:</strong> ${row['nameofguardian'] || ''}<br>
                                <strong>Guardian Phone:</strong> ${row['guardiansphonenumber'] || ''}<br>
                                <strong>NIC Number:</strong> ${row['nicnumber'] || ''}<br>
                                <strong>District:</strong> ${row['district'] || ''}<br>
                            `;
                            previewContainer.appendChild(div);
                            if (dob) {
                                let birthDate = new Date(dob);
                                let today = new Date();
                                let years = today.getFullYear() - birthDate.getFullYear();
                                let months = today.getMonth() - birthDate.getMonth();

                                if (months < 0 || (months === 0 && today.getDate() < birthDate
                                        .getDate())) {
                                    years--;
                                    months += 12;
                                }

                                $('#age_display').val(years + ' years, ' + months + ' months');
                                $('#stu_age_hidden').val(years); // Set the hidden input for stu_age
                            } else {
                                $('#age_display').val('');
                                $('#stu_age_hidden').val('');
                            }

                        });

                        previewContainer.style.display = 'block';

                        // Only show import button if no duplicates
                        const confirmBtn = document.getElementById('confirmImport');

                        if (hasDuplicate) {
                            confirmBtn.style.display = 'none';
                            Swal.fire({
                                icon: 'warning',
                                title: 'Duplicate NIC found',
                                text: 'Some NIC numbers already exist! Please fix before importing.'
                            });
                        } else {

                            fillStudentForm(previewStudents[0]);
                            $('#importStudentModal').modal('hide');
                            // Swal.fire({
                            //     title: 'What do you want to do?',
                            //     text: 'Import all students or continue manually?',
                            //     icon: 'question',
                            //     showCancelButton: true,
                            //     confirmButtonText: 'Save All',
                            //     cancelButtonText: 'Continue Manually'
                            // }).then((result) => {

                            //     if (result.isConfirmed) {
                            //          confirmBtn.style.display = 'inline-block';
                            //     } else {
                            //         fillStudentForm(previewStudents[0]);
                            //         $('#importStudentModal').modal('hide');
                            //     }

                            // });

                        }
                    });
            };

            reader.readAsArrayBuffer(file);
        });

        function fillStudentForm(student) {

            if (!student) return;
            $('#full_name').val(student.studentfullname || '');
            $('#permanent_address').val(student.permanentaddress || '');
            $('#phone_number').val(student.phonenumber || '');
            $('#guardians_name').val(student.nameofguardian || '');
            $('#guardians_phone').val(student["guardian'sphonenumber"] || '');
            $('#national_id_number').val(student.nicnumber || '');

            // DOB convert
            let dob = student.dateofbirth || '';
            if (typeof dob === 'number') {
                const date = XLSX.SSF.parse_date_code(dob);
                dob = `${date.y}-${String(date.m).padStart(2,'0')}-${String(date.d).padStart(2,'0')}`;
            }
            $('#date_of_birth').val(dob);

            console.log('dd', student.district)
            const cleanDistrict = (student.district || '')
                .replace(/\n/g, '')
                .replace(/\s+/g, ' ')
                .trim()
                .toLowerCase();
            $("#district_id option").each(function() {

                const optionText = $(this).text()
                    .replace(/\n/g, '')
                    .replace(/\s+/g, ' ')
                    .trim()
                    .toLowerCase();

                if (optionText === cleanDistrict) {
                    $(this).prop('selected', true);
                }
            });

            $('#district_id').niceSelect('update');

            toastr.success('Student data loaded to form. You can continue editing.');
        }

        $(document).ready(function() {

            var currentDate = new Date();
            $('#startDate').datepicker({
                format: 'mm/dd/yyyy',
                autoclose: true,
                endDate: "currentDate",
                maxDate: currentDate
            }).on('changeDate', function(ev) {
                $(this).datepicker('hide');
                console.log($(this).datepicker('hide'));
            });
            $('#startDate').keyup(function() {
                if (this.value.match(/[^0-9]/g)) {
                    this.value = this.value.replace(/[^0-9^-]/g, '');
                }
            });

            $(document).on('change', '.cutom-photo', function() {
                let v = $(this).val();
                let v1 = $(this).data("id");
                console.log(v, v1);
                getFileName(v, v1);

            });

            function getFileName(value, placeholder) {
                if (value) {
                    var startIndex = (value.indexOf('\\') >= 0 ? value.lastIndexOf('\\') : value.lastIndexOf('/'));
                    var filename = value.substring(startIndex);
                    if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                        filename = filename.substring(1);
                    }
                    $(placeholder).val(filename); // Changed from .attr('placeholder', filename) to .val(filename)
                } else {
                    $(placeholder).val(''); // Clear the value if no file is selected
                }
            }
            $(document).on('change', '.phone_number', function() {

                let email = $("#email_address").val();
                let phone = $(this).val();
                checkExitStudent(email, phone);
            });
            $(document).on('change', '.email_address', function() {
                let email = $(this).val();
                let phone = $("#phone_number").val();
                checkExitStudent(email, phone);
            });

            function checkExitStudent(email, phone) {
                var url = $("#url").val();
                var formData = {
                    email: email,
                    phone: phone,
                }
                $.ajax({
                    type: "GET",
                    data: formData,
                    dataType: "json",
                    url: url + "/" + "student/check-exit",
                    success: function(data) {
                        if (data.student != null) {
                            $('#exitStudent').removeClass('d-none');
                        } else {
                            $('#exitStudent').addClass('d-none');
                            $('#edit_info').prop('checked', false);
                        }

                    },
                    error: function(data) {
                        console.log("Error:", data);
                    }

                })
            }
            $(document).on('change', '.addParent', function() {
                let type = $(this).val();
                if (type == 'staff') {
                    $('#staffParent').removeClass('d-none');
                    $('#siblingParent').addClass('d-none');
                } else if (type == 'sibling') {
                    $('#siblingParent').removeClass('d-none');
                    $('#staffParent').addClass('d-none');
                }
            });

            // Handle relation button changes
            $(document).on('change', '.relationButton', function() {
                let relationValue = $(this).val();
                let relationText = $(this).next('label').text();

                $('#relation').val(relationText);
                $('#relationButtonHidden').val(relationValue);
            });

            // Initialize relation on page load
            let initialRelation = $('#relationButtonHidden').val();
            if (initialRelation === '1') {
                $('#relationFather').prop('checked', true);
                $('#relation').val('Father');
            } else if (initialRelation === '2') {
                $('#relationMother').prop('checked', true);
                $('#relation').val('Mother');
            } else {
                $('#relationOther').prop('checked', true);
                $('#relation').val('Other');
            }

            // Auto-select branch if only one is available
            const branchCount = {{ $branchCount }};
            const singleBranchId = {{ Js::from($singleBranchId) }};

            if (branchCount === 1 && singleBranchId) {
                $('#branch_id').val(singleBranchId);
                if ($.fn.niceSelect) {
                    $('#branch_id').niceSelect('update');
                }
                $('#branch_id').trigger('change'); // Trigger change event for any listeners
            }
        })
        $(document).on('change', '#addStudentImage', function(event) {
            $('#studentImageShow').removeClass('d-none');
            if (this.files && this.files[0]) {
                placeholderPhotoInput.value = this.files[0].name; // Directly set the filename
            } else {
                placeholderPhotoInput.value = ''; // Clear if no file selected
            }
            imageChangeWithFile($(this)[0], '#studentImageShow');
        });
        $(document).on('change', '#addFatherImage', function(event) {
            $('#fatherImageShow').removeClass('d-none');
            getFileName($(this).val(), '#placeholderFathersName');
            imageChangeWithFile($(this)[0], '#fatherImageShow');
        });
        $(document).on('change', '#addMotherImage', function(event) {
            $('#motherImageShow').removeClass('d-none');
            getFileName($(this).val(), '#placeholderMothersName');
            imageChangeWithFile($(this)[0], '#motherImageShow');
        });
        $(document).on('change', '#addGuardianImage', function(event) {
            $('#guardianImageShow').removeClass('d-none');
            getFileName($(this).val(), '#placeholderGuardiansName');
            imageChangeWithFile($(this)[0], '#guardianImageShow');
        });

        // Calculate age when date of birth changes
        $(document).on('change', '#date_of_birth', function() {
            let dob = $(this).val();
            if (dob) {
                let birthDate = new Date(dob);
                let today = new Date();
                let years = today.getFullYear() - birthDate.getFullYear();
                let months = today.getMonth() - birthDate.getMonth();

                if (months < 0 || (months === 0 && today.getDate() < birthDate.getDate())) {
                    years--;
                    months += 12;
                }

                $('#age_display').val(years + ' years, ' + months + ' months');
                $('#stu_age_hidden').val(years); // Set the hidden input for stu_age
            } else {
                $('#age_display').val('');
                $('#stu_age_hidden').val('');
            }
        });

        // Trigger age calculation on page load if date of birth is already set
        $('#date_of_birth').trigger('change');

        // Camera and photo capture functionality
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const startCameraButton = document.getElementById('startCameraButton');
        const captureButton = document.getElementById('captureButton');
        const saveCapturedButton = document.getElementById('saveCapturedButton');
        const imagePreview = document.getElementById('imagePreview');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const addStudentImageInput = document.getElementById('addStudentImage');
        const placeholderPhotoInput = document.getElementById('placeholderPhoto');

        let stream;

        startCameraButton.addEventListener('click', async () => {
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });
                video.srcObject = stream;
                video.style.display = 'block';
                canvas.style.display = 'none';
                startCameraButton.style.display = 'none';
                captureButton.style.display = 'inline-block';
                saveCapturedButton.style.display = 'none';
                imagePreviewContainer.style.display = 'none';
                addStudentImageInput.value = ''; // Clear any previously selected file
                placeholderPhotoInput.value = ''; // Clear placeholder
            } catch (err) {
                console.error("Error accessing camera: ", err);
                alert("Could not access camera. Please ensure you have a camera and have granted permission.");
            }
        });

        captureButton.addEventListener('click', () => {
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            video.style.display = 'none';
            canvas.style.display = 'block';
            captureButton.style.display = 'none';
            saveCapturedButton.style.display = 'inline-block';
            startCameraButton.style.display = 'inline-block'; // Allow restarting camera
        });

        saveCapturedButton.addEventListener('click', () => {
            const dataURL = canvas.toDataURL('image/png');

            // Display the captured image in studentImageShow
            $('#studentImageShow').attr('src', dataURL).removeClass('d-none');
            imagePreviewContainer.style.display = 'none'; // Hide the imagePreviewContainer

            // Update the placeholder input with the filename
            placeholderPhotoInput.value = 'captured_photo.png';

            // Convert dataURL to a File object and assign to the file input
            fetch(dataURL)
                .then(res => res.blob())
                .then(blob => {
                    const file = new File([blob], 'captured_photo.png', {
                        type: 'image/png'
                    });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    addStudentImageInput.files = dataTransfer.files;
                });

            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            video.style.display = 'none';
            canvas.style.display = 'none';
            saveCapturedButton.style.display = 'none';
            startCameraButton.style.display = 'inline-block';
        });

        // Handle file input change for browsing photos
        addStudentImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#studentImageShow').attr('src', e.target.result).removeClass('d-none');
                    imagePreviewContainer.style.display = 'none'; // Hide the imagePreviewContainer
                    video.style.display = 'none';
                    canvas.style.display = 'none';
                    startCameraButton.style.display = 'inline-block';
                    captureButton.style.display = 'none';
                    saveCapturedButton.style.display = 'none';
                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                    }
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                $('#studentImageShow').addClass('d-none').attr('src', ''); // Clear image if no file selected
                imagePreviewContainer.style.display = 'none';
            }
        });
    </script>
@endsection
