@extends('layouts.app')
@push('css')
    <style>
        #table_id thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

        .student_rec_card {
            border-radius: 6px;
            border: 1px solid var(--border_color);
            width: 100%;
        }

        .student_rec_header {
            padding: 12px;
            background: -webkit-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -moz-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -o-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -ms-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
        }

        .student_rec_footer {
            padding: 12px;
            margin-top: 16px;
            border-top: 1px solid var(--border_color);
        }

        .student_rec_content {
            padding: 16px;
            max-height: 300px;
            min-height: 300px;
        }

        /* ---- Modern refresh: cards, table, status badges (Section 20) ---- */
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

        table.Crm_table_active3 thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

        table.Crm_table_active3 tbody tr {
            transition: background-color .12s ease;
        }

        table.Crm_table_active3 tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        table.Crm_table_active3 tbody td {
            vertical-align: middle;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .3px;
            white-space: nowrap;
        }

        .status-badge.status-active {
            background: #e6f7ec;
            color: #1e8e4f;
        }

        .status-badge.status-inactive {
            background: #fdecec;
            color: #d64545;
        }

        .dropdown-item.text-danger:hover {
            background-color: #fdecec;
        }

        .assign-subject-selection-container {
            border: 1px solid var(--border_color);
            border-radius: 8px;
            max-height: 650px;
            overflow-y: auto;
        }
    </style>
@endpush

@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('academics.subject')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Assign @lang('academics.subject')</a>
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid p-0">
        @include('components.partials.permanent-delete-note', ['rule' => 'assign_subject'])
    </div>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            @php
                // Button-level permissions (Section 21). The "Copy" row action
                // is treated as an Add-shortcut (it just pre-fills this same
                // form via JS, no route of its own) so it's gated on the same
