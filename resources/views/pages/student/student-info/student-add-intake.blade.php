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
                                <button type="button" class="primary-btn fix-gr-bg" data-toggle="modal"
                                    data-target="#addStudentIntakeModal">
                                    <span class="ti-plus"></span> Add Student Course and Intake
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="white-box filter_card">
                        <form class="form-horizontal" action="{{ route('student.add-intake.search') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-lg-8 col-md-6 col-sm-6">
                                    <div class="main-title mt_0_sm mt_0_md">
                                        <h3 class="mb-15">Select Criteria</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-gap-24">
                                {{-- Branch --}}
                                <div class="col-lg-4 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="search_branch_id">Branch <span
                                                class="text-danger">*</span></label>
                                        <select class="primary_select form-control ajax_select" name="branch_id"
                                            id="search_branch_id">
                                            <option value="">Select Branch</option>
                                            @foreach ($branches ?? [] as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ isset($search_branch_id) && $search_branch_id == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Intake --}}
                                <div class="col-lg-4 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="search_intake_id">Intake <span
                                                class="text-danger">*</span></label>
                                        <select class="primary_select form-control ajax_select" name="intake_id"
                                            id="search_intake_id">
                                            <option value="">Select Intake</option>
                                            @if (isset($search_intakes))
                                                @foreach ($search_intakes as $intake)
                                                    <option value="{{ $intake->id }}"
                                                        {{ isset($search_intake_id) && $search_intake_id == $intake->id ? 'selected' : '' }}>
                                                        {{ $intake->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                {{-- Course --}}
                                <div class="col-lg-4 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="search_course_id">Course <span
                                                class="text-danger">*</span></label>
                                        <select class="primary_select form-control ajax_select" name="course_id"
                                            id="search_course_id">
                                            <option value="">Select Course</option>
                                            @if (isset($search_courses))
                                                @foreach ($search_courses as $course)
                                                    <option value="{{ $course->id }}"
                                                        {{ isset($search_course_id) && $search_course_id == $course->id ? 'selected' : '' }}>
                                                        {{ $course->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-20 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg">
                                        <span class="ti-search pr-2"></span> Search
                                    </button>
                                </div>
                            </div>
                        </form>
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
                                                <th>No.</th>
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
                                            @forelse($studentIntakes as $key => $studentIntake)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $studentIntake->student_name }}</td>
                                                    <td>{{ $studentIntake->admission_number }}</td>
                                                    <td>{{ $studentIntake->intake_name }}</td>
                                                    <td>{{ $studentIntake->branch_name ?? 'N/A' }}</td>
                                                    <td>
                                                        @if (isset($studentIntake->courses) && $studentIntake->courses->count() > 0)
                                                            @foreach ($studentIntake->courses as $course)
                                                                <span class="badge badge-info">{{ $course->name }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">No courses</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $studentIntake->enrolled_at ? date('Y-m-d', strtotime($studentIntake->enrolled_at)) : '-' }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $studentIntake->status == 'pending' ? 'badge-success' : ($studentIntake->status == 'start' ? 'badge-info' : ($studentIntake->status == 'return' ? 'badge-warning' : 'badge-primary')) }}">
                                                            {{ ucfirst($studentIntake->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <x-drop-down>
                                                            @if ($studentIntake->status == 'pending')
                                                                <a class="dropdown-item" href="#"
                                                                    onclick="changeStatus({{ $studentIntake->id }}, 'start')">
                                                                    <span class="text-info">▶ Start</span>
                                                                </a>
                                                            @endif
                                                            @if ($studentIntake->status == 'start')
                                                                <a class="dropdown-item" href="#"
                                                                    onclick="changeStatus({{ $studentIntake->id }}, 'return')">
                                                                    <span class="text-warning">↩ Return</span>
                                                                </a>
                                                                <a class="dropdown-item" href="#"
                                                                    onclick="changeStatus({{ $studentIntake->id }}, 'complete')">
                                                                    <span class="text-success">✓ Complete</span>
                                                                </a>
                                                            @endif
                                                            @if ($studentIntake->status == 'return')
                                                                <a class="dropdown-item" href="#"
                                                                    onclick="changeStatus({{ $studentIntake->id }}, 'start')">
                                                                    <span class="text-info">▶ Restart</span>
                                                                </a>
                                                                <a class="dropdown-item" href="#"
                                                                    onclick="changeStatus({{ $studentIntake->id }}, 'complete')">
                                                                    <span class="text-success">✓ Complete</span>
                                                                </a>
                                                            @endif
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#deleteStudentIntakeModal{{ $studentIntake->id }}"
                                                                href="#">
                                                                <span class="text-danger">🗑 Delete</span>
                                                            </a>
                                                        </x-drop-down>
                                                    </td>
                                                </tr>

                                                <!-- Delete Modal -->
                                                <div class="modal fade admin-query"
                                                    id="deleteStudentIntakeModal{{ $studentIntake->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Remove Intake</h4>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="text-center">
                                                                    <h4>Are you sure to remove intake?</h4>
                                                                </div>
                                                                <div class="mt-40 d-flex justify-content-between">
                                                                    <button type="button" class="primary-btn tr-bg"
                                                                        data-dismiss="modal">
                                                                        Cancel
                                                                    </button>
                                                                    <form
                                                                        action="{{ route('student.add-intake.destroy', $studentIntake->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">
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
                                                    <td colspan="9" class="text-center">No intake assignments found
                                                    </td>
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
                                    <select
                                        class="primary_select form-control{{ $errors->has('student_id') ? ' is-invalid' : '' }}"
                                        name="student_id" id="modal_student_id" required>
                                        <option value="">Select Student</option>
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}"
                                                {{ old('student_id', $studentIntake->student_id ?? null) == $student->id ? 'selected' : '' }}>
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
                                    <select
                                        class="primary_select form-control{{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                                        name="branch_id" id="modal_branch_id" required>
                                        <option value="">Select Branch</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id', $studentIntake->branch_id ?? null) == $branch->id ? 'selected' : '' }}>
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
                                    <select
                                        class="primary_select form-control{{ $errors->has('intake_id') ? ' is-invalid' : '' }}"
                                        name="intake_id" id="modal_intake_id" required>
                                        <option value="">Select Intake</option>
                                        @if (isset($studentIntake) && $studentIntake->intake_id)
                                            <option value="{{ $studentIntake->intake_id }}" selected>
                                                {{ $studentIntake->intake_name }}
                                                ({{ $studentIntake->branch_name ?? 'No Branch' }})
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
                                    <select
                                        class="primary_select form-control{{ $errors->has('course_ids') ? ' is-invalid' : '' }}"
                                        name="course_ids[]" id="modal_course_ids" required>
                                        <option value="">Select Courses</option>
                                        @if (isset($studentIntake) && isset($studentIntake->courses))
                                            @foreach ($studentIntake->courses as $course)
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
                                    <input
                                        class="primary_input_field form-control{{ $errors->has('enrolled_at') ? ' is-invalid' : '' }}"
                                        type="date" name="enrolled_at" id="modal_enrolled_at"
                                        value="{{ old('enrolled_at', date('Y-m-d')) }}" required>
                                    @error('enrolled_at')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-lg-6 mt-4" hidden>
                                <div class="primary_input">
                                    <label class="primary_input_label" for="modal_status">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        class="primary_select form-control{{ $errors->has('status') ? ' is-invalid' : '' }}"
                                        name="status" id="modal_status">
                                        <option selected value="pending"
                                            {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>
                                            Pending
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
                                    <textarea class="primary_input_field form-control{{ $errors->has('notes') ? ' is-invalid' : '' }}" name="notes"
                                        id="modal_notes" rows="4" placeholder="Add notes here">{{ old('notes') }}</textarea>
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
            // order: [[0, 'desc']], // Sort by first column (No.) in descending order to show latest data first
          

            // 👇 This part adds the search and pagination icons
            language: {
                search: '<i class="fa-solid fa-magnifying-glass me-2"></i>',
                searchPlaceholder: 'Search...',
                paginate: {
                    first: '<i class="fa-solid fa-angle-double-left"></i>',
                    last: '<i class="fa-solid fa-angle-double-right"></i>',
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            }
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
                        url: '{{ route('get-intakes-by-branch', ':branchId') }}'.replace(
                            ':branchId', branchId),
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
                                    options += '<option value="' + intake.id + '">' +
                                        intake.name + '</option>';
                                });
                            } else {
                                options =
                                    '<option value="">No intakes available for this branch</option>';
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
                            intakeSelect.html(
                            '<option value="">Error loading intakes</option>');
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
                        url: '{{ route('get-courses-by-intake', ':intakeId') }}'.replace(
                            ':intakeId', intakeId),
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
                                    options += '<option value="' + course.id + '">' +
                                        course.name + '</option>';
                                });
                            } else {
                                options =
                                    '<option value="">No courses available for this intake</option>';
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
                            courseSelect.html(
                            '<option value="">Error loading courses</option>');

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
            @if (isset($studentIntake))
                // Use modal IDs for auto-population
                $('#modal_student_id').val('{{ $studentIntake->student_id }}');
                $('#modal_branch_id').val('{{ $studentIntake->branch_id ?? '' }}').trigger('change');
                $('#modal_enrolled_at').val(
                    '{{ $studentIntake->enrolled_at ? date('Y-m-d', strtotime($studentIntake->enrolled_at)) : '' }}'
                    );
                $('#modal_status').val('{{ $studentIntake->status }}');

                // Set intake and courses after a delay to allow branch change to complete
                setTimeout(function() {
                    $('#modal_intake_id').val('{{ $studentIntake->intake_id }}').trigger('change');

                    // Set courses after another delay
                    setTimeout(function() {
                        @if (isset($studentIntake->courses))
                            var courseIds = [
                                {{ $studentIntake->courses->pluck('id')->implode(',') }}
                            ];
                            $('#modal_course_ids').val(courseIds);
                            $('#modal_course_ids').niceSelect('update');
                        @endif
                    }, 1500);
                }, 1000);
            @endif

            // Handle branch change event to load intakes dynamically (Search Form)
            $('#search_branch_id').on('change', function() {
                var branchId = $(this).val();
                var intakeSelect = $('#search_intake_id');
                var courseSelect = $('#search_course_id');

                if (branchId) {
                    // Show loading state
                    intakeSelect.html('<option value="">Loading intakes...</option>');
                    courseSelect.html('<option value="">Select Course</option>');

                    // Make AJAX request to get intakes for selected branch
                    $.ajax({
                        url: '{{ route('student.add-intake.get-intakes', ':branchId') }}'.replace(
                            ':branchId', branchId),
                        type: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        success: function(data) {
                            console.log('Search Intakes response:', data);
                            var options = '<option value="">Select Intake</option>';

                            if (data.intakes && data.intakes.length > 0) {
                                data.intakes.forEach(function(intake) {
                                    options += '<option value="' + intake.id + '">' +
                                        intake.name + '</option>';
                                });
                            } else {
                                options =
                                    '<option value="">No intakes available for this branch</option>';
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
                            console.error('Error loading search intakes:', error);
                            console.error('XHR response:', xhr.responseText);
                            intakeSelect.html(
                            '<option value="">Error loading intakes</option>');
                            courseSelect.html('<option value="">Select Course</option>');

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
                    courseSelect.html('<option value="">Select Course</option>');

                    // Trigger niceSelect update if using niceSelect
                    if (intakeSelect.hasClass('primary_select')) {
                        intakeSelect.niceSelect('update');
                    }
                    if (courseSelect.hasClass('primary_select')) {
                        courseSelect.niceSelect('update');
                    }
                }
            });

            // Handle intake change event to load courses dynamically (Search Form)
            $('#search_intake_id').on('change', function() {
                var intakeId = $(this).val();
                var courseSelect = $('#search_course_id');

                if (intakeId) {
                    // Show loading state
                    courseSelect.html('<option value="">Loading courses...</option>');

                    // Make AJAX request to get courses for selected intake
                    $.ajax({
                        url: '{{ route('student.add-intake.get-courses', ':intakeId') }}'.replace(
                            ':intakeId', intakeId),
                        type: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        success: function(data) {
                            console.log('Search Courses response:', data);
                            var options = '<option value="">Select Course</option>';

                            if (data.courses && data.courses.length > 0) {
                                data.courses.forEach(function(course) {
                                    options += '<option value="' + course.id + '">' +
                                        course.name + '</option>';
                                });
                            } else {
                                options =
                                    '<option value="">No courses available for this intake</option>';
                            }

                            courseSelect.html(options);

                            // Trigger niceSelect update if using niceSelect
                            if (courseSelect.hasClass('primary_select')) {
                                courseSelect.niceSelect('update');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading search courses:', error);
                            console.error('XHR response:', xhr.responseText);
                            courseSelect.html(
                            '<option value="">Error loading courses</option>');

                            // Trigger niceSelect update if using niceSelect
                            if (courseSelect.hasClass('primary_select')) {
                                courseSelect.niceSelect('update');
                            }
                        }
                    });
                } else {
                    // Reset course dropdown when no intake is selected
                    courseSelect.html('<option value="">Select Course</option>');

                    // Trigger niceSelect update if using niceSelect
                    if (courseSelect.hasClass('primary_select')) {
                        courseSelect.niceSelect('update');
                    }
                }
            });

            // Initialize search dropdowns if values are already set (e.g., after a form submission with old input)
            $(window).on('load', function() {
                var searchBranchId = $('#search_branch_id').val();
                var searchIntakeId = $('#search_intake_id').val();
                var searchCourseId = $('#search_course_id').val();

                if (searchBranchId) {
                    // Trigger change to load intakes
                    $('#search_branch_id').trigger('change');
                    // After intakes load, set the selected intake
                    setTimeout(function() {
                        if (searchIntakeId) {
                            $('#search_intake_id').val(searchIntakeId).niceSelect('update');
                            // Trigger change to load courses
                            $('#search_intake_id').trigger('change');
                            // After courses load, set the selected course
                            setTimeout(function() {
                                if (searchCourseId) {
                                    $('#search_course_id').val(searchCourseId).niceSelect(
                                        'update');
                                }
                            }, 500); // Adjust delay as needed
                        }
                    }, 500); // Adjust delay as needed
                }
            });
        });

        // Function to handle status changes
        function changeStatus(studentIntakeId, newStatus) {
            // Show loading state
            $.ajax({
                url: '{{ route('student.add-intake.update-status', ':id') }}'.replace(':id', studentIntakeId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        toastr.success('Status updated successfully!');
                        // Reload the page to reflect changes
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error('Failed to update status: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating status:', error);
                    console.error('XHR response:', xhr.responseText);

                    var errorMessage = 'Failed to update status';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    toastr.error(errorMessage);
                }
            });
        }
    </script>
@endpush
