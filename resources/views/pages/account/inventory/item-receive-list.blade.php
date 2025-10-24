@extends('layouts.app')

@section('title')
    @lang('inventory.item_receive_list')
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
                                                {{-- <th>@lang('inventory.transaction_id')</th> --}}
                                                <th>@lang('inventory.reference_no')</th>
                                                <th>@lang('inventory.supplier_company_name')</th>
                                                <th>@lang('common.date')</th>
                                                <th>@lang('inventory.total_quantity')</th>
                                                <th>@lang('inventory.grand_total')</th>
                                                {{-- <th>@lang('inventory.paid')</th> --}}
                                                {{-- <th>@lang('inventory.balance') (Rs)</th> --}}
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($itemReceives as $key => $itemReceive)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    {{-- <td>{{ $itemReceive->transaction_id }}</td> --}}
                                                    <td>{{ $itemReceive->reference_no }}</td>
                                                    <td>{{ optional($itemReceive->supplier)->company_name }}</td>
                                                    <td>{{ $itemReceive->receive_date }}</td>
                                                    <td>{{ $itemReceive->total_quantity }}</td>
                                                    <td>{{ number_format($itemReceive->total_sub_total, 2) }}</td>
                                                    {{-- <td>{{ number_format($itemReceive->total_paid, 2) }}</td> --}}
                                                    {{-- <td>{{ number_format($itemReceive->total_balance, 2) }}</td> --}}
                                                    <td>
                                                        <x-drop-down>
                                                            <a class="dropdown-item view-item-receive" href="#"
                                                                data-id="{{ $itemReceive->transaction_id }}">
                                                                 @lang('common.view')
                                                            </a>
                                                            <a class="dropdown-item edit-item-receive" href="#"
                                                                data-id="{{ $itemReceive->transaction_id }}">
                                                                 @lang('common.edit')
                                                            </a>
                                                            <a class="dropdown-item delete-item-receive" href="#"
                                                                data-id="{{ $itemReceive->transaction_id }}">
                                                                 @lang('common.delete')
                                                            </a>
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
                                            <td><span id="view_total_paid" class="text-success font-weight-bold"></span></td>
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

                    <!-- Supplier & Store Information Section -->
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <h5 class="text-primary mb-3">
                                <i class="ti-info-circle"></i> Store Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-section p-3" style="background-color: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                                        <h6 class="mb-2 text-warning">
                                            <i class="ti-truck"></i> Supplier Information
                                        </h6>
                                        <p class="mb-1"><strong>@lang('inventory.supplier_company_name'):</strong> <span id="view_supplier_name"></span></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-section p-3" style="background-color: #d4edda; border-radius: 8px; border-left: 4px solid #28a745;">
                                        <h6 class="mb-2 text-success">
                                            <i class="ti-home"></i> Store Information
                                        </h6>
                                        <p class="mb-1"><strong>@lang('inventory.store_name'):</strong> <span id="view_store_name"></span></p>
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
                            <div class="description-box p-3" style="background-color: #f8f9fa; border-radius: 8px; min-height: 60px;">
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
                                <label for="edit_reference_no">@lang('inventory.reference_no') <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="reference_no" id="edit_reference_no" autocomplete="off" readonly>
                            </div>

                            <div class="form-group mb-20">
                                <label for="edit_supplier_id">@lang('inventory.supplier_company_name') <span class="text-danger">*</span></label>
                                <select class="form-control" name="supplier_id" id="edit_supplier_id">
                                    <option value="">@lang('inventory.select_supplier')</option>
                                    {{-- Suppliers will be loaded via AJAX or passed from controller --}}
                                </select>
                            </div>

                            <div class="form-group mb-20">
                                <label for="edit_store_id">@lang('inventory.store_name') <span class="text-danger">*</span></label>
                                <select class="form-control" name="store_id" id="edit_store_id">
                                    <option value="">@lang('inventory.select_store')</option>
                                    {{-- Branches/Stores will be loaded via AJAX or passed from controller --}}
                                </select>
                            </div>

                            <div class="form-group mb-20">
                                <label for="edit_receive_date">@lang('inventory.receive_date') <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="receive_date" id="edit_receive_date" autocomplete="off">
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="form-group mb-20">
                                <label for="edit_item_id">@lang('inventory.item_name') <span class="text-danger">*</span></label>
                                <select class="form-control" name="item_id" id="edit_item_id">
                                    <option value="">@lang('inventory.select_item')</option>
                                    {{-- Items will be loaded via AJAX or passed from controller --}}
                                </select>
                            </div>

                            <div class="form-group mb-20">
                                <label for="edit_unit_price">@lang('inventory.unit_price') <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rs</span>
                                    </div>
                                    <input class="form-control" type="number" step="0.01" name="unit_price" id="edit_unit_price" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group mb-20">
                                <label for="edit_quantity">@lang('inventory.quantity') <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="quantity" id="edit_quantity" autocomplete="off">
                            </div>

                            <div class="form-group mb-20">
                                <label for="edit_payment_method">@lang('inventory.payment_method') <span class="text-danger">*</span></label>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('common.cancel')</button>
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
                    <h5>@lang('common.delete_item_receive')</h5>
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
                            $('#view_transaction_id').text(transaction.transaction_id || 'N/A');
                            $('#view_reference_no').text(transaction.reference_no || 'N/A');
                            $('#view_receive_date').text(moment(transaction.receive_date).format('MM/DD/YYYY'));
                            $('#view_payment_method').text(transaction.payment_method || 'N/A');
                            $('#view_total_paid').text('Rs ' + parseFloat(totals.total_paid || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#view_grand_total').text('Rs ' + parseFloat(totals.total_sub_total || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

                            // Supplier & Store Information
                            $('#view_supplier_name').text(transaction.supplier?.company_name ?? 'N/A');
                            $('#view_store_name').text(transaction.branch?.name ?? 'N/A');

                            // Description
                            $('#view_description').text(transaction.description || 'No description available');

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

                            $('#viewItemReceiveModal').modal('show');
                        },
                        error: function(xhr) {
                            console.error('Error response:', xhr.responseText);
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error("Failed to fetch item receive details. Please check the console for more information.");
                            }
                        }
                    });
                });

                //  Edit Item Receive
                $(document).on('click', '.edit-item-receive', function(e) {
                    e.preventDefault();
                    let transactionId = $(this).data('id');

                    $('#editItemReceiveForm').attr('action', '/account/inventory/item-receive/' + transactionId);

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
                            $('#edit_receive_date').val(moment(transaction.receive_date).format('MM/DD/YYYY'));
                            $('#edit_description').val(transaction.description);

                            // Populate suppliers dropdown
                            let supplierOptions = '<option data-display="@lang('inventory.select_supplier') *" value="">@lang('common.select')</option>';
                            response.suppliers.forEach(supplier => {
                                supplierOptions += `<option value="${supplier.id}" ${transaction.supplier_id == supplier.id ? 'selected' : ''}>${supplier.company_name}</option>`;
                            });
                            $('#edit_supplier_id').html(supplierOptions);

                            // Populate branches/stores dropdown
                            let branchOptions = '<option data-display="@lang('inventory.select_store') *" value="">@lang('common.select')</option>';
                            response.branches.forEach(branch => {
                                branchOptions += `<option value="${branch.id}" ${transaction.store_id == branch.id ? 'selected' : ''}>${branch.name}</option>`;
                            });
                            $('#edit_store_id').html(branchOptions);

                            // Populate items dropdown (for first item)
                            let itemOptions = '<option data-display="@lang('inventory.select_item') *" value="">@lang('common.select')</option>';
                            response.items.forEach(item => {
                                itemOptions += `<option value="${item.id}" ${firstItem.item_id == item.id ? 'selected' : ''}>${item.item_name}</option>`;
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
                                toastr.error("Update failed. Please check the console for more information.");
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

            });
        })(jQuery);
    </script>
@endpush
