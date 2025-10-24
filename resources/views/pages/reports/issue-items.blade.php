@extends('layouts.app')
@section('title')
    Issue Items Report
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Issue Items Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">Issue Items</a>
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
                                    <h3 class="mb-15">Issue Items Report</h3>
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
                                                <th>Issue To</th>
                                                <th>Student Name</th>
                                                <th>Issue Date</th>
                                                <th>Due Date</th>
                                                <th>Total Items</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($itemIssues))
                                                @foreach ($itemIssues as $key => $itemIssue)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>
                                                            @if ($itemIssue->student)
                                                                Student
                                                            @elseif ($itemIssue->staff)
                                                                Staff
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($itemIssue->student)
                                                                {{ $itemIssue->student->student_name }}
                                                            @elseif ($itemIssue->staff)
                                                                {{ $itemIssue->staff->name }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td>{{ $itemIssue->issue_date ? \Carbon\Carbon::parse($itemIssue->issue_date)->format('m/d/Y') : '' }}</td>
                                                        <td>{{ $itemIssue->due_date ? \Carbon\Carbon::parse($itemIssue->due_date)->format('m/d/Y') : '' }}</td>
                                                        <td>{{ $itemIssue->total_items }}</td>
                                                        <td>{{ $itemIssue->status_text }}</td>
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
