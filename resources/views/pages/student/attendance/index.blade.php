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
        .method-cards { display: flex; gap: 16px; flex-wrap: wrap; }
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
        .method-card:hover { border-color: #c9d0e0; transform: translateY(-1px); }
        .method-card.active { border-color: var(--tp-theme-primary, #5150e9); background: #f5f5ff; }
        .method-card .method-icon {
            flex: 0 0 auto;
            width: 46px; height: 46px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            background: #eef0fb;
            color: var(--tp-theme-primary, #5150e9);
            font-size: 20px;
        }
        .method-card.active .method-icon { background: var(--tp-theme-primary, #5150e9); color: #fff; }
        .method-card .method-title { font-weight: 600; margin-bottom: 2px; }
        .method-card .method-desc { font-size: 12px; color: #8a8fa3; line-height: 1.3; }

        .section-panel { padding-top: 18px; }

        /* ---- Scan box ---- */
        .scan-input-wrap { position: relative; }
        .scan-input-wrap .ti-search {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: #aeb2c3; font-size: 18px; pointer-events: none;
        }
        .scanner-input { font-size: 1.15rem; letter-spacing: .3px; padding-left: 44px !important; height: 52px; }
        .scanner-input:focus { border-color: var(--tp-theme-primary, #5150e9); box-shadow: 0 0 0 3px rgba(81,80,233,.12); }
        #qr-reader { width: 100%; max-width: 420px; margin: 14px auto 0; border-radius: 10px; overflow: hidden; }

        /* ---- Subject search + checklist ---- */
        .subject-search { margin-bottom: 8px; }
        .subject-check-list {
            max-height: 240px; overflow-y: auto;
            border: 1px solid #e9ecf2; border-radius: 10px; padding: 6px;
            background: #fafbfd;
        }
        .subject-check-list .form-check {
            padding: 8px 10px 8px 34px;
            border-radius: 8px;
            margin: 2px 0;
        }
        .subject-check-list .form-check:hover { background: #fff; }
        .subject-type-badge { font-size: 10px; margin-left: 6px; vertical-align: 1px; }

        /* ---- Scan log ---- */
        #scan_log tbody tr { transition: background-color .3s ease; }
        #scan_log tbody tr.action-in td { background-color: #eafaf0; }
        #scan_log tbody tr.action-out td { background-color: #fff6e8; }
        #scan_log tbody tr.action-error td { background-color: #fdecec; }
        #scan_log tbody tr.action-already td { background-color: #f2f3f6; }
        #scan_log tbody tr.action-undone td { background-color: #f2f3f6; text-decoration: line-through; opacity: .55; }
        #scan_log tbody tr.row-flash td { background-color: #fff3b0 !important; }
        .status-pill { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .status-pill.pill-in { background: #d3f5df; color: #1a7f45; }
        .status-pill.pill-out { background: #ffe6bf; color: #a25a00; }
        .status-pill.pill-already { background: #e4e6ec; color: #5a5f70; }
        .status-pill.pill-error { background: #fbd6d6; color: #a3272c; }
        #scan_log_empty { text-align: center; color: #9aa0b4; padding: 24px 0; }

        .btn-undo { border-radius: 20px; }

        /* ---- Buttons: loading state ---- */
        .btn-loading { position: relative; pointer-events: none; opacity: .75; }
        .btn-loading::after {
            content: ''; position: absolute; top: 50%; left: 50%;
            width: 14px; height: 14px; margin: -7px 0 0 -7px;
            border: 2px solid rgba(255,255,255,.5); border-top-color: #fff;
            border-radius: 50%; animation: btn-spin .6s linear infinite;
        }
        @keyframes btn-spin { to { transform: rotate(360deg); } }

        .intake-chip {
            display: inline-block; margin-top: 8px; padding: 4px 12px;
            border-radius: 20px; background: #eef0fb; color: var(--tp-theme-primary, #5150e9);
            font-size: 12px; font-weight: 600;
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
                        <div class="main-title mb-15"><h3 class="mb-0">Attendance Method</h3></div>

                        <div class="method-cards mb-10">
                            <div class="method-card active" data-section="course">
                                <div class="method-icon"><span class="ti-book"></span></div>
                                <div>
                                    <div class="method-title">Course</div>
                                    <div class="method-desc">Pick a course, then scan — intake is detected from the student.</div>
                                </div>
                            </div>
                            <div class="method-card" data-section="subject">
                                <div class="method-icon"><span class="ti-agenda"></span></div>
                                <div>
                                    <div class="method-title">Subject</div>
                                    <div class="method-desc">Pick one or more subjects, then scan any student.</div>
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
                            <div class="col-lg- mb-15">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="course_id">Course <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" id="course_id">
                                        <option value="">Select Course</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                        </div>

                        {{-- Section 2: any number of subjects, no course/intake picked up front --}}
                        <div id="section_subject" class="section-panel row" style="display:none;">
                            <div class="col-lg-12 mb-15">
                                <label class="primary_input_label">Subjects <span class="text-danger">*</span> (select one or more)</label>
                                <input type="text" id="subject_search" class="primary_input_field form-control subject-search" placeholder="Search subjects...">
                                <div class="subject-check-list" id="subject_all_list">
                                    @foreach ($subjects as $subject)
                                        <div class="form-check" data-subject-name="{{ strtolower($subject->name) }}">
                                            <input class="form-check-input subject-all-check" type="checkbox" value="{{ $subject->id }}" id="allsubj_{{ $subject->id }}">
                                            <label class="form-check-label" for="allsubj_{{ $subject->id }}">
                                                {{ $subject->name }}
                                                @if ($subject->type)
                                                    <span class="badge badge-secondary subject-type-badge">{{ $subject->type }}</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">Checked against each student's own active course(s) — if a subject belongs to more than one active course, both get marked.</small>
                            </div>
                        </div>

                        {{-- Section 3: scan the student first --}}
                        <div id="section_practical" class="section-panel" style="display:none;">
                            <div class="alert alert-info mb-0 d-flex align-items-center">
                                <span class="ti-info-alt pr-2"></span>
                                Scan or enter the student's register number below. Only their active <strong>practical</strong>
                                subjects will be offered — pick exactly one, then optionally a vehicle and note.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scan box (shared by all three methods) --}}
            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title mb-15"><h3>Scan / Enter Student</h3></div>
                        <div class="row align-items-center">
                            <div class="col-lg-9 mb-15">
                                <div class="scan-input-wrap">
                                    <span class="ti-search"></span>
                                    <input type="text" id="scan_input" class="primary_input_field form-control scanner-input"
                                           placeholder="Scan barcode, or type register no. and press Enter" autocomplete="off" autofocus>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-15">
                                <button type="button" id="scan_submit_btn" class="primary-btn fix-gr-bg w-100">Submit</button>
                            </div>
                        </div>

                        {{-- Camera QR is always on — no button needed, it starts as soon as the page loads. --}}
                        <div id="qr_camera_status" class="text-muted mb-10" style="font-size:12px;"></div>
                        <div id="qr-reader"></div>
                        <div id="scan_feedback"></div>
                    </div>
                </div>
            </div>

            {{-- Section 3 step 2: pick the one practical subject, optional vehicle + note --}}
            <div class="row mt-20" id="practical_confirm_box" style="display:none;">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title mb-15"><h3>Pick Subject for <span id="practical_student_name"></span></h3></div>
                        <div class="row">
                            <div class="col-lg-4 mb-15">
                                <div id="practical_subject_list"></div>
                            </div>
                            <div class="col-lg-4 mb-15">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="practical_vehicle_id">Vehicle (optional)</label>
                                    <select class="primary_select form-control" id="practical_vehicle_id">
                                        <option value="">No vehicle</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->name }}{{ $vehicle->vehicle_number ? ' - ' . $vehicle->vehicle_number : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-15">
                                <div class="primary_input">
                                    <label class="primary_input_label" for="practical_note">Note (optional)</label>
                                    <input type="text" id="practical_note" class="primary_input_field form-control" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button type="button" id="practical_submit_btn" class="primary-btn fix-gr-bg">Mark Attendance</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Live scan log --}}
            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title mb-15"><h3>Today's Scans</h3></div>
                        <div class="table-responsive">
                            <table class="table school-table-style" id="scan_log">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Student</th>
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
$(function () {
    const courseScanUrl = "{{ route('scan-attendance.course.scan') }}";
    const subjectScanUrl = "{{ route('scan-attendance.subject.scan') }}";
    const practicalResolveUrl = "{{ route('scan-attendance.practical.resolve') }}";
    const practicalScanUrl = "{{ route('scan-attendance.practical.scan') }}";
    const undoUrl = "{{ route('scan-attendance.undo') }}";
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

    function subjectLabel(subjectId) {
        if (!subjectId) return 'Course-wise';
        return subjectNameById[subjectId] || ('#' + subjectId);
    }

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
            section === 'practical'
                ? "Scan barcode / QR, or type register no. — subject is picked after"
                : "Scan barcode / QR, or type register no. and press Enter"
        ).focus();

        try { localStorage.setItem(STORAGE_KEY, section); } catch (e) {}
    }

    $('.method-card').on('click', function () {
        activateSection($(this).data('section'));
    });

    // Restore the last-used section on page load; default to "course" if
    // nothing's cached yet.
    let savedSection = null;
    try { savedSection = localStorage.getItem(STORAGE_KEY); } catch (e) {}
    activateSection((savedSection === 'course' || savedSection === 'subject' || savedSection === 'practical') ? savedSection : 'course');

    // ---- Section 2: live subject search filter ----
    $('#subject_search').on('input', function () {
        const term = $(this).val().trim().toLowerCase();
        $('#subject_all_list .form-check').each(function () {
            const matches = !term || $(this).data('subject-name').indexOf(term) !== -1;
            $(this).toggle(matches);
        });
    });

    // ---- Scan submit (shared) ----
    $('#scan_submit_btn').on('click', submitScan);
    $('#scan_input').on('keypress', function (e) {
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
            ajaxScan(courseScanUrl, { identifier, course_id: courseId }, identifier, done);
            return;
        }

        if (activeSection === 'subject') {
            const subjectIds = $('.subject-all-check:checked').map(function () { return $(this).val(); }).get();
            if (!subjectIds.length) {
                showFeedback('danger', 'Select at least one subject before scanning.');
                done();
                return;
            }
            ajaxScan(subjectScanUrl, { identifier, subject_ids: subjectIds }, identifier, done);
            return;
        }

        // practical: resolve first, don't mark yet.
        practicalIdentifier = identifier;
        $.ajax({
            url: practicalResolveUrl,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: { identifier },
            success: showPracticalSubjectPicker,
            error: function (xhr) { handleErrorResponse(xhr.responseJSON || {}, identifier); },
            complete: done
        });
    }

    function ajaxScan(url, data, identifier, done) {
        $.ajax({
            url,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data,
            success: function (res) { handleOkResponse(res); },
            error: function (xhr) { handleErrorResponse(xhr.responseJSON || {}, identifier); },
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
        };
        const label = statusLabels[res.status] || 'Error';
        const name = res.student ? res.student.name : identifier;
        addLogRow(new Date(), name, '-', '-', label, 'action-error', 'pill-error', null);
        showFeedback('danger', res.message || 'Something went wrong.');
    }

    function handleOkResponse(res) {
        (res.results || []).forEach(r => {
            let cls = 'action-already', pill = 'pill-already', label = 'Already completed today';
            if (r.action === 'in') { cls = 'action-in'; pill = 'pill-in'; label = 'Time In'; }
            else if (r.action === 'out') { cls = 'action-out'; pill = 'pill-out'; label = 'Time Out'; }
            else if (r.action === 'not_assigned') { cls = 'action-error'; pill = 'pill-error'; label = 'Subject not assigned'; }

            addLogRow(r.time || new Date(), res.student.name, subjectLabel(r.subject_id), label, 'OK', cls, pill, r.session_id || null);
        });

        const intakeNote = res.intake_name ? ` (Intake: ${res.intake_name})` : '';
        showFeedback('success', `${res.student.name}: processed.${intakeNote}`);
        $('#scan_input').focus();
    }

    // ---- Section 3 step 2: pick the practical subject ----
    function showPracticalSubjectPicker(res) {
        $('#practical_student_name').text(res.student.name);

        let html = '';
        (res.subjects || []).forEach((s, i) => {
            subjectNameById[s.id] = s.name;
            html += `<div class="form-check">
                <input class="form-check-input" type="radio" name="practical_subject" value="${s.id}" id="psubj_${s.id}" ${i === 0 ? 'checked' : ''}>
                <label class="form-check-label" for="psubj_${s.id}">${s.name}</label>
            </div>`;
        });
        $('#practical_subject_list').html(html);
        $('#practical_vehicle_id').val('');
        $('#practical_note').val('');
        $('#practical_confirm_box').show();
    }

    $('#practical_submit_btn').on('click', function () {
        const subjectId = $('input[name="practical_subject"]:checked').val();
        if (!subjectId || !practicalIdentifier) return;

        const $btn = $(this);
        setButtonLoading($btn, true);

        $.ajax({
            url: practicalScanUrl,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: {
                identifier: practicalIdentifier,
                subject_id: subjectId,
                vehicle_id: $('#practical_vehicle_id').val() || null,
                note: $('#practical_note').val() || null,
            },
            success: function (res) {
                handleOkResponse(res);
                $('#practical_confirm_box').hide();
                practicalIdentifier = null;
            },
            error: function (xhr) { handleErrorResponse(xhr.responseJSON || {}, practicalIdentifier); },
            complete: function () { setButtonLoading($btn, false); }
        });
    });

    // ---- Camera QR — always on, no button. Starts as soon as the page
    // loads and keeps running between scans so it can pick up the next one
    // right away. Manual entry + Submit still works alongside it.
    let qrScanner = null;
    let qrScanCooldown = false; // brief pause after a decode so one QR code isn't submitted twice in a row

    function startCamera() {
        qrScanner = new Html5Qrcode('qr-reader');
        qrScanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: 220 },
            (decodedText) => {
                if (qrScanCooldown) return;
                qrScanCooldown = true;
                $('#scan_input').val(decodedText);
                submitScan();
                setTimeout(() => { qrScanCooldown = false; }, 2000);
            },
            () => {}
        ).then(() => {
            $('#qr_camera_status').text('Camera QR is on — hold a code up to scan.');
        }).catch(err => {
            $('#qr_camera_status').text('Camera unavailable — manual entry still works.');
        });
    }

    startCamera();

    // ---- Scan log + undo ----
    let rowSeq = 0;

    function addLogRow(time, student, subject, action, status, cls, pill, sessionId) {
        $('#scan_log_empty').hide();

        const t = (time instanceof Date) ? time.toLocaleTimeString() : time;
        const rowId = 'scan_row_' + (++rowSeq);
        const undoCell = sessionId
            ? `<button type="button" class="btn btn-sm btn-outline-danger btn-undo" data-session-id="${sessionId}" data-row-id="${rowId}">Undo</button>`
            : '';
        const statusHtml = `<span class="status-pill ${pill}">${status}</span>`;
        const row = `<tr id="${rowId}" class="${cls || ''}"><td>${t}</td><td>${student}</td><td>${subject}</td><td>${action}</td><td>${statusHtml}</td><td>${undoCell}</td></tr>`;
        $('#scan_log tbody').prepend(row);

        // Brief flash so the newest row is easy to spot.
        const $row = $('#' + rowId);
        $row.addClass('row-flash');
        setTimeout(() => $row.removeClass('row-flash'), 700);
    }

    // Undo is same-day, same-user only — enforced server-side; this button
    // just calls the endpoint and reflects the result.
    $('#scan_log').on('click', '.btn-undo', function () {
        const $btn = $(this);
        const sessionId = $btn.data('session-id');
        const rowId = $btn.data('row-id');

        setButtonLoading($btn, true);

        $.ajax({
            url: undoUrl,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: { session_id: sessionId },
            success: function (res) {
                $('#' + rowId).removeClass('action-in action-out action-already action-error').addClass('action-undone');
                $btn.remove();
                showFeedback('success', res.message || 'Undone.');
            },
            error: function (xhr) {
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
});
</script>
@endpush