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
                                                        <x-drop-down>
                                                            <a class="dropdown-item view-item-receive" href="#"
                                                                data-id="{{ $itemReceive->id }}">
                                                                <i class="ti-eye"></i> @lang('common.view')
                                                            </a>
                                                            <a class="dropdown-item edit-item-receive" href="#"
                                                                data-id="{{ $itemReceive->id }}">
                                                                <i class="ti-pencil"></i> @lang('common.edit')
                                                            </a>
                                                            <a class="dropdown-item delete-item-receive" href="#"
                                                                data-id="{{ $itemReceive->id }}">
                                                                <i class="ti-trash"></i> @lang('common.delete')
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('inventory.view_item_receive')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <p><strong>@lang('inventory.reference_no'):</strong> <span id="view_reference_no"></span></p>
                            <p><strong>@lang('inventory.supplier_company_name'):</strong> <span id="view_supplier_name"></span></p>
                            <p><strong>@lang('inventory.store_name'):</strong> <span id="view_store_name"></span></p>
                            <p><strong>@lang('inventory.receive_date'):</strong> <span id="view_receive_date"></span></p>
                            <p><strong>@lang('inventory.item_name'):</strong> <span id="view_item_name"></span></p>
                            <p><strong>@lang('inventory.unit_price'):</strong> <span id="view_unit_price"></span></p>
                            <p><strong>@lang('inventory.quantity'):</strong> <span id="view_quantity"></span></p>
                            <p><strong>@lang('inventory.sub_total'):</strong> <span id="view_sub_total"></span></p>
                            <p><strong>@lang('inventory.paid'):</strong> <span id="view_total_paid"></span></p>
                            <p><strong>@lang('inventory.balance'):</strong> <span id="view_total_due"></span></p>
                            <p><strong>@lang('inventory.payment_method'):</strong> <span id="view_payment_method"></span></p>
                            <p><strong>@lang('common.description'):</strong> <span id="view_description"></span></p>
                        </div>
                    </div>
                    <div class="mt-40 d-flex justify-content-end">
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
    <script>
        (function($) {
            $(document).ready(function() {

                //  View Item Receive
                $(document).on('click', '.view-item-receive', function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');

                    $.ajax({
                        url: '/account/inventory/item-receive/' + id + '/edit', // ✅ fixed URL
                        type: 'GET',
                        success: function(response) {
                            let itemReceive = response.itemReceive;

                            $('#view_reference_no').text(itemReceive.reference_no);
                            $('#view_supplier_name').text(itemReceive.supplier
                                ?.company_name ?? 'N/A');
                            $('#view_store_name').text(itemReceive.branch?.name ?? 'N/A');
                            $('#view_receive_date').text(moment(itemReceive.receive_date)
                                .format('MM/DD/YYYY'));
                            $('#view_payment_method').text(itemReceive.payment_method);
                            $('#view_item_name').text(itemReceive.item?.item_name ?? 'N/A');
                            $('#view_unit_price').text(itemReceive.unit_price);
                            $('#view_quantity').text(itemReceive.quantity);
                            $('#view_sub_total').text(itemReceive.sub_total);
                            $('#view_total_paid').text(itemReceive.total_paid);
                            $('#view_total_due').text(itemReceive.sub_total - itemReceive
                                .total_paid);
                            $('#view_description').text(itemReceive.description);

                            $('#viewItemReceiveModal').modal('show');
                        },
                        error: function() {
                            toastr.error("Failed to fetch item receive details.");
                        }
                    });
                });

                //  Edit Item Receive
                $(document).on('click', '.edit-item-receive', function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');

                    $('#editItemReceiveForm').attr('action', '/account/inventory/item-receive/' + id);

                    $.ajax({
                        url: '/account/inventory/item-receive/' + id + '/edit', // ✅ fixed URL
                        type: 'GET',
                        success: function(response) {
                            let itemReceive = response.itemReceive;

                            $('#edit_item_receive_id').val(id);
                            $('#edit_payment_method').val(itemReceive.payment_method);
                            $('#edit_reference_no').val(itemReceive.reference_no);
                            $('#edit_receive_date').val(moment(itemReceive.receive_date)
                                .format('MM/DD/YYYY'));
                            $('#edit_unit_price').val(itemReceive.unit_price);
                            $('#edit_quantity').val(itemReceive.quantity);
                            $('#edit_description').val(itemReceive.description);

                            // Populate suppliers dropdown
                            let supplierOptions = '<option data-display="@lang('inventory.select_supplier') *" value="">@lang('common.select')</option>';
                            response.suppliers.forEach(supplier => {
                                supplierOptions += `<option value="${supplier.id}" ${itemReceive.supplier_id == supplier.id ? 'selected' : ''}>${supplier.company_name}</option>`;
                            });
                            $('#edit_supplier_id').html(supplierOptions);

                            // Populate branches/stores dropdown
                            let branchOptions = '<option data-display="@lang('inventory.select_store') *" value="">@lang('common.select')</option>';
                            response.branches.forEach(branch => {
                                branchOptions += `<option value="${branch.id}" ${itemReceive.store_id == branch.id ? 'selected' : ''}>${branch.name}</option>`;
                            });
                            $('#edit_store_id').html(branchOptions);

                            // Populate items dropdown
                            let itemOptions = '<option data-display="@lang('inventory.select_item') *" value="">@lang('common.select')</option>';
                            response.items.forEach(item => {
                                itemOptions += `<option value="${item.id}" ${itemReceive.item_id == item.id ? 'selected' : ''}>${item.item_name}</option>`;
                            });
                            $('#edit_item_id').html(itemOptions);

                            $('.primary_select').niceSelect('update');
                            $('#editItemReceiveModal').modal('show');
                        },
                        error: function() {
                            toastr.error("Failed to load item for editing.");
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
                            toastr.success(response.message ?? "Updated successfully!");
                            location.reload();
                        },
                        error: function(xhr) {
                            toastr.error("Update failed: " + xhr.responseText);
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
