{{-- <div class="modal-header">
    <h4 class="modal-title">@lang('common.cancel') @lang('inventory.item_sell')</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div> --}}

<div class="modal-body">
    <div class="text-center">
        <h4>@lang('common.are_you_sure_to_cancel')</h4>
    </div>

    <div class="mt-40 d-flex justify-content-between">
        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.no')</button>
        <form action="{{ route('cancel-item-sell', $itemSell->id) }}" method="POST">
            @csrf
            <button class="primary-btn fix-gr-bg" type="submit">@lang('common.yes')</button>
        </form>
    </div>
</div>
