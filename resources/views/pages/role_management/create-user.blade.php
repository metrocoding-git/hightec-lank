@extends('layouts.app')

@push('css')
    <style>
        /* ===== Modern refresh: breadcrumb, cards, table (Section 35) ===== */
        .sms-breadcrumb {
            position: relative;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            padding: 22px 30px;
            overflow: hidden;
        }

        .sms-breadcrumb::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            background: linear-gradient(180deg, var(--gradient_1), var(--gradient_2));
        }

        .sms-breadcrumb .row {
            align-items: center;
        }

        .sms-breadcrumb h1 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
            color: #1c1c1c;
        }

        .sms-breadcrumb .bc-pages a {
            color: #7a7f87;
            font-size: 14px;
            text-decoration: none;
        }

        .sms-breadcrumb .bc-pages a:hover {
            color: var(--gradient_2);
        }

        .sms-breadcrumb .bc-pages a:not(:last-child)::after {
            content: '\203A';
            margin: 0 8px;
            color: #c3c7cf;
        }

        .sms-breadcrumb .bc-pages a:last-child {
            font-weight: 600;
            color: #1c1c1c;
        }

        .white-box {
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        .white-box .main-title h3::after {
            content: '';
            display: block;
            width: 46px;
            height: 3px;
            margin-top: 8px;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2));
        }

        #table_id thead tr th {
            background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2));
            color: #fff;
            border: none;
            white-space: nowrap;
        }

        #table_id tbody tr:hover {
            background-color: #f7f9fc;
        }

        #table_id tbody tr td {
            vertical-align: middle;
        }

        .text-muted {
            color: #9aa1ac !important;
        }

        /* ===== "More than beautiful" elevation (Section 39) ===== */
        .cu-card {
            padding: 34px 36px 38px;
        }

        .form-section-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 34px 0 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid #eef0f4;
        }

        .form-section-header:first-of-type {
            margin-top: 4px;
        }

        .form-section-header .fsh-icon {
            flex: 0 0 auto;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #f0f2f7;
            background: color-mix(in srgb, var(--gradient_1) 12%, #ffffff);
            color: var(--gradient_1);
            font-size: 16px;
        }

        .form-section-header h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #1c1c1c;
            letter-spacing: .2px;
        }

        .form-section-header .fsh-sub {
            display: block;
            margin-top: 2px;
            font-size: 12px;
            font-weight: 400;
            color: #9aa1ac;
        }

        .optional-pill {
            display: inline-block;
            margin-left: 8px;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .3px;
            text-transform: uppercase;
            color: var(--gradient_2);
            background: color-mix(in srgb, var(--gradient_2) 10%, #ffffff);
            vertical-align: middle;
        }

        #create-user-form .primary_input_field,
        #create-user-form select.primary_select,
        #create-user-form select.form-control {
            border-radius: 8px;
            border: 1px solid #e3e6ec;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        #create-user-form .primary_input_field:focus,
        #create-user-form select.primary_select:focus,
        #create-user-form select.form-control:focus {
            border-color: var(--gradient_1);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--gradient_1) 15%, transparent);
            outline: none;
        }

        #create-user-form .primary_input_label {
            font-weight: 600;
            font-size: 13px;
            color: #4b5160;
            margin-bottom: 6px;
        }

        #student_fields {
            border-radius: 10px;
            background: #fafbfd;
            border: 1px dashed #e3e6ec;
            padding: 22px 22px 4px;
        }

        #create-user-form button[type="submit"].primary-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 30px;
            border-radius: 8px;
            box-shadow: 0 6px 18px color-mix(in srgb, var(--gradient_1) 30%, transparent);
            transition: transform .15s ease, box-shadow .15s ease;
        }

        #create-user-form button[type="submit"].primary-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px color-mix(in srgb, var(--gradient_1) 40%, transparent);
        }
    </style>
@endpush

