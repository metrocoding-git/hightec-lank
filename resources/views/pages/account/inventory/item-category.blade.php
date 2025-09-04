@extends('layouts.app')
@section('title')
    @lang('academics.About')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('inventory.item_category_list')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('inventory.inventory')</a>
                    <a href="#">@lang('inventory.item_category_list')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @if (isset($editData))
                <div class="row">
                    <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                        <a href="{{ route('item-category') }}" class="primary-btn small fix-gr-bg">
                            <span class="ti-plus pr-2"></span>
                            @lang('common.add')
                        </a>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">
                            {{-- @if (isset($editData))
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => ['item-category-update', $editData->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                            @else
                                @if (userPermission('item-category-store'))
                                    {{ Form::open([
                                        'class' => 'form-horizontal',
                                        'files' => true,
                                        'route' => 'item-category-store',
                                        'method' => 'POST',
                                        'enctype' => 'multipart/form-data',
                                    ]) }}
                                                                    @endif
                                                                @endif --}}
                                                    <form id="categoryForm" class="form-horizontal"
                                                        @isset($editData) 
                                action="{{ route('item-category-update', $editData->id) }}" 
                                method="POST" 
                            @else 
                                action="{{ route('item-category-store') }}" 
                                method="POST"
                            @endisset>
                                @csrf
                                @isset($editData)
                                    @method('PUT')
                                @endisset

                                <div class="white-box">
                                    <div class="main-title">
                                        <h3 class="mb-15">
                                            @if (isset($editData))
                                                @lang('inventory.edit_item_category')
                                            @else
                                                @lang('inventory.add_item_category')
                                            @endif
                                        </h3>
                                    </div>
                                    <div class="add-visitor">
                                        <div class="row">
                                            <div class="col-lg-12 mb-20">
                                                <div class="primary_input">
                                                    <label class="primary_input_label">@lang('inventory.category_name') <span
                                                            class="text-danger">*</span></label>
                                                    <input class="primary_input_field form-control" type="text"
                                                        name="name" id="name"
                                                        value="{{ isset($editData) ? $editData->name : old('category_name') }}">
                                                    <span class="text-danger" id="category_name_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-40">
                                            <div class="col-lg-12 text-center">
                                                <button type="submit" class="primary-btn fix-gr-bg">
                                                    <span class="ti-check"></span>
                                                    @if (isset($editData))
                                                        @lang('common.update')
                                                    @else
                                                        @lang('common.save')
                                                    @endif
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

                    <div class="white-box">
                        <div class="row">
                            <div class="col-lg-4 no-gutters">
                                <div class="main-title">
                                    <h3 class="mb-15"> @lang('inventory.item_category_list')</h3>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-lg-12">
                                <x-table>
                                    <table id="table_id" class="table" cellspacing="0" width="100%">

                                        <thead>

                                            <tr>
                                                <th> @lang('common.sl')</th>
                                                <th> @lang('student.category_title')</th>
                                                <th> @lang('common.action')</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($categories as $key => $category)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $category->name }}</td>
                                                    <td>
                                                        <x-drop-down>
                                                            <a class="dropdown-item"
                                                                href="{{ route('item-category-edit', $category->id) }}">
                                                                @lang('common.edit')
                                                            </a>
                                                            
                                                            <a class="dropdown-item" data-toggle="modal"
                                                                data-target="#deletecategoryModal{{ $category->id }}"
                                                                href="#">@lang('common.delete')</a>

                                                        </x-drop-down>
                                                    </td>
                                                </tr>




                                                <div class="modal fade admin-query"
                                                    id="deletecategoryModal{{ $category->id }}">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">@lang('fees.delete_fees_type')</h4>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
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
                                                                    <form
                                                                        action="{{ route('delete-item-category', $category->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">
                                                                            @lang('common.delete')
                                                                        </button>
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
        </div>
    </section>
    
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@include('components.partials.data_table_js')


@push('scripts')
    {{-- <script>
        $(document).ready(function() {
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = new FormData(this);
                let actionUrl = form.attr('action');

                $.ajax({
                    url: actionUrl,
                    type: 'POST', // always POST
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val() // CSRF token
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#categoryForm')[0].reset();
                            $('#category_name_error').text('');

                            // Reload to show updated table
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $('#category_name_error').text(errors.name ? errors.name[0] : '');
                        } else {
                            toastr.error("Something went wrong!");
                        }
                    }
                });
            });
        });


         --}}
         <script>
    @if(session('message-success'))
        toastr.success("{{ session('message-success') }}");
    @endif

    @if(session('message-error'))
        toastr.error("{{ session('message-error') }}");
    @endif
</script>

    </script>



@endpush
