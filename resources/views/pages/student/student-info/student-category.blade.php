@extends('layouts.app')
@push('css')
    <style>
        /* ---- Modern refresh: cards, table styling (Section 25) ---- */
        .white-box {
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
        }

        .white-box .main-title h3,
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
@section('title')
    @lang('student.student_category')
@endsection
@section('mainContent')

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('student.student_category')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('student.student_category')</a>
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid p-0">
        @include('components.partials.permanent-delete-note', ['rule' => 'student_category'])
    </div>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @php
                // Button-level permissions (Section 25).
                $canAddCategory = Auth::user()->hasPermission('student-category.store');
                $canEditCategory = Auth::user()->hasPermission('student-category.update');
                $canDeleteCategory = Auth::user()->hasPermission('student-category.destroy');
                $showCategoryForm = isset($student_type) ? $canEditCategory : $canAddCategory;
            @endphp

            <div class="row">
                @if ($showCategoryForm)
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-12">


                                <form
                                    action="{{ isset($student_type) ? route('student-category.update', ['id' => $student_type->id]) : route('student-category.store') }}"
                                    method="POST" class="form-horizontal">
                                    @csrf
                                    @if (isset($student_type))
                                        @method('PUT')
                                    @endif
                                    <input type="hidden" name="dt_page" class="dt-page-field"
                                        value="{{ (int) request('dt_page', 0) }}">
                                    <div class="white-box">
                                        <div class="main-title">
                                            <h3 class="mb-15">
                                                @lang('student.add_student_category')
                                            </h3>
                                        </div>

                                        <div class="add-visitor">
                                            <div class="row">
                                                <div class="col-lg-12">

                                                    <div class="primary_input">
                                                        <label class="primary_input_label" for="">@lang('common.type')
                                                            <span class="text-danger"> *</span>
                                                        </label>
                                                        <input
                                                            class="primary_input_field form-control{{ $errors->has('category') ? ' is-invalid' : '' }}"
                                                            type="text" name="category" autocomplete="off"
                                                            placeholder="Category Name"
                                                            value="{{ isset($student_type) ? $student_type->category_name : old('category') }}">
                                                        <input type="hidden" name="id"
                                                            value="{{ isset($student_type) ? $student_type->id : '' }}">

                                                        @if ($errors->has('category'))
                                                            <span class="text-danger">
                                                                {{ $errors->first('category') }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="row mt-20">
                                                <div class="col-lg-12 text-center">
                                                    <button class="primary-btn fix-gr-bg submit col-12"
                                                        data-toggle="tooltip">
                                                        <span class="ti-check"></span>

                                                        @lang('student.save_category')


                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-lg-{{ $showCategoryForm ? '8' : '12' }}">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('student.student_category_list')</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id" class="table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>@lang('common.sl')</th>
                                                <th>@lang('student.category')</th>
                                                <th>@lang('common.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categories as $key => $category)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $category->category_name }}</td>

                                                    <td>
                                                        @if ($canEditCategory || $canDeleteCategory)
                                                            <x-drop-down>
                                                                @if ($canEditCategory)
                                                                    <a class="dropdown-item editCategory student-category-edit-link"
                                                                        href="{{ route('student-category.edit', $category->id) }}">
                                                                        @lang('common.edit')
                                                                    </a>
                                                                @endif
                                                                @if ($canDeleteCategory)
                                                                    <a class="dropdown-item" data-toggle="modal"
                                                                        data-target="#deleteCategoryModal{{ $category->id }}"
                                                                        href="#">
                                                                        @lang('common.delete')
                                                                    </a>
                                                                @endif
                                                            </x-drop-down>
                                                        @else
                                                            <span class="text-muted">&mdash;</span>
                                                        @endif
                                                    </td>
                                                </tr>

                                                @if ($canDeleteCategory)
                                                    {{-- BUG FIX (Section 25): this action used to post to
                                                     student-category.toggle-status (Activate/Deactivate). Per your
                                                     confirmed decision on the Courses page, Activate/Deactivate is
                                                     replaced with a real Delete against the student-category.destroy
                                                     route, which already exists and is permission-gated. --}}
                                                    <div class="modal fade admin-query"
                                                        id="deleteCategoryModal{{ $category->id }}">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">
                                                                        @lang('common.delete')
                                                                    </h4>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="text-center">
                                                                        <h4>
                                                                            @lang('common.are_you_sure_to_delete')
                                                                            "{{ $category->category_name }}"?
                                                                        </h4>
                                                                    </div>
                                                                    <div class="mt-40 d-flex justify-content-between">
                                                                        <button type="button" class="primary-btn tr-bg"
                                                                            data-dismiss="modal">
                                                                            @lang('common.cancel')
                                                                        </button>
                                                                        <form
                                                                            action="{{ route('student-category.destroy', $category->id) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <input type="hidden" name="dt_page"
                                                                                class="dt-page-field" value="0">
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
                                                @endif
                                            @endforeach
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
        var table = $('#table_id').DataTable({
            responsive: true,
            dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3'" +
                "<'dt-toolbar-title'>" +
                "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-length'l><'dt-search'f><'dt-buttons-wrap'B>>" +
                ">" +
                "<'row'<'col-sm-12'tr>>" +
                "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>> ",

            buttons: [{
                    extend: 'copyHtml5',
                    title: getFileName(),
                    text: '<i class="fa-regular fa-copy"></i>',
                    titleAttr: 'Copy'
                },
                {
                    extend: 'csvHtml5',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-file-csv"></i>',
                    titleAttr: 'CSV'
                },
                {
                    extend: 'excelHtml5',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-file-excel"></i>',
                    titleAttr: 'Excel'
                },
                {
                    extend: 'pdfHtml5',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-file-pdf"></i>',
                    titleAttr: 'PDF'
                },
                {
                    extend: 'print',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-print"></i>',
                    titleAttr: 'Print'
                }
            ],
            pageLength: 100,
            language: {
                search: '<i class=""></i>',
                searchPlaceholder: 'Search...',
                paginate: {
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            },
            stateSave: true,
            paging: true,
            searching: true,
            ordering: true,
            info: true,


        });

        var initialDtPage = parseInt('{{ (int) request('dt_page', 0) }}', 10);
        if (initialDtPage > 0) {
            table.page(initialDtPage).draw(false);
        }

        $(document).on('click', '.student-category-edit-link', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            var sep = href.indexOf('?') === -1 ? '?' : '&';
            window.location.href = href + sep + 'dt_page=' + table.page();
        });

        $(document).on('submit', 'form', function() {
            $(this).find('.dt-page-field').val(table.page());
        });
    </script>
@endpush

@section('script')
    <script></script>
@endsection
