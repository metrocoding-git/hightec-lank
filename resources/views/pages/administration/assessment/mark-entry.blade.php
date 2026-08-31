@extends('layouts.app')
@section('title')
    Final Assessment Marks
@endsection

@section('mainContent')
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
            <div class="white-box">
                <h4 class="mb-15">Final Assessment Records</h4>

                <form method="GET" action="{{ route('student-assessment.index') }}" class="row align-items-end mb-15">
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label class="ampl-filter-label">Course</label>
                        <select name="course_id" class="form-control">
                            <option value="">All Courses</option>
                            @foreach ($courseOptions ?? [] as $c)
                                <option value="{{ $c->id }}" {{ (string) request('course_id') === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label class="ampl-filter-label">Result</label>
                        <select name="result" class="form-control">
                            <option value="">All Results</option>
                            <option value="PASS" {{ request('result') === 'PASS' ? 'selected' : '' }}>Pass</option>
                            <option value="FAIL" {{ request('result') === 'FAIL' ? 'selected' : '' }}>Fail</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-2">
                        <label class="ampl-filter-label">Created By</label>
                        <select name="created_by" class="form-control">
                            <option value="">Everyone</option>
                            @foreach ($creatorOptions ?? [] as $u)
                                <option value="{{ $u->id }}" {{ (string) request('created_by') === (string) $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-2">
                        <label class="ampl-filter-label">Search (Name / Reg No / NIC)</label>
                        <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                               placeholder="Search student...">
                    </div>
                    <div class="col-lg-3 col-md-6 mb-2 d-flex" style="gap:8px;">
                        <button type="submit" class="ampl-btn ampl-btn-primary" style="flex:1;">Search</button>
                        <a href="{{ route('student-assessment.index') }}" class="ampl-btn ampl-btn-outline"
                           style="flex:1; text-align:center; text-decoration:none; margin-top:0;">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <x-table>
                        <table id="assessments-table" class="table data-table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Reg No</th>
                                    <th>Course</th>
                                    <th>Module</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Result</th>
                                    <th>Created By</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($recent ?? collect()) as $i => $a)
                                    <tr>
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
                                        <td>{{ $a->evaluator->name ?? '-' }}</td>
                                        <td>
                                            <a href="{{ url('/student/student-info/assessment/' . $a->id . '/pdf') }}"
                                               class="btn btn-sm btn-outline-primary" target="_blank">PDF</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No assessments found.</td>
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
    <style>
        .ampl-card { background: #fff; border-radius: 10px; padding: 18px 20px; margin-bottom: 16px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .ampl-findform { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
        .ampl-findform > div { flex: 1; min-width: 220px; }
        .ampl-input { width: 100%; padding: 8px 10px; border: 1px solid #d7dde3; border-radius: 6px; font-size: 14px; background: #fff; }
        .ampl-input:focus { outline: none; border-color: #1d4e89; }
        label { font-weight: 600; font-size: 13px; color: #37474f; display: block; margin-bottom: 4px; }
        .ampl-filter-label { font-weight: 600; font-size: 12px; color: #6b7784; display: block; margin-bottom: 4px; }
        .ampl-btn { border: none; border-radius: 6px; padding: 9px 18px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .ampl-btn-primary { background: #1d4e89; color: #fff; }
        .ampl-btn-primary:disabled { opacity: .6; cursor: default; }
        .ampl-btn-outline { background: #fff; border: 1px solid #1d4e89; color: #1d4e89; margin-top: 8px; }
        .ampl-btn-danger { background: #fff; border: 1px solid #c0392b; color: #c0392b; }
        .ampl-btn-danger:disabled { opacity: .5; cursor: default; }
        .ampl-banner { padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
        .ampl-banner-error { background: #fdecea; color: #a12a2a; border: 1px solid #f5c6c6; }
        .ampl-banner-success { background: #e8f6ee; color: #1e7a43; border: 1px solid #bfe6cf; }
        .ampl-studentrow { display: flex; gap: 24px; flex-wrap: wrap; font-size: 14px; color: #37474f; }
        .ampl-grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media (max-width: 767px) { .ampl-grid2 { grid-template-columns: 1fr; } }
        .ampl-muted { color: #8a95a1; font-size: 13px; }
        .ampl-tablewrap { overflow-x: auto; }
        .ampl-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .ampl-table th, .ampl-table td { border: 1px solid #e1e6eb; padding: 8px; vertical-align: middle; }
        .ampl-table th { background: #f4f6f8; text-align: left; }
        .ampl-titlerow td { background: #eef3f9; }
        .ampl-yieldcell { text-align: center; font-weight: 700; background: #f9fbfc; }
        .ampl-totalcell { text-align: right; font-weight: 700; }
        .ampl-pass { color: #1e7a43; font-weight: 700; margin-top: 4px; }
        .ampl-fail { color: #a12a2a; font-weight: 700; margin-top: 4px; }
        .ampl-badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 700; }
        .ampl-badge-locked { background: #f4f6f8; color: #6b7784; }
        .ampl-badge-open { background: #e8f6ee; color: #1e7a43; }
        .ampl-locked-note { color: #8a95a1; font-size: 13px; margin-top: 8px; }
        .ampl-context-ribbon {
            display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
            background: linear-gradient(90deg, #1d4e89, #2f6fb0); color: #fff;
            padding: 12px 18px; border-radius: 8px; margin-bottom: 16px; font-size: 13px;
        }
        .ampl-context-ribbon .chip { background: rgba(255,255,255,.16); padding: 4px 12px; border-radius: 20px; font-weight: 600; }
        .mt-10 { margin-top: 10px; }
        .mt-15 { margin-top: 15px; }

        #assessments-table thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff; font-weight: 600; white-space: nowrap; border: none;
        }
        #assessments-table tbody tr { transition: background-color .12s ease; }
        #assessments-table tbody tr:hover { background-color: rgba(0, 0, 0, 0.025); }
        #assessments-table tbody td { vertical-align: middle; }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/18.3.1/umd/react.production.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/18.3.1/umd/react-dom.production.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/7.24.7/babel.min.js"></script>

    <script>
        window.__CSRF_TOKEN__ = "{{ csrf_token() }}";
        window.__ROUTES__ = {
            find: "{{ route('student-assessment.find') }}",
            existing: "{{ url('/student/student-info/assessment/existing') }}",
            marksPopup: "{{ url('/student/student-info/assessment/marks-popup') }}",
            assessmentBase: "{{ url('/student/student-info/assessment') }}",
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

        function App() {
            const [regNo, setRegNo] = React.useState('');
            const [busy, setBusy] = React.useState(false);
            const [banner, setBanner] = React.useState(null);
            const [student, setStudent] = React.useState(null);
            const [courses, setCourses] = React.useState([]);
            const [studentCourseId, setStudentCourseId] = React.useState('');
            const [courseId, setCourseId] = React.useState('');
            const [existingAssessment, setExistingAssessment] = React.useState(null);
            const [canUndo, setCanUndo] = React.useState(false);

            // Listens for the popup marks-entry window telling us it saved
            // successfully, so this window can update without the popup
            // having to reach back into our React state directly.
            React.useEffect(() => {
                function handleMessage(e) {
                    if (e.origin !== window.location.origin) return;
                    if (!e.data || e.data.type !== 'assessment-saved') return;
                    if (String(e.data.studentCourseId) === String(studentCourseId)) {
                        checkExisting(studentCourseId);
                    }
                }
                window.addEventListener('message', handleMessage);
                return () => window.removeEventListener('message', handleMessage);
            }, [studentCourseId]);

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
                } catch (err) {
                    setStudent(null);
                    setCourses([]);
                    setExistingAssessment(null);
                    setBanner({ type: 'error', text: err.message });
                } finally {
                    setBusy(false);
                }
            }

            async function checkExisting(scId) {
                setBusy(true);
                setBanner(null);
                try {
                    const data = await apiFetch(`${window.__ROUTES__.existing}/${scId}`);
                    if (data.status === 'ok' && data.assessment) {
                        setExistingAssessment(data.assessment);
                        setCanUndo(!!data.can_undo);
                    } else {
                        setExistingAssessment(null);
                        setCanUndo(false);
                    }
                } catch (err) {
                    setBanner({ type: 'error', text: err.message });
                } finally {
                    setBusy(false);
                }
            }

            async function handleCourseChange(e) {
                const scId = e.target.value;
                setStudentCourseId(scId);
                setExistingAssessment(null);
                setCanUndo(false);

                if (!scId) {
                    setCourseId('');
                    return;
                }

                const sc = courses.find((c) => String(c.student_course_id) === String(scId));
                if (!sc) {
                    setBanner({ type: 'error', text: 'Could not match that selection back to a course — the course list looks malformed.' });
                    return;
                }
                setCourseId(sc.course_id);

                // Open the popup window SYNCHRONOUSLY, inside this click/change
                // handler, before the network round-trip below — most browsers
                // block window.open() once it happens after an `await`, treating
                // it as an unrequested popup rather than a direct user action.
                // We decide afterwards whether to navigate it or close it again.
                const popup = window.open('', 'assessmentMarksPopup', POPUP_FEATURES);

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
                        } else {
                            setBanner({ type: 'error', text: 'Could not open the marks-entry window — please allow pop-ups for this site and try again.' });
                        }
                    }
                } catch (err) {
                    if (popup && !popup.closed) popup.close();
                    setBanner({ type: 'error', text: err.message });
                } finally {
                    setBusy(false);
                }
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
                    <div className="ampl-card">
                        <form onSubmit={handleFind} className="ampl-findform">
                            <div>
                                <label>Student Registration Number</label>
                                <input
                                    type="text"
                                    className="ampl-input"
                                    value={regNo}
                                    onChange={(e) => setRegNo(e.target.value)}
                                    placeholder="e.g. HTL/2024/0123"
                                />
                            </div>
                            <button type="submit" className="ampl-btn ampl-btn-primary" disabled={busy}>
                                {busy ? 'Searching…' : 'Find Student'}
                            </button>
                        </form>
                    </div>

                    {banner && <div className={`ampl-banner ampl-banner-${banner.type}`}>{banner.text}</div>}

                    {student && (
                        <div className="ampl-card">
                            <div className="ampl-studentrow">
                                <div><strong>{student.name}</strong></div>
                                <div>Reg No: {student.admission_number}</div>
                                <div>NIC: {student.nic || '-'}</div>
                            </div>

                            <div className="ampl-grid2 mt-10">
                                <div>
                                    <label>Select Course (started only)</label>
                                    <select className="ampl-input" value={studentCourseId} onChange={handleCourseChange} disabled={busy}>
                                        <option value="">-- Select Course --</option>
                                        {courses.map((c) => (
                                            <option key={c.student_course_id} value={c.student_course_id}>{c.course_name}</option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            {!existingAssessment && studentCourseId && !busy && (
                                <div className="ampl-muted mt-10">
                                    No existing assessment for this course — a marks-entry window should have opened.
                                    If nothing opened, your browser likely blocked the pop-up; allow pop-ups for this
                                    site and reselect the course.
                                </div>
                            )}
                        </div>
                    )}

                    {existingAssessment && (
                        <div className="ampl-card">
                            <div className="ampl-context-ribbon">
                                <span className="chip">{student.name}</span>
                                <span className="chip">{student.admission_number}</span>
                                <span className="chip">{existingAssessment.template.module_name}</span>
                            </div>

                            <div className="ampl-studentrow">
                                <h4 style={{ margin: 0 }}>{existingAssessment.template.module_name}</h4>
                                <span className={`ampl-badge ${canUndo ? 'ampl-badge-open' : 'ampl-badge-locked'}`}>
                                    {canUndo ? 'Undo available' : 'Locked'}
                                </span>
                            </div>
                            <div className="ampl-muted mt-10">
                                This student already has a saved assessment for this course
                                (by {existingAssessment.evaluator ? existingAssessment.evaluator.name : 'unknown'},{' '}
                                {existingAssessment.created_at ? existingAssessment.created_at.substring(0, 10) : ''}).
                            </div>

                            <div className="mt-15">
                                <a
                                    href={`${window.__ROUTES__.assessmentBase}/${existingAssessment.id}/pdf`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="ampl-btn ampl-btn-outline"
                                    style={{ marginRight: 10 }}
                                >
                                    View / Export PDF
                                </a>
                                {canUndo ? (
                                    <button type="button" className="ampl-btn ampl-btn-danger" onClick={handleUndo} disabled={busy}>
                                        {busy ? 'Undoing…' : 'Undo This Assessment'}
                                    </button>
                                ) : (
                                    <div className="ampl-locked-note">
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
            $(function () {
                if (!$('#assessments-table').length) {
                    return;
                }

                $('#assessments-table').DataTable({
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
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            title: typeof getFileName === 'function' ? getFileName() : 'assessments',
                            text: '<i class="fa-regular fa-copy"></i>',
                            titleAttr: 'Copy'
                        },
                        {
                            extend: 'csvHtml5',
                            title: typeof getFileName === 'function' ? getFileName() : 'assessments',
                            text: '<i class="fa-solid fa-file-csv"></i>',
                            titleAttr: 'CSV'
                        },
                        {
                            extend: 'excelHtml5',
                            title: typeof getFileName === 'function' ? getFileName() : 'assessments',
                            text: '<i class="fa-solid fa-file-excel"></i>',
                            titleAttr: 'Excel'
                        },
                        {
                            extend: 'pdfHtml5',
                            title: typeof getFileName === 'function' ? getFileName() : 'assessments',
                            text: '<i class="fa-solid fa-file-pdf"></i>',
                            titleAttr: 'PDF'
                        },
                        {
                            extend: 'print',
                            title: typeof getFileName === 'function' ? getFileName() : 'assessments',
                            text: '<i class="fa-solid fa-print"></i>',
                            titleAttr: 'Print'
                        }
                    ],
                    pageLength: 25,
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