@extends('layouts.app')
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
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div id="assessment-mark-root"></div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor mt-10">
        <div class="container-fluid p-0">
            <div class="white-box p-20 mb-20 mt-40">
                <form method="GET" action="{{ route('student-assessment.index') }}" class="row align-items-end mb-15">
                    <div class="col-lg-4 mb-2">
                        <select name="course_id" class="primary_select form-control ajax_select">
                            @foreach ($courseOptions ?? [] as $c)
                                <option value="{{ $c->id }}"
                                    {{ (string) request('course_id') === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 mb-2">
                        <label class="ampl-filter-label">Result</label>
                        <select name="result" class="primary_select form-control ajax_select">
                            <option value="">All Results</option>
                            <option value="PASS" {{ request('result') === 'PASS' ? 'selected' : '' }}>Pass</option>
                            <option value="FAIL" {{ request('result') === 'FAIL' ? 'selected' : '' }}>Fail</option>
                        </select>
                    </div>
                    <div class="col-lg-4 mb-2">
                        <select name="created_by" class="primary_select form-control ajax_selectl">
                            <option value="">Everyone</option>
                            @foreach ($creatorOptions ?? [] as $u)
                                <option value="{{ $u->id }}"
                                    {{ (string) request('created_by') === (string) $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-12 mb-2">
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
                                <button class="primary-btn small fix-gr-bg col-12 p-2">
                                    <a href="{{ route('student-assessment.index') }}" class="text-white">Reset</a>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="white-box">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-15">Final Assessment Records</h3>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <x-table>
                        <table id="table_id" class="table data-table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Reg No</th>
                                    <th>Course</th>
                                    <th>Module</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Result</th>
                                    <th>Grade</th>
                                    <th>Student Category</th>
                                    <th>Course Start Date</th>
                                    <th>Address</th>
                                    <th>Name Verified</th>
                                    <th>NIC / ID No. Verified</th>
                                    <th>Address Verified</th>
                                    <th>Created By</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($recent ?? collect()) as $i => $a)
                                    @php
                                        $assessedCourseId = optional($a->template->course)->id;
                                        $categoryKey = $a->student_id . '-' . $assessedCourseId;
                                        $studentCategory = optional($categoriesByPair->get($categoryKey))->category_name ?? '-';
                                    @endphp
                                    <tr>
                                        <td></td>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $a->student->student_name ?? '-' }}</td>
                                        <td>{{ $a->student->admission_number ?? '-' }}</td>
                                        <td>{{ $a->template->course->name ?? '-' }}</td>
                                        <td>{{ $a->template->module_name ?? '-' }}</td>
                                        <td>{{ optional($a->assessed_at)->format('Y-m-d') }}</td>
                                        <td>{{ $a->total_marks }} / {{ $a->max_marks }}</td>
                                        <td>
                                            @if ($a->is_qualified)
                                                <span class="badge badge-success">PASS</span>
                                            @else
                                                <span class="badge badge-danger">FAIL</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- grade now stores the letter band (A / B+ / B / C) or FAIL,
                                                 computed in StudentAssessmentController::percentageToGrade() --}}
                                            {{ $a->grade ?? '-' }}
                                        </td>
                                        <td>{{ $studentCategory }}</td>
                                        <td>
                                            {{-- TODO: wire this in once the real column is confirmed —
                                                 student_courses.start_date? intakes.start_date? something
                                                 else? Left as a placeholder rather than guessing a column
                                                 name that might not exist and 500ing the page. --}}
                                            -
                                        </td>
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
                                        <td>{{ $a->evaluator->name ?? '-' }}</td>
                                        <td>
                                            <a href="{{ url('/student/student-info/assessment/' . $a->id . '/pdf') }}"
                                                class="btn btn-sm btn-outline-primary" target="_blank">PDF</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="17" class="text-center">No assessments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </x-table>
                </div>
            </div>
        </div>
    </section>
