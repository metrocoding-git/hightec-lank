<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row">
            <!-- Left Section: Invoice Info -->
            <div class="col-lg-3">
                <div class="white-box">
                    <div class="main-title">
                        <h3>Invoice Details</h3>
                    </div>
                    <div class="add-visitor">
                        <input type="hidden" id="url" value="{{ URL::to('/') }}">

                        {{-- Invoice Number --}}
                        <div class="row mt-15">
                            <div class="col-lg-12">
                                <label>@lang('fees.invoice_number')</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="#{{ $invoice->id }}" readonly>
                                </div>
                            </div>
                        </div>

                        {{-- Branch --}}
                        <div class="row mt-15">
                            <div class="col-lg-12">
                                <label>@lang('common.branch')</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $invoice->branch->name }}" readonly>
                                </div>
                            </div>
                        </div>

                        {{-- Student --}}
                        <div class="row mt-15">
                            <div class="col-lg-12">
                                <label>@lang('common.student')</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $invoice->student->student_name }}" readonly>
                                </div>
                            </div>
                        </div>

                        {{-- Admission Number --}}
                        <div class="row mt-15">
                            <div class="col-lg-12">
                                <label>@lang('admin.admission_no')</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $invoice->student->admission_number }}" readonly>
                                </div>
                            </div>
                        </div>

                        {{-- Dates --}}
                        <div class="row mt-15">
                            <div class="col-lg-12">
                                <label>@lang('fees.create_date')</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ date('m/d/Y', strtotime($invoice->create_date)) }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-15">
                            <div class="col-lg-12">
                                <label>@lang('fees.due_date')</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ date('m/d/Y', strtotime($invoice->due_date)) }}" readonly>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Status --}}
                        <div class="row mt-15">
                            <div class="col-lg-12">
                                <label>@lang('fees.payment_status')</label>
                                <div class="input-group">
                                    @if ($invoice->payment_status == 'partial')
                                        <input type="text" class="form-control" value="@lang('fees.partial_paid')" readonly style="background-color: #fff3cd; color: #856404;">
                                    @elseif ($invoice->payment_status == 'full')
                                        <input type="text" class="form-control" value="@lang('fees.full_paid')" readonly style="background-color: #d4edda; color: #155724;">
                                    @elseif ($invoice->payment_status == 'not')
                                        <input type="text" class="form-control" value="@lang('fees.not_paid')" readonly style="background-color: #f8d7da; color: #721c24;">
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="row mt-15">
                            <div class="col-lg-12">
                                <label>@lang('fees.payment_method')</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $invoice->payment_method }}" readonly>
                                </div>
                            </div>
                        </div>

                        {{-- Totals Summary --}}
                        <div class="row mt-15">
                            <div class="col-lg-12">
                                <div class="white-box" style="background-color: #f8f9fa;">
                                    <h5>Accounts Summary</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>@lang('accounts.total'):</strong>
                                        </div>
                                        <div class="col-6">
                                            {{ number_format($invoice->total_amount, 2) }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>@lang('fees.paid'):</strong>
                                        </div>
                                        <div class="col-6">
                                            {{ number_format($invoice->paid_amount, 2) }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>@lang('accounts.balance'):</strong>
                                        </div>
                                        <div class="col-6">
                                            {{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Section: Fee Type List -->
            <div class="col-lg-9">
                <div class="white-box">
                    <div class="main-title">
                        <h3>@lang('fees.fees_type_list')</h3>
                    </div>

                    {{-- Fees Table --}}
                    <div class="big-table mt-10">
                        <table class="table school-table-style fees_invoice_type_table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('common.sl')</th>
                                    <th>@lang('student.name')</th>
                                    <th>@lang('fees.fees_type')</th>
                                    <th>@lang('accounts.amount')</th>
                                    <th>@lang('fees.paid_amount')</th>
                                    <th>@lang('accounts.balance')</th>
                                </tr>
                            </thead>
                            <tbody class="allFeesTypes">
                                @foreach($invoice->invoice_item as $key => $item)
                                    @php
                                        $balance = $item->amount - $item->paid_amount;
                                    @endphp
                                    <tr data-id="{{ $item->fee_type_id }}">
                                        <td class="sl">{{ $key + 1 }}</td>
                                        <td class="tdName">{{ $item->fee_type->name ?? 'N/A' }}</td>
                                        <td class="fees_type">{{ $item->fee_type->name ?? 'N/A' }}</td>
                                        <td class="amount_cell">
                                            <div class="input-group input-group-sm" style="max-width: 120px;">
                                                <input type="number" class="form-control" value="{{ $item->amount }}" readonly>
                                                <div class="input-group-append"><span class="input-group-text">.00</span></div>
                                            </div>
                                        </td>
                                        <td class="payment_cell">
                                            <div class="input-group input-group-sm" style="max-width: 120px;">
                                                <input type="number" class="form-control" value="{{ $item->paid_amount }}" readonly>
                                                <div class="input-group-append"><span class="input-group-text">.00</span></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm" style="max-width: 120px;">
                                                <input type="number" class="form-control" value="{{ $balance }}" readonly>
                                                <div class="input-group-append"><span class="input-group-text">.00</span></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Document Section --}}
        @if($invoice->payment_file)
        <div class="row mt-20">
            <div class="col-lg-12">
                <div class="white-box">
                    <div class="main-title">
                        <h3>@lang('fees.payment_document')</h3>
                    </div>
                    <div class="text-center">
                        @if(in_array(pathinfo($invoice->payment_file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{ asset('storage/' . $invoice->payment_file) }}" class="img-fluid" style="max-height: 400px; border: 1px solid #ddd; padding: 10px;">
                        @else
                            <a href="{{ asset('storage/' . $invoice->payment_file) }}" target="_blank" class="btn btn-primary btn-lg">
                                <i class="fa fa-download"></i> @lang('common.download_document')
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
