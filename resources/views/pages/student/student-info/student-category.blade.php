@extends('layouts.app')
@section('title')
    @lang('student.student_category')
@endsection
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <div class="row">

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

                                        <div class="row mt-40">
                                            <div class="col-lg-12 text-center">
                                                <button class="primary-btn fix-gr-bg submit" data-toggle="tooltip">
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

                <div class="col-lg-8">
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
                                                <th>Status</th>
                                                <th>@lang('common.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categories as $key => $category)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $category->category_name }}</td>
                                                    <td>{{ $category->is_active ? 'Activate' : 'Deactivate' }}</td>

                                                    <td>

                                                        <x-drop-down>
                                                            <a class="dropdown-item editCategory student-category-edit-link"
                                                                href="{{ route('student-category.edit', $category->id) }}">
                                                                @lang('common.edit')
                                                            </a>
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#toggleStatusModal{{ $category->id }}"
                                                                href="#">
                                                                {{ $category->is_active ?? true ? 'Deactivate' : 'Activate' }}
                                                            </a>
                                                        </x-drop-down>
                                                    </td>
                                                </tr>

                                                <!-- Activate/Deactivate confirm modal for each category — replaces the old Delete modal -->
                                                <div class="modal fade admin-query"
                                                    id="toggleStatusModal{{ $category->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">
                                                                    {{ $category->is_active ?? true ? 'Deactivate Category' : 'Activate Category' }}
                                                                </h4>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="text-center">
                                                                    <h4>
                                                                        @if ($category->is_active ?? true)
                                                                            Are you sure you want to deactivate
                                                                            "{{ $category->category_name }}"?
                                                                        @else
                                                                            Are you sure you want to activate
                                                                            "{{ $category->category_name }}"?
                                                                        @endif
                                                                    </h4>
                                                                </div>
                                                                <div class="mt-40 d-flex justify-content-between">
                                                                    <button type="button" class="primary-btn tr-bg"
                                                                        data-dismiss="modal">
                                                                        @lang('common.cancel')
                                                                    </button>
                                                                    <form
                                                                        action="{{ route('student-category.toggle-status', $category->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="dt_page"
                                                                            class="dt-page-field" value="0">
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">
                                                                            {{ $category->is_active ?? true ? 'Deactivate' : 'Activate' }}
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
            dom: 'Bfrtip',
            stateSave: true,

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

            // 👇 This part adds the search and pagination icons
            language: {
                search: '<i class="fa-solid fa-magnifying-glass me-2"></i>',
                searchPlaceholder: 'Search...',
                paginate: {
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            }
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
