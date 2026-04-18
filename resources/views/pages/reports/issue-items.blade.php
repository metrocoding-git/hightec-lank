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
                                                <th>#</th>
                                                <th>@lang('inventory.item_name')</th>
                                                <th>@lang('inventory.item_category')</th>
                                                <th>@lang('inventory.issue_to')</th>
                                                <th>Reg No</th>
                                                <th>Category</th>
                                                <th>@lang('inventory.issue_date')</th>
                                                <th>@lang('inventory.quantity')</th>
                                                <th>Intake Name</th>
                                                <th>Course Name</th>
                                                <th>Create user</th>
                                                <th>Course Expiration</th>
                                                <th hidden>@lang('common.description')</th>
                                                <th>@lang('common.status')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($itemIssues))
                                                @foreach ($itemIssues as $key => $batchData)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>
                                                            {{ $batchData->item->item_name ?? 'No Items' }}
                                                        </td>
                                                        <td>
                                                            {{ $batchData->item->category->name ?? '-' }} </td>
                                                        <td>
                                                            {{ $batchData->batch->student->student_name ?? '-' }}
                                                        </td>
                                                        <td>
                                                            {{ $batchData->batch->student->admission_number ?? '-' }}
                                                        </td>
                                                        <td>
                                                            {{ $batchData->batch->student->studentCategory->category_name ?? '' }}
                                                        </td>

                                                        <td data-sort="{{ strtotime($batchData->batch->issue_date) }}">
                                                            {{ $batchData->batch->issue_date != '' ? date('m/d/Y', strtotime($batchData->batch->issue_date)) : '' }}
                                                        </td>
                                                        <td>{{ $batchData->quantity }}
                                                        </td>
                                                        <td>{{ $batchData->batch->intake->name ?? '-' }}</td>
                                                        <td>{{ $batchData->batch->course->name ?? '-' }}</td>
                                                        <td> {{ $batchData->batch->createdBy?->name ?? '-' }}</td>

                                                        <td>
                                                            {{ $batchData->expire_status }}
                                                        </td>

                                                        <td>{{ $batchData->batch->description }}</td>
                                                        <td>
                                                            @if ($batchData->batch->issue_status == 'I')
                                                                @lang('inventory.issued')
                                                            @else
                                                                @lang('inventory.returned')
                                                            @endif
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
        $('#table_id').DataTable({
            responsive: true,
            dom: 'Bfrtip',
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
        });

        function getFileName() {
            let now = new Date();

            let date = now.toISOString().slice(0, 10);
            let time = now.toTimeString().slice(0, 8).replace(/:/g, '-'); // HH-MM-SS

            return 'Item-Issue-Report_' + date + '__' + time;
        }
    </script>
@endpush
