@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')



    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('lesson::lesson.topic_overview')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('lesson::lesson.lesson')</a>
                    <a href="#">@lang('lesson::lesson.topic_overview')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">

        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'search-topic-overview', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_lesson_Plan']) }} --}}
                    <form class="form-horizontal">
                        <div class="row">

                            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                            <div class="col-lg-4 ">
                                <label class="primary_input_label" for="">Course
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="primary_select form-control {{ $errors->has('class') ? ' is-invalid' : '' }}"
                                    id="select_class" name="intake">
                                    <option data-display="" value="">
                                        Select Course *</option>
                                    @if (isset($classes))
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ isset($intake) ? ($intake == $class->id ? 'selected' : '') : '' }}>
                                                {{ $class->class_name }}</option>
                                        @endforeach
                                    @endif
                                </select>

                                @if ($errors->has('class'))
                                    <span class="text-danger invalid-select" role="alert">
                                        {{ $errors->first('class') }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-lg-4 ">
                                <label class="primary_input_label" for="">Intake
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="primary_select form-control {{ $errors->has('class') ? ' is-invalid' : '' }}"
                                    id="select_class" name="intake">
                                    <option data-display="" value="">
                                        Select Intake *</option>
                                    @if (isset($classes))
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ isset($intake) ? ($intake == $class->id ? 'selected' : '') : '' }}>
                                                {{ $class->class_name }}</option>
                                        @endforeach
                                    @endif
                                </select>

                                @if ($errors->has('class'))
                                    <span class="text-danger invalid-select" role="alert">
                                        {{ $errors->first('class') }}
                                    </span>
                                @endif
                            </div>


                            <div class="col-lg-4 ">
                                <label class="primary_input_label" for="">Subject
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="primary_select form-control {{ $errors->has('class') ? ' is-invalid' : '' }}"
                                    id="select_class" name="intake">
                                    <option data-display="" value="">
                                        Select Subject *</option>
                                    @if (isset($classes))
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ isset($intake) ? ($intake == $class->id ? 'selected' : '') : '' }}>
                                                {{ $class->class_name }}</option>
                                        @endforeach
                                    @endif
                                </select>

                                @if ($errors->has('class'))
                                    <span class="text-danger invalid-select" role="alert">
                                        {{ $errors->first('class') }}
                                    </span>
                                @endif
                            </div>
                            <div class="col-lg-12 mt-20 text-right">
                                <button type="submit" class="primary-btn small fix-gr-bg">
                                    <span class="ti-search pr-2"></span>
                                    @lang('common.search')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if (isset($topics_detail))
            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row mt-40">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title" style="padding-left: 15px;">

                                </div>
                            </div>
                        </div>
                        <div class="">
                            <x-table>
                                <table id="table_id" class="table school-table-style" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>@lang('lesson::lesson.lesson')</th>
                                            <th>@lang('lesson::lesson.topic')</th>
                                            <th>@lang('lesson::lesson.completed_date') </th>
                                            <th>@lang('lesson::lesson.teacher') </th>
                                            <th>@lang('common.status')</th>

                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($topics_detail as $data)
                                            <tr>
                                                <td>{{ $data->lesson_title != '' ? $data->lesson_title->lesson_title : '' }}
                                                </td>
                                                <td> {{ @$data->topic_title != '' ? @$data->topic_title : '' }}</td>
                                                <td>
                                                    @foreach ($data->lessonPlan as $status)
                                                        @if ($status->competed_date != '' && $status->completed_status != '')
                                                            {{ $status->competed_date }} <br>
                                                        @else
                                                            @if (date('Y-m-d') < $status->lessonDetail->lesson_date)
                                                                Assign
                                                            @else
                                                                Assigned
                                                            @endif
                                                            Date ({{ $status->lessonDetail->lesson_date }})<br>
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach ($data->lessonPlan as $status)
                                                        @if ($status->lessonDetail->competed_date != '' && $status->lessonDetail->completed_status != '')
                                                            <strong>
                                                                {{ $status->lessonDetail->teacherName->full_name }}
                                                            </strong> <br>
                                                        @else
                                                            <span>
                                                                {{ $status->lessonDetail->teacherName->full_name }}
                                                            </span><br>
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach ($data->lessonPlan as $status)
                                                        @if ($status->lessonDetail->competed_date != '' && $status->lessonDetail->completed_status != '')
                                                            <strong class="gradient-color2">@lang('lesson::lesson.completed')</strong> <br>
                                                        @else
                                                            <span class="gradient-color">@lang('lesson::lesson.incomplete')</span><br>
                                                        @endif
                                                    @endforeach
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
        @endif
        </div>
        </div>
    </section>

@endsection
@include('components.partials.data_table_js')