@section('title')
    Create User
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Create User</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="{{ route('role-management.user-list') }}">@lang('role.user_list')</a>
                    <a href="#">Create User</a>
                </div>
            </div>
        </div>
    </section>

    {{-- PERMISSION (Section 35): gate the Create User submit button --}}
    @php
        $canStoreUser = Auth::user()->hasPermission('role-management.store-user');
    @endphp

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="white-box cu-card">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-sm-6 col-5">
                            <div class="main-title my-0 xs_mt_0 mt_0_sm">
                                <h3 class="mb-15">Create User</h3>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-4 row-gap-24">
                        <div class="col-lg-12">
                            <form id="create-user-form" action="{{ route('role-management.store-user') }}" method="POST">
                                @csrf

                                <div class="form-section-header">
                                    <span class="fsh-icon"><i class="fas fa-user-shield"></i></span>
                                    <div>
                                        <h4>Role &amp; Branch Assignment</h4>
                                        <span class="fsh-sub">Decide what this user can access and where</span>
                                    </div>
                                </div>

                                <div class="row pt-4 row-gap-24">
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-3">
                                            <label class="primary_input_label" for="role_id">Select Role:</label>
                                            <select name="role_id" id="role_id" class="primary_select form-control">
                                                <option value="">Select Role</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-3">
                                            <label class="primary_input_label" for="branch_ids">Select Branches:</label>
                                            <select name="branch_ids[]" id="branch_ids" class="primary_select form-control"
                                                multiple>
                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="student_fields_header" style="display: none;" class="form-section-header">
                                    <span class="fsh-icon"><i class="fas fa-user-graduate"></i></span>
                                    <div>
                                        <h4>Student Information</h4>
                                        <span class="fsh-sub">Link this account to a student record</span>
                                    </div>
                                </div>
                                <div id="student_fields" style="display: none;" class="my-4">
                                    <div class="primary_input mb-3">
                                        <label class="primary_input_label" for="search_student">Search Student:</label>
                                        <select name="search_student" id="search_student"
                                            class="primary_select form-control">
                                            <option value="">Select Student</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}">{{ $student->first_name }}
                                                    {{ $student->last_name }} ({{ $student->admission_number }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="new_student_input_fields">
                                        <div class="row pt-4 row-gap-24">
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label" for="admission_number">Admission
                                                        Number:</label>
                                                    <input type="text" name="admission_number" id="admission_number"
                                                        class="primary_input_field form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label" for="national_id_number">National ID
                                                        Number:</label>
                                                    <input type="text" name="national_id_number" id="national_id_number"
                                                        class="primary_input_field form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="existing_student_display_fields" style="display: none;">
                                        <div class="row pt-4 row-gap-24">
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label" for="existing_first_name">First Name
                                                        (Existing):</label>
                                                    <input type="text" name="existing_first_name"
                                                        id="existing_first_name" class="primary_input_field form-control"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label" for="existing_last_name">Last Name
                                                        (Existing):</label>
                                                    <input type="text" name="existing_last_name" id="existing_last_name"
                                                        class="primary_input_field form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label"
                                                        for="existing_admission_number">Admission Number (Existing):</label>
                                                    <input type="text" name="existing_admission_number"
                                                        id="existing_admission_number"
                                                        class="primary_input_field form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label"
                                                        for="existing_national_id_number">National ID Number
                                                        (Existing):</label>
                                                    <input type="text" name="existing_national_id_number"
                                                        id="existing_national_id_number"
                                                        class="primary_input_field form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="primary_input mb-3">
                                        <label class="primary_input_label" for="course_ids">Courses <span class="optional-pill">Optional</span></label>
                                        <select name="course_ids[]" id="course_ids" class="primary_select form-control" multiple>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->id }}">{{ $course->name }}{{ $course->code ? ' (' . $course->code . ')' : '' }}</option>
                                            @endforeach
                                        </select>
                                        <span class="fsh-sub" style="display:block;margin-top:6px;">You can select one or more courses to associate with this student now, or add them later.</span>
                                    </div>
                                </div>

                                <div id="non_student_fields">
                                    <div class="row pt-4 row-gap-24">
                                        <div class="col-lg-6">
                                            <div class="primary_input mb-3">
                                                <label class="primary_input_label" for="first_name">First Name:</label>
                                                <input type="text" name="first_name" id="first_name"
                                                    class="primary_input_field form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="primary_input mb-3">
                                                <label class="primary_input_label" for="last_name">Last Name:</label>
                                                <input type="text" name="last_name" id="last_name"
                                                    class="primary_input_field form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section-header">
                                    <span class="fsh-icon"><i class="fas fa-key"></i></span>
                                    <div>
                                        <h4>Account Credentials</h4>
                                        <span class="fsh-sub">Used to sign in to this account</span>
                                    </div>
                                </div>

                                <div class="primary_input mb-3">
                                    <label class="primary_input_label" for="email_address">Email Address:</label>
                                    <input type="email" name="email_address" id="email_address"
                                        class="primary_input_field form-control">
                                </div>
                                <div class="primary_input mb-3">
                                    <label class="primary_input_label" for="password">Password:</label>
                                    <input type="password" name="password" id="password"
                                        class="primary_input_field form-control">
                                </div>
                                <div class="primary_input mb-3">
                                    <label class="primary_input_label" for="password_confirmation">Confirm
                                        Password:</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="primary_input_field form-control">
                                </div>

                                @if ($canStoreUser)
                                    <button type="submit" class="primary-btn fix-gr-bg mt-4">Create User</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BUG FIX (Section 35): removed a redundant jQuery CDN include here.
         layouts.app already loads jQuery globally; loading a second copy can
         silently overwrite $.fn extensions registered by plugins (select2,
         datepicker, etc.) that ran against the first copy. --}}
    <script>
        $(document).ready(function() {
            $(document).on('change', '#role_id', function() {
                var studentFields = $('#student_fields');
                var newStudentInputFields = $('#new_student_input_fields');
                var searchStudentDropdown = $('#search_student');
                var existingStudentDisplayFields = $('#existing_student_display_fields');

                if ($(this).find('option:selected').text() === 'Student') {
                    studentFields.show();
                    $('#student_fields_header').show();
                    newStudentInputFields.show(); // Show new student fields by default
                    searchStudentDropdown.val(''); // Clear search student selection
                    existingStudentDisplayFields.hide(); // Hide existing student display fields
                    // Clear all student-related fields
                    $('#first_name').val('');
                    $('#last_name').val('');
                    $('#existing_first_name').val('');
                    $('#existing_last_name').val('');
                    $('#existing_admission_number').val('');
                    $('#existing_national_id_number').val(''); // Clear existing national ID
                    $('#admission_number').val('');
                    $('#national_id_number').val('');
                    $('#email_address').val('');
                    // Make admission_number and national_id_number editable for new student input
                    $('#admission_number').prop('readonly', false);
                    $('#national_id_number').prop('readonly', false);
                    $('#first_name').prop('readonly', false);
                    $('#last_name').prop('readonly', false);
                    $('#non_student_fields').hide(); // Hide non-student fields when 'Student' is selected
                } else {
                    studentFields.hide();
                    $('#student_fields_header').hide();
                    newStudentInputFields.hide();
                    existingStudentDisplayFields.hide();
                    // Clear any course selection made while 'Student' was picked
                    $('#course_ids').val(null);
                    $('#course_ids').niceSelect('update');
                    $('#non_student_fields').show(); // Show non-student fields when role is not 'Student'
                    // Ensure first_name and last_name are editable when non-student role is selected
                    $('#first_name').prop('readonly', false);
                    $('#last_name').prop('readonly', false);
                }
            });

            $(document).on('change', '#search_student', function() {
                var studentId = $(this).val();
                var newStudentInputFields = $('#new_student_input_fields');
                var existingStudentDisplayFields = $('#existing_student_display_fields');

                if (studentId) {
                    newStudentInputFields.hide(); // Hide new student fields
                    existingStudentDisplayFields.show(); // Show existing student display fields
                    fetch('/role-management/get-student-details/' + studentId)
                        .then(response => response.json())
                        .then(data => {
                            $('#existing_first_name').val(data.first_name);
                            $('#existing_last_name').val(data.last_name);
                            $('#existing_admission_number').val(data.admission_number);
                            $('#existing_national_id_number').val(data.national_id_number ||
                                ''); // Populate existing national ID
                            $('#email_address').val(data.email_address);
                            // Clear new student input fields
                            $('#first_name').val('');
                            $('#last_name').val('');
                            $('#admission_number').val('');
                            $('#national_id_number').val('');
                            // Make new student input fields readonly when an existing student is selected
                            $('#first_name').prop('readonly', true);
                            $('#last_name').prop('readonly', true);
                            $('#admission_number').prop('readonly', true);
                            $('#national_id_number').prop('readonly', true);
                        });
                } else {
                    newStudentInputFields.show(); // Show new student fields
                    existingStudentDisplayFields.hide(); // Hide existing student display fields
                    // Clear existing fields
                    $('#existing_first_name').val('');
                    $('#existing_last_name').val('');
                    $('#existing_admission_number').val('');
                    $('#existing_national_id_number').val(''); // Clear existing national ID
                    $('#national_id_number').val('');
                    $('#admission_number').val('');
                    $('#email_address').val(''); // Clear email address when "Select Student" is chosen
                    // Make new student input fields editable for new student input
                    $('#first_name').prop('readonly', false);
                    $('#last_name').prop('readonly', false);
                    $('#admission_number').prop('readonly', false);
                    $('#national_id_number').prop('readonly', false);
                }
            });

            // Initial state on page load
            if ($('#role_id').find('option:selected').text() === 'Student') {
                $('#student_fields').show();
                $('#student_fields_header').show();
                $('#non_student_fields').hide();
            } else {
                $('#student_fields').hide();
                $('#student_fields_header').hide();
                $('#non_student_fields').show();
            }
        });
    </script>
@endsection