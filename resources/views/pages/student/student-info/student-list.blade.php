@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection


@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('student.manage_student')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('student.student_information')</a>
                    <a href="#">@lang('student.student_list')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'student-list-search', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'infix_form']) }} --}}
            <form class="form-horizontal" action="{{ route('frontend.student.search') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white-box filter_card">
                            <div class="row">
                                <div class="col-lg-8 col-md-6 col-sm-6">
                                    <div class="main-title mt_0_sm mt_0_md">
                                        <h3 class="mb-15">@lang('common.select_criteria')</h3>
                                    </div>
                                </div>


                                <div class="col-lg-4 col-md-6 col-sm-6 text-left text-sm-right mb-4 mb-sm-0">
                                    <a href="{{ route('add-student.index') }}" class="primary-btn small fix-gr-bg">
                                        <span class="ti-plus pr-2"></span>
                                        @lang('student.add_student')
                                    </a>
                                </div>

                            </div>
                            <div class="row row-gap-24">

                                {{-- <input type="hidden" name="url" id="url" value="{{ URL::to("/") }}"> --}}


                                {{-- Branch --}}
                                <div class="col-lg-4 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="branch_id">Branch <span
                                                class="text-danger">*</span></label>
                                        <select class="primary_select form-control ajax_select" name="branch_id"
                                            id="branch-select">
                                            <option value="">Select Branch</option>
                                            @foreach ($branches ?? [] as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Intake --}}
                                <div class="col-lg-4 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="intake_id">Intake <span
                                                class="text-danger">*</span></label>
                                        <select class="primary_select form-control ajax_select" name="intake_id"
                                            id="intake-select">
                                            <option value="">Select Intake</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Course --}}
                                <div class="col-lg-4 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="course_id">Course <span
                                                class="text-danger">*</span></label>
                                        <select class="primary_select form-control ajax_select" name="course_id"
                                            id="course-select">
                                            <option value="">Select Course</option>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-lg-12 mt-20 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg">
                                        <span class="ti-search pr-2"></span> @lang('common.search')
                                    </button>
                                </div>





                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="academic_id" value="{{ @$academic_year }}">
                <input type="hidden" id="class" value="{{ @$class_id }}">
                <input type="hidden" id="section" value="{{ @$section }}">
                <input type="hidden" id="roll" value="{{ @$roll_no }}">
                <input type="hidden" id="name" value="{{ @$name }}">
                <input type="hidden" id="un_session" value="{{ @$data['un_session_id'] }}">
                <input type="hidden" id="un_academic" value="{{ @$data['un_academic_id'] }}">
                <input type="hidden" id="un_faculty" value="{{ @$data['un_faculty_id'] }}">
                <input type="hidden" id="un_department" value="{{ @$data['un_department_id'] }}">
                <input type="hidden" id="un_semester_label" value="{{ @$data['un_semester_label_id'] }}">
                <input type="hidden" id="un_section" value="{{ @$data['un_section_id'] }}">
            </form>
            {{-- @if (@$students) --}}
            <div class="row mt-40 full_wide_table">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('student.student_list')</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id"
                                        class="table data-table Crm_table_active3 no-footer dtr-inline collapsed"
                                        cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>@lang('student.admission_no')</th>
                                                <th>@lang('student.name')</th>
                                                <th>Nic Number</th>


                                                <th>@lang('student.date_of_birth')</th>

                                                <th>@lang('student.class_sec')</th>

                                                <th>@lang('common.gender')</th>
                                                <th>@lang('common.type')</th>
                                                <th>@lang('common.phone')</th>
                                                <th>@lang('common.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($students as $student)
                                                <tr>
                                                    <td>{{ $student->admission_number }}</td>
                                                    <td>{{ $student->student_name ?? 'N/A' }}</td>
                                                    <td>{{ $student->national_id_number ?? 'N/A' }}</td>
                                                    <td>{{ $student->date_of_birth ?? '-' }}</td>
                                                    <td>{{ $student->branch?->name }}</td>
                                                    <td>{{ $student->gender->name ?? '-' }}</td>
                                                    <td>{{ $student->studentCategory->category_name ?? 'N/A' }}</td>
                                                    <td>{{ $student->phone_number }}</td>
                                                    <td valign="top">
                                                        @php
                                                            $routeList = [
                                                                '<a class="dropdown-item" target="_blank" href="' .
                                                                route('student-details', $student->id) .
                                                                '">' .
                                                                __('common.view') .
                                                                '</a>',
                                                                '<a class="dropdown-item" href="' .
                                                                route('add-student.edit', $student->id) .
                                                                '">' .
                                                                __('common.edit') .
                                                                '</a>',
                                                                '<a class="dropdown-item" data-toggle="modal" data-student-id="' .
                                                                $student->id .
                                                                '" data-target="#statusUpdateModal"
                                                                href="' .
                                                                route('student.update-status', $student->id) .
                                                                '">' .
                                                                __('common.disable') .
                                                                '</a>',
                                                            ];
                                                        @endphp
                                                        <x-drop-down-action-component :routeList="$routeList" />
                                                    </td>
                                                </tr>
                                            {{-- @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">No students found</td>
                                                </tr> --}}
                                            @endforeach
                                        </tbody>
                                    </table>
                                </x-table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            {{-- @endif --}}
        </div>
    </section>
    {{-- disable student  --}}
    <div class="modal fade admin-query" id="deleteStudentModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('student.disable_student')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <h4>@lang('student.are_you_sure_to_disable')</h4>
                    </div>
                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                        {{-- {{ Form::open(['route' => 'student-delete', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }} --}}
                        <form class="">
                            <input type="hidden" name="id" value="{{ @$student->id }}" id="student_delete_i">
                            {{-- using js in main.js --}}
                            <button class="primary-btn fix-gr-bg" type="submit">@lang('common.disable')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade admin-query" id="statusUpdateModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('student.disable_student')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <h4>@lang('common.are_you_sure_to_update_student_status')</h4>
                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                        <form action="{{ route('student.update-status') }}" method="post">
                            @csrf
                            @method('put')
                            <input type="hidden" id="student-id-form" name="student_id" value="">
                            <button class="primary-btn fix-gr-bg" type="submit">@lang('common.disable')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        if (isset($academic_year) || isset($class_id)) {
            $ajax_url = url(
                'student-list-datatable?academic_year=' .
                    $academic_year .
                    '&class=' .
                    $class_id .
                    '&section=' .
                    $section .
                    '&roll_no=' .
                    $roll_no .
                    '&name=' .
                    $name,
            );
        } else {
            $ajax_url = url('student-list-datatable');
        }
    @endphp
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
            order: [[0, 'desc']], // 👈 ensure newest entries show first

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
    </script>


    <script>
        $('#statusUpdateModal').on('show.bs.modal', function(event) {
            var input = $('#student-id-form');
            var button = $(event.relatedTarget);
            var studentId = button.data('student-id');
            input.val(studentId);
        });

        // $('#table_id').DataTable({
        //     dom: 'Bfrtip',
        //     paging: true,
        //     searching: true,
        //     ordering: true,
        //     info: true,
        // });
        // document.querySelector("form").addEventListener("submit", function() {
        //     this.querySelector("button[type=submit]").disabled = true;
        // });


        // Handle branch change event to load intakes dynamically
        $('#branch-select').on('change', function() {
            var branchId = $(this).val();
            var intakeSelect = $('#intake-select');
            var courseSelect = $('#course-select');

            if (branchId) {
                // Show loading state
                intakeSelect.html('<option value="">Loading intakes...</option>');
                courseSelect.html('<option value="">Select Course</option>');

                // Make AJAX request to get intakes for selected branch
                $.ajax({
                    url: '{{ route('student-list.get-intakes', ':branchId') }}'.replace(':branchId',
                        branchId),
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
                                options += '<option value="' + intake.id + '">' + intake.name +
                                    '</option>';
                            });
                        } else {
                            options = '<option value="">No intakes available for this branch</option>';
                        }

                        intakeSelect.html(options);

                        // Trigger niceSelect update if using niceSelect
                        if (intakeSelect.hasClass('primary_select')) {
                            intakeSelect.niceSelect('update');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading intakes:', error);
                        console.error('XHR response:', xhr.responseText);
                        intakeSelect.html('<option value="">Error loading intakes</option>');
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

        // Handle intake change event to load courses dynamically
        $('#intake-select').on('change', function() {
            var intakeId = $(this).val();
            var courseSelect = $('#course-select');

            if (intakeId) {
                // Show loading state
                courseSelect.html('<option value="">Loading courses...</option>');

                // Make AJAX request to get courses for selected intake
                $.ajax({
                    url: '{{ route('student-list.get-courses', ':intakeId') }}'.replace(':intakeId',
                        intakeId),
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        console.log('Courses response:', data);
                        var options = '<option value="">Select Course</option>';

                        if (data.courses && data.courses.length > 0) {
                            data.courses.forEach(function(course) {
                                options += '<option value="' + course.id + '">' + course.name +
                                    '</option>';
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
                courseSelect.html('<option value="">Select Course</option>');

                // Trigger niceSelect update if using niceSelect
                if (courseSelect.hasClass('primary_select')) {
                    courseSelect.niceSelect('update');
                }
            }
        });
    </script>
@endpush
