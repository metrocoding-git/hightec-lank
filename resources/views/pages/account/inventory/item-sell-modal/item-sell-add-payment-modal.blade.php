{{-- <div class="modal-header">
    <h4 class="modal-title">@lang('inventory.add_payment')</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div> --}}

<div class="modal-body">
    <form action="{{ route('item-sell-payments.store', $itemSell->id) }}" method="POST" id="add-payment-form">
        @csrf
        <div class="row">
            <div class="col-lg-12 mb-30">
                <div class="primary_input">
                    <label class="primary_input_label" for="amount">@lang('inventory.amount') <span class="text-danger">*</span></label>
                    <input class="primary_input_field form-control" type="number" step="0.01" name="amount" id="amount" value="{{ $itemSell->total_due }}" required>
                </div>
            </div>
            <div class="col-lg-12 mb-30">
                <label class="primary_input_label" for="payment_method">@lang('inventory.payment_method') <span class="text-danger">*</span></label>
                <select class="primary_select form-control" name="payment_method" id="payment_method" required>
                    <option value="">@lang('common.select')</option>
                    <option value="Cash">Cash</option>
                    <option value="Cheque">Cheque</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>
            <div class="col-lg-12 mb-30">
                <div class="primary_input ">
                    <label class="primary_input_label" for="payment_date">@lang('common.date') <span></span> </label>
                    <div class="primary_datepicker_input">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="">
                                    <input class="primary_input_field  primary_input_field date form-control"
                                        id="payment_date" type="text" name="payment_date"
                                        value="{{ date('m/d/Y') }}" autocomplete="off" required>
                                </div>
                            </div>
                            <button class="btn-date" data-id="#payment_date" type="button">
                                <label for="payment_date" class="m-0 p-0">
                                    <i class="ti-calendar" id="start-date-icon"></i>
                                </label>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 text-center">
                <button class="primary-btn fix-gr-bg" type="submit">@lang('common.add_payment')</button>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#payment_method').niceSelect();
        $('#payment_date').datepicker({
            autoclose: true,
            setDate: new Date(),
            format: 'mm/dd/yyyy',
            orientation: "bottom auto",
        });

        $('#add-payment-form').on('submit', function(e) {
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
                        $('.modal').modal('hide');
                        location.reload();
                    } else {
                        toastr.error(response.message || "Something went wrong", 'Error');
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            toastr.error(value[0], 'Validation Error');
                        });
                    } else {
                        toastr.error("An unexpected error occurred.", 'Error');
                    }
                }
            });
        });
    });
</script>
