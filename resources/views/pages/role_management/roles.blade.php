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
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12 d-flex justify-content-between align-items-center">
                            <div class="main-title">
                                <h3 class="mb-30">Role List</h3>
                            </div>
                            <button class="primary-btn small fix-gr-bg mb-20" data-toggle="modal" data-target="#addRoleModal">
                                <span class="ti-plus"></span> Add Role
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="white-box">
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
                                                    <a class="dropdown-item edit-role-btn" href="#" data-id="{{$role->id}}" data-name="{{$role->role_name}}" data-toggle="modal" data-target="#addRoleModal">Edit</a>
                                                    <a class="dropdown-item permission-role-btn" href="#" data-id="{{$role->id}}" data-name="{{$role->role_name}}" data-toggle="modal" data-target="#addPermissionModal">Permissions</a>
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
        </div>
    </section>

    <!-- Add Role Modal -->
    <div class="modal fade admin-query" id="addRoleModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Role</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid">
                        <form action="{{route('roles.store')}}" method="POST" id="modalRoleForm">
                            @csrf
                            <input type="hidden" name="_method" id="modal_method" value="POST">
                            <input type="hidden" name="id" id="role_id">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label" for="modal_role_name">Role Name</label>
                                        <input class="primary_input_field" type="text" id="modal_role_name" name="name" value="" placeholder="Role Name">
                                        <span class="text-danger" id="modal-name-error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-40">
                                <div class="col-lg-12 text-center">
                                    <button class="primary-btn fix-gr-bg" type="submit">
                                        <span class="ti-check"></span>
                                        Save Role
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Permission Modal -->
    <div class="modal fade admin-query" id="addPermissionModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Manage Permissions for <span id="permission_role_name"></span></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid">
                        <form action="#" method="POST" id="modalPermissionForm">
                            @csrf
                            <input type="hidden" name="role_id" id="permission_role_id">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label">Permissions</label>
                                        <div class="d-flex flex-column">
                                            <label class="primary_checkbox d-flex mr-12">
                                                <input type="checkbox" name="permissions[]" value="view" id="perm_view">
                                                <span class="checkmark"></span>
                                                <span class="ml-2">View</span>
                                            </label>
                                            <label class="primary_checkbox d-flex mr-12">
                                                <input type="checkbox" name="permissions[]" value="add" id="perm_add">
                                                <span class="checkmark"></span>
                                                <span class="ml-2">Add</span>
                                            </label>
                                            <label class="primary_checkbox d-flex mr-12">
                                                <input type="checkbox" name="permissions[]" value="edit" id="perm_edit">
                                                <span class="checkmark"></span>
                                                <span class="ml-2">Edit</span>
                                            </label>
                                            <label class="primary_checkbox d-flex mr-12">
                                                <input type="checkbox" name="permissions[]" value="delete" id="perm_delete">
                                                <span class="checkmark"></span>
                                                <span class="ml-2">Delete</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-40">
                                <div class="col-lg-12 text-center">
                                    <button class="primary-btn fix-gr-bg" type="submit">
                                        <span class="ti-check"></span>
                                        Save Permissions
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@include('components.partials.data_table_css')
@include('components.partials.data_table_js')

@push('script')
<script>
$(document).ready(function() {
    // Function to update the role table
    function updateRoleTable() {
        $.ajax({
            url: "{{ route('roles.index') }}",
            type: "GET",
            success: function(response) {
                // Reload the page to refresh the table with new data
                location.reload();
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Failed to refresh role list.');
            }
        });
    }

    // Add/Update Role Form Submission (for the modal form)
    $('#modalRoleForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var method = form.find('#modal_method').val(); // Get method from hidden input
        var formData = form.serialize();

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                alert(response.message);
                $('#modal_role_name').val(''); // Clear the input field
                $('#modal-name-error').text(''); // Clear any previous errors
                $('#addRoleModal').modal('hide'); // Hide the modal
                updateRoleTable(); // Refresh the table
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                if (errors && errors.name) {
                    $('#modal-name-error').text(errors.name[0]);
                } else {
                    alert('Operation Failed: ' + xhr.responseJSON.message);
                }
            }
        });
    });

    // Edit Role button click (for modal)
    $(document).on('click', '.edit-role-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#role_id').val(id);
        $('#modal_role_name').val(name);
        $('#modalRoleForm').attr('action', "{{ url('role-management/roles') }}/" + id);
        $('#modal_method').val('PUT'); // Set method to PUT for update
        $('#addRoleModal .modal-title').text('Edit Role');
        $('#addRoleModal button[type="submit"]').html('<span class="ti-check"></span> Update Role');
        $('#addRoleModal').modal('show');
    });

    // Reset modal form when it's hidden
    $('#addRoleModal').on('hidden.bs.modal', function () {
        $('#modalRoleForm')[0].reset();
        $('#modalRoleForm').attr('action', "{{ route('roles.store') }}");
        $('#modal_method').val('POST');
        $('#addRoleModal .modal-title').text('Add Role');
        $('#addRoleModal button[type="submit"]').html('<span class="ti-check"></span> Save Role');
        $('#modal-name-error').text('');
    });

    // Permission Role button click (for modal)
    $(document).on('click', '.permission-role-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#permission_role_id').val(id);
        $('#permission_role_name').text(name);
        // Reset checkboxes
        $('#modalPermissionForm input[type="checkbox"]').prop('checked', false);

        // TODO: Fetch existing permissions for this role and check the checkboxes
        // For now, just show the modal
        $('#addPermissionModal').modal('show');
    });

    // Add/Update Permission Form Submission
    $('#modalPermissionForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var roleId = $('#permission_role_id').val();
        var formData = form.serialize();

        // TODO: Implement AJAX call to save permissions
        console.log('Saving permissions for role ' + roleId + ':', formData);
        alert('Permissions saved (frontend placeholder).');
        $('#addPermissionModal').modal('hide');
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
