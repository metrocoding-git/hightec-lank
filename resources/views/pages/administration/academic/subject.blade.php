@extends('layouts.app')
@push('css')
    <style>
        .student_rec_card {
            border-radius: 6px;
            border: 1px solid var(--border_color);
            width: 100%;
        }

        .student_rec_header {
            padding: 12px;
            background: -webkit-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -moz-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -o-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -ms-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
        }

        .student_rec_footer {
            padding: 12px;
            margin-top: 16px;
            border-top: 1px solid var(--border_color);
        }

        .student_rec_content {
            padding: 16px;
            max-height: 300px;
            min-height: 300px;
        }

        .action-disabled {
            opacity: 0.45;
            pointer-events: none;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('academics.subject')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('academics.academics')</a>
                    <a href="#">@lang('academics.subject')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <div class="row">
                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">

                            @if (isset($subjectId))
                                {{-- Update form --}}
                                <form class="form-horizontal" action="{{ route('subject.update', $subject->id) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @method('POST') {{-- Or PATCH/PUT depending on your route --}}
                                @else
                                    {{-- Store form --}}
                                    {{-- You can remove this check if not needed --}}
                                    <form class="form-horizontal" action="{{ route('subject.store') }}" method="POST"
                                        enctype="multipart/form-data">
                            @endif

                            @csrf
                            {{-- FIX (pagination — same mechanism as the Courses/Intake pages):
                                 carries the DataTable page you were on through the
                                 Add/Edit round trip. See the script at the bottom of
                                 this file for how the pieces fit together. --}}
                            <input type="hidden" name="dt_page" class="dt-page-field"
                                value="{{ (int) request('dt_page', 0) }}">

                            <div class="white-box">
                                <div class="main-title">
                                    <h3 class="mb-20">
                                        @if (isset($subjectId))
                                            @lang('academics.update_subject')
                                        @else
                                            @lang('academics.add_subject')
                                        @endif

                                    </h3>
                                </div>
                                <div class="add-visitor">
                                    {{-- Subject Name --}}
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="name">@lang('academics.subject_name')
                                                    <span>*</span></label>
                                                <input
                                                    class="primary_input_field form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                                    type="text" name="name" id="name" autocomplete="off"
                                                    value="{{ old('name', $subject->name ?? '') }}">
                                                @if ($errors->has('name'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Subject Type --}}
                                    <div class="row mt-20">
                                        <div class="col-lg-12">
                                            <label class="primary_input_label">@lang('academics.subject_type') <span>*</span></label>
                                            <div class="d-flex radio-btn-flex">
                                                <div class="mr-30">
                                                    <input type="radio" name="type" id="theory" value="T"
                                                        class="common-radio relationButton"
                                                        {{ old('type', $subject->type ?? 'T') == 'T' ? 'checked' : '' }}>
                                                    <label for="theory">@lang('academics.theory')</label>
                                                </div>
                                                <div class="mr-30">
                                                    <input type="radio" name="type" id="practical" value="P"
                                                        class="common-radio relationButton"
                                                        {{ old('type', $subject->type ?? '') == 'P' ? 'checked' : '' }}>
                                                    <label for="practical">@lang('academics.practical')</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    {{-- Demonstrator Time --}}
                                    <div class="row mt-20">
                                        <div class="col-lg-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="demonstrator_time">
                                                    @lang('academics.demonstrator_time') ({{ __('common.minutes') }})
                                                </label>
                                                <input
                                                    class="primary_input_field form-control{{ $errors->has('demonstrator_time') ? ' is-invalid' : '' }}"
                                                    type="number" min="0" name="demonstrator_time"
                                                    id="demonstrator_time" autocomplete="off"
                                                    value="{{ old('demonstrator_time', $subject->demonstrator_time ?? '') }}">
                                                @if ($errors->has('demonstrator_time'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('demonstrator_time') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Student Practical --}}
                                    <div class="row mt-20">
                                        <div class="col-lg-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="practical_time">
                                                    @lang('academics.practical_time') ({{ __('common.minutes') }})
                                                </label>
                                                <input
                                                    class="primary_input_field form-control{{ $errors->has('practical_time') ? ' is-invalid' : '' }}"
                                                    type="number" min="0" name="practical_time" id="practical_time"
                                                    autocomplete="off"
                                                    value="{{ old('practical_time', $subject->practical_time ?? '') }}">
                                                @if ($errors->has('practical_time'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('practical_time') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Submit --}}
                                    <div class="row mt-20">
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg submit" type="submit">
                                                <span class="ti-check"></span>
                                                {{ isset($subjectId) ? __('common.update') : __('academics.save_class') }}
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div class="col-lg-9">

                    <div class="row white-box">
                        <div class="col-lg-12">

                            <div class="main-title">
                                <h3 class="mb-20">@lang('academics.subject_list')</h3>
                            </div>

                            <x-table>
                                <table id="table_id" class="table Crm_table_active3" cellspacing="0" width="100%">

                                    <thead>

                                        <tr>
                                            <th> @lang('common.sl')</th>
                                            <th> @lang('academics.subject')</th>
                                            <th> @lang('academics.subject_type')</th>
                                            <th>@lang('academics.subject_code')</th>
                                            <th>@lang('academics.demonstrator_time')</th>
                                            <th>@lang('academics.practical_time')</th>
                                            <th>Status</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subjects as $key => $subject)
                                            @php
                                                $isAssigned = ($subject->assign_subjects_count ?? 0) > 0;
                                            @endphp
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $subject->name }}</td>
                                                <td>
                                                    {{ $subject->type == 'T' ? __('academics.theory') : __('academics.practical') }}
                                                </td>
                                                <td>
                                                    <a href="#">{{ $subject->code }}</a>
                                                </td>
                                                <td>{{ $subject->demonstrator_time ?? '-' }}</td>
                                                <td>{{ $subject->practical_time ?? '-' }}</td>
                                                <td>{{ $subject->is_active ? 'Activate' : 'Deactivate' }}</td>
                                                <td valign="top">
                                                    @if ($isAssigned)
                                                        <span class="badge badge-secondary" title="@lang('academics.subject_in_use_cannot_edit_delete')">
                                                            @lang('common.in_use')
                                                        </span>
                                                    @else
                                                        @php
                                                            $routeList = [
                                                                '<a class="dropdown-item subject-edit-link" href="' .
                                                                route('subject.edit', $subject->id) .
                                                                '">' .
                                                                __('common.edit') .
                                                                '</a>',
                                                                '<a class="dropdown-item" data-toggle="modal" data-target="#toggleStatusModal' .
                                                                $subject->id .
                                                                '" href="#">' .
                                                                ($subject->is_active ?? true
                                                                    ? 'Deactivate'
                                                                    : 'Activate') .
                                                                '</a>',
                                                            ];
                                                        @endphp
                                                        <x-drop-down-action-component :routeList="$routeList" />
                                                    @endif
                                                </td>
                                            </tr>

                                            @unless ($isAssigned)
                                                <div class="modal fade admin-query"
                                                    id="toggleStatusModal{{ $subject->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">
                                                                    {{ $subject->is_active ?? true ? 'Deactivate Subject' : 'Activate Subject' }}
                                                                </h4>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>

                                                            <div class="modal-body text-center">
                                                                <h4>
                                                                    @if ($subject->is_active ?? true)
                                                                        Are you sure you want to deactivate
                                                                        "{{ $subject->name }}"?
                                                                    @else
                                                                        Are you sure you want to activate
                                                                        "{{ $subject->name }}"?
                                                                    @endif
                                                                </h4>
                                                                <div class="mt-40 d-flex justify-content-between">
                                                                    <button type="button" class="primary-btn tr-bg"
                                                                        data-dismiss="modal">@lang('common.cancel')</button>
                                                                    <form
                                                                        action="{{ route('subject.toggle-status', $subject->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="dt_page"
                                                                            class="dt-page-field" value="0">
                                                                        <button class="primary-btn fix-gr-bg" type="submit">
                                                                            {{ $subject->is_active ?? true ? 'Deactivate' : 'Activate' }}
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endunless
                                        @endforeach
                                    </tbody>
                                </table>
                            </x-table>
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

        $(document).on('click', '.subject-edit-link', function(e) {
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
