@extends('layouts.app')
@push('css')
    <style>
        /* ---- Modern refresh: cards, table, breadcrumb (Section 34) ----
                     BUG FIX: this page already used the sms-breadcrumb markup and
                     .white-box/#table_id markup, but never defined any CSS for them
                     anywhere in the Inventory module, so the banner and cards
                     rendered completely unstyled. Adding the same breadcrumb/card/
                     table CSS used across the rest of the app. ---- */
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
    @lang('inventory.item_sell_list')
@endsection
@section('mainContent')
    @php
        $canAddItemSell = Auth::user()->hasPermission('save-item-sell-data');
        $canViewItemSell = Auth::user()->hasPermission('view-item-sell');
        $canReprintItemSell = Auth::user()->hasPermission('inventory.item-sell-bill');
        $canReturnItemSell = Auth::user()->hasPermission('return-item-sell');
        $canSaveItemSellInvoice = Auth::user()->hasPermission('item-sell.invoic-save');
    @endphp
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('inventory.item_sell_list')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('inventory.item_sell_list')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">

            @if ($canAddItemSell)
                <div class="white-box mb-10 filter_card">
                    <div class="row">
                        <div class="col-12 mb-4 mb-sm-0">
                            <a href="{{ route('item-sell') }}" class="primary-btn small fix-gr-bg col-12 p-2">
                                <span class="ti-plus pr-2"></span>
                                @lang('inventory.new_item_sell')
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row mb-10">
                <div class="col-lg-12">
                    <div class="white-box">
                        <form method="GET" action="{{ route('item-sell-list') }}" class="row align-items-end mb-0">
                            <div class="col-md-6">
                                <div class="primary_input">
                                    <input type="date" class="primary_input_field form-control" id="from_date"
                                        name="from_date" value="{{ $fromDateInput }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="primary_input">
                                    <input type="date" class="primary_input_field form-control" id="to_date"
                                        name="to_date" value="{{ $toDateInput }}">
                                </div>
                            </div>
                            <div class="col-12 pt-10">
                                <button type="submit" class="primary-btn small fix-gr-bg col-12 p-2">
                                    <span class="ti-search pr-2"></span>Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('inventory.item_sell_list')
                                        
                                    </h3>
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
                                                    <th>Invoice NO</th>
                                                    <th>@lang('inventory.reference_no')</th>
                                                    <th>@lang('inventory.role_name')</th>
                                                    <th>@lang('inventory.buyer_name')</th>
                                                    <th>Create User</th>
                                                    <th>Reg No</th>
                                                    <th>Category</th>
                                                    <th>@lang('common.date')</th>
                                                    <th>Sell Price</th>
                                                    <th>@lang('inventory.grand_total')</th>
                                                    <th>@lang('inventory.total_quantity')</th>
                                                    <th>Payment Method</th>
                                                    <th>Bank Payment Amount</th>
                                                    <th>Bank Payment Date</th>
                                                    <th>Bank Payment Doc</th>
                                                    <th>Invoice Number</th>

                                                    <th>@lang('common.status')</th>
                                                    <th>@lang('common.action')</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @if (isset($allItemSellLists))
                                                    @foreach ($allItemSellLists as $key => $value)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $value->invoice_number }}</td>
                                                            <td>{{ $value->reference_no }}</td>
                                                            <td>{{ $value->role ? $value->role->role_name : 'Others' }}
                                                            </td>
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
                                                            <td> {{ $value->createdBy?->name ?? '-' }}</td>
                                                            <td>
                                                                @if ($value->student)
                                                                    {{ $value->student->admission_number }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($value->student)
                                                                    {{ $value->student?->studentCategory?->category_name ?? '-' }}
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
                                                            <td>{{ $value->payment_method ?? '-' }}</td>
                                                            <td>
                                                                {{ $value->bank_payment_amount ? number_format((float) $value->bank_payment_amount, 2, '.', '') : '-' }}
                                                            </td>
                                                            <td>
                                                                {{ $value->bank_payment_date ? \Carbon\Carbon::parse($value->bank_payment_date)->format('m/d/Y') : '-' }}
                                                            </td>
                                                            <td>
                                                                @if ($value->bank_payment_document)
                                                                    <a href="{{ asset('storage/' . $value->bank_payment_document) }}"
                                                                        target="_blank" class="text-primary">View
                                                                        Document</a>
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($value->invoice_number && $value->invoice_number != '')
                                                                    {{ $value->invoice_number }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
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
                                                                    @if ($canViewItemSell)
                                                                        <a class="dropdown-item view-item-sell"
                                                                            title="@lang('common.view') @lang('inventory.item_sell')"
                                                                            data-id="{{ $value->id }}"
                                                                            href="#">@lang('common.view')</a>
                                                                    @endif

                                                                    @if ($canReprintItemSell)
                                                                        <a class="dropdown-item item-sell-bill"
                                                                            title="Re-Print Invoice" target="_blank"
                                                                            href="{{ route('inventory.item-sell-bill', $value->id) }}">Re-Print</a>
                                                                    @endif


                                                                    @if ($value->status != 'R' && $canReturnItemSell)
                                                                        <a class="dropdown-item return-button"
                                                                            data-id="{{ $value->id }}"
                                                                            title="Return Item Sell"
                                                                            href="{{ route('return-item-sell', $value->id) }}">@lang('inventory.return')</a>
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
                                    <div class="col-md-12">
                                        <div class="info-card bg-light p-3 rounded mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ti-wallet text-primary mr-3" style="font-size: 24px;"></i>
                                                <div>
                                                    <h6 class="mb-1">Payment Method</h6>
                                                    <span class="text-muted" id="itemSellPaymentMethod"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="invoiceDetails" class="d-none">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="info-card bg-light p-3 rounded mb-3" id="invoiceViewBlock">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti-receipt text-warning mr-3"
                                                        style="font-size: 24px;"></i>
                                                    <div>
                                                        <h6 class="mb-1">Invoice Number</h6>
                                                        <span class="text-muted" id="itemSellInvoiceNumber"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="info-card bg-light p-3 rounded mb-3 d-none"
                                                id="invoiceAddBlock">
                                                <label class="mb-1">Add Invoice Number</label>

                                                <div class="d-flex gap-2">
                                                    <input type="text" id="newInvoiceNumber" class="form-control"
                                                        placeholder="Enter invoice number">
                                                    @if ($canSaveItemSellInvoice)
                                                        <button class="btn btn-primary btn-sm"
                                                            id="saveInvoiceNumberBtn" data-id="">Save</button>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div id="bankTransferDetails" class="d-none">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-card bg-light p-3 rounded mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti-money text-success mr-3"
                                                        style="font-size: 24px;"></i>
                                                    <div>
                                                        <h6 class="mb-1">Bank Payment Amount</h6>
                                                        <span class="text-muted"
                                                            id="itemSellBankPaymentAmount"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-card bg-light p-3 rounded mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti-calendar text-info mr-3"
                                                        style="font-size: 24px;"></i>
                                                    <div>
                                                        <h6 class="mb-1">Bank Payment Date</h6>
                                                        <span class="text-muted" id="itemSellBankPaymentDate"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="info-card bg-light p-3 rounded mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti-file text-secondary mr-3"
                                                        style="font-size: 24px;"></i>
                                                    <div>
                                                        <h6 class="mb-1">Bank Payment Document</h6>
                                                        <a href="#" id="itemSellBankPaymentDocument"
                                                            target="_blank" class="text-primary">View Document</a>
                                                    </div>
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




@include('components.partials.data_table_js')
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')
@push('script')
    <script>
        $(document).ready(function() {
            $('#table_id').DataTable({
                responsive: true,
dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 '" +
                "<'dt-toolbar-title'>" +
                "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-search'f><'dt-buttons-wrap'B>>" +
                ">" +
                "<'row'<'col-sm-12'tr>>" +
                "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>>",                buttons: [{
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

            // Show Item Details
            $(document).on('click', '.view-item-sell', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                // Fetch data via AJAX and populate modal fields
                $.ajax({
                    url: '{{ url('account/inventory/item-sell') }}/' + id,
                    method: 'GET',
                    success: function(data) {
                        console.log('dd', data);
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

                            // Handle payment method details
                            $('#itemSellPaymentMethod').text(data.itemSell.payment_method);
                            if (data.itemSell.payment_method === 'Invoice') {
                                $('#invoiceDetails').removeClass('d-none');
                                $('#bankTransferDetails').addClass('d-none');
                                if (data.itemSell.invoice_number && data.itemSell
                                    .invoice_number !== "") {
                                    // Show invoice display block
                                    $('#invoiceViewBlock').removeClass('d-none');
                                    $('#invoiceAddBlock').addClass('d-none');
                                    $('#itemSellInvoiceNumber').text(data.itemSell
                                        .invoice_number);

                                } else {
                                    // Hide view, show input for adding invoice
                                    $('#invoiceViewBlock').addClass('d-none');
                                    $('#invoiceAddBlock').removeClass('d-none');
                                    $('#saveInvoiceNumberBtn').attr('data-id', data.itemSell
                                        .id);
                                }
                            } else if (data.itemSell.payment_method === 'Bank Transfer') {
                                $('#invoiceDetails').addClass('d-none');
                                $('#bankTransferDetails').removeClass('d-none');
                                $('#itemSellBankPaymentAmount').text(parseFloat(data.itemSell
                                    .bank_payment_amount).toFixed(2));
                                $('#itemSellBankPaymentDate').text(data.itemSell
                                    .bank_payment_date ? moment(data.itemSell
                                        .bank_payment_date).format('MM/DD/YYYY') : '');
                                if (data.itemSell.bank_payment_document) {
                                    $('#itemSellBankPaymentDocument').attr('href',
                                        '{{ asset('storage') }}/' + data.itemSell
                                        .bank_payment_document);
                                    $('#itemSellBankPaymentDocument').parent().parent().parent()
                                        .parent().removeClass('d-none');
                                } else {
                                    $('#itemSellBankPaymentDocument').parent().parent().parent()
                                        .parent().addClass('d-none');
                                }
                            } else {
                                $('#invoiceDetails').addClass('d-none');
                                $('#bankTransferDetails').addClass('d-none');
                            }

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

            // add invoice number in invoice payment
            $(document).on('click', '#saveInvoiceNumberBtn', function() {
                let id = $(this).data('id');
                let invoiceNumber = $('#newInvoiceNumber').val();

                console.log('ddd', id, invoiceNumber);
                if (invoiceNumber === "") {
                    toastr.error("Invoice number cannot be empty.");
                    return;
                }

                $.ajax({
                    url: "{{ route('item-sell.invoic-save') }}",
                    method: "POST",
                    data: {
                        id: id,
                        invoice_number: invoiceNumber,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        toastr.success("Invoice number added successfully.");

                        // Update UI
                        $('#itemSellInvoiceNumber').text(invoiceNumber);
                        $('#invoiceViewBlock').removeClass('d-none');
                        $('#invoiceAddBlock').addClass('d-none');
                    },
                    error: function() {
                        toastr.error("Failed to save invoice number.");
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
