@extends('layouts.app')
@section('title')
    @lang('inventory.issued_item_list')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('inventory.issued_item_list')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('inventory.inventory')</a>
                    <a href="#">@lang('inventory.issued_item_list')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-15">@lang('inventory.issued_item_list')</h3>
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
                                            <th>@lang('inventory.item_name')</th>
                                            {{-- <th>@lang('inventory.item_category')</th> --}}
                                            <th>@lang('inventory.issue_to')</th>
                                            <th>Reg No</th>
                                            <th>Category</th>
                                            <th>@lang('inventory.issue_date')</th>
                                            <th>@lang('inventory.return_date')</th>
                                            <th>@lang('inventory.quantity')</th>
                                            <th>Intake Name</th>
                                            <th>Course Name</th>
                                            <th>Create user</th>
                                            <th>Course Expiration</th>
                                            <th hidden>@lang('common.description')</th>
                                            <th>@lang('common.status')</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($batchesWithCourseStatus))
                                            @foreach ($batchesWithCourseStatus as $key => $batchData)
                                                @php $batch = $batchData['batch']; @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        @if ($batch->items->count() > 0)
                                                            @foreach ($batch->items as $index => $item)
                                                                {{ $item->item->item_name ?? 'N/A' }}
                                                                @if (!$loop->last)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            No Items
                                                        @endif
                                                    </td>
                                                    {{-- <td>
                                                        @if ($batch->items->count() > 0)
                                                            @foreach ($batch->items as $index => $item)
                                                                {{ $item->category->name ?? 'N/A' }}
                                                                @if (!$loop->last), @endif
                                                            @endforeach
                                                        @else
                                                            No Categories
                                                        @endif
                                                    </td> --}}
                                                    <td>
                                                        @if ($batch->student_id && $batch->student)
                                                            {{ $batch->student->student_name }}
                                                        @elseif ($batch->issue_to && $batch->staff)
                                                            {{ $batch->staff->name }}
                                                        @else
                                                            {{ $batch->branch ? $batch->branch->name : '' }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @isset($batch->student->admission_number)
                                                            {{ $batch->student->admission_number }}
                                                        @else
                                                            N/A
                                                        @endisset
                                                    </td>

                                                    <td>
                                                        {{ $batch->student->studentCategory->category_name ?? 'N/A' }}
                                                    </td>
                                                    <td data-sort="{{ strtotime($batch->issue_date) }}">
                                                        {{ $batch->issue_date != '' ? date('m/d/Y', strtotime($batch->issue_date)) : '' }}
                                                    </td>
                                                    <td data-sort="{{ strtotime($batch->due_date) }}">
                                                        {{ $batch->due_date != '' ? date('m/d/Y', strtotime($batch->due_date)) : '' }}
                                                    </td>
                                                    <td>{{ $batch->total_items ?? $batch->items->sum('quantity') }}</td>
                                                    <td>{{ $batchData['intake_name'] }}</td>
                                                    <td>{{ $batchData['course_name'] }}</td>
                                                    <td> {{ $batch->createdBy?->name ?? '-' }}</td>

                                                    <td>
                                                        <button
                                                            class="primary-btn small {{ $batchData['expire_class'] }} text-white border-0">{{ $batchData['expire_status'] }}</button>
                                                    </td>

                                                    <td hidden>{{ $batch->description }}</td>
                                                    <td>
                                                        @if ($batch->issue_status == 'I')
                                                            <button
                                                                class="primary-btn small bg-success text-white border-0">@lang('inventory.issued')</button>
                                                        @else
                                                            <button
                                                                class="primary-btn small bg-primary text-white border-0">@lang('inventory.returned')</button>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <x-drop-down>
                                                            @if ($batch->issue_status == 'I')
                                                                @if ($batchData['expire_status'] == 'Expired')
                                                                    <button type="button" class="dropdown-item text-danger"
                                                                        disabled>Cannot Return (Expired)</button>
                                                                @else
                                                                    <form action="{{ route('return-item') }}"
                                                                        method="POST" class="m-0">
                                                                        @csrf
                                                                        <input type="hidden" name="id"
                                                                            value="{{ @$batch->id }}">
                                                                        <button type="submit"
                                                                            class="dropdown-item">@lang('inventory.return')</button>
                                                                    </form>
                                                                @endif
                                                            @else
                                                                <form action="{{ route('return-back-item') }}" method="POST"
                                                                    class="m-0">
                                                                    @csrf
                                                                    <input type="hidden" name="id"
                                                                        value="{{ @$batch->id }}">
                                                                    <button type="submit"
                                                                        class="dropdown-item">Issue</button>
                                                                </form>
                                                            @endif
                                                            <a class="dropdown-item view-item-issue" href="#"
                                                                data-id="{{ @$batch->id }}">@lang('common.view')</a>
                                                            {{-- <a class="dropdown-item"
                                                                href="{{ route('item-issue-edit', @$batch->id) }}">@lang('common.edit')</a> --}}
                                                        </x-drop-down>
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
    </section>

    <!-- Item Issue Details Modal -->
    <div class="modal fade admin-query" id="itemIssueDetailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #6f42c1; color: white;">
                    <h4 class="modal-title">Item Issue Batch Details</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body modal-info">
                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Batch Information -->
                            <h5>Batch Information</h5>
                            <table class="table table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <td><strong>Batch ID:</strong></td>
                                        <td id="modalBatchId"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('inventory.issue_to'):</strong></td>
                                        <td id="modalIssueTo"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('inventory.issue_date'):</strong></td>
                                        <td id="modalIssueDate"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('inventory.return_date'):</strong></td>
                                        <td id="modalReturnDate"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('common.description'):</strong></td>
                                        <td id="modalDescription"></td>
                                    </tr>
                                    {{-- <tr>
                                        <td><strong>@lang('common.status'):</strong></td>
                                        <td id="modalStatus"></td>
                                    </tr> --}}
                                </tbody>
                            </table>

                            <!-- Items in Batch -->
                            <h5 class="my-4">Items in this Batch</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Item Name</th>
                                            <th>Category</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalBatchItems">
                                        <!-- Items will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Item Issue Details Modal -->
@endsection

@include('components.partials.data_table_js')
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
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
            order: [
                [0, 'desc']
            ], // Sort by first column (ID) in descending order
        });

        $(document).on('click', '.view-item-issue', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var row = $(this).closest('tr');

            // Set basic batch information from the row
            $('#modalBatchId').text(id);
            $('#modalIssueTo').text(row.find('td:eq(3)').text());
            $('#modalIssueDate').text(row.find('td:eq(4)').text());
            $('#modalReturnDate').text(row.find('td:eq(5)').text());
            $('#modalDescription').text(row.find('td:eq(7)').text());
            $('#modalStatus').text(row.find('td:eq(8)').text());

            // Clear previous items
            $('#modalBatchItems').empty();

            // Show loading
            $('#modalBatchItems').html('<tr><td colspan="4" class="text-center">Loading items...</td></tr>');

            // Fetch batch details with items via AJAX
            $.ajax({
                url: '{{ route('item-issue.get-batch-details', ':id') }}'.replace(':id', id),
                type: 'GET',
                success: function(data) {
                    console.log('Batch details received:', data);

                    if (data && data.items) {
                        var itemsHtml = '';
                        if (data.items.length > 0) {
                            data.items.forEach(function(item, index) {
                                itemsHtml += '<tr>' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + (item.item_name || 'N/A') + '</td>' +
                                    '<td>' + (item.category_name || 'N/A') + '</td>' +
                                    '<td>' + (item.quantity || 0) + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            itemsHtml =
                                '<tr><td colspan="4" class="text-center">No items found in this batch</td></tr>';
                        }
                        $('#modalBatchItems').html(itemsHtml);
                    } else {
                        $('#modalBatchItems').html(
                            '<tr><td colspan="4" class="text-center text-danger">Error loading batch items</td></tr>'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching batch details:', error);
                    $('#modalBatchItems').html(
                        '<tr><td colspan="4" class="text-center text-danger">Error: ' + error +
                        '</td></tr>');
                }
            });

            $('#itemIssueDetailsModal').modal('show');
        });
    </script>
    <style>
        #itemIssueDetailsModal .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        #itemIssueDetailsModal .modal-title {
            color: #343a40;
            font-weight: bold;
        }

        #itemIssueDetailsModal .modal-body.modal-info {
            padding: 20px;
        }

        #itemIssueDetailsModal .table td {
            vertical-align: middle;
            padding: 10px;
        }

        #itemIssueDetailsModal .table strong {
            color: #555;
        }

        #itemIssueDetailsModal .table-bordered td,
        #itemIssueDetailsModal .table-bordered th {
            border: 1px solid #dee2e6;
        }

        #itemIssueDetailsModal .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        #itemIssueDetailsModal .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        #itemIssueDetailsModal .modal-dialog {
            max-width: 800px;
        }

        #itemIssueDetailsModal .modal-content {
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .5);
        }

        #itemIssueDetailsModal .modal-header {
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            background-color: #007bff;
            /* Primary color for header */
            color: white;
            padding: 15px 20px;
        }

        #itemIssueDetailsModal .modal-title {
            color: white;
            font-size: 1.5rem;
        }

        #itemIssueDetailsModal .close {
            color: white;
            opacity: 1;
            font-size: 1.8rem;
        }

        #itemIssueDetailsModal .modal-body.modal-info {
            background-color: #f4f7f6;
            /* Light background for body */
            padding: 30px;
        }

        #itemIssueDetailsModal .table {
            margin-bottom: 0;
            background-color: white;
            border-radius: 5px;
            overflow: hidden;
        }

        #itemIssueDetailsModal .table td {
            font-size: 0.95rem;
            color: #495057;
        }

        #itemIssueDetailsModal .table strong {
            color: #343a40;
            font-weight: 600;
            width: 150px;
            /* Fixed width for labels */
            display: inline-block;
        }

        #itemIssueDetailsModal .table tr:last-child td {
            border-bottom: none;
        }

        #itemIssueDetailsModal .table tbody tr td:first-child {
            font-weight: bold;
            color: #343a40;
        }

        #itemIssueDetailsModal .table tbody tr td:last-child {
            color: #6c757d;
        }
    </style>
@endpush
