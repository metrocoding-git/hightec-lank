@extends('layouts.app')
@section('title')
    @lang('fees.fees_invoice_report')
@endsection
@section('mainContent')
    @push('css')
        <link rel="stylesheet" href="{{ url('Modules\Fees\Resources\assets\css\feesStyle.css') }}" />
        <style>
            div#table_id_wrapper {
                margin-top: 40px;
            }
            .report-header {
                background-color: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
            }
            .report-title {
                font-size: 24px;
                font-weight: bold;
                color: #333;
            }
            .report-date {
                font-size: 14px;
                color: #666;
            }
        </style>
    @endpush

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Fees Invoice Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees')</a>
                    <a href="#">@lang('fees::feesModule.fees_invoice_report')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box">
                <div class="report-header">
                    <div class="report-title">Detailed Invoice Report</div>
                    <div class="report-date">Generated on: {{ date('Y-m-d H:i:s') }}</div>
                </div>

                <div class="row">
                    <div class="col-lg-12 text-left col-md-12">
                        <a href="{{ route('fees.fee-invoice') }}" class="primary-btn small bg-secondary text-white border-0">
                            <span class="ti-arrow-left pr-2"></span>
                            Back to Invoice List
                        </a>
                    </div>
                </div>

                <div class="row mt-40">
                    <div class="col-lg-12 student-details up_admin_visitor mt-0">
                        <div class="tab-content">
                            <div role="tabpanel">
                                <x-table>
                                    <table id="table_id" class="table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Main Invoice No.</th>
                                                <th>@lang('common.student')</th>
                                                <th>Branch</th>
                                                <th>@lang('admin.admission_no')</th>
                                                <th>@lang('accounts.amount')(Rs)</th>
                                                <th>@lang('fees.paid')(Rs)</th>
                                                <th>@lang('accounts.balance')(Rs)</th>
                                                <th>Payment Method</th>
                                                <th>Payment Status</th>
                                                <th>Payment Date</th>
                                                <th>Due Date</th>
                                                <th>Created Date</th>
                                                <th>Invoice Items</th>
                                                <th>Payment History</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($fee_invoices as $key => $invoice)
                                                @php
                                                    $amount = $invoice->total_amount;
                                                    $paid_amount = $invoice->paid_amount;
                                                    $balance = $amount - $paid_amount;
                                                    $invoice_items = $invoice->invoice_item->pluck('fee_type.name', 'amount')->toArray();
                                                    $payment_history = $invoice->payments->pluck('receipt_number', 'amount')->toArray();
                                                @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $invoice->main_invoice_number }}</td>
                                                    <td>{{ $invoice->student->student_name }}</td>
                                                    <td>{{ $invoice->branch->name }}</td>
                                                    <td>{{ $invoice->student->admission_number }}</td>
                                                    <td>{{ number_format($amount, 2) }}</td>
                                                    <td>{{ number_format($paid_amount, 2) }}</td>
                                                    <td>{{ number_format($balance, 2) }}</td>
                                                    <td>{{ $invoice->payment_method }}</td>
                                                    <td>
                                                        @if ($invoice->payment_status == 'partial')
                                                            <span class="badge badge-warning">@lang('fees.partial')</span>
                                                        @elseif ($invoice->payment_status == 'full')
                                                            <span class="badge badge-success">@lang('fees.paid')</span>
                                                        @elseif ($invoice->payment_status == 'returned')
                                                            <span class="badge badge-info">Returned</span>
                                                        @elseif ($invoice->payment_status == 'not')
                                                            <span class="badge badge-danger">@lang('fees.unpaid')</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $invoice->create_date }}</td>
                                                    <td>{{ $invoice->due_date }}</td>
                                                    <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                                    <td>
                                                        @if($invoice_items)
                                                            <ul>
                                                                @foreach($invoice_items as $item => $name)
                                                                    <li>{{ $name }}: {{ number_format($item, 2) }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($payment_history)
                                                            <ul>
                                                                @foreach($payment_history as $payment => $receipt)
                                                                    <li>{{ number_format($payment, 2) }} ({{ $receipt }})</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            No payments
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
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

@include('components.partials.data_table_css')
@include('components.partials.data_table_js')

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
