@extends('layouts.app')
@section('title')
    @lang('inventory.issued_item_list')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('inventory.issued_item_list')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('inventory.inventory')</a>
                    <a href="#">@lang('inventory.issued_item_list')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-15">@lang('inventory.issued_item_list')</h3>
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
                                            <th>@lang('inventory.item_name')</th>
                                            <th>@lang('inventory.item_category')</th>
                                            <th>@lang('inventory.issue_to')</th>
                                            <th>@lang('inventory.issue_date')</th>
                                            <th>@lang('inventory.return_date')</th>
                                            <th>@lang('inventory.quantity')</th>
                                            <th>@lang('common.status')</th>
                                            <th>@lang('common.action')</th>
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
                                                        @if ($value->student_id && $value->student)
                                                            {{ $value->student->student_name }}
                                                        @elseif ($value->issue_to && $value->staff)
                                                            {{ $value->staff->name }}
                                                        @else
                                                            {{ $value->branch ? $value->branch->name : '' }}
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
                                                            <button class="primary-btn small bg-success text-white border-0">@lang('inventory.issued')</button>
                                                        @else
                                                            <button class="primary-btn small bg-primary text-white border-0">@lang('inventory.returned')</button>
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
                                                            @else
                                                                <form action="{{ route('issue-item') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="id" value="{{ @$value->id }}">
                                                                    <button type="submit" class="dropdown-item">Issue</button>
                                                                </form>
                                                            @endif
                                                            <a class="dropdown-item" href="{{ route('item-issue-edit', @$value->id) }}">@lang('common.edit')</a>
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
    </section>
@endsection

@include('components.partials.data_table_js')
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')
@include('components.partials.data_table_js')

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