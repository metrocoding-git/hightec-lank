{{-- @extends('layouts.app')
@section('title')
    @lang('student.student_add_course')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('student.student_add_course')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('student.student_information')</a>
                    <a href="#">@lang('student.student_add_course')</a>
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
                            <div class="col-lg-8 col-md-6">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('student.assign_course_to_student')</h3>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('student.add-course.store') }}">
                            @csrf
                            <div class="row">
                                <!-- Student Selection -->
                                <div class="col-lg-6">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="student_id">
                                            @lang('student.select_student') <span class="text-danger">*</span>
                                        </label>
                                        <select class="primary_select form-control{{ $errors->has('student_id') ? ' is-invalid' : '' }}"
                                                name="student_id" id="student_id" required>
                                            <option value="">@lang('student.select_student')</option>
                                            @foreach($students as $student)
                                                <option value="{{ $student->id }}" {{ (old('student_id', $studentCourse->student_id ?? null) == $student->id) ? 'selected' : '' }}>
                                                    {{ $student->student_name }} ({{ $student->admission_number }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('student_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Course Selection -->
                                <div class="col-lg-6">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="course_id">
                                            @lang('student.select_course') <span class="text-danger">*</span>
                                        </label>
                                        <select class="primary_select form-control{{ $errors->has('course_id') ? ' is-invalid' : '' }}"
                                                name="course_id" id="course_id" required>
                                            <option value="">@lang('student.select_course')</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}" {{ (old('course_id', $studentCourse->course_id ?? null) == $course->id) ? 'selected' : '' }}>
                                                    {{ $course->name }} ({{ $course->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('course_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Enrollment Date -->
                                <div class="col-lg-6 mt-4">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="enrolled_at">
                                            @lang('student.enrollment_date') <span class="text-danger">*</span>
                                        </label>
                                        <input class="primary_input_field form-control{{ $errors->has('enrolled_at') ? ' is-invalid' : '' }}"
                                               type="date" name="enrolled_at" id="enrolled_at"
                                               value="{{ old('enrolled_at', date('Y-m-d')) }}" required>
                                        @error('enrolled_at')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-lg-6 mt-4">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="status">
                                            @lang('student.status') <span class="text-danger">*</span>
                                        </label>
                                        <select class="primary_select form-control{{ $errors->has('status') ? ' is-invalid' : '' }}"
                                                name="status" id="status" required>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                                @lang('student.active')
                                            </option>
                                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                                @lang('student.completed')
                                            </option>
                                            <option value="dropped" {{ old('status') == 'dropped' ? 'selected' : '' }}>
                                                @lang('student.dropped')
                                            </option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="col-lg-12 mt-4">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="notes">
                                            @lang('student.notes')
                                        </label>
                                        <textarea class="primary_input_field form-control{{ $errors->has('notes') ? ' is-invalid' : '' }}"
                                                  name="notes" id="notes" rows="4"
                                                  placeholder="@lang('student.add_notes_here')">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-lg-12 mt-4 text-center">
                                    <button type="submit" class="primary-btn fix-gr-bg">
                                        <span class="ti-check"></span>
                                        @lang('student.assign_course')
                                    </button>
                                    <a href="{{ route('student.add-course.index') }}" class="primary-btn tr-bg">
                                        @lang('common.cancel')
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Student Course List -->
            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('student.student_course_list')</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id" class="table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>@lang('student.student_name')</th>
                                                <th>@lang('student.admission_no')</th>
                                                <th>@lang('student.course_name')</th>
                                                <th>@lang('student.enrollment_date')</th>
                                                <th>@lang('student.status')</th>
                                                <th>@lang('common.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($studentCourses as $studentCourse)
                                                <tr>
                                                    <td>{{ $studentCourse->student_name }}</td>
                                                    <td>{{ $studentCourse->admission_number }}</td>
                                                    <td>{{ $studentCourse->course_name }}</td>
                                                    <td>{{ $studentCourse->enrolled_at ? date('Y-m-d', strtotime($studentCourse->enrolled_at)) : '-' }}</td>
                                                    <td>
                                                        <span class="badge {{ $studentCourse->status == 'active' ? 'badge-success' : ($studentCourse->status == 'completed' ? 'badge-primary' : 'badge-warning') }}">
                                                            {{ ucfirst($studentCourse->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <x-drop-down>
                                                            <a class="dropdown-item" href="{{ route('student.add-course.edit', $studentCourse->id) }}">
                                                                @lang('common.edit')
                                                            </a>
                                                            <a class="dropdown-item" data-toggle="modal"
                                                               data-target="#deleteStudentCourseModal{{ $studentCourse->id }}" href="#">
                                                                @lang('common.delete')
                                                            </a>
                                                        </x-drop-down>
                                                    </td>
                                                </tr>

                                                <!-- Delete Modal -->
                                                <div class="modal fade admin-query" id="deleteStudentCourseModal{{ $studentCourse->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">@lang('student.remove_course')</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="text-center">
                                                                    <h4>@lang('student.are_you_sure_to_remove_course')</h4>
                                                                </div>
                                                                <div class="mt-40 d-flex justify-content-between">
                                                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">
                                                                        @lang('common.cancel')
                                                                    </button>
                                                                    <form action="{{ route('student.add-course.destroy', $studentCourse->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="primary-btn fix-gr-bg" type="submit">
                                                                            @lang('common.remove')
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">@lang('student.no_course_assignments_found')</td>
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
        </div>
    </section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-populate form if editing
    @if(isset($studentCourse))
        $('#student_id').val('{{ $studentCourse->student_id }}');
        $('#course_id').val('{{ $studentCourse->course_id }}');
        $('#enrolled_at').val('{{ $studentCourse->enrolled_at ? date('Y-m-d', strtotime($studentCourse->enrolled_at)) : '' }}');
        $('#status').val('{{ $studentCourse->status }}');
        $('#notes').val('{{ $studentCourse->notes }}');
    @endif
});
</script>
@endpush --}}
