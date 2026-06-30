@extends('layouts.app')
@section('title', 'Students Intake Balance Report')

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
                <h1>Students Intake Balance Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">Students Intake Balance</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box">
                <form method="GET" action="" class="mb-20" id="filter-form">
                    <div class="row">
                        {{-- Notice --}}
                        <div class="col-md-12 mt-2">
                            <small class="text-danger">
                                <i class="ti-info-alt"></i>
                                <strong>Group A</strong> (To Date + Branch) and <strong>Group B</strong> (Intake) cannot be
                                used together.
                                Selecting one group clears the other.
                            </small>
                        </div>

                        {{-- GROUP A: To Date + Branch --}}
                        <div class="col-md-4">
                            <label>To Date <small class="text-muted">(Group A)</small></label>
                            <input type="text" id="to_date" name="to_date" class="form-control group-a"
                                value="{{ request('to_date') }}" placeholder="Select to date">
                        </div>

                        <div class="col-md-4">
                            <label>Branch <small class="text-muted">(Group A)</small></label>
                            <select name="branch_id" class="form-control group-a">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- GROUP B: Intake --}}
                        <div class="col-md-4">
                            <label>Intake <small class="text-muted">(Group B)</small></label>
                            <select class="primary_select form-control group-b" name="intake_id" id="search_intake_id">
                                <option value="">Select Intake</option>
                                @foreach ($intakes as $intake)
                                    <option value="{{ $intake->id }}"
                                        {{ request('intake_id') == $intake->id ? 'selected' : '' }}>
                                        {{ $intake->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        <div class="col-lg-12 mt-20 text-right">
                            <button type="submit" class="primary-btn small fix-gr-bg">
                                <span class="ti-search pr-2"></span> Search
                            </button>
                            <button class="primary-btn small fix-gr-bg">
                                <a href="{{ route('reports.students-intake-balance') }}" class="text-white">
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
                        <div class="main-title mb-15">
                            <h3>Payments Report</h3>
                        </div>
                        <x-table>
                            <table id="table_id" class="table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Intake</th>
                                        <th>Course</th>
                                        <th>Total Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>

                                <tfoot>
                                    <tr>
                                        <th colspan="3" style="text-align:right">Total:</th>
                                        <th id="total_amount_footer"></th>
                                        <th id="paid_amount_footer"></th>
                                        <th id="balance_amount_footer"></th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($reportData as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $data->intake_name }}</td>
                                            <td>{{ $data->course_name }}</td>
                                            <td>{{ number_format($data->total_amount, 2) }}</td>
                                            <td>{{ number_format($data->paid_amount, 2) }}</td>
                                            <td>{{ number_format($data->balance_amount, 2) }}</td>
                                        </tr>
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
        flatpickr("#to_date", {
            mode: "single",
            dateFormat: "Y-m-d",
            allowInput: true,
            defaultDate: "{{ request('to_date') }}" 
        });

        // Mutual exclusivity between Group A and Group B
        $('.group-a').on('change input', function() {
            var hasGroupA = $('#to_date').val() !== '' || $('[name="branch_id"]').val() !== '';
            if (hasGroupA) {
                $('#search_intake_id').val('').trigger('change').prop('disabled', true);
            } else {
                $('#search_intake_id').prop('disabled', false);
            }
        });

        $('.group-b').on('change', function() {
            var hasGroupB = $(this).val() !== '';
            if (hasGroupB) {
                $('#to_date').val('');
                $('[name="branch_id"]').val('');
                $('.group-a').prop('disabled', true);
            } else {
                $('.group-a').prop('disabled', false);
            }
        });

        // On page load, restore disabled state based on which group has a value
        $(document).ready(function() {
            var hasIntake = $('#search_intake_id').val() !== '';
            var hasGroupA = $('#to_date').val() !== '' || $('[name="branch_id"]').val() !== '';

            if (hasIntake) {
                $('.group-a').prop('disabled', true);
            } else if (hasGroupA) {
                $('#search_intake_id').prop('disabled', true);
            }
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
                }
            });

            function getFileName() {
                let now = new Date();

                let date = now.toISOString().slice(0, 10);
                let time = now.toTimeString().slice(0, 8).replace(/:/g, '-'); // HH-MM-SS

                return 'Students-Intake-Balance-Report_' + date + '__' + time;
            }

            table.on('length.dt page.dt search.dt order.dt draw.dt', function() {
                updateFooter();
            });

            // ============ SUM FOOTER ============
            function updateFooter() {
                var totalAmount = 0;
                var paidAmount = 0;
                var balanceAmount = 0;

                $('#table_id tbody tr').each(function() {
                    var totalText = $(this).find('td:eq(3)').text().trim();
                    var paidText = $(this).find('td:eq(4)').text().trim();
                    var balanceText = $(this).find('td:eq(5)').text().trim();

                    // remove commas
                    totalText = totalText.replace(/,/g, '');
                    paidText = paidText.replace(/,/g, '');
                    balanceText = balanceText.replace(/,/g, '');

                    totalAmount += parseFloat(totalText) || 0;
                    paidAmount += parseFloat(paidText) || 0;
                    balanceAmount += parseFloat(balanceText) || 0;
                });

                $('#total_amount_footer').html(totalAmount.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                $('#paid_amount_footer').html(paidAmount.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));

                $('#balance_amount_footer').html(balanceAmount.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }

            // initial calculation
            updateFooter();

            // recalc on search, paging, ordering
            table.on('search.dt page.dt order.dt', function() {
                updateFooter();
            });
        });
    </script>
@endpush
