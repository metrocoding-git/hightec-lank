@extends('layouts.app')

@section('title')
    @lang('student.student_attendance_import')
@endsection

@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('student.student_attendance_import')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="{{ route('student-attendance') }}">@lang('student.student_attendance')</a>
                    <a href="#">@lang('student.student_attendance_import')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-12 d-flex align-items-center justify-content-between flex-wrap">
                                <div class="main-title m-0">
                                    <h3 class="mb-15">@lang('common.select_criteria')</h3>
                                </div>
                                <a href="{{ route('student-attendance') }}" class="primary-btn small fix-gr-bg pull-right mb-15">
                                    <span class="ti-back-left pr-2"></span>@lang('student.student_attendance')
                                </a>
                            </div>
                        </div>

                        <form id="attendanceImportForm" action="{{ route('student-attendance-import.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 mb-20">
                                    <label class="primary_input_label">Intake <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" id="intake_id" name="intake_id">
                                        <option value="">Select Intake</option>
                                        @foreach ($intakes as $intake)
                                            <option value="{{ $intake->id }}">{{ $intake->name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="intake_inline_error" class="text-danger d-none">Please select intake.</span>
                                    @error('intake_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-4 mb-20">
                                    <label class="primary_input_label">Course <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" id="course_id" name="course_id">
                                        <option value="">Select Course</option>
                                    </select>
                                    <span id="course_inline_error" class="text-danger d-none">Please select course.</span>
                                    @error('course_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-4 mb-20">
                                    <label class="primary_input_label">@lang('hr.attendance_date') <span class="text-danger">*</span></label>
                                    <input class="primary_input_field form-control" type="date" name="attendance_date"
                                        value="{{ old('attendance_date', date('Y-m-d')) }}" required>
                                    @error('attendance_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12 mb-20">
                                    <label class="primary_input_label">Excel / CSV File <span class="text-danger">*</span></label>
                                    <input type="file" class="primary_input_field form-control" name="attendance_file"
                                        accept=".xlsx,.xls,.csv" required>
                                    <small class="text-muted">Allowed: .xlsx, .xls, .csv (max 10MB)</small>
                                    @error('attendance_file')
                                        <br><span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-20">
                                <div class="col-lg-12 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg">
                                        <span class="ti-upload pr-2"></span>
                                        Import Attendance
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row mt-30">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title mb-20">
                            <h3>Uploaded Attendance Files</h3>
                        </div>

                        <x-table>
                            <table id="attendance_import_table" class="table data-table Crm_table_active3 no-footer dtr-inline" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Intake</th>
                                        <th>Course</th>
                                        <th>Attendance Date</th>
                                        <th>File Name</th>
                                        <th>Uploaded By</th>
                                        <th>Uploaded At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendanceImports as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->intake->name ?? '-' }}</td>
                                            <td>{{ $item->course->name ?? '-' }}</td>
                                            <td>{{ $item->attendance_date }}</td>
                                            <td>{{ $item->file_name }}</td>
                                            <td>{{ $item->uploader->name ?? '-' }}</td>
                                            <td>{{ $item->created_at ? $item->created_at->format('Y-m-d H:i') : '-' }}</td>
                                            <td>
                                                <a href="{{ route('student-attendance-import.download', $item->id) }}" class="primary-btn small fix-gr-bg">
                                                    <span class="ti-download"></span>
                                                    Download
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No uploaded attendance files found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </x-table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@include('components.partials.data_table_js')
@include('components.partials.data_table_css')

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#attendance_import_table').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                order: [
                    [6, 'desc']
                ]
            });

            $('#attendanceImportForm').on('submit', function(e) {
                var intakeId = $('#intake_id').val();
                var courseId = $('#course_id').val();
                var hasError = false;

                if (!intakeId) {
                    $('#intake_inline_error').removeClass('d-none');
                    hasError = true;
                } else {
                    $('#intake_inline_error').addClass('d-none');
                }

                if (!courseId) {
                    $('#course_inline_error').removeClass('d-none');
                    hasError = true;
                } else {
                    $('#course_inline_error').addClass('d-none');
                }

                if (hasError) {
                    e.preventDefault();
                    return false;
                }
            });

            $('#intake_id').on('change', function() {
                var intakeId = $(this).val();
                var courseSelect = $('#course_id');

                if (intakeId) {
                    $('#intake_inline_error').addClass('d-none');
                }

                courseSelect.html('<option value="">Loading courses...</option>');

                if (!intakeId) {
                    courseSelect.html('<option value="">Select Course</option>');
                    if (courseSelect.hasClass('primary_select')) {
                        courseSelect.niceSelect('update');
                    }
                    return;
                }

                $.ajax({
                    url: '{{ route('student-attendance.get-courses', ':intakeId') }}'.replace(':intakeId', intakeId),
                    type: 'GET',
                    success: function(data) {
                        var options = '<option value="">Select Course</option>';

                        if (data.courses && data.courses.length > 0) {
                            data.courses.forEach(function(course) {
                                options += '<option value="' + course.id + '">' + course.name + '</option>';
                            });
                        } else {
                            options = '<option value="">No courses available</option>';
                        }

                        courseSelect.html(options);
                        $('#course_inline_error').addClass('d-none');
                        if (courseSelect.hasClass('primary_select')) {
                            courseSelect.niceSelect('update');
                        }
                    },
                    error: function() {
                        courseSelect.html('<option value="">Error loading courses</option>');
                        if (courseSelect.hasClass('primary_select')) {
                            courseSelect.niceSelect('update');
                        }
                    }
                });
            });

            $('#course_id').on('change', function() {
                if ($(this).val()) {
                    $('#course_inline_error').addClass('d-none');
                }
            });
        });
    </script>
@endpush
