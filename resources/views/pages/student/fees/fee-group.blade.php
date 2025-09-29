@extends('layouts.app')
@section('title', __('fees.fees_group'))


@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('fees.fees_group')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees')</a>
                    <a href="#">@lang('fees.fees_group')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                {{-- Form Column --}}
                <div class="col-lg-3">
                    <form
    action="{{ isset($feesGroup) ? route('fee-group-update', $feesGroup->id) : route('fee-group-create') }}"
    method="POST">
    @csrf
    @if(isset($feesGroup))
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
                        <label class="primary_input_label">@lang('common.name') <span class="text-danger">*</span></label>
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
            <div class="row mt-40">
                <div class="col-lg-12 text-center">
                    <button class="primary-btn fix-gr-bg" type="submit">
                        <span class="ti-check"></span>
                        {{ isset($feesGroup) ? __('common.update') : __('common.save') }}
                    </button>
                </div>
            </div>
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
                                                <x-drop-down>
                                                <a class="dropdown-item"
                                                    href="{{ route('fee-group-edit', $group->id) }}"
                                                    >
                                                    @lang('common.edit')</a>
                                                    <form action="{{ route('fee-group-delete', $group->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button class="dropdown-item"
                                                            onclick="return confirm('@lang('common.are_you_sure_to_delete')');">@lang('common.delete')</button>
                                                    </form>
                                                </x-drop-down>
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
