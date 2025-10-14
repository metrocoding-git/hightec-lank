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
                                            <th>@lang('inventory.item_category')</th>
                                            <th>@lang('inventory.issue_to')</th>
                                            <th>@lang('inventory.issue_date')</th>
                                            <th>@lang('inventory.return_date')</th>
                                            <th>@lang('inventory.quantity')</th>
                                            <th hidden>@lang('common.description')</th>
                                            <th>@lang('common.status')</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($issuedItems))
                                            @foreach ($issuedItems as $key => $value)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $value->item ? $value->item->item_name : '' }}</td>
                                                    <td>{{ $value->category ? $value->category->name : '' }}</td>
                                                    <td>
                                                        @if ($value->student_id && $value->student)
                                                            {{ $value->student->student_name }}
                                                        @elseif ($value->issue_to && $value->staff)
                                                            {{ $value->staff->name }}
                                                        @else
                                                            {{ $value->branch ? $value->branch->name : '' }}
                                                        @endif
                                                    </td>
                                                    <td data-sort="{{ strtotime($value->issue_date) }}">
                                                        {{ $value->issue_date != '' ? date('m/d/Y', strtotime($value->issue_date)) : '' }}
                                                    </td>
                                                    <td data-sort="{{ strtotime($value->due_date) }}">
                                                        {{ $value->due_date != '' ? date('m/d/Y', strtotime($value->due_date)) : '' }}
                                                    </td>
                                                    <td>{{ $value->quantity }}</td>
                                                    <td hidden>{{ $value->description }}</td>
                                                    <td>
                                                        @if ($value->issue_status == 'I')
                                                            <button
                                                                class="primary-btn small bg-success text-white border-0">@lang('inventory.issued')</button>
                                                        @else
                                                            <button
                                                                class="primary-btn small bg-primary text-white border-0">@lang('inventory.returned')</button>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <x-drop-down>
                                                            @if ($value->issue_status == 'I')
                                                                <form action="{{ route('return-item') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="id"
                                                                        value="{{ @$value->id }}">
                                                                    <button type="submit"
                                                                        class="dropdown-item">@lang('inventory.return')</button>
                                                                </form>
                                                            @else
                                                                <form action="{{ route('issue-item') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="id"
                                                                        value="{{ @$value->id }}">
                                                                    <button type="submit"
                                                                        class="dropdown-item">Issue</button>
                                                                </form>
                                                            @endif
                                                            <a class="dropdown-item view-item-issue" href="#"
                                                                data-id="{{ @$value->id }}">@lang('common.view')</a>
                                                            <a class="dropdown-item"
                                                                href="{{ route('item-issue-edit', @$value->id) }}">@lang('common.edit')</a>
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
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Item Issue Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body modal-info">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-bordered  table-hover">
                                <tbody>
                                    <tr>
                                        <td><strong>@lang('inventory.item_name'):</strong></td>
                                        <td id="modalItemName"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('inventory.item_category'):</strong></td>
                                        <td id="modalItemCategory"></td>
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
                                        <td><strong>@lang('inventory.quantity'):</strong></td>
                                        <td id="modalQuantity"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('common.description'):</strong></td>
                                        <td id="modalDescription"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('common.status'):</strong></td>
                                        <td id="modalStatus"></td>
                                    </tr>
                                </tbody>
                            </table>
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
        });

        $(document).on('click', '.view-item-issue', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var row = $(this).closest('tr');

            $('#modalItemName').text(row.find('td:eq(1)').text());
            $('#modalItemCategory').text(row.find('td:eq(2)').text());
            $('#modalIssueTo').text(row.find('td:eq(3)').text());
            $('#modalIssueDate').text(row.find('td:eq(4)').text());
            $('#modalReturnDate').text(row.find('td:eq(5)').text());
            $('#modalQuantity').text(row.find('td:eq(6)').text());
            $('#modalDescription').text(row.find('td:eq(7)').text());
            $('#modalStatus').text(row.find('td:eq(8)').text());

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
