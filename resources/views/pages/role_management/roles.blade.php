@extends('layouts.app')
@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Role Management</h1>
                <div class="bc-pages">
                    <a href="{{url('dashboard')}}">Dashboard</a>
                    <a href="#">Role Management</a>
                    <a href="#">Roles</a>
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
                            <form method="POST" action="{{ isset($editRole) ? route('roles.update', $editRole->id) : route('roles.store') }}" id="roleForm">
                                @csrf
                                @if(isset($editRole))
                                    @method('PUT')
                                @endif
                                <div class="white-box">
                                    <div class="main-title">
                                        <h3 class="mb-20" id="formTitle">{{ isset($editRole) ? 'Edit Role' : 'Add Role' }}</h3>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label" for="role_name">Role Name</label>
                                                <input class="primary_input_field" type="text" id="role_name" name="name" value="{{ old('name', $editRole->role_name ?? '') }}" placeholder="Role Name">
                                                <span class="text-danger" id="name-error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg" type="submit">
                                                <span class="ti-check"></span>
                                                <span id="submitButtonText">{{ isset($editRole) ? 'Update Role' : 'Save Role' }}</span>
                                            </button>
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
                            <div class="main-title d-flex justify-content-between align-items-center">
                                <h3 class="mb-20">Role List</h3>
                                <button class="primary-btn small fix-gr-bg mb-20" id="addRoleBtn">
                                    <span class="ti-plus"></span> Add Role
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table id="roleTable" class="display school-table" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Role Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($roles as $key => $role)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$role->role_name}}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                        Select
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item edit-role-btn" href="#" data-id="{{$role->id}}" data-name="{{$role->role_name}}">Edit</a>
                                                        <a class="dropdown-item" href="{{ route('roles.grantPermission', $role->id) }}">Grant Permissions</a>
                                                        <a class="dropdown-item delete-role-btn" href="#" data-id="{{$role->id}}">Delete</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@include('components.partials.data_table_css')
@include('components.partials.data_table_js')

@push('script')
<script>
$(document).ready(function() {
    // Function to update the role table (reload page for simplicity)
    function updateRoleTable() {
        location.reload();
    }

    // Add/Update Role Form Submission
    $('#roleForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var method = form.attr('method');
        var formData = form.serialize();

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                alert(response.message);
                $('#role_name').val(''); // Clear the input field
                $('#name-error').text(''); // Clear any previous errors
                updateRoleTable(); // Refresh the table
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                if (errors && errors.name) {
                    $('#name-error').text(errors.name[0]);
                } else {
                    alert('Operation Failed: ' + xhr.responseJSON.message);
                }
            }
        });
    });

    // Edit Role button click
    $(document).on('click', '.edit-role-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#role_name').val(name);
        $('#roleForm').attr('action', "{{ url('role-management/roles') }}/" + id);
        $('#roleForm').find('input[name="_method"]').remove(); // Remove existing method if any
        $('#roleForm').prepend('<input type="hidden" name="_method" value="PUT">'); // Set method to PUT for update
        $('#formTitle').text('Edit Role');
        $('#submitButtonText').text('Update Role');
        $('#name-error').text(''); // Clear any previous errors
    });

    // Add Role button click (to reset form)
    $('#addRoleBtn').on('click', function(e) {
        e.preventDefault();
        $('#roleForm')[0].reset();
        $('#roleForm').attr('action', "{{ route('roles.store') }}");
        $('#roleForm').find('input[name="_method"]').remove(); // Remove PUT method
        $('#formTitle').text('Add Role');
        $('#submitButtonText').text('Save Role');
        $('#name-error').text('');
    });

    // Delete Role button click
    $(document).on('click', '.delete-role-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this role?')) {
            $.ajax({
                url: "{{ url('role-management/roles') }}/" + id,
                type: "POST", // Laravel uses POST for DELETE with _method field
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: "DELETE"
                },
                success: function(response) {
                    alert(response.message);
                    updateRoleTable(); // Refresh the table
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('Failed to delete role: ' + xhr.responseJSON.message);
                }
            });
        }
    });
});
</script>
@endpush
