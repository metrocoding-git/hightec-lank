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
                                                    <label class="primary_input_label" for="">Item Price
                                                        <span class="text-danger"> *</span> </label>
                                                    <input
                                                        class="primary_input_field form-control{{ $errors->has('item_price') ? ' is-invalid' : '' }}"
                                                        type="text" name="item_price" autocomplete="off"
                                                        value="{{ isset($editData) ? $editData->item_price : '' }}">


                                                    @if ($errors->has('item_price'))
                                                        <span class="text-danger">
                                                            {{ $errors->first('item_price') }}
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
                                                <th>#</th>
                                                <th>Item Name</th>
                                                <th>Category</th>
                                                <th>Item Price</th>
                                                <th>Description</th>
                                                <th>Available Stock</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $key => $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->item_name }}</td>
                                                    <td>{{ $item->category->name ?? '' }}</td>
                                                    <td>{{ $item->item_price ?? 'N/A' }}</td>
                                                    <td>{{ $item->description }}</td>
                                                    <td>{{ $item->current_balance ?? 0 }}</td>
                                                    <td>
                                                        <x-drop-down>
                                                            <a class="dropdown-item view-item-btn" data-toggle="modal"
                                                                data-target="#viewItemModal"
                                                                data-item-id="{{ $item->id }}" href="#">
                                                                @lang('common.view')
                                                            </a>
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
                                                                    <form action="{{ route('item-delete', $item->id) }}"
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

            <!-- View Item Modal -->
            <div class="modal fade admin-query" id="viewItemModal">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-purple text-white">
                            <h4 class="modal-title">
                                <i class="ti-eye mr-2"></i>Item Details & History
                            </h4>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body p-5">
                            <!-- Item Information Section -->
                            <div class="row mb-4">
                                <div class="col-lg-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0 text-primary">
                                                <i class="ti-info-alt mr-2"></i>Item Information
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold text-muted">Item Name:</label>
                                                        <p class="h5 text-dark" id="modal-item-name">-</p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold text-muted">Category:</label>
                                                        <p class="h5" id="modal-item-category">-</p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold text-muted">Item Price:</label>
                                                        <p class="h5 text-success" id="modal-item-price">-</p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold text-muted">Description:</label>
                                                        <p class="text-dark" id="modal-item-description">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Inventory History Section -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0 text-primary">
                                                <i class="ti-list mr-2"></i>Inventory History
                                            </h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0">
                                                    <thead class="">
                                                        <tr>
                                                            <th class="border-0">Transaction Type</th>
                                                            <th class="border-0">Quantity</th>
                                                            <th class="border-0">Current Balance</th>
                                                            <th class="border-0">Transaction Date</th>
                                                            <th class="border-0">Description</th>
                                                            <th class="border-0">Reference No</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="inventory-history-body">
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted">
                                                                <i class="ti-reload mr-2"></i>Loading inventory history...
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="ti-close mr-1"></i>Close
                            </button>
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
@include('components.partials.data_table_css')
@include('components.partials.data_table_js')
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

        // Load item details and inventory history when modal is shown
        $('#viewItemModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var itemId = button.data('item-id');
            var modal = $(this);

            // Clear previous content
            modal.find('#modal-item-name').text('Loading...');
            modal.find('#modal-item-category').text('Loading...');
            modal.find('#modal-item-price').text('Loading...');
            modal.find('#modal-item-description').text('Loading...');
            modal.find('#inventory-history-body').html(
                '<tr><td colspan="6" class="text-center text-muted"><i class="ti-reload mr-2"></i>Loading inventory history...</td></tr>'
                );

            // Fetch item details and history
            $.ajax({
                url: '{{ url('/') }}/account/inventory/item-history/' + itemId,
                type: 'GET',
                success: function(response) {
                    // Get the first history record to find the item details
                    if (response.length > 0) {
                        var firstHistory = response[0];
                        var item = firstHistory.item;

                        // Populate item information with stylish formatting
                        modal.find('#modal-item-name').html('<strong>' + (item.item_name || 'N/A') +
                            '</strong>');
                        modal.find('#modal-item-category').html('<strong">' + (item.category ? item
                            .category.name : 'N/A') + '</strong>');
                        modal.find('#modal-item-price').html('<strong class="text-success">Rs ' + (item
                            .item_price || '0.00') + '</strong>');
                        modal.find('#modal-item-description').text(item.description ||
                            'No description available');

                        // Populate history table with stylish rows
                        var html = '';
                        response.forEach(function(history, index) {
                            var type = '';
                            var typeClass = '';
                            if (history.transaction_type == 'receive') {
                                type = 'Item Received';
                                typeClass = 'badge-success';
                            } else if (history.transaction_type == 'sell') {
                                type = 'Item Sold';
                                typeClass = 'badge-danger';
                            } else if (history.transaction_type == 'issue') {
                                type = 'Item Issued';
                                typeClass = 'badge-warning';
                            } else if (history.transaction_type == 'sell_return') {
                                type = 'Item Returned';
                                typeClass = 'badge-info';
                            } else {
                                type = history.transaction_type.charAt(0).toUpperCase() +
                                    history.transaction_type.slice(1);
                                typeClass = 'badge-secondary';
                            }

                            var rowClass = index % 2 === 0 ? 'table-light' : '';
                            html += '<tr class="' + rowClass + '">' +
                                '<td><span class="badge ' + typeClass + '">' + type +
                                '</span></td>' +
                                '<td><strong>' + history.quantity + '</strong></td>' +
                                '<td><strong class="text-primary">' + history.current_balance +
                                '</strong></td>' +
                                '<td>' + new Date(history.transaction_date).toLocaleDateString(
                                    'en-US', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric'
                                    }) + '</td>' +
                                '<td>' + (history.description || 'No description') + '</td>' +
                                '<td><small class="text-muted">' + (history.reference_no ||
                                    'N/A') + '</small></td>' +
                                '</tr>';
                        });
                        modal.find('#inventory-history-body').html(html);
                    } else {
                        modal.find('#inventory-history-body').html(
                            '<tr><td colspan="6" class="text-center text-muted"><i class="ti-info-alt mr-2"></i>No inventory history available</td></tr>'
                            );
                    }
                },
                error: function() {
                    modal.find('#inventory-history-body').html(
                        '<tr><td colspan="6" class="text-center text-danger"><i class="ti-alert mr-2"></i>Error loading inventory data</td></tr>'
                        );
                }
            });
        });
    </script>
@endpush
