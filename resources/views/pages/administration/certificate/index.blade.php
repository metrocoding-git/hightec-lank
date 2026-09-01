@extends('layouts.app')
@section('title')
    Certificate Management
@endsection

@section('mainContent')
    @php
        $genarateCertificate = Auth::user()->hasPermission('certificate.generate');
        $signConductor = Auth::user()->hasPermission('certificate.sign-conductor');
        $signOwner = Auth::user()->hasPermission('certificate.sign-owner');
        // $download = Auth::user()->hasPermission('student-list.search');
    @endphp

    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Certificate Management</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Certificate Management</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box filter_card mb-10">

                <form method="GET" action="{{ route('certificate.index') }}" class="row align-items-end mb-0">
                    <div class="col-lg-4 mb-2">
                        <select name="course_id" class="primary_select form-control ajax_selectl">
                            <option value="">All Courses</option>
                            @foreach ($courseOptions ?? [] as $c)
                                <option value="{{ $c->id }}"
                                    {{ (string) request('course_id') === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 mb-2">
                        <select name="cert_status" class="primary_select form-control ajax_selectl">
                            <option value="">All Stages</option>
                            <option value="not_generated"
                                {{ request('cert_status') === 'not_generated' ? 'selected' : '' }}>Not Generated Yet
                            </option>
                            <option value="pending_conductor"
                                {{ request('cert_status') === 'pending_conductor' ? 'selected' : '' }}>Awaiting Conductor
                                Signature</option>
                            <option value="pending_owner"
                                {{ request('cert_status') === 'pending_owner' ? 'selected' : '' }}>Awaiting Owner Signature
                            </option>
                            <option value="approved" {{ request('cert_status') === 'approved' ? 'selected' : '' }}>Fully
                                Signed / Approved</option>
                        </select>
                    </div>
                    <div class="col-lg-4 mb-2">
                        <select name="downloaded" class="primary_select form-control ajax_selectl">
                            <option value="">All Download or Not</option>
                            <option value="no" {{ request('downloaded') === 'no' ? 'selected' : '' }}>Not Downloaded Yet
                            </option>
                            <option value="yes" {{ request('downloaded') === 'yes' ? 'selected' : '' }}>Already
                                Downloaded</option>
                        </select>
                    </div>
                    <div class="col-lg-12">
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
                                    <a href="{{ route('certificate.index') }}" class="text-white">Reset</a>
                                </button>

                            </div>

                        </div>

                    </div>

                </form>
            </div>
            <div class="white-box mb-20">
                <div class="col-12">
                    <div class="cert-bulkbar row">
                        @if ($genarateCertificate)
                            <button type="button" id="cert-generate-btn" class="ampl-btn ampl-btn-outline col-3">
                                <i class="fa-solid fa-file-circle-plus"></i> Generate Certificate(s)
                            </button>
                        @endif

                        @if ($signConductor)
                            <button type="button" id="cert-sign-conductor-btn" class="ampl-btn ampl-btn-outline col-3">
                                <i class="fa-solid fa-signature"></i> Sign — Assessment Conductor
                            </button>
                        @endif

                        @if ($signOwner)
                            <button type="button" id="cert-sign-owner-btn" class="ampl-btn ampl-btn-outline col-3">
                                <i class="fa-solid fa-signature"></i> Sign — Owner
                            </button>
                        @endif

                        <button type="button" id="cert-download-btn" class="ampl-btn ampl-btn-outline col-3">
                            <i class="fa-solid fa-download"></i> Download Certificate(s)
                        </button>

                    </div>
                </div>

            </div>

            <div class="white-box">

                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-15">Final Assessment Certificates</h3>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <x-table>
                        <table id="certificates-table" class="table data-table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center select-checkbox">
                                        <label class="custom-checkbox mb-0">
                                            <input type="checkbox" id="cert-select-all">
                                            <span class="custom-checkbox-indicator"></span>
                                        </label>
                                    </th>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Reg No</th>
                                    <th>Course</th>
                                    <th>Module</th>
                                    <th>Certificate #</th>
                                    <th>Stage</th>
                                    <th>Conductor Signed</th>
                                    <th>Owner Signed</th>
                                    <th>Downloaded</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($assessments ?? collect()) as $i => $a)
                                    @php $cert = $a->certificate; @endphp
                                    <tr data-assessment-id="{{ $a->id }}" data-cert-id="{{ optional($cert)->id }}"
                                        data-cert-status="{{ optional($cert)->status }}"
                                        data-downloaded="{{ $cert && $cert->downloaded_at ? 1 : 0 }}">
                                        <td class="text-center select-checkbox">
                                            <label class="custom-checkbox mb-0">
                                                <input type="checkbox" class="cert-select-checkbox"
                                                    value="{{ $a->id }}">
                                                <span class="custom-checkbox-indicator"></span>
                                            </label>
                                        </td>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $a->student->student_name ?? '-' }}</td>
                                        <td>{{ $a->student->admission_number ?? '-' }}</td>
                                        <td>{{ $a->template->course->name ?? '-' }}</td>
                                        <td>{{ $a->template->module_name ?? '-' }}</td>
                                        <td>{{ $cert->certificate_number ?? '-' }}</td>
                                        <td>
                                            @if (!$cert)
                                                <span class="cert-badge cert-badge-none">Not generated</span>
                                            @elseif ($cert->status === 'generated')
                                                <span class="cert-badge cert-badge-pending">Awaiting Conductor</span>
                                            @elseif ($cert->status === 'conductor_signed')
                                                <span class="cert-badge cert-badge-pending">Awaiting Owner</span>
                                            @else
                                                <span class="cert-badge cert-badge-approved">Approved</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($cert && $cert->conductor_signed_at)
                                                <span class="cert-yes">Yes</span>
                                                <div class="ampl-muted" style="font-size:11px;">
                                                    {{ optional($cert->conductorSignedBy)->name ?? '' }} ·
                                                    {{ $cert->conductor_signed_at->format('Y-m-d') }}</div>
                                            @else
                                                <span class="cert-no">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($cert && $cert->owner_signed_at)
                                                <span class="cert-yes">Yes</span>
                                                <div class="ampl-muted" style="font-size:11px;">
                                                    {{ optional($cert->ownerSignedBy)->name ?? '' }} ·
                                                    {{ $cert->owner_signed_at->format('Y-m-d') }}</div>
                                            @else
                                                <span class="cert-no">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($cert && $cert->downloaded_at)
                                                <span class="cert-yes">Yes ({{ $cert->download_count }}×)</span>
                                                <div class="ampl-muted" style="font-size:11px;">
                                                    {{ $cert->downloaded_at->format('Y-m-d') }}</div>
                                            @else
                                                <span class="cert-no">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($cert && $cert->status === 'approved')
                                                <a href="{{ route('certificate.download', ['id' => $cert->id]) }}"
                                                    class="btn btn-sm btn-outline-success" target="_blank"
                                                    title="Download certificate">
                                                    <i class="fa-solid fa-download"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">No PASSed assessments found for these
                                            filters.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </x-table>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== Sign — Assessment Conductor modal ===================== --}}
    <div class="modal fade" id="certSignConductorModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sign — Assessment Conductor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p id="cert-sign-conductor-summary" class="ampl-muted"></p>

                    <div class="cert-sign-toggle">
                        <button type="button" class="btn btn-sm btn-outline-primary cert-mode-btn active"
                            data-role="conductor" data-mode="draw">Draw</button>
                        <button type="button" class="btn btn-sm btn-outline-primary cert-mode-btn" data-role="conductor"
                            data-mode="upload">Upload</button>
                    </div>
                    <div class="cert-sign-draw" data-role="conductor">
                        <canvas class="cert-sign-canvas" data-role="conductor"></canvas>
                        <button type="button" class="btn btn-sm btn-outline-secondary cert-clear-btn"
                            data-role="conductor">Clear</button>
                    </div>
                    <div class="cert-sign-upload" data-role="conductor" style="display:none;">
                        <input type="file" accept="image/png,image/jpeg" class="form-control cert-upload-input"
                            data-role="conductor">
                    </div>

                    <div id="cert-sign-conductor-error" class="ampl-banner ampl-banner-error mt-15"
                        style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="cert-sign-conductor-confirm" class="btn btn-primary">Sign as
                        Conductor</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== Sign — Owner modal ===================== --}}
    <div class="modal fade" id="certSignOwnerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sign — Owner</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p id="cert-sign-owner-summary" class="ampl-muted"></p>
                    <p class="ampl-muted">This finalizes the certificate(s) and unlocks download.</p>

                    <div class="cert-sign-toggle">
                        <button type="button" class="btn btn-sm btn-outline-primary cert-mode-btn active"
                            data-role="owner" data-mode="draw">Draw</button>
                        <button type="button" class="btn btn-sm btn-outline-primary cert-mode-btn" data-role="owner"
                            data-mode="upload">Upload</button>
                    </div>
                    <div class="cert-sign-draw" data-role="owner">
                        <canvas class="cert-sign-canvas" data-role="owner"></canvas>
                        <button type="button" class="btn btn-sm btn-outline-secondary cert-clear-btn"
                            data-role="owner">Clear</button>
                    </div>
                    <div class="cert-sign-upload" data-role="owner" style="display:none;">
                        <input type="file" accept="image/png,image/jpeg" class="form-control cert-upload-input"
                            data-role="owner">
                    </div>

                    <div id="cert-sign-owner-error" class="ampl-banner ampl-banner-error mt-15" style="display:none;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="cert-sign-owner-confirm" class="btn btn-primary">Sign as Owner &amp;
                        Approve</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('components.partials.data_table_css')
