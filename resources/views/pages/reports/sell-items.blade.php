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
                                            <tr>
                                                <th>@lang('common.sl')</th>
                                                <th>@lang('inventory.reference_no')</th>
                                                <th>Item</th>
                                                <th>Item Category</th>
                                                <th>@lang('inventory.buyer_name')</th>
                                                <th>Create User</th>
                                                <th>Reg No</th>
                                                <th>Stu Category</th>
                                                <th>@lang('common.date')</th>
                                                <th>Sell Price</th>
                                                <th>@lang('inventory.total_quantity')</th>
                                                <th>@lang('inventory.grand_total')</th>

                                                <th>Total Price</th>
                                                <th>Total Qty</th>

                                                <th>Payment Method</th>
                                                <th>Bank Payment Amount</th>
                                                <th>Bank Payment Date</th>
                                                <th>Bank Payment Doc</th>
                                                <th>Invoice Number</th>
                                                <th>Note</th>

                                                <th>@lang('common.status')</th>
                                            </tr>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($itemSells))
                                                @foreach ($itemSells as $key => $value)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $value->itemSellBatch->reference_no }}</td>
                                                        <td>{{ $value->item->item_name }}</td>
                                                        <td> {{ $value->item->category->name ?? '-' }} </td>

                                                        </td>
                                                        <td>
                                                            @if ($value->itemSellBatch->role_id == 2 && $value->itemSellBatch->student)
                                                                {{-- Student Role --}}
                                                                {{ $value->itemSellBatch->student->student_name }}
                                                            @elseif ($value->itemSellBatch->role_id == 0 && $value->itemSellBatch->others_name)
                                                                {{-- Other/Customer Role --}}
                                                                {{ $value->itemSellBatch->others_name }}
                                                            @elseif ($value->itemSellBatch->role_id == 3 && $value->itemSellBatch->user)
                                                                {{-- Staff Role --}}
                                                                {{ $value->itemSellBatch->user->name }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td> {{ $value->itemSellBatch->createdBy?->name ?? '-' }}</td>
                                                        <td>
                                                            @if ($value->itemSellBatch->student)
                                                                {{ $value->itemSellBatch->student->admission_number }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($value->itemSellBatch->student)
                                                                {{ $value->itemSellBatch->student->studentCategory->category_name }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td data-sort="{{ strtotime($value->itemSellBatch->sell_date) }}">
                                                            {{ $value->itemSellBatch->sell_date ? \Carbon\Carbon::parse($value->itemSellBatch->sell_date)->format('m/d/Y') : '' }}
                                                        </td>
                                                        <td>{{ number_format((float) $value->unit_price, 2, '.', '') }}
                                                        </td>
                                                        <td>{{ $value->quantity }}</td>

                                                        <td>{{ number_format((float) $value->sub_total, 2, '.', '') }}
                                                        </td>


                                                        <td>{{ number_format((float) $value->itemSellBatch->total_sell_price, 2, '.', '') }}
                                                        </td>
                                                        <td>{{ $value->itemSellBatch->total_quantity }}</td>


                                                        <td>{{ $value->itemSellBatch->payment_method ?? '-' }}</td>
                                                        <td>
                                                            {{ $value->itemSellBatch->bank_payment_amount ? number_format((float) $value->itemSellBatch->bank_payment_amount, 2, '.', '') : '-' }}
                                                        </td>
                                                        <td>
                                                            {{ $value->itemSellBatch->bank_payment_date ? \Carbon\Carbon::parse($value->itemSellBatch->bank_payment_date)->format('m/d/Y') : '-' }}
                                                        </td>
                                                        <td>
                                                            @if ($value->itemSellBatch->bank_payment_document)
                                                                <a href="{{ asset('storage/' . $value->itemSellBatch->bank_payment_document) }}"
                                                                    target="_blank" class="text-primary">View
                                                                    Document</a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($value->itemSellBatch->invoice_number && $value->itemSellBatch->invoice_number != '')
                                                                {{ $value->itemSellBatch->invoice_number }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $value->itemSellBatch->description ?? '-' }}</td>
                                                        <td>
                                                            @if ($value->itemSellBatch->status == 'P')
                                                                @lang('inventory.paid')
                                                            @elseif($value->itemSellBatch->status == 'PP')
                                                                @lang('inventory.partial')
                                                            @elseif($value->itemSellBatch->status == 'U')
                                                                @lang('inventory.unpaid')
                                                            @elseif($value->itemSellBatch->status == 'R')
                                                                @lang('inventory.returned')
                                                            @else
                                                                @lang('inventory.N/A')
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
