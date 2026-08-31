@extends('layouts.app')
{{-- BUG FIX (Section 27): the browser-tab title called @lang('academics.About') —
     a leftover placeholder — instead of naming this page. Also adding the
     standard breadcrumb-banner CSS, which this page never defined despite
     using the sms-breadcrumb markup (this page has no white-box/#table_id
     cards — it's a plain print/view invoice layout — so only the breadcrumb
     rules are added).

     FLAGGED, NOT FIXED (Section 27): this file's @lang() calls use the
     'fees::feesModule.xxx' module-namespaced form (e.g.
     'fees::feesModule.view_fees_invoice') everywhere, while every other file
     in this Fees batch uses the plain 'fees.xxx' form. This may be an old
     package-view left in place, or it may be intentional if a separate
     'feesModule' language namespace still exists — the language files aren't
     available in this pass to tell which is correct, so this is left as-is
     rather than guessed at. --}}
@section('title')
    @lang('fees::feesModule.view_fees_invoice')
@endsection
@section('mainContent')
    @push('css')
        <link rel="stylesheet" href="{{url('Modules\Fees\Resources\assets\css\feesStyle.css')}}"/>
        <style>
        .margin_auto{
            margin-left: auto;
             margin-right: 0
        }
        html[dir="rtl"] .margin_auto{
            margin-left: 0;
             margin-right: auto;
        }
        html[dir="rtl"] .address_text p {
            margin-right: auto;
            margin-left: 0;
        }
        html[dir="rtl"] .total_count {
            margin-right: auto;
            margin-left: 0;
        }

        .invoice_wrapper{
            overflow: auto;
        }

        .table{
            min-width: 600px;
        }

        .sms-breadcrumb {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
            padding: 18px 24px;
            position: relative;
            overflow: hidden;
        }

        .sms-breadcrumb::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--gradient_1), var(--gradient_2));
        }

        .sms-breadcrumb .row {
            align-items: center;
        }

        .sms-breadcrumb h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .sms-breadcrumb .bc-pages {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .sms-breadcrumb .bc-pages a {
            font-size: 13px;
            font-weight: 500;
            color: #8a90a2;
            text-decoration: none;
            transition: color .15s ease;
        }

        .sms-breadcrumb .bc-pages a:hover {
            color: var(--gradient_1);
        }

        .sms-breadcrumb .bc-pages a:not(:last-child)::after {
            content: '\203A';
            margin: 0 8px;
            color: #c7cbd6;
            font-weight: 400;
        }

        .sms-breadcrumb .bc-pages a:last-child {
            color: var(--gradient_1);
            font-weight: 700;
            pointer-events: none;
        }
    </style>
    @endpush

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('fees::feesModule.view_fees_invoice')</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees')</a>
                    <a href="">@lang('fees.fees_invoice')</a>
                    <a href="#">@lang('fees::feesModule.view_fees_invoice')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="max_1200 text-right">
            <a href="{{route('fees.fees-invoice-view',['id'=>$invoiceInfo->id,'state'=>'print'])}}" class="primary-btn small fix-gr-bg" target="_blank">
                <span class="ti-printer pr-2"></span>
                @lang('common.print')
            </a>
        </div>
        <div class="invoice_wrapper">
            <!-- invoice print part here -->
            <div class="invoice_print">
                <div class="container">
                    <div class="invoice_part_iner">
                        <table class="table">
                            <thead>
                            <td>
                                <div class="logo_img">
                                    <img  src="{{asset($generalSetting->logo)}}" alt="{{$generalSetting->school_name}}">
                                </div>
                            </td>
                            <td class="virtical_middle address_text">
                                <p>{{$generalSetting->school_name}}</p>
                                <p>{{$generalSetting->phone}}</p>
                                <p>{{$generalSetting->email}}</p>
                                <p>{{$generalSetting->address}}</p>
                            </td>
                            </thead>
                        </table>
                        <!-- middle content  -->
                        <table class="table">
                            <tbody>
                            <tr>
                                <td>
                                    <!-- single table  -->
                                    <table class="mb_30">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="addressleft_text">
                                                        <p><span>@lang('fees.fees_invoice_issued_to')</p>
                                                        <p><span><strong>@lang('student.student_name') </span> <span class="nowrap">: {{@$invoiceInfo->studentInfo->full_name}}</span> </strong></p>
                                                        <p><span>@lang('student.class_section')</span> <span>: {{@$invoiceInfo->recordDetail->class->class_name}} ({{@$invoiceInfo->recordDetail->section->section_name}})</span> </p>
                                                        <p><span>@lang('student.roll_no')</span> <span>: {{@$invoiceInfo->recordDetail->roll_no}}</span> </p>
                                                        <p><span>@lang('student.admission_no')</span> <span>: {{@$invoiceInfo->studentInfo->admission_no}}</span> </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!--/ single table  -->
                                </td>
                                <td >
                                    <!-- single table  -->
                                    <table class="mb_30 margin_auto">
                                        <tbody>
                                        <tr>
                                            <td>
                                                @php
                                                    $subTotal = $invoiceDetails->sum('sub_total');
                                                    $paidAmount = $invoiceDetails->sum('paid_amount');
                                                    $paymentStatus = $subTotal - $paidAmount;
                                                @endphp
                                                <div class="addressright_text">
                                                    <p><span><strong>@lang('fees.invoice_number')</span> <span>: {{$invoiceInfo->invoice_id}}</span> </strong></p>
                                                    <p><span>@lang('fees.create_date') </span> <span>: {{dateConvert($invoiceInfo->create_date)}}</span> </p>
                                                    <p><span>@lang('fees.due_date') </span> <span>: {{dateConvert($invoiceInfo->due_date)}}</span> </p>
                                                    <p>
                                                                <span>
                                                                    @lang('fees.payment_status')
                                                                </span>
                                                        <span>:
                                                                    @if ($paymentStatus == 0)
                                                                @lang('fees.paid')
                                                            @else
                                                                @if ($paidAmount > 0)
                                                                    @lang('fees.partial')
                                                                @else
                                                                    @lang('fees.unpaid')
                                                                @endif
                                                            @endif
                                                                </span>
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <!--/ single table  -->
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- invoice print part end -->
            <table class="table border_table mb_30 description_table" >
                <thead>
                <tr>
                    <th>@lang('common.sl')</th>
                    <th>@lang('fees.fees_type')</th>
                    <th>@lang('accounts.amount')</th>
                    <th>@lang('fees.waiver')</th>
                    <th>@lang('fees.fine')</th>
                    <th>@lang('fees::feesModule.paid_amount')</th>
                    <th class="text-right">@lang('fees.sub_total')</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $amount = 0;
                    $weaver = 0;
                    $paid_amount = 0;
                    $fine = 0;
                    $service_charge = 0;
                    $grand_total = 0;
                    $balance = 0;
                @endphp
                @foreach ($invoiceDetails as $key=>$invoiceDetail)
                    @php
                        $amount += $invoiceDetail->amount;
                        $weaver += $invoiceDetail->weaver;
                        $fine += $invoiceDetail->fine;
                        $service_charge += $invoiceDetail->service_charge;
                        $paid_amount += $invoiceDetail->paid_amount;

                        $totalAmount = ($invoiceDetail->amount + $invoiceDetail->fine) - $invoiceDetail->weaver;
                        $grand_total += $totalAmount ;

                        $total = ($invoiceDetail->amount+ $invoiceDetail->fine) - ($invoiceDetail->paid_amount + $invoiceDetail->weaver) ;
                        $balance += $total;
                    @endphp
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>
                            {{@$invoiceDetail->feesType->name}}
                            @if($invoiceDetail->note)
                                <i class="fa fa-info-circle" aria-hidden="true"data-tooltip="tooltip" title="{{$invoiceDetail->note}}" style="courser:help;"></i>
                            @endif
                        </td>
                        <td>{{($invoiceDetail)? $invoiceDetail->amount : 0.00}}</td>
                        <td>{{($invoiceDetail->weaver)? $invoiceDetail->weaver : 0}}</td>
                        <td>{{($invoiceDetail->fine)? $invoiceDetail->fine : 0}}</td>
                        <td>{{($invoiceDetail->paid_amount)? $invoiceDetail->paid_amount : 0}}</td>
                        <td class="text-right pr-0">{{currency_format($total)}}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2">
                        <p class="total_count"><span>@lang('fees::feesModule.total_amount')</span> <span>{{currency_format($amount)}}</span></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2">
                        <p class="total_count"><span>@lang('fees::feesModule.total_waiver')</span> <span>{{currency_format($weaver)}}</span></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2">
                        <p class="total_count"><span>@lang('fees::feesModule.total_fine')</span> <span>{{currency_format($fine)}}</span></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2">
                        <p class="total_count"><span>@lang('fees::feesModule.total_service_charge')</span> <span>{{currency_format($service_charge)}}</span></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2">
                        <p class="total_count"><span>@lang('fees::feesModule.total_paid')</span> <span>{{currency_format($paid_amount)}}</span></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2">
                        <p class="total_count"><span>@lang('accounts.grand_total')</span> <span>{{currency_format($grand_total + $service_charge)}}</span></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2">
                        <p class="total_count"><span><strong>@lang('fees::feesModule.due_balance')</span> <span>
                                {{currency_format($balance)}}
                                </strong></span></p>
                    </td>
                </tr>
                </tfoot>
            </table>
            @if($banks && count($banks) > 0)
            <div class="col-lg-12">
                <table class="table border_table mb_30 description_table" >
                    <thead>
                        <tr>
                            <th>@lang('common.sl')</th>
                            <th>@lang('accounts.bank_name')</th>
                            <th>@lang('accounts.account_name')</th>
                            <th>@lang('accounts.account_number')</th>
                            <th>@lang('accounts.account_type')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banks as $key=>$bank)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>{{$bank->bank_name}}</td>
                                <td>{{$bank->account_name}}</td>
                                <td>{{$bank->account_number}}</td>
                                <td>{{$bank->account_type}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </section>
@endsection