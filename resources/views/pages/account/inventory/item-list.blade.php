@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('inventory.item_list')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('inventory.inventory')</a>
                    <a href="#">@lang('inventory.item_list')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @if (isset($editData))

                <div class="row">
                    <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                        <a href="{{ route('item-list') }}" class="primary-btn small fix-gr-bg">
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
                            {{-- @if (isset($editData))
                                {{ Form::open(['class' => 'form-horizontal', 'route' => ['item-list-update', $editData->id], 'method' => 'PUT']) }}
                            @else
                                @if (userPermission('item-list-store'))
                                    {{ Form::open(['class' => 'form-horizontal', 'route' => 'item-list-store', 'method' => 'POST']) }}
                                @endif
                            @endif --}}
                            <form class="form-horizontal"
                                action="{{ isset($editData) ? route('item-update', $editData->id) : route('item-store') }}"
                                method="POST">
                                @csrf
                                @if (isset($editData))
                                    @method('PUT')
                                @endif

                                <div class="white-box">
                                    <div class="main-title">
                                        <h3 class="mb-15">
                                            @if (isset($editData))

                                                @lang('inventory.edit_item')
                                            @else
                                                @lang('inventory.add_item')
                                            @endif
                                        </h3>
                                    </div>
                                    <div class="add-visitor">
                                        <div class="row">
                                            <div class="col-lg-12 mb-15">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="">@lang('inventory.item_name')
                                                        <span class="text-danger"> *</span> </label>
                                                    <input
                                                        class="primary_input_field form-control{{ $errors->has('item_name') ? ' is-invalid' : '' }}"
                                                        type="text" name="item_name" autocomplete="off"
                                                        value="{{ isset($editData) ? $editData->item_name : '' }}">


                                                    @if ($errors->has('item_name'))
                                                        <span class="text-danger">
                                                            {{ $errors->first('item_name') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-12 mb-15">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="">@lang('inventory.item_category')
                                                        <span class="text-danger"> *</span> </label>
                                                    <select
                                                        class="primary_select  form-control{{ $errors->has('category_name') ? ' is-invalid' : '' }}"
                                                        name="category_name" id="category_name">
                                                        <option data-display="@lang('inventory.select_item_category') *" value="">
                                                            @lang('common.select')</option>
                                                        @foreach ($itemCategories as $key => $value)
                                                            <option value="{{ $value->id }}"
                                                                @if (isset($editData)) @if ($editData->item_category_id == $value->id)
                                                    selected @endif
                                                                @endif
                                                                >{{ $value->name }}</option>
                                                        @endforeach
                                                    </select>




                                                    @if ($errors->has('category_name'))
                                                        <span class="text-danger invalid-select" role="alert">
                                                            {{ $errors->first('category_name') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-12 mb-15">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="">@lang('common.description')
                                                        <span></span> </label>
                                                    <textarea class="primary_input_field form-control" cols="0" rows="4" name="description" id="description">{{ isset($editData) ? $editData->description : '' }}</textarea>



                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-40">
                                            <div class="col-lg-12 text-center">
                                                <button class="primary-btn fix-gr-bg submit" data-toggle="tooltip">

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
                <div class="col-lg-9">
                    <div class="white-box">
                        <div div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('inventory.item_list')</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id" class="table data-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>@lang('common.sl')</th>
                                                <th>@lang('inventory.item_name')</th>
                                                <th>@lang('student.category') </th>
                                                <th>@lang('common.description') </th>
                                                <th>@lang('inventory.total_in_stock') </th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $key => $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->item_name }}</td>
                                                    <td>{{ $item->category->name ?? '' }}</td>
                                                    <td>{{ $item->description }}</td>
                                                    <td>{{ $item->stock ?? 0 }}</td>
                                                    <td>
                                                        <x-drop-down>
                                                            <a class="dropdown-item"
                                                                href="{{ route('item-edit', $item->id) }}">
                                                                @lang('common.edit')
                                                            </a>
                                                            
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#deletecategoryModal{{ $item->id }}"
                                                                href="#">@lang('common.delete')</a>

                                                        </x-drop-down>
                                                    </td>
                                                </tr>

                                                
                                                <div class="modal fade admin-query"
                                                    id="deletecategoryModal{{ $item->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">@lang('fees.delete_item')</h4>
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
                                                                        action="{{ route('item-delete', $item->id) }}"
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
@include('components.partials.server_side_datatable')
@include('components.partials.data_table_css')
@push('script')
    <script>
        $('#table_id').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
        });
    </script>
    
@endpush
