@extends('layouts.app')
@section('mainContent')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create User</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('role-management.store-user') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="role_id">Select Role:</label>
                                <select name="role_id" id="role_id" class="form-control">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="student_fields" style="display: none;">
                                <div class="form-group">
                                    <label for="search_student">Search Student:</label>
                                    <select name="search_student" id="search_student" class="form-control">
                                        <option value="">Select Student</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }} ({{ $student->admission_number }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="new_student_input_fields">
                                    <div class="form-group">
                                        <label for="first_name">First Name:</label>
                                        <input type="text" name="first_name" id="first_name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="last_name">Last Name:</label>
                                        <input type="text" name="last_name" id="last_name" class="form-control">
                                    </div>
                                </div>

                                <div id="existing_student_display_fields" style="display: none;">
                                    <div class="form-group">
                                        <label for="existing_first_name">First Name (Existing):</label>
                                        <input type="text" name="existing_first_name" id="existing_first_name" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="existing_last_name">Last Name (Existing):</label>
                                        <input type="text" name="existing_last_name" id="existing_last_name" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="existing_admission_number">Admission Number (Existing):</label>
                                        <input type="text" name="existing_admission_number" id="existing_admission_number" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="existing_national_id_number">National ID Number (Existing):</label>
                                        <input type="text" name="existing_national_id_number" id="existing_national_id_number" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="admission_number">Admission Number:</label>
                                    <input type="text" name="admission_number" id="admission_number" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="national_id_number">National ID Number:</label>
                                    <input type="text" name="national_id_number" id="national_id_number" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email_address">Email Address:</label>
                                <input type="email" name="email_address" id="email_address" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password:</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-primary">Create User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('role_id').addEventListener('change', function () {
            var studentFields = document.getElementById('student_fields');
            var newStudentInputFields = document.getElementById('new_student_input_fields');
            var searchStudentDropdown = document.getElementById('search_student');
            var existingStudentDisplayFields = document.getElementById('existing_student_display_fields');

            if (this.options[this.selectedIndex].text === 'Student') {
                studentFields.style.display = 'block';
                newStudentInputFields.style.display = 'block'; // Show new student fields by default
                searchStudentDropdown.value = ''; // Clear search student selection
                existingStudentDisplayFields.style.display = 'none'; // Hide existing student display fields
                // Clear all student-related fields
                document.getElementById('first_name').value = '';
                document.getElementById('last_name').value = '';
                document.getElementById('existing_first_name').value = '';
                document.getElementById('existing_last_name').value = '';
                document.getElementById('existing_admission_number').value = '';
                document.getElementById('existing_national_id_number').value = ''; // Clear existing national ID
                document.getElementById('admission_number').value = '';
                document.getElementById('national_id_number').value = '';
                document.getElementById('email_address').value = '';
                // Make admission_number and national_id_number editable for new student input
                document.getElementById('admission_number').readOnly = false;
                document.getElementById('national_id_number').readOnly = false;
            } else {
                studentFields.style.display = 'none';
                newStudentInputFields.style.display = 'none';
                existingStudentDisplayFields.style.display = 'none';
            }
        });

        document.getElementById('search_student').addEventListener('change', function () {
            var studentId = this.value;
            var newStudentInputFields = document.getElementById('new_student_input_fields');
            var existingStudentDisplayFields = document.getElementById('existing_student_display_fields');

            if (studentId) {
                newStudentInputFields.style.display = 'none'; // Hide new student fields
                existingStudentDisplayFields.style.display = 'block'; // Show existing student display fields
                fetch('/role-management/get-student-details/' + studentId)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('existing_first_name').value = data.first_name;
                        document.getElementById('existing_last_name').value = data.last_name;
                        document.getElementById('existing_admission_number').value = data.admission_number;
                        document.getElementById('existing_national_id_number').value = data.national_id_number || ''; // Populate existing national ID
                        document.getElementById('admission_number').value = data.admission_number; // Also populate the editable admission number
                        document.getElementById('national_id_number').value = data.national_id_number || ''; // Also populate the editable national ID
                        document.getElementById('email_address').value = data.email_address;
                        // Make admission_number and national_id_number read-only for existing student
                        document.getElementById('admission_number').readOnly = true;
                        document.getElementById('national_id_number').readOnly = true;
                    });
            } else {
                newStudentInputFields.style.display = 'block'; // Show new student fields
                existingStudentDisplayFields.style.display = 'none'; // Hide existing student display fields
                // Clear existing fields
                document.getElementById('existing_first_name').value = '';
                document.getElementById('existing_last_name').value = '';
                document.getElementById('existing_admission_number').value = '';
                document.getElementById('existing_national_id_number').value = ''; // Clear existing national ID
                document.getElementById('national_id_number').value = '';
                document.getElementById('admission_number').value = '';
                document.getElementById('email_address').value = '';
                // Make admission_number and national_id_number editable for new student input
                document.getElementById('admission_number').readOnly = false;
                document.getElementById('national_id_number').readOnly = false;
            }
        });
    </script>
@endsection