@include('components.partials.data_table_js')

@push('script')
    <style>
        .ampl-muted {
            color: #8a95a1;
            font-size: 13px;
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

        .ampl-btn-outline {
            background: #fff;
            border: 1px solid #1d4e89;
            color: #1d4e89;
            margin-top: 8px;
        }

        .ampl-filter-label {
            font-weight: 600;
            font-size: 12px;
            color: #6b7784;
            display: block;
            margin-bottom: 4px;
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

        .mt-15 {
            margin-top: 15px;
        }

        #certificates-table thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

        #certificates-table tbody tr {
            transition: background-color .12s ease;
        }

        #certificates-table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        #certificates-table tbody td {
            vertical-align: middle;
        }

        #certificates-table td.select-checkbox {
            cursor: default;
        }

        .cert-bulkbar {
            display: flex;
            align-items: center;
            /* gap: 10px; */
            flex-wrap: wrap;
            background: #f4f6f8;
            border: 1px solid #e1e6eb;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .cert-bulkbar-label {
            font-size: 13px;
            font-weight: 600;
            color: #6b7784;
            margin-right: 4px;
        }

        .cert-bulkbar .ampl-btn {
            margin-top: 0;
            padding: 7px 14px;
            font-size: 13px;
        }

        .cert-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .cert-badge-none {
            background: #f4f6f8;
            color: #8a95a1;
        }

        .cert-badge-pending {
            background: #fff6e0;
            color: #9a7300;
        }

        .cert-badge-approved {
            background: #e8f6ee;
            color: #1e7a43;
        }

        .cert-yes {
            color: #1e7a43;
            font-weight: 700;
            font-size: 12px;
        }

        .cert-no {
            color: #8a95a1;
            font-weight: 600;
            font-size: 12px;
        }

        .cert-sign-toggle {
            display: flex;
            gap: 6px;
            margin-bottom: 8px;
            margin-top: 10px;
        }

        .cert-mode-btn.active {
            background: #1d4e89;
            color: #fff;
            border-color: #1d4e89;
        }

        .cert-sign-canvas {
            width: 100%;
            height: 150px;
            border: 1px dashed #c3cbd3;
            border-radius: 6px;
            background: #fff;
            touch-action: none;
        }

        .cert-clear-btn {
            margin-top: 6px;
        }

        /* Row-selection checkbox — same pill/gradient style as the Credit Invoice
           page's payment checkboxes. .custom-checkbox / .custom-checkbox-indicator
           are expected to already exist globally in your app CSS (both this page
           and the Credit Invoice page rely on them without redefining the wrapper
           class) — if they render unstyled, that global rule is missing and this
           block needs a `.custom-checkbox { position: relative; display: inline-flex; }`
           added alongside it. */
        .custom-checkbox input.cert-select-checkbox {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            margin: 0;
        }

        .custom-checkbox-indicator {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 2px solid #c7cbd6;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color .15s ease, border-color .15s ease;
        }

        .custom-checkbox-indicator::after {
            content: '';
            width: 6px;
            height: 11px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg) scale(0);
            transition: transform .12s ease;
            margin-top: -2px;
        }

        .custom-checkbox input.cert-select-checkbox:checked+.custom-checkbox-indicator {
            background: linear-gradient(135deg, var(--gradient_1), var(--gradient_2));
            border-color: transparent;
        }

        .custom-checkbox input.cert-select-checkbox:checked+.custom-checkbox-indicator::after {
            transform: rotate(45deg) scale(1);
        }

        .custom-checkbox input.cert-select-checkbox:focus-visible+.custom-checkbox-indicator {
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.08);
        }

        .custom-checkbox input.cert-select-checkbox:disabled+.custom-checkbox-indicator {
            opacity: .5;
            cursor: not-allowed;
        }

        #cert-select-all {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            margin: 0;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        window.__CSRF_TOKEN__ = "{{ csrf_token() }}";
        window.__ROUTES__ = {
            certificateGenerate: "{{ route('certificate.generate') }}",
            certificateSignConductor: "{{ route('certificate.sign-conductor') }}",
            certificateSignOwner: "{{ route('certificate.sign-owner') }}",
            certificateDownloadTemplate: "{{ route('certificate.download', ['id' => '__ID__']) }}",
            certificateBulkDownload: "{{ route('certificate.bulk-download') }}",
        };
    </script>

    @pushOnce('scripts')
        <script>
            $(function() {
                if (!$('#certificates-table').length) {
                    return;
                }

                var certTable = $('#certificates-table').DataTable({
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
                    columnDefs: [{
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0
                    }],
                    order: [
                        [1, 'asc']
                    ],
                    buttons: [{
                            extend: 'copyHtml5',
                            title: typeof getFileName === 'function' ? getFileName() : 'certificates',
                            text: '<i class="fa-regular fa-copy"></i>',
                            titleAttr: 'Copy',
                            exportOptions: {
                                columns: ':not(.select-checkbox)'
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            title: typeof getFileName === 'function' ? getFileName() : 'certificates',
                            text: '<i class="fa-solid fa-file-csv"></i>',
                            titleAttr: 'CSV',
                            exportOptions: {
                                columns: ':not(.select-checkbox)'
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            title: typeof getFileName === 'function' ? getFileName() : 'certificates',
                            text: '<i class="fa-solid fa-file-excel"></i>',
                            titleAttr: 'Excel',
                            exportOptions: {
                                columns: ':not(.select-checkbox)'
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            title: typeof getFileName === 'function' ? getFileName() : 'certificates',
                            text: '<i class="fa-solid fa-file-pdf"></i>',
                            titleAttr: 'PDF',
                            exportOptions: {
                                columns: ':not(.select-checkbox)'
                            }
                        },
                        {
                            extend: 'print',
                            title: typeof getFileName === 'function' ? getFileName() : 'certificates',
                            text: '<i class="fa-solid fa-print"></i>',
                            titleAttr: 'Print',
                            exportOptions: {
                                columns: ':not(.select-checkbox)'
                            }
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

                // Select-all toggles every row checkbox currently known to the table
                // (DataTables keeps off-screen-page rows as live DOM nodes it moves
                // in and out of the tbody, so checked state survives paging).
                $('#cert-select-all').on('change', function() {
                    $('.cert-select-checkbox').prop('checked', this.checked);
                });

                // If any individual row gets unchecked, the header checkbox
                // shouldn't keep showing as fully checked.
                $(document).on('change', '.cert-select-checkbox', function() {
                    var total = $('.cert-select-checkbox').length;
                    var checked = $('.cert-select-checkbox:checked').length;
                    $('#cert-select-all').prop('checked', total > 0 && total === checked);
                });

                function selectedRows() {
                    return $('.cert-select-checkbox:checked').map(function() {
                        var $tr = $(this).closest('tr');
                        return {
                            assessmentId: $tr.data('assessment-id'),
                            certId: $tr.data('cert-id') || null,
                            certStatus: $tr.data('cert-status') || null,
                            downloaded: String($tr.data('downloaded')) === '1',
                        };
                    }).get();
                }

                function apiCall(url, options) {
                    options = options || {};
                    options.headers = Object.assign({
                        'X-CSRF-TOKEN': window.__CSRF_TOKEN__,
                        'Accept': 'application/json',
                    }, options.headers || {});
                    return fetch(url, options).then(function(res) {
                        return res.json().catch(function() {
                            throw new Error('Server returned a non-JSON response (HTTP ' + res.status +
                                ').');
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
                    });
                }

                // ---------------- Generate ----------------
                $('#cert-generate-btn').on('click', function() {
                    var rows = selectedRows();
                    var ids = rows.filter(function(r) {
                        return !r.certId;
                    }).map(function(r) {
                        return r.assessmentId;
                    });
                    var skippedCount = rows.length - ids.length;

                    if (!rows.length) {
                        alert('Select one or more rows first.');
                        return;
                    }
                    if (!ids.length) {
                        alert(
                            'None of the selected rows are eligible — a certificate already exists for all of them.'
                        );
                        return;
                    }
                    var msg = 'Generate ' + ids.length + ' certificate(s)?';
                    if (skippedCount > 0) msg += ' (' + skippedCount +
                        ' selected row(s) already have one and will be skipped.)';
                    if (!confirm(msg)) return;

                    apiCall(window.__ROUTES__.certificateGenerate, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            student_assessment_ids: ids
                        }),
                    }).then(function(data) {
                        alert(data.created.length + ' certificate(s) generated.' + (data.skipped
                            .length ? ' ' + data.skipped.length + ' skipped.' : ''));
                        window.location.reload();
                    }).catch(function(err) {
                        alert(err.message);
                    });
                });

                // ---------------- Signature pads (one per modal, re-init on show) ----------------
                var signaturePads = {};

                function initSignaturePad(role) {
                    var canvas = document.querySelector('.cert-sign-canvas[data-role="' + role + '"]');
                    if (!canvas) return;
                    var ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext('2d').scale(ratio, ratio);
                    signaturePads[role] = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255,255,255)'
                    });
                }

                $('#certSignConductorModal').on('shown.bs.modal', function() {
                    initSignaturePad('conductor');
                });
                $('#certSignOwnerModal').on('shown.bs.modal', function() {
                    initSignaturePad('owner');
                });

                $('.cert-mode-btn').on('click', function() {
                    var $btn = $(this);
                    var role = $btn.data('role');
                    var mode = $btn.data('mode');
                    $('.cert-mode-btn[data-role="' + role + '"]').removeClass('active');
                    $btn.addClass('active');
                    if (mode === 'draw') {
                        $('.cert-sign-draw[data-role="' + role + '"]').show();
                        $('.cert-sign-upload[data-role="' + role + '"]').hide();
                    } else {
                        $('.cert-sign-draw[data-role="' + role + '"]').hide();
                        $('.cert-sign-upload[data-role="' + role + '"]').show();
                    }
                });

                $('.cert-clear-btn').on('click', function() {
                    var role = $(this).data('role');
                    if (signaturePads[role]) signaturePads[role].clear();
                });

                function signatureBlob(role) {
                    var mode = $('.cert-mode-btn.active[data-role="' + role + '"]').data('mode');
                    if (mode === 'upload') {
                        var input = document.querySelector('.cert-upload-input[data-role="' + role + '"]');
                        return input && input.files && input.files[0] ? {
                            type: 'file',
                            value: input.files[0]
                        } : null;
                    }
                    var pad = signaturePads[role];
                    if (!pad || pad.isEmpty()) return null;
                    return {
                        type: 'data',
                        value: pad.toDataURL('image/png')
                    };
                }

                // ---------------- Sign — Assessment Conductor (step 1) ----------------
                var pendingConductorIds = [];

                $('#cert-sign-conductor-btn').on('click', function() {
                    var rows = selectedRows();
                    pendingConductorIds = rows.filter(function(r) {
                        return r.certId && r.certStatus === 'generated';
                    }).map(function(r) {
                        return r.certId;
                    });
                    var skippedCount = rows.length - pendingConductorIds.length;

                    if (!rows.length) {
                        alert('Select one or more rows first.');
                        return;
                    }
                    if (!pendingConductorIds.length) {
                        alert(
                            'None of the selected rows are eligible — a certificate must be generated (and not yet Conductor-signed) first.'
                        );
                        return;
                    }
                    var summary = pendingConductorIds.length +
                        ' certificate(s) will be signed as Assessment Conductor.';
                    if (skippedCount > 0) summary += ' ' + skippedCount + ' selected row(s) will be skipped.';
                    $('#cert-sign-conductor-summary').text(summary);
                    $('#cert-sign-conductor-error').hide().text('');
                    $('.cert-mode-btn[data-role="conductor"][data-mode="draw"]').trigger('click');
                    $('.cert-upload-input[data-role="conductor"]').val('');
                    $('#certSignConductorModal').modal('show');
                });

                $('#cert-sign-conductor-confirm').on('click', function() {
                    var sig = signatureBlob('conductor');
                    if (!sig) {
                        $('#cert-sign-conductor-error').text(
                            'Assessment Conductor signature is required — draw or upload one.').show();
                        return;
                    }

                    var $btn = $(this);
                    $btn.prop('disabled', true).text('Signing…');
                    $('#cert-sign-conductor-error').hide().text('');

                    var formData = new FormData();
                    pendingConductorIds.forEach(function(id) {
                        formData.append('certificate_ids[]', id);
                    });
                    if (sig.type === 'file') {
                        formData.append('conductor_signature_file', sig.value);
                    } else {
                        formData.append('conductor_signature_data', sig.value);
                    }

                    apiCall(window.__ROUTES__.certificateSignConductor, {
                            method: 'POST',
                            body: formData
                        })
                        .then(function(data) {
                            $('#certSignConductorModal').modal('hide');
                            alert(data.signed.length + ' certificate(s) signed as Conductor.' + (data
                                .skipped.length ? ' ' + data.skipped.length + ' skipped.' : ''));
                            window.location.reload();
                        })
                        .catch(function(err) {
                            $('#cert-sign-conductor-error').text(err.message).show();
                        })
                        .finally(function() {
                            $btn.prop('disabled', false).text('Sign as Conductor');
                        });
                });

                // ---------------- Sign — Owner (step 2, finalizes) ----------------
                var pendingOwnerIds = [];

                $('#cert-sign-owner-btn').on('click', function() {
                    var rows = selectedRows();
                    pendingOwnerIds = rows.filter(function(r) {
                        return r.certId && r.certStatus === 'conductor_signed';
                    }).map(function(r) {
                        return r.certId;
                    });
                    var skippedCount = rows.length - pendingOwnerIds.length;

                    if (!rows.length) {
                        alert('Select one or more rows first.');
                        return;
                    }
                    if (!pendingOwnerIds.length) {
                        alert(
                            'None of the selected rows are eligible — the Assessment Conductor must sign first.'
                        );
                        return;
                    }
                    var summary = pendingOwnerIds.length +
                        ' certificate(s) will be signed as Owner and marked Approved.';
                    if (skippedCount > 0) summary += ' ' + skippedCount + ' selected row(s) will be skipped.';
                    $('#cert-sign-owner-summary').text(summary);
                    $('#cert-sign-owner-error').hide().text('');
                    $('.cert-mode-btn[data-role="owner"][data-mode="draw"]').trigger('click');
                    $('.cert-upload-input[data-role="owner"]').val('');
                    $('#certSignOwnerModal').modal('show');
                });

                $('#cert-sign-owner-confirm').on('click', function() {
                    var sig = signatureBlob('owner');
                    if (!sig) {
                        $('#cert-sign-owner-error').text('Owner signature is required — draw or upload one.')
                            .show();
                        return;
                    }

                    var $btn = $(this);
                    $btn.prop('disabled', true).text('Signing…');
                    $('#cert-sign-owner-error').hide().text('');

                    var formData = new FormData();
                    pendingOwnerIds.forEach(function(id) {
                        formData.append('certificate_ids[]', id);
                    });
                    if (sig.type === 'file') {
                        formData.append('owner_signature_file', sig.value);
                    } else {
                        formData.append('owner_signature_data', sig.value);
                    }

                    apiCall(window.__ROUTES__.certificateSignOwner, {
                            method: 'POST',
                            body: formData
                        })
                        .then(function(data) {
                            $('#certSignOwnerModal').modal('hide');
                            alert(data.approved.length + ' certificate(s) approved.' + (data.skipped
                                .length ? ' ' + data.skipped.length + ' skipped.' : ''));
                            window.location.reload();
                        })
                        .catch(function(err) {
                            $('#cert-sign-owner-error').text(err.message).show();
                        })
                        .finally(function() {
                            $btn.prop('disabled', false).text('Sign as Owner & Approve');
                        });
                });

                // ---------------- Download ----------------
                $('#cert-download-btn').on('click', function() {
                    var rows = selectedRows();
                    var ids = rows.filter(function(r) {
                        return r.certId && r.certStatus === 'approved';
                    }).map(function(r) {
                        return r.certId;
                    });

                    if (!rows.length) {
                        alert('Select one or more rows first.');
                        return;
                    }
                    if (!ids.length) {
                        alert(
                            'None of the selected rows have a fully approved (downloadable) certificate yet.'
                        );
                        return;
                    }

                    if (ids.length === 1) {
                        window.open(window.__ROUTES__.certificateDownloadTemplate.replace('__ID__', ids[0]),
                            '_blank');
                        return;
                    }

                    var $form = $('<form>', {
                        method: 'POST',
                        action: window.__ROUTES__.certificateBulkDownload,
                        target: '_blank'
                    });
                    $form.append($('<input>', {
                        type: 'hidden',
                        name: '_token',
                        value: window.__CSRF_TOKEN__
                    }));
                    ids.forEach(function(id) {
                        $form.append($('<input>', {
                            type: 'hidden',
                            name: 'certificate_ids[]',
                            value: id
                        }));
                    });
                    $('body').append($form);
                    $form.trigger('submit');
                    $form.remove();
                });
            });
        </script>
    @endPushOnce
@endpush