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
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        <form id="searchForm" method="GET" action="{{ route('topic-overview.index') }}" class="form-horizontal">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                                
                                <div class="col-lg-4">
                                    <label class="primary_input_label" for="">Course
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="primary_select form-control @error('course_id') is-invalid @enderror"
                                        name="course_id" id="course_id" required>
                                        <option value="">Select Course *</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}"
                                                {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <span class="text-danger invalid-select" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-4">
                                    <label class="primary_input_label" for="">Intake
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="primary_select form-control @error('intake_id') is-invalid @enderror"
                                        name="intake_id" id="intake_id" required>
                                        <option value="">Select Intake *</option>
                                        @foreach ($intakes as $intake)
                                            <option value="{{ $intake->id }}"
                                                {{ request('intake_id') == $intake->id ? 'selected' : '' }}>
                                                {{ $intake->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('intake_id')
                                        <span class="text-danger invalid-select" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-4">
                                    <label class="primary_input_label" for="">Subject
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="primary_select form-control @error('subject_id') is-invalid @enderror"
                                        name="subject_id" id="subject_id" required>
                                        <option value="">Select Subject *</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <span class="text-danger invalid-select" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-12 mt-20 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg" id="searchBtn">
                                        <span class="ti-search pr-2"></span>
                                        @lang('common.search')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row mt-40">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title" style="padding-left: 15px;">
                                    <!-- Title remains unchanged -->
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <x-table>
                                <table id="table_id" class="table school-table-style" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Lesson</th>
                                            <th>Topic</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resultsBody">
    @forelse ($topics_detail as $data)
        <tr>
            <td>{{ $data->lesson->lesson_title ?? 'No Lesson' }}</td>
            <td>{{ $data->topic_title ?? 'No Topic' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="2" class="text-center">No topics found</td>
        </tr>
    @endforelse
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

@push('scripts')

<script>
$(document).ready(function() {
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();

        var searchBtn = $('#searchBtn');
        var originalText = searchBtn.html();
        searchBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Searching...');

        var formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            type: 'GET',
            data: formData,
            success: function(data) {
    let rows = '';
    if (data.length > 0) {
        data.forEach(function(item) {
            rows += `<tr>
                <td>${item.lesson ? item.lesson.lesson_title : 'No Lesson'}</td>
                <td>${item.topic_title ?? 'No Topic'}</td>
            </tr>`;
        });
    } else {
        rows = `<tr><td colspan="2" class="text-center">No topics found</td></tr>`;
    }
    $('#resultsBody').html(rows);
},
            error: function() {
                alert('Error occurred during search');
            },
            complete: function() {
                searchBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>


@endpush