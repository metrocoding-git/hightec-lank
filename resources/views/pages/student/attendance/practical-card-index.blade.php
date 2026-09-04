@extends('layouts.app')
@push('css')
    <style>
        /* Matches the disabled-students list window's look (card/table/
               breadcrumb) — same --gradient_1/--gradient_2/--border_color
               custom properties that page already relies on, so this doesn't
               redefine them, just reuses them. */
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

        #attendance-cards-table thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

        #attendance-cards-table tbody tr {
            transition: background-color .12s ease;
        }

        #attendance-cards-table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        #attendance-cards-table tbody td {
            vertical-align: middle;
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
    Student Attendance Cards
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Student Attendance Cards</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Student Attendance Cards</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor full_wide_table">
        <div class="container-fluid p-0">

            <form class="form-horizontal" method="GET" action="{{ route('student-practical-card.index') }}"
                id="cardsFilterForm">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white-box">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="primary_input">
                                        <select class="primary_select form-control" name="branch_id" id="branch-select">
                                            <option value="">Select Branch</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}" @selected((string) $selectedBranchId === (string) $branch->id)>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Intake — populated via AJAX once a branch is picked, same
                                     cascade the disabled-students search form already uses. --}}
                                <div class="col-lg-4">
                                    <div class="primary_input">
                                        <select class="primary_select form-control" name="intake_id" id="intake-select">
                                            <option value="">Select Intake</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Course — populated via AJAX once an intake is picked. --}}
                                <div class="col-lg-4">
                                    <div class="primary_input">
                                        <select class="primary_select form-control" name="course_id" id="course-select">
                                            <option value="">Select Course</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-2">
                                    <div class="primary_input">
                                        <input type="text" name="q" class="form-control primary_input_field"
                                            value="{{ $q }}" placeholder="Search (Name / Admission No / NIC)">
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-10 text-right">
                                    <div class="row">
                                        <button type="submit" class="primary-btn small fix-gr-bg p-2 col-8">
                                            <span class="ti-search pr-2"></span>
                                            Filter
                                        </button>
                                        <a href="{{ route('student-practical-card.index') }}"
                                            class="primary-btn small tr-bg p-2 col-4">Reset</a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">Student Attendance Cards</h3>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="attendance-cards-table"
                                        class="table data-table Crm_table_active3 no-footer dtr-inline collapsed"
                                        cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Admission No</th>
                                                <th>Student Name</th>
                                                <th>NIC</th>
                                                <th>Branch</th>
                                                <th>Card</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($students as $index => $student)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $student->admission_number ?? '-' }}</td>
                                                    <td>{{ $student->student_name }}</td>
                                                    <td>{{ $student->national_id_number ?? '-' }}</td>
                                                    <td>{{ $student->branch->name ?? '-' }}</td>
                                                    <td>
                                                        <a href="{{ route('student-practical-card.show', $student->id) }}"
                                                            target="_blank" rel="noopener"
                                                            class="primary-btn small fix-gr-bg p-2">
                                                            <i class="fa-solid fa-id-card"></i> View Card
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">
                                                        @if ($hasFilter ?? false)
                                                            No students found.
                                                        @else
                                                            Select a Branch, Intake, Course or search above to see results.
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforelse
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

