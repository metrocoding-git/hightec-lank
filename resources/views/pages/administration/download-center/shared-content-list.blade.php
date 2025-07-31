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
                                                <th>@lang('downloadCenter.sl')</th>
                                                <th>@lang('downloadCenter.title')</th>
                                                <th>@lang('downloadCenter.send_to')</th>
                                                <th>@lang('downloadCenter.share_date')</th>
                                                <th>@lang('downloadCenter.valid_upto')</th>
                                                <th>@lang('downloadCenter.shared_by')</th>
                                                <th>@lang('downloadCenter.description')</th>
                                                <th>@lang('common.action')</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        $sharedContents = collect([
                                            (object) [
                                                'id' => 1,
                                                'title' => 'Math Grade 10 Notes',
                                                'send_type' => 'G',
                                                'share_date' => '2025-07-25',
                                                'valid_upto' => '2025-08-10',
                                                'description' => 'Includes algebra and geometry',
                                                'url' => 'https://example.com/shared/math10',
                                                'user' => (object) [
                                                    'full_name' => 'Mr. Ruwan Fernando',
                                                ],
                                            ],
                                            (object) [
                                                'id' => 2,
                                                'title' => 'Science Presentation',
                                                'send_type' => 'I',
                                                'share_date' => '2025-07-26',
                                                'valid_upto' => '2025-08-15',
                                                'description' => null,
                                                'url' => null,
                                                'user' => (object) [
                                                    'full_name' => 'Ms. Dilani Perera',
                                                ],
                                            ],
                                            (object) [
                                                'id' => 3,
                                                'title' => 'ICT Class Notes',
                                                'send_type' => 'C',
                                                'share_date' => '2025-07-20',
                                                'valid_upto' => '2025-08-05',
                                                'description' => 'PDF files for the entire syllabus.',
                                                'url' => 'https://example.com/shared/ict',
                                                'user' => (object) [
                                                    'full_name' => 'Mr. Harsha Madusanka',
                                                ],
                                            ],
                                            (object) [
                                                'id' => 4,
                                                'title' => 'Public Workshop Guide',
                                                'send_type' => 'P',
                                                'share_date' => '2025-07-28',
                                                'valid_upto' => '2025-08-12',
                                                'description' => 'General guide for all attendees.',
                                                'url' => 'https://example.com/shared/workshop',
                                                'user' => (object) [
                                                    'full_name' => 'Admin User',
                                                ],
                                            ],
                                        ]);
                                        
                                        ?>
                                        <tbody>
                                            @foreach ($sharedContents as $key => $sharedContent)
                                                @php
                                                    $send_type = '';
                                                    if ($sharedContent->send_type == 'G') {
                                                        $send_type = 'Group';
                                                    } elseif ($sharedContent->send_type == 'I') {
                                                        $send_type = 'Individual';
                                                    } elseif ($sharedContent->send_type == 'C') {
                                                        $send_type = 'Class';
                                                    } else {
                                                        $send_type = 'Public';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $sharedContent->title }}</td>
                                                    <td>{{ $send_type }}</td>
                                                    <td>{{ $sharedContent->share_date }}</td>
                                                    <td>{{ $sharedContent->valid_upto }}</td>
                                                    <td>{{ $sharedContent->user->full_name }}</td>
                                                    <td>{{ $sharedContent->description ? $sharedContent->description : 'No Description' }}
                                                    </td>
                                                    <td>
                                                        <x-drop-down>
                                                            @if ($sharedContent->url)
                                                                <a class="modalLink dropdown-item"
                                                                    title="@lang('downloadCenter.shared_content_link')" href="/"
                                                                    {{-- href="{{ route('download-center.content-share-link-modal', [$sharedContent->id]) }}" --}}>
                                                                    @lang('downloadCenter.link')
                                                                </a>
                                                            @endif
                                                            <a class="modalLink dropdown-item" data-modal-size="large-modal"
                                                                title="@lang('downloadCenter.view_shared_content')" href="/"
                                                                {{-- href="{{ route('download-center.content-view-link-modal', [$sharedContent->id]) }}" --}}
                                                                >
                                                                @lang('common.view')
                                                            </a>
                                                            {{-- @if (auth()->user()->role_id != 2)
                                                                <a class="dropdown-item" data-toggle="modal"
                                                                    data-target="#deleteModal{{ @$sharedContent->id }}"
                                                                    href="#">
                                                                    @lang('common.delete')
                                                                </a>
                                                            @endif --}}
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#deleteModal{{ @$sharedContent->id }}"
                                                                href="#"
                                                                >
                                                                @lang('common.delete')
                                                            </a>
                                                        </x-drop-down>
                                                    </td>
                                                </tr>
                                                {{-- Start Delete Modal --}}
                                                <div class="modal fade admin-query"
                                                    id="deleteModal{{ @$sharedContent->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">
                                                                    @lang('downloadCenter.delete_shared_content')
                                                                </h4>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    &times;
                                                                </button>
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
                                                                    <a href="/" class="text-light">
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">
                                                                            @lang('common.delete')
                                                                        </button>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- End Delete Modal --}}
                                            @endforeach
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
