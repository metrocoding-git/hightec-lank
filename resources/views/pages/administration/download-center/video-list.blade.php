@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@push('css')
    <style>
        .vidoe-list {
            row-gap: 30px;
        }

        .single-video-item {
            height: 300px;
            width: 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            padding: 20px;
            position: relative;
        }

        .single-video-item::before {
            content: '';
            background: rgba(0, 0, 0, 0.4);
            height: 100%;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
            transition: all 0.4s ease 0s;
            opacity: 0.2;
        }

        .single-video-item:hover::before {
            opacity: 1;
        }

        .single-video-item:hover .video-action-btns {
            display: block;
        }

        .video-action-btns {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 3;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 4px;
            padding: 5px;
            display: none;
        }

        .video-action-btns > ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: center;
            gap: 8px;
        }

        .video-action-btns li {
            margin: 0;
            padding: 0;
            display: inline-block;
        }

        .video-action-btns a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 4px;
            background: #f8f9fa;
            color: #495057;
            transition: all 0.3s ease;
        }

        .video-action-btns a:hover {
            background: #e9ecef;
            color: #000;
            text-decoration: none;
        }

        .single-video-info {
            position: absolute;
            bottom: 25px;
            z-index: 2;
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
            width: 90%;
            left: 5%;
            right: 5%;
        }

        .single-video-item .vidoe-title {
            color: #ffffff;
            font-size: 16px;
            font-weight: 500;
            font-family: inherit;
            text-align: center;
        }

        .video-action-btns ul li a i {
            color: #ffffff;
            transition: 0.4s all ease-in-out;
            font-size: 15px;
        }

        .video-action-btns ul li a:hover i {
            transform: scale(1.5)
        }

        div#showDetaildModalBody iframe {
            width: 100%;
        }
    </style>
