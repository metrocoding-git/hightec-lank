@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@push('css')
    <style>
        .content-modal-section-title {
            font-size: 18px;
            font-weight: 500;
            line-height: 1.1;
            color: inherit;
            margin-bottom: 10px;
            color: var(--base_color);
        }

        .content-modal-section-title.sidebar {
            margin-bottom: 10px;
        }

        .single-content-modal-info .row {
            row-gap: 10px;
        }

        .single-content-modal-info .content-type {
            color: var(--base_color);
            font-size: 14px;
        }

        ul.content-links li {
            border-top: 1px solid #dad6d6;

        }

        ul.content-links li:first-child {
            border-top: none;
        }

        ul.content-links li a {
            color: #828bb2;
            padding: 5px
        }

        .single-content-modal.modal .modal-dialog {
            max-width: 90%;
        }

        .single-content-modal.modal .modal-dialog .modal-body .row.content-container {
            row-gap: 20px;
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
                                                <th>ID</th>
                                                <th>TITLE</th>
                                                <th>ROLE</th>
                                                <th>SHARE DATE</th>
                                                <th>VALID UPTO</th>
                                                <th>SHARED BY</th>
                                                <th>DESCRIPTION</th>
                                                <th>ACTION</th>
                                            </tr>
                                        </thead>
                                       
                                        <tbody>
                                            @forelse($sharedContents as $key => $sharedContent)
                                                @php
                                                    $sendTypeMap = ['G' => 'Group', 'I' => 'Individual', 'C' => 'Class', 'P' => 'Public'];
                                                    $sendType = $sendTypeMap[$sharedContent->send_to ?? 'I'] ?? 'Individual';
                                                    $roleName = optional($sharedContent->role)->role_name ?? '-';
                                                    $shareDate = $sharedContent->save_date ? \Carbon\Carbon::parse($sharedContent->save_date)->format('d/m/Y') : '-';
                                                    $validUpto = $sharedContent->valid_date ? \Carbon\Carbon::parse($sharedContent->valid_date)->format('d/m/Y') : '-';
                                                    $sharedByNames = $sharedContent->users->pluck('name')->implode(', ');
                                                    if(empty($sharedByNames)) { $sharedByNames = '-'; }
                                                    $description = $sharedContent->description ?? 'No Description';
                                                @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $sharedContent->title }}</td>
                                                    <td>{{ $roleName }}</td>
                                                    <td>{{ $shareDate }}</td>
                                                    <td>{{ $validUpto }}</td>
                                                    <td>{{ $sharedByNames }}</td>
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
                                                            <a class="modalLink dropdown-item" data-modal-size="large-modal"
                                                                title="@lang('downloadCenter.view_shared_content')" href="#">
                                                                @lang('common.view')
                                                            </a>
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#deleteModal{{ $sharedContent->id }}" href="#">
                                                                @lang('common.delete')
                                                            </a>
                                                        </x-drop-down>
                                                    </td>
                                                </tr>
                                                {{-- Start Delete Modal --}}
                                                <div class="modal fade admin-query" id="deleteModal{{ $sharedContent->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">@lang('downloadCenter.delete_shared_content')</h4>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="text-center">
                                                                    <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                                </div>
                                                                <div class="mt-40 d-flex justify-content-between">
                                                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">
                                                                        @lang('common.cancel')
                                                                    </button>
                                                                    <a href="#" class="text-light">
                                                                        <button class="primary-btn fix-gr-bg" type="button">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
    <script type="text/javascript">
        var Clipboard = new ClipboardJS('.copyToClipboard');
    </script>
    <script>
        $(document).on('click', '.copyToClipboard', function(e) {
            toastr.success("Link Copied", 'Success');
        });
    </script>
@endpush
