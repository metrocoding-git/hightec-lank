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
                        <a href="{{ route('exam') }}" class="primary-btn small fix-gr-bg">
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
            <form class="form-horizontal" method="POST">
                <div class="row">
                    <div class="col-lg-4 col-xl-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="white-box">
                                    <div class="main-title">
                                        <h3 class="mb-15">
                                            @if (isset($data))
                                                @lang('lesson::lesson.edit_lesson')
                                            @else
                                                Lesson Adding
                                            @endif
                                        </h3>
                                    </div>
                                    <div class="add-visitor">


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label class="primary_input_label" for="">
                                                    Course
                                                    <span class="text-danger"> *</span>
                                                </label>
                                                <select
                                                    class="primary_select form-control {{ $errors->has('class') ? ' is-invalid' : '' }}"
                                                    id="select_class_lesson" name="class">
                                                    <option data-display="Select Course *" value="">
                                                        Select Course *
                                                    </option>
                                                    <option value="class1">
                                                        Course 1</option>
                                                    <option value="class2">
                                                        Course 2</option>
                                                </select>
                                                @if ($errors->has('class'))
                                                    <span class="text-danger invalid-select" role="alert">
                                                        {{ $errors->first('class') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-15">
                                            <div class="col-lg-12">
                                                <label class="primary_input_label" for="">
                                                    Intake
                                                    <span class="text-danger"> *</span>
                                                </label>
                                                <select
                                                    class="primary_select form-control {{ $errors->has('class') ? ' is-invalid' : '' }}"
                                                    id="select_class_lesson" name="class">
                                                    <option data-display="Select Intake *" value="">
                                                        Select Intake *
                                                    </option>
                                                    <option value="class1">
                                                        Intake 1</option>
                                                    <option value="class2">
                                                        Intake 2</option>
                                                </select>
                                                @if ($errors->has('class'))
                                                    <span class="text-danger invalid-select" role="alert">
                                                        {{ $errors->first('class') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-15">
                                            <div class="col-lg-12" id="select_subject_div">
                                                <label class="primary_input_label" for="">
                                                    {{ __('common.subject') }}
                                                    <span class="text-danger"> *</span>
                                                </label>
                                                <select
                                                    class="primary_select form-control{{ $errors->has('subject') ? ' is-invalid' : '' }}"
                                                    id="select_subject" name="subject">
                                                    <option data-display="@lang('common.select_subjects') *" value="">
                                                        @lang('common.select_subjects')*
                                                    </option>
                                                </select>
                                                <div class="pull-right loader" id="select_subject_loader"
                                                    style="margin-top: -30px;padding-right: 21px;">
                                                    <img src="{{ asset('Modules/Lesson/Resources/assets/images/pre-loader.gif') }}"
                                                        alt="" style="width: 28px;height:28px;">
                                                </div>
                                                @if ($errors->has('subject'))
                                                    <span class="text-danger invalid-select" role="alert">
                                                        {{ $errors->first('subject') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

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
                                                <span class="ti-plus"></span></button>
                                        </div>
                                    </div>
                                    <table class="" id="productTable">
                                        <thead>
                                            <tr>


                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="row1" class="mt-40">
                                                <td class="">
                                                    <input type="hidden" name="url" id="url"
                                                        value="{{ URL::to('/') }}">
                                                    <div class="primary_input">

                                                        <input
                                                            class="primary_input_field form-control{{ $errors->has('topic') ? ' is-invalid' : '' }}"
                                                            type="text" id="lesson"
                                                            placeholder="{{ __('common.title') }}" name="lesson[]"
                                                            autocomplete="off"
                                                            value="{{ isset($editData) ? $editData->exam_title : '' }}">

                                                    </div>
                                                </td>

                                                <td>
                                                    <button class="primary-btn icon-only fix-gr-bg ml-2" type="button">
                                                        <span class="ti-trash"></span>
                                                    </button>

                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
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
                        <div class="row mt-15">
                            <div class="col-lg-12">
                                <div class="white-box">
                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg" data-toggle="tooltip"
                                                title="{{ @$tooltip }}">
                                                <span class="ti-check"></span>
                                                @if (isset($data))
                                                    @lang('lesson::lesson.update_lesson')
                                                @else
                                                    Save Lesson
                                                @endif


                                            </button>
                                        </div>
                                    </div>
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
                                            <th> Intake </th>
                                            <th>Subject</th>
                                            <th>Lessons</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <tr>
                                            <td>1</td>
                                            <td> Intake 20 </td>

                                            <td>Course 1</td>
                                            <td>Subject 1</td>


                                            <td>
                                                <div class="row">
                                                    <div class="col-sm-10">Introduction,</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-10">Chapter 1,</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-10">Conclusion</div>
                                                </div>
                                            </td>

                                            <td>
                                                <x-drop-down>
                                                    <a class="dropdown-item" href="/">
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
                                                            <form>
                                                                <button class="primary-btn fix-gr-bg" type="submit">
                                                                    @lang('common.delete')
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

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
            var url = $("#url").val();
            var tableRow;
            var arrayNumber;
            var count;
            if (tableLength > 0) {
                tableRow = $("#productTable tbody tr:last").attr("id");
                arrayNumber = $("#productTable tbody tr:last").attr("class");
                count = tableRow.substring(3);
                count = Number(count) + 1;
                arrayNumber = Number(arrayNumber) + 1;
            } else {
                // no table row
                count = 1;
                arrayNumber = 0;
            }

            $("#addRowBtn").button("reset");
            var newRow = "<tr id='row1' class='mt-30'>";
            newRow += "<td class=''>";
            newRow += "<div class='primary_input pt-10'>";
            newRow +=
                "<input class='primary_input_field form-control' type='text' placeholder='title' id='lesson' name='lesson[]' autocomplete='off'>";

            newRow += "</div>";
            newRow += "</td>";

            newRow += "<td  class=''>";
            newRow +=
                "<button class='primary-btn icon-only fix-gr-bg' type='button' id='removeMark'>";
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

        function deleteLesson(id) {
            var modal = $('#deleteLessonModal');
            modal.find('input[name=id]').val(id)
            modal.modal('show');
        }
    </script>
@endpush
