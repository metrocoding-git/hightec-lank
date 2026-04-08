@extends('layouts.app')
@section('title')
    Course Paid Report
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Course Paid Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Reports</a>
                    <a href="#">Course Paid</a>
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
                                    <h3 class="mb-15">Course Paid Report</h3>
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
                                                <th>Course Name</th>
                                                <th>Course Price</th>
                                                <th>Paid Amount</th>
                                                <th>Due Amount</th>
                                                <th>Payment Status</th>
                                                <th>Payment Method</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($paidCourses))
                                                @foreach ($paidCourses as $key => $paidCourse)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $paidCourse->invoice && $paidCourse->invoice->student ? $paidCourse->invoice->student->student_name : 'N/A' }}
                                                        </td>
                                                        <td>{{ $paidCourse->course ? $paidCourse->course->name : 'N/A' }}
                                                        </td>
                                                        <td>{{ number_format((float) $paidCourse->course_price, 2, '.', '') }}
                                                        </td>
                                                        <td>{{ number_format((float) $paidCourse->paid_amount, 2, '.', '') }}
                                                        </td>
                                                        <td>{{ number_format((float) $paidCourse->due_amount, 2, '.', '') }}
                                                        </td>
                                                        <td>{{ $paidCourse->payment_status }}</td>
                                                        <td>{{ $paidCourse->payment_method }}</td>
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
