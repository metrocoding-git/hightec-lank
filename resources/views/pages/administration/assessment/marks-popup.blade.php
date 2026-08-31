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
        body { background: #f4f6f8; }
        .ampl-card { background: #fff; border-radius: 10px; padding: 18px 20px; margin-bottom: 16px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .ampl-input { width: 100%; padding: 8px 10px; border: 1px solid #d7dde3; border-radius: 6px; font-size: 14px; background: #fff; }
        .ampl-input-sm { padding: 5px 8px; }
        .ampl-input:focus { outline: none; border-color: #1d4e89; }
        .ampl-input:disabled { background: #f4f6f8; color: #8a95a1; }
        label { font-weight: 600; font-size: 13px; color: #37474f; display: block; margin-bottom: 4px; }
        .ampl-btn { border: none; border-radius: 6px; padding: 9px 18px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .ampl-btn-primary { background: #1d4e89; color: #fff; }
        .ampl-btn-primary:disabled { opacity: .6; cursor: default; }
        .ampl-btn-outline { background: #fff; border: 1px solid #1d4e89; color: #1d4e89; margin-top: 8px; }
        .ampl-banner { padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
        .ampl-banner-error { background: #fdecea; color: #a12a2a; border: 1px solid #f5c6c6; }
        .ampl-banner-success { background: #e8f6ee; color: #1e7a43; border: 1px solid #bfe6cf; }
        .ampl-grid3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
        @media (max-width: 767px) { .ampl-grid3 { grid-template-columns: 1fr; } }
        .ampl-muted { color: #8a95a1; font-size: 13px; }
        .ampl-tablewrap { overflow-x: auto; }
        .ampl-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .ampl-table th, .ampl-table td { border: 1px solid #e1e6eb; padding: 8px; vertical-align: middle; }
        .ampl-table th { background: #f4f6f8; text-align: left; }
        .ampl-titlerow td { background: #eef3f9; }
        .ampl-yieldcell { text-align: center; font-weight: 700; background: #f9fbfc; }
        .ampl-pass { color: #1e7a43; font-weight: 700; }
        .ampl-fail { color: #a12a2a; font-weight: 700; }
        .ampl-context-ribbon {
            display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
            background: linear-gradient(90deg, #1d4e89, #2f6fb0); color: #fff;
            padding: 12px 18px; border-radius: 8px; margin-bottom: 16px; font-size: 13px;
        }
        .ampl-context-ribbon .chip { background: rgba(255,255,255,.16); padding: 4px 12px; border-radius: 20px; font-weight: 600; }
        .ampl-competency-toggle { display: flex; gap: 6px; }
        .ampl-competency-toggle button {
            flex: 1; border: 1px solid #d7dde3; background: #fff; padding: 6px 4px; border-radius: 6px;
            font-size: 12px; font-weight: 700; cursor: pointer; color: #6b7784; transition: all .12s ease;
        }
        .ampl-competency-toggle button.is-active-c { background: #e8f6ee; border-color: #1e7a43; color: #1e7a43; }
        .ampl-competency-toggle button.is-active-nc { background: #fdecea; border-color: #a12a2a; color: #a12a2a; }
        .ampl-competency-toggle button:disabled { opacity: .55; cursor: default; }
        .ampl-summary-bar {
            margin-top: 16px; padding: 16px 20px; border-radius: 8px; display: flex;
            justify-content: space-between; align-items: center; font-size: 17px; font-weight: 700;
        }
        .ampl-summary-bar.is-pass { background: #e8f6ee; color: #1e7a43; }
        .ampl-summary-bar.is-fail { background: #fdecea; color: #a12a2a; }
        .ampl-summary-bar .sub { font-size: 12px; font-weight: 500; opacity: .8; }
        .ampl-sig-preview {
            height: 180px; border: 1px solid #d7dde3; border-radius: 8px; background: #f8f9fa;
            display: flex; align-items: center; justify-content: center; cursor: pointer; text-align: center;
        }
        .ampl-sig-preview:hover { border-color: #1d4e89; }
        .ampl-sig-preview img { height: 150px; }
        .ampl-sig-canvas-wrap canvas { border: 1px solid #ccc; width: 100%; height: 350px; touch-action: none; }
        .ampl-signed-lock-note {
            background: #eef3f9; border: 1px solid #d7e2ee; color: #1d4e89; border-radius: 6px;
            padding: 8px 12px; font-size: 13px; margin-bottom: 10px;
        }
        .ampl-closing-overlay {
            position: fixed; inset: 0; background: rgba(20,26,32,.6); display: flex;
            align-items: center; justify-content: center; z-index: 1000;
        }
        .ampl-closing-box {
            background: #fff; border-radius: 10px; padding: 30px 40px; text-align: center; font-size: 16px;
        }
        .ampl-closing-box .checkmark { font-size: 40px; color: #1e7a43; margin-bottom: 10px; }
        .mt-10 { margin-top: 10px; }
        .mt-15 { margin-top: 15px; }
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

            const overallMax = template ? (template.titles || []).reduce((s, t) => s + Number(t.max_marks || 0), 0) : 0;
            const grandTotal = template ? (template.titles || []).reduce((s, t) => s + titleSum(t), 0) : 0;
            const qualified = grandTotal > 59;

            async function handleSave() {
                if (!template) return;

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

            return (
                <div>
                    <div className="ampl-context-ribbon">
                        <span className="chip">{ctx.studentName}</span>
                        <span className="chip">{ctx.admissionNumber}</span>
                        <span className="chip">{ctx.courseName}</span>
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
                                        {(template.titles || []).map((t) => (
                                            <React.Fragment key={t.id}>
                                                <tr className="ampl-titlerow">
                                                    <td colSpan="4">
                                                        <strong>{t.title}</strong>{' '}
                                                        <span className="ampl-muted">(Max {t.max_marks})</span>
                                                    </td>
                                                    <td rowSpan={(t.sub_titles || []).length + 1} className="ampl-yieldcell">
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
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            <div className={`ampl-summary-bar ${qualified ? 'is-pass' : 'is-fail'}`}>
                                <div>
                                    Total: {grandTotal} / {overallMax}
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
                                <label>Evaluator Signature</label>
                                <SignaturePad ref={sigRef} onChange={(url) => setHasSignature(!!url)} />
                            </div>

                            <div className="mt-15">
                                <button type="button" className="ampl-btn ampl-btn-primary" onClick={handleSave} disabled={busy}>
                                    {busy ? 'Saving…' : 'Submit Assessment'}
                                </button>
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
