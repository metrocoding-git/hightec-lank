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
    {{-- <style type="text/css">
        #selectStaffsDiv,
        .forStudentWrapper {
            display: none;
        }
    </style> --}}
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">

                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">
                            
                            @if (isset($editData))
                                <form class="form-horizontal" action="{{ route('item-issue-update', $editData->id) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf
                            @else
                                <form class="form-horizontal" action="{{ route('save-item-issue-data') }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                            @endif
                            <div class="white-box">
                                <div class="main-title">
                                    <h3 class="mb-15">
                                        @lang('inventory.issue_a_item')
                                    </h3>
                                </div>
                                <div class="add-visitor">
                                    <div class="row">

                                        <div class="col-lg-12 mb-15">
                                            <label class="primary_input_label" for="">@lang('common.role') <span
                                                    class="text-danger"> *</span> </label>
                                            <select
                                                class="primary_select  form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}"
                                                name="role_id" id="member_type">
                                                <option data-display=" @lang('inventory.user_type') *" value="">
                                                    @lang('inventory.user_type')
                                                    *</option>
                                                @foreach ($roles as $value)
                                                    <option value="{{ $value->id }}"
                                                        {{ isset($editData) ? ($editData->role_id == $value->id ? 'selected' : '') : (old('role_id') == $value->id ? 'selected' : '') }}>
                                                        {{ $value->role_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('role_id'))
                                                <span class="text-danger invalid-select" role="alert">
                                                    {{ $errors->first('role_id') }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="forStudentWrapper col-lg-12">
                                            <div class="row">
                                                <div class="col-lg-12 mb-15">
                                                    <label class="primary_input_label" for="">@lang('common.class')
                                                        <span class="text-danger"> *</span> </label>
                                                    <select
                                                        class="primary_select form-control{{ $errors->has('class_id') ? ' is-invalid' : '' }}"
                                                        id="select_class" name="class_id">
                                                        <option data-display="@lang('common.select_class') *" value="">
                                                            @lang('common.select_class') *</option>
                                                        @foreach ($classes as $class)
                                                            <option value="{{ $class->id }}"
                                                                {{ isset($editData) ? ($editData->class_id == $class->id ? 'selected' : '') : (old('class_id') == $class->id ? 'selected' : '') }}>
                                                                {{ $class->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('class_id'))
                                                        <span class="text-danger invalid-select" role="alert">
                                                            {{ $errors->first('class_id') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="col-lg-12 mb-15" id="select_section_div">
                                                    <label class="primary_input_label" for="">@lang('common.section')
                                                        <span class="text-danger"> *</span> </label>
                                                    <select
                                                        class="primary_select form-control{{ $errors->has('section_id') ? ' is-invalid' : '' }}"
                                                        id="select_section" name="section_id">
                                                        <option data-display="@lang('common.select_section') *" value="">
                                                            @lang('common.select_section') *</option>
                                                        @foreach ($intakes as $value)
                                                            <option value="{{ $value->id }}"
                                                                {{ isset($editData) ? ($editData->section_id == $value->id ? 'selected' : '') : (old('section_id') == $value->id ? 'selected' : '') }}>
                                                                {{ $value->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="pull-right loader loader_style"
                                                        id="select_section_loader">
                                                        <img class="loader_img_style"
                                                            src="{{ asset('public/backEnd/img/demo_wait.gif') }}"
                                                            alt="loader">
                                                    </div>
                                                    @if ($errors->has('section_id'))
                                                        <span class="text-danger invalid-select" role="alert">
                                                            {{ $errors->first('section_id') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-lg-12 mb-15" id="select_student_div">
                                                    <label class="primary_input_label" for="">@lang('common.student')
                                                        <span class="text-danger"> *</span> </label>
                                                    <select
                                                        class="primary_select form-control{{ $errors->has('student_id') ? ' is-invalid' : '' }}"
                                                        id="select_student" name="student_id">
                                                        <option data-display="@lang('common.select_student')*" value="">
                                                            @lang('inventory.select_student_for_issue') *</option>
                                                        @foreach ($students as $value)
                                                            <option value="{{ $value->id }}"
                                                                {{ isset($editData) ? ($editData->student_id == $value->id ? 'selected' : '') : (old('student_id') == $value->id ? 'selected' : '') }}>
                                                                {{ $value->student_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="pull-right loader loader_style"
                                                        id="select_student_loader">
                                                        <img class="loader_img_style"
                                                            src="{{ asset('public/backEnd/img/demo_wait.gif') }}"
                                                            alt="loader">
                                                    </div>
                                                    @if ($errors->has('student_id'))
                                                        <span class="text-danger invalid-select" role="alert">
                                                            {{ $errors->first('student_id') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mb-15" id="selectStaffsDiv">
                                            <label class="primary_input_label" for="">@lang('inventory.issue_to')
                                                <span></span> </label>
                                            <select
                                                class="primary_select  form-control{{ $errors->has('staff_id') ? ' is-invalid' : '' }}"
                                                name="staff_id" id="selectStaffs">
                                                <option data-display="@lang('inventory.issue_to')" value="">
                                                    @lang('inventory.issue_to')
                                                </option>


                                                @foreach ($staffs as $value)
                                                    <option value="{{ $value->id }}"
                                                        {{ isset($editData) ? ($editData->issue_to == $value->id ? 'selected' : '') : (old('staff_id') == $value->id ? 'selected' : '') }}>
                                                        {{ $value->name }}</option>
                                                @endforeach

                                            </select>
                                            @if ($errors->has('staff_id'))
                                                <span class="text-danger invalid-select" role="alert">
                                                    {{ $errors->first('staff_id') }}
                                                </span>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="row mb-15">
                                        <div class="col-lg-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="">@lang('inventory.issue_date')
                                                    <span></span> </label>
                                                <div class="primary_datepicker_input">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="">
                                                                <input
                                                                    class="primary_input_field  primary_input_field date form-control form-control{{ $errors->has('issue_date') ? ' is-invalid' : '' }}"
                                                                    id="startDate" type="text" name="issue_date"
                                                                    value="{{ isset($editData) ? date('m/d/Y', strtotime($editData->issue_date)) : date('m/d/Y') }}">
                                                            </div>
                                                        </div>
                                                        <button class="btn-date" data-id="#issue_date"
                                                            type="button">
                                                            <i class="ti-calendar" id="start-date-icon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <span class="text-danger">{{ $errors->first('issue_date') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-15">
                                        <div class="col-lg-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="">@lang('inventory.due_date')
                                                    <span></span> </label>
                                                <div class="primary_datepicker_input">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="">
                                                                <input
                                                                    class="primary_input_field  primary_input_field date form-control form-control{{ $errors->has('due_date') ? ' is-invalid' : '' }}"
                                                                    id="endDate" type="text" name="due_date"
                                                                    value="{{ isset($editData) ? date('m/d/Y', strtotime($editData->due_date)) : date('m/d/Y') }}">
                                                            </div>
                                                        </div>
                                                        <button class="btn-date" data-id="#due_date" type="button">
                                                            <i class="ti-calendar" id="start-date-icon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <span class="text-danger">{{ $errors->first('due_date') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row ">
                                        <div class="col-lg-12 mb-15">
                                            <label class="primary_input_label" for="">@lang('inventory.category') <span
                                                    class="text-danger"> *</span> </label>
                                            <select
                                                class="primary_select  form-control{{ $errors->has('item_category_id') ? ' is-invalid' : '' }}"
                                                name="item_category_id" id="item_category_id">
                                                <option data-display="@lang('inventory.item_category') *" value="">
                                                    @lang('inventory.item_category') *</option>
                                                @foreach ($categories as $value)
                                                    <option value="{{ $value->id }}"
                                                        {{ isset($editData) ? ($editData->item_category_id == $value->id ? 'selected' : '') : (old('item_category_id') == $value->id ? 'selected' : '') }}>
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                            @if ($errors->has('item_category_id'))
                                                <span class="text-danger invalid-select" role="alert">
                                                    {{ $errors->first('item_category_id') }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="col-lg-12 mb-15" id="selectItemsDiv">
                                            <label class="primary_input_label" for="">@lang('common.name') <span
                                                    class="text-danger"> *</span> </label>
                                            <select
                                                class="primary_select  form-control{{ $errors->has('item_id') ? ' is-invalid' : '' }}"
                                                name="item_id" id="selectItems">
                                                <option data-display="@lang('inventory.item_name') *" value="">
                                                    @lang('inventory.item_name') *</option>
                                                @foreach ($inventoryItems as $value)
                                                    <option value="{{ $value->id }}"
                                                        {{ isset($editData) ? ($editData->item_id == $value->id ? 'selected' : '') : (old('item_id') == $value->id ? 'selected' : '') }}>
                                                        {{ $value->item_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('item_id'))
                                                <span class="text-danger invalid-select" role="alert">
                                                    {{ $errors->first('item_id') }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="col-lg-12 mb-15">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="">@lang('inventory.quantity')
                                                    <span class="text-danger"> *</span> </label>
                                                <input
                                                    class="primary_input_field form-control{{ $errors->has('quantity') ? ' is-invalid' : '' }}"
                                                    type="text" onkeypress="return isNumberKey(event)"
                                                    name="quantity" autocomplete="off"
                                                    value="{{ isset($editData) ? $editData->quantity : old('quantity') }}">


                                                @if ($errors->has('quantity'))
                                                    <span class="text-danger">
                                                        {{ $errors->first('quantity') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-15">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="">@lang('inventory.note')
                                                    <span></span> </label>
                                                <textarea class="primary_input_field form-control" cols="0" rows="4" name="description"
                                                    id="description">{{ isset($editData) ? $editData->description : old('description') }}</textarea>



                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="url" id="url"
                                        value="{{ URL::to('/') }}">
                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg submit issuedSubmit"
                                                data-toggle="tooltip">
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
                            @if (isset($editData))
                                {{-- Form::close() is not needed here as it's a plain HTML form --}}
                            @else
                                @if (false) {{-- userPermission('save-item-issue-data') is removed --}}
                                    {{-- Form::close() is not needed here as it's a plain HTML form --}}
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">


                    <div class="">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15"> @lang('inventory.issued_item_list')</h3>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id" class="table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th> @lang('common.sl')</th>
                                                <th> @lang('inventory.item_name')</th>
                                                <th> @lang('inventory.item_category')</th>
                                                <th> @lang('inventory.issue_to')</th>
                                                <th> @lang('inventory.issue_date')</th>
                                                <th> @lang('inventory.return_date')</th>
                                                <th> @lang('inventory.quantity')</th>
                                                <th> @lang('common.status')</th>
                                                <th> @lang('common.action')</th>
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
                                                            @if ($value->role_id == 2 && $value->student)
                                                                {{ $value->student->student_name }}
                                                            @elseif ($value->issue_to && $value->staff)
                                                                {{ $value->staff->name }}
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
                                                                <button class="primary-btn small bg-success text-white border-0">
                                                                    @lang('inventory.issued')</button>
                                                            @else
                                                                <button
                                                                    class="primary-btn small bg-primary text-white border-0">@lang('inventory.returned')</button>
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
                                                                    @else($value->issue_status == 'R')
                                                                    <form action="{{ route('issue-item') }}" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="id" value="{{ @$value->id }}">
                                                                        <button type="submit" class="dropdown-item">Issue</button>
                                                                    </form>
                                                                @endif
                                                                <a class="dropdown-item"
                                                                    href="{{ route('item-issue-edit', @$value->id) }}">@lang('common.edit')</a>
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
        </div>
        </div>
    </section>
@endsection
@include('components.partials.data_table_js')
@include('components.partials.date_picker_css_js')
@push('scripts')
    <script>
        $(document).on('.issuedSubmit', 'click', function(e) {
            let issue_date = $('#startDate').val();
            let return_date = $('#endDate').val();
            alert(return_date);
            if (issue_date > return_date) {
                toastr.error("Return Date will be greater Than issue date");
                return;
                e.preventdefault();
            }
        })
    </script>
@endpush
