@extends('layouts.app')
@push('css')
    <style>
        .student_rec_card {
            border-radius: 6px;
            border: 1px solid var(--border_color);
            width: 100%;
        }

        .student_rec_header {
            padding: 12px;
            background: -webkit-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -moz-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -o-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -ms-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
        }

        .student_rec_footer {
            padding: 12px;
            margin-top: 16px;
            border-top: 1px solid var(--border_color);
        }

        .student_rec_content {
            padding: 16px;
            max-height: 300px;
            min-height: 300px;
        }
    </style>
@endpush

@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('academics.subject')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('academics.academics')</a>
                    <a href="#">@lang('academics.subject')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <div class="row">
                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">

                            @if (isset($subjectId))
                                {{-- Update form --}}
                                <form class="form-horizontal" action="{{ route('subject.update', $subject->id) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @method('POST') {{-- Or PATCH/PUT depending on your route --}}
                                @else
                                    {{-- Store form --}}
                                    {{-- You can remove this check if not needed --}}
                                    <form class="form-horizontal" action="{{ route('subject.store') }}" method="POST"
                                        enctype="multipart/form-data">
                            @endif

                            @csrf

                            <div class="white-box">
                                <div class="main-title">
                                    <h3 class="mb-20">
                                        @if (isset($subjectId))
                                            @lang('academics.update_subject')
                                        @else
                                            @lang('academics.add_subject')
                                        @endif

                                    </h3>
                                </div>
                                <div class="add-visitor">
                                    {{-- Subject Name --}}
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="name">@lang('academics.subject_name')
                                                    <span>*</span></label>
                                                <input
                                                    class="primary_input_field form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                                    type="text" name="name" id="name" autocomplete="off"
                                                    value="{{ old('name', $subject->name ?? '') }}">
                                                @if ($errors->has('name'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Subject Type --}}
                                    <div class="row mt-20">
                                        <div class="col-lg-12">
                                            <label class="primary_input_label">@lang('academics.subject_type') <span>*</span></label>
                                            <div class="d-flex radio-btn-flex">
                                                <div class="mr-30">
                                                    <input type="radio" name="type" id="theory" value="T"
                                                        class="common-radio relationButton"
                                                        {{ old('type', $subject->type ?? 'T') == 'T' ? 'checked' : '' }}>
                                                    <label for="theory">@lang('academics.theory')</label>
                                                </div>
                                                <div class="mr-30">
                                                    <input type="radio" name="type" id="practical" value="P"
                                                        class="common-radio relationButton"
                                                        {{ old('type', $subject->type ?? '') == 'P' ? 'checked' : '' }}>
                                                    <label for="practical">@lang('academics.practical')</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Subject Code --}}
                                    <div class="row mt-20">
                                        <div class="col-lg-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="code">@lang('academics.subject_code')
                                                    <span>*</span></label>
                                                <input
                                                    class="primary_input_field form-control{{ $errors->has('code') ? ' is-invalid' : '' }}"
                                                    type="text" name="code" id="code" autocomplete="off"
                                                    value="{{ old('code', $subject->code ?? '') }}">
                                                @if ($errors->has('code'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('code') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Submit --}}
                                    <div class="row mt-20">
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg submit" type="submit">
                                                <span class="ti-check"></span>
                                                {{ isset($subjectId) ? __('common.update') : __('academics.save_class') }}
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div class="col-lg-9">

                    <div class="row white-box">
                        <div class="col-lg-12">

                            <div class="main-title">
                                <h3 class="mb-20">@lang('academics.subject_list')</h3>
                            </div>

                            <x-table>
                                <table id="table_id" class="table Crm_table_active3" cellspacing="0" width="100%">

                                    <thead>

                                        <tr>
                                            <th> @lang('common.sl')</th>
                                            <th> @lang('academics.subject')</th>
                                            <th> @lang('academics.subject_type')</th>
                                            <th>@lang('academics.subject_code')</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subjects as $key => $subject)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $subject->name }}</td>
                                                <td>
                                                    {{ $subject->type == 'T' ? __('academics.theory') : __('academics.practical') }}
                                                </td>
                                                <td>
                                                    <a href="#">{{ $subject->code }}</a>
                                                </td>
                                                <td valign="top">
                                                    @php
                                                        $routeList = [
                                                            '<a class="dropdown-item" href="' .
                                                            route('subject.edit', $subject->id) .
                                                            '">' .
                                                            __('common.edit') .
                                                            '</a>',
                                                            '<a class="dropdown-item" data-toggle="modal" data-target="#deleteClassModal' .
                                                            $subject->id .
                                                            '" href="#">' .
                                                            __('common.delete') .
                                                            '</a>',
                                                        ];
                                                    @endphp
                                                    <x-drop-down-action-component :routeList="$routeList" />
                                                </td>
                                            </tr>

                                            {{-- Delete Modal --}}
                                            <div class="modal fade admin-query" id="deleteClassModal{{ $subject->id }}">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">@lang('academics.delete_class')</h4>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal">&times;</button>
                                                        </div>

                                                        <div class="modal-body text-center">
                                                            <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                            <div class="mt-40 d-flex justify-content-between">
                                                                <button type="button" class="primary-btn tr-bg"
                                                                    data-dismiss="modal">@lang('common.cancel')</button>
                                                                <form action="{{ route('subject.destroy', $subject->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button class="primary-btn fix-gr-bg"
                                                                        type="submit">@lang('common.delete')</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </x-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@include('components.partials.data_table_js')
