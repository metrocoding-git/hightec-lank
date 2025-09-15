@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    @push('css')
        <link rel="stylesheet" href="{{ url('Modules\Fees\Resources\assets\css\feesStyle.css') }}" />
    @endpush

    @push('css')
        <style>
            div#table_id_wrapper {
                margin-top: 40px;
            }
        </style>
    @endpush

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Fees Invoice List</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees')</a>
                    <a href="#">@lang('fees::feesModule.fees_invoice')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box">

                <div class="row">
                    <div class="col-lg-12 text-left col-md-12">
                        <a href="{{ route('fees.add-invoice') }}" class="primary-btn small fix-gr-bg">
                            <span class="ti-plus pr-2"></span>
                            @lang('common.add')
                        </a>
                    </div>
                </div>

                <div class="row mt-40">


                    <div class="col-lg-12 student-details up_admin_visitor mt-0">

                        <div class="tab-content">
                            {{-- @foreach ($records as $key => $record) --}}
                            <div role="tabpanel">
                                <x-table>
                                    <table id="table_id" class="table" cellspacing="0" width="100%">

                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>@lang('common.student')</th>
                                                <th>Branch</th>
                                                <th>@lang('admin.admission_no')</th>
                                                <th>@lang('accounts.amount')</th>
                                                <th>@lang('fees.paid')</th>
                                                <th>@lang('accounts.balance')</th>
                                                <th>Payment Method</th>
                                                <th>Payment Status</th>
                                                <th>Payment Date</th>
                                                <th>Due Date</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>


                                        <tbody>
                                            @foreach ($fee_invoices as $key => $invoice)
                                                @php
                                                    $amount = $invoice->total_amount;
                                                    $paid_amount = $invoice->paid_amount;
                                                    $balance = $amount - $paid_amount;
                                                @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        <a href="{{ route('fees.fee-invoice-view', ['id' => $invoice->id, 'state' => 'view']) }}"
                                                            target="_blank">
                                                            {{ $invoice->student->student_name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $invoice->branch->name }}</td>
                                                    <td>{{ $invoice->student->admission_number }}</td>

                                                    <td>{{ $amount }}</td>
                                                    <td>{{ $paid_amount }}</td>
                                                    <td>{{ $balance }}</td>
                                                    <td>{{ $invoice->payment_method }}</td>

                                                    <td>
                                                        @if ($invoice->payment_status == 'partial')
                                                            <button
                                                                class="primary-btn small bg-warning text-white border-0">@lang('fees.partial')</button>
                                                        @elseif ($invoice->payment_status == 'full')
                                                            <button
                                                                class="primary-btn small bg-success text-white border-0">@lang('fees.paid')</button>
                                                        @elseif ($invoice->payment_status == 'not')
                                                            <button
                                                                class="primary-btn small bg-danger text-white border-0">@lang('fees.unpaid')</button>
                                                        @endif

                                                    </td>
                                                    <td>{{ $invoice->create_date }}</td>
                                                    <td>{{ $invoice->due_date }}</td>
                                                    <td>
                                                        <x-drop-down>
                                                            <a class="dropdown-item"
                                                                href="{{ route('fees.fee-invoice-view', ['id' => $invoice->id, 'state' => 'view']) }}">@lang('common.view')</a>
                                                            @if ($invoice->payment_status != 'full')
                                                                <a class="dropdown-item" data-toggle="modal"
                                                                    data-target="#addPaymentModal{{ $invoice->id }}">
                                                                    @lang('inventory.add_payment')
                                                                </a>
                                                            @endif
                                                            <a class="dropdown-item"
                                                                href="{{ route('fees.fee-invoice-edit', $invoice->id) }}">@lang('common.edit')</a>
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#deletecategoryModal{{ $invoice->id }}"
                                                                href="#">@lang('common.delete')</a>
                                                        </x-drop-down>
                                                    </td>
                                                </tr>

                                                <div class="modal fade admin-query"
                                                    id="deletecategoryModal{{ $invoice->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">@lang('fees.delete_fees_type')</h4>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="text-center">
                                                                    <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                                </div>
                                                                <div class="mt-40 d-flex justify-content-between">
                                                                    <button type="button" class="primary-btn tr-bg"
                                                                        data-dismiss="modal">
                                                                        @lang('common.cancel')
                                                                    </button>
                                                                    <form
                                                                        action="{{ route('fees.fee-delete', $invoice->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">
                                                                            @lang('common.delete')
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Add Payment Modal -->
                                            @endforeach
                                        </tbody>
                                    </table>
                                </x-table>
                            </div>

                            <!-- Place modals here, outside table -->
                            @foreach ($fee_invoices as $invoice)
                                <div class="modal fade admin-query" id="addPaymentModal{{ $invoice->id }}">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">@lang('fees.add_payment') - Invoice #{{ $invoice->id }}
                                                </h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form action="{{ route('fees.fees-invoice-payment', $invoice->id) }}"
                                                method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <table class="table table-bordered text-center">
                                                        <thead>
                                                            <tr>
                                                                <th>@lang('fees.fee_type')</th>
                                                                <th>@lang('accounts.amount')</th>
                                                                <th>@lang('fees.paid')</th>
                                                                <th>@lang('accounts.balance')</th>
                                                                <th>@lang('fees.pay_now')</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($invoice->invoice_item as $item)
                                                                @php $balance = $item->amount - $item->paid_amount; @endphp
                                                                <tr>
                                                                    <td>{{ $item->fee_type->name ?? 'N/A' }}</td>
                                                                    <td>{{ $item->amount }}</td>
                                                                    <td>{{ $item->paid_amount }}</td>
                                                                    <td>{{ $balance }}</td>
                                                                    

                                                                    <td class="text-center">
                                                                        <div class="input-group input-group-sm"
                                                                            style="max-width: 120px;">
                                                                            <input type="number"
                                                                                name="pay_amount[{{ $item->id }}]"
                                                                                class="form-control text-center"
                                                                                min="0" max="{{ $balance }}"
                                                                                value="0"
                                                                               >
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">.00</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>

                                                    <div class="form-group mt-3">
                                                        <label>@lang('accounts.payment_method')</label>
                                                        <select name="payment_method" class="form-control" required>
                                                            <option value="Cash">Cash</option>
                                                            <option value="Card">Card</option>
                                                            <option value="Bank Transfer">Bank Transfer</option>
                                                        </select>
                                                    </div>



                                                </div>

                                                <div class="modal-footer">

                                                    <button type="submit"
                                                        class="primary-btn fix-gr-bg col-12">@lang('common.submit')</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- @endforeach --}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>


    {{-- View Fees Modal End --}}
@endsection


@include('components.partials.data_table_css')
@include('components.partials.data_table_js')


@push('script')
    <script>
        $('#table_id').DataTable({
            responsive: true, // ✅ Enable responsive extension
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            // scrollX: true, 
        });
    </script>
@endpush
