@extends('layouts.app')
{{-- BUG FIX (Section 34): browser-tab title called @lang('academics.About') --
     the same leftover Academics-template placeholder found and fixed on many
     other pages across this project -- instead of naming this page. Reused
     the 'issue_item_list' key already used correctly elsewhere on this
     same page. --}}
@section('title')
    @lang('inventory.issue_item_list')
@endsection
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

        .available_quantity input {
            width: 100px !important;
            text-align: center;
        }
    </style>
@endpush
@section('mainContent')
    @php
        // Button-level permission (Section 34).
        $canSaveItemIssue = Auth::user()->hasPermission('save-item-issue-data');
    @endphp
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('inventory.issue_item_list')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('inventory.inventory')</a>
                    <a href="#">@lang('inventory.issue_item_list')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            {{-- Form starts --}}
            <form class="form-horizontal"
                action="{{ isset($editData) ? route('item-issue-update', $editData->id) : route('save-item-issue-data') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($editData))
                    @method('PUT')
                @endif

                <div class="row">
                    {{-- Left Side - Vertical Dropdowns --}}
                    <div class="col-lg-3">
                        <div class="white-box">
                            <div class="main-title">
                                <h3 class="mb-15">@lang('inventory.issue_a_item')</h3>
                            </div>
                            <div class="add-visitor">
                                <div class="row">

                                    {{-- Branch Dropdown --}}
                                    <div class="col-lg-12 mb-15">
                                        <label class="primary_input_label">Branch <span class="text-danger">*</span></label>
                                        <select class="primary_select form-control" name="branch_id" id="branch_select"
                                            required>
                                            @if (count($branches) > 1)
                                                <option value="">Select Branch *</option>
                                            @endif
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ count($branches) == 1 ? 'selected' : '' }}>{{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Student Dropdown --}}
                                    <div class="col-lg-12 mb-15">
                                        <label class="primary_input_label">Student <span
                                                class="text-danger">*</span></label>
                                        <select class="primary_select form-control" name="student_id" id="student_select"
                                            disabled required>
                                            <option value="">Select Branch First *</option>
                                        </select>
                                    </div>

                                    {{-- Intake Dropdown --}}
                                    <div class="col-lg-12 mb-15">
                                        <label class="primary_input_label">Intake <span class="text-danger">*</span></label>
                                        <select class="primary_select form-control" name="intake_id" id="intake_select"
                                            disabled required>
                                            <option value="">Select Student First *</option>
                                        </select>
                                    </div>

                                    {{-- Course Dropdown --}}
                                    <div class="col-lg-12 mb-15">
                                        <label class="primary_input_label">Course <span class="text-danger">*</span></label>
                                        <select class="primary_select form-control" name="course_id" id="course_select"
                                            disabled required>
                                            <option value="">Select Student First *</option>
                                        </select>
                                        @error('course_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>



                                    {{-- Issue Date --}}
                                    <div class="col-lg-12 mb-15">
                                        <label class="primary_input_label">Issue Date</label>
                                        <input class="primary_input_field form-control date" type="text"
                                            name="issue_date"
                                            value="{{ isset($editData) ? date('m/d/Y', strtotime($editData->issue_date)) : date('m/d/Y') }}">
                                    </div>

                                    {{-- Due Date (hidden by default, can be shown if needed) --}}
                                    <div class="col-lg-12 mb-15" style="display: none;">
                                        <label class="primary_input_label">Due Date</label>
                                        <input class="primary_input_field form-control date" type="text" name="due_date"
                                            value="{{ isset($editData) ? date('m/d/Y', strtotime($editData->due_date)) : date('m/d/Y') }}">
                                    </div>

                                    {{-- Description --}}
                                    <div class="col-lg-12 mb-15">
                                        <label class="primary_input_label">Note</label>
                                        <textarea class="primary_input_field form-control" name="description" rows="4">{{ isset($editData) ? $editData->description : old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side - Category & Item horizontally + Save --}}
                    <div class="col-lg-9">
                        <div class="white-box">
                            <div class="main-title">
                                <h3 class="mb-15">Inventory Items Select</h3>
                            </div>
                            <div class="row">
                                {{-- Combined Category / Item Dropdown --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Category / Item *</label>
                                    <select class="primary_select form-control" id="item_category_item_select">
                                        <option value="">Select Category / Item *</option>
                                        @foreach ($categories as $category)
                                            <option value="cat_{{ $category->id }}" data-type="category"
                                                data-category-id="{{ $category->id }}"
                                                data-category-name="{{ $category->name }}" class="font-weight-bolder">
                                                {{ $category->name }}</option>
                                            @foreach ($inventoryItems->where('item_category_id', $category->id) as $item)
                                                <option value="{{ $item->id }}" data-type="item"
                                                    data-category-id="{{ $category->id }}"
                                                    data-category-name="{{ $category->name }}"
                                                    data-item-name="{{ $item->item_name }}"
                                                    data-item-price="{{ $item->item_price ?? '0' }}">
                                                    &nbsp;&nbsp;&nbsp;{{ $item->item_name }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Selected Items Table --}}
                                <div class="col-lg-12 mt-10">
                                    <div class="white-box">
                                        <div class="table-responsive">
                                            <table class="table   table-bordered school-table-style selected_items_table"
                                                cellspacing="0" width="100%">

                                                <thead>
                                                    <tr>
                                                        <th>@lang('common.sl')</th>
                                                        <th>Category</th>
                                                        <th>Item</th>
                                                        {{-- <th>Item Price</th> --}}
                                                        <th>Available Qty</th>
                                                        <th>@lang('inventory.quantity')</th>
                                                        <th class="text-center"> @lang('common.action')</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="allIssuedItems">
                                                    {{-- Existing items for edit mode --}}
                                                    @if (isset($editData))
                                                        <tr data-id="{{ $editData->item_id }}">
                                                            <td class="sl">1</td>
                                                            <td class="category_name">
                                                                {{ $editData->category->name ?? 'N/A' }}
                                                            </td>
                                                            <td class="item_name">
                                                                {{ $editData->item->item_name ?? 'N/A' }}
                                                            </td>
                                                            <td class="item_price">
                                                                Rs.{{ number_format($editData->item->item_price ?? 0, 2) }}
                                                            </td>
                                                            <td class="available_quantity">
                                                                <input type="text"
                                                                    class="primary_input_field form-control"
                                                                    id="available_quantity_0" value="" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="items[0][quantity]"
                                                                    class="primary_input_field form-control item_quantity"
                                                                    value="{{ $editData->quantity }}" min="1"
                                                                    required oninput="validateQuantity(0)"
                                                                    onchange="validateQuantity(0)">
                                                                <input type="hidden" name="items[0][item_id]"
                                                                    value="{{ $editData->item_id }}">
                                                                <input type="hidden" name="items[0][item_category_id]"
                                                                    value="{{ $editData->item_category_id }}">
                                                                <input type="hidden" name="items[0][item_price]"
                                                                    value="{{ $editData->item->item_price ?? 0 }}">
                                                            </td>
                                                            <td class="text-center align-middle p-1">
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm remove_item_btn">
                                                                    <i class="ti-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>


                            </div>

                            {{-- Student Fee Data Section --}}

                            <div id="student-fee-data" style="display: none;">
                                <div class="white-box">
                                    <div class="main-title">
                                        <h4 class="mb-15">Student fee Informations</h4>
                                    </div>
                                    <div class="row">

                                        {{-- <div class="col-lg-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label">Payment Count</label>
                                                <input type="text" class="form-control" id="payment_count"
                                                    readonly>
                                            </div>
                                        </div> --}}
                                        <div class="col-lg-4">
                                            <div class="primary_input">
                                                <label class="primary_input_label">Total Fees</label>
                                                <input type="text" class="form-control" id="fee_total_fees" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="primary_input">
                                                <label class="primary_input_label">@lang('inventory.total_paid')</label>
                                                <input type="text" class="form-control" id="fee_total_paid" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="primary_input">
                                                <label class="primary_input_label">@lang('inventory.total_due')</label>
                                                <input type="text" class="form-control" id="fee_total_due" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Fee Invoices Table --}}

                                </div>




                            </div>




                        </div>
                        <div class="white-box mt-10">
                            {{-- Save Button --}}
                            <div class="col-lg-12 text-center p-0">
                                @if ($canSaveItemIssue)
                                    <button type="submit" class="primary-btn fix-gr-bg w-100 col-12" onclick="debugFormData()">
                                        <span class="ti-check"></span> Issue
                                    </button>
                                @endif
                            </div>
                        </div>


                    </div>
                </div>
            </form>
            {{-- Form ends --}}
        </div>
    </section>
