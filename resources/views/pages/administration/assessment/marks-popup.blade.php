@extends('layouts.app')
@section('title')
    Enter Assessment Marks
@endsection

@section('mainContent')
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div id="assessment-popup-root"></div>
        </div>
    </section>
@endsection

@push('script')
    <style>
        body {
            background: #f4f6f8;
        }

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

        .ampl-input-sm {
            padding: 5px 8px;
        }

        .ampl-input:focus {
            outline: none;
            border-color: #1d4e89;
        }

        .ampl-input:disabled {
            background: #f4f6f8;
            color: #8a95a1;
        }

        label {
            font-weight: 600;
            font-size: 13px;
            color: #37474f;
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

        .ampl-btn-primary {
            background: #1d4e89;
            color: #fff;
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

        .ampl-banner-warning {
            background: #fff6e5;
            color: #8a6300;
            border: 1px solid #f2dfa1;
        }

        /* NEW — informational only (practical/theory completion counts),
           deliberately a calmer blue rather than the amber warning color
           used for payment/verification, since "X of Y completed" isn't a
           problem to flag, just a status readout. */
        .ampl-banner-info {
            background: #eef3f9;
            color: #1d4e89;
            border: 1px solid #d7e2ee;
        }

        .ampl-banner-info strong {
            color: #123a68;
        }

        /* NEW — "header want like attendance card": photo + name/sub +
           an Admission/NIC/Course meta row, styled as its own card at the
           top of the popup (same visual language as .ampl-card elsewhere
           in this file — white card, soft shadow, rounded corners). */
        .header {
            display: flex;
            align-items: center;
            gap: 20px;
            background: #fff;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 16px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
            flex-wrap: wrap;
        }

        .header .photo {
            width: 84px;
            height: 84px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #d7dde3;
            flex-shrink: 0;
        }

        .header .titles {
            flex: 1;
            min-width: 220px;
        }

        .header .titles h1 {
            font-size: 19px;
            font-weight: 700;
            margin: 0 0 2px;
            color: #1d4e89;
        }

        .header .titles .sub {
            font-size: 12.5px;
            color: #8a95a1;
            margin-bottom: 12px;
        }

        .header .meta-grid {
            display: flex;
            gap: 28px;
            flex-wrap: wrap;
        }

        .header .meta-grid .item .label {
            font-size: 11px;
            font-weight: 600;
            color: #8a95a1;
            text-transform: uppercase;
            letter-spacing: .02em;
            margin-bottom: 2px;
        }

        .header .meta-grid .item .value {
            font-size: 14px;
            font-weight: 600;
            color: #37474f;
        }

        /* NEW — "there want link final attendace card for link locate that
           card": a link inside the new .header block to this student's
           real Practical/Theory Attendance Card (StudentPracticalCardController
           ::show(), route `student-practical-card.show` — the same route
           StudentController's own student-list dropdown already links to
           as "Attendance Card"). Reuses .ampl-btn/.ampl-btn-outline, but
           those are normally <button>s with a margin-top meant for stacking
           under a form field — neither makes sense for an inline link
           sitting next to the header's meta-grid, so both are overridden
           here, scoped to just this header so nothing elsewhere changes. */
        .header a.ampl-btn {
            flex-shrink: 0;
            margin-top: 0;
            text-decoration: none;
            display: inline-block;
            white-space: nowrap;
        }

        .ampl-grid3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
        }

        @media (max-width: 767px) {
            .ampl-grid3 {
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

        .ampl-titlerow.is-over td {
            background: #fdecea;
        }

        .ampl-yieldcell {
            text-align: center;
            font-weight: 700;
            background: #f9fbfc;
        }

        .ampl-yieldcell.is-over {
            background: #fdecea;
            color: #a12a2a;
        }

        .ampl-pass {
            color: #1e7a43;
            font-weight: 700;
        }

        .ampl-fail {
            color: #a12a2a;
            font-weight: 700;
        }

        .ampl-title-overlimit {
            color: #a12a2a;
            font-size: 11.5px;
            font-weight: 700;
            margin-top: 4px;
        }

        .ampl-context-ribbon {
            display: flex;
            gap: 10px;
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

        .ampl-competency-toggle {
            display: flex;
            gap: 6px;
        }

        .ampl-competency-toggle button {
            flex: 1;
            border: 1px solid #d7dde3;
            background: #fff;
            padding: 6px 4px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            color: #6b7784;
            transition: all .12s ease;
        }

        .ampl-competency-toggle button.is-active-c {
            background: #e8f6ee;
            border-color: #1e7a43;
            color: #1e7a43;
        }

        .ampl-competency-toggle button.is-active-nc {
            background: #fdecea;
            border-color: #a12a2a;
            color: #a12a2a;
        }

        .ampl-competency-toggle button:disabled {
            opacity: .55;
            cursor: default;
        }

        .ampl-summary-bar {
            margin-top: 16px;
            padding: 16px 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 17px;
            font-weight: 700;
        }

        .ampl-summary-bar.is-pass {
            background: #e8f6ee;
            color: #1e7a43;
        }

        .ampl-summary-bar.is-fail {
            background: #fdecea;
            color: #a12a2a;
        }

        .ampl-summary-bar .sub {
            font-size: 12px;
            font-weight: 500;
            opacity: .8;
        }

        .ampl-sig-preview {
            height: 180px;
            border: 1px solid #d7dde3;
            border-radius: 8px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-align: center;
        }

        .ampl-sig-preview:hover {
            border-color: #1d4e89;
        }

        .ampl-sig-preview img {
            height: 150px;
        }

        .ampl-sig-canvas-wrap canvas {
            border: 1px solid #ccc;
            width: 100%;
            height: 350px;
            touch-action: none;
        }

        .ampl-signed-lock-note {
            background: #eef3f9;
            border: 1px solid #d7e2ee;
            color: #1d4e89;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .ampl-closing-overlay {
            position: fixed;
            inset: 0;
            background: rgba(20, 26, 32, .6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .ampl-closing-box {
            background: #fff;
            border-radius: 10px;
            padding: 30px 40px;
            text-align: center;
            font-size: 16px;
        }

        .ampl-closing-box .checkmark {
            font-size: 40px;
            color: #1e7a43;
            margin-bottom: 10px;
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
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        window.__CSRF_TOKEN__ = "{{ csrf_token() }}";
        window.__EVALUATOR_NAME__ = @json(Auth::user()->name);
        window.__CONTEXT__ = {
            studentCourseId: {{ (int) $studentCourse->id }},
            courseId: {{ (int) $studentCourse->course_id }},
            studentId: {{ (int) $studentCourse->student_id }},
            studentName: @json($studentCourse->student->student_name ?? ''),
            nationalIdNumber: @json($studentCourse->student->national_id_number ?? ''),
            photo: @json($studentCourse->student->photo ?? ''),
            photoUrl: @json($studentCourse->student->photo ? asset('storage/' . $studentCourse->student->photo) : ''),
            admissionNumber: @json($studentCourse->student->admission_number ?? ''),
            branchId: {{ $studentCourse->student->branch_id ?? 'null' }},
            branchName: @json(optional($studentCourse->student->branch)->name ?? ''),
            courseName: @json($studentCourse->course->name ?? ''),
        };
        window.__VEHICLES__ = @json($vehicles);
        window.__ROUTES__ = {
            existing: "{{ url('/student/student-info/assessment/existing') }}",
            template: "{{ url('/student/student-info/assessment/template') }}",
            store: "{{ route('student-assessment.store') }}",
            // NEW — "there want link final attendace card for link locate
            // that card": links to this student's real Practical/Theory
            // Attendance Card (StudentPracticalCardController::show(), the
            // same `student-practical-card.show` route StudentController's
            // own student-list dropdown already opens as "Attendance
            // Card"). That route only takes a student id and defaults to
            // whichever course was enrolled last — passing course_id here
            // as a query param (buildCardData() already reads
            // $request->input('course_id')) makes it land directly on THIS
            // course's card instead.
            attendanceCard: "{{ route('student-practical-card.show', [$studentCourse->student_id, 'course_id' => $studentCourse->course_id]) }}",
        };

        @php
            // Computed here as a plain PHP variable, NOT inline as a
            // multi-line closure passed directly into @json(...) — that
            // was what was actually tripping up the Blade compiler (the
            // directive-argument parser chokes on a closure/array literal
            // spanning many lines inside @json(...)). @json() now only
            // ever receives a single already-built variable, which is a
            // trivially simple argument for it to handle.
            $__paymentCheckInvoices = ($invoices ?? collect())->map(function ($inv) {
                return [
                    'type' => $inv->invoice_type,
                    'total' => (float) $inv->total_amount,
                    'paid' => (float) $inv->paid_amount,
                    'balance' => (float) ($inv->total_amount - $inv->paid_amount),
                    'status' => $inv->payment_status,
                ];
            })->values();
        @endphp
        window.__PAYMENT_CHECK__ = {
            incomplete: @json($paymentIncomplete ?? false),
            invoices: @json($__paymentCheckInvoices),
        };
        window.__VERIFICATION__ = {
            name: @json((bool) optional($studentCourse->student)->isVerified('Full Name Verified')),
            nic: @json((bool) optional($studentCourse->student)->isVerified('NIC Verified')),
            address: @json((bool) optional($studentCourse->student)->isVerified('Address Verified')),
        };
        // NEW — "how many practical subject completed (like 10 for 4)" /
        // "how many theory subject completed": {completed, total} for each,
        // computed server-side by
        // StudentAssessmentController::countSubjectCompletion().
        window.__PROGRESS__ = {
            practical: @json($practicalProgress ?? ['completed' => 0, 'total' => 0]),
            theory: @json($theoryProgress ?? ['completed' => 0, 'total' => 0]),
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

        // Percentage (0-100) -> letter grade — mirrors
        // StudentAssessmentController::percentageToGrade() exactly ("90-100
        // = A, 80-89 = B+, 70-79 = B, 60-69 = C"), so this live preview can
        // never disagree with what actually gets saved.
        const GRADE_BANDS = [
            { min: 90, grade: 'A' },
            { min: 80, grade: 'B+' },
            { min: 70, grade: 'B' },
            { min: 60, grade: 'C' },
        ];
        function percentageToGrade(percentage) {
            for (const band of GRADE_BANDS) {
                if (percentage >= band.min) return band.grade;
            }
            return 'FAIL';
        }

        const SignaturePad = React.forwardRef(function SignaturePad(props, ref) {
            const canvasRef = React.useRef(null);
            const padRef = React.useRef(null);
            const [dataUrl, setDataUrl] = React.useState('');

            React.useImperativeHandle(ref, () => ({
                getDataURL() {
                    return dataUrl;
                },
                clear() {
                    setDataUrl('');
                    if (props.onChange) props.onChange('');
                },
            }));

            function resizeCanvas() {
                const canvas = canvasRef.current;
                if (!canvas) return;
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                if (padRef.current) padRef.current.clear();
            }

            React.useEffect(() => {
                const $ = window.jQuery || window.$;
                if (!$) return;
                const $modal = $('#assessmentSignatureModal');

                function onShown() {
                    const canvas = canvasRef.current;
                    if (!padRef.current) {
                        padRef.current = new window.SignaturePad(canvas);
                        canvas.style.touchAction = 'none';
                    }
                    resizeCanvas();
                }

                $modal.on('shown.bs.modal', onShown);
                return () => {
                    $modal.off('shown.bs.modal', onShown);
                };
            }, []);

            function openModal() {
                const $ = window.jQuery || window.$;
                if ($) $('#assessmentSignatureModal').modal('show');
            }

            function handleClear() {
                if (padRef.current) padRef.current.clear();
            }

            function handleSave() {
                if (padRef.current && !padRef.current.isEmpty()) {
                    const url = padRef.current.toDataURL();
                    setDataUrl(url);
                    if (props.onChange) props.onChange(url);
                }
                const $ = window.jQuery || window.$;
                if ($) $('#assessmentSignatureModal').modal('hide');
            }

            function handleResign() {
                setDataUrl('');
                if (props.onChange) props.onChange('');
            }

            return (
                <div>
                    <div className="ampl-sig-preview" onClick={openModal}>
                        {dataUrl ? <img src={dataUrl} alt="Signature" /> : <span className="ampl-muted">Tap here to draw signature</span>}
                    </div>

                    {dataUrl && (
                        <button type="button" className="ampl-btn ampl-btn-outline" onClick={handleResign}>
                            Re-Sign
                        </button>
                    )}

                    <div className="modal fade" id="assessmentSignatureModal" tabIndex="-1">
                        <div className="modal-dialog modal-lg">
                            <div className="modal-content">
                                <div className="modal-header">
                                    <h4 className="modal-title">Sign Here</h4>
                                    <button type="button" className="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div className="modal-body ampl-sig-canvas-wrap">
                                    <canvas ref={canvasRef}></canvas>
                                </div>
                                <div className="modal-footer">
                                    <button type="button" className="btn btn-warning" onClick={handleClear}>Clear</button>
                                    <button type="button" className="btn btn-primary" onClick={handleSave}>Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            );
        });

        function CompetencyToggle({ value, onChange, disabled }) {
            return (
                <div className="ampl-competency-toggle">
                    <button type="button" className={value === 'C' ? 'is-active-c' : ''} onClick={() => onChange('C')} disabled={disabled}>C</button>
                    <button type="button" className={value === 'NC' ? 'is-active-nc' : ''} onClick={() => onChange('NC')} disabled={disabled}>NC</button>
                </div>
            );
        }

        function App() {
            const ctx = window.__CONTEXT__;
            const vehicles = window.__VEHICLES__ || [];
            const paymentCheck = window.__PAYMENT_CHECK__ || { incomplete: false, invoices: [] };
            const verification = window.__VERIFICATION__ || { name: true, nic: true, address: true };
            const progress = window.__PROGRESS__ || {
                practical: { completed: 0, total: 0 },
                theory: { completed: 0, total: 0 },
            };

            const [loading, setLoading] = React.useState(true);
            const [busy, setBusy] = React.useState(false);
            const [banner, setBanner] = React.useState(null);
            const [blocked, setBlocked] = React.useState(null); // set if an assessment already exists (race condition guard)
            const [template, setTemplate] = React.useState(null);
            const [marks, setMarks] = React.useState({});
            const [comments, setComments] = React.useState('');
            const [vehicleId, setVehicleId] = React.useState('');
            const [equipmentModel, setEquipmentModel] = React.useState('');
            const [hasSignature, setHasSignature] = React.useState(false);
            const [assessedAt, setAssessedAt] = React.useState(() => {
                const now = new Date();
                const pad = (n) => String(n).padStart(2, '0');
                return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
            });
            const [closing, setClosing] = React.useState(false);
            const sigRef = React.useRef(null);

            React.useEffect(() => {
                (async () => {
                    setLoading(true);
                    try {
                        const existing = await apiFetch(`${window.__ROUTES__.existing}/${ctx.studentCourseId}`);
                        if (existing.status === 'ok' && existing.assessment) {
                            setBlocked('This course already has a saved assessment — it must have been entered in another window. Close this one and undo it from the main page first if you need to redo it.');
                            setLoading(false);
                            return;
                        }

                        const data = await apiFetch(`${window.__ROUTES__.template}/${ctx.courseId}`);
                        const tpl = data.template;
                        setTemplate(tpl);

                        const initMarks = {};
                        (tpl.titles || []).forEach((t) => {
                            (t.sub_titles || []).forEach((st) => {
                                initMarks[st.id] = { competency: '', marks: '', remarks: '' };
                            });
                        });
                        setMarks(initMarks);
                    } catch (err) {
                        setBanner({ type: 'error', text: err.message });
                    } finally {
                        setLoading(false);
                    }
                })();
            }, []);

            function updateMark(subId, field, value) {
                if (hasSignature) return;
                setMarks((prev) => ({ ...prev, [subId]: { ...prev[subId], [field]: value } }));
            }

            function titleSum(title) {
                return (title.sub_titles || []).reduce((sum, st) => {
                    const v = parseFloat(marks[st.id] && marks[st.id].marks);
                    return sum + (isNaN(v) ? 0 : v);
                }, 0);
            }

            // "mark subtitle total can't be go more title mark. sub title
            // marks total leas than or equal to title total marks"
            function isTitleOverLimit(title) {
                return titleSum(title) > Number(title.max_marks || 0);
            }

            const overallMax = template ? (template.titles || []).reduce((s, t) => s + Number(t.max_marks || 0), 0) : 0;
            const grandTotal = template ? (template.titles || []).reduce((s, t) => s + titleSum(t), 0) : 0;
            const percentage = overallMax > 0 ? (grandTotal / overallMax) * 100 : 0;
            const grade = percentageToGrade(percentage);
            const qualified = grade !== 'FAIL';
            const anyTitleOverLimit = template ? (template.titles || []).some(isTitleOverLimit) : false;

            async function handleSave() {
                if (!template) return;

                const overLimitTitles = (template.titles || []).filter(isTitleOverLimit);
                if (overLimitTitles.length) {
                    setBanner({
                        type: 'error',
                        text: `Sub Title marks total can't be more than the Title's Max Marks: ${overLimitTitles.map((t) => t.title).join(', ')}`,
                    });
                    return;
                }

                const marksPayload = [];
                (template.titles || []).forEach((t) => {
                    (t.sub_titles || []).forEach((st) => {
                        const m = marks[st.id] || {};
                        if (!m.competency) return;
                        marksPayload.push({
                            title_id: t.id,
                            sub_title_id: st.id,
                            competency: m.competency,
                            marks: m.marks || 0,
                            remarks: m.remarks || '',
                        });
                    });
                });

                if (!marksPayload.length) {
                    setBanner({ type: 'error', text: 'Mark at least one checklist item before saving.' });
                    return;
                }

                const signature = sigRef.current ? sigRef.current.getDataURL() : '';

                setBusy(true);
                setBanner(null);
                try {
                    await apiFetch(window.__ROUTES__.store, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            student_id: ctx.studentId,
                            student_course_id: ctx.studentCourseId,
                            assessment_template_id: template.id,
                            vehicle_id: vehicleId || null,
                            equipment_model: equipmentModel,
                            branch_id: ctx.branchId,
                            assessed_at: assessedAt,
                            comments: comments,
                            signature_data: signature,
                            marks: marksPayload,
                        }),
                    });

                    setClosing(true);

                    if (window.opener) {
                        window.opener.postMessage(
                            { type: 'assessment-saved', studentCourseId: ctx.studentCourseId },
                            window.location.origin
                        );
                    }

                    setTimeout(() => {
                        window.close();
                    }, 900);
                } catch (err) {
                    setBusy(false);
                    setBanner({ type: 'error', text: err.message });
                }
            }

            if (loading) {
                return <div className="ampl-card"><span className="ampl-muted">Loading assessment template…</span></div>;
            }

            if (blocked) {
                return (
                    <div className="ampl-card">
                        <div className="ampl-banner ampl-banner-error">{blocked}</div>
                        <button type="button" className="ampl-btn ampl-btn-outline" onClick={() => window.close()}>Close Window</button>
                    </div>
                );
            }

            const unverifiedFields = [];
            if (!verification.name) unverifiedFields.push('Full Name');
            if (!verification.nic) unverifiedFields.push('NIC');
            if (!verification.address) unverifiedFields.push('Address');

            return (
                <div>
                    <div className="ampl-context-ribbon">
                        <span className="chip">{ctx.studentName}</span>
                        <span className="chip">{ctx.admissionNumber}</span>
                        <span className="chip">{ctx.courseName}</span>
                    </div>

                    {/* FIXED — "header is not work.. that header want like
                        attendance card". This block used to be pasted-in
                        Blade markup (@if / {{ }} / PHP's ?? '-') sitting
                        inside a @verbatim React script — Blade never
                        touches anything inside @verbatim, and PHP's ??
                        isn't valid JS, so it was a hard Babel syntax error
                        that broke the ENTIRE popup (blank white screen),
                        not just the header. Rewritten as real JSX using the
                        ctx object already in scope, plus a matching .header
                        CSS block added above (none of these classes existed
                        before — .header/.photo/.titles/.meta-grid/.item/
                        .label/.value are all new). ctx.photoUrl is a new
                        field added to window.__CONTEXT__ above, built
                        server-side with asset('storage/...') since that
                        Blade helper can't be called from client-side JS.

                        NEW — "there want link final attendace card for link
                        locate that card": a "View Attendance Card" link
                        added at the end of this header, opening this
                        student's real Practical/Theory Attendance Card
                        (window.__ROUTES__.attendanceCard, built server-side
                        above) in a new tab — same route/target="_blank"
                        pattern the student list's own dropdown already uses
                        to reach that same page. */}
                    <div className="header">
                        {ctx.photoUrl && (
                            <img className="photo" src={ctx.photoUrl} alt="photo" />
                        )}
                        <div className="titles">
                            <h1>{ctx.studentName}</h1>
                            <div className="sub">Practical Attendance Card</div>
                            <div className="meta-grid">
                                <div className="item">
                                    <div className="label">Admission No</div>
                                    <div className="value">{ctx.admissionNumber || '-'}</div>
                                </div>
                                <div className="item">
                                    <div className="label">NIC</div>
                                    <div className="value">{ctx.nationalIdNumber || '-'}</div>
                                </div>
                                <div className="item">
                                    <div className="label">Course</div>
                                    <div className="value">{ctx.courseName || '-'}</div>
                                </div>
                            </div>
                        </div>
                        {window.__ROUTES__.attendanceCard && (
                            <a
                                className="ampl-btn ampl-btn-outline"
                                href={window.__ROUTES__.attendanceCard}
                                target="_blank"
                                rel="noopener"
                            >
                                View Attendance Card
                            </a>
                        )}
                    </div>

                    {/* "before student assesment mark. want display student related
                        all payments if not complete want notify, student details
                        is verified" — notice only, doesn't block entering marks. */}
                    {paymentCheck.incomplete && (
                        <div className="ampl-banner ampl-banner-warning">
                            ⚠ This student's payments for this course is not fully complete yet
                            {paymentCheck.invoices.some((inv) => inv.status !== 'full') && (
                                <> — outstanding balance:{' '}
                                    {paymentCheck.invoices
                                        .filter((inv) => inv.status !== 'full')
                                        .reduce((s, inv) => s + inv.balance, 0)
                                        .toFixed(2)}
                                </>
                            )}. You can still proceed — please confirm with accounts if unsure.
                        </div>
                    )}
                    {unverifiedFields.length > 0 && (
                        <div className="ampl-banner ampl-banner-warning">
                            ⚠ This student's details are not fully verified yet: {unverifiedFields.join(', ')}.
                        </div>
                    )}

                    {/* NEW — "how many practical subject completed (like 10 for
                        4)" / "how many theory subject completed": informational
                        readout, doesn't block entering marks either. */}
                    <div className="ampl-banner ampl-banner-info">
                        <strong>Practical:</strong> {progress.practical.completed} / {progress.practical.total} subject{progress.practical.total === 1 ? '' : 's'} completed
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <strong>Theory:</strong> {progress.theory.completed} / {progress.theory.total} subject{progress.theory.total === 1 ? '' : 's'} completed
                    </div>

                    {banner && <div className={`ampl-banner ampl-banner-${banner.type}`}>{banner.text}</div>}

                    {template && (
                        <div className="ampl-card">
                            <h4>{template.module_name}</h4>
                            <div className="ampl-muted">NCS Code: {template.ncs_code || '-'}</div>

                            {hasSignature && (
                                <div className="ampl-signed-lock-note mt-10">
                                    This sheet is signed — marks are locked. Click "Re-Sign" below to make changes.
                                </div>
                            )}

                            <div className="ampl-tablewrap mt-10">
                                <table className="ampl-table">
                                    <thead>
                                        <tr>
                                            <th style={{ width: '35%' }}>Item</th>
                                            <th style={{ width: '110px' }}>Competency</th>
                                            <th>Marks</th>
                                            <th>Remarks</th>
                                            <th>Yield Marks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {(template.titles || []).map((t) => {
                                            const overLimit = isTitleOverLimit(t);
                                            return (
                                                <React.Fragment key={t.id}>
                                                    <tr className={`ampl-titlerow ${overLimit ? 'is-over' : ''}`}>
                                                        <td colSpan="4">
                                                            <strong>{t.title}</strong>{' '}
                                                            <span className="ampl-muted">(Max {t.max_marks})</span>
                                                            {overLimit && (
                                                                <div className="ampl-title-overlimit">
                                                                    Sub Title marks total ({titleSum(t)}) is more than this Title's Max Marks ({t.max_marks}) — reduce a mark below before saving.
                                                                </div>
                                                            )}
                                                        </td>
                                                        <td rowSpan={(t.sub_titles || []).length + 1} className={`ampl-yieldcell ${overLimit ? 'is-over' : ''}`}>
                                                            {titleSum(t)} / {t.max_marks}
                                                        </td>
                                                    </tr>
                                                    {(t.sub_titles || []).map((st) => (
                                                        <tr key={st.id}>
                                                            <td>{st.sub_title}</td>
                                                            <td>
                                                                <CompetencyToggle
                                                                    value={(marks[st.id] && marks[st.id].competency) || ''}
                                                                    onChange={(v) => updateMark(st.id, 'competency', v)}
                                                                    disabled={hasSignature}
                                                                />
                                                            </td>
                                                            <td>
                                                                <input
                                                                    type="number" min="0" className="ampl-input ampl-input-sm"
                                                                    value={(marks[st.id] && marks[st.id].marks) || ''}
                                                                    onChange={(e) => updateMark(st.id, 'marks', e.target.value)}
                                                                    disabled={hasSignature}
                                                                />
                                                            </td>
                                                            <td>
                                                                <input
                                                                    type="text" className="ampl-input ampl-input-sm"
                                                                    value={(marks[st.id] && marks[st.id].remarks) || ''}
                                                                    onChange={(e) => updateMark(st.id, 'remarks', e.target.value)}
                                                                    disabled={hasSignature}
                                                                />
                                                            </td>
                                                        </tr>
                                                    ))}
                                                </React.Fragment>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>

                            <div className={`ampl-summary-bar ${qualified ? 'is-pass' : 'is-fail'}`}>
                                <div>
                                    Total: {grandTotal} / {overallMax} ({percentage.toFixed(1)}%) — Grade: {grade}
                                    <div className="sub">Live total — updates as marks are entered</div>
                                </div>
                                <div>{qualified ? 'PASS' : 'FAIL'}</div>
                            </div>

                            <div className="ampl-grid3 mt-15">
                                <div>
                                    <label>Comments</label>
                                    <textarea className="ampl-input" rows="2" value={comments} onChange={(e) => setComments(e.target.value)}></textarea>
                                </div>
                                <div>
                                    <label>Vehicle</label>
                                    <select className="ampl-input" value={vehicleId} onChange={(e) => setVehicleId(e.target.value)}>
                                        <option value="">-- None --</option>
                                        {vehicles.map((v) => (
                                            <option key={v.id} value={v.id}>{v.vehicle_number || v.name}</option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label>Equipment Model</label>
                                    <input type="text" className="ampl-input" value={equipmentModel} onChange={(e) => setEquipmentModel(e.target.value)} />
                                </div>
                            </div>

                            <div className="ampl-grid3 mt-10">
                                <div>
                                    <label>Date &amp; Time</label>
                                    <input type="datetime-local" className="ampl-input" value={assessedAt} onChange={(e) => setAssessedAt(e.target.value)} />
                                </div>
                                <div>
                                    <label>Evaluator</label>
                                    <input type="text" className="ampl-input" value={window.__EVALUATOR_NAME__} readOnly />
                                </div>
                                <div>
                                    <label>Location / Branch</label>
                                    <input type="text" className="ampl-input" value={ctx.branchName || '-'} readOnly />
                                </div>
                            </div>

                            <div className="mt-15">
                                <label>Operator Signature</label>
                                <SignaturePad ref={sigRef} onChange={(url) => setHasSignature(!!url)} />
                            </div>

                            <div className="mt-15">
                                <button type="button" className="ampl-btn ampl-btn-primary col-12" onClick={handleSave} disabled={busy || anyTitleOverLimit}>
                                    {busy ? 'Saving…' : 'Submit Assessment'}
                                </button>
                                {anyTitleOverLimit && (
                                    <span className="ampl-title-overlimit" style={{ marginLeft: 12 }}>
                                        Fix the over-limit title(s) above before saving.
                                    </span>
                                )}
                            </div>
                        </div>
                    )}

                    {closing && (
                        <div className="ampl-closing-overlay">
                            <div className="ampl-closing-box">
                                <div className="checkmark">&#10003;</div>
                                <div>Saved. Closing this window…</div>
                            </div>
                        </div>
                    )}
                </div>
            );
        }

        ReactDOM.createRoot(document.getElementById('assessment-popup-root')).render(<App />);
    </script>
    @endverbatim
@endpush