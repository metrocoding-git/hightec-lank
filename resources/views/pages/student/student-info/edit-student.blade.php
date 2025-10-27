@extends('layouts.app')
@section('title')
    @lang('student.edit_student')
@endsection
@section('mainContent')
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
                <h1>@lang('student.edit_student')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('student.student_information')</a>
                    <a href="#">@lang('student.edit_student')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <form method="POST" action="{{ route('add-student.update', $student->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">

                        <div class="white-box">
                            <div class="row align-items-center">
                                <div class="col-lg-6 col-sm-6 col-5">
                                    <div class="main-title my-0 xs_mt_0 mt_0_sm">
                                        <h3 class="mb-15">@lang('student.edit_student')</h3>
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
                                            <button class="primary-btn fix-gr-bg submit" id="_submit_btn_admission"
                                                data-toggle="tooltip">
                                                <span class="ti-check"></span>
                                                @lang('student.update_student')
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-lg-12">
                                    <div class="student-add-form-container">
                                        <div class="tab-content">
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
                                                                                    @if (old('branch_id', $student->branch_id) == $branch->id) selected @endif>
                                                                                    {{ $branch->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('branch_id')
                                                                            <span class="text-danger">{{ $message }}</span>
                                                                        @enderror
                                                                    </div>
                                                                </div>

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
                                                                            value="{{ old('admission_number', $student->admission_number) }}"
                                                                            readonly>
                                                                        @if ($errors->has('admission_number'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('admission_number') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input mb-15">
                                                                        <label class="primary_input_label"
                                                                            for="admission_date">@lang('student.admission_date')
                                                                            <span class="text-danger"> *</span>
                                                                        </label>
                                                                        <div class="primary_datepicker_input">
                                                                            <div class="no-gutters input-right-icon">
                                                                                <div class="col">
                                                                                    <input
                                                                                        class="primary_input_field primary_input_field date form-control"
                                                                                        id="admission_date" type="date"
                                                                                        name="admission_date"
                                                                                        value="{{ old('admission_date', $student->admission_date) }}"
                                                                                        autocomplete="off">
                                                                                </div>
                                                                                <button class="btn-date"
                                                                                    data-id="#admission_date"
                                                                                    type="button">
                                                                                    <label class="m-0 p-0"
                                                                                        for="admission_date">
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
                                                                        <label class="primary_input_label"
                                                                            for="student_category_id">
                                                                            @lang('student.category') <span class="text-danger">
                                                                                *</span>
                                                                        </label>
                                                                        <select
                                                                            class="primary_select form-control{{ $errors->has('student_category_id') ? ' is-invalid' : '' }}"
                                                                            name="student_category_id"
                                                                            id="student_category_id">
                                                                            <option value="">Select Student Category
                                                                            </option>
                                                                            @foreach ($categories as $category)
                                                                                <option value="{{ $category->id }}"
                                                                                    @if (old('student_category_id', $student->student_category_id) == $category->id) selected @endif>
                                                                                    {{ $category->category_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @if ($errors->has('student_category_id'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('student_category_id') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                {{-- === ===== --}}
                                                                <div class="">
                                                                    <div class="form-section">
                                                                        <div class="row">
                                                                            <div class="col-lg-12">
                                                                                <div class="main-title">
                                                                                    <h4 class="stu-sub-head">
                                                                                        @lang('student.contact_info')</h4>
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
                                                                                        value="{{ old('email_address', $student->email_address) }}">
                                                                                    @if ($errors->has('email_address'))
                                                                                        <span
                                                                                            class="text-danger">{{ $errors->first('email_address') }}</span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-6 mt-4">
                                                                                <div class="primary_input">
                                                                                    <label class="primary_input_label"
                                                                                        for="">@lang('student.permanent_address')
                                                                                        <span class="text-danger">*</span>
                                                                                    </label>
                                                                                    <textarea class="primary_input_field form-control{{ $errors->has('permanent_address') ? ' is-invalid' : '' }}"
                                                                                        cols="0" rows="3" name="permanent_address" id="permanent_address">{{ old('permanent_address', $student->permanent_address) }}</textarea>
                                                                                    @if ($errors->has('permanent_address'))
                                                                                        <span
                                                                                            class="text-danger">{{ $errors->first('permanent_address') }}</span>
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
                                                                                        value="{{ old('phone_number', $student->phone_number) }}">
                                                                                    @if ($errors->has('phone_number'))
                                                                                        <span
                                                                                            class="text-danger">{{ $errors->first('phone_number') }}</span>
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
                                                                                        type="tel"
                                                                                        name="phone_number_2"
                                                                                        id="phone_number_2"
                                                                                        value="{{ old('phone_number_2', $student->phone_number_2) }}">
                                                                                    @if ($errors->has('phone_number_2'))
                                                                                        <span
                                                                                            class="text-danger">{{ $errors->first('phone_number_2') }}</span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-40 d-none"
                                                                                id="exitStudent">
                                                                                <div class="col-lg-12">
                                                                                    <input type="checkbox" id="edit_info"
                                                                                        value="yes"
                                                                                        class="common-checkbox"
                                                                                        name="edit_info">
                                                                                    <label for="edit_info"
                                                                                        class="text-danger">@lang('student.student_already_exit_this_phone_number/email_are_you_to_edit_student_parent_info')</label>
                                                                                </div>
                                                                            </div>

                                                                            {{-- <div class="col-lg-12">
                                                                    <div class="row mt-40">
                                                                        <div class="col-lg-12">
                                                                            <div class="main-title">
                                                                                <h4 class="stu-sub-head">@lang('student.student_address_info')
                                                                                </h4>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-lg-6 mt-4" hidden>
                                                                            <div class="primary_input ">
                                                                                <label class="primary_input_label"
                                                                                    for="">@lang('student.current_address')
                                                                                    <span class="text-danger">*</span>
                                                                                </label>
                                                                                <textarea class="primary_input_field form-control{{ $errors->has('current_address') ? ' is-invalid' : '' }}"
                                                                                    cols="0" rows="3" name="current_address" id="current_address">{{ old('current_address', $student->current_address) }}</textarea>
                                                                                @if ($errors->has('current_address'))
                                                                                    <span
                                                                                        class="text-danger">{{ $errors->first('current_address') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        
                                                                    </div>
                                                                </div> --}}
                                                                        </div>
                                                                    </div>
                                                                </div>



                                                            </div>

                                                        </div>
                                                    </div>


                                                    {{-- ==================personal info================== --}}
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
                                                                            value="{{ old('first_name', $student->first_name) }}">
                                                                        @if ($errors->has('first_name'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('first_name') }}</span>
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
                                                                            value="{{ old('last_name', $student->last_name) }}">
                                                                        @if ($errors->has('last_name'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('last_name') }}</span>
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
                                                                            @foreach ($genders as $gender)
                                                                                <option value="{{ $gender->id }}"
                                                                                    {{ old('gender_id', $student->gender_id) == $gender->id ? 'selected' : '' }}>
                                                                                    {{ $gender->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @if ($errors->has('gender_id'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('gender_id') }}</span>
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
                                                                        <div class="primary_datepicker_input">
                                                                            <div class="no-gutters input-right-icon">
                                                                                <div class="col">
                                                                                    <input
                                                                                        class="primary_input_field date form-control{{ $errors->has('date_of_birth') ? ' is-invalid' : '' }}"
                                                                                        id="date_of_birth" type="date"
                                                                                        name="date_of_birth"
                                                                                        value="{{ old('date_of_birth', $student->date_of_birth) }}"
                                                                                        autocomplete="off" readonly>
                                                                                </div>
                                                                                <button class="btn-date"
                                                                                    data-id="#date_of_birth"
                                                                                    type="button">
                                                                                    <label class="m-0 p-0"
                                                                                        for="date_of_birth">
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
                                                                            type="text" id="age_display" readonly
                                                                            value="{{ $student->age_display ?? '' }}">
                                                                        <input type="hidden" name="stu_age"
                                                                            id="stu_age_hidden"
                                                                            value="{{ $student->stu_age ?? '' }}">
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
                                                                                placeholder="@lang('common.student_photo')"
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
                                                                        <span
                                                                            class="text-danger">{{ $errors->first('photo') }}</span>
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
                                                                    @if ($student->photo)
                                                                        <img class="previewImageSize"
                                                                            src="{{ asset('storage/' . $student->photo) }}"
                                                                            alt="Student Photo" id="studentImageShow"
                                                                            height="100" width="100">
                                                                    @else
                                                                        <img class="d-none previewImageSize"
                                                                            src="" alt=""
                                                                            id="studentImageShow" height="100"
                                                                            width="100">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- ==========cobtact info=============== --}}

                                                    <div class="col-lg-6" hidden>
                                                        <div class="form-section">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="main-title">
                                                                        <h4 class="stu-sub-head">@lang('student.medical_record')</h4>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('common.blood_group')</label>
                                                                        <select
                                                                            class="primary_select form-control{{ $errors->has('blood_group_id') ? ' is-invalid' : '' }}"
                                                                            name="blood_group_id">
                                                                            <option value="">Select Blood Group
                                                                            </option>
                                                                            @foreach ($bloodGroups as $group)
                                                                                <option value="{{ $group->id }}"
                                                                                    {{ old('blood_group_id', $student->blood_group_id) == $group->id ? 'selected' : '' }}>
                                                                                    {{ $group->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        @if ($errors->has('blood_group_id'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('blood_group_id') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.height_in')</label>
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('height') ? ' is-invalid' : '' }}"
                                                                            type="text" name="height"
                                                                            value="{{ old('height', $student->height) }}">
                                                                        @if ($errors->has('height'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('height') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.weight_kg')</label>
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('weight') ? ' is-invalid' : '' }}"
                                                                            type="text" name="weight"
                                                                            value="{{ old('weight', $student->weight) }}">
                                                                        @if ($errors->has('weight'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('weight') }}</span>
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
                                                    <div class="col-lg-12 guardian_section">
                                                        <div class="form-section">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="main-title">
                                                                        <h4 class="stu-sub-head">@lang('common.guardian_info')</h4>
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
                                                                                        {{ old('relation_guardian_id', $student->relation_guardian_id) == '1' ? 'checked' : '' }}>
                                                                                    <label
                                                                                        for="relationFather">@lang('student.father')</label>
                                                                                </div>
                                                                                <div class="mr-30">
                                                                                    <input type="radio"
                                                                                        name="relation_guardian_id"
                                                                                        id="relationMother" value="2"
                                                                                        class="common-radio relationButton"
                                                                                        {{ old('relation_guardian_id', $student->relation_guardian_id) == '2' ? 'checked' : '' }}>
                                                                                    <label
                                                                                        for="relationMother">@lang('student.mother')</label>
                                                                                </div>
                                                                                <div>
                                                                                    <input type="radio"
                                                                                        name="relation_guardian_id"
                                                                                        id="relationOther" value="3"
                                                                                        class="common-radio relationButton"
                                                                                        {{ old('relation_guardian_id', $student->relation_guardian_id) == '3' ? 'checked' : '' }}>
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
                                                                            <span class="text-danger">*</span>
                                                                        </label>
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('guardians_name') ? ' is-invalid' : '' }}"
                                                                            type="text" name="guardians_name"
                                                                            id="guardians_name"
                                                                            value="{{ old('guardians_name', $student->guardians_name) }}">
                                                                        @if ($errors->has('guardians_name'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('guardians_name') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.relation_with_guardian')
                                                                        </label>
                                                                        <input class="primary_input_field read-only-input"
                                                                            type="text" placeholder="Relation"
                                                                            name="relation" id="relation"
                                                                            value="{{ old('relation', $student->relation) != '' ? old('relation', $student->relation) : 'Other' }}"
                                                                            readonly>
                                                                        <input type="hidden" name="relation_guardian_id"
                                                                            id="relationButtonHidden"
                                                                            value="{{ old('relation_guardian_id', $student->relation_guardian_id) != '' ? old('relation_guardian_id', $student->relation_guardian_id) : '3' }}">
                                                                        @if ($errors->has('relation'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('relation') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.guardian_email')
                                                                        </label>
                                                                        <input oninput="emailCheck(this)"
                                                                            class="primary_input_field form-control{{ $errors->has('guardians_email') ? ' is-invalid' : '' }}"
                                                                            type="text" name="guardians_email"
                                                                            id="guardians_email"
                                                                            value="{{ old('guardians_email', $student->guardians_email) }}">
                                                                        @if ($errors->has('guardians_email'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('guardians_email') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.guardians_photo')</label>
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
                                                                        @if ($student->guardians_photo)
                                                                            <img class="previewImageSize"
                                                                                src="{{ asset('storage/' . $student->guardians_photo) }}"
                                                                                alt="Guardian Photo" id="guardianImageShow"
                                                                                height="100" width="100">
                                                                        @else
                                                                            <img class="d-none previewImageSize"
                                                                                src="" alt=""
                                                                                id="guardianImageShow" height="100"
                                                                                width="100">
                                                                        @endif
                                                                    </div>
                                                                </div>


                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.guardian_phone')
                                                                        </label>
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('guardians_phone') ? ' is-invalid' : '' }}"
                                                                            type="text" name="guardians_phone"
                                                                            id="guardians_phone"
                                                                            value="{{ old('guardians_phone', $student->guardians_phone) }}">
                                                                        @if ($errors->has('guardians_phone'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('guardians_phone') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.guardian_occupation')
                                                                        </label>
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('guardians_occupation') ? ' is-invalid' : '' }}"
                                                                            type="text" name="guardians_occupation"
                                                                            id="guardians_occupation"
                                                                            value="{{ old('guardians_occupation', $student->guardians_occupation) }}">
                                                                        @if ($errors->has('guardians_occupation'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('guardians_occupation') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-12 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.guardian_address')
                                                                        </label>
                                                                        <textarea class="primary_input_field form-control{{ $errors->has('guardians_address') ? ' is-invalid' : '' }}"
                                                                            cols="0" rows="3" name="guardians_address" id="guardians_address">{{ old('guardians_address', $student->guardians_address) }}</textarea>
                                                                        @if ($errors->has('guardians_address'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('guardians_address') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                                        <input
                                                                            class="primary_input_field form-control{{ $errors->has('national_id_number') ? ' is-invalid' : '' }}"
                                                                            type="text" name="national_id_number"
                                                                            value="{{ old('national_id_number', $student->national_id_number) }}">
                                                                        @if ($errors->has('national_id_number'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('national_id_number') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-12 mt-4">
                                                                    <div class="primary_input ">
                                                                        <label class="primary_input_label"
                                                                            for="">@lang('student.additional_notes')</label>
                                                                        <textarea class="primary_input_field form-control{{ $errors->has('additional_notes') ? ' is-invalid' : '' }}"
                                                                            cols="0" rows="3" name="additional_notes">{{ old('additional_notes', $student->additional_notes) }}</textarea>
                                                                        @if ($errors->has('additional_notes'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('additional_notes') }}</span>
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
                                                                                    for="">@lang('student.document_01_title')</label>
                                                                                <input
                                                                                    class="primary_input_field form-control{{ $errors->has('document_title_1') ? ' is-invalid' : '' }}"
                                                                                    type="text" name="document_title_1"
                                                                                    value="{{ old('document_title_1', $student->document_title_1) }}">
                                                                                @if ($errors->has('document_title_1'))
                                                                                    <span
                                                                                        class="text-danger">{{ $errors->first('document_title_1') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 mt-2 mb-3 mb-lg-0">
                                                                            <div class="primary_input">
                                                                                <div class="primary_file_uploader">
                                                                                    <input class="primary_input_field"
                                                                                        type="text"
                                                                                        id="placeholderFileOneName"
                                                                                        placeholder="01 document_file_1"
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
                                                                                    for="">@lang('student.document_02_title')</label>
                                                                                <input
                                                                                    class="primary_input_field form-control{{ $errors->has('document_title_2') ? ' is-invalid' : '' }}"
                                                                                    type="text" name="document_title_2"
                                                                                    value="{{ old('document_title_2', $student->document_title_2) }}">
                                                                                @if ($errors->has('document_title_2'))
                                                                                    <span
                                                                                        class="text-danger">{{ $errors->first('document_title_2') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 mt-2 mb-3 mb-lg-0">
                                                                            <div class="primary_input">
                                                                                <div class="primary_file_uploader">
                                                                                    <input class="primary_input_field"
                                                                                        type="text"
                                                                                        id="placeholderFileTwoName"
                                                                                        placeholder="02 document_file_2"
                                                                                        readonly=""
                                                                                        value="{{ isset($student) ? $student->document_file_2 : '' }}">
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
                                                                                    for="">@lang('student.document_03_title')</label>
                                                                                <input
                                                                                    class="primary_input_field form-control{{ $errors->has('document_title_3') ? ' is-invalid' : '' }}"
                                                                                    type="text" name="document_title_3"
                                                                                    value="{{ old('document_title_3', $student->document_title_3) }}">
                                                                                @if ($errors->has('document_title_3'))
                                                                                    <span
                                                                                        class="text-danger">{{ $errors->first('document_title_3') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 mt-2 mb-3 mb-lg-0">
                                                                            <div class="primary_input">
                                                                                <div class="primary_file_uploader">
                                                                                    <input class="primary_input_field"
                                                                                        type="text"
                                                                                        id="placeholderFileThreeName"
                                                                                        placeholder="03 document_file_3"
                                                                                        readonly=""
                                                                                        value="{{ isset($student) ? $student->document_file_3 : '' }}">
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
                                                                                    for="">@lang('student.document_04_title')</label>
                                                                                <input
                                                                                    class="primary_input_field form-control{{ $errors->has('document_title_4') ? ' is-invalid' : '' }}"
                                                                                    type="text" name="document_title_4"
                                                                                    value="{{ old('document_title_4', $student->document_title_4) }}">
                                                                                @if ($errors->has('document_title_4'))
                                                                                    <span
                                                                                        class="text-danger">{{ $errors->first('document_title_4') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-12 mt-2 mb-3 mb-lg-0">
                                                                            <div class="primary_input">
                                                                                <div class="primary_file_uploader">
                                                                                    <input class="primary_input_field"
                                                                                        type="text"
                                                                                        id="placeholderFileFourName"
                                                                                        placeholder="04 document_file_4"
                                                                                        readonly=""
                                                                                        value="{{ isset($student) ? $student->document_file_4 : '' }}">
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
                                                                            for="">Previous Institute
                                                                            Information</label>
                                                                        <textarea
                                                                            class="primary_input_field form-control{{ $errors->has('previous_institute_details') ? ' is-invalid' : '' }}"
                                                                            cols="0" rows="5" name="previous_institute_details">{{ old('previous_institute_details', $student->previous_institute_details) }}</textarea>
                                                                        @if ($errors->has('previous_institute_details'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('previous_institute_details') }}</span>
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
                                                                        <h4 class="stu-sub-head">@lang('student.dormitory_info')</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-15 mt-30">
                                                                <div class="col-lg-12">
                                                                    <div class="primary_input ">
                                                                        <label
                                                                            for="primary_input_label">@lang('dormitory.dormitory')</label>
                                                                        <select
                                                                            class="primary_select form-control{{ $errors->has('dormitory_id') ? ' is-invalid' : '' }}"
                                                                            name="dormitory_id" id="SelectDormitory">
                                                                            <option data-display="@lang('dormitory.dormitory_name')"
                                                                                value="">@lang('dormitory.dormitory_name')</option>
                                                                            <option value="1"
                                                                                {{ old('dormitory_id', $student->dormitory_id) == '1' ? 'selected' : '' }}>
                                                                                1</option>
                                                                            <option value="2"
                                                                                {{ old('dormitory_id', $student->dormitory_id) == '2' ? 'selected' : '' }}>
                                                                                2</option>
                                                                        </select>
                                                                    </div>
                                                                    @if ($errors->has('dormitory_id'))
                                                                        <span
                                                                            class="text-danger">{{ $errors->first('dormitory_id') }}</span>
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
    <script>
        $(document).ready(function() {
            // Age calculation
            $('#date_of_birth').on('change', function() {
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
                    $('#stu_age_hidden').val(years);
                } else {
                    $('#age_display').val('');
                    $('#stu_age_hidden').val('');
                }
            });

            // Camera functionality
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const startCameraButton = document.getElementById('startCameraButton');
            const captureButton = document.getElementById('captureButton');
            const saveCapturedButton = document.getElementById('saveCapturedButton');
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
                } catch (err) {
                    console.error("Error accessing camera: ", err);
                    alert(
                        "Could not access camera. Please ensure you have a camera and have granted permission."
                        );
                }
            });

            captureButton.addEventListener('click', () => {
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                video.style.display = 'none';
                canvas.style.display = 'block';
                captureButton.style.display = 'none';
                saveCapturedButton.style.display = 'inline-block';
                startCameraButton.style.display = 'inline-block';
            });

            saveCapturedButton.addEventListener('click', () => {
                const dataURL = canvas.toDataURL('image/png');
                $('#studentImageShow').attr('src', dataURL).removeClass('d-none');
                placeholderPhotoInput.value = 'captured_photo.png';

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

            // File input change handlers
            $(document).on('change',
                '#addStudentImage, #addGuardianImage, #document_file_1, #document_file_2, #document_file_3, #document_file_4',
                function() {
                    let fileName = $(this).val().split('\\').pop();
                    let placeholderId = '#' + $(this).attr('id') + 'Name';
                    if ($(this).attr('id') === 'addStudentImage') {
                        placeholderId = '#placeholderPhoto';
                    } else if ($(this).attr('id') === 'addGuardianImage') {
                        placeholderId = '#placeholderGuardiansName';
                    }
                    $(placeholderId).val(fileName);

                    // Preview images
                    if (this.files && this.files[0] && this.id.includes('Image')) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var imageId = this.id === 'addStudentImage' ? '#studentImageShow' :
                                '#guardianImageShow';
                            $(imageId).attr('src', e.target.result).removeClass('d-none');
                        }.bind(this);
                        reader.readAsDataURL(this.files[0]);
                    }
                });

            // Relation button handling
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

            // Email and phone validation
            function emailCheck(input) {
                // Add email validation logic here
                console.log('Email changed:', input.value);
            }

            function phoneCheck(input) {
                // Add phone validation logic here
                console.log('Phone changed:', input.value);
            }

            // Trigger age calculation on page load
            $('#date_of_birth').trigger('change');
        });
    </script>
@endsection
