@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
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
        <form class="form-horizontal" action="{{ isset($editData) ? route('item-issue-update', $editData->id) : route('save-item-issue-data') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($editData))
                @method('PUT')
            @endif

            <div class="row">
                {{-- Left Side - Vertical Dropdowns --}}
                <div class="col-lg-4">
                    <div class="white-box">
                        <div class="main-title">
                            <h3 class="mb-15">@lang('inventory.issue_a_item')</h3>
                        </div>
                        <div class="add-visitor">
                            <div class="row">
                                
                                 {{-- Student Dropdown --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Student <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" name="student_id" id="student_select">
                                        <option value="">Select Student *</option>
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}">{{ $student->student_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Branch Dropdown --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Branch <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" name="branch_id" id="branch_select">
                                        <option value="">Select Student First *</option>
                                    </select>
                                </div>

                                {{-- Intake Dropdown --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Intake <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" name="intake_id" id="intake_select" disabled>
                                        <option value="">Select Student First *</option>
                                    </select>
                                </div>

                                {{-- Course Dropdown --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Course <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" name="course_id" id="course_select" disabled>
                                        <option value="">Select Student First *</option>
                                    </select>
                                </div>

                               

                                {{-- Staff Dropdown --}}
                                {{-- <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Issue To</label>
                                    <select class="primary_select form-control" name="staff_id" id="staff_select">
                                        <option value="">Select Staff</option>
                                        @foreach ($staffs as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}

                                {{-- Issue Date --}}
                                <div class="col-lg-12 mb-15">
                                    <label class="primary_input_label">Issue Date</label>
                                    <input class="primary_input_field form-control date" type="text" name="issue_date" value="{{ isset($editData) ? date('m/d/Y', strtotime($editData->issue_date)) : date('m/d/Y') }}">
                                </div>

                                {{-- Due Date --}}
                                <div class="col-lg-12 mb-15" hidden>
                                    <label class="primary_input_label">Due Date</label>
                                    <input class="primary_input_field form-control date" type="text" name="due_date" value="{{ isset($editData) ? date('m/d/Y', strtotime($editData->due_date)) : date('m/d/Y') }}">
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
                <div class="col-lg-8">
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
                                        <option value="cat_{{ $category->id }}" data-type="category" data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}" class="font-weight-bolder">{{ $category->name }}</option>
                                        @foreach ($inventoryItems->where('item_category_id', $category->id) as $item)
                                            <option value="{{ $item->id }}" data-type="item" data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}" data-item-name="{{ $item->item_name }}" data-item-price="{{ $item->item_price ?? '0' }}">
                                                &nbsp;&nbsp;&nbsp;{{ $item->item_name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>

                            {{-- Selected Items Table --}}
                            <div class="col-lg-12 mt-10">
                                <div class="big-table">
                                    <table class="table school-table-style selected_items_table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>@lang('common.sl')</th>
                                                <th>@lang('inventory.category')</th>
                                                <th>@lang('inventory.item')</th>
                                                <th>@lang('inventory.item_price')</th>
                                                <th>@lang('inventory.quantity')</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody class="allIssuedItems">
                                            {{-- Existing items for edit mode --}}
                                            @if(isset($editData))
                                                <tr data-id="{{ $editData->item_id }}">
                                                    <td class="sl">1</td>
                                                    <td class="category_name">{{ $editData->category->name ?? 'N/A' }}</td>
                                                    <td class="item_name">{{ $editData->item->item_name ?? 'N/A' }}</td>
                                                    <td>
                                                        <input type="number" name="items[0][quantity]" class="primary_input_field form-control item_quantity" value="{{ $editData->quantity }}" min="1" required>
                                                        <input type="hidden" name="items[0][item_id]" value="{{ $editData->item_id }}">
                                                        <input type="hidden" name="items[0][item_category_id]" value="{{ $editData->item_category_id }}">
                                                    </td>
                                                    <td><button type="button" class="primary-btn small bg-danger text-white border-0 remove_item_btn">Delete</button></td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Save Button --}}
                            <div class="col-lg-12 text-center mt-4">
                                <button type="submit" class="primary-btn fix-gr-bg" onclick="debugFormData()">
                                    <span class="ti-check"></span> @lang('common.save')
                                </button>
                            </div>
                        </div>

                        {{-- Student Fee Data Section --}}
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <div id="student-fee-data" style="display: none;">
                                    <div class="white-box">
                                        <div class="main-title">
                                            <h4 class="mb-15">Student fee Informations</h4>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="primary_input">
                                                    <label class="primary_input_label">Student Name</label>
                                                    <input type="text" class="form-control" id="fee_student_name" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="primary_input">
                                                    <label class="primary_input_label">Total Invoice</label>
                                                    <input type="text" class="form-control" id="fee_total_invoices" readonly>
                                                </div>
                                            </div>
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
                        </div>
                    </div>
                </div>
            </div>
        </form>
        {{-- Form ends --}}
    </div>
</section>

@endsection

@include('components.partials.data_table_js')
@include('components.partials.date_picker_css_js')

@push('scripts')
    <script>
        $(document).ready(function() {
            console.log('🚀 Item Issue Page Loaded - Starting Cascading Dropdown System');

            // Helper function to refresh niceSelect safely
            function refreshNiceSelect(selector) {
                if ($.fn.niceSelect) {
                    $(selector).niceSelect('destroy');
                    $(selector).niceSelect();
                }
            }

            // Track added item types
            let addedItemTypes = new Set();
            @if(isset($editData))
                addedItemTypes.add('{{ $editData->item_id }}');
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

                if (type === 'category') {
                    // Handle category selection - select all items in category
                    let categoryValue = selectedOption.val();
                    let categoryId = selectedOption.data('category-id'); // Get actual category ID from data attribute
                    let categoryName = selectedOption.data('category-name') || selectedOption.text();

                    console.log('🔍 Category selected:', categoryName, 'Value:', categoryValue, 'ID:', categoryId);
                    console.log('🔍 Available options in dropdown:');
                    $('#item_category_item_select option').each(function() {
                        console.log('  -', $(this).val(), $(this).data('type'), $(this).data('category-id'), $(this).text());
                    });

                    // Find all items in this category by data attribute
                    let itemsFound = 0;
                    $(`#item_category_item_select option[data-category-id="${categoryId}"][data-type="item"]`).each(function() {
                        let itemId = $(this).val();
                        let itemName = $(this).data('item-name');

                        console.log('📦 Found item in category:', itemName, 'ID:', itemId, 'Category ID:', $(this).data('category-id'));

                        // Only add if not already added
                        if (!addedItemTypes.has(itemId)) {
                            let itemPrice = $(this).data('item-price') || '0';
                            addItemRow(itemId, itemName, categoryId, categoryName, itemPrice);
                            addedItemTypes.add(itemId);
                            itemsFound++;
                        }
                    });

                    console.log('✅ Total items found in category:', itemsFound);

                    if (itemsFound > 0) {
                        toastr.success(`All ${itemsFound} items from category "${categoryName}" have been selected`, '', {
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

                    console.log('📦 Individual item selected:', itemName, 'ID:', itemId, 'Category:', categoryName);

                    // Only add if not already added
                    if (!addedItemTypes.has(itemId)) {
                        let itemPrice = selectedOption.data('item-price') || '0';
                        addItemRow(itemId, itemName, categoryId, categoryName, itemPrice);
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

            function addItemRow(itemId, itemName, categoryId, categoryName, itemPrice = '0') {
                // Use a simple index for array keys to avoid conflicts
                let itemIndex = $('.allIssuedItems tr').length;

                // Format price for display
                let displayPrice = itemPrice ? 'Rs.' + parseFloat(itemPrice).toFixed(2) : 'N/A';

                let row = `<tr data-id="${itemId}">
                    <td class="sl"></td>
                    <td class="category_name">${categoryName}</td>
                    <td class="item_name">${itemName}</td>
                    <td class="item_price">${displayPrice}</td>
                    <td>
                        <input type="number" name="items[${itemIndex}][quantity]" class="primary_input_field form-control item_quantity" value="1" min="1" required>
                        <input type="hidden" name="items[${itemIndex}][item_id]" value="${itemId}">
                        <input type="hidden" name="items[${itemIndex}][item_category_id]" value="${categoryId}">
                        <input type="hidden" name="items[${itemIndex}][item_price]" value="${itemPrice}">
                    </td>
                    <td><button type="button" class="primary-btn small bg-danger text-white border-0 remove_item_btn">Delete</button></td>
                </tr>`;
                $('.allIssuedItems').append(row);
                updateSerialNumbers();

                console.log('✅ Added item row:', {
                    index: itemIndex,
                    itemId: itemId,
                    itemName: itemName,
                    categoryId: categoryId,
                    categoryName: categoryName,
                    itemPrice: itemPrice,
                    displayPrice: displayPrice
                });
            }

            // Delete row
            $(document).on('click', '.remove_item_btn', function() {
                let itemId = $(this).closest('tr').data('id');
                addedItemTypes.delete(String(itemId)); // Ensure string type for Set
                $(this).closest('tr').remove();
                updateSerialNumbers();
            });

            // Debug function to log form data before submission
            function debugFormData() {
                console.log('🔍 === FORM DEBUG DATA ===');

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

                console.log('📋 Form Data Object:', formObject);

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

                // Show alert with item details for user to see
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

                    alert(debugInfo);
                } else {
                    alert('No items selected! Please select at least one item.');
                }
            }

            // Handle student change - Populate Branch, Intake, Course, and Load fee data
            $('#student_select').on('change', function() {
                var student_id = $(this).val();
                console.log('👨‍🎓 Student selected:', student_id);
                console.log('DEBUG: Student ID value:', student_id); // Added debug log

                // Reset and disable Branch, Intake, Course dropdowns
                $('#branch_select').html('<option value="">Loading...</option>').prop('disabled', true);
                $('#intake_select').html('<option value="">Loading...</option>').prop('disabled', true);
                $('#course_select').html('<option value="">Loading...</option>').prop('disabled', true);
                refreshNiceSelect('#branch_select');
                refreshNiceSelect('#intake_select');
                refreshNiceSelect('#course_select');

                // Initially disable branch_select
                $('#branch_select').prop('disabled', true);
                refreshNiceSelect('#branch_select');

                if (student_id && student_id !== '') {
                    console.log('DEBUG: Inside student_id check. Preparing AJAX call.'); // Added debug log
                    // AJAX call to get student details (branch, intake, course)
                    var studentDetailsUrl = '{{ route('item-issue.get-student-details', ':studentId') }}'.replace(':studentId', student_id);
                    console.log('🌐 Student Details AJAX URL:', studentDetailsUrl);

                    $.get(studentDetailsUrl, function(data) {
                        console.log('✅ Student details received:', data);

                        // Populate Branch dropdown
                        var branchOptionsHtml = '<option value="">Select Branch *</option>';
                        if (data.branch) {
                            branchOptionsHtml += '<option value="' + data.branch.id + '" selected>' + data.branch.name + '</option>';
                            $('#branch_select').html(branchOptionsHtml).prop('disabled', false); // Enable if branch found
                        } else {
                            branchOptionsHtml += '<option value="">No Branch Found</option>';
                            $('#branch_select').html(branchOptionsHtml).prop('disabled', true); // Keep disabled if no branch
                        }
                        refreshNiceSelect('#branch_select');

                        // Populate Intake dropdown
                        var intakeOptionsHtml = '<option value="">Select Student First *</option>';
                        if (data.intakes && data.intakes.length > 0) {
                            data.intakes.forEach(function(intake) {
                                intakeOptionsHtml += '<option value="' + intake.id + '">' + intake.name + '</option>';
                            });
                            $('#intake_select').html(intakeOptionsHtml).prop('disabled', false); // Enable after populating
                        } else {
                            intakeOptionsHtml += '<option value="">No Intakes Found</option>';
                            $('#intake_select').html(intakeOptionsHtml).prop('disabled', true); // Keep disabled if no options
                        }
                        refreshNiceSelect('#intake_select');

                        // Populate Course dropdown
                        var courseOptionsHtml = '<option value="">Select Student First *</option>';
                        if (data.courses && data.courses.length > 0) {
                            data.courses.forEach(function(course) {
                                courseOptionsHtml += '<option value="' + course.id + '">' + course.name + '</option>';
                            });
                            $('#course_select').html(courseOptionsHtml).prop('disabled', false); // Enable after populating
                        } else {
                            courseOptionsHtml += '<option value="">No Courses Found</option>';
                            $('#course_select').html(courseOptionsHtml).prop('disabled', true); // Keep disabled if no options
                        }
                        refreshNiceSelect('#course_select');

                    }).fail(function(xhr, status, error) {
                        console.error('❌ AJAX Error fetching student details:', error);
                        console.error('DEBUG: XHR status:', xhr.status); // Added debug log
                        console.error('DEBUG: XHR responseText:', xhr.responseText); // Added debug log
                        $('#branch_select').html('<option value="">Error loading branch</option>').prop('disabled', true);
                        $('#intake_select').html('<option value="">Error loading intake</option>').prop('disabled', true);
                        $('#course_select').html('<option value="">Error loading course</option>').prop('disabled', true);
                        refreshNiceSelect('#branch_select');
                        refreshNiceSelect('#intake_select');
                        refreshNiceSelect('#course_select');
                    });

                    // Existing logic for fetching student fee data
                    $('#student-fee-data').show();
                    $('#fee_invoices_tbody').html('<tr><td colspan="4" class="text-center">Loading fee data...</td></tr>');

                    var url = '{{ route('item-issue.get-student-fee-data', ':studentId') }}'.replace(':studentId', student_id);
                    console.log('🌐 Fee Data AJAX URL:', url);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            console.log('✅ Fee data received:', data);

                            if (data) {
                                $('#fee_student_name').val(data.student_name || 'N/A');
                                $('#fee_total_invoices').val(data.total_invoices || 0);
                                $('#fee_total_fees').val('$' + (data.total_fees || 0).toFixed(2));
                                $('#fee_total_paid').val('$' + (data.total_paid || 0).toFixed(2));
                                $('#fee_total_due').val('$' + (data.total_due || 0).toFixed(2));

                                var tableHtml = '';
                                if (data.fee_invoices && data.fee_invoices.length > 0) {
                                    data.fee_invoices.forEach(function(invoice) {
                                        var dueAmount = (invoice.amount || 0) - (invoice.paid_amount || 0);
                                        tableHtml += '<tr>' +
                                            '<td>' + (invoice.fee_type_name || 'N/A') + '</td>' +
                                            '<td>$' + (invoice.amount || 0).toFixed(2) + '</td>' +
                                            '<td>$' + (invoice.paid_amount || 0).toFixed(2) + '</td>' +
                                            '<td>$' + dueAmount.toFixed(2) + '</td>' +
                                            '</tr>';
                                    });
                                } else {
                                    tableHtml = '<tr><td colspan="4" class="text-center">No fee invoices found</td></tr>';
                                }
                                $('#fee_invoices_tbody').html(tableHtml);
                            } else {
                                $('#fee_student_name').val('No data received');
                                $('#fee_total_invoices').val('0');
                                $('#fee_total_fees').val('$0.00');
                                $('#fee_total_paid').val('$0.00');
                                $('#fee_total_due').val('$0.00');
                                $('#fee_invoices_tbody').html('<tr><td colspan="4" class="text-center text-warning">No data received</td></tr>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('❌ AJAX Error:', error);
                            console.error('❌ Status:', status);
                            console.error('❌ Response:', xhr.responseText);

                            $('#fee_student_name').val('Error loading data');
                            $('#fee_total_invoices').val('0');
                            $('#fee_total_fees').val('$0.00');
                            $('#fee_total_paid').val('$0.00');
                            $('#fee_total_due').val('$0.00');
                            $('#fee_invoices_tbody').html('<tr><td colspan="4" class="text-center text-danger">Error: ' + error + '</td></tr>');
                        }
                    });
                } else {
                    console.log('DEBUG: Student ID is empty. Hiding fee data and resetting dropdowns.'); // Added debug log
                    // Hide fee data when no student is selected
                    $('#student-fee-data').hide();
                    // Reset and disable Branch, Intake, Course dropdowns
                    $('#branch_select').html('<option value="">Select Student First *</option>').prop('disabled', true);
                    $('#intake_select').html('<option value="">Select Student First *</option>').prop('disabled', true);
                    $('#course_select').html('<option value="">Select Student First *</option>').prop('disabled', true);
                    refreshNiceSelect('#branch_select');
                    refreshNiceSelect('#intake_select');
                    refreshNiceSelect('#course_select');
                }
            });

            // Handle intake change - Filter courses by selected intake and student
            $('#intake_select').on('change', function() {
                var intake_id = $(this).val();
                var student_id = $('#student_select').val(); // Get the currently selected student
                console.log('📚 Intake selected:', intake_id, 'for Student:', student_id);

                // Reset and disable Course dropdown
                $('#course_select').html('<option value="">Loading courses...</option>').prop('disabled', true);
                refreshNiceSelect('#course_select');

                if (intake_id && intake_id !== '' && student_id && student_id !== '') {
                    var url = '{{ route('item-issue.get-courses-by-intake-and-student', [':intakeId', ':studentId']) }}'
                        .replace(':intakeId', intake_id)
                        .replace(':studentId', student_id);
                    console.log('🌐 AJAX URL for courses by intake and student:', url);

                    $.get(url, function(data) {
                        console.log('✅ Courses received by intake and student:', data);

                        var optionsHtml = '<option value="">Select Course *</option>';
                        if (data && Array.isArray(data) && data.length > 0) {
                            data.forEach(function(course) {
                                optionsHtml += '<option value="' + course.id + '" data-created-date="' + (course.created_at || '') + '" data-expiration-date="' + (course.expiration_date || '') + '">' + course.name + '</option>';
                            });
                            $('#course_select').html(optionsHtml).prop('disabled', false); // Enable after populating
                        } else {
                            optionsHtml += '<option value="">No courses found</option>';
                            $('#course_select').html(optionsHtml).prop('disabled', true); // Keep disabled if no options
                        }
                        refreshNiceSelect('#course_select');

                    }).fail(function(xhr, status, error) {
                        console.error('❌ Error loading courses by intake and student:', error);
                        $('#course_select').html('<option value="">Error loading courses</option>').prop('disabled', true);
                        refreshNiceSelect('#course_select');
                    });
                } else {
                    $('#course_select').html('<option value="">Select Intake First *</option>').prop('disabled', true);
                    refreshNiceSelect('#course_select');
                }
            });

            // Handle course change - Validate course expiration
            $('#course_select').on('change', function() {
                var course_id = $(this).val();
                var selectedOption = $(this).find("option:selected");
                var createdDate = selectedOption.data('created-date');
                var expirationDate = selectedOption.data('expiration-date');

                console.log('📖 Course selected:', course_id);
                console.log('📅 Course created date:', createdDate);
                console.log('⏰ Course expiration date:', expirationDate);

                if (course_id && course_id !== '' && createdDate && expirationDate) {
                    // Calculate days difference between course creation and today
                    var courseCreatedDate = new Date(createdDate);
                    var today = new Date();
                    var timeDifference = today.getTime() - courseCreatedDate.getTime();
                    var daysDifference = Math.ceil(timeDifference / (1000 * 3600 * 24));

                    console.log('📊 Days since course creation:', daysDifference);
                    console.log('📊 Course expiration days:', expirationDate);

                    // Check if course has expired
                    if (daysDifference > parseInt(expirationDate)) {
                        console.log('❌ Course has expired');
                        toastr.error('This course has expired and cannot be issued to students. Course expired ' + (daysDifference - parseInt(expirationDate)) + ' days ago.', '', {
                            timeOut: 5000,
                            extendedTimeOut: 1000
                        });
                        $('#course_select').val('').niceSelect('update');
                        return;
                    } else {
                        console.log('✅ Course is valid');
                        toastr.success('Course is valid and can be issued', '', {
                            timeOut: 5000,
                            extendedTimeOut: 1000
                        });
                    }
                }
            });
        });
    </script>
@endpush
