@extends("layouts.app")
@section("title")
    @lang("academics.About")
@endsection
@section("mainContent")
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang("student.manage_student")</h1>
                <div class="bc-pages">
                    <a href="{{ route("dashboard") }}">@lang("common.dashboard")</a>
                    <a href="#">@lang("student.student_information")</a>
                    <a href="#">@lang("student.student_list")</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'student-list-search', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'infix_form']) }} --}}
            <form class="form-horizontal">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white-box filter_card">
                            <div class="row">
                                <div class="col-lg-8 col-md-6 col-sm-6">
                                    <div class="main-title mt_0_sm mt_0_md">
                                        <h3 class="mb-15">@lang("common.select_criteria")</h3>
                                    </div>
                                </div>


                                <div class="col-lg-4 col-md-6 col-sm-6 text-left text-sm-right mb-4 mb-sm-0">
                                    <a href="{{ route("add-student.index") }}" class="primary-btn small fix-gr-bg">
                                        <span class="ti-plus pr-2"></span>
                                        @lang("student.add_student")
                                    </a>
                                </div>

                            </div>
                            <div class="row row-gap-24">
                                <input type="hidden" name="url" id="url" value="{{ URL::to("/") }}">


                                @include("components.common.search_criteria", [
                                    "mt" => "mt-0",
                                    "div" => "col-lg-4 col-md-6",
                                    "required" => ["academic"],
                                    "visiable" => ["academic", "class", "section"],
                                ])
                                <div class="col-lg-4 col-md-6">
                                    <div class="primary_input sm_mb_20 ">
                                        <label class="primary_input_label" for="">@lang("student.search_by_name")</label>

                                        <input class="primary_input_field" type="text" placeholder="Name" name="name"
                                            value="{{ isset($name) ? $name : old("name") }}">

                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="primary_input sm_mb_20 ">
                                        <label class="primary_input_label" for="">@lang("student.search_by_roll")</label>
                                        <input class="primary_input_field" type="text" placeholder="Roll" name="roll_no"
                                            value="{{ isset($roll_no) ? $roll_no : old("roll_no") }}">


                                    </div>
                                </div>

                                <div class="col-lg-12 mt-20 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg" id="btnsubmit">
                                        <span class="ti-search pr-2"></span>
                                        @lang("common.search")
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="academic_id" value="{{ @$academic_year }}">
                <input type="hidden" id="class" value="{{ @$class_id }}">
                <input type="hidden" id="section" value="{{ @$section }}">
                <input type="hidden" id="roll" value="{{ @$roll_no }}">
                <input type="hidden" id="name" value="{{ @$name }}">
                <input type="hidden" id="un_session" value="{{ @$data["un_session_id"] }}">
                <input type="hidden" id="un_academic" value="{{ @$data["un_academic_id"] }}">
                <input type="hidden" id="un_faculty" value="{{ @$data["un_faculty_id"] }}">
                <input type="hidden" id="un_department" value="{{ @$data["un_department_id"] }}">
                <input type="hidden" id="un_semester_label" value="{{ @$data["un_semester_label_id"] }}">
                <input type="hidden" id="un_section" value="{{ @$data["un_section_id"] }}">
            </form>
            {{-- @if (@$students) --}}
            <div class="row mt-40 full_wide_table">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang("student.student_list")</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id"
                                        class="table data-table Crm_table_active3 no-footer dtr-inline collapsed"
                                        cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>@lang("student.admission_no")</th>
                                                <th>@lang("student.name")</th>

                                                <th>@lang("student.date_of_birth")</th>

                                                <th>@lang("student.class_sec")</th>

                                                <th>@lang("common.gender")</th>
                                                <th>@lang("common.type")</th>
                                                <th>@lang("common.phone")</th>
                                                <th>@lang("common.actions")</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($students as $student)
                                                <tr>
                                                    <td>{{ $student->admission_number }}</td>
                                                    <td>{{ $student->student_name }}</td>
                                                    <td>{{ $student->date_of_birth }}</td>
                                                    <td>-</td>
                                                    <td>{{ $student->gender->name }}</td>
                                                    <td>{{ $student->studentCategory->category_name }}</td>
                                                    <td>{{ $student->phone_number }}</td>
                                                    <td valign="top">
                                                        @php
                                                            $studentLink = $student->is_active
                                                                ? "student.disable"
                                                                : "student.enable";
                                                            $routeList = [
                                                                '<a class="dropdown-item" data-toggle="modal" data-target="#statusUpdateModal' .
                                                                $student->id .
                                                                '" href="' .
                                                                route("student.update-status", $student) .
                                                                '">' .
                                                                __("common.disable") .
                                                                "</a>",
                                                            ];
                                                        @endphp
                                                        <x-drop-down-action-component :routeList="$routeList" />
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td></td>
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
            {{-- @endif --}}
        </div>
    </section>
    {{-- disable student  --}}
    <div class="modal fade admin-query" id="deleteStudentModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang("student.disable_student")</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <h4>@lang("student.are_you_sure_to_disable")</h4>
                    </div>
                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang("common.cancel")</button>
                        {{-- {{ Form::open(['route' => 'student-delete', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }} --}}
                        <form class="">
                            <input type="hidden" name="id" value="{{ @$student->id }}" id="student_delete_i">
                            {{-- using js in main.js --}}
                            <button class="primary-btn fix-gr-bg" type="submit">@lang("common.disable")</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade admin-query" id="statusUpdateModal{{ $student->id }}">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang("academics.delete_class")</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <h4>@lang("common.are_you_sure_to_update_student_status")</h4>
                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang("common.cancel")</button>
                        <form action="{{ route("student.update-status", $student->id) }}" method="POST">
                            @csrf
                            @method("patch")
                            <button class="primary-btn fix-gr-bg" type="submit">@lang("common.update")</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        if (isset($academic_year) || isset($class_id)) {
            $ajax_url = url(
                "student-list-datatable?academic_year=" .
                    $academic_year .
                    "&class=" .
                    $class_id .
                    "&section=" .
                    $section .
                    "&roll_no=" .
                    $roll_no .
                    "&name=" .
                    $name,
            );
        } else {
            $ajax_url = url("student-list-datatable");
        }
    @endphp
@endsection

@include("components.partials.data_table_js")
@include("components.partials.server_side_datatable")

@push("script")
    <script>
        $('#table_id').DataTable({
            dom: 'Bfrtip',
            paging: true,
            searching: true,
            ordering: true,
            info: true,
        });
    </script>
@endpush
