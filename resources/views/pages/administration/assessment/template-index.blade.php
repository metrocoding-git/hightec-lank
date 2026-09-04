@extends('layouts.app')
@section('title')
    Final Assessment Templates
@endsection

@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Final Assessment Templates</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Final Assessment Templates</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div id="assessment-template-root"></div>
        </div>
    </section>
@endsection

@push('script')
    <style>
        .atpl {
            font-size: 13.5px;
        }

        .atpl-card {
            background: #fff;
            border: 1px solid #e5e9f0;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 16px;
        }

        .atpl-card h4 {
            margin: 0 0 14px;
            font-size: 15px;
            font-weight: 600;
        }

        .atpl-label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #667;
            margin-bottom: 6px;
        }

        .atpl-select,
        .atpl-input {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #d8dfe8;
            border-radius: 6px;
            font-size: 13.5px;
            background: #fff;
        }

        .atpl-input-narrow {
            max-width: 140px;
        }

        .atpl-banner {
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .atpl-banner-success {
            background: #e8f7ee;
            color: #1f8a4c;
            border: 1px solid #bfe8cf;
        }

        .atpl-banner-error {
            background: #fbeaea;
            color: #b93a3a;
            border: 1px solid #f3c3c3;
        }

        .atpl-grid {
            display: grid;
            grid-template-columns: minmax(280px, 400px) 1fr;
            gap: 16px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .atpl-grid {
                grid-template-columns: 1fr;
            }
        }

        .atpl-muted {
            color: #9aa4b2;
            font-size: 13px;
        }

        .atpl-list {
            list-style: none;
            margin: 0 0 14px;
            padding: 0;
        }

        .atpl-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #eef1f6;
        }

        .atpl-list-actions {
            display: flex;
            gap: 6px;
            flex-shrink: 0;
        }

        .atpl-btn {
            padding: 8px 14px;
            font-size: 12.5px;
            border-radius: 6px;
            border: 1px solid #d8dfe8;
            background: #fff;
            color: #333;
            cursor: pointer;
        }

        .atpl-btn-sm {
            padding: 5px 10px;
            font-size: 11.5px;
        }

        .atpl-btn-icon {
            padding: 4px 10px;
            font-size: 14px;
            line-height: 1;
            color: #b93a3a;
            border-color: #f3c3c3;
        }

        .atpl-btn-primary {
            background: #1d4e89;
            border-color: #1d4e89;
            color: #fff;
        }

        .atpl-btn-outline {
            background: #fff;
            color: #1d4e89;
            border-color: #1d4e89;
        }

        .atpl-btn-danger {
            background: #fff;
            color: #b93a3a;
            border-color: #e9b7b7;
        }

        .atpl-field {
            margin-bottom: 12px;
        }

        .atpl-field label {
            display: block;
            font-size: 12px;
            color: #556;
            margin-bottom: 4px;
        }

        .atpl-title-block {
            border: 1px solid #e5e9f0;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 14px;
            background: #fafbfd;
        }

        .atpl-title-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .atpl-sub-row {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
            align-items: center;
        }

        .atpl-sub-row .atpl-input {
            flex: 1;
        }

        .atpl-title-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
        }

        .atpl-copied-banner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            background: #eef3fb;
            border: 1px solid #c8d9f0;
            color: #1d4e89;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .atpl-copied-banner .atpl-copied-actions {
            display: flex;
            gap: 8px;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/18.3.1/umd/react.production.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/18.3.1/umd/react-dom.production.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/7.24.7/babel.min.js"></script>

    <script>
        window.__COURSES__ = @json($courses);
        window.__CSRF_TOKEN__ = "{{ csrf_token() }}";
        window.__ROUTES__ = {
            courseData: "{{ url('administration/academic/assessment-template/course') }}",
            store: "{{ route('assessment-template.store') }}",
            update: "{{ url('administration/academic/assessment-template') }}",
            destroy: "{{ url('administration/academic/assessment-template') }}",
        };
    </script>

    @verbatim
        <script type="text/babel" data-presets="react">
            async function apiFetch(url, options = {}) {
                const res = await fetch(url, {
                    ...options,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.__CSRF_TOKEN__,
                        ...(options.headers || {}),
                    },
                });

                let data = null;
                try {
                    data = await res.json();
                } catch (e) {
                    throw new Error(`Server returned a non-JSON response (HTTP ${res.status}) — check the Laravel log, this usually means a PHP error.`);
                }

                if (!res.ok) {
                    const msg = data?.message || (data?.errors ? Object.values(data.errors).flat().join(' ') : `Request failed (HTTP ${res.status})`);
                    throw new Error(msg);
                }

                return data;
            }

            function blankSub() {
                return { id: null, subTitle: '' };
            }
            function blankTitle() {
                return { id: null, title: '', maxMarks: '', subTitles: [blankSub()] };
            }
            function blankBuilder(courseId, courseName) {
                return { id: null, courseId, moduleName: courseName || '', ncsCode: '', titles: [blankTitle()] };
            }

            function App() {
                const [courses] = React.useState(window.__COURSES__ || []);
                const [courseId, setCourseId] = React.useState('');
                const [templates, setTemplates] = React.useState([]);
                const [loadingList, setLoadingList] = React.useState(false);
                const [banner, setBanner] = React.useState(null);
                const [builder, setBuilder] = React.useState(null);
                // "want to have copy button for coppy template, then want
                // past button for tamplete past" — Copy holds a template's
                // content (title/sub-title text + planned marks, IDs
                // stripped so pasting always creates something NEW rather
                // than editing the original) so it can be pasted into the
                // builder for whichever course is currently selected,
                // whether that's a different course entirely or the same
                // one after a delete.
                const [copied, setCopied] = React.useState(null);

                const loadTemplates = React.useCallback(async (cid) => {
                    setLoadingList(true);
                    try {
                        const data = await apiFetch(`${window.__ROUTES__.courseData}/${cid}/data`);
                        setTemplates(data.templates || []);
                    } catch (err) {
                        setBanner({ type: 'error', text: err.message });
                        setTemplates([]);
                    } finally {
                        setLoadingList(false);
                    }
                }, []);

                React.useEffect(() => {
                    if (!courseId) {
                        setTemplates([]);
                        setBuilder(null);
                        return;
                    }
                    const course = courses.find(c => String(c.id) === String(courseId));
                    setBuilder(blankBuilder(courseId, course ? course.name : ''));
                    setBanner(null);
                    loadTemplates(courseId);
                }, [courseId]);

                function startNew() {
                    const course = courses.find(c => String(c.id) === String(courseId));
                    setBuilder(blankBuilder(courseId, course ? course.name : ''));
                }

                function startEdit(tpl) {
                    setBuilder({
                        id: tpl.id,
                        courseId: tpl.course_id,
                        moduleName: tpl.module_name,
                        ncsCode: tpl.ncs_code || '',
                        titles: (tpl.titles || []).map(t => ({
                            id: t.id,
                            title: t.title,
                            maxMarks: t.max_marks,
                            subTitles: (t.sub_titles || []).map(s => ({ id: s.id, subTitle: s.sub_title })),
                        })),
                    });
                    setBanner(null);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                function handleCopy(tpl) {
                    setCopied({
                        moduleName: tpl.module_name,
                        ncsCode: tpl.ncs_code || '',
                        titles: (tpl.titles || []).map(t => ({
                            title: t.title,
                            maxMarks: t.max_marks,
                            subTitles: (t.sub_titles || []).map(s => ({ subTitle: s.sub_title })),
                        })),
                    });
                    setBanner({ type: 'success', text: `Copied "${tpl.module_name}". Select a course (any course) and click "Paste" to use it there.` });
                }

                function handlePaste() {
                    if (!copied || !courseId) return;
                    setBuilder({
                        id: null,
                        courseId,
                        moduleName: copied.moduleName,
                        ncsCode: copied.ncsCode,
                        titles: copied.titles.map(t => ({
                            id: null,
                            title: t.title,
                            maxMarks: t.maxMarks,
                            subTitles: t.subTitles.map(s => ({ id: null, subTitle: s.subTitle })),
                        })),
                    });
                    setBanner(null);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                async function handleDelete(tpl) {
                    if (!window.confirm(`Delete "${tpl.module_name}"? This only hides it going forward — any assessments already saved against it are kept.`)) return;
                    try {
                        await apiFetch(`${window.__ROUTES__.destroy}/${tpl.id}`, { method: 'DELETE' });
                        setBanner({ type: 'success', text: 'Template deleted.' });
                        if (builder && builder.id === tpl.id) startNew();
                        loadTemplates(courseId);
                    } catch (err) {
                        setBanner({ type: 'error', text: err.message });
                    }
                }

                function updateBuilder(patch) {
                    setBuilder(b => ({ ...b, ...patch }));
                }
                function updateTitle(idx, patch) {
                    setBuilder(b => {
                        const titles = b.titles.slice();
                        titles[idx] = { ...titles[idx], ...patch };
                        return { ...b, titles };
                    });
                }
                function updateSub(tIdx, sIdx, patch) {
                    setBuilder(b => {
                        const titles = b.titles.slice();
                        const subTitles = titles[tIdx].subTitles.slice();
                        subTitles[sIdx] = { ...subTitles[sIdx], ...patch };
                        titles[tIdx] = { ...titles[tIdx], subTitles };
                        return { ...b, titles };
                    });
                }
                function addTitle() {
                    setBuilder(b => ({ ...b, titles: [...b.titles, blankTitle()] }));
                }
                function removeTitle(idx) {
                    setBuilder(b => ({ ...b, titles: b.titles.filter((_, i) => i !== idx) }));
                }
                function addSub(tIdx) {
                    setBuilder(b => {
                        const titles = b.titles.slice();
                        titles[tIdx] = { ...titles[tIdx], subTitles: [...titles[tIdx].subTitles, blankSub()] };
                        return { ...b, titles };
                    });
                }
                function removeSub(tIdx, sIdx) {
                    setBuilder(b => {
                        const titles = b.titles.slice();
                        titles[tIdx] = { ...titles[tIdx], subTitles: titles[tIdx].subTitles.filter((_, i) => i !== sIdx) };
                        return { ...b, titles };
                    });
                }

                async function handleSave(e) {
                    e.preventDefault();
                    setBanner(null);

                    const cleanTitles = builder.titles
                        .map(t => ({
                            id: t.id,
                            title: (t.title || '').trim(),
                            max_marks: Number(t.maxMarks) || 0,
                            sub_titles: t.subTitles
                                .map(s => ({ id: s.id, sub_title: (s.subTitle || '').trim() }))
                                .filter(s => s.sub_title),
                        }))
                        .filter(t => t.title && t.sub_titles.length);

                    if (!builder.moduleName.trim()) {
                        setBanner({ type: 'error', text: 'Module Name is required.' });
                        return;
                    }
                    if (!cleanTitles.length) {
                        setBanner({ type: 'error', text: 'Add at least one Main Title with at least one Sub Title.' });
                        return;
                    }

                    const payload = {
                        course_id: builder.courseId,
                        module_name: builder.moduleName.trim(),
                        ncs_code: builder.ncsCode.trim(),
                        titles: cleanTitles,
                    };

                    const url = builder.id
                        ? `${window.__ROUTES__.update}/${builder.id}/update`
                        : window.__ROUTES__.store;

                    try {
                        const data = await apiFetch(url, { method: 'POST', body: JSON.stringify(payload) });
                        setBanner({ type: 'success', text: data.message || 'Saved.' });
                        await loadTemplates(courseId);
                        startNew();
                    } catch (err) {
                        setBanner({ type: 'error', text: err.message });
                    }
                }

                return (
                    <div className="atpl">
                        <div className="atpl-card">
                            <label className="atpl-label">Select Course</label>
                            <select className="atpl-select" value={courseId} onChange={e => setCourseId(e.target.value)}>
                                <option value="">-- Select Course --</option>
                                {courses.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                            </select>
                        </div>

                        {copied && (
                            <div className="atpl-copied-banner">
                                <div>Copied template: <strong>{copied.moduleName}</strong></div>
                                <div className="atpl-copied-actions">
                                    {courseId && (
                                        <button type="button" className="atpl-btn atpl-btn-primary atpl-btn-sm" onClick={handlePaste}>
                                            Paste into this course
                                        </button>
                                    )}
                                    <button type="button" className="atpl-btn atpl-btn-sm" onClick={() => setCopied(null)}>Clear</button>
                                </div>
                            </div>
                        )}

                        {banner && <div className={`atpl-banner atpl-banner-${banner.type}`}>{banner.text}</div>}

                        {courseId && (
                            <div className="atpl-grid">
                                <div className="atpl-card">
                                    <h4>Existing Templates</h4>
                                    {loadingList && <p className="atpl-muted">Loading…</p>}
                                    {!loadingList && templates.length === 0 && (
                                        <p className="atpl-muted">No templates yet for this course.</p>
                                    )}
                                    <ul className="atpl-list">
                                        {templates.map(t => (
                                            <li key={t.id} className="atpl-list-item">
                                                <div>
                                                    <strong>{t.module_name}</strong>
                                                    <div className="atpl-muted">NCS: {t.ncs_code || '-'}</div>
                                                </div>
                                                <div className="atpl-list-actions">
                                                    <button type="button" className="atpl-btn atpl-btn-outline" onClick={() => startEdit(t)}>Edit</button>
                                                    <button type="button" className="atpl-btn" onClick={() => handleCopy(t)}>Copy</button>
                                                    <button type="button" className="atpl-btn atpl-btn-danger" onClick={() => handleDelete(t)}>Delete</button>
                                                </div>
                                            </li>
                                        ))}
                                    </ul>
                                    <button type="button" className="atpl-btn atpl-btn-primary" onClick={startNew}>+ New Template</button>
                                </div>

                                {builder && (
                                    <div className="atpl-card">
                                        <h4>{builder.id ? `Edit Template — ${builder.moduleName}` : 'New Template'}</h4>
                                        <form onSubmit={handleSave}>
                                            <div className="atpl-field">
                                                <label>Module Name</label>
                                                <input className="atpl-input" value={builder.moduleName}
                                                    onChange={e => updateBuilder({ moduleName: e.target.value })} required />
                                            </div>
                                            <div className="atpl-field">
                                                <label>NCS Code</label>
                                                <input className="atpl-input" value={builder.ncsCode}
                                                    onChange={e => updateBuilder({ ncsCode: e.target.value })} />
                                            </div>

                                            <hr />

                                            {builder.titles.map((t, tIdx) => (
                                                <div key={tIdx} className="atpl-title-block">
                                                    <div className="atpl-title-row">
                                                        <input className="atpl-input" placeholder="Main Title, e.g. Maintenance & Related Knowledge"
                                                            value={t.title} onChange={e => updateTitle(tIdx, { title: e.target.value })} required />
                                                        <input className="atpl-input atpl-input-narrow" type="number" min="0" placeholder="Max Marks"
                                                            value={t.maxMarks} onChange={e => updateTitle(tIdx, { maxMarks: e.target.value })} required />
                                                    </div>

                                                    {t.subTitles.map((s, sIdx) => (
                                                        <div key={sIdx} className="atpl-sub-row">
                                                            <input className="atpl-input" placeholder="Sub Title, e.g. -Needed Lubricants, Oil, Fluid, Grease"
                                                                value={s.subTitle} onChange={e => updateSub(tIdx, sIdx, { subTitle: e.target.value })} required />
                                                            <button type="button" className="atpl-btn atpl-btn-icon" onClick={() => removeSub(tIdx, sIdx)}>&times;</button>
                                                        </div>
                                                    ))}

                                                    <div className="atpl-title-actions">
                                                        <button type="button" className="atpl-btn atpl-btn-outline atpl-btn-sm" onClick={() => addSub(tIdx)}>+ Add Sub Title</button>
                                                        <button type="button" className="atpl-btn atpl-btn-danger atpl-btn-sm" onClick={() => removeTitle(tIdx)}>Remove Title</button>
                                                    </div>
                                                </div>
                                            ))}

                                            <button type="button" className="atpl-btn atpl-btn-outline" onClick={addTitle}>+ Add Main Title</button>

                                            <hr />
                                            <button type="submit" className="atpl-btn atpl-btn-primary">
                                                {builder.id ? 'Update Template' : 'Save Template'}
                                            </button>
                                        </form>
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                );
            }

            ReactDOM.createRoot(document.getElementById('assessment-template-root')).render(<App />);
        </script>
    @endverbatim
@endpush