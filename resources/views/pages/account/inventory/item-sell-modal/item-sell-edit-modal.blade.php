<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('common.edit') @lang('inventory.item_sell')</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        <form class="form-horizontal" id="item-sell-edit-form" action="{{ route('update-item-sell', $itemSell->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="add-visitor">
                                <div class="row">
                                    <div class="col-lg-12 mb-30">
                                        <label class="primary_input_label" for="">@lang('inventory.payment_method') <span
                                                class="text-danger"> *</span> </label>
                                        <select class="primary_select  form-control" name="payment_method"
                                            id="item_sell_payment_method_id_modal">
                                            <option data-display="@lang('inventory.payment_method')*" value="">
                                                @lang('inventory.payment_method')*</option>
                                            <option value="Cash" {{ $itemSell->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="Cheque" {{ $itemSell->payment_method == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                        </select>
                                        <div class="text-danger" id="paymentErrorModal"></div>
                                    </div>

                                    <div class="col-lg-12 mb-30">
                                        <label class="primary_input_label" for="">@lang('inventory.sell_to') <span
                                                class="text-danger"> *</span> </label>
                                        <select
                                            class="primary_select  form-control"
                                            name="role_id" id="buyer_type_modal">
                                            <option data-display="@lang('inventory.sell_to') *" value="">
                                                @lang('inventory.sell_to') *</option>
                                            @foreach ($roles as $value)
                                                <option value="{{ $value->id }}"
                                                    {{ $value->id == $itemSell->role_id ? 'selected' : '' }}>
                                                    {{ $value->role_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger" id="buyerErrorModal"></div>
                                    </div>
                                    <div class="forStudentWrapper col-lg-12" style="{{ ($itemSell->role_id == 2 || $itemSell->role_id == 3) ? 'display:block;' : 'display:none;' }}">
                                        <div class="row">
                                            <div class="col-lg-12 mb-30">
                                                <label class="primary_input_label" for="">@lang('common.class')
                                                    <span class="text-danger"> *</span> </label>
                                                <select
                                                    class="primary_select form-control"
                                                    id="select_class_modal" name="class">
                                                    <option data-display="@lang('common.select_class') *" value="">
                                                        @lang('common.select_class') *</option>
                                                    @foreach ($classes as $class)
                                                        <option value="{{ $class->id }}"
                                                            {{ $itemSell->course_id == $class->id ? 'selected' : '' }}>
                                                            {{ $class->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="text-danger" id="classErrorModal"></div>
                                            </div>
                                            <div class="col-lg-12 mb-30" id="select_section_div_modal">
                                                <label class="primary_input_label" for="">@lang('common.section')
                                                    <span class="text-danger"> *</span> </label>
                                                <select
                                                    class="primary_select form-control"
                                                    id="select_section_modal" name="section">
                                                    <option data-display="@lang('common.select_section') *" value="">
                                                        @lang('common.select_section') *</option>
                                                    @foreach ($sections as $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ $itemSell->intake_id == $value->id ? 'selected' : '' }}>
                                                            {{ $value->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="text-danger" id="sectionErrorModal"></div>
                                            </div>


                                            <div class="col-lg-12 mb-30" id="select_student_div_modal">
                                                <label class="primary_input_label" for="">@lang('common.student')
                                                    <span class="text-danger"> *</span> </label>
                                                <select
                                                    class="primary_select form-control"
                                                    id="select_student_modal" name="student">
                                                    <option data-display="@lang('common.select_student') *" value="">
                                                        @lang('common.select_student') *</option>
                                                    @foreach ($students as $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ $itemSell->student_id == $value->id ? 'selected' : '' }}>
                                                            {{ $value->student_name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="text-danger" id="studentErrorModal"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mb-30" id="selectStaffsDivModal" style="{{ ($itemSell->role_id != 2 && $itemSell->role_id != 3) ? 'display:block;' : 'display:none;' }}">
                                        <label class="primary_input_label" for="">@lang('common.name') <span
                                                class="text-danger"> *</span> </label>
                                        <select
                                            class="primary_select  form-control"
                                            name="staff_id" id="selectStaffsModal">
                                            <option data-display="@lang('common.name') *" value="">
                                                @lang('common.name') *</option>
                                            @foreach ($users as $value)
                                                <option value="{{ $value->id }}" {{ $itemSell->user_id == $value->id ? 'selected' : '' }}>
                                                    {{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger" id="nameErrorModal"></div>
                                    </div>

                                    <div class="col-lg-12 mb-30">
                                        <div class="primary_input">
                                            <label class="primary_input_label"
                                                for="">@lang('inventory.reference_no')</label>
                                            <input class="primary_input_field form-control" type="text"
                                                name="reference_no" autocomplete="off"
                                                value="{{ $itemSell->reference_no }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mb-30">
                                        <div class="primary_input ">
                                            <label class="primary_input_label" for="">@lang('inventory.sell_date')
                                                <span></span> </label>
                                            <div class="primary_datepicker_input">
                                                <div class="no-gutters input-right-icon">
                                                    <div class="col">
                                                        <div class="">
                                                            <input
                                                                class="primary_input_field  primary_input_field date form-control"
                                                                id="sell_date_modal" type="text" name="sell_date"
                                                                value="{{ date('m/d/Y', strtotime($itemSell->sell_date)) }}"
                                                                autocomplete="off">
                                                        </div>
                                                    </div>
                                                    <button class="btn-date" data-id="#sell_date_modal" type="button">
                                                        <label for="sell_date_modal" class="m-0 p-0">
                                                            <i class="ti-calendar" id="start-date-icon-modal"></i>
                                                        </label>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-20">
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">@lang('common.description')
                                                <span></span> </label>
                                            <textarea class="primary_input_field form-control" cols="0" rows="4" name="description"
                                                id="description_modal">{{ $itemSell->description }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row xm_3 aling-tiems-center">
                                    <div class="col-lg-4 no-gutters col-6 xs_mt_0">
                                        <div class="main-title">
                                            <h3 class="mb-15">@lang('inventory.item_receive')</h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="white-box">
                                            <div class="alert alert-danger" id="errorMessage2Modal" style="display:none;">
                                                <div id="itemErrorModal"></div>
                                                <div id="priceErrorModal"></div>
                                                <div id="quantityErrorModal"></div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table" id="productTableModal">
                                                    <thead>
                                                        <tr>
                                                            <th> @lang('inventory.product_name')* </th>
                                                            <th> @lang('inventory.sell_price')* </th>
                                                            <th> @lang('inventory.quantity')* </th>
                                                            <th>@lang('inventory.sub_total')</th>
                                                            <th>@lang('common.action')</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr id="row1Modal" class="0">
                                                            <td class="border-top-0">
                                                                <input type="hidden" name="url" id="urlModal" value="{{ URL::to('/') }}">
                                                                <div class="primary_input">
                                                                    <select class="primary_select form-control" name="item_id[]" id="productName1Modal">
                                                                        <option value="">@lang('common.select')*</option>
                                                                        @foreach ($items as $key => $value)
                                                                            <option value="{{ $value->id }}" {{ $itemSell->item_id == $value->id ? 'selected' : '' }}>{{ $value->item_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td class="border-top-0">
                                                                <div class="primary_input">
                                                                    <input oninput="numberCheckWithDot(this)" class="primary_input_field form-control"
                                                                           type="text" step="0.1" id="unit_price1Modal" name="unit_price[]"
                                                                           autocomplete="off" value="{{ $itemSell->sell_price }}" onkeyup="getTotalByPriceModal(1)">
                                                                </div>
                                                            </td>
                                                            <td class="border-top-0">
                                                                <div class="primary_input">
                                                                    <input oninput="numberCheckWithDot(this)" class="primary_input_field form-control"
                                                                           type="text" id="quantity1Modal" name="quantity[]" autocomplete="off"
                                                                           onkeyup="getTotalModal(1);" value="{{ $itemSell->total_quantity }}">
                                                                </div>
                                                            </td>
                                                            <td class="border-top-0">
                                                                <div class="primary_input">
                                                                    <input oninput="numberCheckWithDot(this)" class="primary_input_field form-control"
                                                                           type="text" name="total[]" id="total1Modal" autocomplete="off" value="{{ number_format($itemSell->sell_price * $itemSell->total_quantity, 2, '.', '') }}">
                                                                </div>
                                                                <input type="hidden" name="totalValue[]" id="totalValue1Modal" autocomplete="off" class="form-control" value="{{ number_format($itemSell->sell_price * $itemSell->total_quantity, 2, '.', '') }}" />
                                                            </td>
                                                            <td>
                                                                {{-- No remove button for single item edit --}}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th class="border-top-0" colspan="2">@lang('inventory.total')</th>
                                                            <th class="border-top-0">
                                                                <input type="text" class="primary_input_field form-control"
                                                                    readonly="" id="subTotalQuantityModal"
                                                                    name="subTotalQuantity" placeholder="0.00" value="{{ $itemSell->total_quantity }}" />

                                                                <input type="hidden" class="form-control"
                                                                    id="subTotalQuantityValueModal" name="subTotalQuantityValue" value="{{ $itemSell->total_quantity }}" />

                                                            </th>

                                                            <th class="border-top-0">
                                                                <input type="number"
                                                                    class="primary_input_field form-control"
                                                                    id="subTotalModal" name="subTotal" placeholder="0.00"
                                                                    readonly="" value="{{ number_format($itemSell->sell_price * $itemSell->total_quantity, 2, '.', '') }}" />

                                                                <input type="hidden" class="form-control" id="subTotalValueModal"
                                                                    name="subTotalValue" value="{{ number_format($itemSell->sell_price * $itemSell->total_quantity, 2, '.', '') }}" />
                                                            </th>
                                                            <th class="border-top-0"></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-30">
                                    <div class="col-lg-12">
                                        <div class="white-box">

                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="col-lg-12 mt-30">
                                                        <div class="primary_input">
                                                            <input type="checkbox" id="full_paid_modal"
                                                                class="common-checkbox form-control"
                                                                name="full_paid" value="1" {{ $itemSell->total_due == 0 && $itemSell->total_paid > 0 ? 'checked' : '' }}>
                                                            <label for="full_paid_modal">@lang('inventory.full_paid')</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 mt-30-md">
                                                    <div class="col-lg-12">
                                                        <div class="primary_input md_mb_20">
                                                            <label class="primary_input_label"
                                                                for="">@lang('inventory.total_paid')</label>
                                                            <input class="primary_input_field" type="number" step="0.1"
                                                                value="{{ $itemSell->total_paid }}" name="totalPaid" id="totalPaidModal"
                                                                onkeyup="paidAmountModal();">
                                                            <input type="hidden" id="totalPaidValueModal" name="totalPaidValue" value="{{ $itemSell->total_paid }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 mt-30-md">
                                                    <div class="col-lg-12">
                                                        <div class="primary_input md_mb_20">
                                                            <label class="primary_input_label"
                                                                for="">@lang('inventory.total_due')</label>
                                                            <input class="primary_input_field" type="text" name="totalDue" value="{{ $itemSell->total_due }}"
                                                                id="totalDueModal" readonly>
                                                            <input type="hidden" id="totalDueValueModal" name="totalDueValue" value="{{ $itemSell->total_due }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12 mt-20 text-center">
                                                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip" type="submit">
                                                        <span class="ti-check"></span>
                                                        @lang('common.update')
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize select2 for modal selects
        $('#item_sell_payment_method_id_modal').niceSelect();
        $('#buyer_type_modal').niceSelect();
        $('#select_class_modal').niceSelect();
        $('#select_section_modal').niceSelect();
        $('#select_student_modal').niceSelect();
        $('#selectStaffsModal').niceSelect();
        $('#productName1Modal').niceSelect();

        // Date picker for modal
        $('#sell_date_modal').datepicker({
            autoclose: true,
            setDate: new Date(),
            format: 'mm/dd/yyyy',
            orientation: "bottom auto",
        });

        // Buyer type change for modal
        $('#buyer_type_modal').on('change', function() {
            const roleId = $(this).val();
            if (roleId == 2 || roleId == 3) { // Student or Parent
                $('.forStudentWrapper').show();
                $('#selectStaffsDivModal').hide();
            } else if (roleId) { // Other roles (staff)
                $('.forStudentWrapper').hide();
                $('#selectStaffsDivModal').show();
            } else {
                $('.forStudentWrapper').hide();
                $('#selectStaffsDivModal').hide();
            }
        });

        // Initial call to set visibility based on existing data
        $('#buyer_type_modal').trigger('change');

        // Update total for a row (modal)
        window.getTotalByPriceModal = function(row) {
            const price = parseFloat($("#unit_price" + row + "Modal").val()) || 0;
            const qty = parseFloat($("#quantity" + row + "Modal").val()) || 0;
            const total = price * qty;

            $("#total" + row + "Modal").val(total.toFixed(2));
            $("#totalValue" + row + "Modal").val(total.toFixed(2));

            calculateGrandTotalModal();
        }

        window.getTotalModal = function(row) {
            getTotalByPriceModal(row);
        }

        // Calculate subtotal & grand total (modal)
        window.calculateGrandTotalModal = function() {
            let totalQty = 0,
                totalPrice = 0;

            $("input[name='quantity[]']").each(function() {
                totalQty += parseFloat($(this).val()) || 0;
            });

            $("input[name='total[]']").each(function() {
                totalPrice += parseFloat($(this).val()) || 0;
            });

            $("#subTotalQuantityModal").val(totalQty);
            $("#subTotalQuantityValueModal").val(totalQty);
            $("#subTotalModal").val(totalPrice.toFixed(2));
            $("#subTotalValueModal").val(totalPrice.toFixed(2));

            updateDueModal();
        }

        // Paid/Due calculations (modal)
        window.updateDueModal = function() {
            const total = parseFloat($("#subTotalModal").val()) || 0;
            const paid = parseFloat($("#totalPaidModal").val()) || 0;
            const due = total - paid;
            $("#totalDueModal").val(due.toFixed(2));
            $("#totalDueValueModal").val(due.toFixed(2));
        }

        window.paidAmountModal = function() {
            updateDueModal();
        }

        // Full paid checkbox (modal)
        $("#full_paid_modal").on("change", function() {
            if ($(this).is(":checked")) {
                $("#totalPaidModal").val($("#subTotalModal").val());
            } else {
                $("#totalPaidModal").val(0);
            }
            updateDueModal();
        });

        // Initial calculation on modal load
        calculateGrandTotalModal();

        // Form submission for modal
        $('#item-sell-edit-form').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            let url = $(this).attr('action');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message, 'Success');
                        $('#editItemSell').modal('hide'); // Assuming the modal has this ID
                        location.reload(); // Reload the page to see updated list
                    } else {
                        toastr.error(response.message || "Something went wrong", 'Error');
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $('#errorMessage2Modal').show();
                        $('#itemErrorModal').empty();
                        $('#priceErrorModal').empty();
                        $('#quantityErrorModal').empty();

                        $.each(xhr.responseJSON.errors, function(key, value) {
                            if (key.includes('item_id')) {
                                $('#itemErrorModal').append(`<div>${value[0]}</div>`);
                            } else if (key.includes('unit_price')) {
                                $('#priceErrorModal').append(`<div>${value[0]}</div>`);
                            } else if (key.includes('quantity')) {
                                $('#quantityErrorModal').append(`<div>${value[0]}</div>`);
                            } else {
                                toastr.error(value[0], 'Validation Error');
                            }
                        });
                    } else {
                        toastr.error("An unexpected error occurred.", 'Error');
                    }
                }
            });
        });
    });
</script>
