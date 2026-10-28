@extends('layouts.app')
@section('title', 'Item Stock Balance Report')

@section('mainContent')
    @push('css')
        <style>
            .white-box { border-radius: 14px; box-shadow: 0 2px 14px rgba(20, 20, 43, 0.05); border: 1px solid #eceef3; }
            .stock-table thead tr th { background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%); color: #fff; font-weight: 600; white-space: nowrap; border: none; }
            .stock-table td { vertical-align: middle; }
            .stock-table .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
            .stock-table .in { color: #1e7a43; }
            .stock-table .out { color: #a12a2a; }
            .stock-table tfoot td { font-weight: 700; background: #f7f8fb; }
            .stock-table .neg { color: #a12a2a; font-weight: 700; }
            .branch-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin: 6px 0 12px; }
            .branch-head h4 { margin: 0; font-weight: 700; font-size: 16px; }
            .branch-chip { font-size: 12px; color: #6b7784; background: #f4f6f8; border-radius: 999px; padding: 3px 12px; }
            .stock-note { font-size: 12px; color: #6b7784; }
            .movement-box { border: 2px solid color-mix(in srgb, var(--gradient_1) 35%, #fff); }
        </style>
    @endpush

    @php
        $canExport = Auth::user()->hasPermission('reports.item-stock.export');
        $baseQuery = request()->except(['movement_item_id', 'movement_branch_id', 'page']);
    @endphp

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Item Stock Balance Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">Item Stock Balance</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            @if ($canExport)
                <div class="white-box mb-10">
                    <a href="{{ route('reports.item-stock.export', $baseQuery) }}"
                        class="primary-btn small fix-gr-bg col-12 p-2">
                        <span class="ti-download pr-2"></span> Export Stock Balance CSV
                    </a>
                </div>
            @endif

            <div class="white-box p-20 mb-20">
                <form method="GET" action="{{ route('reports.item-stock') }}" class="mb-0">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="small text-muted mb-1">Period</label>
                            <input type="text" id="date_range" name="date_range" class="form-control"
                                placeholder="Select Date Range"
                                value="{{ request('date_range', $from->toDateString() . ' to ' . $to->toDateString()) }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small text-muted mb-1">Branch</label>
                            <select name="branch_id" class="form-control primary_select">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small text-muted mb-1">Category</label>
                            <select name="category_id" class="form-control primary_select">
                                <option value="">All Categories</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small text-muted mb-1">Item</label>
                            <select name="item_id" class="form-control primary_select">
                                <option value="">All Items</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->item_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-2">
                            <input type="hidden" name="hide_zero" value="0">
                            <label class="mb-0 small">
                                <input type="checkbox" name="hide_zero" value="1"
                                    {{ request('hide_zero', '1') == '1' ? 'checked' : '' }}>
                                Hide items with no stock and no movement in this period
                            </label>
                        </div>
                        <div class="col-12 mt-10">
                            <div class="row align-items-center">
                                <div class="col-10">
                                    <button type="submit" class="primary-btn small fix-gr-bg col-12 p-2">
                                        <span class="ti-search pr-2"></span> Search
                                    </button>
                                </div>
                                <div class="col-2">
                                    <a href="{{ route('reports.item-stock') }}"
                                        class="primary-btn small fix-gr-bg col-12 p-2 text-white text-center d-block">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ===== Item movement (drill-down) ===== --}}
            @if ($movement)
                <div class="white-box movement-box mb-20" id="movement">
                    <div class="branch-head">
                        <h4><i class="fa-solid fa-right-left mr-1"></i> Movement — {{ $movement['item_name'] }}
                            <span class="branch-chip">{{ $movement['branch_name'] }}</span></h4>
                        <div>
                            @if ($canExport)
                                <a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('reports.item-stock.export', request()->query()) }}">
                                    <i class="fa-solid fa-download"></i> Export movement</a>
                            @endif
                            <a class="btn btn-sm btn-outline-secondary"
                                href="{{ route('reports.item-stock', $baseQuery) }}">Close</a>
                        </div>
                    </div>
                    <x-table>
                        <table class="table stock-table" width="100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th class="num">In</th>
                                    <th class="num">Out</th>
                                    <th class="num">Balance</th>
                                    <th>Reference</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $from->toDateString() }}</td>
                                    <td><strong>Opening balance</strong></td>
                                    <td></td>
                                    <td></td>
                                    <td class="num"><strong>{{ number_format($movement['opening']) }}</strong></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @forelse ($movement['rows'] as $m)
                                    <tr>
                                        <td>{{ $m['date'] }}</td>
                                        <td>{{ $m['type'] }}</td>
                                        <td class="num in">{{ $m['in'] ? number_format($m['in']) : '' }}</td>
                                        <td class="num out">{{ $m['out'] ? number_format($m['out']) : '' }}</td>
                                        <td class="num {{ $m['balance'] < 0 ? 'neg' : '' }}">{{ number_format($m['balance']) }}</td>
                                        <td>{{ $m['reference'] }}</td>
                                        <td>{{ $m['description'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No movement in this period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>{{ $to->toDateString() }}</td>
                                    <td>Closing balance</td>
                                    <td class="num in">{{ number_format($movement['total_in']) }}</td>
                                    <td class="num out">{{ number_format($movement['total_out']) }}</td>
                                    <td class="num {{ $movement['closing'] < 0 ? 'neg' : '' }}">{{ number_format($movement['closing']) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </x-table>
                </div>
            @endif

            @php $driftCount = $rows->filter(fn($r) => !is_null($r['system_balance']) && $r['system_balance'] != $r['closing'])->count(); @endphp
            @if ($driftCount > 0)
                <div class="alert alert-warning mb-20">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    {{ $driftCount }} item(s) have a stored system balance that doesn't match their movements (marked
                    <i class="fa-solid fa-triangle-exclamation text-warning"></i>). Closing here is calculated from the
                    movements; open "Movement" on those items to check.
                </div>
            @endif

            {{-- ===== Stock balance, one table per branch ===== --}}
            @forelse ($byBranch as $branchName => $branchRows)
                <div class="white-box mb-20">
                    <div class="branch-head">
                        <h4><i class="fa-solid fa-warehouse mr-1"></i> {{ $branchName }}</h4>
                        <span class="branch-chip">{{ $from->toDateString() }} → {{ $to->toDateString() }} ·
                            {{ $branchRows->count() }} item(s)</span>
                    </div>
                    <x-table>
                        <table class="table stock-table" width="100%">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Item</th>
                                    <th class="num">Opening</th>
                                    <th class="num">Received</th>
                                    <th class="num">Issue Returned</th>
                                    <th class="num">Sell Returned</th>
                                    <th class="num">Issued</th>
                                    <th class="num">Sold</th>
                                    <th class="num">Closing</th>
                                    <th class="num">Closing Value (Rs.)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($branchRows as $r)
                                    <tr>
                                        <td>{{ $r['category_name'] }}</td>
                                        <td>{{ $r['item_name'] }}</td>
                                        <td class="num">{{ number_format($r['opening']) }}</td>
                                        <td class="num in">{{ $r['receive'] ? number_format($r['receive']) : '-' }}</td>
                                        <td class="num in">{{ $r['issue_return'] ? number_format($r['issue_return']) : '-' }}</td>
                                        <td class="num in">{{ $r['sell_return'] ? number_format($r['sell_return']) : '-' }}</td>
                                        <td class="num out">{{ $r['issue'] ? number_format($r['issue']) : '-' }}</td>
                                        <td class="num out">{{ $r['sell'] ? number_format($r['sell']) : '-' }}</td>
                                        <td class="num {{ $r['closing'] < 0 ? 'neg' : '' }}"><strong>{{ number_format($r['closing']) }}</strong>
                                            @if (!is_null($r['system_balance']) && $r['system_balance'] != $r['closing'])
                                                <i class="fa-solid fa-triangle-exclamation text-warning"
                                                    title="System stock balance shows {{ $r['system_balance'] }} — it does not match the movements ({{ $r['closing'] }}). Check this item's movement."></i>
                                            @endif
                                        </td>
                                        <td class="num">{{ number_format($r['closing_value'], 2) }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-primary text-nowrap"
                                                href="{{ route('reports.item-stock', array_merge($baseQuery, ['movement_item_id' => $r['item_id'], 'movement_branch_id' => $r['branch_id'] ?? 'none'])) }}#movement">
                                                <i class="fa-solid fa-right-left"></i> Movement</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Total</td>
                                    <td class="num">{{ number_format($branchRows->sum('opening')) }}</td>
                                    <td class="num in">{{ number_format($branchRows->sum('receive')) }}</td>
                                    <td class="num in">{{ number_format($branchRows->sum('issue_return')) }}</td>
                                    <td class="num in">{{ number_format($branchRows->sum('sell_return')) }}</td>
                                    <td class="num out">{{ number_format($branchRows->sum('issue')) }}</td>
                                    <td class="num out">{{ number_format($branchRows->sum('sell')) }}</td>
                                    <td class="num">{{ number_format($branchRows->sum('closing')) }}</td>
                                    <td class="num">{{ number_format($branchRows->sum('closing_value'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </x-table>
                </div>
            @empty
                <div class="white-box p-20 mb-20 text-center text-muted">No stock records for these filters.</div>
            @endforelse
        </div>
    </section>
@endsection

@include('components.partials.date_picker_css_js')
@push('script')
    <script>
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: true,
        });
    </script>
@endpush
