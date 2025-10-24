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
                                <div id="itemSellListTable">
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
                                                                @if ($value->role_id == 2 && $value->student)
                                                                    {{-- Student Role --}}
                                                                    {{ $value->student->student_name }}
                                                                @elseif ($value->role_id == 0 && $value->others_name)
                                                                    {{-- Other/Customer Role --}}
                                                                    {{ $value->others_name }}
                                                                @elseif ($value->role_id == 3 && $value->user)
                                                                    {{-- Staff Role --}}
                                                                    {{ $value->user->name }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td data-sort="{{ strtotime($value->sell_date) }}">
                                                                {{ $value->sell_date ? \Carbon\Carbon::parse($value->sell_date)->format('m/d/Y') : '' }}
                                                            </td>
                                                            <td>{{ number_format((float) $value->total_sell_price, 2, '.', '') }}
                                                            </td>

                                                            <td>{{ number_format((float) $value->total_paid + $value->total_due, 2, '.', '') }}
                                                            </td>
                                                            <td>{{ $value->total_quantity }}</td>
                                                            <td>
                                                                @if ($value->status == 'P')
                                                                    <button
                                                                        class="primary-btn small bg-success text-white border-0">@lang('inventory.paid')</button>
                                                                @elseif($value->status == 'PP')
                                                                    <button
                                                                        class="primary-btn small bg-warning text-white border-0">@lang('inventory.partial')</button>
                                                                @elseif($value->status == 'U')
                                                                    <button
                                                                        class="primary-btn small bg-danger text-white border-0">@lang('inventory.unpaid')</button>
                                                                @elseif($value->status == 'R')
                                                                    <button
                                                                        class="primary-btn small bg-secondary text-white border-0">@lang('inventory.returned')</button>
                                                                @else
                                                                    <button
                                                                        class="primary-btn small bg-info text-white border-0">@lang('inventory.N/A')</button>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <x-drop-down>
                                                                    <a class="dropdown-item view-item-sell"
                                                                        title="@lang('common.view') @lang('inventory.item_sell')"
                                                                        data-id="{{ $value->id }}"
                                                                        href="#">@lang('common.view')</a>
                                                                    @if ($value->status != 'R')
                                                                        @if ($value->total_paid == 0)
                                                                            <a class="dropdown-item modalLink"
                                                                                title="@lang('common.edit') @lang('inventory.item_sell')"
                                                                                data-modal-size="modal-lg"
                                                                                href="{{ route('edit-item-sell', $value->id) }}">@lang('common.edit')</a>
                                                                        @endif
                                                                    @endif

                                                                    @if ($value->status != 'R')
                                                                        @if ($value->total_due > 0)
                                                                            <a class="dropdown-item modalLink"
                                                                                title="@lang('inventory.add_payment')"
                                                                                data-modal-size="modal-md"
                                                                                href="{{ route('add-payment-sell', $value->id) }}">@lang('common.add_payment')</a>
                                                                        @endif
                                                                    @endif

                                                                    <a class="dropdown-item view-sell-payments"
                                                                        title="@lang('inventory.view_payment')"
                                                                        data-id="{{ $value->id }}"
                                                                        href="#">@lang('inventory.view_payment')</a>

                                                                    @if ($value->status != 'R')
                                                                        @if ($value->total_paid == 0)
                                                                            <a class="dropdown-item deleteUrl"
                                                                                data-modal-size="modal-md"
                                                                                title="@lang('inventory.delete_sold_item')"
                                                                                href="{{ route('delete-item-sell-view', $value->id) }}">@lang('common.delete')</a>
                                                                        @endif
                                                                    @endif

                                                                    @if ($value->status != 'R')
                                                                        @if ($value->total_paid > 0)
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
        </div>
    </section>
@endsection

<!-- Item Sell Details Modal -->
<div class="modal fade admin-query" id="itemSellDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #6f42c1; color: white;">
                <h4 class="modal-title">Item Sell Details</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body modal-info">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-card bg-light p-3 rounded mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ti-receipt text-primary mr-3" style="font-size: 24px;"></i>
                                                <div>
                                                    <h6 class="mb-1">@lang('inventory.reference_no')</h6>
                                                    <span class="text-muted" id="itemSellReferenceNo"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-card bg-light p-3 rounded mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ti-user text-success mr-3" style="font-size: 24px;"></i>
                                                <div>
                                                    <h6 class="mb-1">@lang('inventory.buyer_name')</h6>
                                                    <span class="text-muted" id="itemSellBuyerName"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-card bg-light p-3 rounded mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ti-calendar text-info mr-3" style="font-size: 24px;"></i>
                                                <div>
                                                    <h6 class="mb-1">@lang('common.date')</h6>
                                                    <span class="text-muted" id="itemSellDate"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-card bg-light p-3 rounded mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ti-shopping-cart text-danger mr-3"
                                                    style="font-size: 24px;"></i>
                                                <div>
                                                    <h6 class="mb-1">@lang('inventory.total_quantity')</h6>
                                                    <span class="text-muted" id="itemSellTotalQuantity"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-card bg-light p-3 rounded mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ti-money text-success mr-3" style="font-size: 24px;"></i>
                                                <div>
                                                    <h6 class="mb-1">@lang('inventory.sell_price')</h6>
                                                    <span class="text-muted" id="itemSellPrice"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-card bg-light p-3 rounded mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ti-credit-card text-warning mr-3"
                                                    style="font-size: 24px;"></i>
                                                <div>
                                                    <h6 class="mb-1">@lang('inventory.total_paid')</h6>
                                                    <span class="text-muted" id="itemSellTotalPaid"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-card bg-light p-3 rounded mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ti-clipboard text-secondary mr-3"
                                                    style="font-size: 24px;"></i>
                                                <div>
                                                    <h6 class="mb-1">Descriptions</h6>
                                                    <span class="text-muted" id="itemSellDescription"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-card bg-light p-3 rounded mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ti-check-box text-success mr-3"
                                                    style="font-size: 24px;"></i>
                                                <div>
                                                    <h6 class="mb-1">@lang('common.status')</h6>
                                                    <span class="text-muted" id="itemSellStatus"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                {{-- --------------------item qty table-------------- --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="mb-3">Sold Items</h6>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>@lang('inventory.item')</th>
                                                        <th>@lang('inventory.quantity')</th>
                                                        <th>@lang('inventory.unit_price')</th>
                                                        <th>@lang('inventory.sub_total')</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="itemSellItemsTableBody">
                                                    {{-- Items will be inserted here by JS --}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<!-- Item Sell Payment Details Modal -->
<div class="modal fade admin-query" id="itemSellPaymentsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #6f42c1; color: white;">
                <h4 class="modal-title">Payment Details</h4>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body modal-info">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            {{-- <div class="card-header bg-success text-white">
                                <h4 class="card-title mb-0">
                                    <i class="ti-credit-card mr-2"></i>Payment Details
                                </h4>
                            </div> --}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="payment-summary bg-light p-4 rounded mb-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti-receipt text-primary mr-3"
                                                        style="font-size: 32px;"></i>
                                                    <div>
                                                        <h5 class="mb-1">@lang('inventory.reference_no')</h5>
                                                        <span class="text-muted" id="itemSellBatchReferenceNo"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="payment-summary bg-light p-4 rounded mb-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti-money text-success mr-3"
                                                        style="font-size: 32px;"></i>
                                                    <div>
                                                        <h5 class="mb-1">@lang('inventory.total_paid')</h5>
                                                        <span class="text-muted" id="itemSellBatchTotalPaid"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="payment-summary bg-light p-4 rounded mb-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti-alert text-danger mr-3" style="font-size: 32px;"></i>
                                                    <div>
                                                        <h5 class="mb-1">@lang('inventory.total_due')</h5>
                                                        <span class="text-muted" id="itemSellBatchTotalDue"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="payment-summary bg-light p-4 rounded mb-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti-bar-chart text-info mr-3"
                                                        style="font-size: 32px;"></i>
                                                    <div>
                                                        <h5 class="mb-1">@lang('inventory.grand_total')</h5>
                                                        <span class="text-muted" id="itemSellBatchGrandTotal"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Payment Status Badge --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="text-center mb-3" id="itemSellBatchStatusBadge">
                                            {{-- Status badge will be inserted here by JS --}}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mt-4 mb-3">Item Sold</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Item</th>
                                                <th>@lang('inventory.quantity')</th>
                                                <th>@lang('inventory.unit_price')</th>
                                                <th>@lang('inventory.sub_total')</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemSellBatchItemsTableBody">
                                            {{-- Items will be inserted here by JS --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.partials.data_table_js')
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')
@push('script')
    <script>
        $(document).ready(function() {
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

            // Show Item Details
            $(document).on('click', '.view-item-sell', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                // Fetch data via AJAX and populate modal fields
                $.ajax({
                    url: '{{ url('account/inventory/item-sell') }}/' + id,
                    method: 'GET',
                    success: function(data) {
                        if (data.itemSell) {
                            $('#itemSellReferenceNo').text(data.itemSell.reference_no);
                            $('#itemSellBuyerName').text(data.itemSell.buyer_name);
                            $('#itemSellDate').text(data.itemSell.sell_date ? moment(data
                                .itemSell.sell_date).format('MM/DD/YYYY') : '');
                            $('#itemSellTotalQuantity').text(data.itemSell.total_quantity);
                            $('#itemSellPrice').text(parseFloat(data.itemSell.sell_price)
                                .toFixed(2));
                            $('#itemSellTotalPaid').text(parseFloat(data.itemSell.total_paid)
                                .toFixed(2));
                            $('#itemSellTotalDue').text(parseFloat(data.itemSell.total_due)
                                .toFixed(2));
                            $('#itemSellCourseName').text(data.itemSell.course ? data.itemSell
                                .course.name : 'N/A');
                            $('#itemSellIntakeName').text(data.itemSell.intake ? data.itemSell
                                .intake.name : 'N/A');
                            $('#itemSellGrandTotal').text(parseFloat(data.itemSell.total_paid +
                                data.itemSell.total_due).toFixed(2));
                            $('#itemSellDescription').text(data.itemSell.description || '-');
                            let statusText = '';
                            if (data.itemSell.status == 'P') {
                                statusText = '@lang('inventory.paid')';
                            } else if (data.itemSell.status == 'PP') {
                                statusText = '@lang('inventory.partial')';
                            } else if (data.itemSell.status == 'U') {
                                statusText = '@lang('inventory.unpaid')';
                            } else if (data.itemSell.status == 'R') {
                                statusText = '@lang('inventory.returned')';
                            } else {
                                statusText = '@lang('inventory.N/A')';
                            }
                            $('#itemSellStatus').text(statusText);

                            // Populate items table
                            let itemsTableBody = '';
                            if (data.itemSell.items && data.itemSell.items.length > 0) {
                                $.each(data.itemSell.items, function(key, item) {
                                    itemsTableBody += `
                                        <tr>
                                            <td>${key + 1}</td>
                                            <td>${item.item ? item.item.item_name : 'N/A'}</td>
                                            <td>${item.quantity}</td>
                                            <td>${parseFloat(item.unit_price).toFixed(2)}</td>
                                            <td>${parseFloat(item.sub_total).toFixed(2)}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                itemsTableBody =
                                    `<tr><td colspan="5" class="text-center">No items found for this sell batch.</td></tr>`;
                            }
                            $('#itemSellItemsTableBody').html(itemsTableBody);

                            // Show the modal
                            $('#itemSellDetailsModal').modal('show');
                        }
                    },
                    error: function() {
                        toastr.error('Error fetching item sell details.');
                    }
                });
            });

            // Show Payment Details
            $(document).on('click', '.view-sell-payments', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                // Fetch data via AJAX and populate modal fields
                $.ajax({
                    url: '{{ url('account/inventory/item-sell') }}/' + id + '/view-payments',
                    method: 'GET',
                    success: function(data) {
                        if (data.itemSellBatch) {
                            $('#itemSellBatchReferenceNo').text(data.itemSellBatch
                            .reference_no);
                            $('#itemSellBatchTotalPaid').text(parseFloat(data.itemSellBatch
                                .total_paid).toFixed(2));
                            $('#itemSellBatchTotalDue').text(parseFloat(data.itemSellBatch
                                .total_due).toFixed(2));
                            $('#itemSellBatchGrandTotal').text(parseFloat(data.itemSellBatch
                                .total_paid + data.itemSellBatch.total_due).toFixed(2));

                            let statusBadge = '';
                            if (data.itemSellBatch.total_due == 0 && data.itemSellBatch
                                .total_paid > 0) {
                                statusBadge =
                                    '<span class="badge badge-success" style="font-size: 16px; padding: 10px 20px;">@lang('inventory.paid')</span>';
                            } else if (data.itemSellBatch.total_paid > 0 && data.itemSellBatch
                                .total_due > 0) {
                                statusBadge =
                                    '<span class="badge badge-warning" style="font-size: 16px; padding: 10px 20px;">@lang('inventory.partial')</span>';
                            } else if (data.itemSellBatch.total_paid == 0 && data.itemSellBatch
                                .total_due > 0) {
                                statusBadge =
                                    '<span class="badge badge-danger" style="font-size: 16px; padding: 10px 20px;">@lang('inventory.unpaid')</span>';
                            } else {
                                statusBadge =
                                    '<span class="badge badge-info" style="font-size: 16px; padding: 10px 20px;">@lang('inventory.N/A')</span>';
                            }
                            $('#itemSellBatchStatusBadge').html(statusBadge);

                            let itemsTableBody = '';
                            if (data.itemSellBatch.items && data.itemSellBatch.items.length >
                                0) {
                                $.each(data.itemSellBatch.items, function(key, item) {
                                    itemsTableBody += `
                                        <tr>
                                            <td>${key + 1}</td>
                                            <td>${item.item ? item.item.item_name : 'N/A'}</td>
                                            <td>${item.quantity}</td>
                                            <td>${parseFloat(item.unit_price).toFixed(2)}</td>
                                            <td>${parseFloat(item.sub_total).toFixed(2)}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                itemsTableBody =
                                    `<tr><td colspan="5" class="text-center">No items found for this sell batch.</td></tr>`;
                            }
                            $('#itemSellBatchItemsTableBody').html(itemsTableBody);

                            // Show the modal
                            $('#itemSellPaymentsModal').modal('show');
                        }
                    },
                    error: function() {
                        toastr.error('Error fetching payment details.');
                    }
                });
            });

            // Handle return button click
            $('.return-button').click(function(e) {
                e.preventDefault();
                var sellBatchId = $(this).data('id');

                $.ajax({
                    url: '{{ url('account/inventory/item-sell') }}' + '/' + sellBatchId +
                        '/direct-return',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Item batch returned successfully!');
                            location.reload();
                        } else {
                            toastr.error('Failed to return item batch.');
                        }
                    },
                    error: function() {
                        toastr.error('Error returning item batch.');
                    }
                });
            });
        });
    </script>

    <style>
        #itemSellDetailsModal .modal-header,
        #itemSellPaymentsModal .modal-header {
            background-color: #007bff;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        #itemSellDetailsModal .modal-title,
        #itemSellPaymentsModal .modal-title {
            color: white;
            font-weight: bold;
        }

        #itemSellDetailsModal .close,
        #itemSellPaymentsModal .close {
            color: white;
            opacity: 1;
            font-size: 1.8rem;
        }

        #itemSellDetailsModal .modal-body,
        #itemSellPaymentsModal .modal-body {
            background-color: #f8f9fa;
            padding: 20px;
        }

        #itemSellDetailsModal .card,
        #itemSellPaymentsModal .card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        #itemSellDetailsModal .info-card,
        #itemSellPaymentsModal .payment-summary {
            border-radius: 8px;
            transition: transform 0.2s ease-in-out;
        }

        #itemSellDetailsModal .info-card:hover,
        #itemSellPaymentsModal .payment-summary:hover {
            transform: translateY(-2px);
        }

        #itemSellDetailsModal .table,
        #itemSellPaymentsModal .table {
            margin-bottom: 0;
            background-color: white;
            border-radius: 5px;
            overflow: hidden;
        }

        #itemSellDetailsModal .table td,
        #itemSellPaymentsModal .table td {
            vertical-align: middle;
            padding: 12px;
        }

        #itemSellDetailsModal .table strong,
        #itemSellPaymentsModal .table strong {
            color: #343a40;
            font-weight: 600;
        }

        #itemSellDetailsModal .modal-dialog,
        #itemSellPaymentsModal .modal-dialog {
            max-width: 900px;
        }

        #itemSellDetailsModal .modal-content,
        #itemSellPaymentsModal .modal-content {
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
    </style>
@endpush