@endsection
@include('components.partials.data_table_css')
@include('components.partials.data_table_js')
@push('script')
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap4.min.css">
    <style>
        .ampl-card {
            background: #fff;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 16px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        }
        .ampl-findform {
            display: flex;
            gap: 12px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .ampl-findform>div {
            flex: 1;
            min-width: 220px;
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
        label {
            font-weight: 600;
            font-size: 13px;
            color: #37474f;
            display: block;
            margin-bottom: 4px;
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
        .ampl-btn-primary:disabled {
            opacity: .6;
            cursor: default;
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
            /* display: flex; */
            gap: 24px;
            flex-wrap: wrap;
            font-size: 14px;
            color: #37474f;
        }
        .ampl-grid2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        @media (max-width: 767px) {
            .ampl-grid2 {
                grid-template-columns: 1fr;
            }
        }
        .ampl-muted {
            color: #8a95a1;
            font-size: 13px;
        }
        .ampl-tablewrap {
            overflow-x: auto;
        }
        .ampl-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .ampl-table th,
        .ampl-table td {
            border: 1px solid #e1e6eb;
            padding: 8px;
            vertical-align: middle;
        }
        .ampl-table th {
            background: #f4f6f8;
            text-align: left;
        }
        .ampl-titlerow td {
            background: #eef3f9;
        }
        .ampl-yieldcell {
            text-align: center;
            font-weight: 700;
            background: #f9fbfc;
        }
        .ampl-totalcell {
            text-align: right;
            font-weight: 700;
        }
        .ampl-pass {
            color: #1e7a43;
            font-weight: 700;
            margin-top: 4px;
        }
        .ampl-fail {
            color: #a12a2a;
            font-weight: 700;
            margin-top: 4px;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/18.3.1/umd/react.production.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/18.3.1/umd/react-dom.production.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/7.24.7/babel.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script>
        window.__CSRF_TOKEN__ = "{{ csrf_token() }}";
        window.__ROUTES__ = {
            find: "{{ route('student-assessment.find') }}",
            existing: "{{ url('/student/student-info/assessment/existing') }}",
            marksPopup: "{{ url('/student/student-info/assessment/marks-popup') }}",
            assessmentBase: "{{ url('/student/student-info/assessment') }}",
        };
        window.__PERMISSIONS__ = {
            canAdd: @json($canAddAssessment),
            canUndo: @json($canUndoAssessment),
        };
    </script>
    @verbatim
        <script type="text/babel" data-presets="react">
        async function apiFetch(url, options = {}) {
            const opts = Object.assign({}, options);
            opts.headers = Object.assign({
                'X-CSRF-TOKEN': window.__CSRF_TOKEN__,
                'Accept': 'application/json',
            }, options.headers || {});
            let res;
            try {
                res = await fetch(url, opts);
            } catch (err) {
                throw new Error('Network error — could not reach the server.');
            }
            let data = null;
            try {
                data = await res.json();
            } catch (err) {
                throw new Error(`Server returned a non-JSON response (HTTP ${res.status}). Check the route/URL.`);
            }
            if (!res.ok) {
                if (res.status === 422 && data.errors) {
                    const msgs = Object.values(data.errors).flat().join(' ');
                    throw new Error(msgs || data.message || 'Validation failed.');
                }
                throw new Error(data.message || `Request failed (HTTP ${res.status}).`);
            }
            return data;
        }
        const POPUP_FEATURES = 'width=1100,height=850,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes';
        function updateUrl(admissionNumber, studentCourseId) {
            const params = new URLSearchParams(window.location.search);
            if (admissionNumber) {
                params.set('admission_number', admissionNumber);
            } else {
                params.delete('admission_number');
            }
            if (studentCourseId) {
                params.set('student_course_id', studentCourseId);
            } else {
                params.delete('student_course_id');
            }
            const qs = params.toString();
            const newUrl = window.location.pathname + (qs ? `?${qs}` : '');
            window.history.replaceState(null, '', newUrl);
        }
        function App() {
            const [regNo, setRegNo] = React.useState('');
            const [busy, setBusy] = React.useState(false);
            const [restoring, setRestoring] = React.useState(true);
            const [banner, setBanner] = React.useState(null);
            const [student, setStudent] = React.useState(null);
            const [courses, setCourses] = React.useState([]);
            const [studentCourseId, setStudentCourseId] = React.useState('');
            const [courseId, setCourseId] = React.useState('');
            const [existingAssessment, setExistingAssessment] = React.useState(null);
            const [canUndo, setCanUndo] = React.useState(false);
            // Shared by: (a) an interactive course selection — where a blank
            // popup was already opened synchronously by the caller, before any
            // await, so browsers don't treat it as an unrequested popup — and
            // (b) restoring state from the URL on page load, where `popup` is
            // null and nothing is auto-opened (a page load isn't a genuine
            // user click, so a popup there would just get blocked anyway).
            async function resolveCourseSelection(scId, popup) {
                setBusy(true);
                setBanner(null);
                try {
                    const data = await apiFetch(`${window.__ROUTES__.existing}/${scId}`);
                    if (data.status === 'ok' && data.assessment) {
                        setExistingAssessment(data.assessment);
                        setCanUndo(!!data.can_undo);
                        if (popup && !popup.closed) popup.close();
                    } else {
                        setExistingAssessment(null);
                        setCanUndo(false);
                        if (popup) {
                            popup.location = `${window.__ROUTES__.marksPopup}/${scId}`;
                            popup.focus();
                        }
                    }
                } catch (err) {
                    if (popup && !popup.closed) popup.close();
                    setBanner({ type: 'error', text: err.message });
                } finally {
                    setBusy(false);
                }
            }
            // Restore student + course from the URL on page load/refresh, so
            // the "already has a saved assessment — View / Undo" card (and any
            // in-progress course selection) survives a reload instead of
            // disappearing every time the page reloads.
            React.useEffect(() => {
                function handleMessage(e) {
                    if (e.origin !== window.location.origin) return;
                    if (!e.data || e.data.type !== 'assessment-saved') return;
                    if (String(e.data.studentCourseId) === String(studentCourseId)) {
                        resolveCourseSelection(studentCourseId, null);
                    }
                }
                window.addEventListener('message', handleMessage);
                return () => window.removeEventListener('message', handleMessage);
            }, [studentCourseId]);
            React.useEffect(() => {
                const params = new URLSearchParams(window.location.search);
                const savedAdmissionNumber = params.get('admission_number');
                const savedStudentCourseId = params.get('student_course_id');
                if (!savedAdmissionNumber) {
                    setRestoring(false);
                    return;
                }
                (async () => {
                    setBusy(true);
                    try {
                        const data = await apiFetch(window.__ROUTES__.find, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ admission_number: savedAdmissionNumber }),
                        });
                        setStudent(data.student);
                        setCourses(data.courses || []);
                        setRegNo(savedAdmissionNumber);
                        if (savedStudentCourseId) {
                            const sc = (data.courses || []).find(
                                (c) => String(c.student_course_id) === String(savedStudentCourseId)
                            );
                            if (sc) {
                                setStudentCourseId(savedStudentCourseId);
                                setCourseId(sc.course_id);
                                await resolveCourseSelection(savedStudentCourseId, null);
                            }
                        }
                    } catch (err) {
                        // Stale/invalid saved link (student deleted, bad id, etc) —
                        // clear it quietly rather than greeting the page with an error.
                        updateUrl('', '');
                    } finally {
                        setBusy(false);
                        setRestoring(false);
                    }
                })();
            }, []);
            async function handleFind(e) {
                e.preventDefault();
                if (!regNo.trim()) return;
                setBusy(true);
                setBanner(null);
                try {
                    const data = await apiFetch(window.__ROUTES__.find, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ admission_number: regNo.trim() }),
                    });
                    setStudent(data.student);
                    setCourses(data.courses || []);
                    setStudentCourseId('');
                    setCourseId('');
                    setExistingAssessment(null);
                    updateUrl(regNo.trim(), '');
                } catch (err) {
                    setStudent(null);
                    setCourses([]);
                    setExistingAssessment(null);
                    setBanner({ type: 'error', text: err.message });
                } finally {
                    setBusy(false);
                }
            }
            function handleCourseChange(e) {
                const scId = e.target.value;
                setStudentCourseId(scId);
                setExistingAssessment(null);
                setCanUndo(false);
                if (!scId) {
                    setCourseId('');
                    updateUrl(regNo, '');
                    return;
                }
                const sc = courses.find((c) => String(c.student_course_id) === String(scId));
                if (!sc) {
                    setBanner({ type: 'error', text: 'Could not match that selection back to a course — the course list looks malformed.' });
                    return;
                }
                setCourseId(sc.course_id);
                updateUrl(regNo, scId);
                // Open the popup window SYNCHRONOUSLY, inside this click/change
                // handler, before the network round-trip below.
                const popup = window.open('', 'assessmentMarksPopup', POPUP_FEATURES);
                resolveCourseSelection(scId, popup);
            }
            function handleOpenMarksWindow() {
                if (!studentCourseId) return;
                const popup = window.open(
                    `${window.__ROUTES__.marksPopup}/${studentCourseId}`,
                    'assessmentMarksPopup',
                    POPUP_FEATURES
                );
                if (popup) popup.focus();
            }
            async function handleUndo() {
                if (!existingAssessment) return;
                if (!window.confirm('Undo this assessment? This cannot be redone once the 2-day window passes.')) return;
                const popup = window.open('', 'assessmentMarksPopup', POPUP_FEATURES);
                setBusy(true);
                setBanner(null);
                try {
                    await apiFetch(`${window.__ROUTES__.assessmentBase}/${existingAssessment.id}`, { method: 'DELETE' });
                    setBanner({ type: 'success', text: 'Assessment undone. Opening a fresh marks-entry window…' });
                    setExistingAssessment(null);
                    setCanUndo(false);
                    if (popup && studentCourseId) {
                        popup.location = `${window.__ROUTES__.marksPopup}/${studentCourseId}`;
                        popup.focus();
                    } else if (popup) {
                        popup.close();
                    }
                } catch (err) {
                    if (popup && !popup.closed) popup.close();
                    setBanner({ type: 'error', text: err.message });
                } finally {
                    setBusy(false);
                }
            }
            return (
                <div>
                    {window.__PERMISSIONS__.canAdd && (
                        <div className="ampl-card col-12">
                            <form onSubmit={handleFind} className="form-horizontal row pb-0 mb-0">
                                <div class="col-12">
                                    <input
                                        type="text"
                                        className="ampl-input"
                                        value={regNo}
                                        onChange={(e) => setRegNo(e.target.value)}
                                        placeholder="Enter Student Reg Number"
                                    />
                                </div>
                                <div class="col-12">
                                    <button type="submit" className="primary-btn small fix-gr-bg me-2 p-2 mt-1 col-12" disabled={busy}>
                                        {busy ? 'Searching…' : 'Find Student'}
                                    </button>
                                </div>
                            </form>
                            {restoring && <div className="ampl-muted mt-10">Restoring your last search…</div>}
                        </div>
                    )}
                    {banner && <div className={`ampl-banner ampl-banner-${banner.type}`}>{banner.text}</div>}
                    {student && (
                        <div className="ampl-card">
                            <div className="ampl-studentrow text-center">
                                <div><strong>{student.name} - {student.admission_number}</strong></div>
                            </div>
                            <div className="mt-10">
                                <div class="col-12">
                                    <select className="primary_select form-control col-12" value={studentCourseId} onChange={handleCourseChange} disabled={busy}>
                                        <option value="">Select Course </option>
                                        {courses.map((c) => (
                                            <option key={c.student_course_id} value={c.student_course_id}>{c.course_name}</option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                            {!existingAssessment && studentCourseId && !busy && (
                                <div className="mt-10">
                                    <div className="ampl-muted">
                                        No existing assessment for this course. A marks-entry window should have
                                        opened automatically — if your browser blocked it, use the button below.
                                    </div>
                                    <button type="button" className="ampl-btn ampl-btn-outline col-12" onClick={handleOpenMarksWindow}>
                                        Open Marks-Entry Window
                                    </button>
                                </div>
                            )}
                        </div>
                    )}
                    {existingAssessment && (
                        <div className="ampl-card col-12">
                            <div className="ampl-context-ribbon row">
                                <span className="chip col-5">{student.name}</span>
                                <span className="chip col-2">{student.admission_number}</span>
                                <span className="chip col-5">{existingAssessment.template.module_name}</span>
                            </div>
                            <div className="ampl-studentrow text-center">
                                <h4 style={{ margin: 0 }}>{existingAssessment.template.module_name}</h4>
                                <span className={`ampl-badge mt-1 ${canUndo ? 'ampl-badge-open' : 'ampl-badge-locked'}`}>
                                    {canUndo ? 'Undo available' : 'Locked'}
                                </span>
                            </div>
                            <div className="ampl-muted mt-10 text-center">
                                This student already has a saved assessment for this course
                                (by {existingAssessment.evaluator ? existingAssessment.evaluator.name : 'unknown'},{' '}
                                {existingAssessment.created_at ? existingAssessment.created_at.substring(0, 10) : ''}).
                            </div>
                            <div className="mt-15 row">
                                <a
                                    href={`${window.__ROUTES__.assessmentBase}/${existingAssessment.id}/pdf`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="primary-btn small fix-gr-bg col-12 p-2"
                                    style={{ marginRight: 10 }}
                                >
                                    Export PDF
                                </a>
                                {canUndo ? (
                                    <button type="button" className="ampl-btn ampl-btn-danger col-12 mt-2 text-center" onClick={handleUndo} disabled={busy}>
                                        {busy ? 'Undoing…' : 'Undo This Assessment'}
                                    </button>
                                ) : (
                                    <div className="ampl-locked-note col-12 mt-2 text-center">
                                        Only the staff member who created this, within 2 days, can undo it.
                                    </div>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            );
        }
        ReactDOM.createRoot(document.getElementById('assessment-mark-root')).render(<App />);
    </script>
    @endverbatim
    @pushOnce('scripts')
        <script>
            $(function() {
                if (!$('#assessments-table').length) {
                    return;
                }
                $('#table_id').DataTable({
                    scrollX: true,
                    dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 '" +
                        "<'dt-toolbar-title'>" +
                        "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-search'f><'dt-buttons-wrap'B>>" +
                        ">" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>>",
                    processing: true,
                    serverSide: true,
                    pageLength: 100,
                    lengthMenu: [
                        [25, 50, 100, -1],
                        [25, 50, 100, "All"]
                    ],
                    buttons: [{
                            extend: 'pdf',
                            title: getFileName(),
                            text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'csv',
                            title: getFileName(),
                            text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            title: getFileName(),
                            text: '<i title="export to excel" class="fa fa-file-excel-o"></i>',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'print',
                            title: getFileName(),
                            text: '<i title="print" class="fa fa-print"></i>',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'colvis',
                            title: getFileName(),
                            text: '<i title="column visibility" class="fa fa-eye"></i>',
                            columns: ':gt(0)'
                        }
                    ],
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    order: [
                        [0, 'desc']
                    ],
                    language: {
                        search: '<i class=""></i>',
                        searchPlaceholder: 'Search...',
                        paginate: {
                            previous: '<i class="fa-solid fa-angle-left"></i>',
                            next: '<i class="fa-solid fa-angle-right"></i>'
                        }
                    },
                });
            });
        </script>
    @endPushOnce
@endpush