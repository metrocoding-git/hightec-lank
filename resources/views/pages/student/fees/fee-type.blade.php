@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('fees.fees_type')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees')</a>
                    @if (isset($feesType))
                        <a href="#">@lang('fees.edit_fees_type')</a>
                    @else
                        <a href="#">@lang('fees.fees_type')</a>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @if (isset($feesType))
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
                                        <div class="row mt-25">
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
                                        <div class="row mt-25">
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
                                        <div class="row mt-25">
                                            <div class="col-lg-12">
                                                <div class="primary_input">
                                                    <label class="primary_input_label">@lang('common.description')</label>
                                                    <textarea class="primary_input_field form-control" rows="4" name="description">{{ old('description', $feesType->description ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Submit Button --}}
                                        <div class="row mt-40">
                                            <div class="col-lg-12 text-center">
                                                <button class="primary-btn fix-gr-bg submit" data-toggle="tooltip">
                                                    <span class="ti-check"></span>
                                                    {{ isset($feesType) ? __('common.update') : __('common.save') }}
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
                                                        <x-drop-down>

                                                            <a class="dropdown-item"
                                                                href="{{ route('fees.fees-type-edit', [$feesType->id]) }}">@lang('common.edit')</a>


                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#deleteFeesTypeModal{{ $feesType->id }}"
                                                                href="#">@lang('common.delete')</a>

                                                        </x-drop-down>
                                                    </td>
                                                </tr>

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
            dom: 'Bfrtip',

            buttons: [{
                    extend: 'copyHtml5',
                    text: '<i class="fa-regular fa-copy"></i>',
                    titleAttr: 'Copy'
                },
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa-solid fa-file-csv"></i>',
                    titleAttr: 'CSV'
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel"></i>',
                    titleAttr: 'Excel'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa-solid fa-file-pdf"></i>',
                    titleAttr: 'PDF'
                },
                {
                    extend: 'print',
                    text: '<i class="fa-solid fa-print"></i>',
                    titleAttr: 'Print'
                }
            ],

            paging: true,
            searching: true,
            ordering: true,
            info: true,

            // 👇 This part adds the search and pagination icons
            language: {
                search: '<i class="fa-solid fa-magnifying-glass me-2"></i>',
                searchPlaceholder: 'Search...',
                paginate: {
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            }
        });    </script>
    
@endpush
