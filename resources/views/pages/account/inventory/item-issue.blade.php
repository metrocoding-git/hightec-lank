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
        {{-- Form starts --}}
        <form class="form-horizontal" action="{{ isset($editData) ? route('item-issue-update', $editData->id) : route('save-item-issue-data') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($editData))
                @method('PUT')
            @endif

            <div class="row">
                {{-- Left Side - Vertical Dropdowns --}}
                <div class="col-lg-4">
                    <div class="white-box">
                        <div class="main-title">
                            <h3 class="mb-15">@lang('inventory.issue_a_item')</h3>
                        </div>
                        <div class="add-visitor">
                            <div class="row">
                                {{-- Branch Dropdown --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Branch <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" name="branch_id" id="branch_select">
                                        <option value="">Select Branch *</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Intake Dropdown --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Intake <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" name="intake_id" id="intake_select">
                                        <option value="">Select Branch First *</option>
                                    </select>
                                </div>

                                {{-- Course Dropdown --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Course <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" name="course_id" id="course_select">
                                        <option value="">Select Intake First *</option>
                                    </select>
                                </div>

                                {{-- Student Dropdown --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Student <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" name="student_id" id="student_select">
                                        <option value="">Select Course First *</option>
                                    </select>
                                </div>

                                {{-- Staff Dropdown --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Issue To</label>
                                    <select class="primary_select form-control" name="staff_id" id="staff_select">
                                        <option value="">Select Staff</option>
                                        @foreach ($staffs as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Issue Date --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Issue Date</label>
                                    <input class="primary_input_field form-control date" type="text" name="issue_date" value="{{ isset($editData) ? date('m/d/Y', strtotime($editData->issue_date)) : date('m/d/Y') }}">
                                </div>

                                {{-- Due Date --}}
                                <div class="col-lg-12 mb-15" hidden>
                                    <label class="primary_input_label">Due Date</label>
                                    <input class="primary_input_field form-control date" type="text" name="due_date" value="{{ isset($editData) ? date('m/d/Y', strtotime($editData->due_date)) : date('m/d/Y') }}">
                                </div>

                                {{-- Quantity --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Quantity <span class="text-danger">*</span></label>
                                    <input class="primary_input_field form-control" type="text" name="quantity" value="{{ isset($editData) ? $editData->quantity : old('quantity') }}">
                                </div>

                                {{-- Description --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Note</label>
                                    <textarea class="primary_input_field form-control" name="description" rows="4">{{ isset($editData) ? $editData->description : old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side - Category & Item horizontally + Save --}}
                <div class="col-lg-8">
                    <div class="white-box">
                        <div class="main-title">
                            <h3 class="mb-15">@lang('inventory.item_selection')</h3>
                        </div>
                        <div class="row">
                            {{-- Category --}}
                            <div class="col-lg-6 mb-15">
                                <label class="primary_input_label">Category *</label>
                                <select class="primary_select form-control" name="item_category_id" id="item_category_id_sidebar">
                                    <option value="">Select Category *</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Item --}}
                            <div class="col-lg-6 mb-15">
                                <label class="primary_input_label">Item *</label>
                                <select class="primary_select form-control" name="item_id" id="selectItems_sidebar">
                                    <option value="">Select Item *</option>
                                    @foreach ($inventoryItems as $item)
                                        <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Save Button --}}
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="primary-btn fix-gr-bg mt-2">
                                    <span class="ti-check"></span> @lang('common.save')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        {{-- Form ends --}}
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
                    $('#intake_select').html('<option value="">Loading intakes...</option>').prop(
                        'disabled', true);
                    refreshNiceSelect('#intake_select');

                    var url = '{{ route('item-issue.get-intakes-by-branch', ':branchId') }}'.replace(
                        ':branchId', branch_id);
                    console.log('🌐 AJAX URL:', url);

                    $.get(url, function(data) {
                        console.log('✅ AJAX Success! Data received:', data);

                        var optionsHtml = '<option value="">Select Intake *</option>';
                        if (data && Array.isArray(data) && data.length > 0) {
                            data.forEach(function(intake) {
                                optionsHtml += '<option value="' + intake.id + '">' + intake
                                    .name + '</option>';
                            });
                        } else {
                            optionsHtml += '<option value="">No intakes found</option>';
                        }

                        $('#intake_select').html(optionsHtml).prop('disabled', false);
                        refreshNiceSelect('#intake_select');

                        // Reset dependent dropdowns
                        $('#course_select').html('<option value="">Select Intake First *</option>')
                            .prop('disabled', true);
                        refreshNiceSelect('#course_select');

                        $('#student_select').html('<option value="">Select Course First *</option>')
                            .prop('disabled', true);
                        refreshNiceSelect('#student_select');

                    }).fail(function(xhr, status, error) {
                        console.error('❌ AJAX Error:', error);
                        $('#intake_select').html('<option value="">Error loading intakes</option>')
                            .prop('disabled', false);
                        refreshNiceSelect('#intake_select');
                    });
                } else {
                    $('#intake_select').html('<option value="">Select Branch First *</option>').prop(
                        'disabled', false);
                    refreshNiceSelect('#intake_select');

                    $('#course_select').html('<option value="">Select Intake First *</option>').prop(
                        'disabled', true);
                    refreshNiceSelect('#course_select');

                    $('#student_select').html('<option value="">Select Course First *</option>').prop(
                        'disabled', true);
                    refreshNiceSelect('#student_select');
                }
            });

            // Handle intake change
            $('#intake_select').on('change', function() {
                var intake_id = $(this).val();
                console.log('📚 Intake selected:', intake_id);

                if (intake_id && intake_id !== '') {
                    $('#course_select').html('<option value="">Loading courses...</option>').prop(
                        'disabled', true);
                    refreshNiceSelect('#course_select');

                    var url = '{{ route('item-issue.get-courses-by-intake', ':intakeId') }}'.replace(
                        ':intakeId', intake_id);

                    $.get(url, function(data) {
                        console.log('✅ Courses received:', data);

                        var optionsHtml = '<option value="">Select Course *</option>';
                        if (data && Array.isArray(data) && data.length > 0) {
                            data.forEach(function(course) {
                                optionsHtml += '<option value="' + course.id + '">' + course
                                    .name + '</option>';
                            });
                        } else {
                            optionsHtml += '<option value="">No courses found</option>';
                        }

                        $('#course_select').html(optionsHtml).prop('disabled', false);
                        refreshNiceSelect('#course_select');

                        $('#student_select').html('<option value="">Select Course First *</option>')
                            .prop('disabled', true);
                        refreshNiceSelect('#student_select');

                    }).fail(function(xhr, status, error) {
                        console.error('❌ Error loading courses:', error);
                        $('#course_select').html('<option value="">Error loading courses</option>')
                            .prop('disabled', false);
                        refreshNiceSelect('#course_select');
                    });
                } else {
                    $('#course_select').html('<option value="">Select Intake First *</option>').prop(
                        'disabled', true);
                    refreshNiceSelect('#course_select');

                    $('#student_select').html('<option value="">Select Course First *</option>').prop(
                        'disabled', true);
                    refreshNiceSelect('#student_select');
                }
            });

            // Handle course change
            $('#course_select').on('change', function() {
                var course_id = $(this).val();
                console.log('🎓 Course selected:', course_id);

                if (course_id && course_id !== '') {
                    $('#student_select').html('<option value="">Loading students...</option>').prop(
                        'disabled', true);
                    refreshNiceSelect('#student_select');

                    var url = '{{ route('item-issue.get-students-by-course', ':courseId') }}'.replace(
                        ':courseId', course_id);

                    $.get(url, function(data) {
                        console.log('✅ Students received:', data);

                        var optionsHtml = '<option value="">Select Student *</option>';
                        if (data && Array.isArray(data) && data.length > 0) {
                            data.forEach(function(student) {
                                optionsHtml += '<option value="' + student.id + '">' +
                                    student.student_name + '</option>';
                            });
                        } else {
                            optionsHtml += '<option value="">No students found</option>';
                        }

                        $('#student_select').html(optionsHtml).prop('disabled', false);
                        refreshNiceSelect('#student_select');

                    }).fail(function(xhr, status, error) {
                        console.error('❌ Error loading students:', error);
                        $('#student_select').html(
                            '<option value="">Error loading students</option>').prop('disabled',
                            false);
                        refreshNiceSelect('#student_select');
                    });
                } else {
                    $('#student_select').html('<option value="">Select Course First *</option>').prop(
                        'disabled', true);
                    refreshNiceSelect('#student_select');
                }
            });

            // Handle category change to filter items (main form)
            $('#item_category_id').on('change', function() {
                var category_id = $(this).val();
                console.log('📦 Main Category selected:', category_id);

                // Sync with sidebar dropdown
                $('#item_category_id_sidebar').val(category_id);
                refreshNiceSelect('#item_category_id_sidebar');

                if (category_id && category_id !== '') {
                    $('#selectItems, #selectItems_sidebar').html('<option value="">Loading items...</option>').prop('disabled', true);
                    refreshNiceSelect('#selectItems');
                    refreshNiceSelect('#selectItems_sidebar');

                    // AJAX call to get items by category
                    var url = '{{ route("item-issue.get-items-by-category", ":categoryId") }}'.replace(':categoryId', category_id);
                    console.log('🌐 Items AJAX URL:', url);

                    $.get(url, function(data) {
                        console.log('✅ Items received:', data);

                        var optionsHtml = '<option value="">Select Item *</option>';
                        if (data && Array.isArray(data) && data.length > 0) {
                            data.forEach(function(item) {
                                optionsHtml += '<option value="' + item.id + '">' + item.item_name + '</option>';
                            });
                        } else {
                            optionsHtml += '<option value="">No items found in this category</option>';
                        }

                        $('#selectItems, #selectItems_sidebar').html(optionsHtml).prop('disabled', false);
                        refreshNiceSelect('#selectItems');
                        refreshNiceSelect('#selectItems_sidebar');

                    }).fail(function(xhr, status, error) {
                        console.error('❌ Error loading items:', error);
                        $('#selectItems, #selectItems_sidebar').html('<option value="">Error loading items</option>').prop('disabled', false);
                        refreshNiceSelect('#selectItems');
                        refreshNiceSelect('#selectItems_sidebar');
                    });
                } else {
                    // Show all items when no category is selected
                    var optionsHtml = '<option value="">Select Item *</option>';
                    @foreach ($inventoryItems as $item)
                        var selected = '{{ old("item_id") }}' == '{{ $item->id }}' ? 'selected' : '';
                        optionsHtml += '<option value="{{ $item->id }}" ' + selected + '>{{ $item->item_name }}</option>';
                    @endforeach

                    $('#selectItems, #selectItems_sidebar').html(optionsHtml).prop('disabled', false);
                    refreshNiceSelect('#selectItems');
                    refreshNiceSelect('#selectItems_sidebar');
                }
            });

            // Handle sidebar category change
            $('#item_category_id_sidebar').on('change', function() {
                var category_id = $(this).val();
                console.log('📦 Sidebar Category selected:', category_id);

                // Sync with main form dropdown
                $('#item_category_id').val(category_id);
                refreshNiceSelect('#item_category_id');

                if (category_id && category_id !== '') {
                    $('#selectItems, #selectItems_sidebar').html('<option value="">Loading items...</option>').prop('disabled', true);
                    refreshNiceSelect('#selectItems');
                    refreshNiceSelect('#selectItems_sidebar');

                    // AJAX call to get items by category
                    var url = '{{ route("item-issue.get-items-by-category", ":categoryId") }}'.replace(':categoryId', category_id);

                    $.get(url, function(data) {
                        console.log('✅ Items received:', data);

                        var optionsHtml = '<option value="">Select Item *</option>';
                        if (data && Array.isArray(data) && data.length > 0) {
                            data.forEach(function(item) {
                                optionsHtml += '<option value="' + item.id + '">' + item.item_name + '</option>';
                            });
                        } else {
                            optionsHtml += '<option value="">No items found in this category</option>';
                        }

                        $('#selectItems, #selectItems_sidebar').html(optionsHtml).prop('disabled', false);
                        refreshNiceSelect('#selectItems');
                        refreshNiceSelect('#selectItems_sidebar');

                    }).fail(function(xhr, status, error) {
                        console.error('❌ Error loading items:', error);
                        $('#selectItems, #selectItems_sidebar').html('<option value="">Error loading items</option>').prop('disabled', false);
                        refreshNiceSelect('#selectItems');
                        refreshNiceSelect('#selectItems_sidebar');
                    });
                } else {
                    // Show all items when no category is selected
                    var optionsHtml = '<option value="">Select Item *</option>';
                    @foreach ($inventoryItems as $item)
                        var selected = '{{ old("item_id") }}' == '{{ $item->id }}' ? 'selected' : '';
                        optionsHtml += '<option value="{{ $item->id }}" ' + selected + '>{{ $item->item_name }}</option>';
                    @endforeach

                    $('#selectItems, #selectItems_sidebar').html(optionsHtml).prop('disabled', false);
                    refreshNiceSelect('#selectItems');
                    refreshNiceSelect('#selectItems_sidebar');
                }
            });
        });
    </script>
@endpush
