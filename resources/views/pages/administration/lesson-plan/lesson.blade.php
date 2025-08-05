@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Lesson Adding</h1>
                <div class="bc-pages">
                    <a href="/#">@lang('common.dashboard')</a>
                    <a href="#">@lang('lesson::lesson.lesson')</a>

                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @if (isset($lesson))
                {{-- @if (userPermission('lesson.create-store')) --}}
                <div class="row">
                    <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                        <a href="{{ route('lesson.index') }}" class="primary-btn small fix-gr-bg">
                            <span class="ti-plus pr-2"></span>
                            @lang('common.add')
                        </a>
                    </div>
                </div>
                {{-- @endif --}}
            @endif
            {{-- @if (isset($lesson))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => ['exam-update', $lesson->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
        @else
            @if (userPermission('lesson.create-store'))
                {{ Form::open(['class' => 'form-horizontal', 'route' => 'lesson.storelesson', 'method' => 'POST']) }}
            @endif
        @endif --}}


            <form class="form-horizontal" method="POST"
                action="{{ isset($lesson) ? route('lesson.update', $lesson->id) : route('lesson.store') }}">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-lg-4 col-xl-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="white-box">
                                    <div class="main-title">
                                        <h3 class="mb-15">
                                            {{ isset($lesson) ? __('lesson::lesson.edit_lesson') : 'Lesson Adding' }}
                                        </h3>
                                    </div>

                                    <div class="add-visitor">
                                        {{-- Course --}}
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label class="primary_input_label">Course <span
                                                        class="text-danger">*</span></label>
                                                <select
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
                                                <select
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
                                                <select
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
                                                            <button type="button" class="primary-btn icon-only fix-gr-bg"
                                                                onclick="addRowLesson();" id="addRowBtn">
                                                                <span class="ti-plus"></span>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <table class="" id="productTable">
                                                        <thead></thead>
                                                        <tbody>
                                                            @if (isset($lesson) && $lesson->titles)
                                                                @foreach ($lesson->titles as $index => $title)
                                                                    <tr id="row{{ $index + 1 }}">
                                                                        <td>
                                                                            <input type="hidden" name="url"
                                                                                id="url" value="{{ URL::to('/') }}">
                                                                            <div class="primary_input">
                                                                                <input
                                                                                    class="primary_input_field form-control"
                                                                                    type="text" name="lesson[]"
                                                                                    value="{{ old('lesson.' . $index, $title) }}"
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
                                                                                type="button" onclick="removeRow(this)">
                                                                                <span class="ti-trash"></span>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr id="row1">
                                                                    <td>
                                                                        <div class="primary_input">
                                                                            <input class="primary_input_field form-control"
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
                                                                        <button class="primary-btn icon-only fix-gr-bg ml-2"
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
                                                <button class="primary-btn fix-gr-bg" data-toggle="tooltip"
                                                    title="{{ $tooltip ?? '' }}">
                                                    <span class="ti-check"></span>
                                                    {{ isset($lesson) ? __('lesson::lesson.update_lesson') : 'Save Lesson' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div> {{-- /.add-visitor --}}
                                </div>
                            </div>
                        </div>
                    </div>

            </form>

            <div class="col-lg-8 col-xl-9">
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
                                        @foreach ($lessons as $index => $lesson)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>

                                                {{-- Assuming relations are set in Lesson model --}}
                                                <td>{{ $lesson->course->name ?? 'N/A' }}</td>
                                                <td>{{ $lesson->intake->name ?? 'N/A' }}</td>
                                                <td>{{ $lesson->subject->name ?? 'N/A' }}</td>

                                                <td>
                                                    @php
                                                        // Decode the JSON stored in 'name' column to array
                                                        $titles = json_decode($lesson->name, true) ?? [];
                                                    @endphp

                                                    @foreach ($titles as $title)
                                                        <div class="row">
                                                            <div class="col-sm-10">{{ $title }},</div>
                                                        </div>
                                                    @endforeach
                                                </td>

                                                <td>
                                                    <x-drop-down>
                                                        <a class="dropdown-item"
                                                            href="{{ route('lesson.edit', $lesson->id) }}">
                                                            @lang('common.edit')
                                                        </a>
                                                        <a class="dropdown-item" data-toggle="modal"
                                                            data-target="#deleteLessonModal{{ $lesson->id }}"
                                                            href="#">
                                                            @lang('common.delete')
                                                        </a>
                                                    </x-drop-down>

                                                    {{-- Delete confirmation modal --}}
                                                    <div class="modal fade admin-query"
                                                        id="deleteLessonModal{{ $lesson->id }}">
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

                                                                    <div class="mt-40 d-flex justify-content-between">
                                                                        <button type="button" class="primary-btn tr-bg"
                                                                            data-dismiss="modal">
                                                                            @lang('common.cancel')
                                                                        </button>
                                                                        <form
                                                                            action="{{ route('lesson.destroy', $lesson->id) }}"
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
@include('components.partials.data_table_js')
@push('script')
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
    </script>
@endpush
