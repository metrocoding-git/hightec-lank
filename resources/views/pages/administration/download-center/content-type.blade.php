@extends('layouts.app')
@push('css')
    <style>
        /* ---- Modern refresh: cards, table styling (Section 24) ---- */
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

        .sms-breadcrumb {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
            padding: 18px 24px;
            position: relative;
            overflow: hidden;
        }

        .sms-breadcrumb::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--gradient_1), var(--gradient_2));
        }

        .sms-breadcrumb .row {
            align-items: center;
        }

        .sms-breadcrumb h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .sms-breadcrumb .bc-pages {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .sms-breadcrumb .bc-pages a {
            font-size: 13px;
            font-weight: 500;
            color: #8a90a2;
            text-decoration: none;
            transition: color .15s ease;
        }

        .sms-breadcrumb .bc-pages a:hover {
            color: var(--gradient_1);
        }

        .sms-breadcrumb .bc-pages a:not(:last-child)::after {
            content: '\203A';
            margin: 0 8px;
            color: #c7cbd6;
            font-weight: 400;
        }

        .sms-breadcrumb .bc-pages a:last-child {
            color: var(--gradient_1);
            font-weight: 700;
            pointer-events: none;
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
                <h1>Content Type</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Content Type</a>
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid p-0">
        @include('components.partials.permanent-delete-note', ['rule' => 'content_type'])
    </div>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @php
                // Button-level permissions (Section 24).
                $canAddContentType = Auth::user()->hasPermission('content-type-store');
                $canEditContentType = Auth::user()->hasPermission('content-type-update');
                $canDeleteContentType = Auth::user()->hasPermission('content-type-delete');
                $showContentTypeForm = isset($type) ? $canEditContentType : $canAddContentType;
            @endphp

            <div class="row">
                @if ($showContentTypeForm)
                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">
                            {{-- @if (isset($type))
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'download-center.content-type-update', 'method' => 'POST']) }}
                            @else
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'download-center.content-type-save', 'method' => 'POST']) }}
                            @endif --}}
                           <form class="form-horizontal"
                                action="{{ isset($type) ? route('content-type-update', $type->id) : route('content-type-store') }}"
                                method="POST">
                                @csrf
                                @if (isset($type))
                                    @method('PUT')
                                @endif
                                <div class="white-box">
                                    <div class="main-title">
                                        <h3 class="mb-15">
                                            @if (isset($type))
                                                @lang('downloadCenter.edit_type')
                                            @else
                                                @lang('downloadCenter.add_type')
                                            @endif
                                        </h3>
                                    </div>
                                    <div class="add-visitor">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="primary_input">
                                                    <label for="name">@lang('downloadCenter.name') <span
                                                            class="text-danger">*</span></label>
                                                    <input
                                                        class="primary_input_field form-control{{ @$errors->has('name') ? ' is-invalid' : '' }}"
                                                        type="text" name="name" autocomplete="off" id="name"
                                                        value="{{ isset($type) ? $type->name : old('type') }}">
                                                    <input type="hidden" name="type_id"
                                                        value="{{ isset($type) ? $type->id : '' }}">
                                                    @if ($errors->has('name'))
                                                        <span class="text-danger">{{ @$errors->first('name') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mt-10">
                                                <div class="primary_input">
                                                    <label for="description">@lang('downloadCenter.description')</label>
                                                    <textarea class="primary_input_field form-control" cols="0" rows="3" name="description" id="description">{{ isset($type) ? $type->description : old('description') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-20">
                                            <div class="col-lg-12 text-center">
                                                <button class="primary-btn fix-gr-bg col-12 submit">
                                                    <span class="ti-check"></span>
                                                    @if (isset($type))
                                                        @lang('downloadCenter.update')
                                                    @else
                                                        @lang('downloadCenter.save')
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
                @endif
                <div class="col-lg-{{ $showContentTypeForm ? '9' : '12' }}">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('downloadCenter.content_type_list')</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id" class="table Crm_table_active3" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>@lang('downloadCenter.name')</th>
                                                <th>@lang('downloadCenter.description')</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            {{-- @foreach ($types as $type)
                                                <tr>
                                                    <td>{{ $type->name }}</td>
                                                    <td>{{ $type->description }}</td>
                                                    <td>
                                                        <x-drop-down>
                                                            <a class="dropdown-item"
                                                                href="{{ route('download-center.content-type-edit', $type->id) }}">@lang('common.edit')</a>
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#deleteTypeModal{{ @$type->id }}"
                                                                href="#">@lang('common.delete')</a>
                                                        </x-drop-down>
                                                    </td>
                                                </tr>
                                                <div class="modal fade admin-query"
                                                    id="deleteTypeModal{{ @$type->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">@lang('downloadCenter.delete_type')</h4>
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
                                                                    <a href="{{ route('download-center.content-type-delete', [@$type->id]) }}"
                                                                        class="text-light">
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">@lang('common.delete')</button>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach --}}

                                           @foreach ($content_Types as $key => $content_Type)
                                                <tr>
                                                    <td>{{ $content_Type->name }}</td>
                                                    <td>{{ $content_Type->description }}</td>
                                                <td>
                                                    @if ($canEditContentType || $canDeleteContentType)
                                                        <x-drop-down>
                                                            @if ($canEditContentType)
                                                                <a class="dropdown-item"
                                                                    href="{{ route('content-type-edit', $content_Type->id) }}">@lang('common.edit')</a>
                                                            @endif
                                                            @if ($canDeleteContentType)
                                                                <a class="dropdown-item" data-toggle="modal"
                                                                        data-target="#deletecategoryModal{{ $content_Type->id }}"
                                                                        href="#">@lang('common.delete')</a>
                                                            @endif
                                                        </x-drop-down>
                                                    @else
                                                        <span class="text-muted">&mdash;</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            @if ($canDeleteContentType)
                                            <div class="modal fade admin-query"
                                                    id="deletecategoryModal{{ $content_Type->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                {{-- BUG FIX (Section 24): this used the "fees.delete_item"
                                                                     translation key, copy-pasted from an unrelated Fees
                                                                     page — the wrong label was shown here. --}}
                                                                <h4 class="modal-title">@lang('downloadCenter.delete_type')</h4>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="text-center">
                                                                    <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                                </div>
                                                                <div class="mt-40 d-flex justify-content-between">
                                                                    <button type="button" class="primary-btn tr-bg"
                                                                        data-dismiss="modal">
                                                                        @lang('common.cancel')
                                                                    </button>
                                                                    <form
                                                                        action="{{ route('content-type-delete', $content_Type->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">
                                                                            @lang('common.delete')
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @endforeach

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
@include('components.partials.data_table_js')