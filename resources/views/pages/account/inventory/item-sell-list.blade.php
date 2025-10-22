@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('inventory.item_sell_list')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('inventory.inventory')</a>
                    <a href="#">@lang('inventory.item_sell_list')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-8 col-md-6">

                </div>

                <div class="col-lg-4 text-md-right text-left col-md-6 mb-30-lg">
                    <a href="{{ route('item-sell') }}" class="primary-btn small fix-gr-bg">
                        <span class="ti-plus pr-2"></span>
                        @lang('inventory.new_item_sell')
                    </a>
                </div>

            </div>

            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('inventory.item_sell_list')</h3>
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
                                                <th>@lang('inventory.reference_no')</th>
                                                <th>@lang('inventory.role_name')</th>
                                                <th>@lang('inventory.buyer_name')</th>
                                                <th>@lang('common.date')</th>
                                                <th>Sell Price</th>
                                                <th>@lang('inventory.grand_total')</th>
                                                <th>@lang('inventory.total_quantity')</th>
                                                {{-- <th>@lang('inventory.paid')</th>
                                                <th>@lang('inventory.balance') (Rs)</th> --}}
                                                <th>@lang('common.status')</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @if (isset($allItemSellLists))
                                                @foreach ($allItemSellLists as $key => $value)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $value->reference_no }}</td>
                                                        <td>{{ $value->role ? $value->role->role_name : 'Others' }}</td>
                                                        <td>
                                                            @if ($value->role_id == 2 && $value->student) {{-- Student Role --}}
                                                                {{ $value->student->student_name }}
                                                            @elseif ($value->role_id == 0 && $value->others_name) {{-- Other/Customer Role --}}
                                                                {{ $value->others_name }}
                                                            @elseif ($value->role_id == 3 && $value->user) {{-- Staff Role --}}
                                                                {{ $value->user->name }}
                                                            @elseif ($value->role_id == 0 && !$value->others_name) {{-- Other but no name provided --}}
                                                                Other Customer
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td data-sort="{{ strtotime($value->sell_date) }}">
                                                            {{ $value->sell_date ? \Carbon\Carbon::parse($value->sell_date)->format('m/d/Y') : '' }}
                                                        </td>
                                                        <td>{{ number_format((float) $value->sell_price, 2, '.', '') }}</td>

                                                        <td>{{ number_format((float) $value->total_paid + $value->total_due, 2, '.', '') }}</td>
                                                        <td>{{ $value->total_quantity }}</td>
                                                        {{-- <td>{{ number_format((float) $value->total_paid, 2, '.', '') }}</td>
                                                        <td>{{ number_format((float) $value->total_due, 2, '.', '') }}</td> --}}
                                                        <td>
                                                            <select class="form-control status-select" data-id="{{ $value->id }}">
                                                                <option value="P" {{ $value->status == 'P' ? 'selected' : '' }}>@lang('inventory.paid')</option>
                                                                <option value="PP" {{ $value->status == 'PP' ? 'selected' : '' }}>@lang('inventory.partial')</option>
                                                                <option value="U" {{ $value->status == 'U' ? 'selected' : '' }}>@lang('inventory.unpaid')</option>
                                                                <option value="R" {{ $value->status == 'R' ? 'selected' : '' }}>@lang('inventory.returned')</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <x-drop-down>
                                                                <a class="dropdown-item modalLink"
                                                                    title="@lang('common.view') @lang('inventory.item_sell')"
                                                                    data-modal-size="modal-lg"
                                                                    href="{{ route('view-item-sell', $value->id) }}">@lang('common.view')</a>
                                                                @if ($value->status != 'R') {{-- Assuming 'R' is for Returned --}}
                                                                    @if ($value->total_paid == 0) {{-- Only allow edit if no payment has been made --}}
                                                                        {{-- @if (userPermission('edit-item-sell')) --}}
                                                                        <a class="dropdown-item modalLink"
                                                                            title="@lang('common.edit') @lang('inventory.item_sell')"
                                                                            data-modal-size="modal-lg"
                                                                            href="{{ route('edit-item-sell', $value->id) }}">@lang('common.edit')</a>
                                                                        {{-- @endif --}}
                                                                    @endif
                                                                @endif

                                                                @if ($value->status != 'R')
                                                                    @if ($value->total_due > 0)
                                                                        {{-- @if (userPermission('add-payment-sell')) --}}
                                                                            <a class="dropdown-item modalLink"
                                                                                title="@lang('inventory.add_payment')"
                                                                                data-modal-size="modal-md"
                                                                                href="{{ route('add-payment-sell', $value->id) }}">@lang('common.add_payment')</a>
                                                                        {{-- @endif --}}
                                                                    @endif
                                                                @endif

                                                                {{-- @if ($value->status != 'P') --}}
                                                                    {{-- @if (userPermission('view-sell-payments')) --}}
                                                                        <a class="dropdown-item modalLink"
                                                                            data-modal-size="modal-lg"
                                                                            title="@lang('inventory.view_payment')"
                                                                            href="{{ route('view-sell-payments', $value->id) }}">@lang('inventory.view_payment')</a>
                                                                    {{-- @endif --}}
                                                                {{-- @endif --}}

                                                                @if ($value->status != 'R')
                                                                    @if ($value->total_paid == 0) {{-- Only allow delete if no payment has been made --}}
                                                                        {{-- @if (userPermission('delete-item-sale-view')) --}}
                                                                            <a class="dropdown-item deleteUrl"
                                                                                data-modal-size="modal-md"
                                                                                title="@lang('inventory.delete_sold_item')"
                                                                                href="{{ route('delete-item-sell-view', $value->id) }}">@lang('common.delete')</a>
                                                                        {{-- @endif --}}
                                                                    @endif
                                                                @endif

                                                                @if ($value->status != 'R')
                                                                    @if ($value->total_paid > 0) {{-- Only allow cancel if some payment has been made --}}
                                                                        <a class="dropdown-item deleteUrl"
                                                                            data-modal-size="modal-md"
                                                                            title="Cancel Item Sell"
                                                                            href="{{ route('cancel-item-sell-view', $value->id) }}">@lang('common.cancel')</a>
                                                                    @endif
                                                                @endif

                                                                @if ($value->status != 'R')
                                                                    <a class="dropdown-item return-button"
                                                                        data-id="{{ $value->id }}"
                                                                        title="Return Item Sell">@lang('inventory.return')</a>
                                                                @endif
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
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
        });

        // Handle status change
        $('.status-select').change(function() {
            var sellId = $(this).data('id');
            var newStatus = $(this).val();

            $.ajax({
                url: '{{ url("account/inventory/item-sell") }}' + '/' + sellId + '/update-status',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Status updated successfully!');
                        location.reload(); // Or update the row without reload
                    } else {
                        toastr.error('Failed to update status.');
                    }
                },
                error: function() {
                    toastr.error('Error updating status.');
                }
            });
        });

        // Handle return button click
        $('.return-button').click(function(e) {
            e.preventDefault();
            var sellId = $(this).data('id');

            if (confirm('Are you sure you want to return this item?')) {
                $.ajax({
                    url: '{{ url("account/inventory/item-sell") }}' + '/' + sellId + '/direct-return',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Item returned successfully!');
                            location.reload();
                        } else {
                            toastr.error('Failed to return item.');
                        }
                    },
                    error: function() {
                        toastr.error('Error returning item.');
                    }
                });
            }
        });
    </script>

@endpush
