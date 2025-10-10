@extends('layouts.app')
@section('title')
    Create User
@endsection
@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="white-box">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-sm-6 col-5">
                            <div class="main-title my-0 xs_mt_0 mt_0_sm">
                                <h3 class="mb-15">Create User</h3>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-4 row-gap-24">
                        <div class="col-lg-12">
                            <form action="{{ route('role-management.store-user') }}" method="POST">
                                @csrf
                                <div class="primary_input mb-3">
                                    <label class="primary_input_label" for="role_id">Select Role:</label>
                                    <select name="role_id" id="role_id" class="primary_select form-control">
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="student_fields" style="display: none;" class="my-4">
                                    <div class="primary_input mb-3">
                                        <label class="primary_input_label" for="search_student">Search Student:</label>
                                        <select name="search_student" id="search_student" class="primary_select form-control">
                                            <option value="">Select Student</option>
                                            @foreach($students as $student)
                                                <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }} ({{ $student->admission_number }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="new_student_input_fields">
                                        <div class="row pt-4 row-gap-24">
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label" for="first_name">First Name:</label>
                                                    <input type="text" name="first_name" id="first_name" class="primary_input_field form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label" for="last_name">Last Name:</label>
                                                    <input type="text" name="last_name" id="last_name" class="primary_input_field form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label" for="admission_number">Admission Number:</label>
                                                    <input type="text" name="admission_number" id="admission_number" class="primary_input_field form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label" for="national_id_number">National ID Number:</label>
                                                    <input type="text" name="national_id_number" id="national_id_number" class="primary_input_field form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="existing_student_display_fields" style="display: none;">
                                        <div class="row pt-4 row-gap-24">
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label" for="existing_first_name">First Name (Existing):</label>
                                                    <input type="text" name="existing_first_name" id="existing_first_name" class="primary_input_field form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label" for="existing_last_name">Last Name (Existing):</label>
                                                    <input type="text" name="existing_last_name" id="existing_last_name" class="primary_input_field form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label" for="existing_admission_number">Admission Number (Existing):</label>
                                                    <input type="text" name="existing_admission_number" id="existing_admission_number" class="primary_input_field form-control" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="primary_input mb-3">
                                                    <label class="primary_input_label" for="existing_national_id_number">National ID Number (Existing):</label>
                                                    <input type="text" name="existing_national_id_number" id="existing_national_id_number" class="primary_input_field form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="primary_input mb-3">
                                    <label class="primary_input_label" for="email_address">Email Address:</label>
                                    <input type="email" name="email_address" id="email_address" class="primary_input_field form-control">
                                </div>
                                <div class="primary_input mb-3">
                                    <label class="primary_input_label" for="password">Password:</label>
                                    <input type="password" name="password" id="password" class="primary_input_field form-control">
                                </div>
                                <div class="primary_input mb-3">
                                    <label class="primary_input_label" for="password_confirmation">Confirm Password:</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="primary_input_field form-control">
                                </div>

                                <button type="submit" class="primary-btn fix-gr-bg mt-4">Create User</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on('change', '#role_id', function () {
                var studentFields = $('#student_fields');
                var newStudentInputFields = $('#new_student_input_fields');
                var searchStudentDropdown = $('#search_student');
                var existingStudentDisplayFields = $('#existing_student_display_fields');

                if ($(this).find('option:selected').text() === 'Student') {
                    studentFields.show();
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
                } else {
                    studentFields.hide();
                    newStudentInputFields.hide();
                    existingStudentDisplayFields.hide();
                }
            });

            $(document).on('change', '#search_student', function () {
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
                            $('#existing_national_id_number').val(data.national_id_number || ''); // Populate existing national ID
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
        });
    </script>
@endsection
