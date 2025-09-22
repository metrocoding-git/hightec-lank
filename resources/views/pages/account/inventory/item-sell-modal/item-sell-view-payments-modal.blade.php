{{-- <div class="modal-header">
    <h4 class="modal-title">@lang('inventory.view_payment') History</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div> --}}

<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="white-box">
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-bordered">
                            <tr>
                                <th>@lang('inventory.reference_no')</th>
                                <td>{{ $itemSell->reference_no }}</td>
                            </tr>
                            <tr>
                                <th>@lang('inventory.total_paid')</th>
                                <td>{{ number_format($itemSell->total_paid, 2, '.', '') }}</td>
                            </tr>
                            <tr>
                                <th>@lang('inventory.total_due')</th>
                                <td>{{ number_format($itemSell->total_due, 2, '.', '') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                {{-- Example of how payment history would be displayed if available: --}}
                {{-- @if ($itemSell->payments->count() > 0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($itemSell->payments as $key => $payment)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ number_format($payment->amount, 2, '.', '') }}</td>
                                    <td>{{ $payment->payment_method }}</td>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('m/d/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="mt-30">No payment history found for this item sell.</p>
                @endif --}}
            </div>
        </div>
    </div>
</div>