@endpush
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('downloadCenter.video') </h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('downloadCenter.download_center')</a>
                    <a href="">@lang('downloadCenter.video')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box filter_card">
                        <div class="row">
                            <div class="col-lg-8 col-md-6 col-sm-6 col-6">
                                <div class="main-title mt_0_sm mt_0_md">
                                    <h3 class="mb-15">@lang('common.search')</h3>
                                </div>
                            </div>
                            <div class="col-lg-4 text-md-right col-md-6 mb-30-lg col-6 text-right">
                                <button class="primary-btn-small-input primary-btn small fix-gr-bg" type="button"
                                    data-toggle="modal" data-target="#addVideo">
                                    <span class="ti-plus pr-2"></span>
                                    @lang('common.add')
                                </button>
                            </div>
                        </div>
                        {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'download-center.video-list-search', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }} --}}
                        <form class="form-horizontal">
                            <div class="row">
                                <!-- @include('components.common.class_section_subject', [
                                    'div' => 'col-lg-4',
                                    'visiable' => ['class', 'section'],
                                ]) -->

                                <div class="col-lg-4">
                                    <div class="primary_input sm_mb_20 ">
                                        <label class="primary_input_label" for="">
                                            @lang('downloadCenter.title') 
                                        </label>
                                        <input class="primary_input_field" type="text" placeholder="@lang('downloadCenter.title')"
                                            name="title">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 text-right mt-20">
                                    <button type="submit" class="primary-btn small fix-gr-bg">
                                        <span class="ti-search pr-2"></span>
                                        @lang('common.search')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row mt-40">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12 white-box mt-10">
                            <div class="row">
                                <div class="col-lg-4 no-gutters">
                                    <div class="main-title">
                                        <h3 class="mb-15">@lang('downloadCenter.video_list')</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row vidoe-list">
                                @foreach ($videos as $video)
                                    @php
                                        // Extract YouTube video ID from saved link
                                        $ytId = null;
                                        $parts = parse_url($video->video_link ?? '');
                                        if (isset($parts['query'])) {
                                            parse_str($parts['query'], $q);
                                            $ytId = $q['v'] ?? null;
                                        }
                                        if (!$ytId) {
                                            $path = $parts['path'] ?? '';
                                            $segments = explode('/', trim($path, '/'));
                                            $ytId = end($segments);
                                        }
                                    @endphp
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                        <div class="single-video-item"
                                            style="background-image: url(http://img.youtube.com/vi/{{ $ytId }}/hqdefault.jpg); height: 300px" >
                                            <div class="single-video-info ">
                                                <div class="vidoe-title text-center text-white text-xl font-bold">
                                                    {{ $video->title }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="video-action-btns">
                                            <ul style="display: flex ; padding: 20px;">
                                                <li>
                                                    <a href="#" class="viewVideoBtn" title="@lang('downloadCenter.view_video')"
                                                       data-title="{{ $video->title }}"
                                                       data-video_link="{{ $video->video_link }}"
                                                       data-intake="{{ optional($video->intake)->name }}"
                                                       data-courses='@json($video->courses->pluck("name"))'>
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </li>
                                            
                                                    <li>
                                                        <a href="#" role="button" class="editVideoBtn" data-modal-size="large-modal"
                                                            title="@lang('downloadCenter.edit_video')"
                                                            data-id="{{ $video->id }}"
                                                            data-intake_id="{{ $video->intake_id }}"
                                                            data-title="{{ $video->title }}"
                                                            data-video_link="{{ $video->video_link }}"
                                                            data-description="{{ $video->description }}"
                                                            data-course-ids='@json($video->courses->pluck("id"))'>
                                                            <i class="fas fa-pen"></i>
                                                        </a>
                                                    </li>
                                                
                                            
                                                    <li>
                                                        <a data-toggle="modal"
                                                            data-target="#deleteModal{{ @$video->id }}"
                                                            href="#">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </li>
                                                
                                            </ul>
                                        </div>
                                    </div>
                                    {{-- Start Delete Modal --}}
                                    <div class="modal fade admin-query" id="deleteModal{{ @$video->id }}">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">
                                                        @lang('downloadCenter.delete_video')
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
                                                        <form action="{{ route('download-center.video-list-destroy', $video->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="primary-btn fix-gr-bg" type="submit">
                                                                @lang('common.delete')
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- End Delete Modal --}}
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Start Video Add Modal -->
    <div class="modal fade admin-query" id="addVideo">
        <div class="modal-dialog modal-dialog-centered large-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('downloadCenter.add_video')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'download-center.video-list-save', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }} --}}
                <form class="form-horizontal" id="add-video-form" action="{{ route('download-center.video-list-save') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <!-- @include('components.common.search_criteria', [
                                        'div' => 'col-lg-6',
                                        'required' => ['class', 'section'],
                                        'visiable' => ['class', 'section'],
                                    ]) -->
                                    <!-- adding intake and course dropdown  -->

                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <select
                                                    class="primary_select form-control"
                                                    name="intake_id" id="video_intake_id">
                                                    <option data-display="@lang('common.intake') *" value="">
                                                        @lang('common.intake') *
                                                    </option>
                                                    @if (isset($intakes))
                                                        @foreach ($intakes as $intake)
                                                            <option value="{{ $intake->id }}">{{ $intake->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-lg-12 mt-30" id="video_selectSectionsDiv" style="display:none;">
                                                <label for="video_course_ids" class="mb-2">@lang('common.course')
                                                    <span class="text-danger">*</span></label>
                                                <select multiple
                                                    class="multypol_check_select active position-relative"
                                                    id="video_course_ids" name="course_ids[]"
                                                    style="width:300px"></select>
                                            </div>
                                        </div>
                                    </div>
 
 
 
                                     <!-- end -->
                                     <div class="col-lg-6 mt-30">
                                         <div class="primary_input">
                                             <label class="primary_input_label" for="">@lang('downloadCenter.title')<span
                                                     class="text-danger"> *</span></label>
                                             <input
                                                 class="primary_input_field read-only-input form-control form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
                                                 type="text" name="title" id="title">
                                             <span class="text-danger" id="titleError">
                                             </span>
                                         </div>
                                         @if ($errors->has('title'))
                                             <span class="text-danger">
                                                 {{ $errors->first('title') }}
                                             </span>
                                         @endif
                                     </div>
                                    <div class="col-lg-6 mt-30">
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">@lang('downloadCenter.video_link')<span
                                                    class="text-danger"> *</span></label>
                                            <input
                                                class="primary_input_field read-only-input form-control form-control{{ $errors->has('video_link') ? ' is-invalid' : '' }}"
                                                type="text" name="video_link" id="video_link">
                                            <span class="text-danger" id="videoLinkError">
                                            </span>
                                        </div>
                                        @if ($errors->has('video_link'))
                                            <span class="text-danger">
                                                {{ $errors->first('video_link') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-25">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="primary_input">
                                            <label class="primary_input_label"
                                                for="">@lang('downloadCenter.description')<span></span> </label>
                                            <textarea class="primary_input_field form-control" cols="0" rows="3" name="description"
                                                id="description"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 text-center mt-25">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="primary-btn tr-bg"
                                        data-dismiss="modal">@lang('downloadCenter.cancel')</button>
                                    <button class="primary-btn fix-gr-bg submit" id="save_button_query"
                                        type="submit">@lang('downloadCenter.save')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Video Add Modal -->
    <!-- Start Video Edit Modal (Dedicated) -->
    <div class="modal fade admin-query" id="editVideo" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered large-modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('downloadCenter.edit_video')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <form class="form-horizontal" id="edit-video-form" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <select class="primary_select form-control" name="intake_id" id="edit_video_intake_id">
                                                <option data-display="@lang('common.intake') *" value="">@lang('common.intake') *</option>
                                                @if (isset($intakes))
                                                    @foreach ($intakes as $intake)
                                                        <option value="{{ $intake->id }}">{{ $intake->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <span class="text-danger" id="editVideoIntakeError"></span>
                                        </div>
                                        <div class="col-lg-12 mt-30" id="edit_video_selectSectionsDiv" style="display:none;">
                                            <label for="edit_video_course_ids" class="mb-2">@lang('common.course') <span class="text-danger">*</span></label>
                                            <select multiple class="multypol_check_select active position-relative" id="edit_video_course_ids" name="course_ids[]" style="width:300px"></select>
                                            <span class="text-danger" id="editVideoCoursesError"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-30">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="">@lang('downloadCenter.title')<span class="text-danger"> *</span></label>
                                        <input class="primary_input_field read-only-input form-control" type="text" name="title" id="edit_title">
                                        <span class="text-danger" id="editTitleError"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-30">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="">@lang('downloadCenter.video_link')<span class="text-danger"> *</span></label>
                                        <input class="primary_input_field read-only-input form-control" type="text" name="video_link" id="edit_video_link">
                                        <span class="text-danger" id="editVideoLinkError"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-25">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="">@lang('downloadCenter.description')</label>
                                        <textarea class="primary_input_field form-control" cols="0" rows="3" name="description" id="edit_description"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12 text-center mt-25">
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('downloadCenter.cancel')</button>
                                        <button class="primary-btn fix-gr-bg submit" id="edit_save_button" type="submit">@lang('common.update')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Video Edit Modal -->
    {{-- View Details Modal --}}
    <div class="modal fade admin-query" id="viewVideoModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered large-modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="viewVideoModalTitle">@lang('downloadCenter.view_video')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="showDetaildModalBody">
                        <div class="embed-responsive embed-responsive-16by9 mb-3">
                            <iframe id="viewVideoIframe" class="embed-responsive-item" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 id="viewVideoTitle" class="mb-2"></h5>
                                <ul class="list-unstyled mb-0">
                                    {{-- <li><strong>@lang('common.intake'):</strong> <span id="viewVideoIntake">-</span></li>
                                    <li><strong>@lang('common.course'):</strong> <span id="viewVideoCourses">-</span></li> --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('components.partials.multi_select_js')
@push('script')
<script>
    // Intake -> Courses dependent dropdown for Add Video modal
    (function () {
        const intakeSelect = '#video_intake_id';
        const courseSelect = '#video_course_ids';
        const courseContainer = '#video_selectSectionsDiv';

        function ensureMultiSelectInitialized() {
            if ($.fn.multiselect) {
                const instance = $(courseSelect).data('plugin_multiselect-instance');
                if (!instance) {
                    $(courseSelect).multiselect({
                        columns: 1,
                        placeholder: 'Select',
                        search: true,
                        searchOptions: { delay: 250, showOptGroups: false, searchText: true, searchValue: false },
                        selectAll: true
                    });
                }
            }
        }

        function disableCoursesUI() {
            if ($.fn.multiselect) {
                ensureMultiSelectInitialized();
                $(courseSelect).multiselect('disable', true);
            }
            $(courseSelect).prop('disabled', true);
            $(courseContainer).hide();
        }

        function enableCoursesUI() {
            if ($.fn.multiselect) {
                ensureMultiSelectInitialized();
                $(courseSelect).multiselect('disable', false);
            }
            $(courseSelect).prop('disabled', false);
            $(courseContainer).show();
        }

        function clearCourses() {
            $(courseSelect).empty();
            if ($.fn.multiselect) {
                ensureMultiSelectInitialized();
                $(courseSelect).multiselect('reload');
            }
            disableCoursesUI();
        }

        function populateCourses(items) {
            // keep container visible but disabled until options added
            $(courseContainer).show();
            $(courseSelect).empty();
            items.forEach(function (item) {
                $(courseSelect).append(new Option(item.name, item.id, false, false));
            });
            if ($.fn.multiselect) {
                ensureMultiSelectInitialized();
                $(courseSelect).multiselect('reload');
            }
            if (items.length > 0) {
                enableCoursesUI();
            } else {
                disableCoursesUI();
            }
            // Notify listeners that course options are ready
            $(courseSelect).trigger('courses:loaded');
        }

        function loadCourses(intakeId) {
            if (!intakeId) {
                clearCourses();
                return;
            }
            const url = "{{ url('administration/download-center/get-courses') }}" + '/' + intakeId;
            disableCoursesUI();
            $.get(url)
                .done(function (resp) {
                    if (resp && resp.status && Array.isArray(resp.data)) {
                        populateCourses(resp.data);
                    } else if (Array.isArray(resp)) { // fallback if other endpoint shape
                        populateCourses(resp);
                    } else {
                        clearCourses();
                    }
                })
                .fail(function () {
                    clearCourses();
                });
        }

        // Bind when modal opens to ensure DOM exists
        $('#addVideo').on('shown.bs.modal', function () {
            ensureMultiSelectInitialized();
            const selectedIntake = $(intakeSelect).val();
            if (selectedIntake) {
                loadCourses(selectedIntake);
            } else {
                clearCourses();
            }
        });

        $(document).on('change', intakeSelect, function () {
            loadCourses($(this).val());
        });

        // Initialize hidden/disabled state on page load
        disableCoursesUI();
    })();
</script>
<script>
    // Handle Add Video form submit via AJAX
    $(document).on('submit', '#add-video-form', function (e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr('action');
        // Prevent submit while courses are loading
        if ($('#save_button_query').data('loading-courses') === true) {
            return alert('Please wait, loading courses...');
        }
        // Basic guard for edit: require at least one course selected
        if ($('#video_form_method').val() === 'PUT') {
            const selected = $('#video_course_ids').val() || [];
            if (selected.length === 0) {
                $('#videoCoursesError').text('Please select at least one course');
                return;
            }
        }
        const data = {
            _method: $('#video_form_method').val(),
            intake_id: $('#video_intake_id').val(),
            title: $('#title').val(),
            video_link: $('#video_link').val(),
            description: $('#description').val(),
            'course_ids': $('#video_course_ids').val() || []
        };

        // clear previous errors
        $('#titleError').text('');
        $('#videoLinkError').text('');

        $('#save_button_query').prop('disabled', true);
        $.ajax({
            url: url,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: data,
            success: function (resp) {
                if (resp && resp.status) {
                    // reset form and UI
                    form[0].reset();
                    // reset courses select
                    $('#video_course_ids').val([]);
                    if ($.fn.multiselect) {
                        $('#video_course_ids').multiselect('reload');
                    }
                    // close modal
                    $('#addVideo').modal('hide');
                    // optional: reload list
                    location.reload();
                } else {
                    alert(resp.message || 'Failed to save');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errs = xhr.responseJSON.errors;
                    if (errs.title) $('#titleError').text(errs.title[0]);
                    if (errs.video_link) $('#videoLinkError').text(errs.video_link[0]);
                } else {
                    alert('Error saving video');
                }
            },
            complete: function () {
                $('#save_button_query').prop('disabled', false);
            }
        });
    });
</script>
<script>
    // Improve icon styling slightly
    $(function(){
        $('.video-action-btns ul li a i').css({fontSize:'16px', margin:'0 4px'});
    });

    function extractYouTubeId(url) {
        try {
            const u = new URL(url);
            if (u.hostname.includes('youtu.be')) {
                return u.pathname.replace('/', '');
            }
            if (u.searchParams.has('v')) {
                return u.searchParams.get('v');
            }
            // last fallback: take last path segment
            const parts = u.pathname.split('/').filter(Boolean);
            return parts.length ? parts.pop() : '';
        } catch (e) {
            return '';
        }
    }

    // Handle View button: open modal with embedded player and details
    $(document).on('click', '.viewVideoBtn', function(e){
        e.preventDefault();
        const title   = $(this).data('title') || '';
        const link    = $(this).data('video_link') || '';
        const intake  = $(this).data('intake') || '-';
        let courses   = $(this).data('courses') || [];
        if (!Array.isArray(courses)) { try { courses = JSON.parse(courses); } catch (_) { courses = []; } }

        const ytId = extractYouTubeId(link);
        const embed = ytId ? 'https://www.youtube.com/embed/' + ytId : '';

        $('#viewVideoModalTitle').text(title);
        $('#viewVideoTitle').text(title);
        $('#viewVideoIntake').text(intake || '-');
        $('#viewVideoCourses').text(courses.length ? courses.join(', ') : '-');
        $('#viewVideoIframe').attr('src', embed);

        $('#viewVideoModal').modal('show');
    });

    // Stop video when modal closes
    $('#viewVideoModal').on('hidden.bs.modal', function(){
        $('#viewVideoIframe').attr('src','');
    });
</script>
<script>
    // ----- Edit Modal: Intake -> Courses dependent dropdown and form handling -----
    (function(){
        const intakeSelect = '#edit_video_intake_id';
        const courseSelect = '#edit_video_course_ids';
        const courseContainer = '#edit_video_selectSectionsDiv';

        function ensureMultiSelectInitialized() {
            if ($.fn.multiselect) {
                const instance = $(courseSelect).data('plugin_multiselect-instance');
                if (!instance) {
                    $(courseSelect).multiselect({ columns: 1, placeholder: 'Select', search: true, searchOptions: { delay: 250, showOptGroups: false, searchText: true, searchValue: false }, selectAll: true });
                }
            }
        }
        function disableCoursesUI() {
            if ($.fn.multiselect) { ensureMultiSelectInitialized(); $(courseSelect).multiselect('disable', true); }
            $(courseSelect).prop('disabled', true);
            $(courseContainer).hide();
        }
        function enableCoursesUI() {
            if ($.fn.multiselect) { ensureMultiSelectInitialized(); $(courseSelect).multiselect('disable', false); }
            $(courseSelect).prop('disabled', false);
            $(courseContainer).show();
        }
        function clearCourses() {
            $(courseSelect).empty();
            if ($.fn.multiselect) { ensureMultiSelectInitialized(); $(courseSelect).multiselect('reload'); }
            disableCoursesUI();
        }
        function populateCourses(items) {
            $(courseContainer).show();
            $(courseSelect).empty();
            items.forEach(function (item) { $(courseSelect).append(new Option(item.name, item.id, false, false)); });
            if ($.fn.multiselect) { ensureMultiSelectInitialized(); $(courseSelect).multiselect('reload'); }
            if (items.length > 0) enableCoursesUI(); else disableCoursesUI();
            $(courseSelect).trigger('courses:loaded');
        }
        function loadCourses(intakeId) {
            if (!intakeId) { clearCourses(); return; }
            const url = "{{ url('administration/download-center/get-courses') }}" + '/' + intakeId;
            disableCoursesUI();
            $.get(url)
             .done(function(resp){
                if (resp && resp.status && Array.isArray(resp.data)) populateCourses(resp.data);
                else if (Array.isArray(resp)) populateCourses(resp);
                else clearCourses();
             })
             .fail(function(){ clearCourses(); });
        }

        // Bind change handler
        $(document).on('change', intakeSelect, function(){ loadCourses($(this).val()); });

        // Open edit modal and prefill
        $(document).on('click', '.editVideoBtn', function(e){
            e.preventDefault();
            const id          = $(this).data('id');
            const intakeId    = $(this).data('intake_id');
            const title       = $(this).data('title');
            const videoLink   = $(this).data('video_link');
            const description = $(this).data('description') || '';
            let courseIds     = $(this).data('courseIds') || [];
            if (!Array.isArray(courseIds)) { try { courseIds = JSON.parse(courseIds); } catch (_) { courseIds = []; } }

            // Set form action for update
            $('#edit-video-form').attr('action', '{{ url('administration/download-center/video-list-update') }}' + '/' + id);

            // Prefill simple fields
            $('#edit_title').val(title);
            $('#edit_video_link').val(videoLink);
            $('#edit_description').val(description);

            // When courses are loaded, select existing values
            $('#edit_video_course_ids').one('courses:loaded', function(){
                const $sel = $('#edit_video_course_ids');
                $sel.val(courseIds.map(String));
                if ($.fn.multiselect) { $sel.multiselect('reload'); }
                $('#edit_video_selectSectionsDiv').show();
            });

            // Show modal first, then set intake and trigger change to load courses
            $('#editVideo')
                .off('shown.bs.modal.editIntakeOnce')
                .on('shown.bs.modal.editIntakeOnce', function(){
                    const $intake = $('#edit_video_intake_id');
                    const valueToSet = String(intakeId ?? '');

                    // Explicitly select the correct option
                    $intake.find('option').prop('selected', false);
                    $intake.find('option[value="'+ valueToSet +'"]').prop('selected', true);
                    $intake.val(valueToSet);

                    // Refresh common select plugins if present
                    if ($.fn.select2 && $intake.data('select2')) {
                        $intake.trigger('change.select2'); // updates UI and fires change
                    } else {
                        if ($.fn.niceSelect) {
                            try { $intake.niceSelect('update'); } catch (e) {}
                        }
                        // Fire change to load courses via our handler
                        $intake.trigger('change');
                    }

                    // Remove this one-time handler
                    $(this).off('shown.bs.modal.editIntakeOnce');
                })
                .modal('show');
        });

        // Handle edit submit via AJAX PUT
        $(document).on('submit', '#edit-video-form', function(e){
            e.preventDefault();
            const form = $(this);
            const url  = form.attr('action');
            const data = {
                _method: 'PUT',
                intake_id: $('#edit_video_intake_id').val(),
                title: $('#edit_title').val(),
                video_link: $('#edit_video_link').val(),
                description: $('#edit_description').val(),
                'course_ids': $('#edit_video_course_ids').val() || []
            };

            // Clear errors
            $('#editTitleError').text('');
            $('#editVideoLinkError').text('');
            $('#editVideoIntakeError').text('');
            $('#editVideoCoursesError').text('');

            $('#edit_save_button').prop('disabled', true);
            $.ajax({
                url: url,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: data,
                success: function(resp){
                    if (resp && resp.status) {
                        $('#editVideo').modal('hide');
                        location.reload();
                    } else {
                        alert((resp && resp.message) ? resp.message : 'Failed to update');
                    }
                },
                error: function(xhr){
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errs = xhr.responseJSON.errors;
                        if (errs.title) $('#editTitleError').text(errs.title[0]);
                        if (errs.video_link) $('#editVideoLinkError').text(errs.video_link[0]);
                        if (errs.intake_id) $('#editVideoIntakeError').text(errs.intake_id[0]);
                        if (errs['course_ids']) $('#editVideoCoursesError').text(errs['course_ids'][0]);
                    } else {
                        let msg = 'Error updating video';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        alert(msg);
                    }
                },
                complete: function(){
                    $('#edit_save_button').prop('disabled', false);
                }
            });
        });
    })();
</script>
@endpush
