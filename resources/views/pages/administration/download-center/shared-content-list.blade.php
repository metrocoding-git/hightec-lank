@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@push('css')
    <style>
        .content-modal-section-title {
            font-size: 24px;
            /* Further increased font size */
            font-weight: 700;
            /* Even bolder font weight */
            line-height: 1.3;
            color: var(--base_color);
            margin-bottom: 20px;
            /* Further increased margin */
        }

        .content-modal-section-title.sidebar {
            margin-bottom: 20px;
            /* Further increased margin */
        }

        .single-content-modal-info .row {
            row-gap: 20px;
            /* Further increased row gap */
        }

        .single-content-modal-info .content-type {
            color: var(--base_color);
            font-size: 18px;
            /* Further increased font size */
        }

        ul.content-links li {
            border-top: 1px solid #d0d0d0;
            /* Slightly darker border for contrast */
            padding: 10px 0;
            /* Further increased vertical padding */
        }

        ul.content-links li:first-child {
            border-top: none;
        }

        ul.content-links li a {
            color: #343a40;
            /* Even darker text color */
            padding: 0;
            font-size: 18px;
            /* Further increased font size */
        }

        .single-content-modal.modal .modal-dialog {
            max-width: 98%;
            /* Even wider modal */
        }

        .single-content-modal.modal .modal-dialog .modal-body .row.content-container {
            row-gap: 30px;
            /* Further increased row gap */
        }

        li.attached-content-item {
            border: 1px solid rgba(130, 139, 178, 0.3);
            padding: 10px 15px;
            margin-bottom: -1px;
        }

        li.attached-content-item:first-child {
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }

        li.attached-content-item:last-child {
            margin-bottom: 0;
            border-bottom-right-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        li.attached-content-item:hover {
            color: var(--base_color);
            cursor: pointer;
        }

        #textForClipboard::selection {
            background: none;
        }
    </style>
    <style>
        /* ---- "View Shared Content" modal redesign (Section 32) ----
             Replaces the generic bg-light/bg-primary/text-primary Bootstrap
             styling with the project's own modern-refresh visual language
             (gradient accents from --gradient_1/--gradient_2, card styling,
             a pill badge) — matches the treatment already given to other
             modals across the app.
             REAL BUG FIXED: .text-white-50/.text-white-70 used to be defined
             with BLACK-based rgba(0,0,0,...) values despite their names and
             despite being used as text color on the dark bg-primary "Shared
             By" card below them — nearly invisible, very low-contrast text
             on a colored background. The redesigned card no longer uses
             text-white-* at all, but the two classes are corrected here to
             true white-based translucency in case anything else on this
             page still references them. */
        .text-white-50 {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        .text-white-70 {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        .shared-content-modal-header {
            background: #fff;
            border-bottom: 1px solid var(--border_color);
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .shared-content-modal-header .modal-header-title-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .shared-content-modal-header .title-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--gradient_1), var(--gradient_2));
            color: #fff;
            font-size: 16px;
            flex-shrink: 0;
        }

        .shared-content-modal-header .modal-title {
            margin: 0;
            font-weight: 700;
            font-size: 17px;
            color: #2b2d3c;
        }

        .shared-content-view-section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #9aa1ac;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border_color);
        }

        .shared-content-view-section-title i {
            color: var(--gradient_1);
        }

        .shared-detail-list {
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .shared-detail-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 11px 4px;
            border-bottom: 1px solid var(--border_color);
            transition: background-color .12s ease;
        }

        .shared-detail-row:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .shared-detail-row:last-child {
            border-bottom: none;
        }

        .shared-detail-row-block {
            align-items: flex-start;
            flex-direction: column;
            gap: 8px;
        }

        .shared-detail-label {
            flex: 0 0 150px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #9aa1ac;
            text-transform: uppercase;
            letter-spacing: .02em;
        }

        .shared-detail-label i {
            color: var(--gradient_1);
            width: 14px;
            text-align: center;
        }

        .shared-detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #2b2d3c;
            flex: 1;
        }

        .shared-description {
            margin: 0;
            line-height: 1.7;
            font-weight: 500;
            color: #495057;
        }

        .send-type-pill {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(0, 0, 0, 0.045);
            color: var(--gradient_1);
        }

        .shared-url-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--gradient_1);
            font-weight: 600;
            text-decoration: none;
        }

        .shared-url-link:hover {
            text-decoration: underline;
        }

        .shared-by-col {
            border-left: 1px solid var(--border_color);
        }

        .shared-by-card {
            margin-top: 16px;
            text-align: center;
            padding: 28px 20px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--gradient_1), var(--gradient_2));
            color: #fff;
            transition: transform .2s ease;
        }

        .shared-by-card:hover {
            transform: translateY(-2px);
        }

        .shared-by-card .shared-avatar {
            width: 56px;
            height: 56px;
            margin: 0 auto 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .shared-by-card .shared-by-name {
            margin: 0 0 4px;
            font-weight: 700;
            font-size: 15px;
        }

        .shared-by-card .shared-by-label {
            color: rgba(255, 255, 255, 0.75);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
    </style>
<style>
        /* ---- Modern refresh: cards, table, top banner (Section 24) ---- */
        .white-box {
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
        }

        .white-box .main-title h3,
        .white-box .main-title h5 {
            font-weight: 700;
            position: relative;
            padding-bottom: 10px;
        }

        .white-box .main-title h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 42px;
            height: 3px;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2));
        }

        #table_id thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

        #table_id tbody tr {
            transition: background-color .12s ease;
        }

        #table_id tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        #table_id tbody td {
            vertical-align: middle;
        }

        .sms-breadcrumb {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
            padding: 18px 24px;
            position: relative;
            overflow: hidden;
        }

        .sms-breadcrumb::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--gradient_1), var(--gradient_2));
        }

        .sms-breadcrumb .row {
            align-items: center;
        }

        .sms-breadcrumb h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .sms-breadcrumb .bc-pages {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .sms-breadcrumb .bc-pages a {
            font-size: 13px;
            font-weight: 500;
            color: #8a90a2;
            text-decoration: none;
            transition: color .15s ease;
        }

        .sms-breadcrumb .bc-pages a:hover {
            color: var(--gradient_1);
        }

        .sms-breadcrumb .bc-pages a:not(:last-child)::after {
            content: '\203A';
            margin: 0 8px;
            color: #c7cbd6;
            font-weight: 400;
        }

        .sms-breadcrumb .bc-pages a:last-child {
            color: var(--gradient_1);
            font-weight: 700;
            pointer-events: none;
        }
</style>
@endpush
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('downloadCenter.shared_content') List </h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('downloadCenter.shared_content') List</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @php
                // Button-level permissions (Section 24). No Add key exists for this
                // window — shared entries are created from Content List's Share /
                // Generate URL actions, not from a form on this page.
                $canViewSharedContent = Auth::user()->hasPermission('download-center.shared-content-view');
                $canDeleteSharedContent = Auth::user()->hasPermission('download-center.shared-content-delete');
            @endphp

            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('downloadCenter.shared_content_list')</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id" class="table Crm_table_active3" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ID </th>
                                                <th>TITLE</th>
                                                <th>ROLE</th>
                                                {{-- <th>INTAKE</th> --}}
                                                <th>SHARE DATE</th>
                                                <th>VALID UPTO</th>
                                                <th style="max-width: 150px; ">URL</th>
                                                <th>DESCRIPTION</th>
                                                <th>ACTION</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse($sharedContents as $key => $sharedContent)
                                                @php
                                                    $sendTypeMap = [
                                                        'G' => 'Group',
                                                        'I' => 'Individual',
                                                        'C' => 'Class',
                                                        'P' => 'Public',
                                                    ];
                                                    $sendType =
                                                        $sendTypeMap[$sharedContent->send_to ?? 'I'] ?? 'Individual';
                                                    $roleName = optional($sharedContent->role)->role_name ?? '-';
                                                    $shareDate = $sharedContent->save_date
                                                        ? \Carbon\Carbon::parse($sharedContent->save_date)->format(
                                                            'd/m/Y',
                                                        )
                                                        : '-';
                                                    $validUpto = $sharedContent->valid_date
                                                        ? \Carbon\Carbon::parse($sharedContent->valid_date)->format(
                                                            'd/m/Y',
                                                        )
                                                        : '-';
                                                    $sharedByNames = $sharedContent->users
                                                        ->pluck('name')
                                                        ->implode(', ');
                                                    if (empty($sharedByNames)) {
                                                        $sharedByNames = '-';
                                                    }
                                                    // $intake = $sharedContent->intake
                                                    //     ->pluck('name')
                                                    //     ->implode(', ');
                                                    // if (empty($sharedByNames)) {
                                                    //     $sharedByNames = '-';
                                                    // }
                                                    $description = $sharedContent->description ?? 'No Description';
                                                    $url = $sharedContent->url ?? 'No url';
                                                    $intake = $sharedContent->intake->name ?? '-';
                                                @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $sharedContent->title }}</td>
                                                    <td>{{ $roleName }}</td>
                                                    {{-- <td>{{ $intake }}</td> --}}
                                                    <td>{{ $shareDate }}</td>
                                                    <td>{{ $validUpto }}</td>
                                                    <td
                                                        style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        {{ $url }}</td>
                                                    <td>{{ $description }}</td>
                                                    <td>
                                                        <x-drop-down>
                                                            {{-- If you later add a public/share URL, show it here --}}
                                                            {{-- @if ($sharedContent->url)
                                                                <a class="modalLink dropdown-item"
                                                                    title="@lang('downloadCenter.shared_content_link)" href="{{ $sharedContent->url }}">
                                                                    @lang('downloadCenter.link')
                                                                </a>
                                                            @endif --}}
                                                            @if ($sharedContent->url && $sharedContent->url != 'No url')
                                                                <a class="dropdown-item copyToClipboard"
                                                                    data-clipboard-text="{{ $url }}"
                                                                    href="#">
                                                                    @lang('downloadCenter.copy_url')
                                                                </a>
                                                            @endif
                                                            @if ($canViewSharedContent)
                                                                <a href="#" class="dropdown-item viewSharedContentBtn"
                                                                    title="@lang('downloadCenter.view_shared_content')"
                                                                    data-title="{{ $sharedContent->title }}"
                                                                    data-role="{{ $roleName }}"
                                                                    data-send_type="{{ $sendType }}"
                                                                    data-share_date="{{ $shareDate }}"
                                                                    data-valid_upto="{{ $validUpto }}"
                                                                    data-url="{{ $url }}"
                                                                    data-description="{{ $description }}"
                                                                    data-shared_by="{{ $sharedByNames }}">
                                                                    @lang('common.view')
                                                                </a>
                                                            @endif
                                                            @if ($canDeleteSharedContent)
                                                                <a class="dropdown-item" data-toggle="modal"
                                                                    data-target="#deleteModal{{ $sharedContent->id }}"
                                                                    href="#">
                                                                    @lang('common.delete')
                                                                </a>
                                                            @endif
                                                        </x-drop-down>
                                                    </td>
                                                </tr>
                                                {{-- Start Delete Modal --}}
                                                @if ($canDeleteSharedContent)
                                                <div class="modal fade admin-query"
                                                    id="deleteModal{{ $sharedContent->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">@lang('downloadCenter.delete_shared_content')</h4>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="text-center">
                                                                    <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                                </div>
                                                                <div class="mt-40 d-flex justify-content-between">
                                                                    <button type="button" class="primary-btn tr-bg"
                                                                        data-dismiss="modal">
                                                                        @lang('common.cancel')
                                                                    </button>
                                                                    <form
                                                                        action="{{ route('download-center.shared-content-delete', $sharedContent->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">
                                                                            @lang('common.delete')
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                {{-- End Delete Modal --}}
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">@lang('common.no_data_available')</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </x-table>
                                <!-- View Shared Content Modal -->
                                <div class="modal fade admin-query single-content-modal" id="viewSharedContent"
                                    tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content">
                                            {{-- VISUAL POLISH (Section 32): redesigned from the generic
                                                 bg-light/bg-primary/text-primary Bootstrap defaults to the
                                                 project's own modern-refresh language — gradient icon-badge
                                                 header, a clean label/value list instead of individually
                                                 boxed "detail-item" rows, a pill for Send To, and a gradient
                                                 "Shared By" card. Every element id the JS below reads
                                                 (#view_sc_title, #view_sc_role, #view_sc_send_type,
                                                 #view_sc_share_date, #view_sc_valid_upto, #view_sc_url,
                                                 #view_sc_description, #view_sc_shared_by,
                                                 #view_sc_modal_title) is unchanged, so no JS changes were
                                                 needed. --}}
                                            <div class="modal-header shared-content-modal-header">
                                                <div class="modal-header-title-group">
                                                    <span class="title-icon"><i class="fa-solid fa-eye"></i></span>
                                                    <h4 class="modal-title" id="view_sc_modal_title">
                                                        @lang('downloadCenter.view_shared_content')
                                                    </h4>
                                                </div>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="row content-container">
                                                    <!-- Main Content Section -->
                                                    <div class="col-lg-8 pr-lg-4">
                                                        <div class="shared-content-view-section-title">
                                                            <i class="fa-solid fa-circle-info"></i>@lang('common.details')
                                                        </div>
                                                        <div class="shared-detail-list">
                                                            <div class="shared-detail-row">
                                                                <span class="shared-detail-label">
                                                                    <i class="fa-solid fa-heading"></i>@lang('downloadCenter.title')
                                                                </span>
                                                                <span class="shared-detail-value" id="view_sc_title">-</span>
                                                            </div>

                                                            <div class="shared-detail-row">
                                                                <span class="shared-detail-label">
                                                                    <i class="fa-solid fa-user-tag"></i>@lang('common.role')
                                                                </span>
                                                                <span class="shared-detail-value" id="view_sc_role">-</span>
                                                            </div>

                                                            <div class="shared-detail-row">
                                                                <span class="shared-detail-label">
                                                                    <i class="fa-solid fa-share-nodes"></i>@lang('downloadCenter.send_to')
                                                                </span>
                                                                <span class="shared-detail-value">
                                                                    <span class="send-type-pill" id="view_sc_send_type">-</span>
                                                                </span>
                                                            </div>

                                                            <div class="shared-detail-row">
                                                                <span class="shared-detail-label">
                                                                    <i class="fa-solid fa-calendar-check"></i>@lang('downloadCenter.share_date')
                                                                </span>
                                                                <span class="shared-detail-value" id="view_sc_share_date">-</span>
                                                            </div>

                                                            <div class="shared-detail-row">
                                                                <span class="shared-detail-label">
                                                                    <i class="fa-solid fa-hourglass-end"></i>@lang('downloadCenter.valid_upto')
                                                                </span>
                                                                <span class="shared-detail-value" id="view_sc_valid_upto">-</span>
                                                            </div>

                                                            <div class="shared-detail-row">
                                                                <span class="shared-detail-label">
                                                                    <i class="fa-solid fa-link"></i>@lang('downloadCenter.url')
                                                                </span>
                                                                <span class="shared-detail-value">
                                                                    <a id="view_sc_url" href="#" target="_blank"
                                                                        class="shared-url-link">
                                                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>View Link
                                                                    </a>
                                                                </span>
                                                            </div>

                                                            <div class="shared-detail-row shared-detail-row-block">
                                                                <span class="shared-detail-label">
                                                                    <i class="fa-solid fa-align-left"></i>@lang('downloadCenter.description')
                                                                </span>
                                                                <p class="shared-detail-value shared-description" id="view_sc_description">-</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Sidebar Section -->
                                                    <div class="col-lg-4 shared-by-col">
                                                        <div class="shared-content-view-section-title">
                                                            <i class="fa-solid fa-user-shield"></i>@lang('downloadCenter.shared_by')
                                                        </div>
                                                        <div class="shared-by-card">
                                                            <div class="shared-avatar">
                                                                <i class="fa-solid fa-user-circle"></i>
                                                            </div>
                                                            <p class="shared-by-name" id="view_sc_shared_by">-</p>
                                                            <small class="shared-by-label">Shared By</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="primary-btn tr-bg" data-dismiss="modal">
                                                    <i class="fa-solid fa-xmark"></i> Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@include('components.partials.data_table_js')
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
    <script type="text/javascript">
        var Clipboard = new ClipboardJS('.copyToClipboard');
    </script>
    <script>
       $('#table_id').DataTable({
            responsive: true,
            dom: "<'dt-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3'" +
                "<'dt-toolbar-title'>" +
                "<'dt-toolbar-controls d-flex align-items-center flex-wrap gap-2 mt-3'<'dt-length'l><'dt-search'f><'dt-buttons-wrap'B>>" +
                ">" +
                "<'row'<'col-sm-12'tr>>" +
                "<'gap-2 mt-3'<'dt-info'i><'dt-pagination'p>> ",
            buttons: [{
                    extend: 'copyHtml5',
                    title: getFileName(),
                    text: '<i class="fa-regular fa-copy"></i>',
                    titleAttr: 'Copy'
                },
                {
                    extend: 'csvHtml5',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-file-csv"></i>',
                    titleAttr: 'CSV'
                },
                {
                    extend: 'excelHtml5',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-file-excel"></i>',
                    titleAttr: 'Excel'
                },
                {
                    extend: 'pdfHtml5',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-file-pdf"></i>',
                    titleAttr: 'PDF'
                },
                {
                    extend: 'print',
                    title: getFileName(),
                    text: '<i class="fa-solid fa-print"></i>',
                    titleAttr: 'Print'
                }
            ],
            pageLength: 100,
            language: {
                search: '<i class=""></i>',
                searchPlaceholder: 'Search...',
                paginate: {
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            },
            paging: true,
            searching: true,
            ordering: true,
            info: true,
 });
        $(document).on('click', '.copyToClipboard', function(e) {
            toastr.success("Link Copied", 'Success');
        });

        // Populate and show View modal for Shared Content
        $(document).on('click', '.viewSharedContentBtn', function(e) {
            e.preventDefault();
            const title = $(this).data('title') || '-';
            const role = $(this).data('role') || '-';
            const sendType = $(this).data('send_type') || '-';
            const shareDate = $(this).data('share_date') || '-';
            const validUpto = $(this).data('valid_upto') || '-';
            const url = $(this).data('url') || '';
            const description = $(this).data('description') || '-';
            const sharedBy = $(this).data('shared_by') || '-';

            $('#view_sc_modal_title').text(title);
            $('#view_sc_title').text(title);
            $('#view_sc_role').text(role);
            $('#view_sc_send_type').text(sendType);
            $('#view_sc_share_date').text(shareDate);
            $('#view_sc_valid_upto').text(validUpto);
            if (url && url !== 'No url') {
                $('#view_sc_url').attr('href', url).text(url);
            } else {
                $('#view_sc_url').attr('href', '#').text('-');
            }
            $('#view_sc_description').text(description);
            $('#view_sc_shared_by').text(sharedBy);

            $('#viewSharedContent').modal('show');
        });
    </script>
@endpush