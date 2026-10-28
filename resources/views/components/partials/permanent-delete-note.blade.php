{{--
    Permanent-delete warning for a section.
    Usage: @include('components.partials.permanent-delete-note', ['rule' => 'course'])
    Text comes from config/delete_rules.php.

    1) Shows a note on the page.
    2) Adds the warning inside every delete modal on the page.
    3) Asks for confirmation (alert) before any DELETE form is submitted.
--}}
@php
    $__r = config('delete_rules.' . ($rule ?? ''));
@endphp

@if ($__r)
    @php
        $__lines = array_merge($__r['also_deleted'] ?? [], $__r['affected'] ?? []);
        $__alert = 'PERMANENT DELETE — ' . $__r['name'] . "\n\n" . $__r['action'] . ' This cannot be undone.';
        if (!empty($__r['also_deleted'])) {
            $__alert .= "\n\nAlso deleted:\n- " . implode("\n- ", $__r['also_deleted']);
        }
        if (!empty($__r['affected'])) {
            $__alert .= "\n\nAffected:\n- " . implode("\n- ", $__r['affected']);
        }
        $__alert .= "\n\nSafe only when: " . $__r['safe_when'];
    @endphp

    <div class="pdn-note" role="alert">
        <div class="pdn-head">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <strong>Delete on this page is permanent.</strong>
            <span>{{ $__r['action'] }} It cannot be undone.</span>
            <button type="button" class="pdn-toggle" aria-expanded="false">Details</button>
        </div>
        <div class="pdn-body" hidden>
            @if (!empty($__r['also_deleted']))
                <div class="pdn-label">Also deleted with it</div>
                <ul>
                    @foreach ($__r['also_deleted'] as $l)
                        <li>{{ $l }}</li>
                    @endforeach
                </ul>
            @endif
            @if (!empty($__r['affected']))
                <div class="pdn-label">Affected</div>
                <ul>
                    @foreach ($__r['affected'] as $l)
                        <li>{{ $l }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="pdn-grid">
                <div><span class="pdn-ok">Safe when</span> {{ $__r['safe_when'] }}</div>
                <div><span class="pdn-bad">Risky when</span> {{ $__r['risky_when'] }}</div>
                <div><span class="pdn-guard">Current protection</span> {{ $__r['guard'] }}</div>
            </div>
        </div>
    </div>

    @once
        @push('css')
            <style>
                .pdn-note { border: 1px solid #f5c6c6; border-left: 4px solid #d93025; background: #fff6f5; border-radius: 10px; padding: 10px 14px; margin-bottom: 16px; color: #5a2020; font-size: 13px; }
                .pdn-head { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
                .pdn-head i { color: #d93025; }
                .pdn-toggle { margin-left: auto; border: 1px solid #f0b4b4; background: #fff; color: #a12a2a; border-radius: 999px; padding: 2px 12px; font-size: 12px; font-weight: 600; cursor: pointer; }
                .pdn-body { margin-top: 8px; }
                .pdn-body ul { margin: 2px 0 8px 18px; padding: 0; }
                .pdn-label { font-weight: 700; margin-top: 4px; }
                .pdn-grid { display: grid; gap: 4px; }
                .pdn-ok, .pdn-bad, .pdn-guard { display: inline-block; min-width: 128px; font-weight: 700; }
                .pdn-ok { color: #1e7a43; } .pdn-bad { color: #a12a2a; } .pdn-guard { color: #565d6d; }
                .pdn-modal-warn { border: 1px solid #f5c6c6; background: #fff6f5; color: #a12a2a; border-radius: 8px; padding: 8px 10px; margin: 0 0 12px; font-size: 13px; text-align: left; }
            </style>
        @endpush
    @endonce

    @push('script')
        <script>
            (function() {
                var alertText = @json($__alert . "\n\nDelete anyway?");
                var shortText = @json('Permanent delete — cannot be undone. ' . (!empty($__r['also_deleted']) ? 'Also deletes: ' . implode('; ', $__r['also_deleted']) . '.' : (!empty($__r['affected']) ? $__r['affected'][0] . '.' : '')));

                document.querySelectorAll('.pdn-toggle').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        var body = btn.closest('.pdn-note').querySelector('.pdn-body');
                        body.hidden = !body.hidden;
                        btn.setAttribute('aria-expanded', String(!body.hidden));
                        btn.textContent = body.hidden ? 'Details' : 'Hide';
                    });
                });

                // form.elements (not querySelector): delete forms placed inside
                // tables get their fields attached by the browser without
                // being children of the <form>.
                function methodField(form) {
                    var m = form.elements ? form.elements.namedItem('_method') : null;
                    if (m && m.length && !m.value) m = m[0];
                    return m || form.querySelector('input[name="_method"]');
                }

                function isDeleteForm(form) {
                    var m = methodField(form);
                    return !!m && String(m.value).toUpperCase() === 'DELETE';
                }

                // Warning inside each delete modal.
                document.querySelectorAll('form').forEach(function(form) {
                    if (!isDeleteForm(form)) return;
                    var modal = form.closest('.modal') || methodField(form).closest('.modal');
                    var body = modal ? modal.querySelector('.modal-body') : null;
                    if (body && !body.querySelector('.pdn-modal-warn')) {
                        var div = document.createElement('div');
                        div.className = 'pdn-modal-warn';
                        div.textContent = shortText;
                        body.insertBefore(div, body.firstChild);
                    }
                });

                // Alert before submit — capture phase, so it runs before any
                // jQuery/AJAX submit handler on the page.
                document.addEventListener('submit', function(e) {
                    var form = e.target;
                    if (!form || form.tagName !== 'FORM' || !isDeleteForm(form)) return;
                    if (form.dataset.pdnConfirmed === '1') return;
                    if (!window.confirm(alertText)) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return;
                    }
                    form.dataset.pdnConfirmed = '1';
                    setTimeout(function() { form.dataset.pdnConfirmed = ''; }, 1500);
                }, true);

                // Used by pages that delete by AJAX (their own confirm shows this text).
                window.__permanentDeleteAlert = alertText;
                window.__permanentDeleteInfo = @json($__alert);
            })();
        </script>
    @endpush
@endif
