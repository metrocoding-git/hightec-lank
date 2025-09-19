@extends('layouts.app')

@section('title')
    @lang('academics.About')
@endsection

@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('inventory.item_receive_list')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('inventory.inventory')</a>
                    <a href="#">@lang('inventory.item_receive_list')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-8 col-md-6"></div>
                <div class="col-lg-4 text-md-right text-left col-md-6 mb-30-lg">
                    <a href="{{ route('item-receive') }}" class="primary-btn small fix-gr-bg">
                        <span class="ti-plus pr-2"></span>
                        @lang('inventory.new_item_receive')
                    </a>
                </div>
            </div>

            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('inventory.item_receive_list')</h3>
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
                                                <th>@lang('inventory.reference_no')</th>
                                                <th>@lang('inventory.supplier_company_name')</th>
                                                <th>@lang('common.date')</th>
                                                <th>@lang('inventory.grand_total')</th>
                                                <th>@lang('inventory.total_quantity')</th>
                                                <th>@lang('inventory.paid')</th>
                                                <th>@lang('inventory.balance') (Rs)</th>
                                             
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($itemReceives))
                                                @foreach ($itemReceives as $key => $itemReceive)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $itemReceive->reference_no }}</td>
                                                        <td>{{ optional($itemReceive->supplier)->company_name }}</td>
                                                        <td>{{ $itemReceive->receive_date }}</td>
                                                        <td>{{ $itemReceive->sub_total }}</td>
                                                        <td>{{ $itemReceive->quantity }}</td>
                                                        <td>{{ $itemReceive->total_paid }}</td>
                                                        <td>{{ $itemReceive->sub_total - $itemReceive->total_paid }}</td>
                                                        
                                                        <td>
                                                            <div class="dropdown">
                                                                <button type="button" class=" dropdown-toggle" data-toggle="dropdown">
                                                                    @lang('common.select')
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item" 
                                                                    href="{{ route('item-receive.show', $itemReceive->id) }}"
                                                                    >
                                                                        <i class="ti-eye"></i> @lang('common.view')
                                                                    </a>
                                                                    <a class="dropdown-item" 
                                                                    href="{{ route('item-receive.edit', $itemReceive->id) }}"
                                                                    >
                                                                        <i class="ti-pencil"></i> @lang('common.edit')
                                                                    </a>
                                                                    <form 
                                                                    action="{{ route('item-receive.destroy', $itemReceive->id) }}" 
                                                                    method="POST" onsubmit="return confirm('@lang('common.are_you_sure')')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="ti-trash"></i> @lang('common.delete')
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
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
    </section>
@endsection

@include('components.partials.data_table_js')
@include('components.partials.server_side_datatable')
