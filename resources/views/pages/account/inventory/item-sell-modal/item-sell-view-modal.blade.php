{{-- <div class="modal-header">
    <h4 class="modal-title">@lang('inventory.view_payment')</h4>
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
                                <th>@lang('inventory.reference_no') </th>
                                <td>{{ $itemSell->reference_no }}</td>
                            </tr>
                            <tr>
                                <th>@lang('inventory.buyer_name')</th>
                                <td>
                                    @if ($itemSell->role_id == 2 && $itemSell->student)
                                        {{ $itemSell->student->student_name }}
                                    @elseif ($itemSell->role_id == 3 && $itemSell->student)
                                        {{ $itemSell->student->guardians_name ?? 'N/A' }}
                                    @elseif ($itemSell->user)
                                        {{ $itemSell->user->name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>@lang('inventory.item')</th>
                                <td>{{ $itemSell->item ? $itemSell->item->item_name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>@lang('inventory.total_quantity')</th>
                                <td>{{ $itemSell->total_quantity }}</td>
                            </tr>
                            <tr>
                                <th>@lang('inventory.sell_price')</th>
                                <td>{{ number_format($itemSell->sell_price, 2, '.', '') }}</td>
                            </tr>
                            <tr>
                                <th>@lang('inventory.total_paid')</th>
                                <td>{{ number_format($itemSell->total_paid, 2, '.', '') }}</td>
                            </tr>
                            <tr>
                                <th>@lang('inventory.total_due')</th>
                                <td>{{ number_format($itemSell->total_due, 2, '.', '') }}</td>
                            </tr>
                            <tr>
                                <th>@lang('common.date')</th>
                                <td>{{ $itemSell->sell_date ? \Carbon\Carbon::parse($itemSell->sell_date)->format('m/d/Y') : '' }}</td>
                            </tr>
                            <tr>
                                <th>Class</th>
                                <td>{{ $itemSell->course->name }}</td>
                            </tr>
                            <tr>
                                <th>Section</th>
                                <td>{{ $itemSell->intake->name }}</td>
                            </tr>
                            <tr>
                                <th>Descriptions</th>
                                <td>{{ $itemSell->description }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                {{-- Assuming there's a payments relationship on ItemSell model --}}
                {{-- @if ($itemSell->payments->count() > 0)
                    <h4 class="mt-30 mb-20">Payment History</h4>
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
