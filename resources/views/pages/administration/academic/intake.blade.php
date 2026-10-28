@extends('layouts.app')
@push('css')
    <style>
        #table_id thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

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

        /* Same as the courses page: keeps horizontal scroll on the table
               from also dragging the left-side form sideways on large screens. */
        .intake-table-scroll {
            overflow-x: auto;
            max-width: 100%;
        }

        /* ---- Modern refresh: cards, table, status badges (Section 20) ---- */
        .white-box {
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
        }

        .white-box .main-title h3 {
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

        .intake-table-scroll table.Crm_table_active3 thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

        .intake-table-scroll table.Crm_table_active3 tbody tr {
            transition: background-color .12s ease;
        }

        .intake-table-scroll table.Crm_table_active3 tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        .intake-table-scroll table.Crm_table_active3 tbody td {
            vertical-align: middle;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .3px;
            white-space: nowrap;
        }

        .status-badge.status-active {
            background: #e6f7ec;
            color: #1e8e4f;
        }

        .status-badge.status-inactive {
            background: #fdecec;
            color: #d64545;
        }

        .dropdown-item.text-danger:hover {
            background-color: #fdecec;
        }

        .course-selection-container {
            border: 1px solid var(--border_color);
            border-radius: 8px;
            padding: 12px 14px;
            max-height: 220px;
            overflow-y: auto;
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
                <h1>Intake</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Intake</a>
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid p-0">
        @include('components.partials.permanent-delete-note', ['rule' => 'intake'])
    </div>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            @php
                // Button-level permissions (Section 21).
                $canAddIntake = Auth::user()->hasPermission('intake.store');
                $canEditIntake = Auth::user()->hasPermission('intake.update');
                $canDeleteIntake = Auth::user()->hasPermission('intake.destroy');
                $showIntakeForm = isset($intakeId) ? $canEditIntake : $canAddIntake;
            @endphp

            <div class="row">
                @if ($showIntakeForm)
                    <div class="col-lg-3">
                        <div class="row">
                            <div class="col-lg-12">

                                <form method="POST"
                                    action="{{ isset($intakeId) ? route('intake.update', $intake->id) : route('intake.store') }}">
                                    @csrf
                                    <input type="hidden" name="dt_page" class="dt-page-field"
                                        value="{{ (int) request('dt_page', 0) }}">
                                    <div class="white-box">
                                        <div class="main-title">
                                            <h3 class="mb-20">{{ isset($intakeId) ? 'Edit Intake' : 'Add Intake' }}</h3>
                                        </div>

                                        <div class="row">
                                            {{-- Branch --}}
                                            <div class="col-lg-12">
                                                <div class="primary_input">
                                                    <label class="primary_input_label">Branch <span>*</span></label>
                                                    <select name="branch_id"
                                                        class="primary_select form-control @error('branch_id') is-invalid @enderror">
                                                        <option value=""> Select Branch </option>
                                                        @foreach ($branches as $key => $branche)
                                                            <option value="{{ $branche->id }}"
                                                                {{ isset($intakeId) && $intake->branch->id === $branche->id ? 'selected' : '' }}>
                                                                {{ $branche->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('branch_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Intake Name --}}
                                            <div class="col-lg-12 mt-20">
                                                <div class="primary_input">
                                                    <label class="primary_input_label">Intake Name <span>*</span></label>
                                                    <input type="text" name="name"
                                                        class="primary_input_field form-control @error('name') is-invalid @enderror"
                                                        value="{{ old('name', $intake->name ?? '') }}" autocomplete="off">
                                                    @error('name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{-- Intake Date --}}
                                            <div class="col-lg-12 mt-20">
                                                <div class="primary_input">
                                                    <label class="primary_input_label">Intake Start Date
                                                        <span>*</span></label>
                                                    <input type="date" name="date"
                                                        class="primary_input_field form-control @error('date') is-invalid @enderror"
                                                        value="{{ old('date', $intake->start_date ?? '') }}"
                                                        autocomplete="off">
                                                    @error('date')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Courses Selection --}}
                                            <div class="col-lg-12 mt-20">
                                                <label class="primary_input_label">Courses <span>*</span></label>
                                                <div class="course-selection-container">
                                                    @foreach ($courses as $course)
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox"
                                                                class="common-checkbox form-control course-checkbox"
                                                                name="courses[]" id="course_{{ $course->id }}"
                                                                value="{{ $course->id }}"
                                                                {{ isset($intakeId) && in_array($course->id, old('courses', $intake->courses->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                                                            <label for="course_{{ $course->id }}"
                                                                class="font-weight-bold">
                                                                {{ $course->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @error('courses')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>


                                            {{-- Submit --}}
                                            <div class="col-lg-12 mt-30 text-center">
                                                <button type="submit" class="primary-btn fix-gr-bg col-12">
                                                    <span class="ti-check"></span>
                                                    {{ isset($intakeId) ? 'Update' : 'Save' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>


                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-lg-{{ $showIntakeForm ? '9' : '12' }}">

                    <div class="row white-box">
                        <div class="col-lg-12">

                            <div class="main-title">
                                <h3 class="mb-20">Intake List</h3>
                            </div>

                            <div class="intake-table-scroll">
                                <x-table>
                                    <table id="table_id" class="table Crm_table_active3" cellspacing="0" width="100%">

                                        <thead>

                                            <tr>
                                                <th>Intake</th>
                                                <th>Branch</th>
                                                <th>Courses</th>
                                                <th>Start Date</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($intakes as $index => $intake)
                                                <tr>
                                                    <td valign="top">{{ $intake->name }}</td>
                                                    <td>{{ $intake->branch->name }}</td>
                                                    <td>
                                                        @foreach ($intake->courses as $index => $course)
                                                            {{ $course->name }}
                                                            @if (!$loop->last)
                                                                /
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td>{{ $intake->start_date }}</td>

                                                    <td valign="top">

                                                        @php
                                                            $routeList = [];
                                                            if ($canEditIntake) {
                                                                $routeList[] =
                                                                    '<a class="dropdown-item intake-edit-link" href="' .
                                                                    route('intake.edit', $intake->id) .
                                                                    '">' .
                                                                    __('common.edit') .
                                                                    '</a>';
                                                            }
                                                            if ($canDeleteIntake) {
                                                                $routeList[] =
                                                                    '<a class="dropdown-item text-danger" data-toggle="modal" data-target="#deleteModal' .
                                                                    $intake->id .
                                                                    '" href="#">Delete</a>';
                                                            }
                                                        @endphp
                                                        @if (count($routeList) > 0)
                                                            <x-drop-down-action-component :routeList="$routeList" />
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                </tr>

                                                @if ($canDeleteIntake)
                                                    <div class="modal fade admin-query"
                                                        id="deleteModal{{ $intake->id }}">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Delete Intake</h4>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <h4>
                                                                        Are you sure you want to delete
                                                                        "{{ $intake->name }}"?
                                                                        This cannot be undone.
                                                                    </h4>
                                                                    <div class="mt-40 d-flex justify-content-between">
                                                                        <button type="button" class="primary-btn tr-bg"
                                                                            data-dismiss="modal">@lang(key: 'common.cancel')</button>
                                                                        <form
                                                                            action="{{ route('intake.destroy', $intake->id) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            @method('DELETE')

                                                                            <input type="hidden" name="dt_page"
                                                                                class="dt-page-field" value="0">
                                                                            <button class="primary-btn fix-gr-bg"
                                                                                type="submit">
                                                                                Delete
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

        $(document).on('click', '.intake-edit-link', function(e) {
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
