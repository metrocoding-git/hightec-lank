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
            <div class="white-box">
                <form method="GET" action="" class="mb-20">
                    <div class="row">

                        {{-- Date Range --}}
                        <div class="col-md-6">
                            <label>Date Range</label>
                            <input type="text" id="date_range" name="date_range" class="form-control"
                                value="{{ request('date_range') }}">
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-md-6">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="">All</option>
                                <option value="Cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>Cash
                                </option>
                                <option value="Bank Transfer"
                                    {{ request('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer
                                </option>
                                <option value="Invoice" {{ request('payment_method') == 'Invoice' ? 'selected' : '' }}>
                                    Invoice
                                </option>
                                {{-- <option value="Refund" {{ request('payment_method') == 'Refund' ? 'selected' : '' }}>Refund
                                </option> --}}
                            </select>
                        </div>

                        {{-- Branch --}}
                        <div class="col-md-6">
                            <label>Branch</label>
                            <select name="branch_id" class="form-control">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Fee Type --}}
                        <div class="col-md-6">
                            <label>Fee Type</label>
                            <select name="payment_type" class="form-control">
                                <option value="">All</option>
                                <option value="course_fee" {{ request('payment_type') == 'course_fee' ? 'selected' : '' }}>
                                    Course & Fee
                                </option>
                                <option value="nvq" {{ request('payment_type') == 'nvq' ? 'selected' : '' }}>NVQ</option>
                                <option value="item_sell" {{ request('payment_type') == 'item_sell' ? 'selected' : '' }}>
                                    Item Sell</option>
                                <option value="others" {{ request('payment_type') == 'others' ? 'selected' : '' }}>Others
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-12 mt-20 text-right">
                            <button type="submit" class="primary-btn small fix-gr-bg">
                                <span class="ti-search pr-2"></span> Search
                            </button>

                            {{-- RESET --}}
                            <button class="primary-btn small fix-gr-bg">
                                <a href="{{ route('reports.students-paid') }}" class="text-white">
                                    Reset
                                </a>
                            </button>

                        </div>

                    </div>
                </form>
            </div>
            <div class="row mt-20">
                <div class="col-lg-12">

                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">Payments Report</h3>
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
                                                <th>Stu No</th>
                                                <th>Stu Category</th>
                                                <th>Paid Branch</th>
                                                <th>Invoice Number</th>
                                                <th>Receipt Number</th>
                                                <th>Payment Name</th>
                                                <th>Paid Amount</th>
                                                <th>Payment Method</th>
                                                <th>Payment Date</th>
                                                <th>Payment Invoice Number</th>
                                                <th>Bank Payment Amount</th>
                                                <th>Bank Payment Date</th>
                                                <th>Note</th>
                                                <th>Create User</th>
                                                <th>Invoice Payment Status</th>
                                                <th>Invoice Payment Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($feeInvoices))
                                                @foreach ($feeInvoices as $key => $feeInvoice)
                                                    <tr>

                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ $key + 1 }}</td>
                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ $feeInvoice->invoice ? $feeInvoice->invoice->student->admission_number : 'N/A' }}
                                                        </td>
                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ $feeInvoice->invoice?->studentCategory?->category_name ??
                                                                ($feeInvoice->invoice?->student?->studentCategory?->category_name ?? 'N/A') }}
                                                        </td>
                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ $feeInvoice->branch ? $feeInvoice->branch->name : 'N/A' }}
                                                        </td>
                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ $feeInvoice->invoice ? $feeInvoice->invoice->main_invoice_number : 'N/A' }}
                                                        </td>
                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ $feeInvoice->receipt_number ?? 'N/A' }}</td>
                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ $feeInvoice->payment_type === 'course'
                                                                ? 'Course Payment - ' . ($feeInvoice->invoice->paidCourses->course->name ?? 'N/A')
                                                                : ($feeInvoice->payment_type === 'nvq'
                                                                    ? 'NVQ Payment - ' . ($feeInvoice->invoice->paidCourses->course->name ?? 'N/A')
                                                                    : ($feeInvoice->payment_type === 'fee'
                                                                        ? 'Fee Payment - ' . ($feeInvoice->invoice->invoice_item[0]->fee_type->name ?? 'N/A')
                                                                        : ($feeInvoice->payment_type === 'item_sell'
                                                                            ? 'Item Sell'
                                                                            : ($feeInvoice->amount < 0
                                                                                ? $feeInvoice->payment_type . ' Payment'
                                                                                : 'N/A')))) }}
                                                        </td>


                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ number_format((float) $feeInvoice->amount, 2, '.', '') }}
                                                        </td>
                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            @if ($feeInvoice->payment_method == 'Invoice' && $feeInvoice->invoice_payment_confirm)
                                                                Confirmed
                                                            @endif

                                                            {{ $feeInvoice->payment_method }}

                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ $feeInvoice->created_at ? \Carbon\Carbon::parse($feeInvoice->created_at)->format('m/d/Y') : 'N/A' }}
                                                        </td>
                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ $feeInvoice->payment_invoice_number ?? '-' }}</td>
                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ $feeInvoice->bank_payment_amount ?? '-' }}</td>
                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ $feeInvoice->bank_payment_date ? \Carbon\Carbon::parse($feeInvoice->bank_payment_date)->format('m/d/Y') : '-' }}
                                                        </td>
                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ $feeInvoice->notes ?? '-' }}</td>
                                                        <td class="{{ $feeInvoice->amount < 0 ? 'text-danger' : '' }}">
                                                            {{ $feeInvoice->invoice && $feeInvoice->invoice->createUser ? $feeInvoice->invoice->createUser->name : 'N/A' }}
                                                        </td>

                                                        <td>
                                                            {{ $feeInvoice->payment_method == 'Invoice'
                                                                ? ($feeInvoice->invoice_payment_confirm
                                                                    ? 'Completed'
                                                                    : ($feeInvoice->payment_invoice_number != null
                                                                        ? 'Pending'
                                                                        : 'Not Open'))
                                                                : '-' }}
                                                        </td>
                                                        <td>{{ $feeInvoice->payment_method == 'Invoice' ? $feeInvoice->invoice_payment_amount : '-' }}
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
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: true,
        });
    </script>

    <script>
        $('#table_id').DataTable({
            responsive: true,
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'copy',
                    title: getFileName()
                },
                {
                    extend: 'csv',
                    title: getFileName()
                },
                {
                    extend: 'excel',
                    title: getFileName()
                },
                {
                    extend: 'pdf',
                    title: getFileName()
                },
                {
                    extend: 'print',
                    title: getFileName()
                }
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            order: [
                [0, 'asc']
            ],
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],


            pageLength: 25,

            language: {
                search: '<i class="fa-solid fa-magnifying-glass me-2"></i>',
                searchPlaceholder: 'Search...',
                paginate: {
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            }

        });

        function getFileName() {
            let now = new Date();

            let date = now.toISOString().slice(0, 10);
            let time = now.toTimeString().slice(0, 8).replace(/:/g, '-'); // HH-MM-SS

            return 'Students-Payment-Report_' + date + '__' + time;
        }
    </script>
@endpush
