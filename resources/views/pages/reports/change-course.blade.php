@extends('layouts.app')
@section('title', 'Change Course Report')

@section('mainContent')

<section class="sms-breadcrumb mb-20">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>Change Course Report</h1>
            <div class="bc-pages">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="#">Reports</a>
                <a href="#">Change Course</a>
            </div>
        </div>
    </div>
</section>

<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">

        <div class="row mt-20">
            <div class="col-lg-12">
                <div class="white-box">
                    <div class="main-title mb-15">
                        <h3>Changed Courses List</h3>
                    </div>

                    <x-table>
                        <table id="table_id" class="table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student No</th>
                                    <th>Student Name</th>
                                    <th>Old Course</th>
                                    <th>New Course</th>
                                    <th>Intake</th>
                                    <th>Branch</th>
                                    <th>Changed By</th>
                                    <th>Changed Date</th>

                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($data as $key => $row)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $row->admission_number }}</td>
                                        <td>{{ $row->student_name }}</td>
                                        <td>{{ $row->old_course_name }}</td>
                                        <td>{{ $row->new_course_name }}</td>
                                        <td>{{ $row->intake_name }}</td>
                                        <td>{{ $row->branch_name ?? '-' }}</td>
                                        <td>{{ $row->changed_by ?? '-' }}</td>
                                        <td>
                                            {{ $row->changed_date ? \Carbon\Carbon::parse($row->changed_date)->format('Y-m-d') : '-' }}
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
</section>

@endsection

@include('components.partials.data_table_js')
@include('components.partials.data_table_css')

@push('script')
<script>
    $(document).ready(function() {

        var table = $('#table_id').DataTable({
            responsive: false,
            dom: 'Blfrtip',
            buttons: [
                { extend: 'copy', title: getFileName() },
                { extend: 'csv', title: getFileName() },
                { extend: 'excel', title: getFileName() },
                { extend: 'pdf', title: getFileName() },
                { extend: 'print', title: getFileName() }
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            scrollX: true,
            lengthChange: true,
            order: [[0, 'asc']],
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
            let time = now.toTimeString().slice(0, 8).replace(/:/g, '-');

            return 'Change-Course-Report_' + date + '__' + time;
        }

    });
</script>
@endpush