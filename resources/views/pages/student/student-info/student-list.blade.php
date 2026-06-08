@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection


@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('student.manage_student')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('student.student_information')</a>
                    <a href="#">@lang('student.student_list')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'student-list-search', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'infix_form']) }} --}}
            <form class="form-horizontal" action="{{ route('frontend.student.search') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white-box filter_card">
                            <div class="row">
                                <div class="col-lg-8 col-md-6 col-sm-6">
                                    <div class="main-title mt_0_sm mt_0_md">
                                        <h3 class="mb-15">@lang('common.select_criteria')</h3>
                                    </div>
                                </div>


                             
                                <div class="col-lg-4 col-md-6 col-sm-6 text-left text-sm-right mb-4 mb-sm-0">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('student-list.export-all') }}"
                                            class="primary-btn small fix-gr-bg me-2" title="Export all students">
                                            <span class="ti-download pr-2"></span>
                                            Export All to Excel
                                        </a>

                                        <button type="button" class="primary-btn small fix-gr-bg me-2"
                                            id="exportFilteredBtn" style="display: none;">
                                            <span class="ti-download pr-2"></span>
                                            Export Filtered
                                        </button>

                                        <a href="{{ route('add-student.index') }}" class="primary-btn small fix-gr-bg">
                                            <span class="ti-plus pr-2"></span>
                                            @lang('student.add_student')
                                        </a>
                                    </div>
                                </div>

                            </div>
                            <div class="row row-gap-24">

                                {{-- <input type="hidden" name="url" id="url" value="{{ URL::to("/") }}"> --}}


                                {{-- Branch --}}
                                <div class="col-lg-4 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="branch_id">Branch <span
                                                class="text-danger">*</span></label>
                                        <select class="primary_select form-control ajax_select" name="branch_id"
                                            id="branch-select">
                                            <option value="">Select Branch</option>
                                            @foreach ($branches ?? [] as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Intake --}}
                                <div class="col-lg-4 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="intake_id">Intake <span
                                                class="text-danger">*</span></label>
                                        <select class="primary_select form-control ajax_select" name="intake_id"
                                            id="intake-select">
                                            <option value="">Select Intake</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Course --}}
                                <div class="col-lg-4 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label" for="course_id">Course <span
                                                class="text-danger">*</span></label>
                                        <select class="primary_select form-control ajax_select" name="course_id"
                                            id="course-select">
                                            <option value="">Select Course</option>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-lg-12 mt-20 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg">
                                        <span class="ti-search pr-2"></span> @lang('common.search')
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
                <input type="hidden" id="un_session" value="{{ @$data['un_session_id'] }}">
                <input type="hidden" id="un_academic" value="{{ @$data['un_academic_id'] }}">
                <input type="hidden" id="un_faculty" value="{{ @$data['un_faculty_id'] }}">
                <input type="hidden" id="un_department" value="{{ @$data['un_department_id'] }}">
                <input type="hidden" id="un_semester_label" value="{{ @$data['un_semester_label_id'] }}">
                <input type="hidden" id="un_section" value="{{ @$data['un_section_id'] }}">
            </form>
            {{-- @if (@$students) --}}
            <div class="row mt-40 full_wide_table">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">@lang('student.student_list')</h3>
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
                                                <th>Admission No</th>
                                                <th>Full Name</th>
                                                <th>Phone</th>
                                                <th>Phone 2</th>
                                                <th>NIC</th>
                                                <th>Address</th>
                                                <th>Branch</th>
                                                <th>Admission Date</th>
                                                <th>Category</th>
                                                <th>Gender</th>
                                                <th>Date of Birth</th>
                                                <th>Age</th>
                                                <th>Photo</th>
                                                <th>Email</th>
                                                <th>District</th>

                                                <th>Full Name Verification</th>
                                                <th>Phone Verification</th>
                                                <th>Phone 2 Verification</th>
                                                <th>NIC Verification</th>
                                                <th>Address Verification</th>

                                                <th>Notes</th>

                                                <th>Previous Institute</th>
                                                <th>Created By</th>
                                                <th>Updated By</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>


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
                    <h4 class="modal-title">@lang('student.disable_student')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <h4>@lang('student.are_you_sure_to_disable')</h4>
                    </div>
                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                        {{-- {{ Form::open(['route' => 'student-delete', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }} --}}
                        <form class="">
                            <input type="hidden" name="id" value="{{ @$student->id }}" id="student_delete_i">
                            {{-- using js in main.js --}}
                            <button class="primary-btn fix-gr-bg" type="submit">@lang('common.disable')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade admin-query" id="statusUpdateModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('student.disable_student')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <h4>@lang('common.are_you_sure_to_update_student_status')</h4>
                    <div class="mt-40 d-flex justify-content-between">
                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                        <form action="{{ route('student.update-status') }}" method="post">
                            @csrf
                            @method('put')
                            <input type="hidden" id="student-id-form" name="student_id" value="">
                            <button class="primary-btn fix-gr-bg" type="submit">@lang('common.disable')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- varification model --}}
    <div class="modal fade" id="verificationCheckModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Verification Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div id="verification-content">
                        <p class="text-center text-muted">Loading...</p>
                    </div>
                </div>

            </div>
        </div>
    </div>


    @php
        if (isset($academic_year) || isset($class_id)) {
            $ajax_url = url(
                'student-list-datatable?academic_year=' .
                    $academic_year .
                    '&class=' .
                    $class_id .
                    '&section=' .
                    $section .
                    '&roll_no=' .
                    $roll_no .
                    '&name=' .
                    $name,
            );
        } else {
            $ajax_url = url('student-list-datatable');
        }
    @endphp
