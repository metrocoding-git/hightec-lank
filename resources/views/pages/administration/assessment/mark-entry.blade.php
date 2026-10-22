@extends('layouts.app')
@push('css')
    <style>
        .ampl-card {
            background: #fff;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 16px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        }

        .ampl-input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #d7dde3;
            border-radius: 6px;
            font-size: 14px;
            background: #fff;
        }

        .ampl-input:focus {
            outline: none;
            border-color: #1d4e89;
        }

        .ampl-filter-label {
            font-weight: 600;
            font-size: 12px;
            color: #6b7784;
            display: block;
            margin-bottom: 4px;
        }

        .ampl-btn {
            border: none;
            border-radius: 6px;
            padding: 9px 18px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .ampl-btn-outline {
            background: #fff;
            border: 1px solid #1d4e89;
            color: #1d4e89;
            margin-top: 8px;
        }

        .ampl-btn-danger {
            background: #fff;
            border: 1px solid #c0392b;
            color: #c0392b;
        }

        .ampl-btn-danger:disabled {
            opacity: .5;
            cursor: default;
        }

        .ampl-banner {
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .ampl-banner-error {
            background: #fdecea;
            color: #a12a2a;
            border: 1px solid #f5c6c6;
        }

        .ampl-banner-success {
            background: #e8f6ee;
            color: #1e7a43;
            border: 1px solid #bfe6cf;
        }

        .ampl-studentrow {
            font-size: 14px;
            color: #37474f;
        }

        .ampl-muted {
            color: #8a95a1;
            font-size: 13px;
        }

        .ampl-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
        }

        .ampl-badge-locked {
            background: #f4f6f8;
            color: #6b7784;
        }

        .ampl-badge-open {
            background: #e8f6ee;
            color: #1e7a43;
        }

        .ampl-locked-note {
            color: #8a95a1;
            font-size: 13px;
            margin-top: 8px;
        }

        .ampl-context-ribbon {
            flex-wrap: wrap;
            align-items: center;
            background: linear-gradient(90deg, #1d4e89, #2f6fb0);
            color: #fff;
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .ampl-context-ribbon .chip {
            background: rgba(255, 255, 255, .16);
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mt-15 {
            margin-top: 15px;
        }
    </style>
@endpush
@section('title')
    Final Assessment Marks
@endsection
@section('mainContent')
    @php
        $canAddAssessment = Auth::user()->hasPermission('student-assessment.store');
        $canUndoAssessment = Auth::user()->hasPermission('student-assessment.destroy');
        $categoriesByPair = $categoriesByPair ?? collect();
    @endphp
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Final Assessment Marks</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Final Assessment Marks</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Find Student (plain HTML + jQuery, no React / @verbatim) ===== --}}
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            @if ($canAddAssessment)
                <div class="ampl-card col-12">
                    <form id="ampl-find-form" class="form-horizontal row pb-0 mb-0">
                        <div class="col-12">
                            <input type="text" id="ampl-regno" class="ampl-input" placeholder="Enter Student Reg Number"
                                autocomplete="off">
                        </div>
                        <div class="col-12">
                            <button type="submit" id="ampl-find-btn"
                                class="primary-btn small fix-gr-bg me-2 p-2 mt-1 col-12">Find Student</button>
                        </div>
                    </form>
                    <div id="ampl-restoring" class="ampl-muted mt-10" style="display:none;">Restoring your last search…
                    </div>
                </div>
            @endif

            <div id="ampl-banner" class="ampl-banner" style="display:none;"></div>

            {{-- Student + course picker --}}
            <div id="ampl-student-card" class="ampl-card" style="display:none;">
                <div class="ampl-studentrow text-center">
                    <strong id="ampl-student-title"></strong>
                </div>
                <div class="mt-10">
                    <div class="col-12">
                        <select id="ampl-course-select" class="form-control col-12">
                            <option value="">Select Course</option>
                        </select>
                    </div>
                </div>
                <div id="ampl-no-existing" class="mt-10" style="display:none;">
                    <div class="ampl-muted">
                        No existing assessment for this course. A marks-entry window should have
                        opened automatically — if your browser blocked it, use the button below.
                    </div>
                    <button type="button" id="ampl-open-marks" class="ampl-btn ampl-btn-outline col-12">
                        Open Marks-Entry Window
                    </button>
                </div>
            </div>

            {{-- Existing assessment --}}
            <div id="ampl-existing-card" class="ampl-card col-12" style="display:none;">
                <div class="ampl-context-ribbon row">
                    <span class="chip col-5" id="ampl-ex-name"></span>
                    <span class="chip col-2" id="ampl-ex-regno"></span>
                    <span class="chip col-5" id="ampl-ex-module"></span>
                </div>
                <div class="ampl-studentrow text-center">
                    <h4 style="margin:0;" id="ampl-ex-module-title"></h4>
                    <span id="ampl-ex-badge" class="ampl-badge mt-1"></span>
                </div>
                <div class="ampl-muted mt-10 text-center" id="ampl-ex-note"></div>
                <div class="mt-15 row">
                    <a id="ampl-ex-pdf" href="#" target="_blank" rel="noreferrer"
                        class="primary-btn small fix-gr-bg col-12 p-2">Export PDF</a>
                    <button type="button" id="ampl-undo-btn" class="ampl-btn ampl-btn-danger col-12 mt-2 text-center"
                        style="display:none;">
                        Undo This Assessment
                    </button>
                    <div id="ampl-locked-note" class="ampl-locked-note col-12 mt-2 text-center" style="display:none;">
                        Only the staff member who created this, within 2 days, can undo it.
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== Filters + table ===== --}}
    <section class="admin-visitor-area up_admin_visitor full_wide_table mt-10">
        <div class="container-fluid p-0">
            <div class="white-box p-20 mb-20 mt-40">
                <form method="GET" action="{{ route('student-assessment.index') }}" class="row align-items-end mb-15">
                    <div class="col-lg-4 mb-2">
                        <select name="course_id" class="primary_select form-control ajax_select">
                            <option value="">All Courses</option>
                            @foreach ($courseOptions ?? [] as $c)
                                <option value="{{ $c->id }}"
                                    {{ (string) request('course_id') === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 mb-2">
                        <select name="result" class="primary_select form-control ajax_select">
                            <option value="">All Results</option>
                            <option value="PASS" {{ request('result') === 'PASS' ? 'selected' : '' }}>Pass</option>
                            <option value="FAIL" {{ request('result') === 'FAIL' ? 'selected' : '' }}>Fail</option>
                        </select>
                    </div>
                    <div class="col-lg-4 mb-2">
                        <select name="created_by" class="primary_select form-control ajax_select">
                            <option value="">Everyone</option>
                            @foreach ($creatorOptions ?? [] as $u)
                                <option value="{{ $u->id }}"
                                    {{ (string) request('created_by') === (string) $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6 mb-2">
                        <span class="ampl-filter-label">Created Date Range</span>
                        <input type="text" id="filter_date_range" name="date_range" class="form-control"
                            value="{{ request('date_range') }}" placeholder="Select Created Date Range"
                            autocomplete="off">
                    </div>
                    <div class="col-lg-6 mb-2">
                        <span class="ampl-filter-label">Course Start Date Range</span>
                        <input type="text" id="filter_start_date_range" name="start_date_range" class="form-control"
                            value="{{ request('start_date_range') }}" placeholder="Select Course Start Date Range"
                            autocomplete="off">
                    </div>
                    <div class="col-lg-6 mb-2">
                        <select name="branch_id" class="primary_select form-control ajax_select">
                            <option value="">All Branches (Evaluator)</option>
                            @foreach ($branchOptions ?? [] as $b)
                                <option value="{{ $b->id }}"
                                    {{ (string) request('branch_id') === (string) $b->id ? 'selected' : '' }}>
                                    {{ $b->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6 mb-2">
                        <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                            placeholder="Search (Name / Reg No / NIC)">
                    </div>
                    <div class="col-12 mt-10">
                        <div class="row align-items-center">
                            <div class="col-10">
                                <button type="submit" class="primary-btn small fix-gr-bg col-12 p-2">
                                    <span class="ti-search pr-2"></span> Search
                                </button>
                            </div>
                            <div class="col-2">
                                <a href="{{ route('student-assessment.index') }}"
                                    class="primary-btn small fix-gr-bg col-12 p-2">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">Final Assessment Records</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id"
                                        class="table data-table Crm_table_active3 no-footer dtr-inline collapsed"
                                        cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>#</th>
                                                <th>Student</th>
                                                <th>Reg No</th>
                                                <th>NIC Number</th>
                                                <th>Course</th>
                                                <th>Module</th>
                                                <th>Theory Subjects</th>
                                                <th>Practical Subjects</th>
                                                <th>Created Date</th>
                                                <th>Total</th>
                                                <th>Result</th>
                                                <th>Grade</th>
                                                <th>Student Category</th>
                                                <th>Address</th>
                                                <th>Name Verified</th>
                                                <th>NIC / ID No. Verified</th>
                                                <th>Address Verified</th>
                                                <th>Start Date</th>
                                                <th>Created By</th>
                                                <th>Intake</th>
                                                <th>FS Conduct Branch</th>
                                                <th>Intake Branch</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recent ?? collect() as $i => $a)
                                                @php
                                                    $assessedCourseId = optional($a->template->course)->id;
                                                    $categoryKey = $a->student_id . '-' . $assessedCourseId;
                                                    $studentCategory =
                                                        optional($categoriesByPair->get($categoryKey))->category_name ??
                                                        '-';
                                                    $theoryProgress = $a->theoryProgress ?? [
                                                        'completed' => 0,
                                                        'total' => 0,
                                                    ];
                                                    $practicalProgress = $a->practicalProgress ?? [
                                                        'completed' => 0,
                                                        'total' => 0,
                                                    ];
                                                @endphp
                                                <tr>
                                                    <td></td>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $a->student->student_name ?? '-' }}</td>
                                                    <td>{{ $a->student->admission_number ?? '-' }}</td>
                                                    <td>{{ $a->student->national_id_number ?? '-' }}</td>
                                                    <td>{{ $a->template->course->name ?? '-' }}</td>
                                                    <td>{{ $a->template->module_name ?? '-' }}</td>
                                                    <td>{{ $theoryProgress['completed'] }}/{{ $theoryProgress['total'] }}
                                                    </td>
                                                    <td>{{ $practicalProgress['completed'] }}/{{ $practicalProgress['total'] }}
                                                    </td>
                                                    <td>{{ optional($a->assessed_at)->format('Y-m-d') }}</td>
                                                    <td>{{ $a->total_marks }} / {{ $a->max_marks }}</td>
                                                    <td>
                                                        @if ($a->is_qualified)
                                                            <span class="badge badge-success">PASS</span>
                                                        @else
                                                            <span class="badge badge-danger">FAIL</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $a->grade ?? '-' }}</td>
                                                    <td>{{ $studentCategory }}</td>
                                                    <td>{{ $a->student->permanent_address ?? '-' }}</td>
                                                    <td>
                                                        @if ($a->student && $a->student->isVerified('Full Name Verified'))
                                                            <span class="text-success">YES</span>
                                                        @else
                                                            NO
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($a->student && $a->student->isVerified('NIC Verified'))
                                                            <span class="text-success">YES</span>
                                                        @else
                                                            NO
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($a->student && $a->student->isVerified('Address Verified'))
                                                            <span class="text-success">YES</span>
                                                        @else
                                                            NO
                                                        @endif
                                                    </td>
                                                    <td>{{ $a->studentCourse->intake->start_date ?? '-' }}</td>
                                                    <td>{{ $a->evaluator->name ?? '-' }}</td>
                                                    <td>{{ $a->studentCourse->intake->name ?? '-' }}</td>
                                                    <td>{{ $a->evaluator->branches->first()->name ?? '-' }}</td>
                                                    <td>{{ $a->studentCourse->branch->name ?? '-' }}</td>
                                                    <td>
                                                        <a href="{{ url('/student/student-info/assessment/' . $a->id . '/pdf') }}"
                                                            class="btn btn-sm btn-outline-primary" target="_blank">PDF</a>
                                                    </td>
                                                </tr>
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

@pushOnce('scripts')
    <script>
        /* ================= DataTable + date pickers (same as other pages) ================= */
        flatpickr("#filter_date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: true,
        });

        flatpickr("#filter_start_date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: true,
        });

        $('#table_id').DataTable({
            responsive: true,
            dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3'" +
                "<'dt-toolbar-title'>" +
                "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-length'l><'dt-search'f><'dt-buttons-wrap'B>>" +
                ">" +
                "<'row'<'col-sm-12'tr>>" +
                "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>> ",
            paging: true,
            searching: true,
            ordering: true,
            info: true,
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
            lengthMenu: [
                [25, 50, 100, -1],
                [25, 50, 100, "All"]
            ],
            pageLength: 100,
            language: {
                search: '<i class=""></i>',
                searchPlaceholder: 'Search...',
                emptyTable: 'No assessments found.',
                paginate: {
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            },
        });

        /* ================= Find Student / marks entry (jQuery) ================= */
        (function() {
            var CSRF = "{{ csrf_token() }}";
            var ROUTES = {
                find: "{{ route('student-assessment.find') }}",
                existing: "{{ url('/student/student-info/assessment/existing') }}",
                marksPopup: "{{ url('/student/student-info/assessment/marks-popup') }}",
                assessmentBase: "{{ url('/student/student-info/assessment') }}"
            };
            var POPUP_FEATURES =
                'width=1100,height=850,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes';

            var state = {
                regNo: '',
                student: null,
                courses: [],
                studentCourseId: '',
                existing: null,
                canUndo: false,
                busy: false
            };

            function esc(s) {
                return $('<div>').text(s == null ? '' : String(s)).html();
            }

            function ajaxErrorMessage(xhr) {
                var data = xhr.responseJSON;
                if (!data) {
                    return xhr.status === 0 ?
                        'Network error — could not reach the server.' :
                        'Server returned a non-JSON response (HTTP ' + xhr.status + '). Check the route/URL.';
                }
                if (xhr.status === 422 && data.errors) {
                    var msgs = [];
                    $.each(data.errors, function(k, v) {
                        msgs = msgs.concat(v);
                    });
                    return msgs.join(' ') || data.message || 'Validation failed.';
                }
                return data.message || ('Request failed (HTTP ' + xhr.status + ').');
            }

            function api(url, method, body) {
                return $.ajax({
                    url: url,
                    type: method || 'GET',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                    contentType: body ? 'application/json' : undefined,
                    data: body ? JSON.stringify(body) : undefined,
                    dataType: 'json'
                });
            }

            function showBanner(type, text) {
                $('#ampl-banner')
                    .removeClass('ampl-banner-error ampl-banner-success')
                    .addClass('ampl-banner-' + type)
                    .text(text)
                    .show();
            }

            function hideBanner() {
                $('#ampl-banner').hide().text('');
            }

            function setBusy(b) {
                state.busy = b;
                $('#ampl-find-btn').prop('disabled', b).text(b ? 'Searching…' : 'Find Student');
                $('#ampl-course-select').prop('disabled', b);
                $('#ampl-undo-btn').prop('disabled', b).text(b ? 'Undoing…' : 'Undo This Assessment');
                render();
            }

            function updateUrl(admissionNumber, studentCourseId) {
                var params = new URLSearchParams(window.location.search);
                if (admissionNumber) params.set('admission_number', admissionNumber);
                else params.delete('admission_number');
                if (studentCourseId) params.set('student_course_id', studentCourseId);
                else params.delete('student_course_id');
                var qs = params.toString();
                window.history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
            }

            function watchPopupAndRefreshOnClose(popup) {
                if (!popup) return;
                var timer = setInterval(function() {
                    if (popup.closed) {
                        clearInterval(timer);
                        window.location.reload();
                    }
                }, 500);
            }

            function render() {
                // Student card
                if (state.student) {
                    $('#ampl-student-title').text(state.student.name + ' - ' + state.student.admission_number);
                    $('#ampl-student-card').show();
                } else {
                    $('#ampl-student-card').hide();
                }

                // "No existing" helper
                $('#ampl-no-existing').toggle(!state.existing && !!state.studentCourseId && !state.busy);

                // Existing assessment card
                var ex = state.existing;
                if (ex && state.student) {
                    var moduleName = ex.template ? ex.template.module_name : '';
                    $('#ampl-ex-name').text(state.student.name);
                    $('#ampl-ex-regno').text(state.student.admission_number);
                    $('#ampl-ex-module').text(moduleName);
                    $('#ampl-ex-module-title').text(moduleName);
                    $('#ampl-ex-badge')
                        .removeClass('ampl-badge-open ampl-badge-locked')
                        .addClass(state.canUndo ? 'ampl-badge-open' : 'ampl-badge-locked')
                        .text(state.canUndo ? 'Undo available' : 'Locked');
                    $('#ampl-ex-note').text(
                        'This student already has a saved assessment for this course (by ' +
                        (ex.evaluator ? ex.evaluator.name : 'unknown') + ', ' +
                        (ex.created_at ? ex.created_at.substring(0, 10) : '') + ').'
                    );
                    $('#ampl-ex-pdf').attr('href', ROUTES.assessmentBase + '/' + ex.id + '/pdf');
                    $('#ampl-undo-btn').toggle(state.canUndo);
                    $('#ampl-locked-note').toggle(!state.canUndo);
                    $('#ampl-existing-card').show();
                } else {
                    $('#ampl-existing-card').hide();
                }
            }

            function fillCourses() {
                var $sel = $('#ampl-course-select');
                var html = '<option value="">Select Course</option>';
                $.each(state.courses, function(_, c) {
                    html += '<option value="' + esc(c.student_course_id) + '">' + esc(c.course_name) +
                        '</option>';
                });
                $sel.html(html).val(state.studentCourseId || '');
            }

            function resolveCourseSelection(scId, popup) {
                setBusy(true);
                hideBanner();
                return api(ROUTES.existing + '/' + scId)
                    .done(function(data) {
                        if (data.status === 'ok' && data.assessment) {
                            state.existing = data.assessment;
                            state.canUndo = !!data.can_undo;
                            if (popup && !popup.closed) popup.close();
                        } else {
                            state.existing = null;
                            state.canUndo = false;
                            if (popup) {
                                popup.location = ROUTES.marksPopup + '/' + scId;
                                popup.focus();
                                watchPopupAndRefreshOnClose(popup);
                            }
                        }
                    })
                    .fail(function(xhr) {
                        if (popup && !popup.closed) popup.close();
                        showBanner('error', ajaxErrorMessage(xhr));
                    })
                    .always(function() {
                        setBusy(false);
                    });
            }

            function findStudent(admissionNumber) {
                return api(ROUTES.find, 'POST', {
                    admission_number: admissionNumber
                });
            }

            // Find Student submit
            $('#ampl-find-form').on('submit', function(e) {
                e.preventDefault();
                var regNo = $.trim($('#ampl-regno').val());
                if (!regNo) return;
                state.regNo = regNo;
                setBusy(true);
                hideBanner();
                findStudent(regNo)
                    .done(function(data) {
                        state.student = data.student;
                        state.courses = data.courses || [];
                        state.studentCourseId = '';
                        state.existing = null;
                        state.canUndo = false;
                        fillCourses();
                        updateUrl(regNo, '');
                        // A course started more than once is left out of the list.
                        if (data.warning) showBanner('error', data.warning);
                    })
                    .fail(function(xhr) {
                        state.student = null;
                        state.courses = [];
                        state.existing = null;
                        showBanner('error', ajaxErrorMessage(xhr));
                    })
                    .always(function() {
                        setBusy(false);
                    });
            });

            // Course selected
            $('#ampl-course-select').on('change', function() {
                var scId = $(this).val();
                state.studentCourseId = scId;
                state.existing = null;
                state.canUndo = false;
                if (!scId) {
                    updateUrl(state.regNo, '');
                    render();
                    return;
                }
                var sc = state.courses.filter(function(c) {
                    return String(c.student_course_id) === String(scId);
                })[0];
                if (!sc) {
                    showBanner('error',
                        'Could not match that selection back to a course — the course list looks malformed.'
                        );
                    return;
                }
                updateUrl(state.regNo, scId);
                // open synchronously (user gesture) so the browser doesn't block it
                var popup = window.open('', 'assessmentMarksPopup', POPUP_FEATURES);
                resolveCourseSelection(scId, popup);
            });

            // Manual "Open Marks-Entry Window"
            $('#ampl-open-marks').on('click', function() {
                if (!state.studentCourseId) return;
                var popup = window.open(ROUTES.marksPopup + '/' + state.studentCourseId, 'assessmentMarksPopup',
                    POPUP_FEATURES);
                if (popup) {
                    popup.focus();
                    watchPopupAndRefreshOnClose(popup);
                }
            });

            // Undo
            $('#ampl-undo-btn').on('click', function() {
                if (!state.existing) return;
                if (!window.confirm(
                    'Undo this assessment? This cannot be redone once the 2-day window passes.'))
                    return;
                var popup = window.open('', 'assessmentMarksPopup', POPUP_FEATURES);
                setBusy(true);
                hideBanner();
                api(ROUTES.assessmentBase + '/' + state.existing.id, 'DELETE')
                    .done(function() {
                        showBanner('success', 'Assessment undone. Opening a fresh marks-entry window…');
                        state.existing = null;
                        state.canUndo = false;
                        if (popup && state.studentCourseId) {
                            popup.location = ROUTES.marksPopup + '/' + state.studentCourseId;
                            popup.focus();
                            watchPopupAndRefreshOnClose(popup);
                        } else if (popup) {
                            popup.close();
                        }
                    })
                    .fail(function(xhr) {
                        if (popup && !popup.closed) popup.close();
                        showBanner('error', ajaxErrorMessage(xhr));
                    })
                    .always(function() {
                        setBusy(false);
                    });
            });

            // Popup tells us it saved
            window.addEventListener('message', function(e) {
                if (e.origin !== window.location.origin) return;
                if (!e.data || e.data.type !== 'assessment-saved') return;
                if (String(e.data.studentCourseId) === String(state.studentCourseId)) {
                    resolveCourseSelection(state.studentCourseId, null);
                }
            });

            // Restore last search from URL
            (function restore() {
                var params = new URLSearchParams(window.location.search);
                var savedReg = params.get('admission_number');
                var savedSc = params.get('student_course_id');
                if (!savedReg) return;
                $('#ampl-restoring').show();
                setBusy(true);
                findStudent(savedReg)
                    .done(function(data) {
                        state.regNo = savedReg;
                        state.student = data.student;
                        state.courses = data.courses || [];
                        $('#ampl-regno').val(savedReg);
                        var sc = savedSc ? state.courses.filter(function(c) {
                            return String(c.student_course_id) === String(savedSc);
                        })[0] : null;
                        if (sc) state.studentCourseId = savedSc;
                        fillCourses();
                        setBusy(false);
                        if (sc) resolveCourseSelection(savedSc, null);
                    })
                    .fail(function() {
                        updateUrl('', '');
                        setBusy(false);
                    })
                    .always(function() {
                        $('#ampl-restoring').hide();
                    });
            })();

            render();
        })
        ();
    </script>
@endPushOnce
