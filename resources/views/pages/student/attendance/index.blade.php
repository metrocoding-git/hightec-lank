@extends('layouts.app')
@section('title')
    Scan Attendance
@endsection

@section('mainContent')
    {{-- Styled inline (not via @push('css')) — this layout doesn't appear to
         output @stack('css') anywhere, so pushed styles were being silently
         dropped. A <style> tag works fine anywhere in the HTML, including
         here in the body, so this guarantees it actually renders. --}}
    <style>
        /* ---- Method selector: icon cards ---- */
        .method-cards {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .method-card {
            flex: 1 1 220px;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            border: 2px solid #e9ecf2;
            border-radius: 12px;
            cursor: pointer;
            background: #fff;
            transition: border-color .15s ease, background-color .15s ease, transform .1s ease;
        }

        .method-card:hover {
            border-color: #c9d0e0;
            transform: translateY(-1px);
        }

        .method-card.active {
            border-color: var(--tp-theme-primary, #5150e9);
            background: #f5f5ff;
        }

        .method-card .method-icon {
            flex: 0 0 auto;
            width: 46px;
            height: 46px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eef0fb;
            color: var(--tp-theme-primary, #5150e9);
            font-size: 20px;
        }

        .method-card.active .method-icon {
            background: var(--tp-theme-primary, #5150e9);
            color: #fff;
        }

        .method-card .method-title {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .method-card .method-desc {
            font-size: 12px;
            color: #8a8fa3;
            line-height: 1.3;
        }

        .section-panel {
            padding-top: 18px;
        }

        /* ---- Scan box ---- */
        .scan-input-wrap {
            position: relative;
        }

        .scan-input-wrap .ti-search {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #aeb2c3;
            font-size: 18px;
            pointer-events: none;
        }

        .scanner-input {
            font-size: 1.15rem;
            letter-spacing: .3px;
            padding-left: 44px !important;
            height: 52px;
        }

        .scanner-input:focus {
            border-color: var(--tp-theme-primary, #5150e9);
            box-shadow: 0 0 0 3px rgba(81, 80, 233, .12);
        }

        #qr-reader {
            width: 100%;
            max-width: 420px;
            margin: 14px auto 0;
            border-radius: 10px;
            overflow: hidden;
        }

        #camera_toggle_btn {
            margin-top: 10px;
        }

        /* ---- Subject search + checklist ---- */
        .subject-search {
            margin-bottom: 8px;
        }

        .subject-check-list {
            max-height: 240px;
            overflow-y: auto;
            border: 1px solid #e9ecf2;
            border-radius: 10px;
            padding: 6px;
            background: #fafbfd;
        }

        .subject-check-list .form-check {
            position: relative;
            padding: 10px 14px;
            border-radius: 8px;
            margin: 2px 0;
            cursor: pointer;
        }

        .subject-check-list .form-check:hover {
            background: #fff;
        }

        /* Checkbox/radio box itself is hidden — the whole row highlight is
           the selection indicator (still fully functional: label clicks and
           our row click-handler both toggle the underlying input). */
        .subject-check-list .form-check-input {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            margin: 0;
        }

        .subject-check-list .form-check-label {
            cursor: pointer;
            display: block;
            margin-bottom: 0;
        }

        .subject-check-list .form-check.row-checked {
            background: #eef0fb;
            box-shadow: inset 3px 0 0 var(--tp-theme-primary, #5150e9);
        }

        .subject-type-badge {
            font-size: 10px;
            margin-left: 6px;
            vertical-align: 1px;
        }

        /* ---- Fee balance alert: deliberately a stronger/different color
           (red) than the generic feedback alerts, so an outstanding balance
           stands out at a glance. ---- */
        .balance-alert-box {
            border-left: 4px solid #dc3545;
        }

        /* ---- Scan log ---- */
        #scan_log tbody tr {
            transition: background-color .3s ease;
        }

        #scan_log tbody tr.action-in td {
            background-color: #eafaf0;
        }

        #scan_log tbody tr.action-out td {
            background-color: #fff6e8;
        }

        #scan_log tbody tr.action-error td {
            background-color: #fdecec;
        }

        #scan_log tbody tr.action-already td {
            background-color: #f2f3f6;
        }

        #scan_log tbody tr.action-undone td {
            background-color: #f2f3f6;
            text-decoration: line-through;
            opacity: .55;
        }

        #scan_log tbody tr.row-flash td {
            background-color: #fff3b0 !important;
        }

        .status-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-pill.pill-in {
            background: #d3f5df;
            color: #1a7f45;
        }

        .status-pill.pill-out {
            background: #ffe6bf;
            color: #a25a00;
        }

        .status-pill.pill-already {
            background: #e4e6ec;
            color: #5a5f70;
        }

        .status-pill.pill-error {
            background: #fbd6d6;
            color: #a3272c;
        }

        /* ---- Repeat-scan badge (Today's Scans): front-end only marker for
           when the same subject/practical has already been marked earlier
           today for the same student + course, so staff can spot "this is
           a repeat" at a glance. ---- */
        .repeat-badge {
            font-size: 10px;
            margin-left: 6px;
            vertical-align: 1px;
        }

        #scan_log tbody tr.row-repeat td:first-child {
            box-shadow: inset 3px 0 0 #e0a800;
        }

        .subject-check-list .form-check.subject-already-done {
            /* background: #fff8e6; */
            box-shadow: inset 3px 0 0 #e0a800;
        }

        .subject-check-list .form-check.subject-done-before {
            outline: 1px dashed #8a8fa3;
            outline-offset: -1px;
        }

        #scan_log_empty {
            text-align: center;
            color: #9aa0b4;
            padding: 24px 0;
        }

        .btn-undo {
            border-radius: 20px;
        }

        /* ---- Buttons: loading state ---- */
        .btn-loading {
            position: relative;
            pointer-events: none;
            opacity: .75;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 14px;
            height: 14px;
            margin: -7px 0 0 -7px;
            border: 2px solid rgba(255, 255, 255, .5);
            border-top-color: #fff;
            border-radius: 50%;
            animation: btn-spin .6s linear infinite;
        }

        @keyframes btn-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .intake-chip {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 12px;
            border-radius: 20px;
            background: #eef0fb;
            color: var(--tp-theme-primary, #5150e9);
            font-size: 12px;
            font-weight: 600;
        }
    </style>

    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Scan Attendance</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Student Information</a>
                    <a href="#">Scan Attendance</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            {{-- Method selector --}}
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title mb-15">
                            <h3 class="mb-0">Attendance Method</h3>
                        </div>

                        <div class="method-cards mb-10">
                            <div class="method-card active" data-section="course">
                                <div class="method-icon"><span class="ti-book"></span></div>
                                <div>
                                    <div class="method-title">Course</div>
                                    <div class="method-desc">Pick a course, then scan — intake is detected from the student.
                                    </div>
                                </div>
                            </div>
                            <div class="method-card" data-section="subject">
                                <div class="method-icon"><span class="ti-agenda"></span></div>
                                <div>
                                    <div class="method-title">Subject</div>
                                    <div class="method-desc">Pick one or more theory subjects, then scan any student.</div>
                                </div>
                            </div>
                            <div class="method-card" data-section="practical">
                                <div class="method-icon"><span class="ti-user"></span></div>
                                <div>
                                    <div class="method-title">Student (Practical)</div>
                                    <div class="method-desc">Scan the student first, then pick one practical subject.</div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 1: course only --}}
                        <div id="section_course" class="section-panel row">
                            <div class="col-lg-5 mb-15">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="course_id">Course <span
                                            class="text-danger">*</span></label>
                                    <select class="primary_select form-control" id="course_id">
                                        <option value="">Select Course</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-7 mb-15 d-flex align-items-center">
                                <small class="text-muted">The intake is detected automatically from the scanned student's
                                    own enrolment — you'll see it appear next to their name once scanned. Ending an open
                                    course session requires that any practical attendance today has finished, plus a short
                                    buffer afterwards.</small>
                            </div>
                        </div>

                        {{-- Section 2: any number of theory subjects, no course/intake picked up front --}}
                        <div id="section_subject" class="section-panel row" style="display:none;">
                            <div class="col-lg-12 mb-15">
                                <label class="primary_input_label">Subjects <span class="text-danger">*</span> (select one
                                    or more)</label>
                                <input type="text" id="subject_search"
                                    class="primary_input_field form-control subject-search"
                                    placeholder="Search subjects...">
                                <div class="subject-check-list" id="subject_all_list">
                                    @foreach ($subjects as $subject)
                                        <div class="form-check" data-subject-name="{{ strtolower($subject->name) }}">
                                            <input class="form-check-input subject-all-check" type="checkbox"
                                                value="{{ $subject->id }}" id="allsubj_{{ $subject->id }}">
                                            <label class="form-check-label" for="allsubj_{{ $subject->id }}">
                                                {{ $subject->name }}
                                                @if ($subject->type)
                                                    <span
                                                        class="badge badge-secondary subject-type-badge">{{ $subject->type }}</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">Checked against each student's own active course(s) — if a subject
                                    belongs to more than one active course, both get marked.</small>
                            </div>
                        </div>

                        {{-- Section 3: scan the student first --}}
                        <div id="section_practical" class="section-panel" style="display:none;">
                            <div class="alert alert-info mb-0 d-flex align-items-center">
                                <span class="ti-info-alt pr-2"></span>
                                Scan or enter the student's register number below. Only their active
                                <strong>practical</strong>
                                subjects will be offered — but only once that student's course attendance has been started
                                today (and hasn't ended yet). Pick exactly one subject, then optionally a vehicle and note.
                                Practical attendance can be repeated more than once a day.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scan box (shared by all three methods) --}}
            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title mb-15">
                            <h3>Scan / Enter Student</h3>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-lg-9 mb-15">
                                <div class="scan-input-wrap">
                                    <span class="ti-search"></span>
                                    <input type="text" id="scan_input"
                                        class="primary_input_field form-control scanner-input"
                                        placeholder="Scan barcode, or type register no. and press Enter" autocomplete="off"
                                        autofocus>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-15">
                                <button type="button" id="scan_submit_btn"
                                    class="primary-btn fix-gr-bg w-100">Submit</button>
                            </div>
                        </div>

                        {{-- Camera QR only opens once "Scan with Camera" is clicked — auto-fill of the
                             decoded value into #scan_input and auto-submit still happen on decode. --}}
                        <button type="button" id="camera_toggle_btn" class="btn btn-outline-secondary btn-sm">
                            <span class="ti-camera pr-1"></span> Scan with Camera
                        </button>
                        <div id="qr_camera_status" class="text-muted mb-10" style="font-size:12px;"></div>
                        <div id="qr-reader" style="display:none;"></div>
                        <div id="scan_feedback"></div>
                        <div id="balance_alert_box"></div>
                    </div>
                </div>
            </div>

            {{-- Section 3 step 2: pick the one practical subject, optional vehicle + note --}}
            <div class="row mt-20" id="practical_confirm_box" style="display:none;">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title mb-15">
                            <h3>Pick Subject for <span id="practical_student_name"></span></h3>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 mb-15">
                                <div id="practical_subject_list" class="subject-check-list"></div>
                            </div>
                            <div class="col-lg-4 mb-15">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="practical_vehicle_id">Vehicle
                                        (optional)</label>
                                    <select class="primary_select form-control" id="practical_vehicle_id">
                                        <option value="">No vehicle</option>
                                    </select>
                                    <small class="text-muted">Only vehicles available at this student's branch are
                                        listed.</small>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-15">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="practical_note">Note (optional)</label>
                                    <input type="text" id="practical_note" class="primary_input_field form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button type="button" id="practical_submit_btn" class="primary-btn fix-gr-bg">Mark
                                    Attendance</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Live scan log --}}
            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title mb-15">
                            <h3>Today's Scans</h3>
                        </div>
                        <small class="text-muted">Shared across everyone signed in — reflects all scans made today, by
                            anyone.</small>
                        <div class="table-responsive mt-10">
                            <table class="table school-table-style" id="scan_log">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Reg No.</th>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Subject</th>
                                        <th>Action</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="scan_log_empty">No scans yet today.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    {{-- Camera QR reading. Swap for any other QR lib you already use in the project. --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

    <script>
        $(function() {
            const courseScanUrl = "{{ route('scan-attendance.course.scan') }}";
            const subjectScanUrl = "{{ route('scan-attendance.subject.scan') }}";
            const practicalResolveUrl = "{{ route('scan-attendance.practical.resolve') }}";
            const practicalScanUrl = "{{ route('scan-attendance.practical.scan') }}";
            const undoUrl = "{{ route('scan-attendance.undo') }}";
            const todayLogUrl = "{{ route('scan-attendance.today-log') }}";
            const csrfToken = "{{ csrf_token() }}";
            const STORAGE_KEY = 'scan_attendance_active_section';

            let activeSection = 'course'; // 'course' | 'subject' | 'practical'
            let practicalIdentifier = null; // identifier awaiting subject pick (section 3)

            // Subject id -> name, used to label the scan log. Pre-seeded from the
            // section 2 checklist rendered server-side; section 3 adds to it as
            // practical subjects are resolved per student.
            const subjectNameById = {};
            @foreach ($subjects as $subject)
                subjectNameById[{{ $subject->id }}] = @json($subject->name);
            @endforeach

            // Course id -> name, used to label the scan log's Course column.
            const courseNameById = {};
            @foreach ($courses as $course)
                courseNameById[{{ $course->id }}] = @json($course->name);
            @endforeach

            function subjectLabel(subjectId) {
                if (!subjectId) return 'Course-wise';
                return subjectNameById[subjectId] || ('#' + subjectId);
            }

            function courseLabel(courseId) {
                if (!courseId) return '-';
                return courseNameById[courseId] || ('#' + courseId);
            }

            // ---- Section 2 checklist: full-row highlight when checked, whole
            // row clickable, native checkbox box hidden (see CSS). ----
            $('#subject_all_list').on('change', '.subject-all-check', function() {
                $(this).closest('.form-check').toggleClass('row-checked', $(this).is(':checked'));
            });
            $('#subject_all_list').on('click', '.form-check', function(e) {
                if ($(e.target).is('input, label')) return;
                $(this).find('.subject-all-check').prop('checked', function(i, checked) {
                    return !checked;
                }).trigger('change');
            });

            // ---- Section 3 practical subject picker: same full-row highlight
            // treatment as the Section 2 checklist, applied to the radio list. ----
            $('#practical_subject_list').on('change', 'input[type="radio"]', function() {
                $('#practical_subject_list .form-check').removeClass('row-checked');
                $(this).closest('.form-check').addClass('row-checked');
            });
            $('#practical_subject_list').on('click', '.form-check', function(e) {
                if ($(e.target).is('input, label')) return;
                $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
            });

            // ---- Method selector: click + remember last-used section ----
            function activateSection(section) {
                activeSection = section;
                $('.method-card').removeClass('active');
                $('.method-card[data-section="' + section + '"]').addClass('active');

                $('.section-panel').hide();
                $('#section_' + section).show();
                $('#practical_confirm_box').hide();
                practicalIdentifier = null;

                $('#scan_input').attr(
                    'placeholder',
                    section === 'practical' ?
                    "Scan barcode / QR, or type register no. — subject is picked after" :
                    "Scan barcode / QR, or type register no. and press Enter"
                ).focus();

                try {
                    localStorage.setItem(STORAGE_KEY, section);
                } catch (e) {}
            }

            $('.method-card').on('click', function() {
                activateSection($(this).data('section'));
            });

            // Restore the last-used section on page load; default to "course" if
            // nothing's cached yet.
            let savedSection = null;
            try {
                savedSection = localStorage.getItem(STORAGE_KEY);
            } catch (e) {}
            activateSection((savedSection === 'course' || savedSection === 'subject' || savedSection ===
                'practical') ? savedSection : 'course');

            // ---- Section 2: live subject search filter ----
            $('#subject_search').on('input', function() {
                const term = $(this).val().trim().toLowerCase();
                $('#subject_all_list .form-check').each(function() {
                    const matches = !term || $(this).data('subject-name').indexOf(term) !== -1;
                    $(this).toggle(matches);
                });
            });

            // ---- Scan submit (shared) ----
            $('#scan_submit_btn').on('click', submitScan);
            $('#scan_input').on('keypress', function(e) {
                if (e.which === 13) { // Enter — barcode scanners send this after the code
                    e.preventDefault();
                    submitScan();
                }
            });

            function setButtonLoading($btn, loading) {
                $btn.toggleClass('btn-loading', loading).prop('disabled', loading);
            }

            function submitScan() {
                const identifier = $('#scan_input').val().trim();
                if (!identifier) return;
                $('#scan_input').val('');
                clearBalanceAlert();

                const $btn = $('#scan_submit_btn');
                setButtonLoading($btn, true);
                const done = () => setButtonLoading($btn, false);

                if (activeSection === 'course') {
                    const courseId = $('#course_id').val();
                    if (!courseId) {
                        showFeedback('danger', 'Select a course before scanning.');
                        done();
                        return;
                    }
                    ajaxScan(courseScanUrl, {
                        identifier,
                        course_id: courseId
                    }, identifier, done);
                    return;
                }

                if (activeSection === 'subject') {
                    const subjectIds = $('.subject-all-check:checked').map(function() {
                        return $(this).val();
                    }).get();
                    if (!subjectIds.length) {
                        showFeedback('danger', 'Select at least one subject before scanning.');
                        done();
                        return;
                    }
                    ajaxScan(subjectScanUrl, {
                        identifier,
                        subject_ids: subjectIds
                    }, identifier, done);
                    return;
                }

                // practical: resolve first, don't mark yet.
                practicalIdentifier = identifier;
                $.ajax({
                    url: practicalResolveUrl,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        identifier
                    },
                    success: showPracticalSubjectPicker,
                    error: function(xhr) {
                        handleErrorResponse(xhr.responseJSON || {}, identifier);
                    },
                    complete: done
                });
            }

            function ajaxScan(url, data, identifier, done) {
                $.ajax({
                    url,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data,
                    success: function(res) {
                        handleOkResponse(res);
                    },
                    error: function(xhr) {
                        handleErrorResponse(xhr.responseJSON || {}, identifier);
                    },
                    complete: done
                });
            }

            function handleErrorResponse(res, identifier) {
                const statusLabels = {
                    not_found: 'Not found',
                    no_enrolment: 'No active enrolment',
                    not_enrolled: "Hasn't started this course",
                    no_practical_subjects: 'No practical subjects assigned',
                    not_assigned: 'Subject not assigned',
                    course_not_open: 'Course attendance not open',
                    practical_not_started: 'Course attendance not started',
                    before_checking_wait: 'Please wait a moment',
                    after_checking_wait: 'Please wait a moment',
                    after_checking_open: 'Close practical session first',
                    no_payment: 'No payment for this course',
                    practical_already_open: 'Another practical subject is open',
                    vehicle_required: 'Vehicle required to start',
                    vehicle_mismatch: 'Vehicle does not match',
                };
                const label = statusLabels[res.status] || 'Error';
                const name = res.student ? res.student.name : identifier;
                const regNo = res.student ? (res.student.admission_number || '-') : identifier;
                addLogRow(new Date(), name, regNo, '-', '-', '-', label, 'action-error', 'pill-error', null, false);
                showFeedback('danger', res.message || 'Something went wrong.');
            }

            function handleOkResponse(res) {
                (res.results || []).forEach(r => {
                    let cls = 'action-already',
                        pill = 'pill-already',
                        label = 'Already completed today';
                    if (r.action === 'in') {
                        cls = 'action-in';
                        pill = 'pill-in';
                        label = 'Time In';
                    } else if (r.action === 'out') {
                        cls = 'action-out';
                        pill = 'pill-out';
                        label = 'Time Out';
                    } else if (r.action === 'not_assigned') {
                        cls = 'action-error';
                        pill = 'pill-error';
                        label = 'Subject not assigned';
                    }

                    const undoable = (r.action === 'in' || r.action === 'out') && !!r.session_id;
                    addLogRow(r.time || new Date(), res.student.name, res.student.admission_number,
                        courseLabel(r.course_id), subjectLabel(r.subject_id), label, 'OK', cls, pill,
                        r.session_id || null, undoable);
                });

                // "Before checking" (course just opened) / "after checking" (a
                // practical session just closed) — real attendance events, not
                // just silent timers. Rendered the same way todayLog() shows
                // them on page load.
                if (res.before_checking) {
                    addLogRow(res.before_checking.starts_at, res.student.name, res.student
                        .admission_number, res.before_checking.course || '-', 'Before Checking',
                        'By ' + (res.before_checking.by || '-'), 'Active', 'action-already',
                        'pill-already', null, false);
                }
                (res.after_checking || []).forEach(evt => {
                    addLogRow(evt.starts_at, res.student.name, res.student.admission_number, evt
                        .course || '-', 'After Checking', 'By ' + (evt.by || '-'), 'Active',
                        'action-already', 'pill-already', null, false);
                });

                const intakeNote = res.intake_name ? ` (Intake: ${res.intake_name})` : '';
                showFeedback('success', `${res.student.name}: processed.${intakeNote}`);

                // Fee balance alert — advisory only, never blocks the scan.
                showBalanceAlerts(res.balance_alert ? [res.balance_alert] : Object.values(res
                    .balance_alerts || {}));

                $('#scan_input').focus();
            }

            let balanceAlertTimer = null;

            function clearBalanceAlert() {
                clearTimeout(balanceAlertTimer);
                $('#balance_alert_box').empty();
            }

            function showBalanceAlerts(alerts) {
                if (!alerts.length) {
                    return;
                }

                const money = n => Number(n).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                const lines = alerts.map(a =>
                    `Outstanding balance: ${money(a.balance)} (course price ${money(a.course_price)}, paid ${money(a.paid_amount)})`
                );

                clearTimeout(balanceAlertTimer);
                $('#balance_alert_box').html(
                    `<div class="alert alert-danger balance-alert-box mt-10 alert-dismissible fade show" role="alert">
                ${lines.join('<br>')}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="$(this).closest('.alert').remove();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>`
                );
                balanceAlertTimer = setTimeout(() => $('#balance_alert_box').empty(), 60000);
            }

            // ---- Section 3 step 2: pick the practical subject ----
            function showPracticalSubjectPicker(res) {
                $('#practical_student_name').text(res.student.name);

                let html = '';
                (res.subjects || []).forEach((s, i) => {
                    subjectNameById[s.id] = s.name;

                    const doneToday = s.done_today || 0;
                    const doneBefore = Math.max((s.done_count || 0) - doneToday, 0);
                    const isOpen = !!s.is_open;

                    let stateCls = '';
                    let badgeHtml = '';
                    if (isOpen) {
                        stateCls = ' subject-open';
                        badgeHtml =
                            ` <span class="badge badge-info repeat-badge" title="Started, not yet closed — picking this will CLOSE it, not start a new one">In Progress</span>`;
                    } else if (doneToday > 0) {
                        stateCls = ' subject-already-done';
                        badgeHtml =
                            ` <span class="badge badge-warning repeat-badge" title="Already marked ${doneToday} time(s) today for this course">Done ${doneToday}x today</span>`;
                    }
                    if (doneBefore > 0) {
                        stateCls += ' subject-done-before';
                        badgeHtml +=
                            ` <span class="badge badge-secondary repeat-badge" title="Marked ${doneBefore} time(s) on earlier day(s) for this course">Before ${doneBefore}x</span>`;
                    }

                    html += `<div class="form-check ${i === 0 ? 'row-checked' : ''}${stateCls}">
                <input class="form-check-input" type="radio" name="practical_subject" value="${s.id}" id="psubj_${s.id}" ${i === 0 ? 'checked' : ''}>
                <label class="form-check-label" for="psubj_${s.id}">${s.name}${badgeHtml}</label>
            </div>`;
                });
                $('#practical_subject_list').html(html);

                // Vehicle list is scoped server-side: prefers the logged-in
                // staff user's own branch, falling back to the student's
                // enrolment branch(es).
                let vehicleOptions = '<option value="">No vehicle</option>';
                (res.vehicles || []).forEach(v => {
                    const label = v.name + (v.vehicle_number ? (' - ' + v.vehicle_number) : '');
                    vehicleOptions += `<option value="${v.id}">${label}</option>`;
                });
                $('#practical_vehicle_id').html(vehicleOptions).val('');

                if (typeof $.fn.niceSelect === 'function') {
                    if ($('#practical_vehicle_id').next('.nice-select').length) {
                        $('#practical_vehicle_id').niceSelect('destroy');
                    }
                    $('#practical_vehicle_id').niceSelect();
                }

                $('#practical_note').val('');
                $('#practical_confirm_box').show();
            }

            $('#practical_submit_btn').on('click', function() {
                const subjectId = $('input[name="practical_subject"]:checked').val();
                if (!subjectId || !practicalIdentifier) return;

                const $btn = $(this);
                setButtonLoading($btn, true);

                $.ajax({
                    url: practicalScanUrl,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        identifier: practicalIdentifier,
                        subject_id: subjectId,
                        vehicle_id: $('#practical_vehicle_id').val() || null,
                        note: $('#practical_note').val() || null,
                    },
                    success: function(res) {
                        handleOkResponse(res);
                        $('#practical_confirm_box').hide();
                        practicalIdentifier = null;
                    },
                    error: function(xhr) {
                        handleErrorResponse(xhr.responseJSON || {}, practicalIdentifier);
                    },
                    complete: function() {
                        setButtonLoading($btn, false);
                    }
                });
            });

            // ---- Camera QR — only starts once the "Scan with Camera" button is
            // clicked (not automatically on page load). Auto-fill of the decoded
            // value into #scan_input and auto-submit still happen on every decode.
            let qrScanner = null;
            let qrScannerRunning = false;
            let qrScanCooldown = false; // brief pause after a decode so one QR code isn't submitted twice in a row

            function startCamera() {
                if (qrScannerRunning) return;

                qrScanCooldown = false;
                $('#qr-reader').show();
                qrScanner = new Html5Qrcode('qr-reader');
                qrScanner.start({
                        facingMode: 'environment'
                    }, {
                        fps: 10,
                        qrbox: 220
                    },
                    (decodedText) => {
                        if (qrScanCooldown) return;
                        qrScanCooldown = true;
                        $('#scan_input').val(decodedText);
                        submitScan();
                        // Auto-close the camera after every successful scan —
                        // scan one code, camera shuts off; click "Scan with
                        // Camera" again to scan another.
                        stopCamera('Scan captured — click "Scan with Camera" to scan again.');
                    },
                    () => {}
                ).then(() => {
                    qrScannerRunning = true;
                    $('#qr_camera_status').text('Camera QR is on — hold a code up to scan.');
                    $('#camera_toggle_btn').html('<span class="ti-close pr-1"></span> Stop Camera');
                }).catch(err => {
                    $('#qr_camera_status').text('Camera unavailable — manual entry still works.');
                });
            }

            function stopCamera(statusMessage) {
                if (!qrScannerRunning || !qrScanner) return;

                qrScanner.stop().then(() => {
                    qrScanner.clear();
                }).finally(() => {
                    qrScannerRunning = false;
                    $('#qr-reader').hide();
                    $('#qr_camera_status').text(statusMessage || '');
                    $('#camera_toggle_btn').html('<span class="ti-camera pr-1"></span> Scan with Camera');
                });
            }

            $('#camera_toggle_btn').on('click', function() {
                qrScannerRunning ? stopCamera() : startCamera();
            });

            // ---- Scan log + undo ----
            let rowSeq = 0;

            const subjectOccurrenceCounts = {};

            function trackSubjectOccurrence(regNo, course, subject, action) {
                if (action !== 'Time In') return null;
                if (!subject || subject === 'Course-wise' || subject === 'Before Checking' || subject ===
                    'After Checking') {
                    return null;
                }

                const key = (regNo || '-') + '||' + (course || '-') + '||' + subject;
                const count = (subjectOccurrenceCounts[key] || 0) + 1;
                subjectOccurrenceCounts[key] = count;
                return count > 1 ? count : null;
            }

            function addLogRow(time, student, regNo, course, subject, action, status, cls, pill,
                sessionId, undoable) {
                $('#scan_log_empty').hide();

                const t = (time instanceof Date) ? time.toLocaleTimeString() : time;
                const rowId = 'scan_row_' + (++rowSeq);
                const undoCell = (sessionId && undoable) ?
                    `<button type="button" class="btn btn-sm btn-outline-danger btn-undo" data-session-id="${sessionId}" data-row-id="${rowId}">Undo</button>` :
                    '';
                const statusHtml = `<span class="status-pill ${pill}">${status}</span>`;

                const repeatCount = trackSubjectOccurrence(regNo, course, subject, action);
                const ordinal = repeatCount === 2 ? '2nd' : (repeatCount ? repeatCount + 'th' : '');
                const subjectHtml = repeatCount ?
                    `${subject} <span class="badge badge-warning repeat-badge" title="Marked ${repeatCount} times today for this subject/course">${ordinal} time</span>` :
                    subject;
                const rowCls = (cls || '') + (repeatCount ? ' row-repeat' : '');

                const row =
                    `<tr id="${rowId}" class="${rowCls}"><td>${t}</td><td>${regNo || '-'}</td><td>${student}</td><td>${course || '-'}</td><td>${subjectHtml}</td><td>${action}</td><td>${statusHtml}</td><td>${undoCell}</td></tr>`;
                $('#scan_log tbody').prepend(row);

                // Brief flash so the newest row is easy to spot.
                const $row = $('#' + rowId);
                $row.addClass('row-flash');
                setTimeout(() => $row.removeClass('row-flash'), 700);
            }

            // Undo is same-day, same-user only, and (for course/practical sessions)
            // strict LIFO relative to each other — all enforced server-side; this
            // button just calls the endpoint and reflects the result.
            $('#scan_log').on('click', '.btn-undo', function() {
                const $btn = $(this);
                const sessionId = $btn.data('session-id');
                const rowId = $btn.data('row-id');

                setButtonLoading($btn, true);

                $.ajax({
                    url: undoUrl,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        session_id: sessionId
                    },
                    success: function(res) {
                        $('#' + rowId).removeClass(
                            'action-in action-out action-already action-error').addClass(
                            'action-undone');
                        $btn.remove();
                        showFeedback('success', res.message || 'Undone.');
                    },
                    error: function(xhr) {
                        const res = xhr.responseJSON || {};
                        showFeedback('danger', res.message || 'Could not undo this scan.');
                        setButtonLoading($btn, false);
                    }
                });
            });

            function showFeedback(type, message) {
                $('#scan_feedback').html(`<div class="alert alert-${type} mt-15">${message}</div>`);
                setTimeout(() => $('#scan_feedback').empty(), 4000);
            }

            // ---- Today's Scans — loaded from the server on page load so it
            $.ajax({
                url: todayLogUrl,
                method: 'GET',
                success: function(res) {
                    (res.rows || []).forEach(row => {
                        let cls = row.cls,
                            pill = row.pill;
                        addLogRow(row.time, row.student, row.reg_no, row.course, row.subject, row
                            .action, row.status, cls, pill, row.session_id, row.undoable);
                    });
                }
            });
        });
    </script>
@endpush
