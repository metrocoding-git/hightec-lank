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

        .primary_input_field, select.primary_select {
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
                <h1>Topic Overview</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Topic Overview</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        <form id="searchForm" method="GET" action="{{ route('topic-overview.index') }}" class="form-horizontal mb-0">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

                                <div class="col-lg-4">

                                    <select class="primary_select form-control @error('course_id') is-invalid @enderror"
                                        name="course_id" id="course-select" required>
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

                                    <select class="primary_select form-control @error('intake_id') is-invalid @enderror"
                                        name="intake_id" id="intake-select" required>
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
          
                                    <select class="primary_select form-control @error('subject_id') is-invalid @enderror"
                                        name="subject_id" id="subject-select" required>
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

                                <div class="col-lg-12 mt-10 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg col-12 p-2" id="searchBtn">
                                        <span class="ti-search pr-2"></span>
                                        @lang('common.search')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row mt-20">
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
                                            <th style="text-align:center;">Lesson</th>
                                            <th style="text-align:center;">Topic</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resultsBody">
                                        <tr>
                                            <td colspan="2" class="text-center">Please search to view topics</td>
                                        </tr>
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
@push('scripts')
 {{-- <script>
        $('#table_id').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
        });
    </script> --}}

<script>
$(document).ready(function() {
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
                url: '{{ route("topic-overview.get-intakes", ":courseId") }}'.replace(':courseId', courseId),
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
                url: '{{ route("topic-overview.get-subjects", ":intakeId") }}'.replace(':intakeId', intakeId),
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
                <td class="text-center">${item.lesson ? item.lesson.name : 'N/A'}</td>
                <td class="text-center">${item.name ? item.name : 'N/A'}</td>
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