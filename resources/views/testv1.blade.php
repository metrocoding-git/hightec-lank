@extends('master')
@section('title')
@lang('dormitory.room_type')
@endsection
@section('mainContent')
<section class="sms-breadcrumb mb-20">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('dormitory.room_type')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('dZormitory.dormitory')</a>
                <a href="#">@lang('dormitory.room_type')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        @if(isset($room_type))
        @if(userPermission("room-type-store"))

        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{route('room-type-index')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('common.add')
                </a>
            </div>
        </div>
        @endif
        @endif
        <div class="row">
           
            <div class="col-lg-3">
                <div class="row">
                    <div class="col-lg-12">
                
                        <div class="white-box">
                            <div class="main-title">
                                <h3 class="mb-15">@if(isset($room_type))
                                        @lang('dormitory.edit_room_type')
                                    @else
                                        @lang('dormitory.add_room_type')
                                    @endif
                                 
                                </h3>
                            </div>
                            <div class="add-visitor">
                                
                                <div class="row">
                                    <div class="col-lg-12"> 
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">@lang('dormitory.room_type') <span class="text-danger"> *</span></label>
                                            <input class="primary_input_field form-control{{ $errors->has('type') ? ' is-invalid' : '' }}"
                                                type="text" name="type" autocomplete="off" value="{{isset($room_type)? $room_type->type:old('type')}}">
                                            <input type="hidden" name="id" value="{{isset($room_type)? $room_type->id: ''}}">
                                            
                                            
                                            @if ($errors->has('type'))
                                            <span class="text-danger" >
                                                {{ $errors->first('type') }}
                                            </span>
                                            @endif
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="row mt-15">
                                    <div class="col-lg-12">
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">@lang('common.description')</label>
                                            <textarea class="primary_input_field form-control" cols="0" rows="4" name="description">{{isset($room_type)? $room_type->description: old('description')}}</textarea>
                                          
                                            
                                        </div>
                                    </div>
                                </div>
                                 
                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                       <button class="primary-btn fix-gr-bg submit" data-toggle="tooltip" >
                                            <span class="ti-check"></span>
                                            @if(isset($room_type))
                                                @lang('dormitory.update_room_type')
                                            @else
                                                @lang('dormitory.save_room_type')
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="white-box">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-15">  @lang('dormitory.room_type_list')</h3>
                            </div>
                        </div>
                    </div>
    
                    <div class="row">
                        <div class="col-lg-12">
                            <x-table>
                                <table id="table_id" class="table" cellspacing="0" width="100%">
    
                                    <thead>
                                    
                                        <tr>
                                            <th>@lang('common.sl')</th>
                                            <th> @lang('dormitory.room_type')</th>
                                            <th> @lang('common.description')</th>
                                            <th> @lang('common.action')</th>
                                        </tr>
                                    </thead>
    
                                    <tbody>
                                      
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
@include('components.partials.data_table_js')