@push('script')
    {{-- <script>
    $(document).on("change", ".assignedClassSection", function() {
        var id = $(this).val();
        var url = '{{url('/')}}';
        var formData = {
            assignedClass: $(this).val(),
        };
        $.ajax({
                type:'GET',
                data: formData,
                dataType:"json",
                url: "{{route('loadAssignedSubject')}}",
                success:function(data){
                    $('#classSectionWiseSubjects_'+ data.class_id).html(data.html);
                    $('#classSectionWiseStudyMat_'+ data.class_id).html(data.html2);
                    
                },
                error:function(error){
                    console.log(error);
                }
            });
    });

    $('.primary_select').niceSelect('update');

     function deleteSubject(id){
        alert(id);
        $(".assignedSubject"+id).remove();
     }


     $(document).on('click', '.saveAssignedSubject', function(event) 
        {
                var submit_btn = $(this).find("button[type=submit]");
                event.preventDefault();
                var url = $("#url").val();
                var class_id = $(this).data('class_id');
                var formData = $("#form_"+class_id).serialize();
                console.log(formData);
                    $.ajax({
                        type: "POST",                   
                        data:formData, 
                        dataType: "json",
                        url: "{{route('saveAssignedSubject')}}" ,
                        beforeSend: function() {
                            submit_btn.button('loading');
                        },
                        success: function(data) {
                            if (data.status == true) {
                                toastr.success(data.message, 'Success');                           
                            } else {
                                toastr.error(data.message, 'Error'); 
                            }
                            submit_btn.button('reset');
                        },
                        error: function(xhr) { 
                            toastr.error("Error occured. please try again", "Error");
                        },
                        complete: function() {
                            submit_btn.button('reset');
                        }
                    });
        });

        function addMoreGlobalSubject(id){
            var url = $("#url").val();
            var count = $("#assign-subject").children().length;
            var divCount = count + 1;

            // get section for student
            $.ajax({
                type: "GET",
                dataType: "json",
                url: url + "/" + "global-assign-subject-get-by-ajax",
                success: function(data) {
                    var subject_teacher = "";
                    subject_teacher +=
                        "<div class='assignedSubject" +id + "' id='assign-subject-" +
                        divCount +
                        "'>";
                    subject_teacher += "<div class='row'>";
                    subject_teacher += "<div class='col-lg-5 mt-30-md'>";
                    subject_teacher +=
                        "<select class='primary_select' name='subjects[]' style='display:none'>";
                    subject_teacher +=
                        "<option data-display='"+window.jsLang('select_subject')+"'  value=''>"+window.jsLang('select_subject')+"</option>";
                    $.each(data[0], function(key, subject) {
                        subject_teacher +=
                            "<option value=" +
                            subject.id +
                            ">" +
                            subject.subject_name +
                            "</option>";
                    });
                    subject_teacher += "</select>";

                    subject_teacher +=
                        "<div class='nice-select primary_select form-control' tabindex='0'>";
                    subject_teacher += "<span class='current'>"+window.jsLang('select_subject')+"</span>";
                    subject_teacher +=
                        "<div class='nice-select-search-box'><input type='text' class='nice-select-search' placeholder='Search...'></div>";
                    subject_teacher += "<ul class='list'>";
                    subject_teacher +=
                        "<li data-value='' data-display='"+window.jsLang('select_subject')+"' class='option selected'>"+window.jsLang('select_subject')+"</li>";
                    $.each(data[0], function(key, subject) {
                        subject_teacher +=
                            "<li data-value=" +
                            subject.id +
                            " class='option'>" +
                            subject.subject_name +
                            "</li>";
                    });
                    subject_teacher += "</ul>";
                    subject_teacher += "</div>";
                    subject_teacher += "</div>";
                    subject_teacher += "<div class='col-lg-5 mt-30-md'>";
                    subject_teacher +=
                        "<select class='primary_select form-control' name='teachers[]' style='display:none'>";
                    subject_teacher +=
                        "<option data-display='"+window.jsLang('select_teacher')+"' value=''>"+window.jsLang('select_teacher')+"</option>";
                    $.each(data[1], function(key, teacher) {
                        subject_teacher +=
                            "<option value=" +
                            teacher.id +
                            ">" +
                            teacher.full_name +
                            "</option>";
                    });
                    subject_teacher += "</select>";
                    subject_teacher +=
                        "<div class='nice-select primary_select form-control' tabindex='0'>";
                    subject_teacher += "<span class='current'>"+window.jsLang('select_teacher')+"</span>";
                    subject_teacher +=
                        "<div class='nice-select-search-box'><input type='text' class='nice-select-search' placeholder='Search...'></div>";
                    subject_teacher += "<ul class='list'>";
                    subject_teacher +=
                        "<li data-value='' data-display='"+window.jsLang('select_teacher')+"' class='option selected'>"+window.jsLang('select_teacher')+"</li>";
                    $.each(data[1], function(key, teacher) {
                        subject_teacher +=
                            "<li data-value=" +
                            teacher.id +
                            " class='option'>" +
                            teacher.full_name +
                            "</li>";
                    });
                    subject_teacher += "</ul>";
                    subject_teacher += "</div>";
                    subject_teacher += "</div>";
                    subject_teacher += "<div class='col-lg-2'>";
                    subject_teacher +=
                        "<button class='primary-btn icon-only fix-gr-bg id='removeSubject' onclick='deleteSubject(" +id + ")' type='button'>";
                    subject_teacher += "<span class='ti-trash' ></span>";
                    subject_teacher += "</button>";
                    subject_teacher += "</div>";
                    subject_teacher += "</div>";
                    subject_teacher += "</div>";
                    $("#assign-subject_"+id).append(subject_teacher);
                },
                error: function(data) {
                    // console.log("Error:", data);
                },
            });
        }

</script> --}}
@endpush
