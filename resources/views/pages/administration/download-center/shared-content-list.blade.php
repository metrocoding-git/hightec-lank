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
.single-content-modal-info .detail-item {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.single-content-modal-info .detail-item:hover {
    border-left-color: #007bff;
    background-color: #f8f9fa !important;
}

.content-modal-section-title h5 {
    font-size: 1.1rem;
}

.detail-label strong {
    font-size: 0.9rem;
}

.detail-value {
    font-size: 0.95rem;
}

.shared-by-card {
    transition: transform 0.3s ease;
}

.shared-by-card:hover {
    transform: translateY(-2px);
}

.line-height-1-8 {
    line-height: 1.8;
}

.border-left {
    border-left: 1px solid #e9ecef !important;
}

.font-weight-500 {
    font-weight: 500;
}

.font-weight-600 {
    font-weight: 600;
}

.text-white-50 {
    color: rgba(0, 0, 0, 0.5) !important;
}

.text-white-70 {
    color: rgba(0, 0, 0, 0.7) !important;
}
</style>
@endpush
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('downloadCenter.shared_content') </h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('downloadCenter.download_center')</a>
                    <a href="#">@lang('downloadCenter.shared_content')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
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
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#deleteModal{{ $sharedContent->id }}"
                                                                href="#">
                                                                @lang('common.delete')
                                                            </a>
                                                        </x-drop-down>
                                                    </td>
                                                </tr>
                                                {{-- Start Delete Modal --}}
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
                                                                    <a href="#" class="text-light">
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="button">
                                                                            @lang('common.delete')
                                                                        </button>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
                                            <div class="modal-header bg-light">
                                                <h4 class="modal-title font-weight-600 text-primary"
                                                    id="view_sc_modal_title">
                                                    <i class="fas fa-eye mr-2"></i>@lang('downloadCenter.view_shared_content')
                                                </h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="container-fluid">
                                                    <div class="row content-container">
                                                        <!-- Main Content Section -->
                                                        <div class="col-lg-8 pr-lg-4">
                                                            <div class="single-content-modal-info">
                                                                <div class="content-modal-section-title mb-3">
                                                                    <h5
                                                                        class="text-dark font-weight-600 border-bottom pb-2">
                                                                        <i
                                                                            class="fas fa-info-circle mr-2 text-primary"></i>@lang('common.details')
                                                                    </h5>
                                                                </div>
                                                                <div class="content-details-list">
                                                                    <div
                                                                        class="detail-item d-flex align-items-center mb-3 p-3 bg-light rounded">
                                                                        <div class="detail-label flex-shrink-0"
                                                                            style="width: 140px;">
                                                                            <strong
                                                                                class="text-muted">@lang('downloadCenter.title'):</strong>
                                                                        </div>
                                                                        <div class="detail-value flex-grow-1">
                                                                            <span id="view_sc_title"
                                                                                class="text-dark font-weight-500">-</span>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="detail-item d-flex align-items-center mb-3 p-3 bg-light rounded">
                                                                        <div class="detail-label flex-shrink-0"
                                                                            style="width: 140px;">
                                                                            <strong
                                                                                class="text-muted">@lang('common.role'):</strong>
                                                                        </div>
                                                                        <div class="detail-value flex-grow-1">
                                                                            <span id="view_sc_role"
                                                                                class="text-dark font-weight-500">-</span>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="detail-item d-flex align-items-center mb-3 p-3 bg-light rounded">
                                                                        <div class="detail-label flex-shrink-0"
                                                                            style="width: 140px;">
                                                                            <strong
                                                                                class="text-muted">@lang('downloadCenter.send_to'):</strong>
                                                                        </div>
                                                                        <div class="detail-value flex-grow-1">
                                                                            <span id="view_sc_send_type"
                                                                                class="text-dark font-weight-500">-</span>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="detail-item d-flex align-items-center mb-3 p-3 bg-light rounded">
                                                                        <div class="detail-label flex-shrink-0"
                                                                            style="width: 140px;">
                                                                            <strong
                                                                                class="text-muted">@lang('downloadCenter.share_date'):</strong>
                                                                        </div>
                                                                        <div class="detail-value flex-grow-1">
                                                                            <span id="view_sc_share_date"
                                                                                class="text-dark font-weight-500">-</span>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="detail-item d-flex align-items-center mb-3 p-3 bg-light rounded">
                                                                        <div class="detail-label flex-shrink-0"
                                                                            style="width: 140px;">
                                                                            <strong
                                                                                class="text-muted">@lang('downloadCenter.valid_upto'):</strong>
                                                                        </div>
                                                                        <div class="detail-value flex-grow-1">
                                                                            <span id="view_sc_valid_upto"
                                                                                class="text-dark font-weight-500">-</span>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="detail-item d-flex align-items-center mb-3 p-3 bg-light rounded">
                                                                        <div class="detail-label flex-shrink-0"
                                                                            style="width: 140px;">
                                                                            <strong
                                                                                class="text-muted">@lang('downloadCenter.url'):</strong>
                                                                        </div>
                                                                        <div class="detail-value flex-grow-1">
                                                                            <a id="view_sc_url" href="#"
                                                                                target="_blank"
                                                                                class="text-primary text-decoration-none font-weight-500">
                                                                                <i
                                                                                    class="fas fa-external-link-alt mr-1"></i>View
                                                                                Link
                                                                            </a>
                                                                        </div>
                                                                    </div>

                                                                    <div class="detail-item mb-3">
                                                                        <div class="detail-label mb-2">
                                                                            <strong
                                                                                class="text-muted">@lang('downloadCenter.description'):</strong>
                                                                        </div>
                                                                        <div class="detail-value p-3 bg-light rounded">
                                                                            <span id="view_sc_description"
                                                                                class="text-dark line-height-1-8">-</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Sidebar Section -->
                                                        <div class="col-lg-4 border-left pl-lg-4">
                                                            <div class="content-modal-section-title mb-3">
                                                                <h5 class="text-dark font-weight-600 border-bottom pb-2">
                                                                    <i
                                                                        class="fas fa-user-shield mr-2 text-primary"></i>@lang('downloadCenter.shared_by')
                                                                </h5>
                                                            </div>
                                                            <div class="single-content-modal-info">
                                                                <div
                                                                    class="shared-by-card text-center p-4 bg-primary text-white rounded">
                                                                    <div class="shared-avatar mb-3">
                                                                        <i
                                                                            class="fas fa-user-circle fa-3x text-white-50"></i>
                                                                    </div>
                                                                    <p id="view_sc_shared_by"
                                                                        class="mb-0 font-weight-500 font-size-16 " style="color: #ffffff">-</p>
                                                                    <small class="text-white-70">Shared By</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    <i class="fas fa-times mr-2"></i>Close
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
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
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
