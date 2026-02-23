@extends('layouts.app')
@section('title', 'Course Intake Payments Report')

@section('mainContent')
@php
    $defaultFrom = request('date_range') ? explode(' to ', request('date_range'))[0] : \Carbon\Carbon::now()->subDays(30)->format('Y-m-d');
    $defaultTo   = request('date_range') ? explode(' to ', request('date_range'))[1] ?? $defaultFrom : \Carbon\Carbon::now()->format('Y-m-d');
@endphp


    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Course Intake Payments Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">Course Intake Payments</a>
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
                                value="{{ $defaultFrom }} to {{ $defaultTo }}">
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

                        <div class="col-lg-12 mt-20 text-right">
                            <button type="submit" class="primary-btn small fix-gr-bg">
                                <span class="ti-search pr-2"></span> Search
                            </button>
                            <button class="primary-btn small fix-gr-bg">
                                <a href="{{ route('reports.course-intake-payments') }}" class="text-white">
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
                                        <th>ID</th>
                                        <th>Student No</th>
                                        <th>Branch</th>
                                        <th>Course</th>
                                        <th>Intake Start</th>
                                        <th>Status</th>
                                        <th>Payment Amount</th>
                                        <th>Payment Method</th>
                                        <th>Payment Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $data['student'] }}</td>
                                            <td>{{ $data['branch'] }}</td>
                                            <td>{{ $data['course'] }}</td>
                                            <td>{{ $data['intake_start_date'] }}</td>
                                            <td>{{ $data['status'] }}</td>
                                            <td>{{ number_format($data['payment_amount'], 2) }}</td>
                                            <td>{{ $data['payment_method'] }}</td>
                                            <td>{{ $data['payment_date'] }}</td>
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
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: true,
            defaultDate: ["{{ $defaultFrom }}", "{{ $defaultTo }}"]
        });

        $('#table_id').DataTable({
            responsive: true,
            dom: 'Blfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
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
    </script>
@endpush
