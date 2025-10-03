@extends('layouts.app')
@section('title')
    Student Add Intake
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Student Add Intake</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Student Information</a>
                    <a href="#">Student Add Intake</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-8 col-md-6">
                                <div class="main-title">
                                    <h3 class="mb-15">Assign Intake to Student</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Add Student Course and Intake Button -->
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <button type="button" class="primary-btn fix-gr-bg" data-toggle="modal" data-target="#addStudentIntakeModal">
                                    <span class="ti-plus"></span> Add Student Course and Intake
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Intake List -->
            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">Student Intake List</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id" class="table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Student Name</th>
                                                <th>Admission No</th>
                                                <th>Intake Name</th>
                                                <th>Branch</th>
                                                <th>Courses</th>
                                                <th>Enrollment Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($studentIntakes as $studentIntake)
                                                <tr>
                                                    <td>{{ $studentIntake->student_name }}</td>
                                                    <td>{{ $studentIntake->admission_number }}</td>
                                                    <td>{{ $studentIntake->intake_name }}</td>
                                                    <td>{{ $studentIntake->branch_name ?? 'N/A' }}</td>
                                                    <td>
                                                        @if(isset($studentIntake->courses) && $studentIntake->courses->count() > 0)
                                                            @foreach($studentIntake->courses as $course)
                                                                <span class="badge badge-info">{{ $course->name }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">No courses</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $studentIntake->enrolled_at ? date('Y-m-d', strtotime($studentIntake->enrolled_at)) : '-' }}</td>
                                                    <td>
                                                        <span class="badge {{ $studentIntake->status == 'pending' ? 'badge-success' : ($studentIntake->status == 'completed' ? 'badge-primary' : 'badge-warning') }}">
                                                            {{ ucfirst($studentIntake->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <x-drop-down>
                                                            {{-- <a class="dropdown-item" href="{{ route('student.add-intake.edit', $studentIntake->id) }}">
                                                                Edit
                                                            </a> --}}
                                                            <a class="dropdown-item" data-toggle="modal"
                                                               data-target="#deleteStudentIntakeModal{{ $studentIntake->id }}" href="#">
                                                                Delete
                                                            </a>
                                                        </x-drop-down>
                                                    </td>
                                                </tr>

                                                <!-- Delete Modal -->
                                                <div class="modal fade admin-query" id="deleteStudentIntakeModal{{ $studentIntake->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Remove Intake</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="text-center">
                                                                    <h4>Are you sure to remove intake?</h4>
                                                                </div>
                                                                <div class="mt-40 d-flex justify-content-between">
                                                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">
                                                                        Cancel
                                                                    </button>
                                                                    <form action="{{ route('student.add-intake.destroy', $studentIntake->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="primary-btn fix-gr-bg" type="submit">
                                                                            Remove
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">No intake assignments found</td>
                                                </tr>
                                            @endforelse
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

    <!-- Add Student Intake Modal -->
    <div class="modal fade admin-query" id="addStudentIntakeModal" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Assign Intake to Student</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="{{ route('student.add-intake-with-courses.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Student Selection -->
                            <div class="col-lg-6">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="modal_student_id">
                                        Select Student <span class="text-danger">*</span>
                                    </label>
                                    <select class="primary_select form-control{{ $errors->has('student_id') ? ' is-invalid' : '' }}"
                                            name="student_id" id="modal_student_id" required>
                                        <option value="">Select Student</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ (old('student_id', $studentIntake->student_id ?? null) == $student->id) ? 'selected' : '' }}>
                                                {{ $student->student_name }} ({{ $student->admission_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('student_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Branch Selection -->
                            <div class="col-lg-6">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="modal_branch_id">
                                        Branch <span class="text-danger">*</span>
                                    </label>
                                    <select class="primary_select form-control{{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                                            name="branch_id" id="modal_branch_id" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ (old('branch_id', $studentIntake->branch_id ?? null) == $branch->id) ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Intake Selection -->
                            <div class="col-lg-6 mt-4">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="modal_intake_id">
                                        Select Intake <span class="text-danger">*</span>
                                    </label>
                                    <select class="primary_select form-control{{ $errors->has('intake_id') ? ' is-invalid' : '' }}"
                                            name="intake_id" id="modal_intake_id" required>
                                        <option value="">Select Intake</option>
                                        @if(isset($studentIntake) && $studentIntake->intake_id)
                                            <option value="{{ $studentIntake->intake_id }}" selected>
                                                {{ $studentIntake->intake_name }} ({{ $studentIntake->branch_name ?? 'No Branch' }})
                                            </option>
                                        @endif
                                    </select>
                                    @error('intake_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Course Selection -->
                            <div class="col-lg-6 mt-4">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="modal_course_ids">
                                        Select Courses <span class="text-danger">*</span>
                                    </label>
                                    <select class="primary_select form-control{{ $errors->has('course_ids') ? ' is-invalid' : '' }}"
                                            name="course_ids[]" id="modal_course_ids" multiple required>
                                        <option value="">Select Courses</option>
                                        @if(isset($studentIntake) && isset($studentIntake->courses))
                                            @foreach($studentIntake->courses as $course)
                                                <option value="{{ $course->id }}" selected>
                                                    {{ $course->name }} ({{ $course->code }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('course_ids')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Enrollment Date -->
                            <div class="col-lg-6 mt-4" hidden>
                                <div class="primary_input">
                                    <label class="primary_input_label" for="modal_enrolled_at">
                                        Enrollment Date <span class="text-danger">*</span>
                                    </label>
                                    <input class="primary_input_field form-control{{ $errors->has('enrolled_at') ? ' is-invalid' : '' }}"
                                           type="date" name="enrolled_at" id="modal_enrolled_at"
                                           value="{{ old('enrolled_at', date('Y-m-d')) }}" required>
                                    @error('enrolled_at')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-lg-6 mt-4" >
                                <div class="primary_input">
                                    <label class="primary_input_label" for="modal_status">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="primary_select form-control{{ $errors->has('status') ? ' is-invalid' : '' }}"
                                            name="status" id="modal_status" required >
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option value="assign" {{ old('status') == 'assign' ? 'selected' : '' }}>
                                            Assign
                                        </option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                            Completed
                                        </option>
                                        <option value="dropped" {{ old('status') == 'dropped' ? 'selected' : '' }}>
                                            Dropped
                                        </option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-lg-12 mt-4">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="modal_notes">
                                        Notes
                                    </label>
                                    <textarea class="primary_input_field form-control{{ $errors->has('notes') ? ' is-invalid' : '' }}"
                                              name="notes" id="modal_notes" rows="4"
                                              placeholder="Add notes here">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="primary-btn fix-gr-bg">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@include('components.partials.data_table_js')
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')
@push('script')
 <script>
        $('#table_id').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
        });
    </script>
    

<script>
$(document).ready(function() {
    // Handle branch change event to load intakes dynamically (Modal)
    $('#modal_branch_id').on('change', function() {
        var branchId = $(this).val();
        var intakeSelect = $('#modal_intake_id');
        var courseSelect = $('#modal_course_ids');

        if (branchId) {
            // Show loading state
            intakeSelect.html('<option value="">Loading intakes...</option>');
            courseSelect.html('<option value="">Select Courses</option>');

            // Make AJAX request to get intakes for selected branch
            $.ajax({
                url: '{{ route("get-intakes-by-branch", ":branchId") }}'.replace(':branchId', branchId),
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(data) {
                    console.log('Intakes response:', data);
                    var options = '<option value="">Select Intake</option>';

                    if (data.intakes && data.intakes.length > 0) {
                        data.intakes.forEach(function(intake) {
                            options += '<option value="' + intake.id + '">' + intake.name + '</option>';
                        });
                    } else {
                        options = '<option value="">No intakes available for this branch</option>';
                    }

                    intakeSelect.html(options);

                    // Trigger niceSelect update if using niceSelect
                    if (intakeSelect.hasClass('primary_select')) {
                        intakeSelect.niceSelect('update');
                    }
                    if (courseSelect.hasClass('primary_select')) {
                        courseSelect.niceSelect('update');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading intakes:', error);
                    console.error('XHR response:', xhr.responseText);
                    intakeSelect.html('<option value="">Error loading intakes</option>');
                    courseSelect.html('<option value="">Select Courses</option>');

                    // Trigger niceSelect update if using niceSelect
                    if (intakeSelect.hasClass('primary_select')) {
                        intakeSelect.niceSelect('update');
                    }
                    if (courseSelect.hasClass('primary_select')) {
                        courseSelect.niceSelect('update');
                    }
                }
            });
        } else {
            // Reset intake and course dropdowns when no branch is selected
            intakeSelect.html('<option value="">Select Intake</option>');
            courseSelect.html('<option value="">Select Courses</option>');

            // Trigger niceSelect update if using niceSelect
            if (intakeSelect.hasClass('primary_select')) {
                intakeSelect.niceSelect('update');
            }
            if (courseSelect.hasClass('primary_select')) {
                courseSelect.niceSelect('update');
            }
        }
    });

    // Handle intake change event to load courses dynamically (Modal)
    $('#modal_intake_id').on('change', function() {
        var intakeId = $(this).val();
        var courseSelect = $('#modal_course_ids');

        if (intakeId) {
            // Show loading state
            courseSelect.html('<option value="">Loading courses...</option>');

            // Make AJAX request to get courses for selected intake
            $.ajax({
                url: '{{ route("get-courses-by-intake", ":intakeId") }}'.replace(':intakeId', intakeId),
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(data) {
                    console.log('Courses response:', data);
                    var options = '<option value="">Select Courses</option>';

                    if (data.courses && data.courses.length > 0) {
                        data.courses.forEach(function(course) {
                            options += '<option value="' + course.id + '">' + course.name + ' (' + course.code + ')</option>';
                        });
                    } else {
                        options = '<option value="">No courses available for this intake</option>';
                    }

                    courseSelect.html(options);

                    // Trigger niceSelect update if using niceSelect
                    if (courseSelect.hasClass('primary_select')) {
                        courseSelect.niceSelect('update');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading courses:', error);
                    console.error('XHR response:', xhr.responseText);
                    courseSelect.html('<option value="">Error loading courses</option>');

                    // Trigger niceSelect update if using niceSelect
                    if (courseSelect.hasClass('primary_select')) {
                        courseSelect.niceSelect('update');
                    }
                }
            });
        } else {
            // Reset course dropdown when no intake is selected
            courseSelect.html('<option value="">Select Courses</option>');

            // Trigger niceSelect update if using niceSelect
            if (courseSelect.hasClass('primary_select')) {
                courseSelect.niceSelect('update');
            }
        }
    });

    // Reset modal form when modal is closed
    $('#addStudentIntakeModal').on('hidden.bs.modal', function() {
        // Reset all form fields
        $('#modal_student_id').val('');
        $('#modal_branch_id').val('');
        $('#modal_intake_id').html('<option value="">Select Intake</option>');
        $('#modal_course_ids').html('<option value="">Select Courses</option>');
        $('#modal_enrolled_at').val('{{ date('Y-m-d') }}');
        $('#modal_status').val('pending');
        $('#modal_notes').val('');

        // Trigger niceSelect update if using niceSelect
        $('.primary_select').each(function() {
            if ($(this).hasClass('primary_select')) {
                $(this).niceSelect('update');
            }
        });

        // Clear any error messages
        $('.text-danger').remove();
        $('.is-invalid').removeClass('is-invalid');
    });

    // Auto-populate form if editing
    @if(isset($studentIntake))
        // Use modal IDs for auto-population
        $('#modal_student_id').val('{{ $studentIntake->student_id }}');
        $('#modal_branch_id').val('{{ $studentIntake->branch_id ?? '' }}').trigger('change');
        $('#modal_enrolled_at').val('{{ $studentIntake->enrolled_at ? date('Y-m-d', strtotime($studentIntake->enrolled_at)) : '' }}');
        $('#modal_status').val('{{ $studentIntake->status }}');

        // Set intake and courses after a delay to allow branch change to complete
        setTimeout(function() {
            $('#modal_intake_id').val('{{ $studentIntake->intake_id }}').trigger('change');

            // Set courses after another delay
            setTimeout(function() {
                @if(isset($studentIntake->courses))
                    var courseIds = [{{ $studentIntake->courses->pluck('id')->implode(',') }}];
                    $('#modal_course_ids').val(courseIds);
                    $('#modal_course_ids').niceSelect('update');
                @endif
            }, 1500);
        }, 1000);
    @endif
});
</script>
@endpush
