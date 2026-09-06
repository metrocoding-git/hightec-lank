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
                    <a href="{{ route('certificate.index') }}">Certificate Management</a>
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
                                    <th>Linked Course</th>
                                    <th>Course Name on Certificate</th>
                                    <th>Bottom Image</th>
                                    <th>Back Side</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($templates ?? collect()) as $i => $t)
                                    <tr data-id="{{ $t->id }}" data-name="{{ $t->name }}"
                                        data-course-id="{{ $t->course_id }}" data-line1="{{ $t->course_name_line1 }}"
                                        data-line2="{{ $t->course_name_line2 }}"
                                        data-back-side="{{ $t->back_side_content }}"
                                        data-image-url="{{ $t->bottom_image_path ? asset('storage/' . $t->bottom_image_path) : '' }}">
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $t->name }}</td>
                                        <td>{{ $t->course->name ?? '—' }}</td>
                                        <td>{{ $t->course_name_line1 }}{{ $t->course_name_line2 ? ' / ' . $t->course_name_line2 : '' }}
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
                                            @if ($t->back_side_content)
                                                <span class="ct-yes">Yes</span>
                                            @else
                                                <span class="ct-muted">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($t->is_active)
                                                <span class="ct-badge ct-badge-active">Active</span>
                                            @else
                                                <span class="ct-badge ct-badge-disabled">Disabled</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            @if ($canUpdate && $t->is_active)
                                                <button type="button" class="btn btn-sm btn-outline-primary ct-edit-btn">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                            @endif
                                            @if ($canDisable && $t->is_active)
                                                <button type="button" class="btn btn-sm btn-outline-danger ct-disable-btn">
                                                    <i class="fa-solid fa-ban"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
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
                        <label>Linked Course (optional)</label>
                        <select id="ct-course-id" class="primary_select form-control">
                            <option value="">— None —</option>
                            @foreach ($courseOptions ?? [] as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <small class="ct-muted">Just a convenience link for your own reference — any template can
                            still be picked for any certificate at generate time.</small>
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

                    <div class="form-group">
                        <label>Bottom Image (bottom-right of the certificate)</label>
                        <input type="file" id="ct-bottom-image" class="form-control" accept="image/png,image/jpeg">
                        <div id="ct-current-image-wrap" class="mt-10" style="display:none;">
                            <small class="ct-muted">Current image:</small><br>
                            <img id="ct-current-image" src="" alt=""
                                style="max-height:60px;max-width:100px;object-fit:contain;">
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label>Back Side Content (optional)</label>
                        <textarea id="ct-back-side" class="form-control" rows="5"
                            placeholder="Printed as a literal second page of the generated certificate PDF. Leave blank for a single-page certificate."></textarea>
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

        .ct-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .ct-badge-active {
            background: #e8f6ee;
            color: #1e7a43;
        }

        .ct-badge-disabled {
            background: #f4f6f8;
            color: #8a95a1;
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
                    $('#ct-course-id').val('').trigger('change');
                    $('#ct-line1').val('');
                    $('#ct-line2').val('');
                    $('#ct-bottom-image').val('');
                    $('#ct-current-image-wrap').hide();
                    $('#ct-current-image').attr('src', '');
                    $('#ct-back-side').val('');
                    $('#ct-form-error').hide().text('');
                }

                $('#ct-new-btn').on('click', function() {
                    // resetForm();
                    $('#ctFormModal').modal('show');
                });

                $(document).on('click', '.ct-edit-btn', function() {
                    var $tr = $(this).closest('tr');
                    resetForm();
                    editingId = $tr.data('id');
                    $('#ctFormModalTitle').text('Edit Certificate Template');
                    $('#ct-name').val($tr.data('name'));
                    $('#ct-course-id').val($tr.data('course-id') || '').trigger('change');
                    $('#ct-line1').val($tr.data('line1'));
                    $('#ct-line2').val($tr.data('line2'));
                    $('#ct-back-side').val($tr.data('back-side'));
                    var imageUrl = $tr.data('image-url');
                    if (imageUrl) {
                        $('#ct-current-image').attr('src', imageUrl);
                        $('#ct-current-image-wrap').show();
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
                    formData.append('course_id', $('#ct-course-id').val() || '');
                    formData.append('course_name_line1', line1);
                    formData.append('course_name_line2', $('#ct-line2').val().trim());
                    formData.append('back_side_content', $('#ct-back-side').val());
                    var file = $('#ct-bottom-image')[0].files[0];
                    if (file) {
                        formData.append('bottom_image', file);
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

                $(document).on('click', '.ct-disable-btn', function() {
                    var $tr = $(this).closest('tr');
                    var id = $tr.data('id');
                    var name = $tr.data('name');

                    if (!confirm('Disable "' + name +
                            '"? This cannot be undone from here — it will stop appearing as a choice when generating new certificates, but certificates already generated with it are unaffected.'
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
