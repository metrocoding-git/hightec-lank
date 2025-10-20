@extends('layouts.app')
@section('title')
    About
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Fees Forward</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Fees Collection</a>
                    <a href="#">Fees Forward</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">

                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="main-title">
                                    <h3 class="mb-15">Select Criteria</h3>
                                </div>
                            </div>
                        </div>
                        <form class="form-horizontal" action="{{ route('fees.fee-return.search') }}" method="POST">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

                                <div class="col-lg-4 mt-30-md">
                                    <select class="primary_select form-control{{ $errors->has('branch') ? ' is-invalid' : '' }}"
                                        id="select_branch" name="branch">
                                        <option data-display="Select Branch *" value="">Select Branch *</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ isset($branch_id) ? ($branch_id == $branch->id ? 'selected' : '') : '' }}>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('branch'))
                                        <span class="text-danger invalid-select" role="alert">
                                            {{ $errors->first('branch') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="col-lg-4 mt-30-md" id="select_intake_div">
                                    <select class="primary_select form-control{{ $errors->has('intake') ? ' is-invalid' : '' }}"
                                        id="select_intake" name="intake">
                                        <option data-display="Select Intake *" value="">Select Intake *</option>
                                        @if (isset($intakes))
                                            @foreach ($intakes as $intake)
                                                <option value="{{ $intake->id }}" {{ isset($intake_id) ? ($intake_id == $intake->id ? 'selected' : '') : '' }}>{{ $intake->name }}</option>
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
                                    <select class="primary_select form-control{{ $errors->has('course') ? ' is-invalid' : '' }}"
                                        id="select_course" name="course">
                                        <option data-display="Select Course *" value="">Select Course *</option>
                                        @if (isset($coursess))
                                            @foreach ($coursess as $course)
                                                <option value="{{ $course->id }}" {{ isset($course_id) ? ($course_id == $course->id ? 'selected' : '') : '' }}>{{ $course->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('course'))
                                        <span class="text-danger invalid-select" role="alert">
                                            {{ $errors->first('course') }}
                                        </span>
                                    @endif
                                </div>
                              
                                <div class="col-lg-12 mt-20 text-right">
                                    <button type="submit" class="primary-btn small fix-gr-bg">
                                        <span class="ti-search pr-2"></span>
                                        Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'method' => 'POST', 'route' => 'fees-forward-store', 'enctype' => 'multipart/form-data'])}} --}}
            <form class="form-horizontal">
                <div class="row mt-40">
                    <div class="col-lg-12">
                        <div class="white-box">
                            <div class="row">
                                <div class="col-lg-6 no-gutters">
                                    <div class="main-title">
                                        <h3 class="mb-15">Previous Session Balance Fees</h3>
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
                                                                <h4 class="mb-0"> Previous balance can only update now. </h4>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <th width="15%">Student Name</th>
                                                    <th width="15%">Admission No</th>
                                                    {{-- <th width="15%">Roll Name</th> --}}
                                                    <th width="15%">Father Name</th>
                                                    <th width="15%">Balance</th>
                                                    <th width="15%">Return Amount</th>
                                                    <th width="15%">Return date</th>
                                                    <th width="25%">Short Note</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($returnDetails as $student)
                                                    <tr>
                                                        <td>{{$student->student->student_name}} <input type="hidden" name="id[]" value="{{isset($update)? @$student->studentDetail->forwardBalance->id: $student->student_id}}"></td>
                                                        <td>{{$student->student->admission_number}}</td>
                                                         {{-- <td>{{$student->role->role_name}}</td> --}}
                    
                                                        <td>{{$student->student->guardians_name !=""?$student->student->guardians_name:""}}</td>
                                                        <td> {{ $student->balance }} </td>
                                                        <td> {{ $student->return_amount?? "N/A" }} </td>
                                                        <td>{{ $student->created_at ? $student->created_at->format('Y-m-d') : 'N/A' }}</td>

                                                        <td> {{ $student->short_note }} </td>
                                                        
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
            </form>

        </div>
    </section>
@endsection
@include('components.partials.data_table_js')

@push('script')
    <script>
        $(document).ready(function() {
            // Function to load intakes based on selected branch
            $('#select_branch').change(function() {
                var branchId = $(this).val();
                console.log("Selected Branch ID:", branchId);
                
                // Destroy niceSelect instances before modifying the underlying select elements
                $('#select_intake').niceSelect('destroy');
                $('#select_course').niceSelect('destroy');

                $('#select_intake').empty().append('<option data-display="Select Intake *" value="">Select Intake *</option>');
                $('#select_course').empty().append('<option data-display="Select Course *" value="">Select Course *</option>');
                
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
                            $('#select_intake').empty().append('<option data-display="Select Intake *" value="">Select Intake *</option>'); // Re-add default option
                            if (response.success && response.intakes.length > 0) {
                                $.each(response.intakes, function(key, value) {
                                    $('#select_intake').append('<option value="' + value.id + '">' + value.name + '</option>');
                                });
                            }
                            // Re-initialize niceSelect for intake after populating
                            $('#select_intake').niceSelect();
                        },
                        error: function(xhr) {
                            console.log("Intakes AJAX error:", xhr.responseText);
                            // Even on error, re-initialize niceSelect to ensure it's not broken
                            $('#select_intake').niceSelect('destroy');
                            $('#select_intake').empty().append('<option data-display="Select Intake *" value="">Select Intake *</option>');
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
                $('#select_course').empty().append('<option data-display="Select Course *" value="">Select Course *</option>');
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
                            $('#select_course').empty().append('<option data-display="Select Course *" value="">Select Course *</option>'); // Re-add default option
                            if (response.success && response.courses.length > 0) {
                                $.each(response.courses, function(key, value) {
                                    $('#select_course').append('<option value="' + value.id + '">' + value.name + '</option>');
                                });
                            }
                            // Re-initialize niceSelect for course after populating
                            $('#select_course').niceSelect();
                        },
                        error: function(xhr) {
                            console.log("Courses AJAX error:", xhr.responseText);
                            // Even on error, re-initialize niceSelect
                            $('#select_course').niceSelect('destroy');
                            $('#select_course').empty().append('<option data-display="Select Course *" value="">Select Course *</option>');
                            $('#select_course').niceSelect();
                        }
                    });
                }
            });
        });
    </script>
@endpush
