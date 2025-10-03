@extends('layouts.app')
@section('title')
    @lang('student.student_category')
@endsection
@section('mainContent')
    @if ($errors->any())
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
            @foreach ($errors->all() as $error)
                <div class="toast align-items-center text-bg-danger border-0 mb-2" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            {{ $error }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
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

                                <div class="offset-lg-3 col-lg-3 text-right col-sm-6 col-7">
                                    <a href="" class="primary-btn small fix-gr-bg">
                                        <span class="ti-plus pr-2"></span>
                                        @lang('student.import_student')
                                    </a>
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
                                                <div class="row pt-4 row-gap-24">
                                                    <div class="col-lg-6">
                                                        <div class="form-section">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="main-title">
                                                                        <h4 class="stu-sub-head">@lang('student.academic_info')</h4>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 mt-4">
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



                                                                <div class="col-lg-6 mt-4">
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
                                                                </div>



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
                                                                                            autocomplete="off">
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
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-section">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="main-title">
                                                                        <h4 class="stu-sub-head">@lang('student.personal_info')</h4>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.first_name')

                                                                            <span class="text-danger"> *</span>

                                                                        </label>
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}"
                                                                            type="text" name="first_name"
                                                                            value="{{ old('first_name') }}">


                                                                        @if ($errors->has('first_name'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('first_name') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.last_name')

                                                                            <span class="text-danger"> *</span>

                                                                        </label>
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}"
                                                                            type="text" name="last_name"
                                                                            value="{{ old('last_name') }}">


                                                                        @if ($errors->has('last_name'))
                                                                            <span class="text-danger">
                                                                                {{ $errors->first('last_name') }}
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

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input">
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



                                                                <div class="col-md-12 mt-15">
                                                                    <img class="d-none previewImageSize" src=""
                                                                        alt="" id="studentImageShow"
                                                                        height="100%" width="100%">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-section">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="main-title">
                                                                        <h4 class="stu-sub-head">@lang('student.contact_info')</h4>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('common.email_address')

                                                                            <span class="text-danger"> *</span>

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
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.phone_number')

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


                                                                <div class="row mb-40 d-none" id="exitStudent">
                                                                    <div class="col-lg-12">
                                                                        <input type="checkbox" id="edit_info"
                                                                            value="yes" class="common-checkbox"
                                                                            name="edit_info">
                                                                        <label for="edit_info"
                                                                            class="text-danger">@lang('student.student_already_exit_this_phone_number/email_are_you_to_edit_student_parent_info')</label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-12">


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


                                                                        <div class="col-lg-6 mt-4">
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

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
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
                                                    </div>
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
                                                                        <div class="col-lg-5">
                                                                            <button
                                                                                class="primary-btn-small-input primary-btn small fix-gr-bg"
                                                                                type="button" data-toggle="modal"
                                                                                data-target="#editStudent">
                                                                                <span class="ti-plus pr-2"></span>
                                                                                @lang('student.add_parents')
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <input type="hidden" name="staff_parent" id="staff_parent">
                                                        <!-- Start Sibling Add Modal -->
                                                        <div class="modal fade admin-query" id="editStudent">
                                                            <div class="modal-dialog small-modal modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">
                                                                            @lang('student.select_sibling')
                                                                        </h4>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal">&times;</button>
                                                                    </div>

                                                                    <div class="modal-body">
                                                                        <div class="container-fluid">
                                                                            <form action="">

                                                                                <div class="row mt-15" id="siblingParent">
                                                                                    <div class="col-lg-12">

                                                                                        <div class="row">
                                                                                            <div class="col-lg-12 sibling_required_error"
                                                                                                id="sibling_required_error">

                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row mt-15">
                                                                                            <div class="col-lg-12">
                                                                                                <label
                                                                                                    class="primary_input_label">Intake
                                                                                                    <span
                                                                                                        class="text-danger">*</span></label>
                                                                                                <select
                                                                                                    class="primary_select form-control @error('intake_id') is-invalid @enderror"
                                                                                                    name="intake_id">
                                                                                                    <option value="">
                                                                                                        Select Intake *
                                                                                                    </option>
                                                                                                    @foreach ($intakes as $intake)
                                                                                                        <option
                                                                                                            value="{{ $intake->id }}"
                                                                                                            {{ old('intake_id', $topic->intake_id ?? '') == $intake->id ? 'selected' : '' }}>
                                                                                                            {{ $intake->name }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="row mt-15">
                                                                                            <div class="col-lg-12"
                                                                                                id="sibling_section_div">
                                                                                                <label
                                                                                                    class="primary_input_label"
                                                                                                    for="branch">
                                                                                                    @lang('common.branch')
                                                                                                    <span
                                                                                                        class="text-danger">*</span>
                                                                                                </label>
                                                                                                <select
                                                                                                    class="primary_select"
                                                                                                    name="branch"
                                                                                                    id="branch">
                                                                                                    <option value="">
                                                                                                        Select Branch
                                                                                                    </option>
                                                                                                    @foreach ($branches as $branch)
                                                                                                        <option
                                                                                                            value="{{ $branch->id }}">
                                                                                                            {{ $branch->name }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row mt-15">
                                                                                            <div class="col-lg-12"
                                                                                                id="sibling_name_div">
                                                                                                <label
                                                                                                    for="primary_input_label">@lang('student.sibling')
                                                                                                    <span
                                                                                                        class="text-danger">
                                                                                                        *</span></label>
                                                                                                <select
                                                                                                    class="primary_select"
                                                                                                    name="select_sibling_name"
                                                                                                    id="select_sibling_name">
                                                                                                    <option
                                                                                                        data-display="@lang('student.sibling') *"
                                                                                                        value="">
                                                                                                        @lang('student.sibling')
                                                                                                        *
                                                                                                    </option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row mt-15 d-none"
                                                                                    id="staffParent">
                                                                                    <div class="col-lg-12">
                                                                                        <div class="row">
                                                                                            <div class="col-lg-12 sibling_required_error"
                                                                                                id="sibling_required_error">

                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-12"
                                                                                                id="staff_class_div">
                                                                                                <select
                                                                                                    class="primary_select"
                                                                                                    id="select_staff_parent">
                                                                                                    <option
                                                                                                        data-display="@lang('hr.select_staff') *"
                                                                                                        value="">
                                                                                                        @lang('hr.select_staff')
                                                                                                        *
                                                                                                    </option>
                                                                                                    option
                                                                                                    value="1">
                                                                                                    1
                                                                                                    </option>
                                                                                                    <option value="2">
                                                                                                        2
                                                                                                    </option>

                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div
                                                                                        class="col-lg-12 text-center mt-40">
                                                                                        <div
                                                                                            class="mt-40 d-flex justify-content-between">
                                                                                            <button type="button"
                                                                                                class="primary-btn tr-bg"
                                                                                                data-dismiss="modal">@lang('common.cancel')</button>

                                                                                            <button
                                                                                                class="primary-btn fix-gr-bg"
                                                                                                id="save_button_parent"
                                                                                                data-dismiss="modal"
                                                                                                type="button">@lang('common.save_information')</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
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
                                                                                        name="relationButton"
                                                                                        id="relationFather" value="F"
                                                                                        class="common-radio relationButton"
                                                                                        {{ old('relationButton') == 'F' ? 'checked' : '' }}>
                                                                                    <label
                                                                                        for="relationFather">@lang('student.father')</label>
                                                                                </div>
                                                                                <div class="mr-30">
                                                                                    <input type="radio"
                                                                                        name="relationButton"
                                                                                        id="relationMother" value="M"
                                                                                        class="common-radio relationButton"
                                                                                        {{ old('relationButton') == 'M' ? 'checked' : '' }}>
                                                                                    <label
                                                                                        for="relationMother">@lang('student.mother')</label>
                                                                                </div>
                                                                                <div>
                                                                                    <input type="radio"
                                                                                        name="relationButton"
                                                                                        id="relationOther" value="O"
                                                                                        class="common-radio relationButton"
                                                                                        {{ old('relationButton') != '' ? (old('relationButton') == 'O' ? 'checked' : '') : 'checked' }}>
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
                                                                                *</span>

                                                                        </label>
                                                                        <input class="primary_input_field read-only-input"
                                                                            type="text" placeholder="Relation"
                                                                            name="relation" id="relation" value="Other"
                                                                            readonly>

                                                                        <input type="hidden" name="relationButton"
                                                                            id="relationButtonHidden" value="O">

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
                                                                                *</span>

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
                                                                </div>

                                                                <div class="col-md-12 mt-15">
                                                                    <img class="d-none previewImageSize" src=""
                                                                        alt="" id="guardianImageShow"
                                                                        height="100%" width="100%">
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.guardian_phone')

                                                                            <span class="text-danger">
                                                                                *</span>

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
                                                                                *</span>

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
                                                                                *</span>

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
                                                                            value="{{ old('national_id_number') }}">



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
                                                                                    value="{{ old('document_title_1') }}">
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
                                                                                        name="document_title_1"
                                                                                        id="placeholderFileOneName"
                                                                                        placeholder="01  document_file_1 * "
                                                                                        readonly="">
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
                                                                                    value="{{ old('document_title_2') }}">


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
                                                                                *
                                                                            </span>
                                                                        </label>
                                                                        <select
                                                                            class="primary_select form-control{{ $errors->has('dormitory_name') ? ' is-invalid' : '' }}"
                                                                            name="dormitory_name" id="SelectDormitory">
                                                                            <option data-display="@lang('dormitory.dormitory_name') *"
                                                                                value="">
                                                                                @lang('dormitory.dormitory_name')

                                                                            </option>
                                                                            <option value="1"> 1 </option>
                                                                            <option value="2"> 2 </option>
                                                                        </select>

                                                                    </div>
                                                                    @if ($errors->has('dormitory_name'))
                                                                        <span class="text-danger">
                                                                            {{ $errors->first('dormitory_name') }}
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
        </div>
    </section>
@endsection
@include('components.partials.date_picker_css_js')
@section('script')
    <script src="{{ asset('public/backEnd/js/croppie.js') }}"></script>
    <script src="{{ asset('public/backEnd/js/st_addmision.js') }}"></script>
    <script>
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
                    $(placeholder).attr('placeholder', '');
                    $(placeholder).attr('placeholder', filename);
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

        })
        $(document).on('change', '#addStudentImage', function(event) {
            $('#studentImageShow').removeClass('d-none');
            getFileName($(this).val(), '#placeholderPhoto');
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
    </script>
@endsection