// 'assign-subject.store' permission as the Add form itself.
$canAddAssignSubject = Auth::user()->hasPermission('assign-subject.store');
$canEditAssignSubject = Auth::user()->hasPermission('assign-subject.update');
$canDeleteAssignSubject = Auth::user()->hasPermission('assign-subject.destroy');
                $showAssignSubjectForm = isset($assignSubjectId) ? $canEditAssignSubject : $canAddAssignSubject;
            @endphp

            <div class="row">
                @if ($showAssignSubjectForm)
                    <div class="col-lg-3">
                        <div class="row">
                            <div class="col-lg-12">

                                {{-- Update form --}}
                                <form method="POST" class="form-horizontal"
                                    action="{{ isset($assignSubjectId) ? route('assign-subject.update', $assignSubjectId) : route('assign-subject.store') }}">

                                    @csrf
                                    <input type="hidden" name="dt_page" class="dt-page-field"
                                        value="{{ (int) request('dt_page', 0) }}">

                                    <div class="white-box">
                                        <div class="main-title">
                                            <h3 class="mb-20">
                                                @if (isset($assignSubjectId))
                                                    Update Assign Subject
                                                @else
                                                    @lang('academics.assign_subject_create')
                                                @endif
                                            </h3>
                                        </div>

                                        <div class="add-visitor">
                                            {{-- Intake --}}
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="primary_input">
                                                        <label class="primary_input_label">Intake <span>*</span></label>
                                                        @if (isset($assignSubjectId))
                                                            <select name="intake_id" id="intake-select"
                                                                class="primary_select form-control {{ $errors->has('intake_id') ? ' is-invalid' : '' }}"
                                                                readonly>
                                                                <option
                                                                    value="{{ $intakes->firstWhere('id', $selectedIntake)?->id }}"
                                                                    selected>
                                                                    {{ $intakes->firstWhere('id', $selectedIntake)?->name }}
                                                                </option>

                                                            </select>
                                                        @else
                                                            <select name="intake_id" id="intake-select"
                                                                class="primary_select form-control {{ $errors->has('intake_id') ? ' is-invalid' : '' }}">
                                                                <option value="">Select intake</option>
                                                                @foreach ($intakes as $intake)
                                                                    <option value="{{ $intake->id }}"
                                                                        {{ old('intake_id', $selectedIntake ?? '') == $intake->id ? 'selected' : '' }}>
                                                                        {{ $intake->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>



                                            {{-- Course --}}
                                            <div class="row mt-20">
                                                <div class="col-lg-12">
                                                    <div class="primary_input">
                                                        <label class="primary_input_label">Course <span>*</span></label>

                                                        @if (isset($assignSubjectId))
                                                            <select name="course_id" id="course-select"
                                                                class="primary_select form-control {{ $errors->has('course_id') ? ' is-invalid' : '' }}"
                                                                readonly>
                                                                <option
                                                                    value="{{ $courses->firstWhere('id', $selectedCourse)?->id }}"
                                                                    selected>
                                                                    {{ $courses->firstWhere('id', $selectedCourse)?->name }}
                                                                </option>

                                                            </select>
                                                        @else
                                                            {{-- Normal selectable dropdown --}}
                                                            <select name="course_id" id="course-select"
                                                                class="primary_select form-control {{ $errors->has('course_id') ? ' is-invalid' : '' }}">
                                                                <option value="">Select course</option>
                                                                @foreach ($courses as $course)
                                                                    <option value="{{ $course->id }}"
                                                                        {{ old('course_id', $selectedCourse ?? '') == $course->id ? 'selected' : '' }}>
                                                                        {{ $course->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('course_id'))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('course_id') }}</span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>


                                            {{-- Subjects --}}
                                            <div class="row mt-20">
                                                <div class="col-lg-12">
                                                    <label class="primary_input_label">@lang('academics.subject')
                                                        <span>*</span></label>
                                                    <div class="assign-subject-selection-container">
                                                        @foreach ($subjects as $subject)
                                                            <div class="ml-1 mt-3 pb-3"
                                                                style="border-bottom: 1px solid var(--border_color);">
                                                                <div>
                                                                    <input type="checkbox" id="sub{{ $subject->id }}"
                                                                        value="{{ $subject->id }}" name="subjects[]"
                                                                        class="common-checkbox"
                                                                        {{ in_array($subject->id, old('subjects', $selectedSubjects ?? [])) ? 'checked' : '' }}>
                                                                    <label
                                                                        for="sub{{ $subject->id }}"><strong>{{ $subject->name }}</strong></label>
                                                                </div>

                                                                <div class="mt-1 d-flex align-items-center ml-30">
                                                                    <label class="mr-2 mb-0" style="min-width:140px"
                                                                        for="demo_time_{{ $subject->id }}">
                                                                        Demonstrator Time
                                                                    </label>
                                                                    <input type="text"
                                                                        id="demo_time_{{ $subject->id }}"
                                                                        name="times[{{ $subject->id }}][demonstrator_time]"
                                                                        class="form-control form-control-sm"
                                                                        style="width:150px" placeholder="Demonstrator time"
                                                                        value="{{ old('times.' . $subject->id . '.demonstrator_time', $selectedTimes[$subject->id]['demonstrator_time'] ?? $subject->demonstrator_time) }}">
                                                                </div>

                                                                <div class="mt-1 d-flex align-items-center ml-30">
                                                                    <label class="mr-2 mb-0" style="min-width:140px"
                                                                        for="practical_time_{{ $subject->id }}">
                                                                        Practical Time
                                                                    </label>
                                                                    <input type="text"
                                                                        id="practical_time_{{ $subject->id }}"
                                                                        name="times[{{ $subject->id }}][practical_time]"
                                                                        class="form-control form-control-sm"
                                                                        style="width:150px" placeholder="Practical time"
                                                                        value="{{ old('times.' . $subject->id . '.practical_time', $selectedTimes[$subject->id]['practical_time'] ?? $subject->practical_time) }}">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        @if ($errors->has('subjects'))
                                                            <span
                                                                class="text-danger">{{ $errors->first('subjects') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Submit --}}
                                            <div class="row mt-40">
                                                <div class="col-lg-12 text-center">
                                                    <button class="primary-btn fix-gr-bg col-12 submit" type="submit">
                                                        <span class="ti-check"></span>
                                                        {{ isset($assignSubjectId) ? __('common.update') : __('academics.save_class') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                @endif

                <div class="col-lg-{{ $showAssignSubjectForm ? '9' : '12' }}">

                    <div class="row white-box">
                        <div class="col-lg-12">

                            <div class="main-title">
                                <h3 class="mb-20">@lang('academics.assign_subject') List</h3>
                            </div>

                            <x-table>
                                <table id="table_id" class="table Crm_table_active3" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>@lang('common.sl')</th>
                                            <th>Intake</th>
                                            <th>Course</th>
                                            <th>Subjects</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assign_subjects as $key => $assign)
                                            @if ($key == 2)
                                            @endif
                                            <tr>
                                                <td>{{ $key + 1 }}</td>

                                                <td>{{ $assign[0]->intake->name ?? '-' }}</td>

                                                <td>
                                                    <a href="#">
                                                        {{ $assign[0]->course->name ?? '-' }}
                                                    </a>
                                                </td>

                                                <td>
                                                    @foreach ($assign as $index => $assignSubject)
                                                        {{ $assignSubject->subject->name }}
                                                        @if (!$loop->last)
                                                            /
                                                        @endif
                                                    @endforeach
                                                </td>


                                                <td valign="top">
                                                    @php
                                                        $routeList = [];
                                                        if ($canEditAssignSubject) {
                                                            $routeList[] =
                                                                '<a class="dropdown-item assign-subject-edit-link" href="' .
                                                                route('assign-subject.edit', $assign[0]->id) .
                                                                '">' .
                                                                __('common.edit') .
                                                                '</a>';
                                                        }
                                                        if ($canDeleteAssignSubject) {
                                                            $routeList[] =
                                                                '<a class="dropdown-item text-danger" data-toggle="modal" data-target="#deleteModal' .
                                                                $assign[0]->id .
                                                                '" href="#">Delete</a>';
                                                        }
                                                        if ($canAddAssignSubject) {
                                                            $routeList[] =
                                                                '<a class="dropdown-item copy-btn" href="#" data-intake-id="' .
                                                                ($assign[0]->intake->id ?? '') .
                                                                '" data-course-id="' .
                                                                ($assign[0]->course->id ?? '') .
                                                                '" data-subject-ids="' .
                                                                htmlspecialchars(
                                                                    json_encode(
                                                                        $assign->pluck('subject.id')->toArray(),
                                                                    ),
                                                                ) .
                                                                '">' .
                                                                __('common.copy') .
                                                                '</a>';
                                                        }
                                                    @endphp
                                                    @if (count($routeList) > 0)
                                                        <x-drop-down-action-component :routeList="$routeList" />
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            @if ($canDeleteAssignSubject)
                                                <div class="modal fade admin-query" id="deleteModal{{ $assign[0]->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Delete Assignment</h4>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <div class="text-center">
                                                                    <h4>
                                                                        Are you sure you want to delete this subject
                                                                        assignment? This cannot be undone.
                                                                    </h4>
                                                                </div>

                                                                <div class="mt-40 d-flex justify-content-between">
                                                                    <button type="button" class="primary-btn tr-bg"
                                                                        data-dismiss="modal">
                                                                        @lang('common.cancel')
                                                                    </button>
                                                                    <form
                                                                        action="{{ route('assign-subject.destroy', $assign[0]->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        {{-- Same page-position mechanism as the
                                                                         Add/Edit form, synced to the live table
                                                                         page right before submit. --}}
                                                                        <input type="hidden" name="dt_page"
                                                                            class="dt-page-field" value="0">
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">
                                                                            Delete
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </x-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@include('components.partials.data_table_css')
@include('components.partials.data_table_js')
@push('script')
    <script>
        var table = $('#table_id').DataTable({
            responsive: true,
            dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3'" +
                "<'dt-toolbar-title'>" +
                "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-length'l><'dt-search'f><'dt-buttons-wrap'B>>" +
                ">" +
                "<'row'<'col-sm-12'tr>>" +
                "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>> ",
            stateSave: true,
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
        });

        var initialDtPage = parseInt('{{ (int) request('dt_page', 0) }}', 10);
        if (initialDtPage > 0) {
            table.page(initialDtPage).draw(false);
        }

        $(document).on('click', '.assign-subject-edit-link', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            var sep = href.indexOf('?') === -1 ? '?' : '&';
            window.location.href = href + sep + 'dt_page=' + table.page();
        });

        $(document).on('submit', 'form', function() {
            $(this).find('.dt-page-field').val(table.page());
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
                    url: '{{ route('assign-subject.get-courses', ':intakeId') }}'.replace(':intakeId',
                        intakeId),
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        console.log('Success response:', data);
                        var options = '<option value="">Select course</option>';

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
                        console.error('Status:', status);
                        courseSelect.html('<option value="">Error loading courses</option>');

                        // Trigger niceSelect update if using niceSelect
                        if (courseSelect.hasClass('primary_select')) {
                            courseSelect.niceSelect('update');
                        }
                    }
                });
            } else {
                // Reset course dropdown when no intake is selected
                courseSelect.html('<option value="">Select course</option>');

                // Trigger niceSelect update if using niceSelect
                if (courseSelect.hasClass('primary_select')) {
                    courseSelect.niceSelect('update');
                }
            }
        });

        // Handle copy button click
        $(document).on('click', '.copy-btn', function(e) {
            e.preventDefault();

            var intakeId = $(this).data('intake-id');
            var courseId = $(this).data('course-id');
            var subjectIds = $(this).data('subject-ids');

            // Set intake
            $('#intake-select').val(intakeId);
            $('#intake-select').niceSelect('update');

            // Trigger change event on intake-select to load courses for the copied intake
            $('#intake-select').trigger('change');

            // After courses are loaded (asynchronously), set the course and subjects
            // A small delay might be needed to ensure AJAX completes and options are populated
            setTimeout(function() {
                // Set course
                $('#course-select').val(courseId);
                $('#course-select').niceSelect('update');

                // Uncheck all subjects first
                $('input[name="subjects[]"]').prop('checked', false);

                // Check the subjects
                if (subjectIds && subjectIds.length > 0) {
                    subjectIds.forEach(function(subjectId) {
                        $('#sub' + subjectId).prop('checked', true);
                    });
                }
            }, 500); // Adjust delay as needed
        });
    </script>
@endpush
