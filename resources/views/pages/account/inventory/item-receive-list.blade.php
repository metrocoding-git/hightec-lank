@extends('layouts.app')

@push('css')
    <style>
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

        .white-box {
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
        }

        .white-box .main-title h3,
        .white-box .main-title h4,
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

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            color: #fff;
        }
    </style>
@endpush
@section('title')
    @lang('inventory.item_receive_list')
@endsection

@section('mainContent')
    @php
        // Button-level permissions (Section 34).
        $canAddItemReceive = Auth::user()->hasPermission('save-item-receive-data');
        $canViewItemReceive = Auth::user()->hasPermission('item-receive.view');
        $canDeleteItemReceive = Auth::user()->hasPermission('item-receive.destroy');
        $canSaveItemReceiveInvoice = Auth::user()->hasPermission('item-receive.invoic-save');
    @endphp

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('inventory.item_receive_list')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('inventory.item_receive_list')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            @if ($canAddItemReceive)
                <div class="white-box mb-10 filter_card">
                    <div class="row">
                        <div class="col-12 mb-4 mb-sm-0">
                            <a href="{{ route('item-receive') }}" class="primary-btn small fix-gr-bg col-12 p-2">
                                <span class="ti-plus pr-2"></span>
                                @lang('inventory.new_item_receive')
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
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
                                                {{-- <th>@lang('inventory.transaction_id')</th> --}}
                                                <th>@lang('inventory.reference_no')</th>
                                                <th>Item name</th>
                                                <th>Item Category</th>

                                                <th>@lang('inventory.supplier_company_name')</th>
                                                <th>@lang('inventory.unit_price')</th>
                                                <th>@lang('inventory.total_quantity')</th>
                                                <th>@lang('inventory.grand_total')</th>
                                                <th>Receive Date</th>
                                                <th>Payment Method</th>
                                                <th>Bank Payment Amount</th>
                                                <th>Bank Payment Date</th>
                                                <th>Bank Payment Doc</th>
                                                <th>Invoice Number</th>
                                                <td>Description</td>
                                                <td>Created By</td>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($itemReceives as $key => $itemReceive)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    {{-- <td>{{ $itemReceive->transaction_id }}</td> --}}
                                                    <td>{{ $itemReceive->reference_no }}</td>
                                                    <td>
                                                        {{ $itemReceive->item->item_name ?? '-' }}
                                                    </td>
                                                    <td>
                                                        {{ $itemReceive->item->category->name ?? '-' }}
                                                    </td>
                                                    <td>{{ $itemReceive->supplier->company_name }}</td>
                                                    <td>{{ number_format($itemReceive->unit_price, 2) }}</td>
                                                    <td>{{ $itemReceive->quantity }}</td>
                                                    <td>{{ number_format($itemReceive->sub_total, 2) }}</td>
                                                    <td>
                                                        {{ $itemReceive->receive_date ? \Carbon\Carbon::parse($itemReceive->receive_date)->format('m/d/Y') : '' }}
                                                    </td>
                                                    <td>
                                                        {{ $itemReceive->payment_method }}
                                                    </td>

                                                    <td>
                                                        {{ $itemReceive->bank_payment_amount ? number_format((float) $itemReceive->bank_payment_amount, 2, '.', '') : '-' }}
                                                    </td>
                                                    <td>
                                                        {{ $itemReceive->bank_payment_date ? \Carbon\Carbon::parse($itemReceive->bank_payment_date)->format('m/d/Y') : '-' }}
                                                    </td>
                                                    <td>
                                                        @if ($itemReceive->bank_payment_document)
                                                            <a href="{{ asset('storage/' . $itemReceive->bank_payment_document) }}"
                                                                target="_blank" class="text-primary">View
                                                                Document</a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($itemReceive->invoice_number != '')
                                                            {{ $itemReceive->invoice_number }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <td>{{ $itemReceive->description ?? '-' }}</td>

                                                    <td>{{ $itemReceive->creator->name ?? '-' }}</td>

                                                    <td>
                                                        <x-drop-down>
                                                            @if ($canViewItemReceive)
                                                                <a class="dropdown-item view-item-receive" href="#"
                                                                    data-id="{{ $itemReceive->transaction_id }}">
                                                                    @lang('common.view')
                                                                </a>
                                                            @endif
                                                            {{-- <a class="dropdown-item edit-item-receive" href="#"
                                                                data-id="{{ $itemReceive->transaction_id }}">
                                                                 @lang('common.edit')
                                                            </a> --}}
                                                            @if ($canDeleteItemReceive)
                                                                <a class="dropdown-item delete-item-receive" href="#"
                                                                    data-id="{{ $itemReceive->transaction_id }}">
                                                                    @lang('common.delete')
                                                                </a>
                                                            @endif
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
            </div>
        </div>
    </section>

    {{-- View Modal --}}
    <div class="modal fade admin-query" id="viewItemReceiveModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">View Item Receive Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- Items Table Section -->
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <h5 class="text-primary mb-3">
                                <i class="ti-package"></i> Items Details
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped text-center">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th class="text-center">Item Name</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">Unit Price</th>
                                            <th class="text-center">Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view_items_table_body">
                                        <!-- Items will be populated here -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold">
                                            <td colspan="3" class="text-center">Total</td>
                                            <td id="view_total_quantity" class="text-center">0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Details Section -->
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <h5 class="text-primary mb-3">
                                <i class="ti-receipt"></i> Transaction Details
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Transaction:</strong></td>
                                            <td><span id="view_transaction_id" class="badge badge-primary"></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('inventory.reference_no'):</strong></td>
                                            <td><span id="view_reference_no" class="badge badge-info"></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('inventory.receive_date'):</strong></td>
                                            <td><span id="view_receive_date"></span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>@lang('inventory.payment_method'):</strong></td>
                                            <td><span id="view_payment_method" class="badge badge-warning"></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('inventory.paid'):</strong></td>
                                            <td><span id="view_total_paid" class="text-success font-weight-bold"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('inventory.grand_total'):</strong></td>
                                            <td><span id="view_grand_total" class="text-info font-weight-bold"></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Specific Details -->
                    <div class="row mb-4" id="payment_details_section">
                        <div class="col-lg-12">
                            <h5 class="text-primary mb-3">
                                <i class="ti-money"></i> Payment Details
                            </h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-sm table-borderless">
                                        <tbody id="payment_method_specific_details">
                                            <!-- Payment method specific details will be populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier & Store Information Section -->
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <h5 class="text-primary mb-3">
                                <i class="ti-info-circle"></i> Store Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-section p-3"
                                        style="background-color: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                                        <h6 class="mb-2 text-warning">
                                            <i class="ti-truck"></i> Supplier Information
                                        </h6>
                                        <p class="mb-1"><strong>@lang('inventory.supplier_company_name'):</strong> <span
                                                id="view_supplier_name"></span></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-section p-3"
                                        style="background-color: #d4edda; border-radius: 8px; border-left: 4px solid #28a745;">
                                        <h6 class="mb-2 text-success">
                                            <i class="ti-home"></i> Store Information
                                        </h6>
                                        <p class="mb-1"><strong>@lang('inventory.store_name'):</strong> <span
                                                id="view_store_name"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description Section -->
                    <div class="row">
                        <div class="col-lg-12">
                            <h5 class="text-primary mb-3">
                                <i class="ti-clipboard"></i> @lang('common.description')
                            </h5>
                            <div class="description-box p-3"
                                style="background-color: #f8f9fa; border-radius: 8px; min-height: 60px;">
                                <p id="view_description" class="mb-0 text-muted">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade admin-query" id="editItemReceiveModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Item Receive</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editItemReceiveForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit_item_receive_id">

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="form-group mb-20">
                                    <label for="edit_reference_no">@lang('inventory.reference_no') <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="reference_no"
                                        id="edit_reference_no" autocomplete="off" readonly>
                                </div>

                                <div class="form-group mb-20">
                                    <label for="edit_supplier_id">@lang('inventory.supplier_company_name') <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="supplier_id" id="edit_supplier_id">
                                        <option value="">@lang('inventory.select_supplier')</option>
                                        {{-- Suppliers will be loaded via AJAX or passed from controller --}}
                                    </select>
                                </div>

                                <div class="form-group mb-20">
                                    <label for="edit_store_id">@lang('inventory.store_name') <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="store_id" id="edit_store_id">
                                        <option value="">@lang('inventory.select_store')</option>
                                        {{-- Branches/Stores will be loaded via AJAX or passed from controller --}}
                                    </select>
                                </div>

                                <div class="form-group mb-20">
                                    <label for="edit_receive_date">@lang('inventory.receive_date') <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="receive_date"
                                        id="edit_receive_date" autocomplete="off">
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="form-group mb-20">
                                    <label for="edit_item_id">@lang('inventory.item_name') <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="item_id" id="edit_item_id">
                                        <option value="">@lang('inventory.select_item')</option>
                                        {{-- Items will be loaded via AJAX or passed from controller --}}
                                    </select>
                                </div>

                                <div class="form-group mb-20">
                                    <label for="edit_unit_price">@lang('inventory.unit_price') <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rs</span>
                                        </div>
                                        <input class="form-control" type="number" step="0.01" name="unit_price"
                                            id="edit_unit_price" autocomplete="off">
                                    </div>
                                </div>

                                <div class="form-group mb-20">
                                    <label for="edit_quantity">@lang('inventory.quantity') <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="number" name="quantity" id="edit_quantity"
                                        autocomplete="off">
                                </div>

                                <div class="form-group mb-20">
                                    <label for="edit_payment_method">@lang('inventory.payment_method') <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="payment_method" id="edit_payment_method">
                                        <option value="">payment method</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank">Cheque</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Full-width textarea -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-20">
                                    <label for="edit_description">@lang('common.description')</label>
                                    <textarea class="form-control" rows="3" name="description" id="edit_description"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-40 d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">@lang('common.cancel')</button>
                            <button class="btn btn-primary" type="submit">@lang('common.update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    {{-- Delete Modal --}}
    <div class="modal fade admin-query" id="deleteItemReceiveModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 style="color: seashell">Delete Item Receive</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-center">@lang('common.are_you_sure_to_delete')</p>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                        <form id="deleteItemReceiveForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="primary-btn fix-gr-bg" type="submit">@lang('common.delete')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('components.partials.data_table_js')
@include('components.partials.server_side_datatable')
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
    <script>
        (function($) {
            $(document).ready(function() {

                //  View Item Receive
                $(document).on('click', '.view-item-receive', function(e) {
                    e.preventDefault();
                    let transactionId = $(this).data('id');

                    $.ajax({
                        url: '/account/inventory/item-receive/' + transactionId + '/view',
                        type: 'GET',
                        success: function(response) {
                            if (response.error) {
                                toastr.error(response.error);
                                return;
                            }

                            let transaction = response.transaction;
                            let items = response.items;
                            let totals = response.totals;

                            // Transaction Details Section
                            $('#view_transaction_id').text(transaction.transaction_id ||
                                'N/A');
                            $('#view_reference_no').text(transaction.reference_no || 'N/A');
                            $('#view_receive_date').text(moment(transaction.receive_date)
                                .format('MM/DD/YYYY'));
                            $('#view_payment_method').text(transaction.payment_method ||
                                'N/A');
                            $('#view_total_paid').text('Rs ' + parseFloat(totals
                                .total_paid || 0).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                            $('#view_grand_total').text('Rs ' + parseFloat(totals
                                .total_sub_total || 0).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));

                            // Supplier & Store Information
                            $('#view_supplier_name').text(transaction.supplier
                                ?.company_name ?? 'N/A');
                            $('#view_store_name').text(transaction.branch?.name ?? 'N/A');

                            // Description
                            $('#view_description').text(transaction.description ||
                                'No description available');

                            // Items Table
                            let itemsHtml = '';
                            items.forEach(item => {
                                itemsHtml += `<tr>
                                    <td>${item.item?.item_name ?? 'N/A'}</td>
                                    <td>${item.quantity}</td>
                                    <td>Rs ${parseFloat(item.unit_price || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td>Rs ${parseFloat(item.sub_total || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                </tr>`;
                            });
                            $('#view_items_table_body').html(itemsHtml);
                            $('#view_total_quantity').text(totals.total_quantity || '0');

                            // Payment Method Specific Details
                            let paymentSpecificHtml = '';
                            if (transaction.payment_method === 'Invoice') {
                                if (transaction.invoice_number) {
                                    paymentSpecificHtml += `
                                            <tr>
                                                <td><strong>Invoice Number:</strong></td>
                                                <td><strong >${transaction.invoice_number}</strong></td>
                                            </tr>
                                        `;
                                } else {
                                    paymentSpecificHtml += `
                                            <tr>
                                                <td><strong>Invoice Number:</strong></td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="invoiceNumberInput" placeholder="Enter invoice number">
                                                        <div class="input-group-append">
                                                            @if ($canSaveItemReceiveInvoice)
                                                                <button class="btn btn-primary btn-sm" id="saveInvoiceBtn" data-transaction-id="${transaction.id}">
                                                                    Save
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div id="invoiceSaveMsg" class="mt-2 text-success" style="display:none;">Saved successfully!</div>
                                                </td>
                                            </tr>
                                        `;
                                }
                            } else if (transaction.payment_method === 'Bank Transfer') {
                                paymentSpecificHtml += `
                                    <tr>
                                        <td><strong>Bank Payment Amount:</strong></td>
                                        <td>Rs ${parseFloat(transaction.bank_payment_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Bank Payment Date:</strong></td>
                                        <td>${transaction.bank_payment_date ? moment(transaction.bank_payment_date).format('MM/DD/YYYY') : 'N/A'}</td>
                                    </tr>
                                `;
                                if (transaction.bank_payment_document) {
                                    paymentSpecificHtml += `
                                        <tr>
                                            <td><strong>Bank Payment Document:</strong></td>
                                            <td><a href="/storage/${transaction.bank_payment_document}" target="_blank" class="primary-btn small fix-gr-bg">View Document</a></td>
                                        </tr>
                                    `;
                                } else {
                                    paymentSpecificHtml += `
                                        <tr>
                                            <td><strong>Bank Payment Document:</strong></td>
                                            <td>N/A</td>
                                        </tr>
                                    `;
                                }
                            }
                            $('#payment_method_specific_details').html(paymentSpecificHtml);

                            $('#viewItemReceiveModal').modal('show');
                        },
                        error: function(xhr) {
                            console.error('Error response:', xhr.responseText);
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error(
                                    "Failed to fetch item receive details. Please check the console for more information."
                                );
                            }
                        }
                    });
                });

                //  Edit Item Receive
                $(document).on('click', '.edit-item-receive', function(e) {
                    e.preventDefault();
                    let transactionId = $(this).data('id');

                    $('#editItemReceiveForm').attr('action', '/account/inventory/item-receive/' +
                        transactionId);

                    $.ajax({
                        url: '/account/inventory/item-receive/' + transactionId + '/edit',
                        type: 'GET',
                        success: function(response) {
                            if (response.error) {
                                toastr.error(response.error);
                                return;
                            }

                            let transaction = response.transaction;
                            let itemReceives = response.itemReceives;

                            // Use the first item for basic form population
                            let firstItem = itemReceives[0];

                            $('#edit_item_receive_id').val(transactionId);
                            $('#edit_payment_method').val(transaction.payment_method);
                            $('#edit_reference_no').val(transaction.reference_no);
                            $('#edit_receive_date').val(moment(transaction.receive_date)
                                .format('MM/DD/YYYY'));
                            $('#edit_description').val(transaction.description);

                            // Populate suppliers dropdown
                            let supplierOptions =
                                '<option data-display="@lang('inventory.select_supplier') *" value="">@lang('common.select')</option>';
                            response.suppliers.forEach(supplier => {
                                supplierOptions +=
                                    `<option value="${supplier.id}" ${transaction.supplier_id == supplier.id ? 'selected' : ''}>${supplier.company_name}</option>`;
                            });
                            $('#edit_supplier_id').html(supplierOptions);

                            // Populate branches/stores dropdown
                            let branchOptions =
                                '<option data-display="@lang('inventory.select_store') *" value="">@lang('common.select')</option>';
                            response.branches.forEach(branch => {
                                branchOptions +=
                                    `<option value="${branch.id}" ${transaction.store_id == branch.id ? 'selected' : ''}>${branch.name}</option>`;
                            });
                            $('#edit_store_id').html(branchOptions);

                            // Populate items dropdown (for first item)
                            let itemOptions =
                                '<option data-display="@lang('inventory.select_item') *" value="">@lang('common.select')</option>';
                            response.items.forEach(item => {
                                itemOptions +=
                                    `<option value="${item.id}" ${firstItem.item_id == item.id ? 'selected' : ''}>${item.item_name}</option>`;
                            });
                            $('#edit_item_id').html(itemOptions);

                            // Set first item's details
                            $('#edit_unit_price').val(firstItem.unit_price);
                            $('#edit_quantity').val(firstItem.quantity);

                            $('.primary_select').niceSelect('update');
                            $('#editItemReceiveModal').modal('show');
                        },
                        error: function(xhr) {
                            console.error('Error response:', xhr.responseText);
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error("Failed to load transaction for editing.");
                            }
                        }
                    });
                });

                //  Submit Edit via AJAX
                $('#editItemReceiveForm').on('submit', function(e) {
                    e.preventDefault();
                    let url = $(this).attr('action');
                    let formData = $(this).serialize();

                    $.ajax({
                        url: url,
                        type: 'POST', // Laravel will handle PUT via _method
                        data: formData,
                        success: function(response) {
                            $('#editItemReceiveModal').modal('hide');
                            if (response.status) {
                                toastr.success(response.message);
                                location.reload();
                            } else {
                                toastr.warning(response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error response:', xhr.responseText);
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error);
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                toastr.error(xhr.responseJSON.message);
                            } else {
                                toastr.error(
                                    "Update failed. Please check the console for more information."
                                );
                            }
                        }
                    });
                });

                //  Delete Item Receive
                $(document).on('click', '.delete-item-receive', function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');
                    $('#deleteItemReceiveForm').attr('action', '/account/inventory/item-receive/' + id);
                    $('#deleteItemReceiveModal').modal('show');
                });

                //  Submit Delete via AJAX
                $('#deleteItemReceiveForm').on('submit', function(e) {
                    e.preventDefault();
                    let url = $(this).attr('action');
                    let formData = $(this).serialize();

                    $.ajax({
                        url: url,
                        type: 'POST', // Laravel will handle DELETE via _method
                        data: formData,
                        success: function(response) {
                            $('#deleteItemReceiveModal').modal('hide');
                            toastr.success(response.message ?? "Deleted successfully!");
                            location.reload();
                        },
                        error: function() {
                            toastr.error("Delete failed.");
                        }
                    });
                });

                // save invoice invoice_number
                $(document).on('click', '#saveInvoiceBtn', function() {
                    const invoiceNumber = $('#invoiceNumberInput').val().trim();
                    const transactionId = $(this).data('transaction-id');

                    if (!invoiceNumber) {
                        toastr.warning('Please enter an invoice number');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('item-receive.invoic-save') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: transactionId,
                            invoice_number: invoiceNumber
                        },
                        success: function(response) {
                            toastr.success('Invoice number saved successfully!');
                            $('#invoiceNumberInput').prop('disabled', true);
                            $('#saveInvoiceBtn').prop('disabled', true).text('Saved');
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message ||
                                'Error saving invoice number');
                        }
                    });
                });



            });
        })(jQuery);
    </script>
@endpush
