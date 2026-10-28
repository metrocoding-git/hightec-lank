@extends('layouts.app')
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
@section('title', __('fees.fees_group'))


@section('mainContent')
    @php
        // Permission keys from config_permissions.php ("Fees" category, Invoice Item Group window).
        $canAddFeeGroup = Auth::user()->hasPermission('fee-group-create');
        $canEditFeeGroup = Auth::user()->hasPermission('fee-group-update');
        $canDeleteFeeGroup = Auth::user()->hasPermission('fee-group-delete');
    @endphp
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('fees.fees_group')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees_group')</a>
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid p-0">
        @include('components.partials.permanent-delete-note', ['rule' => 'fee_group'])
    </div>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                {{-- Form Column --}}
                <div class="col-lg-3">
                    <form
                        action="{{ isset($feesGroup) ? route('fee-group-update', $feesGroup->id) : route('fee-group-create') }}"
                        method="POST">
                        @csrf
                        @if (isset($feesGroup))
                            @method('PUT') {{-- This tells Laravel to treat it as an update --}}
                        @endif

                        <div class="white-box">
                            <div class="main-title">
                                <h3 class="mb-15">
                                    {{ isset($feesGroup) ? __('fees.edit_fees_group') : __('fees.add_fees_group') }}
                                </h3>
                            </div>
                            <div class="add-visitor">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="primary_input">
                                            <label class="primary_input_label">@lang('common.name') <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" class="primary_input_field form-control"
                                                value="{{ old('name', $feesGroup->name ?? '') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <div class="primary_input">
                                            <label class="primary_input_label">@lang('common.description')</label>
                                            <textarea name="description" rows="4" class="primary_input_field form-control">{{ old('description', $feesGroup->description ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                @if (isset($feesGroup) ? $canEditFeeGroup : $canAddFeeGroup)
                                    <div class="row mt-20">
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg col-12" type="submit">
                                                <span class="ti-check"></span>
                                                {{ isset($feesGroup) ? __('common.update') : __('common.save') }}
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>

                </div>

                {{-- Table Column --}}
                <div class="col-lg-9">
                    <div class="white-box">
                        <div class="main-title mb-15">
                            <h3>@lang('fees.fees_group_list')</h3>
                        </div>
                        <x-table>
                            <table id="table_id" class="table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('common.name')</th>
                                        <th>@lang('common.description')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($feesGroups as $group)
                                        <tr>
                                            <td>{{ $group->name }}</td>
                                            <td>{{ $group->description }}</td>
                                            <td>
                                                @if ($canEditFeeGroup || $canDeleteFeeGroup)
                                                    <x-drop-down>
                                                        @if ($canEditFeeGroup)
                                                            <a class="dropdown-item"
                                                                href="{{ route('fee-group-edit', $group->id) }}">
                                                                @lang('common.edit')</a>
                                                        @endif
                                                        @if ($canDeleteFeeGroup)
                                                            <form action="{{ route('fee-group-delete', $group->id) }}"
                                                                method="POST" style="display:inline;">
                                                                @method('DELETE')
                                                                @csrf
                                                                <button class="dropdown-item"
                                                                    onclick="return confirm('@lang('common.are_you_sure_to_delete')');">@lang('common.delete')</button>
                                                            </form>
                                                        @endif
                                                    </x-drop-down>
                                                @else
                                                    <span class="text-muted">&mdash;</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
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
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')
@push('script')
    <script>
        $('#table_id').DataTable({
            responsive: true,
            dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3'" +
                "<'dt-toolbar-title'>" +
                "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-length'l><'dt-search'f><'dt-buttons-wrap'B>>" +
                ">" +
                "<'row'<'col-sm-12'tr>>" +
                "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>> ",

          
            paging: true,
            searching: true,
            ordering: true,
            info: true,
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
        });
    </script>
@endpush
