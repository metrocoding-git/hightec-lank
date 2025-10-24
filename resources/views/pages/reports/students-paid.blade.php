@extends('layouts.app')
@section('title')
    Students Paid Report
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Students Paid Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">Students Paid</a>
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
                                    <h3 class="mb-15">Students Paid Report</h3>
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
                                                <th>Student Name</th>
                                                <th>Invoice Number</th>
                                                <th>Paid Amount</th>
                                                <th>Due Amount</th>
                                                <th>Payment Status</th>
                                                <th>Payment Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($feeInvoices))
                                                @foreach ($feeInvoices as $key => $feeInvoice)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $feeInvoice->student ? $feeInvoice->student->student_name : 'N/A' }}</td>
                                                        <td>{{ $feeInvoice->main_invoice_number ?? 'N/A' }}</td>
                                                        <td>{{ number_format((float) $feeInvoice->paid_amount, 2, '.', '') }}</td>
                                                        <td>{{ number_format((float) $feeInvoice->due_amount, 2, '.', '') }}</td>
                                                        <td>{{ $feeInvoice->payment_status }}</td>
                                                        <td>{{ $feeInvoice->create_date ? \Carbon\Carbon::parse($feeInvoice->paid_date)->format('m/d/Y') : '' }}</td>
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
