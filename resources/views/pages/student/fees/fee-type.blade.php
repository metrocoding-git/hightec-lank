@extends('layouts.app')
{{-- BUG FIX (Section 27): the browser-tab title called @lang('academics.About') —
     a leftover placeholder copied from an Academics-module page — instead of
     naming this page. Also adding the standard modern-refresh + breadcrumb CSS,
     which this page never defined despite using the sms-breadcrumb/white-box
     markup. --}}
@push('css')
    <style>
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

        .primary_input_field,
        select.primary_select {
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
    @lang('fees.fees_type')
@endsection
@section('mainContent')
    @php
        // Permission keys from config_permissions.php ("Fees" category, Invoice Type window).
        $canAddFeesType = Auth::user()->hasPermission('fees.fees-type-store');
        $canEditFeesType = Auth::user()->hasPermission('fees.fees-type-update');
        $canDeleteFeesType = Auth::user()->hasPermission('fees.fees-type-delete');
    @endphp
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('fees.fees_type')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees') Type</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @if (isset($feesType) && $canAddFeesType)
                <div class="row">
                    <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                        <a href="{{ route('fees.fees-type') }}" class="primary-btn small fix-gr-bg">
                            <span class="ti-plus pr-2"></span>
                            @lang('common.add')
                        </a>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">
                            <form class="form-horizontal"
                                action="{{ isset($feesType) ? route('fees.fees-type-update', $feesType->id) : route('fees.fees-type-store') }}"
                                method="POST">

                                @csrf

                                @if (isset($feesType))
                                    @method('PUT')
                                @endif

                                <div class="white-box">
                                    <div class="main-title">
                                        <h3 class="mb-15">
                                            {{ isset($feesType) ? __('fees.edit_fees_type') : __('fees.add_fees_type') }}
                                        </h3>
                                    </div>

                                    <div class="add-visitor">

                                        {{-- Name Field --}}
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="primary_input">
                                                    <label class="primary_input_label">@lang('common.name') <span
                                                            class="text-danger">*</span></label>
                                                    <input
                                                        class="primary_input_field form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                                        type="text" name="name" autocomplete="off"
                                                        value="{{ old('name', $feesType->name ?? '') }}" required>
                                                    @if ($errors->has('name'))
                                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Fees Group Field --}}
                                        <div class="row mt-15">
                                            <div class="col-lg-12">
                                                <label class="primary_input_label">
                                                    {{ __('fees.fees_group') }} <span class="text-danger">*</span>
                                                </label>
                                                <select name="fee_group_id" id="fee_group_id"
                                                    class="primary_select form-control{{ $errors->has('fees_group') || session()->has('message-exist') ? ' is-invalid' : '' }}"
                                                    {{ isset($fees_master) ? 'disabled' : '' }}>
                                                    <option value="">@lang('fees.fees_group')</option>
                                                    @foreach ($feesGroups as $group)
                                                        <option value="{{ $group->id }}"
                                                            {{ old('fees_group', $feesType->fee_group_id ?? '') == $group->id ? 'selected' : '' }}>
                                                            {{ $group->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @if (session()->has('message-exist'))
                                                    <span class="text-danger invalid-select" role="alert">
                                                        <strong>{{ session()->get('message-exist') }}</strong>
                                                    </span>
                                                @endif
                                                @if ($errors->has('fees_group'))
                                                    <span class="text-danger invalid-select" role="alert">
                                                        {{ $errors->first('fees_group') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Amount Field --}}
                                        <div class="row mt-15">
                                            <div class="col-lg-12">
                                                <div class="primary_input">
                                                    <label class="primary_input_label">@lang('price')
                                                        <input
                                                            class="primary_input_field form-control{{ $errors->has('price') ? ' is-invalid' : '' }}"
                                                            type="number" name="price" autocomplete="off"
                                                            value="{{ old('amount', $feesType->price ?? '') }}">
                                                        @if ($errors->has('price'))
                                                            <span class="text-danger">{{ $errors->first('price') }}</span>
                                                        @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Description Field --}}
                                        <div class="row mt-15">
                                            <div class="col-lg-12">
                                                <div class="primary_input">
                                                    <label class="primary_input_label">@lang('common.description')</label>
                                                    <textarea class="primary_input_field form-control" rows="4" name="description">{{ old('description', $feesType->description ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Submit Button --}}
                                        @if (isset($feesType) ? $canEditFeesType : $canAddFeesType)
                                            <div class="row mt-20">
                                                <div class="col-lg-12 text-center">
                                                    <button class="primary-btn fix-gr-bg submit col-12"
                                                        data-toggle="tooltip">
                                                        <span class="ti-check"></span>
                                                        {{ isset($feesType) ? __('common.update') : __('common.save') }}
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
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
                                    <h3 class="mb-15"> @lang('fees.fees_type_list')</h3>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id" class="table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th> @lang('common.name')</th>
                                                <th> @lang('fees.fees_group')</th>
                                                <th> @lang('common.description')</th>
                                                <th> @lang('price')</th>
                                                <th> @lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($feesTypes as $feesType)
                                                <tr>
                                                    {{-- @php
                                                        dd($feesType);
                                                    @endphp --}}
                                                    <td>{{ $feesType->name }}</td>
                                                    <td>{{ $feesType->feeGroup?->name ?? 'No Group' }}</td>
                                                    <td>{{ $feesType->description }}</td>
                                                    <td>{{ $feesType->price }}</td>
                                                    <td>
                                                        @if ($canEditFeesType || $canDeleteFeesType)
                                                            <x-drop-down>

                                                                @if ($canEditFeesType)
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('fees.fees-type-edit', [$feesType->id]) }}">@lang('common.edit')</a>
                                                                @endif

                                                                @if ($canDeleteFeesType)
                                                                    <a class="dropdown-item" data-toggle="modal"
                                                                        data-target="#deleteFeesTypeModal{{ $feesType->id }}"
                                                                        href="#">@lang('common.delete')</a>
                                                                @endif

                                                            </x-drop-down>
                                                        @else
                                                            <span class="text-muted">&mdash;</span>
                                                        @endif
                                                    </td>
                                                </tr>

                                                @if ($canDeleteFeesType)
                                                    <div class="modal fade admin-query"
                                                        id="deleteFeesTypeModal{{ $feesType->id }}">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">@lang('fees.delete_fees_type')</h4>
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
                                                                            action="{{ route('fees.fees-type-delete', $feesType->id) }}"
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
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')
@push('script')
    <script>
        $('#table_id').DataTable({
            responsive: true,
            dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 '" +
                "<'dt-toolbar-title'>" +
                "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-search'f><'dt-buttons-wrap'B>>" +
                ">" +
                "<'row'<'col-sm-12'tr>>" +
                "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>>",
            buttons: [{
                    extend: 'copyHtml5',
                    title: getFileName(),
                    text: '<i class="fa-regular fa-copy"></i>',
                    titleAttr: 'Copy'
                },
                {
                    extend: 'csvHtml5',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-file-csv"></i>',
                    titleAttr: 'CSV'
                },
                {
                    extend: 'excelHtml5',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-file-excel"></i>',
                    titleAttr: 'Excel'
                },
                {
                    extend: 'pdfHtml5',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-file-pdf"></i>',
                    titleAttr: 'PDF'
                },
                {
                    extend: 'print',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-print"></i>',
                    titleAttr: 'Print'
                }
            ],
            pageLength: 100,
            language: {
                search: '<i class=""></i>',
                searchPlaceholder: 'Search...',
                paginate: {
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            },

            paging: true,
            searching: true,
            ordering: true,
            info: true,
        });
    </script>
@endpush
