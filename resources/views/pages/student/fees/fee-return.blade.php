@extends('layouts.app')
{{-- BUG FIX (Section 27): the browser-tab title was plain "About" — a leftover
     placeholder — instead of naming this page. Also adding the standard
     modern-refresh + breadcrumb + table CSS, which this page never defined
     despite using the sms-breadcrumb/white-box/#table_id markup.

     No button-level permission gating is needed here beyond what already exists:
     this window has no Add/Edit/Delete controls, only a read-only report table
     driven by the Search form, and the window-level 'fees.fee-return.search' key
     already gates access to the whole page (same precedent as Topic-Overview). --}}
@section('title')
    Return Fee Invoice
@endsection
@push('css')
    <style>
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

        .primary_input_field,
        select.primary_select {
            border-radius: 8px !important;
            transition: border-color .15s ease, box-shadow .15s ease;
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

        .text-muted {
            color: #9aa1ac !important;
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
                <h1>Cancelled Fee Invoice</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Cancelled Fee Invoice</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">

                    <div class="white-box">

                        <form class="form-horizontal mb-0" action="{{ route('fees.fee-return.search') }}" method="POST">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

                                <div class="col-lg-4 mt-30-md">
                                    <select
                                        class="primary_select form-control{{ $errors->has('branch') ? ' is-invalid' : '' }}"
                                        id="select_branch" name="branch">
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ isset($branch_id) ? ($branch_id == $branch->id ? 'selected' : '') : '' }}>
                                                {{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('branch'))
                                        <span class="text-danger invalid-select" role="alert">
                                            {{ $errors->first('branch') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="col-lg-4 mt-30-md" id="select_intake_div">
                                    <select
                                        class="primary_select form-control{{ $errors->has('intake') ? ' is-invalid' : '' }}"
                                        id="select_intake" name="intake">
                                        @if (isset($intakes))
                                            @foreach ($intakes as $intake)
                                                <option value="{{ $intake->id }}"
                                                    {{ isset($intake_id) ? ($intake_id == $intake->id ? 'selected' : '') : '' }}>
                                                    {{ $intake->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('intake'))
                                        <span class="text-danger invalid-select" role="alert">
                                            {{ $errors->first('intake') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="col-lg-4 mt-30-md" id="select_course_div">
                                    <select
                                        class="primary_select form-control{{ $errors->has('course') ? ' is-invalid' : '' }}"
                                        id="select_course" name="course">
                                        @if (isset($coursess))
                                            @foreach ($coursess as $course)
                                                <option value="{{ $course->id }}"
                                                    {{ isset($course_id) ? ($course_id == $course->id ? 'selected' : '') : '' }}>
                                                    {{ $course->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('course'))
                                        <span class="text-danger invalid-select" role="alert">
                                            {{ $errors->first('course') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="col-lg-12 mt-10 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg col-12 p-2">
                                        <span class="ti-search pr-2"></span>
                                        Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-6 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15">Return Fee Invoice</h3>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="update" value="{{ isset($update) ? 1 : '' }}">
                        <div class="row">
                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id" class="table" cellspacing="0" width="100%">
                                        <thead>
                                            @if (isset($update))
                                                <tr>
                                                    <td colspan="6">
                                                        <div class="alert">
                                                            <h4 class="mb-0"> Previous balance can only update now.
                                                            </h4>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <th>ID</th>
                                                <th>Main Invoice No.</th>
                                                <th>Fee Name</th>
                                                <th>@lang('common.student')</th>
                                                <th>Stu Category</th>
                                                <th>Branch</th>
                                                <th>@lang('admin.admission_no')</th>
                                                <th>Balance</th>
                                                <th>Return Amount</th>
                                                <th>Return date</th>
                                                <th>Payment Method</th>
                                                <th>Payment Date</th>
                                                <th>Short Note</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($returnDetails as $key => $rInvoice)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        <a href="{{ route('fees.fee-invoice-view', ['id' => $rInvoice->invoice->id, 'state' => 'view']) }}"
                                                            target="_blank">
                                                            {{ $rInvoice->invoice->main_invoice_number }}
                                                        </a>
                                                    </td>
                                                    <td>

                                                        @if ($rInvoice->invoice->invoice_type == 'fee')
                                                            {{ $rInvoice->invoice->invoice_item[0]->fee_type->name }}
                                                        @elseif ($rInvoice->invoice->invoice_type == 'course')
                                                            {{ $rInvoice->invoice->paidCourses?->course?->name ?? '-' }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('student-details', ['id' => $rInvoice->invoice->student->id, 'state' => 'view']) }}"
                                                            target="_blank">
                                                            {{ $rInvoice->invoice->student->student_name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $rInvoice->invoice->student->studentCategory->category_name ?? '-' }}
                                                    </td>
                                                    <td>{{ $rInvoice->invoice->branch->name }}</td>
                                                    <td>{{ $rInvoice->invoice->student->admission_number }}</td>

                                                    <td> {{ $rInvoice->balance }} </td>
                                                    <td> {{ $rInvoice->return_amount ?? 'N/A' }} </td>
                                                    <td>{{ $rInvoice->created_at ? $rInvoice->created_at->format('Y-m-d') : 'N/A' }}
                                                    </td>
                                                    <td>{{ $rInvoice->invoice->payment_method }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($rInvoice->invoice->create_date)->format('m/d/Y') }}
                                                    </td>
                                                    <td> {{ $rInvoice->short_note }} </td>

                                                    {{-- <td>
                                                            <div class="primary_input">
                                                                <input oninput="numberCheckWithDot(this)" type="text" class="primary_input_field form-control" cols="0" rows="1" name="balance[{{isset($update)? @$student->studentDetail->forwardBalance->id:@$student->student_id}}]" maxlength="8" value="{{isset($update)?
                                                                @$student->studentDetail->forwardBalance->balance: ''}}">
                                                            </div>
                                                        </td>
                                                        <td> --}}
                                                    {{-- <div class="primary_input">
                                                                <input type="text" class="primary_input_field form-control" cols="0" rows="1" name="notes[{{isset($update)? @$student->studentDetail->forwardBalance->id:$student->student_id}}]" value="{{isset($update)?
                                                                @$student->studentDetail->forwardBalance->notes: 'Fees Carry Forward'}}">
                                                            </div> --}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tr>
                                            {{-- <td colspan="6">
                                                    <div class="text-center">
                                                        <button type="submit" class="primary-btn fix-gr-bg mb-0 submit">
                                                            <span class="ti-save pr"></span>
                                                            Save
                                                        </button>
                                                    </div>
                                                </td> --}}
                                        </tr>
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
@include('components.partials.data_table_js')
@include('components.partials.multi_select_js')
@include('components.partials.date_picker_css_js')
@include('components.partials.data_table_css')
@push('script')
    <script>
        $('#table_id').DataTable({
            responsive: true,
            dom: 'Bfrtip',

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

            paging: true,
            searching: true,
            ordering: true,
            info: true,

            // 👇 This part adds the search and pagination icons
            language: {
                search: '<i class="fa-solid fa-magnifying-glass me-2"></i>',
                searchPlaceholder: 'Search...',
                paginate: {
                    previous: '<i class="fa-solid fa-angle-left"></i>',
                    next: '<i class="fa-solid fa-angle-right"></i>'
                }
            }
        });

        function getFileName() {
            let now = new Date();

            let date = now.toISOString().slice(0, 10);
            let time = now.toTimeString().slice(0, 8).replace(/:/g, '-'); // HH-MM-SS

            return 'Fee_Return_' + date + '__' + time;
        }
    </script>
    <script>
        $(document).ready(function() {
            // Function to load intakes based on selected branch
            $('#select_branch').change(function() {
                var branchId = $(this).val();
                console.log("Selected Branch ID:", branchId);

                // Destroy niceSelect instances before modifying the underlying select elements
                $('#select_intake').niceSelect('destroy');
                $('#select_course').niceSelect('destroy');

                $('#select_intake').empty().append(
                    '<option data-display="Select Intake *" value="">Select Intake *</option>');
                $('#select_course').empty().append(
                    '<option data-display="Select Course *" value="">Select Course *</option>');

                // Re-initialize niceSelect after clearing
                $('#select_intake').niceSelect();
                $('#select_course').niceSelect();

                if (branchId) {
                    $.ajax({
                        url: "{{ url('student/student-info/get-intakes-by-branch') }}/" + branchId,
                        type: "GET",
                        success: function(response) {
                            console.log("Intakes response:", response);
                            // Destroy niceSelect for intake before populating
                            $('#select_intake').niceSelect('destroy');
                            $('#select_intake').empty().append(
                                '<option data-display="Select Intake *" value="">Select Intake *</option>'
                            ); // Re-add default option
                            if (response.success && response.intakes.length > 0) {
                                $.each(response.intakes, function(key, value) {
                                    $('#select_intake').append('<option value="' + value
                                        .id + '">' + value.name + '</option>');
                                });
                            }
                            // Re-initialize niceSelect for intake after populating
                            $('#select_intake').niceSelect();
                        },
                        error: function(xhr) {
                            console.log("Intakes AJAX error:", xhr.responseText);
                            // Even on error, re-initialize niceSelect to ensure it's not broken
                            $('#select_intake').niceSelect('destroy');
                            $('#select_intake').empty().append(
                                '<option data-display="Select Intake *" value="">Select Intake *</option>'
                            );
                            $('#select_intake').niceSelect();
                        }
                    });
                }
            });

            // Function to load courses based on selected intake
            $('#select_intake').change(function() {
                var intakeId = $(this).val();
                console.log("Selected Intake ID:", intakeId);

                // Destroy niceSelect for course before modifying
                $('#select_course').niceSelect('destroy');
                $('#select_course').empty().append(
                    '<option data-display="Select Course *" value="">Select Course *</option>');
                // Re-initialize niceSelect for course after clearing
                $('#select_course').niceSelect();

                if (intakeId) {
                    $.ajax({
                        url: "{{ url('student/student-info/get-courses-by-intake') }}/" + intakeId,
                        type: "GET",
                        success: function(response) {
                            console.log("Courses response:", response);
                            // Destroy niceSelect for course before populating
                            $('#select_course').niceSelect('destroy');
                            $('#select_course').empty().append(
                                '<option data-display="Select Course *" value="">Select Course *</option>'
                            ); // Re-add default option
                            if (response.success && response.courses.length > 0) {
                                $.each(response.courses, function(key, value) {
                                    $('#select_course').append('<option value="' + value
                                        .id + '">' + value.name + '</option>');
                                });
                            }
                            // Re-initialize niceSelect for course after populating
                            $('#select_course').niceSelect();
                        },
                        error: function(xhr) {
                            console.log("Courses AJAX error:", xhr.responseText);
                            // Even on error, re-initialize niceSelect
                            $('#select_course').niceSelect('destroy');
                            $('#select_course').empty().append(
                                '<option data-display="Select Course *" value="">Select Course *</option>'
                            );
                            $('#select_course').niceSelect();
                        }
                    });
                }
            });
        });
    </script>
@endpush
