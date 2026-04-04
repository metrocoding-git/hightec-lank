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
                        <div class="main-title mb-20">
                            <h3>@lang('common.select_criteria')</h3>
                        </div>

                        <form action="{{ route('student-attendance-import.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 mb-20">
                                    <label class="primary_input_label">Intake <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" id="intake_id" name="intake_id" required>
                                        <option value="">Select Intake</option>
                                        @foreach ($intakes as $intake)
                                            <option value="{{ $intake->id }}">{{ $intake->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('intake_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-4 mb-20">
                                    <label class="primary_input_label">Course <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" id="course_id" name="course_id" required>
                                        <option value="">Select Course</option>
                                    </select>
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
                                <div class="col-lg-12 text-center">
                                    <button type="submit" class="primary-btn fix-gr-bg">
                                        <span class="ti-check"></span>
                                        Upload Attendance File
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#intake_id').on('change', function() {
                var intakeId = $(this).val();
                var courseSelect = $('#course_id');

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
        });
    </script>
@endpush
