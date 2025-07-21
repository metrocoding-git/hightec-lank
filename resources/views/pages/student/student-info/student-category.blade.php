@extends('layouts.app')
@section('title')
    @lang('student.student_category')
@endsection
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <div class="row">

                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">


                            <form action="" method="POST" class="form-horizontal">
                                <div class="white-box">
                                    <div class="main-title">
                                        <h3 class="mb-15">
                                            @lang('student.add_student_category')
                                        </h3>
                                    </div>

                                    <div class="add-visitor">
                                        <div class="row">
                                            <div class="col-lg-12">

                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="">@lang('common.type')
                                                        <span class="text-danger"> *</span></label>
                                                    <input
                                                        class="primary_input_field form-control{{ $errors->has('category') ? ' is-invalid' : '' }}"
                                                        type="text" name="category" autocomplete="off"
                                                        value="{{ isset($student_type) ? $student_type->category_name : '' }}">
                                                    <input type="hidden" name="id"
                                                        value="{{ isset($student_type) ? $student_type->id : '' }}">

                                                    @if ($errors->has('category'))
                                                        <span class="text-danger">
                                                            {{ $errors->first('category') }}
                                                        </span>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row mt-40">
                                            <div class="col-lg-12 text-center">
                                                <button class="primary-btn fix-gr-bg submit" data-toggle="tooltip">
                                                    <span class="ti-check"></span>

                                                    @lang('student.save_category')


                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('student.student_category_list')</h3>
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
                                                <th>@lang('student.category')</th>
                                                <th>@lang('common.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr>
                                                <td>1</td>
                                                <td>Normal</td>
                                                <td>
                                                    @php
                                                        $routeList = [
                                                            '<a class="dropdown-item" href="/">' .
                                                            __('common.edit') .
                                                            '</a>',
                                                            '<a class="dropdown-item" data-toggle="modal" data-target="" 
                                                                href="#">' .
                                                            __('common.delete') .
                                                            '</a>',
                                                        ];
                                                    @endphp
                                                    <x-drop-down-action-component :routeList="$routeList" />
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>2</td>
                                                <td>High Level</td>
                                                <td>
                                                    @php
                                                        $routeList = [
                                                            '<a class="dropdown-item" href="/">' .
                                                            __('common.edit') .
                                                            '</a>',
                                                            '<a class="dropdown-item" data-toggle="modal" data-target="" 
                                                                href="#">' .
                                                            __('common.delete') .
                                                            '</a>',
                                                        ];
                                                    @endphp
                                                    <x-drop-down-action-component :routeList="$routeList" />
                                                </td>
                                            </tr>

                                            <div class="modal fade admin-query" id="deleteStudentTypeModal1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">@lang('student.delete_category')</h4>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="text-center">
                                                                <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                            </div>
                                                            <div class="mt-40 d-flex justify-content-between">
                                                                <button type="button" class="primary-btn tr-bg"
                                                                    data-dismiss="modal">@lang('common.cancel')</button>
                                                                <a href=""><button class="primary-btn fix-gr-bg"
                                                                        type="submit">@lang('common.delete')</button></a>
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
