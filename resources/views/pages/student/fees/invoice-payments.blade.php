@extends('layouts.app')
@section('title')
    @lang('finance.Invoice Payments')
@endsection

@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('finance.Invoice Payments')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('finance.invoice')</a>
                    <a href="#">@lang('finance.invoice_payments')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <form class="form-horizontal" action="{{ route('invoice-payments.search') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white-box filter_card">
                            <div class="row align-items-center mb-3">
                                <div class="col-lg-8 col-md-6">
                                    <h3 class="mb-15">@lang('common.select_criteria')</h3>
                                </div>
                                <div class="col-lg-4 col-md-6 text-left text-sm-right mb-4 mb-sm-0">
                                    <a href="{{ route('invoice-payments.create') }}" class="primary-btn small fix-gr-bg">
                                        <span class="ti-plus pr-2"></span>@lang('finance.add_payment')
                                    </a>
                                </div>
                            </div>

                            <div class="row row-gap-24">

                                {{-- Branch Filter --}}
                                <div class="col-lg-4 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="branch_id">@lang('common.branch')</label>
                                        <select class="primary_select form-control" name="branch_id" id="branch-select">
                                            <option value="">@lang('common.select_branch')</option>
                                            @foreach ($branches ?? [] as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Payment Status Filter --}}
                                <div class="col-lg-4 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="payment_status">@lang('finance.payment_status')</label>
                                        <select class="primary_select form-control" name="payment_status"
                                            id="status-select">
                                            <option value="">@lang('common.select_status')</option>
                                            <option value="not_open">@lang('finance.not_open')</option>
                                            <option value="pending">@lang('finance.pending')</option>
                                            <option value="completed">@lang('finance.completed')</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-20 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg">
                                        <span class="ti-search pr-2"></span>@lang('common.search')
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Payments Table --}}
            <div class="row mt-40 full_wide_table">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title mb-3">
                            <h3>@lang('finance.invoice_payment_list')</h3>
                        </div>

                        <x-table>
                            <table id="invoice_table"
                                class="table data-table Crm_table_active3 no-footer dtr-inline collapsed" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>Invoice Number</th>
                                        <th>Branch</th>
                                        <th>Student Name</th>
                                        <th>Admission Number</th>
                                        <th>Fee Name</th>
                                        <th>amount</th>
                                        <th>status</th>
                                        <th>date</th>
                                        <th>@lang('common.actions')</th>
                                    </tr>
                                </thead>
                            </table>
                        </x-table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Status Update Modal --}}
    <div class="modal fade admin-query" id="paymentStatusModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('finance.update_payment_status')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <h4>@lang('common.are_you_sure_to_update_status')</h4>
                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                        <form action="{{ route('invoice-payments.update-status') }}" method="post">
                            @csrf
                            @method('put')
                            <input type="hidden" id="payment-id-form" name="payment_id" value="">
                            <button class="primary-btn fix-gr-bg" type="submit">@lang('common.update')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('components.partials.data_table_js')
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')

@push('script')
    <script>
        $('#invoice_table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('invoice-payments.datatable') }}",
                data: function(d) {
                    d.branch_id = $('#branch-select').val();
                    d.payment_status = $('#status-select').val();
                }
            },
            dom: "<'d-flex justify-content-between align-items-center mb-2'<'me-2'l><'mx-auto'f><'ms-2'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'d-flex justify-content-between align-items-center mt-2'<'mb-0 col-4'i><'mb-0 col-8'p>>",
            buttons: ['pdf', 'csv', 'excel', 'print', 'colvis'],
            order: [
                [0, 'desc']
            ],
            columns: [{
                    data: 'invoice_no'
                },
                {
                    data: 'branch'
                },
                {
                    data: 'student_name'
                },
                {
                    data: 'admission_number'
                },
                {
                    data: 'fee_name'
                },
                {
                    data: 'amount'
                },
                {
                    data: 'status'
                },
                {
                    data: 'date'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        // Filter table on select change
        $('#branch-select, #status-select').on('change', function() {
            $('#invoice_table').DataTable().ajax.reload();
        });

        // Status modal
        $('#paymentStatusModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var paymentId = button.data('payment-id');
            $('#payment-id-form').val(paymentId);
        });
    </script>
@endpush
