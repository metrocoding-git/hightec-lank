@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('inventory.issue_item_list')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('inventory.inventory')</a>
                    <a href="#">@lang('inventory.issue_item_list')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (isset($editData))
                                <form class="form-horizontal" action="{{ route('item-issue-update', $editData->id) }}" method="POST" enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf
                                @else
                                    <form class="form-horizontal" action="{{ route('save-item-issue-data') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                            @endif

                            <div class="white-box">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('inventory.issue_a_item')</h3>
                                </div>
                                <div class="add-visitor">
                                    <div class="row">
                                        {{-- Branch Dropdown --}}
                                        <div class="col-lg-12 mb-15">
                                            <label class="primary_input_label" for="branch_select">Branch <span class="text-danger"> *</span></label>
                                            <select class="primary_select form-control{{ $errors->has('branch_id') ? ' is-invalid' : '' }}" name="branch_id" id="branch_select">
                                                <option value="">Select Branch *</option>
                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}" {{ isset($editData) ? ($editData->branch_id == $branch->id ? 'selected' : '') : (old('branch_id') == $branch->id ? 'selected' : '') }}>
                                                        {{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('branch_id'))
                                                <span class="text-danger invalid-select" role="alert">{{ $errors->first('branch_id') }}</span>
                                            @endif
                                        </div>

                                        {{-- Intake Dropdown --}}
                                        <div class="col-lg-12 mb-15">
                                            <label class="primary_input_label" for="intake_select">Intake <span class="text-danger"> *</span></label>
                                            <select class="primary_select form-control{{ $errors->has('intake_id') ? ' is-invalid' : '' }}" name="intake_id" id="intake_select">
                                                <option value="">Select Branch First *</option>
                                            </select>
                                            @if ($errors->has('intake_id'))
                                                <span class="text-danger invalid-select" role="alert">{{ $errors->first('intake_id') }}</span>
                                            @endif
                                        </div>

                                        {{-- Course Dropdown --}}
                                        <div class="col-lg-12 mb-15">
                                            <label class="primary_input_label" for="course_select">Course <span class="text-danger"> *</span></label>
                                            <select class="primary_select form-control{{ $errors->has('course_id') ? ' is-invalid' : '' }}" name="course_id" id="course_select">
                                                <option value="">Select Intake First *</option>
                                            </select>
                                            @if ($errors->has('course_id'))
                                                <span class="text-danger invalid-select" role="alert">{{ $errors->first('course_id') }}</span>
                                            @endif
                                        </div>

                                        {{-- Student Dropdown --}}
                                        <div class="col-lg-12 mb-15">
                                            <label class="primary_input_label" for="student_select">@lang('common.student') <span class="text-danger"> *</span></label>
                                            <select class="primary_select form-control{{ $errors->has('student_id') ? ' is-invalid' : '' }}" name="student_id" id="student_select">
                                                <option value="">Select Course First *</option>
                                            </select>
                                            @if ($errors->has('student_id'))
                                                <span class="text-danger invalid-select" role="alert">{{ $errors->first('student_id') }}</span>
                                            @endif
                                        </div>

                                        {{-- Staff Dropdown --}}
                                        <div class="col-lg-12 mb-15">
                                            <label class="primary_input_label" for="staff_select">@lang('inventory.issue_to')</label>
                                            <select class="primary_select form-control{{ $errors->has('staff_id') ? ' is-invalid' : '' }}" name="staff_id" id="staff_select">
                                                <option value="">@lang('inventory.issue_to')</option>
                                                @foreach ($staffs as $staff)
                                                    <option value="{{ $staff->id }}" {{ isset($editData) ? ($editData->issue_to == $staff->id ? 'selected' : '') : (old('staff_id') == $staff->id ? 'selected' : '') }}>
                                                        {{ $staff->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('staff_id'))
                                                <span class="text-danger invalid-select" role="alert">{{ $errors->first('staff_id') }}</span>
                                            @endif
                                        </div>

                                        {{-- Issue Date --}}
                                        <div class="col-lg-12 mb-15">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="startDate">@lang('inventory.issue_date')</label>
                                                <div class="primary_datepicker_input">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <input class="primary_input_field primary_input_field date form-control form-control{{ $errors->has('issue_date') ? ' is-invalid' : '' }}" id="startDate" type="text" name="issue_date" value="{{ isset($editData) ? date('m/d/Y', strtotime($editData->issue_date)) : date('m/d/Y') }}">
                                                        </div>
                                                        <button class="btn-date" data-id="#issue_date" type="button">
                                                            <i class="ti-calendar" id="start-date-icon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <span class="text-danger">{{ $errors->first('issue_date') }}</span>
                                            </div>
                                        </div>

                                        {{-- Due Date --}}
                                        <div class="col-lg-12 mb-15">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="endDate">@lang('inventory.due_date')</label>
                                                <div class="primary_datepicker_input">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <input class="primary_input_field primary_input_field date form-control form-control{{ $errors->has('due_date') ? ' is-invalid' : '' }}" id="endDate" type="text" name="due_date" value="{{ isset($editData) ? date('m/d/Y', strtotime($editData->due_date)) : date('m/d/Y') }}">
                                                        </div>
                                                        <button class="btn-date" data-id="#due_date" type="button">
                                                            <i class="ti-calendar" id="start-date-icon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <span class="text-danger">{{ $errors->first('due_date') }}</span>
                                            </div>
                                        </div>

                                        {{-- Category --}}
                                        <div class="col-lg-12 mb-15">
                                            <label class="primary_input_label" for="item_category_id">@lang('inventory.category') <span class="text-danger"> *</span></label>
                                            <select class="primary_select form-control{{ $errors->has('item_category_id') ? ' is-invalid' : '' }}" name="item_category_id" id="item_category_id">
                                                <option value="">@lang('inventory.item_category') *</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" {{ isset($editData) ? ($editData->item_category_id == $category->id ? 'selected' : '') : (old('item_category_id') == $category->id ? 'selected' : '') }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('item_category_id'))
                                                <span class="text-danger invalid-select" role="alert">{{ $errors->first('item_category_id') }}</span>
                                            @endif
                                        </div>

                                        {{-- Item --}}
                                        <div class="col-lg-12 mb-15">
                                            <label class="primary_input_label" for="selectItems">@lang('common.name') <span class="text-danger"> *</span></label>
                                            <select class="primary_select form-control{{ $errors->has('item_id') ? ' is-invalid' : '' }}" name="item_id" id="selectItems">
                                                <option value="">@lang('inventory.item_name') *</option>
                                                @foreach ($inventoryItems as $item)
                                                    <option value="{{ $item->id }}" {{ isset($editData) ? ($editData->item_id == $item->id ? 'selected' : '') : (old('item_id') == $item->id ? 'selected' : '') }}>
                                                        {{ $item->item_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('item_id'))
                                                <span class="text-danger invalid-select" role="alert">{{ $errors->first('item_id') }}</span>
                                            @endif
                                        </div>

                                        {{-- Quantity --}}
                                        <div class="col-lg-12 mb-15">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="quantity">@lang('inventory.quantity') <span class="text-danger"> *</span></label>
                                                <input class="primary_input_field form-control{{ $errors->has('quantity') ? ' is-invalid' : '' }}" type="text" onkeypress="return isNumberKey(event)" name="quantity" autocomplete="off" value="{{ isset($editData) ? $editData->quantity : old('quantity') }}">
                                                @if ($errors->has('quantity'))
                                                    <span class="text-danger">{{ $errors->first('quantity') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Description --}}
                                        <div class="col-lg-12 mb-15">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="description">@lang('inventory.note')</label>
                                                <textarea class="primary_input_field form-control" cols="0" rows="4" name="description" id="description">{{ isset($editData) ? $editData->description : old('description') }}</textarea>
                                            </div>
                                        </div>

                                        <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

                                        {{-- Submit Button --}}
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg submit issuedSubmit" type="submit">
                                                <span class="ti-check"></span>
                                                @if (isset($editData))
                                                    @lang('common.update')
                                                @else
                                                    @lang('common.save')
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Issued Items List --}}
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-15">@lang('inventory.issued_item_list')</h3>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <x-table>
                                <table id="table_id" class="table" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>@lang('common.sl')</th>
                                            <th>@lang('inventory.item_name')</th>
                                            <th>@lang('inventory.item_category')</th>
                                            <th>@lang('inventory.issue_to')</th>
                                            <th>@lang('inventory.issue_date')</th>
                                            <th>@lang('inventory.return_date')</th>
                                            <th>@lang('inventory.quantity')</th>
                                            <th>@lang('common.status')</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($issuedItems))
                                            @foreach ($issuedItems as $key => $value)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $value->item ? $value->item->item_name : '' }}</td>
                                                    <td>{{ $value->category ? $value->category->name : '' }}</td>
                                                    <td>
                                                        @if ($value->student_id && $value->student)
                                                            {{ $value->student->student_name }}
                                                        @elseif ($value->issue_to && $value->staff)
                                                            {{ $value->staff->name }}
                                                        @else
                                                            {{ $value->branch ? $value->branch->name : '' }}
                                                        @endif
                                                    </td>
                                                    <td data-sort="{{ strtotime($value->issue_date) }}">
                                                        {{ $value->issue_date != '' ? date('m/d/Y', strtotime($value->issue_date)) : '' }}
                                                    </td>
                                                    <td data-sort="{{ strtotime($value->due_date) }}">
                                                        {{ $value->due_date != '' ? date('m/d/Y', strtotime($value->due_date)) : '' }}
                                                    </td>
                                                    <td>{{ $value->quantity }}</td>
                                                    <td>
                                                        @if ($value->issue_status == 'I')
                                                            <button class="primary-btn small bg-success text-white border-0">@lang('inventory.issued')</button>
                                                        @else
                                                            <button class="primary-btn small bg-primary text-white border-0">@lang('inventory.returned')</button>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <x-drop-down>
                                                            @if ($value->issue_status == 'I')
                                                                <form action="{{ route('return-item') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="id" value="{{ @$value->id }}">
                                                                    <button type="submit" class="dropdown-item">@lang('inventory.return')</button>
                                                                </form>
                                                            @else
                                                                <form action="{{ route('issue-item') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="id" value="{{ @$value->id }}">
                                                                    <button type="submit" class="dropdown-item">Issue</button>
                                                                </form>
                                                            @endif
                                                            <a class="dropdown-item" href="{{ route('item-issue-edit', @$value->id) }}">@lang('common.edit')</a>
                                                        </x-drop-down>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
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

