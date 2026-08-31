@extends('layouts.app')
@push('css')
    <style>
        /* ---- Modern refresh: cards, table, breadcrumb (Section 25) ----
                     BUG FIX: this page already used the sms-breadcrumb markup, but like
                     student-view.blade.php it never defined the CSS for it, so the banner
                     rendered unstyled. Adding the same card/table/breadcrumb CSS used
                     everywhere else. ---- */
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

        .sms-breadcrumb {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
            padding: 18px 24px;
            position: relative;
            overflow: hidden;
        }

        .sms-breadcrumb::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--gradient_1), var(--gradient_2));
        }

        .sms-breadcrumb .row {
            align-items: center;
        }

        .sms-breadcrumb h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .sms-breadcrumb .bc-pages {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .sms-breadcrumb .bc-pages a {
            font-size: 13px;
            font-weight: 500;
            color: #8a90a2;
            text-decoration: none;
            transition: color .15s ease;
        }

        .sms-breadcrumb .bc-pages a:hover {
            color: var(--gradient_1);
        }

        .sms-breadcrumb .bc-pages a:not(:last-child)::after {
            content: '\203A';
            margin: 0 8px;
            color: #c7cbd6;
            font-weight: 400;
        }

        .sms-breadcrumb .bc-pages a:last-child {
            color: var(--gradient_1);
            font-weight: 700;
            pointer-events: none;
        }
    </style>
@endpush
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    @php
        // Button-level permissions (Section 25).
        $canSearchDisabledStudents = Auth::user()->hasPermission('disabled-students.search');
        $canEnableStudent = Auth::user()->hasPermission('student.update-status');
    @endphp
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Disabled @lang('student.student_list')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Disabled @lang('student.student_list')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor full_wide_table">
        <div class="container-fluid p-0">

            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'disabled_student_search', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }} --}}
            <form class="form-horizontal" action="{{ route('disabled-students.search') }}" method="post">
                @csrf
                <input type="hidden" id="class_id" value="{{ @$class_id }}">
                <input type="hidden" id="section_id" value="{{ @$section_id }}">
                <input type="hidden" id="admission_no" value="{{ @$admission_no }}">
                <input type="hidden" id="name" value="{{ @$name }}">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="white-box">
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                                <div class="col-lg-4">
                                    <div class="primary_input">
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
                                <div class="col-lg-4">
                                    <div class="primary_input">
                                        <select class="primary_select form-control ajax_select" name="intake_id"
                                            id="intake-select">
                                            <option value="">Select Intake</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Course --}}
                                <div class="col-lg-4">
                                    <div class="primary_input">
                                        <select class="primary_select form-control ajax_select" name="course_id"
                                            id="course-select">
                                            <option value="">Select Course</option>
                                        </select>

                                    </div>
                                </div>


                                @if ($canSearchDisabledStudents)
                                    <div class="col-lg-12 mt-10 text-right">
                                        <button type="submit" class="primary-btn small fix-gr-bg p-2 col-12">
                                            <span class="ti-search pr-2"></span>
                                            @lang('common.search')
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">Disabled @lang('student.student_list')</h3>
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

                                                <th>@lang('student.date_of_birth')</th>

                                                <th>@lang('student.class_sec')</th>

                                                <th>@lang('common.gender')</th>
                                                <th>@lang('common.type')</th>
                                                <th>@lang('common.phone')</th>
                                                <th>@lang('common.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($students as $student)
                                                <tr>
                                                    <td>{{ $student->admission_number }}</td>
                                                    <td>{{ $student->student_name ?? 'N/A' }}</td>
                                                    <td>{{ $student->date_of_birth }}</td>
                                                    <td>-</td>
                                                    <td>{{ $student->gender->name ?? '-' }}</td>
                                                    <td>{{ $student->studentCategory->category_name ?? 'N/A' }}</td>
                                                    <td>{{ $student->phone_number }}</td>
                                                    <td valign="top">
                                                        @if ($canEnableStudent)
                                                            @php
                                                                $studentLink = $student->is_active
                                                                    ? 'student.disable'
                                                                    : 'student.enable';
                                                                $routeList = [
                                                                    '<a class="dropdown-item" data-toggle="modal" data-student-id="' .
                                                                    $student->id .
                                                                    '" data-target="#statusUpdateModal" href="' .
                                                                    route('student.update-status') .
                                                                    '">' .
                                                                    __('common.enable') .
                                                                    '</a>',
                                                                ];
                                                            @endphp
                                                            <x-drop-down-action-component :routeList="$routeList" />
                                                        @else
                                                            <span class="text-muted">&mdash;</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">No disabled students found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </x-table>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade admin-query" id="statusUpdateModal">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">@lang('academics.delete_class')</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body text-center">
                                    <h4>@lang('common.are_you_sure_to_update_student_status')</h4>
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button type="button" class="primary-btn tr-bg"
                                            data-dismiss="modal">@lang('common.cancel')</button>
                                        <form action="{{ route('student.update-status') }}" method="post">
                                            @csrf
                                            @method('put')
                                            <input type="hidden" id="student-id-form" name="student_id" value="">
                                            <button class="primary-btn fix-gr-bg" type="submit">@lang('common.update')</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade admin-query" id="deleteStudentModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirmation Required</h4>
                    {{-- <h4 class="modal-title">@lang('student.delete') @lang('student.student')</h4> --}}
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="text-center">
                        {{-- <h4>@lang('student.are_you_sure_to_delete')</h4> --}}
                        <h4 class="text-danger">You are going to remove
                            {{ @$student->first_name . ' ' . @$student->last_name }}. Removed data CANNOT be
                            restored! Are
                            you
                            ABSOLUTELY Sure!</h4>
                        {{-- <div class="alert alert-warning">@lang('student.student_delete_note')</div> --}}
                    </div>

                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                        {{-- {{ Form::open(['route' => 'disable_student_delete', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }} --}}
                        <form class="">
                            <input type="hidden" name="id" value="" id="student_delete_i">
                            {{-- using js in main.js --}}
                            <button class="primary-btn fix-gr-bg" type="submit">@lang('common.delete')</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@include('components.partials.data_table_css')
@include('components.partials.data_table_js')

@pushOnce('scripts')
    <script>
        $('#statusUpdateModal').on('show.bs.modal', function(event) {
            var input = $('#student-id-form');
            var button = $(event.relatedTarget);
            var studentId = button.data('student-id');
            input.val(studentId);
        });

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
                    url: '{{ route('get-intakes-by-branch', ':branchId') }}'.replace(':branchId',
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
                    url: '{{ route('get-courses-by-intake', ':intakeId') }}'.replace(':intakeId',
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


        $('#table_id').DataTable({
            responsive: true,
            dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 '" +
                "<'dt-toolbar-title'>" +
                "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-search'f><'dt-buttons-wrap'B>>" +
                ">" +
                "<'row'<'col-sm-12'tr>>" +
                "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>>",
            
            paging: true,
            searching: true,
            ordering: true,
            info: true,
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
        });
    </script>
@endPushOnce