@pushOnce('scripts')
    <script>
        // Same branch->intake->course AJAX cascade as the disabled-students
        // search form, reusing the same two existing endpoints.
        //
        // BUG FIX: ".primary_select" elements on this app are enhanced by
        // the niceSelect plugin, which renders its own separate dropdown
        // widget on top of the real <select> at page-load time. Swapping
        // the real <select>'s options with jQuery .html() (what this code
        // was already doing) DOES update the underlying element — that's
        // why the API response clearly contains the right data — but
        // niceSelect's own visible widget is a snapshot from init time and
        // does NOT auto-refresh from that change. It needs an explicit
        // '.niceSelect('update')' call after every .html() swap, exactly
        // like the disabled-students reference page already does; that
        // call was missing here, which is why the intake dropdown looked
        // empty/unchanged even though the data had already arrived.
        function refreshSelect($el) {
            if ($el.hasClass('primary_select') && typeof $el.niceSelect === 'function') {
                $el.niceSelect('update');
            }
        }

        function loadIntakes(branchId, preselectIntakeId, preselectCourseId) {
            var intakeSelect = $('#intake-select');
            var courseSelect = $('#course-select');

            if (!branchId) {
                intakeSelect.html('<option value="">Select Intake</option>');
                courseSelect.html('<option value="">Select Course</option>');
                refreshSelect(intakeSelect);
                refreshSelect(courseSelect);
                return;
            }

            intakeSelect.html('<option value="">Loading intakes...</option>');
            courseSelect.html('<option value="">Select Course</option>');
            refreshSelect(intakeSelect);
            refreshSelect(courseSelect);

            var intakesUrl = '{{ route('get-intakes-by-branch', ':branchId') }}'.replace(':branchId', branchId);
            console.log('[attendance-cards] fetching intakes ->', intakesUrl);

            $.ajax({
                url: intakesUrl,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(data) {
                    console.log('[attendance-cards] intakes response ->', data);
                    var options = '<option value="">Select Intake</option>';

                    if (data.intakes && data.intakes.length > 0) {
                        data.intakes.forEach(function(intake) {
                            var selected = preselectIntakeId && String(preselectIntakeId) === String(
                                intake.id) ? ' selected' : '';
                            options += '<option value="' + intake.id + '"' + selected + '>' + intake
                                .name + '</option>';
                        });
                    } else {
                        options = '<option value="">No intakes available for this branch</option>';
                    }

                    intakeSelect.html(options);
                    refreshSelect(intakeSelect);

                    if (preselectIntakeId) {
                        loadCourses(preselectIntakeId, preselectCourseId);
                    }
                },
                error: function(xhr, status, err) {
                    console.error('[attendance-cards] intakes request failed ->', status, xhr.status, xhr
                        .responseText, err);
                    intakeSelect.html('<option value="">Error loading intakes</option>');
                    courseSelect.html('<option value="">Select Course</option>');
                    refreshSelect(intakeSelect);
                    refreshSelect(courseSelect);
                }
            });
        }

        function loadCourses(intakeId, preselectCourseId) {
            var courseSelect = $('#course-select');

            if (!intakeId) {
                courseSelect.html('<option value="">Select Course</option>');
                refreshSelect(courseSelect);
                return;
            }

            courseSelect.html('<option value="">Loading courses...</option>');
            refreshSelect(courseSelect);

            $.ajax({
                url: '{{ route('get-courses-by-intake', ':intakeId') }}'.replace(':intakeId', intakeId),
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(data) {
                    var options = '<option value="">Select Course</option>';

                    if (data.courses && data.courses.length > 0) {
                        data.courses.forEach(function(course) {
                            var selected = preselectCourseId && String(preselectCourseId) === String(
                                course.id) ? ' selected' : '';
                            options += '<option value="' + course.id + '"' + selected + '>' + course
                                .name + '</option>';
                        });
                    } else {
                        options = '<option value="">No courses available for this intake</option>';
                    }

                    courseSelect.html(options);
                    refreshSelect(courseSelect);
                },
                error: function() {
                    courseSelect.html('<option value="">Error loading courses</option>');
                    refreshSelect(courseSelect);
                }
            });
        }

        // Delegated (not a direct .on() bind) so this keeps working even if
        // something re-renders #branch-select after this script first runs
        // (e.g. a global niceSelect() init elsewhere in the layout that
        // fires later than expected) — matches #intake-select's binding
        // below, which was already delegated.
        $(document).on('change', '#branch-select', function() {
            console.log('[attendance-cards] branch changed ->', $(this).val());
            loadIntakes($(this).val(), null, null);
        });

        $(document).on('change', '#intake-select', function() {
            console.log('[attendance-cards] intake changed ->', $(this).val());
            loadCourses($(this).val(), null);
        });

        // Restore the Intake/Course dropdowns after a filtered reload —
        // ?branch_id=...&intake_id=...&course_id=... comes back from the
        // server, but those two selects start out AJAX-populated and empty,
        // so without this the filtered results would show while the form
        // looked reset.
        $(function() {
            var initialBranchId = @json($selectedBranchId);
            var initialIntakeId = @json($selectedIntakeId);
            var initialCourseId = @json($selectedCourseId);

            if (initialBranchId) {
                loadIntakes(initialBranchId, initialIntakeId, initialCourseId);
            }
        });

        $('#attendance-cards-table').DataTable({
            responsive: true,
            dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 '" +
                "<'dt-toolbar-title'>" +
                "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-search'f><'dt-buttons-wrap'B>>" +
                ">" +
                "<'row'<'col-sm-12'tr>>" +
                "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>>",
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            order: [
                [0, 'asc']
            ],
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
            pageLength: 100,
            language: {
                search: '<i class=""></i>',
                searchPlaceholder: 'Search...',
                paginate: {
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            },
        });
    </script>
@endPushOnce
