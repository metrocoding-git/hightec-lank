@extends('layouts.app')
@section('title')
    @lang('student.student_category')
@endsection
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <div class="row">

                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">


                          <form action="{{ isset($student_type) ? route('student-category.update', ['id' => $student_type->id]) : route('student-category.store') }}" method="POST" class="form-horizontal">
                                @csrf
                                @if(isset($student_type))
                                    @method('PUT')
                                @endif
                                     <div class="white-box">
                                    <div class="main-title">
                                        <h3 class="mb-15">
                                            @lang('student.add_student_category')
                                        </h3>
                                    </div>

                                    <div class="add-visitor">
                                        <div class="row">
                                            <div class="col-lg-12">

                                                <div class="primary_input">
                                            <label class="primary_input_label" for="">@lang('common.type')
                                                <span class="text-danger"> *</span>
                                            </label>
                                                <input
                                                    class="primary_input_field form-control{{ $errors->has('category') ? ' is-invalid' : '' }}"
                                                    type="text" 
                                                    name="category" 
                                                    autocomplete="off"
                                                    placeholder="Category Name"
                                                    value="{{ isset($student_type) ? $student_type->category_name : old('category') }}">
                                                <input type="hidden" name="id"
                                                    value="{{ isset($student_type) ? $student_type->id : '' }}">

                                                @if ($errors->has('category'))
                                                    <span class="text-danger">
                                                        {{ $errors->first('category') }}
                                                    </span>
                                                @endif
                                            </div>

                                            </div>
                                        </div>

                                        <div class="row mt-40">
                                            <div class="col-lg-12 text-center">
                                                <button class="primary-btn fix-gr-bg submit" data-toggle="tooltip">
                                                    <span class="ti-check"></span>

                                                    @lang('student.save_category')


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
                                    <h3 class="mb-15">@lang('student.student_category_list')</h3>
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
                                                <th>@lang('student.category')</th>
                                                <th>@lang('common.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($categories as $key => $category)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $category->category_name }}</td>
                                                <td>

                                                     <x-drop-down>
                                                        <a class="dropdown-item editCategory"
                                                            href="{{ route('student-category.edit', $category->id) }}">
                                                            @lang('common.edit')
                                                        </a>
                                                        <a class="dropdown-item" data-toggle="modal"
                                                            data-target="#deleteStudentTypeModal{{ $category->id }}"
                                                            href="#">
                                                            @lang('common.delete')
                                                        </a>
                                                    </x-drop-down>
                                                </td>
                                            </tr>
                                            
                                            <!-- Delete Modal for each category -->
                                            <div class="modal fade admin-query" id="deleteStudentTypeModal{{ $category->id }}">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">@lang('student.delete_category')</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="text-center">
                                                                <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                            </div>
                                                            <div class="mt-40 d-flex justify-content-between">
                                                                <button type="button" class="primary-btn tr-bg" data-dismiss="modal">
                                                                    @lang('common.cancel')
                                                                </button>
                                                                <form action="{{ route('student-category.destroy', $category->id) }}" method="POST">
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
                    text: '<i class="fa-regular fa-copy"></i>',
                    titleAttr: 'Copy'
                },
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa-solid fa-file-csv"></i>',
                    titleAttr: 'CSV'
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel"></i>',
                    titleAttr: 'Excel'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa-solid fa-file-pdf"></i>',
                    titleAttr: 'PDF'
                },
                {
                    extend: 'print',
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
        });    </script>
    
@endpush

@section('script')
    <script>
        // $(document).ready(function() {
        //     let currentDeleteId = null;
            
        //     // Handle edit button click
        //     $(document).on('click', '.editCategory', function(e) {
        //         e.preventDefault();
                
        //         var id = $(this).data('id');
        //         var name = $(this).data('name');
                
        //         // Set form values
        //         $('input[name="category"]').val(name);
        //         $('input[name="id"]').val(id);
                
        //         // Update form action with correct parameter name
        //         $('form').attr('action', '/student/student-info/student-category/update/' + id);
        //         $('form').find('input[name="_method"]').remove();
        //         $('form').append('<input type="hidden" name="_method" value="PUT">');
                
        //         // UI updates
        //         $('.main-title h3').text('@lang("student.edit_student_category")');
        //         $('.submit span').removeClass('ti-check').addClass('ti-pencil');
        //         $('.submit').html('<span class="ti-pencil"></span> @lang("student.update_category")');
        //     });
            
        //     // Reset form when clicking cancel (optional)
        //     $(document).on('click', '.cancel-edit', function() {
        //         resetForm();
        //     });
            
        //     // Handle delete button click
        //    $(document).on('click', '.deleteCategory', function(e) {
        //         e.preventDefault();
        //         currentDeleteId = $(this).data('id');
        //         $('#deleteStudentTypeModal' + currentDeleteId).modal('show');
        //     });
            
        //     // Confirm delete action - now using form submission
        //     $(document).on('click', '.confirm-delete', function() {
        //         if (!currentDeleteId) return;
                
        //         // Find the form in the modal and submit it
        //         $('#deleteStudentTypeModal' + currentDeleteId)
        //             .find('form')
        //             .trigger('submit');
        //     });
        
            
        //     function resetForm() {
        //         $('input[name="category"]').val('');
        //         $('input[name="id"]').val('');
        //         $('.main-title h3').text('@lang("student.add_student_category")');
        //         $('.submit span').removeClass('ti-pencil').addClass('ti-check');
        //         $('.submit').html('<span class="ti-check"></span> @lang("student.save_category")');
        //         $('form').attr('action', '{{ route("student-category.store") }}');
        //         $('input[name="_method"]').remove();
        //     }
        // });
    </script>
@endsection