@include('components.partials.data_table_js')
@include('components.partials.date_picker_css_js')

@push('scripts')
<script>
$(document).ready(function() {
    console.log('🚀 Item Issue Page Loaded - Starting Cascading Dropdown System');

    // Helper function to refresh niceSelect safely
    function refreshNiceSelect(selector) {
        if ($.fn.niceSelect) {
            $(selector).niceSelect('destroy');
            $(selector).niceSelect();
        }
    }

    // Handle branch change
    $('#branch_select').on('change', function() {
        var branch_id = $(this).val();
        console.log('📍 Branch selected:', branch_id);

        if (branch_id && branch_id !== '') {
            $('#intake_select').html('<option value="">Loading intakes...</option>').prop('disabled', true);
            refreshNiceSelect('#intake_select');

            var url = '{{ route("item-issue.get-intakes-by-branch", ":branchId") }}'.replace(':branchId', branch_id);
            console.log('🌐 AJAX URL:', url);

            $.get(url, function(data) {
                console.log('✅ AJAX Success! Data received:', data);

                var optionsHtml = '<option value="">Select Intake *</option>';
                if (data && Array.isArray(data) && data.length > 0) {
                    data.forEach(function(intake) {
                        optionsHtml += '<option value="' + intake.id + '">' + intake.name + '</option>';
                    });
                } else {
                    optionsHtml += '<option value="">No intakes found</option>';
                }

                $('#intake_select').html(optionsHtml).prop('disabled', false);
                refreshNiceSelect('#intake_select');

                // Reset dependent dropdowns
                $('#course_select').html('<option value="">Select Intake First *</option>').prop('disabled', true);
                refreshNiceSelect('#course_select');

                $('#student_select').html('<option value="">Select Course First *</option>').prop('disabled', true);
                refreshNiceSelect('#student_select');

            }).fail(function(xhr, status, error) {
                console.error('❌ AJAX Error:', error);
                $('#intake_select').html('<option value="">Error loading intakes</option>').prop('disabled', false);
                refreshNiceSelect('#intake_select');
            });
        } else {
            $('#intake_select').html('<option value="">Select Branch First *</option>').prop('disabled', false);
            refreshNiceSelect('#intake_select');

            $('#course_select').html('<option value="">Select Intake First *</option>').prop('disabled', true);
            refreshNiceSelect('#course_select');

            $('#student_select').html('<option value="">Select Course First *</option>').prop('disabled', true);
            refreshNiceSelect('#student_select');
        }
    });

    // Handle intake change
    $('#intake_select').on('change', function() {
        var intake_id = $(this).val();
        console.log('📚 Intake selected:', intake_id);

        if (intake_id && intake_id !== '') {
            $('#course_select').html('<option value="">Loading courses...</option>').prop('disabled', true);
            refreshNiceSelect('#course_select');

            var url = '{{ route("item-issue.get-courses-by-intake", ":intakeId") }}'.replace(':intakeId', intake_id);

            $.get(url, function(data) {
                console.log('✅ Courses received:', data);

                var optionsHtml = '<option value="">Select Course *</option>';
                if (data && Array.isArray(data) && data.length > 0) {
                    data.forEach(function(course) {
                        optionsHtml += '<option value="' + course.id + '">' + course.name + '</option>';
                    });
                } else {
                    optionsHtml += '<option value="">No courses found</option>';
                }

                $('#course_select').html(optionsHtml).prop('disabled', false);
                refreshNiceSelect('#course_select');

                $('#student_select').html('<option value="">Select Course First *</option>').prop('disabled', true);
                refreshNiceSelect('#student_select');

            }).fail(function(xhr, status, error) {
                console.error('❌ Error loading courses:', error);
                $('#course_select').html('<option value="">Error loading courses</option>').prop('disabled', false);
                refreshNiceSelect('#course_select');
            });
        } else {
            $('#course_select').html('<option value="">Select Intake First *</option>').prop('disabled', true);
            refreshNiceSelect('#course_select');

            $('#student_select').html('<option value="">Select Course First *</option>').prop('disabled', true);
            refreshNiceSelect('#student_select');
        }
    });

    // Handle course change
    $('#course_select').on('change', function() {
        var course_id = $(this).val();
        console.log('🎓 Course selected:', course_id);

        if (course_id && course_id !== '') {
            $('#student_select').html('<option value="">Loading students...</option>').prop('disabled', true);
            refreshNiceSelect('#student_select');

            var url = '{{ route("item-issue.get-students-by-course", ":courseId") }}'.replace(':courseId', course_id);

            $.get(url, function(data) {
                console.log('✅ Students received:', data);

                var optionsHtml = '<option value="">Select Student *</option>';
                if (data && Array.isArray(data) && data.length > 0) {
                    data.forEach(function(student) {
                        optionsHtml += '<option value="' + student.id + '">' + student.student_name + '</option>';
                    });
                } else {
                    optionsHtml += '<option value="">No students found</option>';
                }

                $('#student_select').html(optionsHtml).prop('disabled', false);
                refreshNiceSelect('#student_select');

            }).fail(function(xhr, status, error) {
                console.error('❌ Error loading students:', error);
                $('#student_select').html('<option value="">Error loading students</option>').prop('disabled', false);
                refreshNiceSelect('#student_select');
            });
        } else {
            $('#student_select').html('<option value="">Select Course First *</option>').prop('disabled', true);
            refreshNiceSelect('#student_select');
        }
    });
});
</script>

@endpush
