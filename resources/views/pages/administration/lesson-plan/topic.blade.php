@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                 {{ isset($topic) ? 'Edit Topic' : 'Topic Adding' }}
                <div class="bc-pages">
                    <a href="/#">@lang('common.dashboard')</a>
                    <a href="#">@lang('lesson::lesson.Topic')</a>

                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
           
            <form class="form-horizontal" method="POST"  action="{{ isset($topic) ? route('topic.update', $topic->id) : route('topic.store') }}">
                 @csrf
                @if (isset($topic))
                    @method('PUT')
                @endif
                <div class="row">
                        <div class="col-lg-4 col-xl-3">
                            <div class="row">
                            <div class="col-lg-12">
                                <div class="white-box">
                                    <div class="main-title">
                                        <h3 class="mb-15">
                                            {{ isset($topic) ? 'Edit Topic' : 'Topic Adding' }}
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
                                                            {{ old('course_id', $topic->course_id ?? '') == $course->id ? 'selected' : '' }}>
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
                                                            {{ old('intake_id', $topic->intake_id ?? '') == $intake->id ? 'selected' : '' }}>
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
                                                            {{ old('subject_id', $topic->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
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

                                        <div class="row mt-15">
                                            <div class="col-lg-12">
                                                <label class="primary_input_label" for="">
                                                    Lesson
                                                    <span class="text-danger"> *</span>
                                                </label>
                                                <select id="lesson-select"
                                                    class="primary_select form-control {{ $errors->has('class') ? ' is-invalid' : '' }}"
                                                    name="lesson_id">
                                                    <option data-display="Select Lesson *" value="">
                                                        Select Lesson *
                                                    </option>
                                                   @foreach ($lessons as $lessonItem)
                                                        <option value="{{ $lessonItem->id }}"
                                                            {{ old('lesson_id', $topic->lesson_id ?? '') == $lessonItem->id ? 'selected' : '' }}>
                                                            {{ $lessonItem->name }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                                @error('lesson_id')
                                                    <span class="text-danger invalid-select"
                                                        role="alert">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="white-box mt-10">
                                                    <div class="row mb-3 align-items-center">
                                                        <div class="col-xl-9 col-lg-8 col-md-10 col-9">
                                                            <div class="main-title my-0 md:mt-auto">
                                                                <h5 class="mb-0">Topic Names</h5>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-3 col-lg-4 col-md-2 col-3 text-right">
                                                            <button type="button" class="primary-btn icon-only fix-gr-bg"
                                                                onclick="addRowTopic();" id="addRowBtn">
                                                                <span class="ti-plus"></span></button>
                                                        </div>
                                                    </div>
                                                    <table class="" id="productTable">
                                                        <thead></thead>
                                                        <tbody>
                                                            @if (isset($groupTopics) && $groupTopics->count())
                                                                @foreach ($groupTopics as $index => $l)
                                                                    <tr id="row{{ $index + 1 }}" class="mt-40 flex justify-content-between">
                                                                        <td>
                                                                            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                                                                            <div class="primary_input">
                                                                                <input
                                                                                    class="primary_input_field form-control{{ $errors->has('topic.' . $index) ? ' is-invalid' : '' }}"
                                                                                    type="text"
                                                                                    id="topic"
                                                                                    name="topic[]"
                                                                                    placeholder="{{ __('common.title') }}"
                                                                                    autocomplete="off"
                                                                                    value="{{ old('topic.' . $index, $l->exam_title ?? $l->name) }}">
                                                                                @if ($errors->has('topic.' . $index))
                                                                                    <span class="text-danger">{{ $errors->first('topic.' . $index) }}</span>
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
                                                                @endforeach
                                                            @else
                                                                <tr id="row1" class="mt-4 ">
                                                                    <td class="w-full">
                                                                        <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                                                                        <div class="primary_input">
                                                                            <input
                                                                                class="primary_input_field form-control w-full gap-2 mx-2 text-base p-3{{ $errors->has('topic.0') ? ' is-invalid' : '' }}"
                                                                                type="text"
                                                                                id="topic"
                                                                                name="topic[]"
                                                                                placeholder="{{ __('common.title') }}"
                                                                                autocomplete="off"
                                                                                value="{{ old('topic.0', isset($editData) ? $editData->exam_title : '') }}">
                                                                            @if ($errors->has('topic.0'))
                                                                                <span class="text-danger">{{ $errors->first('topic.0') }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <button class="primary-btn icon-only fix-gr-bg ml-2 px-2  gap-2" type="button" onclick="removeRow(this)">
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


                                         <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg" data-toggle="tooltip"
                                                title="{{ @$tooltip }}">
                                                <span class="ti-check"></span>
                                                @if (isset($data))
                                                    @lang('lesson::lesson.update_topic')
                                                @else
                                                    Save Topic
                                                @endif


                                            </button>
                                        </div>
                                    </div>


                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- @php
                            $tooltip = '';
                            if (userPermission('lesson.create-store')) {
                                $tooltip = '';
                            } elseif (userPermission('lesson-edit') && isset($data)) {
                                $tooltip = '';
                            } else {
                                $tooltip = 'You have no permission to add';
                            }
                        @endphp --}}
                       
                    </div>

            </form>

            <div class="col-lg-8 col-xl-9">
                <div class="white-box">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-15">Topic List</h3>
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
                                            <th>Lesson</th>
                                            <th>Topic</th>

                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                         @foreach ($topics as $groupIndex => $topicGroup)
                                            @php
                                                $firstTopic = $topicGroup->first();
                                            @endphp

                                        <tr>
                                             <td>{{ $loop->iteration }}</td>
                                            <td>{{ $firstTopic->course?->name ?? 'N/A' }}</td>
                                            <td>{{ $firstTopic->intake?->name ?? 'N/A' }}</td>
                                            <td>{{ $firstTopic->subject?->name ?? 'N/A' }}</td>
                                            <td>{{ $firstTopic->lesson?->name ?? 'N/A' }}</td>
                                            <td>
                                                @foreach ($topicGroup as $topic)
                                                    <div>{{ $topic->name }}</div>
                                                @endforeach
                                            </td>
                                            <td>
                                                <x-drop-down>
                                                    <a class="dropdown-item"  href="{{ route('topic.edit', $topic->id) }}">
                                                        @lang('common.edit')
                                                    </a>
                                                    <a class="dropdown-item" data-toggle="modal"
                                                        data-target="#deleteExamModal1" href="#">
                                                        @lang('common.delete')
                                                    </a>
                                                </x-drop-down>
                                            </td>
                                        </tr>

                                       <div class="modal fade admin-query" id="deleteExamModal1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">@lang('topic::topic.delete_topic')</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="text-center">
                                                            <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                        </div>

                                                        <div class="mt-40 d-flex justify-content-between">
                                                            <button type="button" class="primary-btn tr-bg"
                                                                data-dismiss="modal">
                                                                @lang('common.cancel')
                                                            </button>
                                                            <form
                                                                            action="{{ route('topic.destroy', $firstTopic->id) }}"
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
     // Add new Topic row
addRowTopic = () => {
    $("#addRowBtn").button("loading");
    var tableLength = $("#productTable tbody tr").length;
    var count = tableLength > 0
        ? parseInt($("#productTable tbody tr:last").attr("id").replace('row', '')) + 1
        : 1;

    $("#addRowBtn").button("reset");

    var newRow = "<tr id='row" + count + "' class='mt-30'>";
    // Input column
    newRow += "<td>";
    newRow += "<div class='primary_input pt-10'>";
    newRow += "<input class='primary_input_field form-control' type='text' placeholder='{{ __('common.title') }}' name='topic[]' autocomplete='off'>";
    newRow += "</div>";
    newRow += "</td>";
    // Delete button column
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


// Remove row without affecting first one
function removeRow(button) {
    const row = $(button).closest('tr');
    row.remove();
}

// Show delete modal for Topic (server delete)
function deleteTopic(id) {
    var modal = $('#deleteExamModal1');
    modal.find('input[name=id]').val(id);
    modal.modal('show');
}

// Handle course change event to load intakes dynamically
$('#course-select').on('change', function() {
    var courseId = $(this).val();
    var intakeSelect = $('#intake-select');
    var subjectSelect = $('#subject-select');
    var lessonSelect = $('#lesson-select');

    if (courseId) {
        // Show loading state
        intakeSelect.html('<option value="">Loading intakes...</option>');
        subjectSelect.html('<option value="">Select Subject *</option>');
        lessonSelect.html('<option data-display="Select Lesson *" value="">Select Lesson *</option>');

        // Make AJAX request to get intakes for selected course
        $.ajax({
            url: '{{ route("topic.get-intakes", ":courseId") }}'.replace(':courseId', courseId),
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
                        options += '<option value="' + intake.id + '">' + intake.name + '</option>';
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
                lessonSelect.html('<option data-display="Select Lesson *" value="">Select Lesson *</option>');

                // Trigger niceSelect update if using niceSelect
                if (intakeSelect.hasClass('primary_select')) {
                    intakeSelect.niceSelect('update');
                }
                if (subjectSelect.hasClass('primary_select')) {
                    subjectSelect.niceSelect('update');
                }
                if (lessonSelect.hasClass('primary_select')) {
                    lessonSelect.niceSelect('update');
                }
            }
        });
    } else {
        // Reset intake, subject and lesson dropdowns when no course is selected
        intakeSelect.html('<option value="">Select Intake *</option>');
        subjectSelect.html('<option value="">Select Subject *</option>');
        lessonSelect.html('<option data-display="Select Lesson *" value="">Select Lesson *</option>');

        // Trigger niceSelect update if using niceSelect
        if (intakeSelect.hasClass('primary_select')) {
            intakeSelect.niceSelect('update');
        }
        if (subjectSelect.hasClass('primary_select')) {
            subjectSelect.niceSelect('update');
        }
        if (lessonSelect.hasClass('primary_select')) {
            lessonSelect.niceSelect('update');
        }
    }
});

// Handle intake change event to load subjects dynamically
$('#intake-select').on('change', function() {
    var intakeId = $(this).val();
    var subjectSelect = $('#subject-select');
    var lessonSelect = $('#lesson-select');

    if (intakeId) {
        // Show loading state
        subjectSelect.html('<option value="">Loading subjects...</option>');
        lessonSelect.html('<option data-display="Select Lesson *" value="">Select Lesson *</option>');

        // Make AJAX request to get subjects for selected intake
        $.ajax({
            url: '{{ route("topic.get-subjects", ":intakeId") }}'.replace(':intakeId', intakeId),
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
                        options += '<option value="' + subject.id + '">' + subject.name + '</option>';
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
                subjectSelect.html('<option value="">Error loading subjects</option>');
                lessonSelect.html('<option data-display="Select Lesson *" value="">Select Lesson *</option>');

                // Trigger niceSelect update if using niceSelect
                if (subjectSelect.hasClass('primary_select')) {
                    subjectSelect.niceSelect('update');
                }
                if (lessonSelect.hasClass('primary_select')) {
                    lessonSelect.niceSelect('update');
                }
            }
        });
    } else {
        // Reset subject and lesson dropdowns when no intake is selected
        subjectSelect.html('<option value="">Select Subject *</option>');
        lessonSelect.html('<option data-display="Select Lesson *" value="">Select Lesson *</option>');

        // Trigger niceSelect update if using niceSelect
        if (subjectSelect.hasClass('primary_select')) {
            subjectSelect.niceSelect('update');
        }
        if (lessonSelect.hasClass('primary_select')) {
            lessonSelect.niceSelect('update');
        }
    }
});

// Handle subject change event to load lessons dynamically
$('#subject-select').on('change', function() {
    var subjectId = $(this).val();
    var lessonSelect = $('#lesson-select');

    if (subjectId) {
        // Show loading state
        lessonSelect.html('<option data-display="Loading lessons..." value="">Loading lessons...</option>');

        // Make AJAX request to get lessons for selected subject
        $.ajax({
            url: '{{ route("topic.get-lessons", ":subjectId") }}'.replace(':subjectId', subjectId),
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(data) {
                console.log('Lessons response:', data);
                var options = '<option data-display="Select Lesson *" value="">Select Lesson *</option>';

                if (data.lessons && data.lessons.length > 0) {
                    data.lessons.forEach(function(lesson) {
                        options += '<option value="' + lesson.id + '">' + lesson.name + '</option>';
                    });
                } else {
                    options = '<option data-display="No lessons available for this subject" value="">No lessons available for this subject</option>';
                }

                lessonSelect.html(options);

                // Trigger niceSelect update if using niceSelect
                if (lessonSelect.hasClass('primary_select')) {
                    lessonSelect.niceSelect('update');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading lessons:', error);
                console.error('XHR response:', xhr.responseText);
                lessonSelect.html('<option data-display="Error loading lessons" value="">Error loading lessons</option>');

                // Trigger niceSelect update if using niceSelect
                if (lessonSelect.hasClass('primary_select')) {
                    lessonSelect.niceSelect('update');
                }
            }
        });
    } else {
        // Reset lesson dropdown when no subject is selected
        lessonSelect.html('<option data-display="Select Lesson *" value="">Select Lesson *</option>');

        // Trigger niceSelect update if using niceSelect
        if (lessonSelect.hasClass('primary_select')) {
            lessonSelect.niceSelect('update');
        }
    }
});

    </script>
     <script>
        $('#table_id').DataTable({
             responsive: true,
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
@endpush
