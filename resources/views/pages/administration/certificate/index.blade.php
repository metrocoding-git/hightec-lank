@extends('layouts.app')
@section('title')
    Certificate Management
@endsection

@section('mainContent')
    @php
        $genarateCertificate = Auth::user()->hasPermission('certificate.generate');
        $signConductor = Auth::user()->hasPermission('certificate.sign-conductor');
        $signOwner = Auth::user()->hasPermission('certificate.sign-owner');
        $undoCertificate = Auth::user()->hasPermission('certificate.undo');
        $manageTemplates = Auth::user()->hasPermission('certificate-template.index');
        // $download = Auth::user()->hasPermission('student-list.search');
    @endphp

    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Certificate Management</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Certificate Management</a>
                    @if ($manageTemplates)
                        <a href="{{ route('certificate-template.index') }}">Certificate Templates</a>
                    @endif
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
                                {{ request('cert_status') === 'pending_conductor' ? 'selected' : '' }}>Awaiting Director
                                Training Signature</option>
                            <option value="pending_owner"
                                {{ request('cert_status') === 'pending_owner' ? 'selected' : '' }}>Awaiting Managing
                                Director Signature
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
                                <i class="fa-solid fa-signature"></i> Sign — Director Training
                            </button>
                        @endif

                        @if ($signOwner)
                            <button type="button" id="cert-sign-owner-btn" class="ampl-btn ampl-btn-outline col-3">
                                <i class="fa-solid fa-signature"></i> Sign — Managing Director
                            </button>
                        @endif

                        <button type="button" id="cert-download-btn" class="ampl-btn ampl-btn-outline col-3">
                            <i class="fa-solid fa-download"></i> Download Certificate(s)
                        </button>

                        @if ($undoCertificate)
                            <button type="button" id="cert-undo-btn" class="ampl-btn ampl-btn-outline col-12 mt-2">
                                <i class="fa-solid fa-rotate-left"></i> Undo Last Step
                            </button>
                        @endif

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
                                    <th>Stage</th>
                                    <th>NIC Number</th>
                                    <th>Course</th>
                                    <th>Module</th>
                                    <th>Theory Subjects</th>
                                    <th>Practical Subjects</th>
                                    <th>Created Date</th>
                                    <th>Total</th>
                                    <th>Result</th>
                                    <th>Grade</th>
                                    <th>Student Category</th>
                                    <th>Address</th>
                                    <th>Name Verified</th>
                                    <th>NIC / ID No. Verified</th>
                                    <th>Address Verified</th>
                                    <th>Start Date</th>
                                    <th>Created By</th>
                                    <th>Intake</th>
                                    <th>FS Conduct Branch</th>
                                    <th>Intake Branch</th>
                                    <th>Certificate #</th>
                                    <th>Director Training Signed</th>
                                    <th>Managing Director Signed</th>
                                    <th>Downloaded</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($assessments ?? collect()) as $i => $a)
                                    @php
                                        $cert = $a->certificate;
                                        $assessedCourseId = optional($a->template->course)->id;
                                        $categoryKey = $a->student_id . '-' . $assessedCourseId;
                                        $studentCategory =
                                            optional(($categoriesByPair ?? collect())->get($categoryKey))->category_name ?? '-';
                                        $theoryProgress = $a->theoryProgress ?? ['completed' => 0, 'total' => 0];
                                        $practicalProgress = $a->practicalProgress ?? ['completed' => 0, 'total' => 0];
                                    @endphp
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
                                        <td>
                                            @if (!$cert)
                                                <span class="cert-badge cert-badge-none">Not generated</span>
                                            @elseif ($cert->status === 'generated')
                                                <span class="cert-badge cert-badge-pending">Awaiting Director Training</span>
                                            @elseif ($cert->status === 'conductor_signed')
                                                <span class="cert-badge cert-badge-pending">Awaiting Managing Director</span>
                                            @else
                                                <span class="cert-badge cert-badge-approved">Approved</span>
                                            @endif
                                        </td>
                                        <td>{{ $a->student->national_id_number ?? '-' }}</td>
                                        <td>{{ $a->template->course->name ?? '-' }}</td>
                                        <td>{{ $a->template->module_name ?? '-' }}</td>
                                        <td>{{ $theoryProgress['completed'] }}/{{ $theoryProgress['total'] }}</td>
                                        <td>{{ $practicalProgress['completed'] }}/{{ $practicalProgress['total'] }}</td>
                                        <td>{{ optional($a->assessed_at)->format('Y-m-d') }}</td>
                                        <td>{{ $a->total_marks }} / {{ $a->max_marks }}</td>
                                        <td>
                                            @if ($a->is_qualified)
                                                <span class="badge badge-success">PASS</span>
                                            @else
                                                <span class="badge badge-danger">FAIL</span>
                                            @endif
                                        </td>
                                        <td>{{ $a->grade ?? '-' }}</td>
                                        <td>{{ $studentCategory }}</td>
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
                                        <td>{{ $a->studentCourse->intake->start_date ?? '-' }}</td>
                                        <td>{{ $a->evaluator->name ?? '-' }}</td>
                                        <td>{{ $a->studentCourse->intake->name ?? '-' }}</td>
                                        <td>{{ $a->evaluator->branches->first()->name ?? '-' }}</td>
                                        <td>{{ $a->studentCourse->branch->name ?? '-' }}</td>
                                        <td>{{ $cert->certificate_number ?? '-' }}</td>

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
                                        <td class="text-nowrap">
                                            @if ($cert && $cert->status === 'approved')
                                                <a href="{{ route('certificate.download', ['id' => $cert->id]) }}"
                                                    class="btn btn-sm btn-outline-success" target="_blank"
                                                    title="Download certificate">
                                                    <i class="fa-solid fa-download"></i>
                                                </a>
                                            @endif
                                            {{-- NEW — same per-row "PDF" action the Final Assessment Marks
                                                 list already has (the assessment's own PDF, distinct from the
                                                 certificate download above). --}}
                                            <a href="{{ url('/student/student-info/assessment/' . $a->id . '/pdf') }}"
                                                class="btn btn-sm btn-outline-primary" target="_blank"
                                                title="Assessment PDF">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="30" class="text-center">No PASSed assessments found for these
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

    {{-- ===================== Generate modal (certificate template is now
         auto-selected per course — see the "course template want auto
         select" request; only Date of Issue and Course End Date are
         asked for here) ===================== --}}
    <div class="modal fade" id="certGenerateModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Certificate(s)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p id="cert-generate-summary" class="ampl-muted"></p>

                    <div class="form-group">
                        <label>Date of Issue <span class="text-danger">*</span></label>
                        <input type="date" id="cert-generate-issue-date" class="form-control">
                    </div>

                    <div class="form-group mb-0">
                        <label>Course End Date <span class="text-danger">*</span></label>
                        <input type="date" id="cert-generate-course-end-date" class="form-control">
                        <small class="ampl-muted">Must be on/after the course's start date, and the gap between them
                            must cover the course's full duration (e.g. a 2-month course needs at least 60
                            days).</small>
                    </div>

                    <div id="cert-generate-error" class="ampl-banner ampl-banner-error mt-15" style="display:none;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="cert-generate-confirm" class="btn btn-primary">Generate</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== Sign — Director Training modal ===================== --}}
    <div class="modal fade" id="certSignConductorModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg cert-sign-modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header cert-sign-modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-signature mr-2"></i>Sign — Director Training</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p id="cert-sign-conductor-summary" class="ampl-muted"></p>

                    <div class="cert-sign-toggle">
                        <button type="button" class="btn btn-sm cert-mode-btn active" data-role="conductor"
                            data-mode="draw"><i class="fa-solid fa-pen-nib"></i> Draw</button>
                        <button type="button" class="btn btn-sm cert-mode-btn" data-role="conductor"
                            data-mode="upload"><i class="fa-solid fa-upload"></i> Upload</button>
                    </div>
                    <div class="cert-sign-draw" data-role="conductor">
                        <div class="cert-sign-pad-wrap">
                            <canvas class="cert-sign-canvas" data-role="conductor"></canvas>
                            <div class="cert-sign-hint" data-role="conductor">
                                <i class="fa-solid fa-pen"></i>
                                <span>Sign here using your mouse, trackpad, or touchscreen</span>
                            </div>
                            <button type="button" class="cert-clear-fab" data-role="conductor" title="Clear signature">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>
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
                        Director Training</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== Sign — Managing Director modal ===================== --}}
    <div class="modal fade" id="certSignOwnerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg cert-sign-modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header cert-sign-modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-signature mr-2"></i>Sign — Managing Director</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p id="cert-sign-owner-summary" class="ampl-muted"></p>
                    <p class="ampl-muted">This finalizes the certificate(s) and unlocks download.</p>

                    <div class="cert-sign-toggle">
                        <button type="button" class="btn btn-sm cert-mode-btn active" data-role="owner"
                            data-mode="draw"><i class="fa-solid fa-pen-nib"></i> Draw</button>
                        <button type="button" class="btn btn-sm cert-mode-btn" data-role="owner"
                            data-mode="upload"><i class="fa-solid fa-upload"></i> Upload</button>
                    </div>
                    <div class="cert-sign-draw" data-role="owner">
                        <div class="cert-sign-pad-wrap">
                            <canvas class="cert-sign-canvas" data-role="owner"></canvas>
                            <div class="cert-sign-hint" data-role="owner">
                                <i class="fa-solid fa-pen"></i>
                                <span>Sign here using your mouse, trackpad, or touchscreen</span>
                            </div>
                            <button type="button" class="cert-clear-fab" data-role="owner" title="Clear signature">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>
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
                    <button type="button" id="cert-sign-owner-confirm" class="btn btn-primary">Sign as Managing
                        Director &amp; Approve</button>
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

        /* ===================== Signature UI — made bigger/friendlier per
           "sign is not user frient and ui beautifull ... want come full
           and big size ... useing touch pad or useing pan in window".
           The modal is now modal-lg (wider dialog), the pad itself is
           much taller with a responsive breakpoint, and a "Sign here"
           hint + a dedicated round Clear button sit on top of it instead
           of the pad being a bare unlabeled rectangle. ===================== */
        .cert-sign-modal-dialog {
            max-width: 760px;
        }

        .cert-sign-modal-header {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            border-bottom: none;
        }

        .cert-sign-modal-header .modal-title {
            color: #fff;
            font-weight: 700;
        }

        .cert-sign-modal-header .close {
            color: #fff;
            opacity: .85;
            text-shadow: none;
        }

        .cert-sign-modal-header .close:hover {
            opacity: 1;
        }

        .cert-sign-toggle {
            display: inline-flex;
            gap: 0;
            margin-bottom: 14px;
            margin-top: 10px;
            background: #f0f2f5;
            border-radius: 999px;
            padding: 4px;
        }

        .cert-mode-btn {
            border: none;
            background: transparent;
            color: #6b7784;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 999px;
            transition: background-color .15s ease, color .15s ease;
        }

        .cert-mode-btn i {
            margin-right: 6px;
        }

        .cert-mode-btn.active {
            background: #fff;
            color: #1d4e89;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
        }

        .cert-sign-pad-wrap {
            position: relative;
        }

        .cert-sign-canvas {
            width: 100%;
            height: 320px;
            border: 2px dashed #c3cbd3;
            border-radius: 12px;
            background: #fff;
            touch-action: none;
            display: block;
        }

        @media (max-width: 576px) {
            .cert-sign-canvas {
                height: 220px;
            }
        }

        /* Fills the frame with a clear "sign in this box, along this
           line" guide instead of a plain blank rectangle — much easier
           to read on a touchpad or pen-tablet where there's no physical
           edge to feel for. */
        .cert-sign-pad-wrap::after {
            content: '';
            position: absolute;
            left: 8%;
            right: 8%;
            bottom: 22%;
            border-bottom: 2px solid #dfe3e8;
            pointer-events: none;
        }

        .cert-sign-hint {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            transform: translateY(-50%);
            text-align: center;
            color: #b0b8c1;
            font-size: 15px;
            font-weight: 600;
            pointer-events: none;
            transition: opacity .12s ease;
        }

        .cert-sign-hint i {
            display: block;
            font-size: 22px;
            margin-bottom: 6px;
            color: #c7cbd6;
        }

        .cert-sign-hint.cert-sign-hint-hidden {
            opacity: 0;
        }

        .cert-clear-fab {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid #e1e6eb;
            background: #fff;
            color: #6b7784;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
            cursor: pointer;
            transition: background-color .15s ease, color .15s ease;
        }

        .cert-clear-fab:hover {
            background: #fdecea;
            color: #a12a2a;
            border-color: #f5c6c6;
        }

        .cert-sign-upload {
            padding: 30px;
            border: 2px dashed #c3cbd3;
            border-radius: 12px;
            text-align: center;
            background: #fafbfc;
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
            certificateUndo: "{{ route('certificate.undo') }}",
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

                // ---------------- Generate (template is auto-selected server-side per course; only Date of Issue + Course End Date are asked for) ----------------
                var pendingGenerateIds = [];

                $('#cert-generate-btn').on('click', function() {
                    var rows = selectedRows();
                    pendingGenerateIds = rows.filter(function(r) {
                        return !r.certId;
                    }).map(function(r) {
                        return r.assessmentId;
                    });
                    var skippedCount = rows.length - pendingGenerateIds.length;

                    if (!rows.length) {
                        alert('Select one or more rows first.');
                        return;
                    }
                    if (!pendingGenerateIds.length) {
                        alert(
                            'None of the selected rows are eligible — a certificate already exists for all of them.'
                        );
                        return;
                    }

                    var summary = pendingGenerateIds.length +
                        ' certificate(s) will be generated (certificate template is auto-selected per course).';
                    if (skippedCount > 0) summary += ' ' + skippedCount +
                        ' selected row(s) already have one and will be skipped.';
                    $('#cert-generate-summary').text(summary);
                    $('#cert-generate-error').hide().text('');
                    // "genarate date (default set today)" — pre-filled here, still editable.
                    var today = new Date().toISOString().slice(0, 10);
                    // Date of Issue can't be before the last certificate's issue date.
                    var lastIssueDate = @json($lastIssueDate);
                    var defaultIssueDate = (lastIssueDate && lastIssueDate > today) ? lastIssueDate : today;
                    $('#cert-generate-issue-date').attr('min', lastIssueDate || '').val(defaultIssueDate);
                    $('#cert-generate-course-end-date').val(today);
                    $('#certGenerateModal').modal('show');
                });

                $('#cert-generate-confirm').on('click', function() {
                    var issueDate = $('#cert-generate-issue-date').val();
                    var courseEndDate = $('#cert-generate-course-end-date').val();

                    if (!issueDate) {
                        $('#cert-generate-error').text('Date of Issue is required.').show();
                        return;
                    }
                    var minIssueDate = $('#cert-generate-issue-date').attr('min');
                    if (minIssueDate && issueDate < minIssueDate) {
                        $('#cert-generate-error').text("Date of Issue can't be earlier than the last certificate's issue date (" + minIssueDate + ').').show();
                        return;
                    }
                    if (!courseEndDate) {
                        $('#cert-generate-error').text('Course End Date is required.').show();
                        return;
                    }

                    var $btn = $(this);
                    $btn.prop('disabled', true).text('Generating…');
                    $('#cert-generate-error').hide().text('');

                    apiCall(window.__ROUTES__.certificateGenerate, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            student_assessment_ids: pendingGenerateIds,
                            issue_date: issueDate,
                            course_end_date: courseEndDate,
                        }),
                    }).then(function(data) {
                        $('#certGenerateModal').modal('hide');
                        var msg = data.created.length + ' certificate(s) generated.';
                        if (data.skipped.length) {
                            msg += ' ' + data.skipped.length + ' skipped:\n' + data.skipped.map(
                                function(s) {
                                    return '- ' + s.reason;
                                }).join('\n');
                        }
                        alert(msg);
                        window.location.reload();
                    }).catch(function(err) {
                        $('#cert-generate-error').text(err.message).show();
                    }).finally(function() {
                        $btn.prop('disabled', false).text('Generate');
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
                        backgroundColor: 'rgb(255,255,255)',
                        // "signs are want come blue color like orginal pen sign"
                        penColor: 'rgb(14, 47, 150)'
                    });

                    // "sign is not user frient" — a plain blank rectangle gave
                    // no clue where or how to sign. The hint text fades out
                    // the moment a stroke starts, and comes back if the pad
                    // is cleared, so it never gets in the way of the actual
                    // signature or ends up baked into the saved PNG.
                    var $hint = $('.cert-sign-hint[data-role="' + role + '"]').removeClass(
                        'cert-sign-hint-hidden');
                    signaturePads[role].addEventListener('beginStroke', function() {
                        $hint.addClass('cert-sign-hint-hidden');
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

                // Round "clear" button that now sits on top of the pad itself
                // (bigger and easier to hit than the old text-link style
                // button used to be) — also brings the "sign here" hint
                // back so it's obvious the pad is empty again.
                $(document).on('click', '.cert-clear-fab', function() {
                    var role = $(this).data('role');
                    if (signaturePads[role]) signaturePads[role].clear();
                    $('.cert-sign-hint[data-role="' + role + '"]').removeClass('cert-sign-hint-hidden');
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

                // ---------------- Sign — Director Training (step 1) ----------------
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
                            'None of the selected rows are eligible — a certificate must be generated (and not yet Director Training-signed) first.'
                        );
                        return;
                    }
                    var summary = pendingConductorIds.length +
                        ' certificate(s) will be signed as Director Training.';
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
                            'Director Training signature is required — draw or upload one.').show();
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
                            alert(data.signed.length + ' certificate(s) signed as Director Training.' + (data
                                .skipped.length ? ' ' + data.skipped.length + ' skipped.' : ''));
                            window.location.reload();
                        })
                        .catch(function(err) {
                            $('#cert-sign-conductor-error').text(err.message).show();
                        })
                        .finally(function() {
                            $btn.prop('disabled', false).text('Sign as Director Training');
                        });
                });

                // ---------------- Sign — Managing Director (step 2, finalizes) ----------------
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
                            'None of the selected rows are eligible — Director Training must sign first.'
                        );
                        return;
                    }
                    var summary = pendingOwnerIds.length +
                        ' certificate(s) will be signed as Managing Director and marked Approved.';
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
                        $('#cert-sign-owner-error').text('Managing Director signature is required — draw or upload one.')
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
                            $btn.prop('disabled', false).text('Sign as Managing Director & Approve');
                        });
                });

                // ---------------- Undo Last Step ----------------
                // "want to can be go back one like if only do genarate can be
                // do undo to that if do sign conductor can be do undo to
                // genarate level if do sign owner sign can be do undo to sign
                // conductor if complete sign owner can be undow that" — every
                // eligible selected row (anything with a certificate at all)
                // steps back exactly one stage. "if download want to aleret
                // that donwloded" is handled here, client-side, BEFORE the
                // request is even sent — a plain confirm() for the normal
                // case, with an extra explicit warning line appended when one
                // or more selected rows have already been downloaded, since
                // undoing doesn't erase that download history (the backend
                // keeps downloaded_at/download_count as-is on purpose).
                $('#cert-undo-btn').on('click', function() {
                    var rows = selectedRows();
                    var eligible = rows.filter(function(r) {
                        return !!r.certId;
                    });
                    var skippedCount = rows.length - eligible.length;
                    var alreadyDownloadedCount = eligible.filter(function(r) {
                        return r.downloaded;
                    }).length;

                    if (!rows.length) {
                        alert('Select one or more rows first.');
                        return;
                    }
                    if (!eligible.length) {
                        alert(
                            'None of the selected rows have a certificate yet — there is nothing to undo.'
                        );
                        return;
                    }

                    var msg = 'Step ' + eligible.length +
                        ' certificate(s) back one stage (e.g. Approved -> Director Training signed, or Generated -> not generated)?';
                    if (skippedCount > 0) msg += ' (' + skippedCount +
                        ' selected row(s) have no certificate and will be skipped.)';
                    if (alreadyDownloadedCount > 0) {
                        msg += '\n\n⚠ ' + alreadyDownloadedCount +
                            ' of these have ALREADY BEEN DOWNLOADED before. Undoing won\'t recall any copies already given out — it only reverts the record here.';
                    }
                    if (!confirm(msg)) return;

                    apiCall(window.__ROUTES__.certificateUndo, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            certificate_ids: eligible.map(function(r) {
                                return r.certId;
                            })
                        }),
                    }).then(function(data) {
                        alert(data.reverted.length + ' certificate(s) stepped back.' + (data.skipped
                            .length ? ' ' + data.skipped.length + ' skipped.' : ''));
                        window.location.reload();
                    }).catch(function(err) {
                        alert(err.message);
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