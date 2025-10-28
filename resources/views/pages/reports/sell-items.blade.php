@extends('layouts.app')
@section('title')
    Sell Items Report
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Sell Items Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">Sell Items</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">Sell Items Report</h3>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id" class="table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Item Name</th>
                                                <th>Reference No</th>
                                                <th>Buyer Name</th>
                                                <th>Sell Date</th>
                                                <th>Sell Price</th>
                                                <th>Quantity</th>
                                                <th>Total Paid</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($itemSells))
                                                @foreach ($itemSells as $key => $itemSell)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $itemSell->item ? $itemSell->item->item_name : 'N/A' }}</td>
                                                        <td>{{ $itemSell->itemSellBatch->reference_no }}</td>
                                                        <td>
                                                            @if ($itemSell->itemSellBatch->role_id == 2 && $itemSell->itemSellBatch->student)
                                                                {{ $itemSell->itemSellBatch->student->student_name }}
                                                            @elseif ($itemSell->itemSellBatch->role_id == 0 && $itemSell->itemSellBatch->others_name)
                                                                {{ $itemSell->itemSellBatch->others_name }}
                                                            @elseif ($itemSell->itemSellBatch->role_id == 3 && $itemSell->itemSellBatch->user)
                                                                {{ $itemSell->itemSellBatch->user->name }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td>{{ $itemSell->itemSellBatch->sell_date ? \Carbon\Carbon::parse($itemSell->itemSellBatch->sell_date)->format('m/d/Y') : '' }}</td>
                                                        <td>{{ number_format((float) $itemSell->unit_price, 2, '.', '') }}</td>
                                                        <td>{{ $itemSell->quantity }}</td>
                                                        <td>{{ number_format((float) $itemSell->itemSellBatch->total_paid, 2, '.', '') }}</td>
                                                        <td>
                                                            @if ($itemSell->itemSellBatch->status == 'P')
                                                                Paid
                                                            @elseif($itemSell->itemSellBatch->status == 'PP')
                                                                Partial
                                                            @elseif($itemSell->itemSellBatch->status == 'U')
                                                                Unpaid
                                                            @elseif($itemSell->itemSellBatch->status == 'R')
                                                                Returned
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </x-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@include('components.partials.data_table_js')
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')
@push('script')
    <script>
        $('#table_id').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
        });
    </script>
@endpush
