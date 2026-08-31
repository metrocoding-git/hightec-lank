@extends('layouts.app')
{{-- BUG FIX (Section 34): browser-tab title called @lang('academics.About') --
     the same leftover Academics-template placeholder found and fixed on many
     other pages across this project -- instead of naming this page. Reused
     the 'supplier_list' key already used correctly elsewhere on this
     same page. --}}
@section('title')
    @lang('inventory.supplier_list')
@endsection
@section('mainContent')
    @push('css')
        <style>
        /* ---- Modern refresh: cards, table, breadcrumb (Section 34) ----
             BUG FIX: this page already used the sms-breadcrumb markup and
             .white-box/#table_id markup, but never defined any CSS for them
             anywhere in the Inventory module, so the banner and cards
             rendered completely unstyled. Adding the same breadcrumb/card/
             table CSS used across the rest of the app. ---- */
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

        .white-box {
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
        }

        .white-box .main-title h3,
        .white-box .main-title h4,
        .white-box .main-title h5 {
            font-weight: 700;
            position: relative;
            padding-bottom: 10px;
        }

        .white-box .main-title h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 42px;
            height: 3px;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2));
        }

        .primary_input_field,
        select.primary_select {
            border-radius: 8px !important;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        #table_id thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

        #table_id tbody tr {
            transition: background-color .12s ease;
        }

        #table_id tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        #table_id tbody td {
            vertical-align: middle;
        }

        .text-muted {
            color: #9aa1ac !important;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            color: #fff;
        }
            .invalid-feedback {
                display: inline-block !important;
            }
        </style>
    @endpush
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('inventory.supplier_list')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('inventory.inventory')</a>
                    <a href="#">@lang('inventory.supplier_list')</a>
                </div>
            </div>
        </div>
    </section>
    @php
        // Button-level permissions (Section 34).
        $canAddSupplier = Auth::user()->hasPermission('supplier-store');
        $canEditSupplier = Auth::user()->hasPermission('supplier-update');
        $canDeleteSupplier = Auth::user()->hasPermission('supplier-delete');
    @endphp
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @if (isset($editData) && $canAddSupplier)
                <div class="row">
                    <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                        <a href="{{ route('supplier') }}" class="primary-btn small fix-gr-bg">
                            <span class="ti-plus pr-2"></span>
                            @lang('common.add')
                        </a>
                    </div>
                </div>
            @endif
            <div class="row">

                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">
                            <form class="form-horizontal"
                                action="{{ isset($editData) ? route('supplier-update', $editData->id) : route('supplier-store') }}"
                                method="POST">
                                @csrf
                                @if (isset($editData))
                                    @method('PUT')
                                @endif
                                <div class="white-box">
                                    <div class="main-title">
                                        <h3 class="mb-15">
                                            @if (isset($editData))
                                                @lang('inventory.edit_supplier')
                                            @else
                                                @lang('inventory.add_supplier')
                                            @endif
                                        </h3>
                                    </div>
                                    <div class="add-visitor">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="primary_input">
                                                    <label> @lang('inventory.company_name') <span class="text-danger"> *</span> </label>
                                                    <input class="primary_input_field" type="text" name="company_name"
                                                        autocomplete="off"
                                                        value="{{ isset($editData) ? $editData->company_name : old('company_name') }}">


                                                    @if ($errors->has('company_name'))
                                                        <span class="text-danger">
                                                            {{ $errors->first('company_name') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-12 mt-15">
                                                <div class="primary_input">
                                                    <label> @lang('inventory.company_address') <span class="text-danger"> *</span> </label>
                                                    <textarea class="primary_input_field" cols="0" rows="4" name="company_address" id="company_address">{{ isset($editData) ? $editData->company_address : old('company_address') }}</textarea>


                                                    @if ($errors->has('company_address'))
                                                        <span class="text-danger custom-server-feedback" role="alert">
                                                            {{ $errors->first('company_address') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-12 mt-15">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="">@lang('inventory.contact_person_name')
                                                        <span class="text-danger"> *</span></label>
                                                    <input class="primary_input_field" type="text"
                                                        name="contact_person_name" autocomplete="off"
                                                        value="{{ isset($editData) ? $editData->contact_person_name : old('contact_person_name') }}">


                                                    @if ($errors->has('contact_person_name'))
                                                        <span class="text-danger">
                                                            {{ $errors->first('contact_person_name') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-12 mt-15">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="">@lang('inventory.contact_person_mobile')
                                                        <span class="text-danger"> *</span></label>
                                                    <input
                                                        class="primary_input_field form-control{{ $errors->has('contact_person_mobile') ? ' is-invalid' : '' }}"
                                                        type="tel" oninput="phoneCheck(this)"
                                                        name="contact_person_mobile" autocomplete="off"
                                                        value="{{ isset($editData) ? $editData->contact_person_mobile : old('contact_person_mobile') }}">


                                                    @if ($errors->has('contact_person_mobile'))
                                                        <span class="text-danger">
                                                            {{ $errors->first('contact_person_mobile') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mt-15">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="">@lang('inventory.contact_person_email')
                                                        <span class="text-danger"></span></label>
                                                    <input oninput="emailCheck(this)"
                                                        class="primary_input_field form-control{{ $errors->has('contact_person_email') ? ' is-invalid' : '' }}"
                                                        type="email" name="contact_person_email" autocomplete="off"
                                                        value="{{ isset($editData) ? $editData->contact_person_email : old('contact_person_email') }}">
                                                    @if ($errors->has('contact_person_email'))
                                                        <span class="text-danger">
                                                            {{ $errors->first('contact_person_email') }}
                                                        </span>
                                                    @endif

                                                </div>
                                            </div>

                                            <div class="col-lg-12 mt-15">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="">@lang('common.description')
                                                        <span></span> </label>
                                                    <textarea class="primary_input_field form-control" cols="0" rows="4" name="description" id="description">{{ isset($editData) ? $editData->description : old('description') }}</textarea>


                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-40">
                                            <div class="col-lg-12 text-center">
                                                @if (isset($editData) ? $canEditSupplier : $canAddSupplier)
                                                    <button class="primary-btn fix-gr-bg submit" data-toggle="tooltip">
                                                        <span class="ti-check"></span>
                                                        @if (isset($editData))
                                                            @lang('common.update')
                                                        @else
                                                            @lang('common.save')
                                                        @endif
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">

                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15"> @lang('inventory.supplier_list')</h3>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <x-table>
                                        <table id="table_id" class="table" cellspacing="0" width="100%">
                                            <thead>

                                                <tr>
                                                    <th> @lang('inventory.supplier_name')</th>
                                                    <th> @lang('inventory.company_name')</th>
                                                    <th> @lang('inventory.company_address')</th>
                                                    <th> @lang('common.email')</th>
                                                    <th> @lang('common.mobile')</th>
                                                    <th> @lang('common.action')</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @if (isset($suppliers))
                                                    @foreach ($suppliers as $value)
                                                        <tr>

                                                            <td>{{ $value->contact_person_name }}</td>
                                                            <td>{{ $value->company_name }}</td>
                                                            <td>{{ $value->company_address }}</td>
                                                            <td>{{ $value->contact_person_email }}</td>
                                                            <td>{{ $value->contact_person_mobile }}</td>
                                                            <td>
                                                                <x-drop-down>

                                                                    @if ($canEditSupplier)
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('supplier-edit', $value->id) }}">
                                                                            @lang('common.edit')</a>
                                                                    @endif

                                                                    @if ($canDeleteSupplier)
                                                                        <a class="dropdown-item" data-toggle="modal"
                                                                            data-target="#deletemodal{{ $value->id }}"
                                                                            href="#">@lang('common.delete')</a>
                                                                    @endif

                                                                </x-drop-down>
                                                            </td>
                                                        </tr>
                                                        <div class="modal fade admin-query"
                                                        id="deletemodal{{ $value->id }}">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">@lang('common.delete')</h4>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="text-center">
                                                                        <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                                    </div>
                                                                    <div class="mt-40 d-flex justify-content-between">
                                                                        <button type="button" class="primary-btn tr-bg"
                                                                            data-dismiss="modal">
                                                                            @lang('common.cancel')
                                                                        </button>
                                                                        <form
                                                                            action="{{ route('supplier-delete', $value->id) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button class="primary-btn fix-gr-bg"
                                                                                type="submit">
                                                                                @lang('common.delete')
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
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
        </div>
    </section>
@endsection
@include('components.partials.data_table_css')
@include('components.partials.data_table_js')
@push('script')

    <script>
        $('#table_id').DataTable({
            responsive: true,
            dom: 'Bfrtip',

            buttons: [{
                    extend: 'copyHtml5',
                    text: '<i class="fa-regular fa-copy"></i>',
                    titleAttr: 'Copy'
                },
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa-solid fa-file-csv"></i>',
                    titleAttr: 'CSV'
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel"></i>',
                    titleAttr: 'Excel'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa-solid fa-file-pdf"></i>',
                    titleAttr: 'PDF'
                },
                {
                    extend: 'print',
                    text: '<i class="fa-solid fa-print"></i>',
                    titleAttr: 'Print'
                }
            ],

            paging: true,
            searching: true,
            ordering: true,
            info: true,

            // This part adds the search and pagination icons
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