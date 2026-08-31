@extends('layouts.app')
@push('css')
    <style>
        .white-box {
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
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
        select.primary_select {
            border-radius: 8px !important;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        #table_id thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

        #table_id tbody tr {
            transition: background-color .12s ease;
        }

        #table_id tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        #table_id tbody td {
            vertical-align: middle;
        }

        .text-muted {
            color: #9aa1ac !important;
        }
    </style>
@endpush
@section('title')
    Student Add Intake
@endsection
@section('mainContent')

    @php
        $canAddStudentIntake = Auth::user()->hasPermission('student.add-intake-with-courses.store');
        $canSearchStudentIntake = Auth::user()->hasPermission('student.add-intake.search');
    @endphp

    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Student Intake & Courses</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Student Intake & Courses</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @if ($canAddStudentIntake)
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white-box">
                            <div class="row">
                                <div class="col-lg-12 text-right">
                                    <button type="button" class="primary-btn fix-gr-bg col-12" data-toggle="modal"
                                        data-target="#addStudentIntakeModal">
                                        <span class="ti-plus"></span> Add Student Course and Intake
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box filter_card">
                        <form class="form-horizontal mb-0">
                            @csrf
                            <div class="row">
                                {{-- Branch --}}
                                <div class="col-lg-3 mb-15 mb-lg-0">
                                    <div class="primary_input">
                                        <select class="primary_select form-control ajax_select" name="branch_id"
                                            id="search_branch_id">
                                            @if (count($branches) > 1)
                                                <option value="">Select Branch</option>
                                            @endif
                                            @foreach ($branches ?? [] as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ isset($search_branch_id) && $search_branch_id == $branch->id ? 'selected' : '' }}
                                                    {{ count($branches) == 1 ? 'selected' : '' }}>
                                                    {{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Intake --}}
                                <div class="col-lg-3 mb-15 mb-lg-0">
                                    <div class="primary_input">
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
                                <div class="col-lg-3 mb-15 mb-lg-0">
                                    <div class="primary_input">
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

                                <div class="col-lg-3 mb-15 mb-lg-0">
                                    <div class="primary_input">
                                        <select class="primary_select form-control ajax_select" name="status"
                                            id="search_status">
                                            <option value="">Select status</option>

                                            <option value="pending"
                                                {{ isset($search_status) && $search_status == 'pending' ? 'selected' : '' }}>
                                                Pending
                                            </option>

                                            <option value="start"
                                                {{ isset($search_status) && $search_status == 'start' ? 'selected' : '' }}>
                                                Started
                                            </option>

                                            <option value="return"
                                                {{ isset($search_status) && $search_status == 'return' ? 'selected' : '' }}>
                                                Returned
                                            </option>

                                            <option value="complete"
                                                {{ isset($search_status) && $search_status == 'complete' ? 'selected' : '' }}>
                                                Completed
                                            </option>
                                        </select>

                                    </div>
                                </div>

                                @if ($canSearchStudentIntake)
                                    <div class="col-lg-12 mt-10 text-right">
                                        <button type="submit" id="filterBtn"
                                            class="primary-btn small fix-gr-bg col-12 p-2">
                                            <span class="ti-search pr-2"></span> Search
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Student Intake List -->
            <div class="row mt-20">
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
                                                <th>Stu Name Verification</th>
                                                <th>Student Category</th>
                                                <th>Admission No</th>
                                                <th>Nic Number</th>
                                                <th>Nic Verification</th>
                                                <th>Intake Name</th>
                                                <th>Course</th>
                                                <th>Change Course</th>
                                                <th>Mobile 1</th>
                                                <th>Mobile 2</th>
                                                <th>Mobile 1 Verification</th>
                                                <th>Mobile 2 Verification</th>
                                                <th>Branch</th>
                                                <th>Address</th>
                                                <th>Address Verification</th>
                                                <th>Enrollment Date</th>
                                                <th>Status</th>
                                                <th>Note</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
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
                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                    <div class="modal-body">
                        <div id="studentCountAlert" class="alert alert-info col-12 text-center" style="display:none;">
                        </div>
                        <div id="courseStatusAlert" class="alert col-12 text-center" style="display:none;"></div>
                        <div class="row">

                            <!-- Student Selection -->



                            <div class="col-lg-6 mt-4">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="modal_student_id">
                                        Select Student <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        class="primary_select form-control{{ $errors->has('student_id') ? ' is-invalid' : '' }}"
                                        name="student_id" id="modal_student_id" required>
                                        <option value="">Select Student</option>
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}">
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
                            <div class="col-lg-6 mt-4">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="modal_branch_id">
                                        Branch <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        class="primary_select form-control{{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                                        name="branch_id" id="modal_branch_id" required>
                                        @if (count($branches) > 1)
                                            <option value="">Select Branch</option>
                                        @endif
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ count($branches) == 1 ? 'selected' : '' }}>
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
                            {{-- <div class="col-lg-6 mt-4" hidden>
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
                            </div> --}}

                            <!-- Status -->
                            {{-- <div class="col-lg-6 mt-4" hidden>
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
                            </div> --}}

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
                        <button type="submit" id="assignBtn" class="primary-btn fix-gr-bg">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade admin-query" id="editCategoryModal" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Student Category</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_student_course_id">
                    <div class="primary_input">
                        <label class="primary_input_label">Student Category <span class="text-danger">*</span></label>
                        <select id="edit_category_id" class="primary_select form-control">
                            <option value="">Select Category</option>
                            @foreach (\App\Models\StudentCategory::all() as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">Cancel</button>
                    <button type="button" id="saveCategoryBtn" class="primary-btn fix-gr-bg">Update</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade admin-query" id="changeCourseModal" role="dialog">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Change Course</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body py-0 mt-4">
                    <form id="changeCourseForm">
                        @csrf
                        <input type="hidden" id="change_student_intake_id">



                        <div class="primary_input">
                            <label class="primary_input_label">Select Course</label>
                            <select id="change_course_id" class="primary_select form-control">
                                <option value="">Select Course</option>
                            </select>
                        </div>



                        <div class="modal-footer">
                            <button type="button" class="primary-btn tr-bg" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="primary-btn fix-gr-bg">Update</button>
                        </div>

                    </form>
                </div>
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
        // store DataTable in a variable
        var table = $('#table_id').DataTable({
            scrollX: true,
            dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 '" +
                "<'dt-toolbar-title'>" +
                "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-search'f><'dt-buttons-wrap'B>>" +
                ">" +
                "<'row'<'col-sm-12'tr>>" +
                "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>>",

            processing: true,
            serverSide: true,
            lengthMenu: [
                [25, 50, 100, -1],
                [25, 50, 100, "All"]
            ],
            ajax: {
                url: "{{ route('student.add-intake-with-courses.data') }}",
                data: function(d) {
                    d.branch_id = $('#search_branch_id').val();
                    d.intake_id = $('#search_intake_id').val();
                    d.course_id = $('#search_course_id').val();
                    d.status = $('#search_status').val();
                }
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'student_name'
                },
                {
                    data: 'name_verified'
                },
                {
                    data: 'category'
                },
                {
                    data: 'admission_no'
                },
                {
                    data: 'nic'
                },
                {
                    data: 'nic_verified'
                },
                {
                    data: 'intake'
                },
                {
                    data: 'course'
                },
                {
                    data: 'change_course'
                },
                {
                    data: 'mobile1'
                },
                {
                    data: 'mobile2'
                },
                {
                    data: 'mobile1_verified'
                },
                {
                    data: 'mobile2_verified'
                },
                {
                    data: 'branch'
                },
                {
                    data: 'address'
                },
                {
                    data: 'address_verified'
                },
                {
                    data: 'enroll_date'
                },
                {
                    data: 'status'
                },
                {
                    data: 'note'
                },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            buttons: [{
                    extend: 'copyHtml5',
                    title: getFileName(),
                    text: '<i class="fa-regular fa-copy"></i>',
                    titleAttr: 'Copy'
                },
                {
                    extend: 'csvHtml5',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-file-csv"></i>',
                    titleAttr: 'CSV'
                },
                {
                    extend: 'excelHtml5',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-file-excel"></i>',
                    titleAttr: 'Excel'
                },
                {
                    extend: 'pdfHtml5',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-file-pdf"></i>',
                    titleAttr: 'PDF'
                },
                {
                    extend: 'print',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-print"></i>',
                    titleAttr: 'Print'
                }
            ],
            pageLength: 100,
            language: {
                search: '<i class=""></i>',
                searchPlaceholder: 'Search...',
                paginate: {
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            },

            paging: true,
            searching: true,
            ordering: true,
            info: true,
            order: [
                [0, 'desc']
            ],



        });

        function getFileName() {
            let now = new Date();

            let date = now.toISOString().slice(0, 10);
            let time = now.toTimeString().slice(0, 8).replace(/:/g, '-'); // HH-MM-SS

            return 'Student_Add_Intake' + date + '__' + time;
        }


        $('#filterBtn').on('click', function(e) {
            e.preventDefault(); // prevent page reload
            table.ajax.reload(); // reload DataTable with current filter values
        });


        $(document).ready(function() {
            if ({{ count($branches) }} == 1) {
                $('#search_branch_id').trigger('change');
                $('#modal_branch_id').trigger('change');
            }

            function clearAlerts() {
                $('#studentCountAlert, #courseStatusAlert')
                    .fadeOut(200, function() {
                        $(this).removeClass('alert-info alert-success alert-danger').empty();
                    });
            }

            $('#addStudentIntakeModal').on('show.bs.modal', function() {
                clearAlerts();
                $('#assignBtn').prop('disabled', true);
            });

            $('#modal_student_id, #modal_branch_id, #modal_intake_id').on('change', function() {
                clearAlerts();
            });


            // Geting added student count
            $('#modal_course_ids').on('change', function() {
                const intakeId = $('#modal_intake_id').val();
                const courseId = $(this).val();

                if (intakeId && courseId) {
                    $.ajax({
                        url: "{{ route('student.intake-course.search') }}",
                        type: "GET",
                        data: {
                            intake_id: intakeId,
                            course_id: courseId
                        },
                        success: function(response) {
                            // show student count alert
                            $('#studentCountAlert')
                                .removeClass('alert-danger alert-success')
                                .addClass('alert-info')
                                .html(`<strong>Student Count:</strong> ${response.count}`)
                                .fadeIn();


                            // course status alert
                            if (response.can_add) {
                                $('#courseStatusAlert')
                                    .removeClass('alert-danger')
                                    .addClass('alert-success')
                                    .html(
                                        `<strong>✅ ${response.message}</strong><br>Expiry Date: ${response.expire_date}`
                                    )
                                    .fadeIn();

                                // enable Assign button
                                $('#assignBtn').prop('disabled', false);
                            } else {
                                $('#courseStatusAlert')
                                    .removeClass('alert-success')
                                    .addClass('alert-danger')
                                    .html(
                                        `<strong>⚠️ ${response.message}</strong><br>Expiry Date: ${response.expire_date}`
                                    )
                                    .fadeIn();

                                // disable Assign button
                                $('#assignBtn').prop('disabled', true);
                            }

                        },
                        error: function() {
                            $('#studentCountAlert')
                                .removeClass('alert-info alert-success')
                                .addClass('alert-danger')
                                .text('Error fetching course info.')
                                .fadeIn();

                            setTimeout(() => $('#studentCountAlert').fadeOut(), 5000);
                        }
                    });
                } else {
                    $('#studentCount').text('');
                }

            });

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
        function changeStatus(id, newStatus) {
            // Show loading state
            $.ajax({
                url: '{{ route('student.add-intake.update-status', ':id') }}'.replace(':id', id),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus,
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

        // BUG FIX (Section 26): the server-rendered Delete action (built in
        // studentAddIntakeWithCoursesData(), see the controller patch) calls
        // deleteIntake(id) on click, but this function did not exist anywhere in this
        // file — clicking Delete silently threw "deleteIntake is not defined" and did
        // nothing. Added here, following the same confirm + ajax + toastr pattern
        // already used by changeStatus() above, posting to the real
        // student.add-intake.destroy route.
        function deleteIntake(id) {
            if (!confirm('Are you sure you want to remove this intake? This cannot be undone.')) {
                return;
            }

            $.ajax({
                url: '{{ route('student.add-intake.destroy', ':id') }}'.replace(':id', id),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    toastr.success('Intake removed successfully!');
                    table.ajax.reload(null, false);
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting intake:', error);
                    console.error('XHR response:', xhr.responseText);

                    var errorMessage = 'Failed to remove intake';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    toastr.error(errorMessage);
                }
            });
        }


        function openChangeCourseModal(studentIntakeId, currentCourseId, intakeId) {

            $('#change_student_intake_id').val(studentIntakeId);

            const courseMap = {
                34: 43,
                43: 34,
                33: 44,
                44: 33,
                35: 45,
                45: 35
            };

            // Load courses based on intake
            $.ajax({
                url: '{{ route('get-courses-by-intake', ':id') }}'.replace(':id', intakeId),
                type: 'GET',
                success: function(data) {

                    let options = '<option value="">Select Course</option>';

                    if (data.courses && data.courses.length > 0) {

                        const allowedCourseId = courseMap[currentCourseId];

                        data.courses.forEach(function(course) {

                            // Only allow mapped course OR current course
                            if (course.id == currentCourseId || course.id == allowedCourseId) {

                                let selected = (course.id == currentCourseId) ? 'selected' : '';

                                options += `<option value="${course.id}" ${selected}>
                                            ${course.name}
                                        </option>`;
                            }
                        });
                    }
                    $('#change_course_id').html(options);

                    if ($('#change_course_id').hasClass('primary_select')) {
                        $('#change_course_id').niceSelect('update');
                    }

                    $('#changeCourseModal').modal('show');
                }
            });
        }

        $('#changeCourseForm').on('submit', function(e) {
            e.preventDefault();

            let id = $('#change_student_intake_id').val();
            let course_id = $('#change_course_id').val();

            $.ajax({
                url: '{{ route('student.add-intake-with-courses.change-course', ':id') }}'.replace(':id',
                    id),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    course_id: course_id
                },
                success: function(response) {

                    if (response.success) {
                        toastr.success('Course updated successfully!');
                        $('#changeCourseModal').modal('hide');

                        setTimeout(() => location.reload(), 1000);
                    } else {
                        toastr.error(response.message || 'Failed to update');
                    }
                },
                error: function() {
                    toastr.error('Something went wrong!');
                }
            });
        });


        function openEditCategoryModal(studentCourseId, currentCategoryId) {
            // Set the hidden input value
            $('#edit_student_course_id').val(studentCourseId);

            // Set the category dropdown value
            $('#edit_category_id').val(currentCategoryId);

            // Destroy and reinitialize niceSelect
            if ($('#edit_category_id').hasClass('primary_select')) {
                try {
                    // Destroy existing niceSelect instance
                    $('#edit_category_id').niceSelect('destroy');

                    // Wait a moment then reinitialize
                    setTimeout(function() {
                        $('#edit_category_id').niceSelect();
                        $('#edit_category_id').val(currentCategoryId).niceSelect('update');
                    }, 100);
                } catch (e) {
                    console.log('NiceSelect not initialized yet');
                    $('#edit_category_id').niceSelect();
                }
            }

            // Show the modal
            $('#editCategoryModal').modal('show');

            return false;
        }

        $('#saveCategoryBtn').on('click', function() {
            let id = $('#edit_student_course_id').val();
            let categoryId = $('#edit_category_id').val();

            if (!categoryId) {
                toastr.error('Please select a category');
                return;
            }

            $.ajax({
                url: '{{ route('student.add-intake.update-category', ':id') }}'.replace(':id', id),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    student_category_id: categoryId,
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Category updated successfully!');
                        $('#editCategoryModal').modal('hide');
                        // Close the modal and unbind the shown event
                        $('#editCategoryModal').off('shown.bs.modal');
                        // Reload table
                        table.ajax.reload();
                    } else {
                        toastr.error(response.message || 'Failed to update');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    toastr.error('Something went wrong!');
                }
            });
        });
    </script>
@endpush
