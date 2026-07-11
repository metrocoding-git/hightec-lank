@extends('layouts.app')
@section('title', 'Course Intake Payments Report')

@section('mainContent')
    @php
        $defaultFrom = request('date_range')
            ? explode(' to ', request('date_range'))[0]
            : \Carbon\Carbon::now()->subDays(30)->format('Y-m-d');
        $defaultTo = request('date_range')
            ? explode(' to ', request('date_range'))[1] ?? $defaultFrom
            : \Carbon\Carbon::now()->format('Y-m-d');
    @endphp

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Invoice Payment Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">Invoice Payment</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box">
                <form method="GET" action="" class="mb-20">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Date Range</label>
                            <input type="text" id="date_range" name="date_range" class="form-control"
                                value="{{ $defaultFrom }} to {{ $defaultTo }}">
                        </div>
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
                        <div class="col-lg-12 mt-20 text-right">
                            <button type="submit" class="primary-btn small fix-gr-bg">
                                <span class="ti-search pr-2"></span> Search
                            </button>
                            <button class="primary-btn small fix-gr-bg">
                                <a href="{{ route('reports.invoice-payments') }}" class="text-white">Reset</a>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title mb-15">
                            <h3>Payments Report</h3>
                        </div>

                        <style>
                            /* Main receipt row */
                            tr.receipt-main-row {
                                background: #e8f0fe;
                            }

                            tr.receipt-main-row:hover {
                                background: #d2e3fc;
                            }

                            /* Sub header label row */
                            tr.sub-header-row td {
                                background: #fff3cd;
                                font-weight: 600;
                                font-size: 0.8em;
                                color: #856404;
                                padding: 4px 8px;
                                border-top: 2px solid #ffc107;
                            }

                            /* Sub item rows */
                            tr.sub-item-row td {
                                background: #f8f9fa;
                                font-size: 0.9em;
                                border-bottom: 1px solid #e9ecef;
                            }

                            tr.sub-last-row td {
                                border-bottom: 2px solid #adb5bd !important;
                            }
                        </style>

                        <x-table>
                            <table id="table_id" class="table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th> {{-- col 0 --}}
                                        <th>Receipt No</th> {{-- col 1 --}}
                                        <th>Cheque No</th> {{-- col 2 --}}
                                        <th>Total Amount</th> {{-- col 3 --}}
                                        <th>Note</th> {{-- col 4 --}}
                                        <th>Created By</th> {{-- col 5 --}}
                                        <th>Inv. Payment No</th> {{-- col 6 --}}
                                        <th>Student Name</th> {{-- col 7 --}}
                                        <th>Reg. No</th> {{-- col 8 --}}
                                        <th>Branch</th> {{-- col 9 --}}
                                        <th>Actions</th> {{-- col 10 --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reportData as $key => $receipt)
                                        @php
                                            $firstPayment = $receipt->items->first()?->payment;
                                            $invoice = $firstPayment?->invoice;
                                            $student = $invoice?->student;
                                            $branch = $invoice?->branch;
                                            $itemCount = $receipt->items->count();
                                            $lastItemKey = $itemCount - 1;

                                            // Build the child-row data as a plain array, then JSON-encode it here
                                            $itemsJson = $receipt->items->map(
                                                fn($i) => [
                                                    'item_no' => $i->id,
                                                    'receipt_no' => $i->payment->receipt_number ?? '-',
                                                    'main_invoice_no' =>
                                                        $i->payment->invoice->main_invoice_number ?? '-',
                                                    'paid_amount' => number_format($i->paid_amount, 2),
                                                    'inv_payment_no' => $i->payment->payment_invoice_number ?? '-',
                                                ],
                                            );
                                        @endphp

                                        {{-- ══ MAIN RECEIPT ROW ══ --}}
                                        <tr class="receipt-main-row" style="cursor:pointer"
                                            data-receipt-id="{{ $receipt->id }}"
                                            data-items='{{ json_encode($itemsJson) }}'>
                                            <td>{{ $key + 1 }}</td>
                                            <td><strong>{{ $receipt->receipt_number }}</strong></td>
                                            <td>{{ $receipt->cheque_number ?? '-' }}</td>
                                            <td><strong>{{ number_format($receipt->total_amount, 2) }}</strong></td>
                                            <td>{{ $receipt->note ?? '-' }}</td>
                                            <td>{{ $receipt->user->first_name ?? '' }}
                                                {{ $receipt->user->last_name ?? '' }}</td>
                                            <td>{{ $firstPayment->payment_invoice_number ?? '-' }}</td>
                                            <td>{{ $student->student_name ?? '-' }}</td>
                                            <td>{{ $student->admission_number ?? '-' }}</td>
                                            <td>{{ $branch->name ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-xs btn-outline-primary toggle-items"
                                                    data-receipt-id="{{ $receipt->id }}">
                                                    <i class="ti-angle-down"></i>
                                                    {{ $itemCount }} item(s)
                                                </button>
                                            </td>
                                        </tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><strong>{{ $receipt->receipt_number }}</strong></td>
                                        <td>{{ $receipt->cheque_number ?? '-' }}</td>
                                        <td><strong>{{ number_format($receipt->total_amount, 2) }}</strong></td>
                                        <td>{{ $receipt->note ?? '-' }}</td>
                                        <td>{{ $receipt->user->first_name ?? '' }}
                                            {{ $receipt->user->last_name ?? '' }}</td>
                                        <td>{{ $firstPayment->payment_invoice_number ?? '-' }}</td>
                                        <td>{{ $student->student_name ?? '-' }}</td>
                                        <td>{{ $student->admission_number ?? '-' }}</td>
                                        <td>{{ $branch->name ?? '-' }}</td>
                                        <td>
                                            <button class="btn btn-xs btn-outline-primary toggle-items"
                                                data-receipt-id="{{ $receipt->id }}">
                                                <i class="ti-angle-down"></i> {{ $itemCount }} item(s)
                                            </button>
                                        </td>
                                        </tr>

                                        {{-- ══ SUB HEADER LABEL ROW ══ --}}
                                        <tr class="sub-header-row sub-group-{{ $receipt->id }}" style="display:none;">
                                            <td></td> {{-- col 0 --}}
                                            <td>Item</td> {{-- col 1 --}}
                                            <td>Payment Receipt No</td> {{-- col 2 --}}
                                            <td>Main Invoice No</td> {{-- col 3 --}}
                                            <td>Paid Amount</td> {{-- col 4 --}}
                                            <td></td> {{-- col 5 --}}
                                            <td>Inv. Payment No</td> {{-- col 6 --}}
                                            <td></td> {{-- col 7 --}}
                                            <td></td> {{-- col 8 --}}
                                            <td></td> {{-- col 9 --}}
                                            <td></td> {{-- col 10 --}}
                                        </tr>

                                        {{-- ══ SUB ITEM ROWS ══ --}}
                                        @foreach ($receipt->items as $itemKey => $item)
                                            <tr class="sub-item-row sub-group-{{ $receipt->id }} {{ $itemKey === $lastItemKey ? 'sub-last-row' : '' }}"
                                                style="display:none;">
                                                <td></td> {{-- col 0 --}}
                                                <td class="text-muted ps-3">
                                                    <i class="ti-corner-down-right"></i> #{{ $itemKey + 1 }}
                                                </td> {{-- col 1 --}}
                                                <td>{{ $item->payment->receipt_number ?? '-' }}</td> {{-- col 2 --}}
                                                <td>{{ $item->payment->invoice->main_invoice_number ?? '-' }}</td>
                                                {{-- col 3 --}}
                                                <td><strong>{{ number_format($item->paid_amount, 2) }}</strong></td>
                                                {{-- col 4 --}}
                                                <td></td> {{-- col 5 --}}
                                                <td>{{ $item->payment->payment_invoice_number ?? '-' }}</td>
                                                {{-- col 6 --}}
                                                <td></td> {{-- col 7 --}}
                                                <td></td> {{-- col 8 --}}
                                                <td></td> {{-- col 9 --}}
                                                <td></td> {{-- col 10 --}}
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </x-table>
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
            defaultDate: ["{{ $defaultFrom }}", "{{ $defaultTo }}"]
        });

        $(document).ready(function() {

            var table = $('#table_id').DataTable({
                responsive: false,
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
                scrollX: true,
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
                },

                // Keep sub-rows out of DataTables sorting/searching
                // They will be re-shown after every draw
                drawCallback: function() {
                    // Re-show any sub-rows that were open before redraw
                    $('.sub-group-open').show();
                }
            });

            // ── ACCORDION TOGGLE ──────────────────────────────────────
            var currentOpen = null; // track which receipt is open

            function formatChild(items) {
                let html = '<table class="child-item-table" style="width:100%">';
                html +=
                    '<tr class="sub-header-row"><td>Item</td><td>Payment Receipt No</td><td>Main Invoice No</td><td>Paid Amount</td><td>Inv. Payment No</td></tr>';
                items.forEach((item, idx) => {
                    html += `<tr class="sub-item-row">
            <td>#${idx + 1}</td>
            <td>${item.receipt_no}</td>
            <td>${item.main_invoice_no}</td>
            <td><strong>${item.paid_amount}</strong></td>
            <td>${item.inv_payment_no}</td>
        </tr>`;
                });
                html += '</table>';
                return html;
            }


            $(document).on('click', '.toggle-items', function(e) {
                e.stopPropagation();
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var icon = $(this).find('i');

                if (row.child.isShown()) {
                    row.child.hide();
                    icon.removeClass('ti-angle-up').addClass('ti-angle-down');
                } else {
                    var items = tr.data('items'); // parsed automatically from data-items JSON attr
                    row.child(formatChild(items)).show();
                    icon.removeClass('ti-angle-down').addClass('ti-angle-up');
                }
            });

            function openGroup(id) {
                $('.sub-group-' + id)
                    .addClass('sub-group-open')
                    .show();

                // Flip icon on the parent row's button
                $('[data-receipt-id="' + id + '"].toggle-items i')
                    .removeClass('ti-angle-down')
                    .addClass('ti-angle-up');
            }

            function closeGroup(id) {
                $('.sub-group-' + id)
                    .removeClass('sub-group-open')
                    .hide();

                $('[data-receipt-id="' + id + '"].toggle-items i')
                    .removeClass('ti-angle-up')
                    .addClass('ti-angle-down');
            }

            // ── FILE NAME FOR EXPORTS ────────────────────────────────
            function getFileName() {
                let now = new Date();
                let date = now.toISOString().slice(0, 10);
                let time = now.toTimeString().slice(0, 8).replace(/:/g, '-');
                return 'Invoice-Payments-Report_' + date + '__' + time;
            }

        });
    </script>
@endpush