@endsection

@include('components.partials.data_table_js')
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')

@push('script')
    <script>
        $('#table_id').DataTable({
            processing: true,
            serverSide: true,

            scrollX: true,

            dom: "<'d-flex justify-content-between align-items-center mb-2'<'me-2'l><'mx-auto'f><'ms-2'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'d-flex justify-content-between align-items-center mt-2'<'mb-0 col-4'i><'mb-0 col-8'p>>",

            lengthChange: true,
            buttons: [{
                    extend: 'pdf',
                    title: getFileName(),
                    text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    title: getFileName(),
                    text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    title: getFileName(),
                    text: '<i title="export to excel" class="fa fa-file-excel-o"></i>',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    title: getFileName(),
                    text: '<i title="print" class="fa fa-print"></i>',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'colvis',
                    title: getFileName(),
                    text: '<i title="column visibility" class="fa fa-eye"></i>',
                    columns: ':gt(0)'
                }
            ],


            paging: true,
            searching: true,
            ordering: true,
            info: true,
            order: [
                [0, 'desc']
            ],
            pageLength: 25,
            lengthMenu: [
                [25, 50, 100, 500],
                [25, 50, 100, 500]
            ],
            language: {
                search: '<i class="fa-solid fa-magnifying-glass me-2"></i>',
                searchPlaceholder: 'Search...',
                paginate: {
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            },


            ajax: "{{ route('student-list.datatable') }}",
            columns: [{
                    data: 'admission_number',
                    name: 'admission_number'
                },
                {
                    data: 'student_name',
                    name: 'student_name'
                },
                {
                    data: 'phone_number',
                    name: 'phone_number'
                },
                {
                    data: 'phone_number_2',
                    name: 'phone_number_2'
                },
                {
                    data: 'nic',
                    name: 'national_id_number'
                },
                {
                    data: 'address',
                    name: 'permanent_address'
                },
                {
                    data: 'branch',
                    orderable: false
                },
                {
                    data: 'admission_date',
                    name: 'admission_date'
                },
                {
                    data: 'category',
                    orderable: false
                },
                {
                    data: 'gender',
                    orderable: false
                },
                {
                    data: 'dob',
                    name: 'date_of_birth'
                },
                {
                    data: 'age',
                    name: 'stu_age'
                },
                {
                    data: 'photo',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'email',
                    name: 'email_address'
                },
                {
                    data: 'district',
                    orderable: false
                },

                {
                    data: 'full_name_verification',
                    orderable: false
                },
                {
                    data: 'phone_verification',
                    orderable: false
                },
                {
                    data: 'phone2_verification',
                    orderable: false
                },
                {
                    data: 'nic_verification',
                    orderable: false
                },
                {
                    data: 'address_verification',
                    orderable: false
                },

                {
                    data: 'notes',
                    name: 'additional_notes'
                },
                {
                    data: 'previous_institute',
                    name: 'previous_institute_details'
                },
                {
                    data: 'created_by',
                    orderable: false
                },
                {
                    data: 'updated_by',
                    orderable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]

        });

        $(document).ready(function() {
            function checkFilters() {
                const hasFilters = $('#branch-select').val() ||
                    $('#intake-select').val() ||
                    $('#course-select').val();
                $('#exportFilteredBtn').toggle(!!hasFilters);
            }

            $('#exportFilteredBtn').on('click', function() {
                const branchId = $('#branch-select').val();
                const intakeId = $('#intake-select').val();
                const courseId = $('#course-select').val();

                let url = '{{ route('student-list.export-filtered') }}?';
                if (branchId) url += 'branch_id=' + branchId + '&';
                if (intakeId) url += 'intake_id=' + intakeId + '&';
                if (courseId) url += 'course_id=' + courseId + '&';
                url = url.replace(/&$/, '');

                window.location.href = url;
            });

            checkFilters();
            $('#branch-select, #intake-select, #course-select').on('change', checkFilters);
        });


        function getFileName() {
            let now = new Date();

            let date = now.toISOString().slice(0, 10);
            let time = now.toTimeString().slice(0, 8).replace(/:/g, '-'); // HH-MM-SS

            return 'Student_List_' + date + '__' + time;
        }

        $('#statusUpdateModal').on('show.bs.modal', function(event) {
            var input = $('#student-id-form');
            var button = $(event.relatedTarget);
            var studentId = button.data('student-id');
            input.val(studentId);
        });


        $(document).on('click', '.openVerificationModal', function() {
            let studentId = $(this).data('id');

            $('#verification-content').html('<p class="text-center text-muted">Loading...</p>');
            $('#verificationCheckModal').modal('show');

            $.ajax({
                url: "/student/student-info/verification-history/" + studentId,
                method: "GET",
                success: function(data) {

                    if (data.length === 0) {
                        $('#verification-content').html(
                            '<p class="text-center text-danger">No verification history found.</p>'
                        );
                        return;
                    }

                    let html = `
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Verified Item</th>
                                    <th>Old Value</th>
                                    <th>New Value</th>
                                    <th>Updated By</th>
                                    <th>Date</th>
                                    <th>Signature</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    data.forEach(row => {
                        html += `
                            <tr>
                                <td>${row.verification_item}</td>
                                <td>${row.old_value ?? '-'}</td>
                                <td class="text-success">${row.new_value ?? '-'}</td>
                                <td>${row.user ? row.user.name : 'N/A'}</td>
                                <td>${new Date(row.created_at).toLocaleDateString()}</td>
                               <td>
                                    ${row.signature_path 
                                        ? `<a href="/storage/${row.signature_path}" target="_blank">
                                                                                                            <img src="/storage/${row.signature_path}" 
                                                                                                                style="width:80px; border:1px solid #ccc; padding:2px; cursor:pointer;">
                                                                                                    </a>`
                                        : '-'
                                    }
                                </td>

                            </tr>
                        `;
                    });

                    html += `
                            </tbody>
                        </table>
                    `;

                    $('#verification-content').html(html);
                }
            });
        });


        // $('#table_id').DataTable({
        //     dom: 'Bfrtip',
        //     paging: true,
        //     searching: true,
        //     ordering: true,
        //     info: true,
        // });
        // document.querySelector("form").addEventListener("submit", function() {
        //     this.querySelector("button[type=submit]").disabled = true;
        // });


        // Handle branch change event to load intakes dynamically
        $('#branch-select').on('change', function() {
            var branchId = $(this).val();
            var intakeSelect = $('#intake-select');
            var courseSelect = $('#course-select');

            if (branchId) {
                // Show loading state
                intakeSelect.html('<option value="">Loading intakes...</option>');
                courseSelect.html('<option value="">Select Course</option>');

                // Make AJAX request to get intakes for selected branch
                $.ajax({
                    url: '{{ route('student-list.get-intakes', ':branchId') }}'.replace(':branchId',
                        branchId),
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        console.log('Intakes response:', data);
                        var options = '<option value="">Select Intake</option>';

                        if (data.intakes && data.intakes.length > 0) {
                            data.intakes.forEach(function(intake) {
                                options += '<option value="' + intake.id + '">' + intake.name +
                                    '</option>';
                            });
                        } else {
                            options = '<option value="">No intakes available for this branch</option>';
                        }

                        intakeSelect.html(options);

                        // Trigger niceSelect update if using niceSelect
                        if (intakeSelect.hasClass('primary_select')) {
                            intakeSelect.niceSelect('update');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading intakes:', error);
                        console.error('XHR response:', xhr.responseText);
                        intakeSelect.html('<option value="">Error loading intakes</option>');
                        courseSelect.html('<option value="">Select Course</option>');

                        // Trigger niceSelect update if using niceSelect
                        if (intakeSelect.hasClass('primary_select')) {
                            intakeSelect.niceSelect('update');
                        }
                        if (courseSelect.hasClass('primary_select')) {
                            courseSelect.niceSelect('update');
                        }
                    }
                });
            } else {
                // Reset intake and course dropdowns when no branch is selected
                intakeSelect.html('<option value="">Select Intake</option>');
                courseSelect.html('<option value="">Select Course</option>');

                // Trigger niceSelect update if using niceSelect
                if (intakeSelect.hasClass('primary_select')) {
                    intakeSelect.niceSelect('update');
                }
                if (courseSelect.hasClass('primary_select')) {
                    courseSelect.niceSelect('update');
                }
            }
        });

        // Handle intake change event to load courses dynamically
        $('#intake-select').on('change', function() {
            var intakeId = $(this).val();
            var courseSelect = $('#course-select');

            if (intakeId) {
                // Show loading state
                courseSelect.html('<option value="">Loading courses...</option>');

                // Make AJAX request to get courses for selected intake
                $.ajax({
                    url: '{{ route('student-list.get-courses', ':intakeId') }}'.replace(':intakeId',
                        intakeId),
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        console.log('Courses response:', data);
                        var options = '<option value="">Select Course</option>';

                        if (data.courses && data.courses.length > 0) {
                            data.courses.forEach(function(course) {
                                options += '<option value="' + course.id + '">' + course.name +
                                    '</option>';
                            });
                        } else {
                            options = '<option value="">No courses available for this intake</option>';
                        }

                        courseSelect.html(options);

                        // Trigger niceSelect update if using niceSelect
                        if (courseSelect.hasClass('primary_select')) {
                            courseSelect.niceSelect('update');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading courses:', error);
                        console.error('XHR response:', xhr.responseText);
                        courseSelect.html('<option value="">Error loading courses</option>');

                        // Trigger niceSelect update if using niceSelect
                        if (courseSelect.hasClass('primary_select')) {
                            courseSelect.niceSelect('update');
                        }
                    }
                });
            } else {
                // Reset course dropdown when no intake is selected
                courseSelect.html('<option value="">Select Course</option>');

                // Trigger niceSelect update if using niceSelect
                if (courseSelect.hasClass('primary_select')) {
                    courseSelect.niceSelect('update');
                }
            }
        });
    </script>
@endpush
