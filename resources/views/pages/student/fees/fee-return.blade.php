@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('fees.fees_forward')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees_collection')</a>
                    <a href="#">@lang('fees.fees_forward')</a>
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
                                    <h3 class="mb-15">@lang('common.select_criteria')</h3>
                                </div>
                            </div>
                        </div>
                        {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'fees-forward-search', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'search_studentA']) }} --}}
                        <form class="form-horizontal">
                            <div class="row">
                                <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

                                <div class="col-lg-6 mt-30-md">
                                    <select
                                        class="primary_select  form-control{{ $errors->has('class') ? ' is-invalid' : '' }}"
                                        id="select_class" name="class">
                                        <option data-display="@lang('common.select_class') *" value="">@lang('common.select_class') *
                                        </option>
                                        @foreach ($coursess as $class)
                                        <option value="{{$class->id}}"  {{isset($class_id)? ($class_id == $class->id? 'selected':''):''}}>{{$class->name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('class'))
                                        <span class="text-danger invalid-select" role="alert">
                                            {{ $errors->first('class') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="col-lg-6 mt-30-md" id="select_section_div">
                                    <select
                                        class="primary_select  form-control{{ $errors->has('section') ? ' is-invalid' : '' }}"
                                        id="select_section" name="section">
                                        <option data-display="@lang('common.select_section') *" value="">@lang('common.select_section') *
                                        @foreach ($intakes as $section) 
                                            <option value="{{$section->id}}"  {{isset($section_id)? ($section_id == $section->id? 'selected':''):''}}>{{$section->name}}</option>
                                            
                                        @endforeach
                                        </option>
                                    </select>
                                    @if ($errors->has('section'))
                                        <span class="text-danger invalid-select" role="alert">
                                            {{ $errors->first('section') }}
                                        </span>
                                    @endif
                                </div>
                              
                                <div class="col-lg-12 mt-20 text-right">
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

            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'method' => 'POST', 'route' => 'fees-forward-store', 'enctype' => 'multipart/form-data'])}} --}}
            <form class="form-horizontal">
                <div class="row mt-40">
                    <div class="col-lg-12">
                        <div class="white-box">
                            <div class="row">
                                <div class="col-lg-6 no-gutters">
                                    <div class="main-title">
                                        <h3 class="mb-15">@lang('fees.previous_Session_Balance_Fees')</h3>
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
                                                                <h4 class="mb-0"> @lang('fees.previous_balance_can_only_update_now.') </h4>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <th width="15%">@lang('student.student_name')</th>
                                                    <th width="15%">@lang('student.admission_no')</th>
                                                    <th width="15%">Roll Name</th>
                                                    <th width="15%">@lang('student.father_name')</th>
                                                    <th width="15%">@lang('fees.balance')</th>
                                                    <th width="15%">Return Amount</th>
                                                    <th width="25%">@lang('fees.short_note')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($returnDetails as $student)
                                                    <tr>
                                                        <td>{{$student->student->student_name}} <input type="hidden" name="id[]" value="{{isset($update)? @$student->studentDetail->forwardBalance->id: $student->student_id}}"></td>
                                                        <td>{{$student->student->admission_number}}</td>
                                                         <td>{{$student->role->role_name}}</td>
                    
                                                        <td>{{$student->student->guardians_name !=""?$student->student->guardians_name:""}}</td>
                                                        <td> {{ $student->balance }} </td>
                                                        <td> {{ $student->return_amount?? "N/A" }} </td>
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
                                                            @lang('common.save')
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
