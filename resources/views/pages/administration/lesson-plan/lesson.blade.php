@extends('layouts.app')
@push('css')
    <style>
        /* ---- Modern refresh: cards, table styling (Section 22) ---- */
        .white-box {
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
        }

        .white-box .main-title h3,
        .white-box .main-title h5 {
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
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Lesson</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Lesson</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @php
                // Button-level permissions (Section 22).
                $canAddLesson = Auth::user()->hasPermission('lesson.store');
                $canEditLesson = Auth::user()->hasPermission('lesson.update');
                $canDeleteLesson = Auth::user()->hasPermission('lesson.destroy');
                $showLessonForm = isset($lesson) ? $canEditLesson : $canAddLesson;
            @endphp

            @if (isset($lesson) && $canAddLesson)
                <div class="row">
                    <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                        <a href="{{ route('lesson.index') }}" class="primary-btn small fix-gr-bg">
                            <span class="ti-plus pr-2"></span>
                            @lang('common.add')
                        </a>
                    </div>
                </div>
            @endif
            {{-- @if (isset($lesson))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => ['exam-update', $lesson->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
        @else
            @if (userPermission('lesson.create-store'))
                {{ Form::open(['class' => 'form-horizontal', 'route' => 'lesson.storelesson', 'method' => 'POST']) }}
            @endif
        @endif --}}


            <div class="row">
                @if ($showLessonForm)
                    <div class="col-lg-4 col-xl-3">
                        <form class="form-horizontal" method="POST"
                            action="{{ isset($lesson) ? route('lesson.update', $lesson->id) : route('lesson.store') }}">
                            @csrf
                            @method('POST')
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="white-box">
                                        <div class="main-title">
                                            <h3 class="mb-15">
                                                {{ isset($lesson) ? __('Edit Lesson') : 'Lesson Adding' }}
                                            </h3>
                                        </div>

                                        <div class="add-visitor">
                                            {{-- Course --}}
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label class="primary_input_label">Course <span
                                                            class="text-danger">*</span></label>
                                                    <select id="course-select"
                                                        class="primary_select form-control @error('course_id') is-invalid @enderror"
                                                        name="course_id">
                                                        <option value="">Select Course *</option>
                                                        @foreach ($courses as $course)
                                                            <option value="{{ $course->id }}"
                                                                {{ old('course_id', $lesson->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                                                {{ $course->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('course_id')
                                                        <span class="text-danger invalid-select"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Intake --}}
                                            <div class="row mt-15">
                                                <div class="col-lg-12">
                                                    <label class="primary_input_label">Intake <span
                                                            class="text-danger">*</span></label>
                                                    <select id="intake-select"
                                                        class="primary_select form-control @error('intake_id') is-invalid @enderror"
                                                        name="intake_id">
                                                        <option value="">Select Intake *</option>
                                                        @foreach ($intakes as $intake)
                                                            <option value="{{ $intake->id }}"
                                                                {{ old('intake_id', $lesson->intake_id ?? '') == $intake->id ? 'selected' : '' }}>
                                                                {{ $intake->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('intake_id')
                                                        <span class="text-danger invalid-select"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Subject --}}
                                            <div class="row mt-15">
                                                <div class="col-lg-12">
                                                    <label class="primary_input_label">Subject <span
                                                            class="text-danger">*</span></label>
                                                    <select id="subject-select"
                                                        class="primary_select form-control @error('subject_id') is-invalid @enderror"
                                                        name="subject_id">
                                                        <option value="">Select Subject *</option>
                                                        @foreach ($subjects as $subject)
                                                            <option value="{{ $subject->id }}"
                                                                {{ old('subject_id', $lesson->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                                                                {{ $subject->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('subject_id')
                                                        <span class="text-danger invalid-select"
                                                            role="alert">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Lesson Names --}}
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="white-box mt-10">
                                                        <div class="row mb-3 align-items-center">
                                                            <div class="col-xl-9 col-lg-8 col-md-10 col-9">
                                                                <div class="main-title my-0 md:mt-auto">
                                                                    <h5 class="mb-0">Lesson Names</h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-xl-3 col-lg-4 col-md-2 col-3 text-right">
                                                                <button type="button"
                                                                    class="primary-btn icon-only fix-gr-bg"
                                                                    onclick="addRowLesson();" id="addRowBtn">
                                                                    <span class="ti-plus"></span>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <table class="" id="productTable">
                                                            <thead></thead>
                                                            <tbody>
                                                                @if (isset($groupLessons) && $groupLessons->count())
                                                                    @foreach ($groupLessons as $index => $l)
                                                                        <tr id="row{{ $index + 1 }}">
                                                                            <td>
                                                                                <input type="hidden" name="url"
                                                                                    id="url"
                                                                                    value="{{ URL::to('/') }}">
                                                                                <div class="primary_input">
                                                                                    <input
                                                                                        class="primary_input_field form-control"
                                                                                        type="text" name="lesson[]"
                                                                                        value="{{ old('lesson.' . $index, $l->name) }}"
                                                                                        placeholder="{{ __('common.title') }}"
                                                                                        autocomplete="off">
                                                                                    @if ($errors->has('lesson.' . $index))
                                                                                        <span
                                                                                            class="text-danger">{{ $errors->first('lesson.' . $index) }}</span>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <button
                                                                                    class="primary-btn icon-only fix-gr-bg ml-2"
                                                                                    type="button"
                                                                                    onclick="removeRow(this)">
                                                                                    <span class="ti-trash"></span>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr id="row1">
                                                                        <td>
                                                                            <div class="primary_input">
                                                                                <input
                                                                                    class="primary_input_field form-control"
                                                                                    type="text" name="lesson[]"
                                                                                    value="{{ old('lesson.0') }}"
                                                                                    placeholder="{{ __('common.title') }}"
                                                                                    autocomplete="off">
                                                                                @if ($errors->has('lesson.0'))
                                                                                    <span
                                                                                        class="text-danger">{{ $errors->first('lesson.0') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <button
                                                                                class="primary-btn icon-only fix-gr-bg ml-2"
                                                                                type="button" onclick="removeRow(this)">
                                                                                <span class="ti-trash"></span>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>

                                                        </table>

                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Submit --}}
                                            <div class="row mt-40">
                                                <div class="col-lg-12 text-center">
                                                    <button class="primary-btn fix-gr-bg col-12" data-toggle="tooltip"
                                                        title="{{ $tooltip ?? '' }}">
                                                        <span class="ti-check"></span>
                                                        {{ isset($lesson) ? __('update lesson') : 'Save Lesson' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div> {{-- /.add-visitor --}}
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="col-lg-{{ $showLessonForm ? '8' : '12' }} col-xl-{{ $showLessonForm ? '9' : '12' }}">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">Lesson List</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id" class="table data-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>@lang('common.sl')</th>
                                                <th>Course</th>
                                                <th>Intake</th>
                                                <th>Subject</th>
                                                <th>Lessons</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lessons as $groupIndex => $lessonGroup)
                                                @php
                                                    $firstLesson = $lessonGroup->first();
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $firstLesson->course->name ?? 'N/A' }}</td>
                                                    <td>{{ $firstLesson->intake->name ?? 'N/A' }}</td>
                                                    <td>{{ $firstLesson->subject->name ?? 'N/A' }}</td>

                                                    <td>
                                                        @foreach ($lessonGroup as $lesson)
                                                            <div>{{ $lesson->name }}</div>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @if ($canEditLesson || $canDeleteLesson)
                                                            <x-drop-down>
                                                                @if ($canEditLesson)
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('lesson.edit', $firstLesson->id) }}">
                                                                        @lang('common.edit')
                                                                    </a>
                                                                @endif
                                                                @if ($canDeleteLesson)
                                                                    <a class="dropdown-item" data-toggle="modal"
                                                                        data-target="#deleteLessonModal{{ $firstLesson->id }}"
                                                                        href="#">
                                                                        @lang('common.delete')
                                                                    </a>
                                                                @endif
                                                            </x-drop-down>
                                                        @else
                                                            <span class="text-muted">&mdash;</span>
                                                        @endif

                                                        {{-- Only showing delete for one record; optionally allow bulk delete --}}
                                                        @if ($canDeleteLesson)
                                                            <div class="modal fade admin-query"
                                                                id="deleteLessonModal{{ $firstLesson->id }}">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">@lang('lesson::lesson.delete_lesson')</h4>
                                                                            <button type="button" class="close"
                                                                                data-dismiss="modal">&times;</button>
                                                                        </div>

                                                                        <div class="modal-body">
                                                                            <div class="text-center">
                                                                                <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                                            </div>

                                                                            <div
                                                                                class="mt-40 d-flex justify-content-between">
                                                                                <button type="button"
                                                                                    class="primary-btn tr-bg"
                                                                                    data-dismiss="modal">@lang('common.cancel')</button>
                                                                                <form
                                                                                    action="{{ route('lesson.destroy', $firstLesson->id) }}"
                                                                                    method="POST">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button class="primary-btn fix-gr-bg"
                                                                                        type="submit">
                                                                                        @lang('common.delete')
                                                                                    </button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

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
@endsection
@include('components.partials.data_table_css')
@include('components.partials.data_table_js')
@push('script')
    <script>
        $('#table_id').DataTable({
            responsive: true,
            dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 '" +
                "<'dt-toolbar-title'>" +
                "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-search'f><'dt-buttons-wrap'B>>" +
                ">" +
                "<'row'<'col-sm-12'tr>>" +
                "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>>",
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
    </script>
    <script>
        addRowLesson = () => {
            $("#addRowBtn").button("loading");
            var tableLength = $("#productTable tbody tr").length;
            var count = tableLength + 1;

            $("#addRowBtn").button("reset");

            var newRow = "<tr id='row" + count + "'>";
            newRow += "<td>";
            newRow += "<div class='primary_input pt-10'>";
            newRow +=
                "<input class='primary_input_field form-control' type='text' placeholder='title' name='lesson[]' autocomplete='off'>";
            newRow += "</div>";
            newRow += "</td>";
            newRow += "<td>";
            newRow += "<button class='primary-btn icon-only fix-gr-bg ml-2' type='button' onclick='removeRow(this)'>";
            newRow += "<span class='ti-trash'></span>";
            newRow += "</button>";
            newRow += "</td>";
            newRow += "</tr>";

            if (tableLength > 0) {
                $("#productTable tbody tr:last").after(newRow);
            } else {
                $("#productTable tbody").append(newRow);
            }
            $(".common-select").addClass("new_select_css");
        };


        function removeRow(button) {
            const row = $(button).closest('tr');
            const rowIndex = row.index();

            // Prevent removing the first row (index 0)
            if (rowIndex === 0) {
                return;
            }

            row.remove();
        }

        function deleteLesson(id) {
            var modal = $('#deleteLessonModal');
            modal.find('input[name=id]').val(id)
            modal.modal('show');
        }

        // Handle course change event to load intakes dynamically
        $('#course-select').on('change', function() {
            var courseId = $(this).val();
            var intakeSelect = $('#intake-select');
            var subjectSelect = $('#subject-select');

            if (courseId) {
                // Show loading state
                intakeSelect.html('<option value="">Loading intakes...</option>');
                subjectSelect.html('<option value="">Select Subject *</option>');

                // Make AJAX request to get intakes for selected course
                $.ajax({
                    url: '{{ route('lesson.get-intakes', ':courseId') }}'.replace(':courseId', courseId),
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        console.log('Intakes response:', data);
                        var options = '<option value="">Select Intake *</option>';

                        if (data.intakes && data.intakes.length > 0) {
                            data.intakes.forEach(function(intake) {
                                options += '<option value="' + intake.id + '">' + intake.name +
                                    '</option>';
                            });
                        } else {
                            options = '<option value="">No intakes available for this course</option>';
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
                        subjectSelect.html('<option value="">Select Subject *</option>');

                        // Trigger niceSelect update if using niceSelect
                        if (intakeSelect.hasClass('primary_select')) {
                            intakeSelect.niceSelect('update');
                        }
                        if (subjectSelect.hasClass('primary_select')) {
                            subjectSelect.niceSelect('update');
                        }
                    }
                });
            } else {
                // Reset intake and subject dropdowns when no course is selected
                intakeSelect.html('<option value="">Select Intake *</option>');
                subjectSelect.html('<option value="">Select Subject *</option>');

                // Trigger niceSelect update if using niceSelect
                if (intakeSelect.hasClass('primary_select')) {
                    intakeSelect.niceSelect('update');
                }
                if (subjectSelect.hasClass('primary_select')) {
                    subjectSelect.niceSelect('update');
                }
            }
        });

        // Handle intake change event to load subjects dynamically
        $('#intake-select').on('change', function() {
            var intakeId = $(this).val();
            var subjectSelect = $('#subject-select');

            if (intakeId) {
                // Show loading state
                subjectSelect.html('<option value="">Loading subjects...</option>');

                // Make AJAX request to get subjects for selected intake
                $.ajax({
                    url: '{{ route('lesson.get-subjects', ':intakeId') }}'.replace(':intakeId', intakeId),
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        console.log('Subjects response:', data);
                        var options = '<option value="">Select Subject *</option>';

                        if (data.subjects && data.subjects.length > 0) {
                            data.subjects.forEach(function(subject) {
                                options += '<option value="' + subject.id + '">' + subject
                                    .name + '</option>';
                            });
                        } else {
                            options = '<option value="">No subjects available for this intake</option>';
                        }

                        subjectSelect.html(options);

                        // Trigger niceSelect update if using niceSelect
                        if (subjectSelect.hasClass('primary_select')) {
                            subjectSelect.niceSelect('update');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading subjects:', error);
                        console.error('XHR response:', xhr.responseText);
                        console.error('Status:', status);
                        console.error('URL:', this.url);
                        subjectSelect.html('<option value="">Error loading subjects</option>');

                        // Trigger niceSelect update if using niceSelect
                        if (subjectSelect.hasClass('primary_select')) {
                            subjectSelect.niceSelect('update');
                        }
                    }
                });
            } else {
                // Reset subject dropdown when no intake is selected
                subjectSelect.html('<option value="">Select Subject *</option>');

                // Trigger niceSelect update if using niceSelect
                if (subjectSelect.hasClass('primary_select')) {
                    subjectSelect.niceSelect('update');
                }
            }
        });
    </script>
@endpush
