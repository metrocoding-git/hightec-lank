@extends('layouts.app')
@section('title')
    Certificate Templates
@endsection

@section('mainContent')
    @php
        $canCreate = Auth::user()->hasPermission('certificate-template.store');
        $canUpdate = Auth::user()->hasPermission('certificate-template.update');
        $canDisable = Auth::user()->hasPermission('certificate-template.destroy');
    @endphp

    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Certificate Templates</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Certificate Templates</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box">

                <div class="row align-items-center mb-15">
                    <div class="col-lg-6 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-0">Templates</h3>
                        </div>
                    </div>
                    @if ($canCreate)
                        <div class="col-lg-6 text-right">
                            <button type="button" id="ct-new-btn" class="primary-btn small fix-gr-bg">
                                <span class="fa-solid fa-plus pr-2"></span> New Template
                            </button>
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <x-table>
                        <table id="certificate-templates-table" class="table data-table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Linked Course(s)</th>
                                    <th>Course Name on Certificate</th>
                                    <th>Duration</th>
                                    <th>Serial Prefix</th>
                                    <th>Bottom Image</th>
                                    <th>QR Code</th>
                                    <th>Back Side</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($templates ?? collect()) as $i => $t)
                                    <tr data-id="{{ $t->id }}" data-name="{{ $t->name }}"
                                        data-course-ids="{{ $t->courses->pluck('id')->toJson() }}"
                                        data-line1="{{ $t->course_name_line1 }}"
                                        data-line2="{{ $t->course_name_line2 }}"
                                        data-duration="{{ $t->duration }}"
                                        data-serial-prefix="{{ $t->serialPrefix->prefix ?? '' }}"
                                        data-image-url="{{ $t->bottom_image_path ? asset('storage/' . $t->bottom_image_path) : '' }}"
                                        data-qr-image-url="{{ $t->qr_code_image_path ? asset('storage/' . $t->qr_code_image_path) : '' }}"
                                        data-back-side-pdf-url="{{ $t->back_side_pdf_path ? asset('storage/' . $t->back_side_pdf_path) : '' }}">
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $t->name }}</td>
                                        <td>{{ $t->courses->pluck('name')->implode(', ') ?: '—' }}</td>
                                        <td>{{ $t->course_name_line1 }}{{ $t->course_name_line2 ? ' / ' . $t->course_name_line2 : '' }}
                                        </td>
                                        <td>{{ $t->duration ?? '—' }}</td>
                                        <td>{{ $t->serialPrefix->prefix ?? '—' }}
                                            @if ($t->serialPrefix)
                                                <div class="ct-muted" style="font-size:11px;">Next:
                                                    <strong>{{ \App\Models\CertificateSerialPrefix::previewNext($t->serialPrefix->prefix) }}</strong></div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($t->bottom_image_path)
                                                <img src="{{ asset('storage/' . $t->bottom_image_path) }}" alt=""
                                                    style="max-height:36px;max-width:60px;object-fit:contain;">
                                            @else
                                                <span class="ct-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($t->qr_code_image_path)
                                                <img src="{{ asset('storage/' . $t->qr_code_image_path) }}" alt=""
                                                    style="max-height:36px;max-width:60px;object-fit:contain;">
                                            @else
                                                <span class="ct-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($t->back_side_pdf_path)
                                                <a href="{{ asset('storage/' . $t->back_side_pdf_path) }}" target="_blank"
                                                    rel="noopener" class="ct-yes">
                                                    <i class="fa-solid fa-file-pdf"></i> View
                                                </a>
                                            @else
                                                <span class="ct-muted">No</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            {{--
                                                BUG FIX — "why?" (Disable "undefined"?): this table
                                                uses responsive: true with 10 columns, so on anything
                                                but a wide screen DataTables Responsive collapses
                                                columns — including, often, this last Actions column —
                                                and MOVES their contents (the actual button elements)
                                                into a synthetic "child" row it generates below the
                                                real one. The buttons' click handlers used to do
                                                $(this).closest('tr') to read data-id/data-name/etc.,
                                                but Responsive's child row is a different <tr> that
                                                carries none of those attributes — only the ORIGINAL
                                                row above does — so that lookup came back `undefined`.
                                                Fix: put the same data-* attributes directly on each
                                                button too, and read them off $(this) in the JS below
                                                instead of walking up to .closest('tr'). Since
                                                Responsive relocates the real button node (not a copy),
                                                its own attributes are never lost no matter which row
                                                it currently lives in.
                                            --}}
                                            @if ($t->pending_certificates_count > 0)
                                                <span class="ct-muted small" title="Certificates from this template are waiting for signatures. It can be edited again once they are approved (or undone).">
                                                    <i class="fa-solid fa-lock"></i> Locked ({{ $t->pending_certificates_count }} pending)
                                                </span>
                                            @else
                                            @if ($canUpdate)
                                                <button type="button" class="btn btn-sm btn-outline-primary ct-edit-btn"
                                                    data-id="{{ $t->id }}" data-name="{{ $t->name }}"
                                                    data-course-ids="{{ $t->courses->pluck('id')->toJson() }}"
                                                    data-line1="{{ $t->course_name_line1 }}"
                                                    data-line2="{{ $t->course_name_line2 }}"
                                                    data-duration="{{ $t->duration }}"
                                                    data-serial-prefix="{{ $t->serialPrefix->prefix ?? '' }}"
                                                    data-image-url="{{ $t->bottom_image_path ? asset('storage/' . $t->bottom_image_path) : '' }}"
                                                    data-qr-image-url="{{ $t->qr_code_image_path ? asset('storage/' . $t->qr_code_image_path) : '' }}"
                                                    data-back-side-pdf-url="{{ $t->back_side_pdf_path ? asset('storage/' . $t->back_side_pdf_path) : '' }}">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                            @endif
                                            @if ($canDisable)
                                                <button type="button" class="btn btn-sm btn-outline-danger ct-disable-btn"
                                                    data-id="{{ $t->id }}" data-name="{{ $t->name }}">
                                                    <i class="fa-solid fa-ban"></i>
                                                </button>
                                            @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </x-table>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== Create / Edit modal (shared) ===================== --}}
    <div class="modal fade" id="ctFormModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ctFormModalTitle">New Certificate Template</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="ct-form-error" class="ampl-banner ampl-banner-error mb-15" style="display:none;"></div>

                    <div class="form-group">
                        <label>Template Name <span class="text-danger">*</span></label>
                        <input type="text" id="ct-name" class="form-control" maxlength="255"
                            placeholder="Internal label, e.g. Fork Lift Operator">
                    </div>

                    <div class="form-group">
                        <label>Linked Course(s) (optional)</label>
                        <select id="ct-course-ids" class="form-control" multiple size="6">
                            @foreach ($courseOptions ?? [] as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <small class="ct-muted">Hold Ctrl (Windows) / Cmd (Mac) to select more than one. A course can
                            only be linked to one active template at a time — saving will tell you which template to
                            remove it from first if there's a clash.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Course Name — Line 1 <span class="text-danger">*</span></label>
                            <input type="text" id="ct-line1" class="form-control" maxlength="255"
                                placeholder='e.g. "Heavy Vehicle Operator"'>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Course Name — Line 2 (optional)</label>
                            <input type="text" id="ct-line2" class="form-control" maxlength="255"
                                placeholder='e.g. "(Fork Lift Truck)"'>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Duration (optional)</label>
                            <input type="text" id="ct-duration" class="form-control" maxlength="255"
                                placeholder='e.g. "3 Months" or "120 Hours"'>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Serial Number Prefix (optional)</label>
                            <input type="text" id="ct-serial-prefix" class="form-control" maxlength="50"
                                list="ct-serial-prefix-options"
                                placeholder='e.g. "WL/OP/10" → WL/OP/10, WL/OP/11, WL/OP/12, ...'>
                            <datalist id="ct-serial-prefix-options">
                                @foreach ($serialPrefixOptions ?? [] as $p)
                                    <option value="{{ $p->prefix }}">
                                @endforeach
                            </datalist>
                            <small class="ct-muted">End it with the first certificate number — e.g. "WL/OP/10"
                                gives WL/OP/10 to the first certificate, then WL/OP/11, WL/OP/12, … (+1 each time).
                                Type an existing prefix to continue that series.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Bottom Image (bottom-right of the certificate)</label>
                        <input type="file" id="ct-bottom-image" class="form-control" accept="image/png,image/jpeg">
                        <div id="ct-current-image-wrap" class="mt-10" style="display:none;">
                            <small class="ct-muted">Current image:</small><br>
                            <img id="ct-current-image" src="" alt=""
                                style="max-height:60px;max-width:100px;object-fit:contain;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>QR Code Image (optional)</label>
                        <input type="file" id="ct-qr-image" class="form-control" accept="image/png,image/jpeg">
                        <div id="ct-current-qr-wrap" class="mt-10" style="display:none;">
                            <small class="ct-muted">Current image:</small><br>
                            <img id="ct-current-qr" src="" alt=""
                                style="max-height:60px;max-width:100px;object-fit:contain;">
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label>Back Side (optional)</label>
                        <input type="file" id="ct-back-side-pdf" class="form-control" accept="application/pdf">
                        <div id="ct-current-back-side-wrap" class="mt-10" style="display:none;">
                            <a id="ct-current-back-side-link" href="" target="_blank" rel="noopener">
                                <i class="fa-solid fa-file-pdf"></i> View current back side PDF
                            </a>
                        </div>
                        <small class="ct-muted">Uploaded as-is as a literal second page of the generated certificate
                            PDF. Leave blank for a single-page certificate.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="ct-save-btn" class="btn btn-primary">Save Template</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('components.partials.data_table_css')
@include('components.partials.data_table_js')

@push('script')
    <style>
        .ct-muted {
            color: #8a95a1;
            font-size: 12px;
        }

        .ct-yes {
            color: #1e7a43;
            font-weight: 700;
            font-size: 12px;
        }

        .ampl-banner {
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 14px;
        }

        .ampl-banner-error {
            background: #fdecea;
            color: #a12a2a;
            border: 1px solid #f5c6c6;
        }

        #certificate-templates-table thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }
    </style>

    <script>
        window.__CSRF_TOKEN__ = "{{ csrf_token() }}";
        window.__ROUTES__ = window.__ROUTES__ || {};
        window.__ROUTES__.certificateTemplateStore = "{{ route('certificate-template.store') }}";
        window.__ROUTES__.certificateTemplateUpdateTemplate =
            "{{ route('certificate-template.update', ['id' => '__ID__']) }}";
        window.__ROUTES__.certificateTemplateDestroyTemplate =
            "{{ route('certificate-template.destroy', ['id' => '__ID__']) }}";
    </script>

    @pushOnce('scripts')
        <script>
            $(function() {
                if (!$('#certificate-templates-table').length) {
                    return;
                }

                $('#certificate-templates-table').DataTable({
                    responsive: true,
                    dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 '" +
                        "<'dt-toolbar-title'>" +
                        "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-search'f>>" +
                        ">" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>>",
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    columnDefs: [{
                        orderable: false,
                        targets: -1
                    }],
                    order: [
                        [0, 'asc']
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

                var editingId = null;

                function resetForm() {
                    editingId = null;
                    $('#ctFormModalTitle').text('New Certificate Template');
                    $('#ct-name').val('');
                    $('#ct-course-ids').val([]);
                    $('#ct-line1').val('');
                    $('#ct-line2').val('');
                    $('#ct-duration').val('');
                    $('#ct-serial-prefix').val('');
                    $('#ct-bottom-image').val('');
                    $('#ct-current-image-wrap').hide();
                    $('#ct-current-image').attr('src', '');
                    $('#ct-qr-image').val('');
                    $('#ct-current-qr-wrap').hide();
                    $('#ct-current-qr').attr('src', '');
                    $('#ct-back-side-pdf').val('');
                    $('#ct-current-back-side-wrap').hide();
                    $('#ct-current-back-side-link').attr('href', '');
                    $('#ct-form-error').hide().text('');
                }

                $('#ct-new-btn').on('click', function() {
                    resetForm();
                    $('#ctFormModal').modal('show');
                });

                // BUG FIX (see the blade comment above the buttons for the
                // full "why?"): read id/name/etc. straight off the clicked
                // BUTTON ($(this)) instead of walking up to its <tr> —
                // DataTables Responsive can relocate this button into a
                // synthetic child row that doesn't carry the row's own
                // data-* attributes, which is what was producing
                // "undefined" values.
                $(document).on('click', '.ct-edit-btn', function() {
                    var $btn = $(this);
                    resetForm();
                    editingId = $btn.data('id');
                    $('#ctFormModalTitle').text('Edit Certificate Template');
                    $('#ct-name').val($btn.data('name'));

                    // jQuery auto-parses a data-course-ids="[1,2,3]" attribute
                    // into a real array; .val() on a multi-select needs the
                    // option values as strings to match correctly.
                    var courseIds = $btn.data('course-ids') || [];
                    $('#ct-course-ids').val(courseIds.map(String));

                    $('#ct-line1').val($btn.data('line1'));
                    $('#ct-line2').val($btn.data('line2'));
                    $('#ct-duration').val($btn.data('duration'));
                    $('#ct-serial-prefix').val($btn.data('serial-prefix'));

                    var imageUrl = $btn.data('image-url');
                    if (imageUrl) {
                        $('#ct-current-image').attr('src', imageUrl);
                        $('#ct-current-image-wrap').show();
                    }

                    var qrUrl = $btn.data('qr-image-url');
                    if (qrUrl) {
                        $('#ct-current-qr').attr('src', qrUrl);
                        $('#ct-current-qr-wrap').show();
                    }

                    var backSideUrl = $btn.data('back-side-pdf-url');
                    if (backSideUrl) {
                        $('#ct-current-back-side-link').attr('href', backSideUrl);
                        $('#ct-current-back-side-wrap').show();
                    }

                    $('#ctFormModal').modal('show');
                });

                $('#ct-save-btn').on('click', function() {
                    var name = $('#ct-name').val().trim();
                    var line1 = $('#ct-line1').val().trim();

                    if (!name || !line1) {
                        $('#ct-form-error').text('Template Name and Course Name — Line 1 are both required.')
                            .show();
                        return;
                    }

                    var formData = new FormData();
                    formData.append('name', name);
                    ($('#ct-course-ids').val() || []).forEach(function(id) {
                        formData.append('course_ids[]', id);
                    });
                    formData.append('course_name_line1', line1);
                    formData.append('course_name_line2', $('#ct-line2').val().trim());
                    formData.append('duration', $('#ct-duration').val().trim());
                    formData.append('serial_prefix', $('#ct-serial-prefix').val().trim());

                    var bottomImage = $('#ct-bottom-image')[0].files[0];
                    if (bottomImage) {
                        formData.append('bottom_image', bottomImage);
                    }

                    var qrImage = $('#ct-qr-image')[0].files[0];
                    if (qrImage) {
                        formData.append('qr_code_image', qrImage);
                    }

                    var backSidePdf = $('#ct-back-side-pdf')[0].files[0];
                    if (backSidePdf) {
                        formData.append('back_side_pdf', backSidePdf);
                    }

                    var url = editingId ?
                        window.__ROUTES__.certificateTemplateUpdateTemplate.replace('__ID__', editingId) :
                        window.__ROUTES__.certificateTemplateStore;

                    var $btn = $(this);
                    $btn.prop('disabled', true).text('Saving…');
                    $('#ct-form-error').hide().text('');

                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': window.__CSRF_TOKEN__,
                                'Accept': 'application/json',
                            },
                            body: formData,
                        }).then(function(res) {
                            return res.json().catch(function() {
                                throw new Error('Server returned a non-JSON response (HTTP ' + res
                                    .status + ').');
                            }).then(function(data) {
                                if (!res.ok) {
                                    var msg = data && data.message ? data.message : (
                                        'Request failed (HTTP ' + res.status + ').');
                                    if (res.status === 422 && data.errors) {
                                        msg = Object.values(data.errors).flat().join(' ') || msg;
                                    }
                                    throw new Error(msg);
                                }
                                return data;
                            });
                        })
                        .then(function() {
                            $('#ctFormModal').modal('hide');
                            window.location.reload();
                        })
                        .catch(function(err) {
                            $('#ct-form-error').text(err.message).show();
                        })
                        .finally(function() {
                            $btn.prop('disabled', false).text('Save Template');
                        });
                });

                // Same fix as .ct-edit-btn above — read straight off the
                // clicked button ($(this)) rather than $(this).closest('tr').
                $(document).on('click', '.ct-disable-btn', function() {
                    var $btn = $(this);
                    var id = $btn.data('id');
                    var name = $btn.data('name');

                    if (!confirm('Disable "' + name +
                            '"? This cannot be undone from here — it will stop appearing as a choice when generating new certificates and will disappear from this list, but certificates already generated with it are unaffected.'
                        )) {
                        return;
                    }

                    fetch(window.__ROUTES__.certificateTemplateDestroyTemplate.replace('__ID__', id), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': window.__CSRF_TOKEN__,
                                'Accept': 'application/json',
                            },
                        }).then(function(res) {
                            return res.json().then(function(data) {
                                if (!res.ok) {
                                    throw new Error(data && data.message ? data.message :
                                        'Request failed.');
                                }
                                return data;
                            });
                        })
                        .then(function() {
                            window.location.reload();
                        })
                        .catch(function(err) {
                            alert(err.message);
                        });
                });
            });
        </script>
    @endPushOnce
@endpush