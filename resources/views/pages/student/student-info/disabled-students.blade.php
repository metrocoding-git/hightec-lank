@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>{{ @$pt }}</h1>
                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('student.student_information')</a>
                    <a href="#">{{ @$pt }}</a>
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
                                <div class="col-lg-8 col-md-6">
                                    <div class="main-title">
                                        <h3 class="mb-15">@lang('common.select_criteria') </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
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
                                        <span class="ti-search pr-2"></span>
                                        @lang('common.search')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">{{ @$pt }} </h3>
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
@include('components.partials.data_table_js')
@include('components.partials.server_side_datatable')

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
                    url: '{{ route("get-courses-by-intake", ":intakeId") }}'.replace(':intakeId', intakeId),
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
                                options += '<option value="' + course.id + '">' + course.name + '</option>';
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
@endPushOnce

{{-- @push('script')
    <script>
        $(document).ready(function() {
            $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                "ajax": $.fn.dataTable.pipeline({
                    url: "{{ route('disable-student-list-datatable') }}",
                    data: {
                        class_id: $('#class_id').val(),
                        section_id: $('#section_id').val(),
                        name: $('#name').val(),
                        admission_no: $('#admission_no').val(),
                    },
                    pages: "{{ generalSetting()->ss_page_load }}" // number of pages to cache
                }),
                columns: [{
                        data: 'admission_no',
                        name: 'admission_no'
                    },
                    {
                        data: 'roll_no',
                        name: 'roll_no'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    @if (moduleStatusCheck('University'))
                        {
                            data: 'semester_label',
                            name: 'semester_label'
                        }, {
                            data: 'class_sec',
                            name: 'class_sec'
                        },
                    @else
                        {
                            data: 'class_sec',
                            name: 'class_sec'
                        },
                    @endif

                    @if (!moduleStatusCheck('University') && generalSetting()->with_guardian)
                        {
                            data: 'parents.fathers_name',
                            name: 'parents.fathers_name'
                        },
                    @endif {
                        data: 'dob',
                        name: 'dob'
                    },

                    {
                        data: 'gender.base_setup_name',
                        name: 'gender.base_setup_name'
                    },
                    {
                        data: 'category.category_name',
                        name: 'category.category_name'
                    },
                    {
                        data: 'mobile',
                        name: 'sm_students.mobile'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'first_name',
                        name: 'first_name',
                        visible: false
                    },
                    {
                        data: 'last_name',
                        name: 'last_name',
                        visible: false
                    },
                ],
                bLengthChange: false,
                bDestroy: true,
                language: {
                    search: "<i class='ti-search'></i>",
                    searchPlaceholder: window.jsLang('quick_search'),
                    paginate: {
                        next: "<i class='ti-arrow-right'></i>",
                        previous: "<i class='ti-arrow-left'></i>",
                    },
                },
                dom: "Bfrtip",
                buttons: [{
                        extend: "copyHtml5",
                        text: '<i class="fa fa-files-o"></i>',
                        title: $("#logo_title").val(),
                        titleAttr: window.jsLang('copy_table'),
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                    },
                    {
                        extend: "excelHtml5",
                        text: '<i class="fa fa-file-excel-o"></i>',
                        titleAttr: window.jsLang('export_to_excel'),
                        title: $("#logo_title").val(),
                        margin: [10, 10, 10, 0],
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                    },
                    {
                        extend: "csvHtml5",
                        text: '<i class="fa fa-file-text-o"></i>',
                        titleAttr: window.jsLang('export_to_csv'),
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        title: $("#logo_title").val(),
                        titleAttr: window.jsLang('export_to_pdf'),
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                        orientation: "landscape",
                        pageSize: "A4",
                        margin: [0, 0, 0, 12],
                        alignment: "center",
                        header: true,
                        customize: function(doc) {
                            doc.content[1].margin = [100, 0, 100, 0]; //left, top, right, bottom
                            doc.content.splice(1, 0, {
                                margin: [0, 0, 0, 12],
                                alignment: "center",
                                image: "data:image/png;base64," + $("#logo_img").val(),
                            });
                            doc.defaultStyle = {
                                font: 'DejaVuSans'
                            }
                        },
                    },
                    {
                        extend: "print",
                        text: '<i class="fa fa-print"></i>',
                        titleAttr: window.jsLang('print'),
                        title: $("#logo_title").val(),
                        exportOptions: {
                            columns: ':visible:not(.not-export-col)'
                        },
                    },
                    {
                        extend: "colvis",
                        text: '<i class="fa fa-columns"></i>',
                        postfixButtons: ["colvisRestore"],
                    },
                ],
                columnDefs: [{
                    visible: false,
                }, ],
                responsive: true,
            });
        });
    </script>
@endpush --}}