@endsection

@push('scripts')
    @include('components.partials.data_table_js')
    @include('components.partials.date_picker_css_js')
    <script>
        $(document).ready(function() {
            // Configure toastr options
            toastr.options = {
                "timeOut": "5000",
                "extendedTimeOut": "1000"
            };

            console.log('🚀 Item Issue Page Loaded - Starting Cascading Dropdown System');

            if ({{ count($branches) }} == 1) {
                $('#branch_select').trigger('change');
            }

            // Helper function to refresh niceSelect safely
            function refreshNiceSelect(selector) {
                if ($.fn.niceSelect) {
                    $(selector).niceSelect('destroy');
                    $(selector).niceSelect();
                }
            }

            // Track added item types
            let addedItemTypes = new Set();
            @if (isset($editData))
                addedItemTypes.add('{{ $editData->item_id }}');
            @endif

            // Track the next available index for item rows
            let itemRowIndex = 0;
            @if (isset($editData))
                itemRowIndex = 1; // If there's an existing item, start new items from index 1
            @endif

            function updateSerialNumbers() {
                $('.allIssuedItems tr').each(function(index) {
                    $(this).find('.sl').text(index + 1);
                });
            }

            // Handle combined category/item change
            $('#item_category_item_select').on('change', function() {
                let selectedOption = $(this).find("option:selected");
                let type = selectedOption.data('type');

                if (!selectedOption.val()) {
                    $(this).val('').niceSelect('update');
                    return;
                }

                let branchId = $('#branch_select').val();
                if (!branchId) {
                    toastr.error('Please select a branch first', 'Warning');
                    $('#branch_select').focus();
                    return;
                }

                if (type === 'category') {
                    // Handle category selection - select all items in category
                    let categoryValue = selectedOption.val();
                    let categoryId = selectedOption.data(
                        'category-id'); // Get actual category ID from data attribute
                    let categoryName = selectedOption.data('category-name') || selectedOption.text();

                    console.log('🔍 Category selected:', categoryName, 'Value:', categoryValue, 'ID:',
                        categoryId);
                    console.log('🔍 Available options in dropdown:');
                    $('#item_category_item_select option').each(function() {
                        console.log('  -', $(this).val(), $(this).data('type'), $(this).data(
                            'category-id'), $(this).text());
                    });

                    // Find all items in this category by data attribute
                    let itemsFound = 0;
                    $(`#item_category_item_select option[data-category-id="${categoryId}"][data-type="item"]`)
                        .each(function() {
                            let itemId = $(this).val();
                            let itemName = $(this).data('item-name');

                            console.log('📦 Found item in category:', itemName, 'ID:', itemId,
                                'Category ID:', $(this).data('category-id'));

                            // Only add if not already added
                            if (!addedItemTypes.has(itemId)) {
                                let itemPrice = $(this).data('item-price') || '0';
                                addItemRow(itemId, itemName, categoryId, categoryName, itemPrice,
                                    branchId);
                                addedItemTypes.add(itemId);
                                itemsFound++;
                            }
                        });

                    console.log('✅ Total items found in category:', itemsFound);

                    if (itemsFound > 0) {
                        toastr.success(
                            `All ${itemsFound} items from category "${categoryName}" have been selected`,
                            '', {
                                timeOut: 3000,
                                extendedTimeOut: 1000
                            });
                    } else {
                        toastr.warning(`No items found in category "${categoryName}"`, '', {
                            timeOut: 3000,
                            extendedTimeOut: 1000
                        });
                    }
                } else {
                    // Handle individual item selection
                    let itemId = selectedOption.val();
                    let itemName = selectedOption.data('item-name');
                    let categoryId = selectedOption.data('category-id');
                    let categoryName = selectedOption.data('category-name');

                    console.log('📦 Individual item selected:', itemName, 'ID:', itemId, 'Category:',
                        categoryName);

                    // Only add if not already added
                    if (!addedItemTypes.has(itemId)) {
                        let itemPrice = selectedOption.data('item-price') || '0';
                        addItemRow(itemId, itemName, categoryId, categoryName, itemPrice, branchId);
                        addedItemTypes.add(itemId);

                        toastr.success(`Item "${itemName}" has been added to the selection`, '', {
                            timeOut: 2000,
                            extendedTimeOut: 500
                        });
                    } else {
                        toastr.warning(`Item "${itemName}" is already selected`, '', {
                            timeOut: 2000,
                            extendedTimeOut: 500
                        });
                    }
                }

                $(this).val('').niceSelect('update');
            });

            function addItemRow(itemId, itemName, categoryId, categoryName, itemPrice = '0', branchId) {

                let currentIndex = itemRowIndex++;

                // Format price for display
                let displayPrice = itemPrice ? 'Rs.' + parseFloat(itemPrice).toFixed(2) : 'N/A';

                // Fetch available quantity
                $.ajax({
                    url: '{{ url('/') }}/account/inventory/get-item-price/' + itemId + '/' + branchId,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let availableQuantity = response.available_quantity;
                            let row = `<tr data-id="${itemId}">
                                <td class="sl"></td>
                                <td class="category_name">${categoryName}</td>
                                <td class="item_name">${itemName}</td>
                                <td class="available_quantity">
                                    <input type="text" class="primary_input_field form-control" id="available_quantity_${currentIndex}" value="${availableQuantity}" readonly>
                                </td>
                                <td>
                                    <input type="number" name="items[${currentIndex}][quantity]" class="primary_input_field form-control item_quantity" value="1" min="1" max="${availableQuantity}" required oninput="validateQuantity(${currentIndex})" onchange="validateQuantity(${currentIndex})">
                                    <input type="hidden" name="items[${currentIndex}][item_id]" value="${itemId}">
                                    <input type="hidden" name="items[${currentIndex}][item_category_id]" value="${categoryId}">
                                    <input type="hidden" name="items[${currentIndex}][item_price]" value="${itemPrice}">
                                </td>
                                <td class="text-center align-middle p-1">
                                    <button type="button" class="btn btn-danger btn-sm remove_item_btn" >
                                        <i class="ti-trash"></i>
                                    </button>
                                </td>
                                </tr>`;
                            $('.allIssuedItems').append(row);
                            updateSerialNumbers();
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to fetch item details.', 'Error');
                    }
                });
            }

            // Delete row
            $(document).on('click', '.remove_item_btn', function() {
                let itemId = $(this).closest('tr').data('id');
                addedItemTypes.delete(String(itemId)); // Ensure string type for Set
                $(this).closest('tr').remove();
                updateSerialNumbers();
            });

            // Validate quantity input
            function validateQuantity(rowIndex) {
                const qtyInput = $(`#available_quantity_${rowIndex}`).closest('tr').find('.item_quantity');
                const availableInput = $(`#available_quantity_${rowIndex}`);
                const qty = parseFloat(qtyInput.val()) || 0;
                const available = parseFloat(availableInput.val()) || 0;
                if (qty > available) {
                    toastr.error(`Quantity cannot exceed available quantity (${available})`, 'Validation Error');
                    qtyInput.val('');
                    return;
                }
            }

            // Debug function to log form data before submission
            function debugFormData() {
                // Collect all form data
                let formData = new FormData(document.querySelector('form'));
                let formObject = {};

                for (let [key, value] of formData.entries()) {
                    if (key.startsWith('items[')) {
                        // Handle array fields
                        if (!formObject[key]) {
                            formObject[key] = value;
                        }
                    } else {
                        formObject[key] = value;
                    }
                }

                // Specifically check items array
                let items = [];
                $('input[name*="[item_id]"]').each(function(index) {
                    let itemId = $(this).val();
                    let row = $(this).closest('tr');
                    let categoryId = row.find('input[name*="[item_category_id]"]').val();
                    let quantity = row.find('input[name*="[quantity]"]').val();

                    items.push({
                        index: index,
                        item_id: itemId,
                        item_category_id: categoryId,
                        quantity: quantity,
                        category_name: row.find('.category_name').text(),
                        item_name: row.find('.item_name').text()
                    });

                    console.log(`Item ${index}:`, {
                        item_id: itemId,
                        item_category_id: categoryId,
                        quantity: quantity
                    });
                });

                console.log('📦 Items Array:', items);
                console.log('🔍 === END FORM DEBUG ===');

                // Show toastr with item details for user to see
                if (items.length > 0) {
                    let debugInfo = 'Form Debug Info:\n\n';
                    debugInfo += `Total Items: ${items.length}\n\n`;
                    items.forEach((item, index) => {
                        debugInfo += `Item ${index + 1}:\n`;
                        debugInfo += `  - Item ID: ${item.item_id}\n`;
                        debugInfo += `  - Category ID: ${item.item_category_id}\n`;
                        debugInfo += `  - Quantity: ${item.quantity}\n`;
                        debugInfo += `  - Item Name: ${item.item_name}\n`;
                        debugInfo += `  - Category Name: ${item.category_name}\n\n`;
                    });

                    toastr.info(debugInfo, 'Form Debug Info', {
                        timeOut: 10000
                    });
                } else {
                    toastr.warning('No items selected! Please select at least one item.', 'Warning');
                }
            }

            $('#student_select, #branch_select, #intake_select, #course_select').on('change', function() {
                checkAndFetchFeeData();
            });

            function checkAndFetchFeeData() {
                let student_id = $('#student_select').val();
                let branch_id = $('#branch_select').val();
                let intake_id = $('#intake_select').val();
                let course_id = $('#course_select').val();

                console.log('Selected:', {
                    student_id,
                    branch_id,
                    intake_id,
                    course_id
                });

                // Only proceed if all are selected
                if (student_id && branch_id && intake_id && course_id) {
                    console.log('✅ All selected — fetching fee data...');

                    // Build URL dynamically
                    var url = '{{ route('item-issue.get-student-fee-data') }}';

                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: {
                            student_id: student_id,
                            branch_id: branch_id,
                            intake_id: intake_id,
                            course_id: course_id
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            $('#student-fee-data').show();
                            $('#fee_invoices_tbody').html(
                                '<tr><td colspan="4" class="text-center">Loading fee data...</td></tr>'
                            );
                        },
                        success: function(data) {
                            // $('#payment_count').val(data.total_invoices || 0);
                            $('#fee_total_fees').val('Rs ' + (data.total_fees || 0).toFixed(2));
                            $('#fee_total_paid').val('Rs ' + (data.total_paid || 0).toFixed(2));
                            $('#fee_total_due').val('Rs ' + (data.total_due || 0).toFixed(2));

                            let tableHtml = '';
                            if (data.fee_invoices && data.fee_invoices.length > 0) {
                                data.fee_invoices.forEach(invoice => {
                                    let due = (invoice.amount || 0) - (invoice.paid_amount ||
                                        0);
                                    tableHtml += `<tr>
                            <td>${invoice.fee_type_name || 'N/A'}</td>
                            <td>Rs ${invoice.amount.toFixed(2)}</td>
                            <td>Rs ${invoice.paid_amount.toFixed(2)}</td>
                            <td>Rs ${due.toFixed(2)}</td>
                        </tr>`;
                                });
                            } else {
                                tableHtml =
                                    `<tr><td colspan="4" class="text-center">No fee invoices found</td></tr>`;
                            }
                            $('#fee_invoices_tbody').html(tableHtml);
                        },
                        error: function(xhr, status, error) {
                            console.error('❌ Error fetching fee data:', error);
                            $('#fee_invoices_tbody').html(
                                `<tr><td colspan="4" class="text-center text-danger">Error: ${error}</td></tr>`
                            );
                        }
                    });
                } else {
                    console.log('⚠️ Waiting for all selections to be made.');
                    $('#student-fee-data').hide();
                }
            }

            // Handle branch change - Populate Student dropdown
            $('#branch_select').on('change', function() {
                var branch_id = $(this).val();

                // Reset and disable Student, Intake, Course dropdowns
                $('#student_select').html('<option value="">Loading students...</option>').prop('disabled',
                    true);
                $('#intake_select').html('<option value="">Select Student First *</option>').prop(
                    'disabled', true);
                $('#course_select').html('<option value="">Select Student First *</option>').prop(
                    'disabled', true);
                refreshNiceSelect('#student_select');
                refreshNiceSelect('#intake_select');
                refreshNiceSelect('#course_select');
                $('#student-fee-data').hide();

                $('.allIssuedItems').empty();
                addedItemTypes.clear();
                itemRowIndex = 0;

                if (branch_id && branch_id !== '') {
                    var url = '{{ route('item-issue.get-students-by-branch', ':branchId') }}'.replace(
                        ':branchId', branch_id);
                    $.get(url, function(data) {
                        var optionsHtml = '<option value="">Select Student *</option>';
                        if (data && Array.isArray(data) && data.length > 0) {
                            console.log("ddd", data)
                            data.forEach(function(student) {
                                optionsHtml +=
                                    `<option value="${student.id}">${student.admission_number} - ${student.student_name}</option>`;
                            });
                            $('#student_select').html(optionsHtml).prop('disabled', false);
                        } else {
                            $('#student_select').html('<option value="">No Students Found</option>')
                                .prop('disabled', true);
                        }
                        refreshNiceSelect('#student_select');
                    }).fail(function(xhr, status, error) {
                        console.error('❌ AJAX Error fetching students by branch:', error);
                        $('#student_select').html(
                            '<option value="">Error loading students</option>').prop('disabled',
                            true);
                        refreshNiceSelect('#student_select');
                    });
                } else {
                    $('#student_select').html('<option value="">Select Branch First *</option>').prop(
                        'disabled', true);
                    refreshNiceSelect('#student_select');
                }
            });

            $('#branch_select').trigger('change');


            // Handle student change - Populate Intake dropdown
            $('#student_select').on('change', function() {
                var student_id = $(this).val();
                var branch_id = $('#branch_select').val();
                console.log('👨‍🎓 Student selected:', student_id, 'for Branch:', branch_id);

                // Reset and disable Intake, Course dropdowns
                $('#intake_select').html('<option value="">Loading intakes...</option>').prop('disabled',
                    true);
                $('#course_select').html('<option value="">Select Intake First *</option>').prop('disabled',
                    true);
                refreshNiceSelect('#intake_select');
                refreshNiceSelect('#course_select');
                $('#student-fee-data').hide();

                if (student_id && student_id !== '' && branch_id && branch_id !== '') {
                    var url =
                        '{{ route('item-issue.get-intakes-by-branch-and-student', [':branchId', ':studentId']) }}'
                        .replace(':branchId', branch_id)
                        .replace(':studentId', student_id);

                    $.get(url, function(data) {
                        var optionsHtml = '<option value="">Select Intake *</option>';
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(function(intake) {
                                optionsHtml +=
                                    `<option value="${intake.id}" data-start-date="${intake.start_date || ''}">${intake.name}</option>`;
                            });
                            $('#intake_select').html(optionsHtml).prop('disabled', false);
                        } else {
                            $('#intake_select').html('<option value="">No Intakes Found</option>')
                                .prop('disabled', true);
                        }
                        refreshNiceSelect('#intake_select');
                    }).fail(function(xhr, status, error) {
                        console.error('❌ AJAX Error fetching intakes by branch and student:',
                            error);
                        $('#intake_select').html('<option value="">Error loading intakes</option>')
                            .prop('disabled', true);
                        refreshNiceSelect('#intake_select');
                    });
                } else {
                    $('#intake_select').html('<option value="">Select Student First *</option>').prop(
                        'disabled', true);
                    refreshNiceSelect('#intake_select');
                }
            });


            // Handle intake change - Filter courses by selected intake and student
            $('#intake_select').on('change', function() {
                var intake_id = $(this).val();
                var student_id = $('#student_select').val(); // Get the currently selected student
                console.log('📚 Intake selected:', intake_id, 'for Student:', student_id);

                // Reset and disable Course dropdown
                $('#course_select').html('<option value="">Loading courses...</option>').prop('disabled',
                    true);
                refreshNiceSelect('#course_select');

                // Get intake start date
                var selectedIntakeOption = $(this).find("option:selected");
                var intakeStartDateStr = selectedIntakeOption.data('start-date');

                if (intakeStartDateStr) {
                    var intakeStartDate = new Date(intakeStartDateStr);
                    var today = new Date();

                    if (intakeStartDate < today) {
                        toastr.warning(`Intake has started. Start date : ${intakeStartDateStr}`,
                            '', {
                                timeOut: 5000,
                                extendedTimeOut: 1000
                            });
                        // Optionally disable further selections or the submit button if intake hasn't started
                        // For now, just show a warning.
                    } else {
                        toastr.success(`This intake has not started yet. Start date: ${intakeStartDateStr}`,
                            '', {
                                timeOut: 3000,
                                extendedTimeOut: 1000
                            });
                    }
                }


                if (intake_id && intake_id !== '' && student_id && student_id !== '') {
                    var url =
                        '{{ route('item-issue.get-courses-by-intake-and-student', [':intakeId', ':studentId']) }}'
                        .replace(':intakeId', intake_id)
                        .replace(':studentId', student_id);

                    $.get(url, function(data) {
                        var optionsHtml = '<option value="">Select Course *</option>';
                        if (data && Array.isArray(data) && data.length > 0) {
                            data.forEach(function(course) {
                                optionsHtml += '<option value="' + course.id +
                                    '" data-intake-start-date="' + intakeStartDateStr +
                                    // Pass intake start date
                                    '" data-expiration-date="' + (course.expiration_date ||
                                        '') + '">' + course.name + '</option>';
                            });
                            $('#course_select').html(optionsHtml).prop('disabled',
                                false); // Enable after populating
                        } else {
                            optionsHtml += '<option value="">No courses found</option>';
                            $('#course_select').html(optionsHtml).prop('disabled',
                                true); // Keep disabled if no options
                        }
                        refreshNiceSelect('#course_select');

                    }).fail(function(xhr, status, error) {
                        console.error('❌ Error loading courses by intake and student:', error);
                        $('#course_select').html('<option value="">Error loading courses</option>')
                            .prop('disabled', true);
                        refreshNiceSelect('#course_select');
                    });
                } else {
                    $('#course_select').html('<option value="">Select Intake First *</option>').prop(
                        'disabled', true);
                    refreshNiceSelect('#course_select');
                }
            });

            // Handle course change - Validate StudentCourse status
            $('#course_select').on('change', function() {
                var course_id = $(this).val();
                var student_id = $('#student_select').val();
                var intake_id = $('#intake_select').val();

                if (course_id && course_id !== '' && student_id && student_id !== '' && intake_id &&
                    intake_id !== '') {
                    // Check StudentCourse status
                    $.ajax({
                        url: '{{ route('item-issue.check-student-course-status') }}',
                        type: 'GET',
                        data: {
                            student_id: student_id,
                            course_id: course_id,
                            intake_id: intake_id
                        },
                        success: function(response) {
                            if (response.can_issue) {
                                toastr.success(response.message, '', {
                                    timeOut: 3000,
                                    extendedTimeOut: 1000
                                });
                            } else {
                                toastr.error(response.message, '', {
                                    timeOut: 5000,
                                    extendedTimeOut: 1000
                                });
                                $('#course_select').val('').niceSelect('update');
                                return;
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('❌ Error checking student course status:', error);
                            toastr.error('Error checking course status. Please try again.',
                                '', {
                                    timeOut: 5000,
                                    extendedTimeOut: 1000
                                });
                            $('#course_select').val('').niceSelect('update');
                        }
                    });
                }
            });

            // Fetch available quantity for edit mode
            @if (isset($editData))
                $.ajax({
                    url: '{{ url('/') }}/account/inventory/get-item-price/{{ $editData->item_id }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#available_quantity_0').val(response.available_quantity);
                            $('.item_quantity').attr('max', response.available_quantity);
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to fetch available quantity.', 'Error');
                    }
                });
            @endif
        });
    </script>
@endpush
