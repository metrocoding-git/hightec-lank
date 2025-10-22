<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="card-title mb-0">
                        <i class="ti-credit-card mr-2"></i>@lang('inventory.payment_details')
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="payment-summary bg-light p-4 rounded mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="ti-receipt text-primary mr-3" style="font-size: 32px;"></i>
                                        <div>
                                            <h5 class="mb-1">@lang('inventory.reference_no')</h5>
                                            <span class="text-muted">{{ $itemSell->reference_no }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="payment-summary bg-light p-4 rounded mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="ti-money text-success mr-3" style="font-size: 32px;"></i>
                                        <div>
                                            <h5 class="mb-1">@lang('inventory.total_paid')</h5>
                                            <span class="text-muted">{{ number_format($itemSell->total_paid, 2, '.', '') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="payment-summary bg-light p-4 rounded mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="ti-alert text-danger mr-3" style="font-size: 32px;"></i>
                                        <div>
                                            <h5 class="mb-1">@lang('inventory.total_due')</h5>
                                            <span class="text-muted">{{ number_format($itemSell->total_due, 2, '.', '') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="payment-summary bg-light p-4 rounded mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="ti-bar-chart text-info mr-3" style="font-size: 32px;"></i>
                                        <div>
                                            <h5 class="mb-1">@lang('inventory.grand_total')</h5>
                                            <span class="text-muted">{{ number_format($itemSell->total_paid + $itemSell->total_due, 2, '.', '') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Status Badge --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center mb-3">
                                @if ($itemSell->total_due == 0 && $itemSell->total_paid > 0)
                                    <span class="badge badge-success" style="font-size: 16px; padding: 10px 20px;">@lang('inventory.paid')</span>
                                @elseif ($itemSell->total_paid > 0 && $itemSell->total_due > 0)
                                    <span class="badge badge-warning" style="font-size: 16px; padding: 10px 20px;">@lang('inventory.partial')</span>
                                @elseif ($itemSell->total_paid == 0 && $itemSell->total_due > 0)
                                    <span class="badge badge-danger" style="font-size: 16px; padding: 10px 20px;">@lang('inventory.unpaid')</span>
                                @else
                                    <span class="badge badge-info" style="font-size: 16px; padding: 10px 20px;">@lang('inventory.N/A')</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Example of how payment history would be displayed if available: --}}
                    {{-- @if ($itemSell->payments->count() > 0)
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mt-4 mb-3">@lang('inventory.payment_history')</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead class="thead-dark">
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
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info text-center mt-4">
                                    <i class="ti-info-alt mr-2"></i>No payment history found for this item sell.
                                </div>
                            </div>
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>
</div>
