<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="ti-eye mr-2"></i>Item Details
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card bg-light p-3 rounded mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti-receipt text-primary mr-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">@lang('inventory.reference_no')</h6>
                                        <span class="text-muted">{{ $itemSell->reference_no }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card bg-light p-3 rounded mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti-user text-success mr-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">@lang('inventory.buyer_name')</h6>
                                        <span class="text-muted">
                                            @if ($itemSell->role_id == 2 && $itemSell->student)
                                                {{ $itemSell->student->student_name }}
                                            @elseif ($itemSell->role_id == 3 && $itemSell->student)
                                                {{ $itemSell->student->guardians_name ?? 'N/A' }}
                                            @elseif ($itemSell->user)
                                                {{ $itemSell->user->name }}
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card bg-light p-3 rounded mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti-package text-warning mr-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">@lang('inventory.item')</h6>
                                        <span class="text-muted">{{ $itemSell->item ? $itemSell->item->item_name : 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card bg-light p-3 rounded mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti-calendar text-info mr-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">@lang('common.date')</h6>
                                        <span class="text-muted">{{ $itemSell->sell_date ? \Carbon\Carbon::parse($itemSell->sell_date)->format('m/d/Y') : '' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-card bg-light p-3 rounded mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti-shopping-cart text-danger mr-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">@lang('inventory.total_quantity')</h6>
                                        <span class="text-muted">{{ $itemSell->total_quantity }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card bg-light p-3 rounded mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti-money text-success mr-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">@lang('inventory.sell_price')</h6>
                                        <span class="text-muted">{{ number_format($itemSell->sell_price, 2, '.', '') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card bg-light p-3 rounded mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti-credit-card text-warning mr-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">@lang('inventory.total_paid')</h6>
                                        <span class="text-muted">{{ number_format($itemSell->total_paid, 2, '.', '') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card bg-light p-3 rounded mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti-alert text-danger mr-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">@lang('inventory.total_due')</h6>
                                        <span class="text-muted">{{ number_format($itemSell->total_due, 2, '.', '') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card bg-light p-3 rounded mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti-book text-info mr-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">Class</h6>
                                        <span class="text-muted">{{ optional($itemSell->course)->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card bg-light p-3 rounded mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti-layers text-primary mr-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">Section</h6>
                                        <span class="text-muted">{{ optional($itemSell->intake)->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card bg-light p-3 rounded mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti-clipboard text-secondary mr-3" style="font-size: 24px;"></i>
                                    <div>
                                        <h6 class="mb-1">Descriptions</h6>
                                        <span class="text-muted">{{ $itemSell->description ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
