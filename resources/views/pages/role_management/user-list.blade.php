@extends('layouts.app')
@section('title')
    Role List
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Role List</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="{{ route('role-management.user-list') }}">@lang('role.user_list')</a>
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
                            <div class="col-lg-6 no-gutters col-6 xm_3">
                                <div class="main-title">
                                    <h3 class="mb-15">User List</h3>
                                </div>
                            </div>
                            <div class="col-lg-6 col-6 text-right col-md-6 col-5">
                                @if(Auth::check() && Auth::user()->hasPermission('role-management.create-user'))
                                <a href="{{ route('role-management.create-user') }}" class="primary-btn small fix-gr-bg">
                                    <span class="ti-plus pr-2"></span>
                                   Add User
                                </a>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table id="table_id" class="table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>@lang('common.sl')</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Branch</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $user)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $user->name }} {{ $user->last_name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->role ? $user->role->role_name : 'N/A' }}</td>
                                                <td>
                                                    @foreach($user->branches as $branch)
                                                        <span class="badge badge-primary">{{ $branch->name }}</span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @if($user->status == 1)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <x-drop-down>

                                                        <a class="dropdown-item view-user" href="#" data-id="{{ $user->id }}">@lang('common.view')</a>
                                                        <a class="dropdown-item edit-user" href="#" data-id="{{ $user->id }}">@lang('common.edit')</a>
                                                        <a class="dropdown-item change-password" href="#" data-id="{{ $user->id }}">Change Password</a>
                                                        
                                                    </x-drop-down>
                                                        
                                                    
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

    {{-- View User Modal --}}
    <div class="modal fade admin-query" id="viewUserModal" tabindex="-1" role="dialog" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewUserModalLabel">View User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="viewUserModalBody">
                    {{-- User details will be loaded here via AJAX --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Edit User Modal --}}
    <div class="modal fade admin-query" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit_user_id">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="edit_first_name">First name <span class="text-danger">*</span></label>
                                    <input class="primary_input_field form-control" type="text" id="edit_first_name" name="first_name" required>
                                    <span class="text-danger" id="edit_first_name_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="edit_last_name">Last Name</label>
                                    <input class="primary_input_field form-control" type="text" id="edit_last_name" name="last_name">
                                    <span class="text-danger" id="edit_last_name_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="edit_email">@lang('common.email') <span class="text-danger">*</span></label>
                                    <input class="primary_input_field form-control" type="email" id="edit_email" name="email" required>
                                    <span class="text-danger" id="edit_email_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="edit_role_id">Role <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" name="role_id" id="edit_role_id" required>
                                        <option value="">@lang('common.select_role')</option>
                                        @foreach(\App\Models\Role::all() as $role)
                                            <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger" id="edit_role_id_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="edit_branches">Branches <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" name="branch_ids[]" id="edit_branches" multiple required>
                                        @foreach(\App\Models\Branch::all() as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger" id="edit_branch_ids_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="edit_status">@lang('common.status') <span class="text-danger">*</span></label>
                                    <select class="primary_select form-control" name="status" id="edit_status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <span class="text-danger" id="edit_status_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-40">
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="primary-btn fix-gr-bg">
                                    <span class="ti-check"></span>
                                    @lang('common.update')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Change Password Modal --}}
    <div class="modal fade admin-query" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        @csrf
                        <input type="hidden" name="id" id="change_password_user_id">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="new_password">New Password <span class="text-danger">*</span></label>
                                    <input class="primary_input_field form-control" type="password" id="new_password" name="password" required>
                                    <span class="text-danger" id="new_password_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                                    <input class="primary_input_field form-control" type="password" id="confirm_password" name="password_confirmation" required>
                                    <span class="text-danger" id="confirm_password_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-40">
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="primary-btn fix-gr-bg">
                                    <span class="ti-check"></span>
                                    Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // View User
            $(document).on('click', '.view-user', function() {
                let userId = $(this).data('id');
                $.ajax({
                    url: '/role-management/user-details/' + userId,
                    type: 'GET',
                    success: function(response) {
                        let branchesHtml = '';
                        response.branches.forEach(function(branch) {
                            branchesHtml += `<span class="badge badge-primary mr-2 mb-2">${branch.name}</span>`;
                        });

                        $('#viewUserModalBody').html(`
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label">first name</label>
                                        <input class="primary_input_field form-control" type="text" value="${response.name}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label">Last Name</label>
                                        <input class="primary_input_field form-control" type="text" value="${response.last_name}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label">@lang('common.email')</label>
                                        <input class="primary_input_field form-control" type="text" value="${response.email}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label">Role</label>
                                        <input class="primary_input_field form-control" type="text" value="${response.role ? response.role.role_name : 'N/A'}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label">Branches</label>
                                        <div class="d-flex flex-wrap">
                                            ${branchesHtml}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label">@lang('common.status')</label>
                                        <input class="primary_input_field form-control" type="text" value="${response.status == 1 ? 'Active' : 'Inactive'}" readonly>
                                    </div>
                                </div>
                            </div>
                        `);
                        $('#viewUserModal').modal('show');
                    },
                    error: function(xhr) {
                        toastr.error('Failed to fetch user details.', 'Error');
                    }
                });
            });

            // Edit User
            $(document).on('click', '.edit-user', function() {
                let userId = $(this).data('id');
                $.ajax({
                    url: '/role-management/user-details/' + userId,
                    type: 'GET',
                    success: function(response) {
                        $('#edit_user_id').val(response.id);
                        $('#edit_first_name').val(response.first_name);
                        $('#edit_last_name').val(response.last_name);
                        $('#edit_email').val(response.email);
                        $('#edit_role_id').val(response.role_id);
                        $('#edit_status').val(response.status);

                        // Select branches
                        let selectedBranches = response.branches.map(branch => branch.id.toString());
                        $('#edit_branches').val(selectedBranches);
                        $('#edit_branches').niceSelect('update'); // Update the nice-select dropdown

                        // Clear password fields
                        $('#edit_password').val('');
                        $('#edit_password_confirmation').val('');

                        // Clear previous errors
                        $('.text-danger').text('');

                        $('#editUserModal').modal('show');
                    },
                    error: function(xhr) {
                        toastr.error('Failed to fetch user details for editing.', 'Error');
                    }
                });
            });

            // Update User Form Submission
            $('#editUserForm').on('submit', function(e) {
                e.preventDefault();

                let userId = $('#edit_user_id').val();
                let formData = $(this).serialize();

                // Clear previous errors
                $('.text-danger').text('');

                $.ajax({
                    url: '/role-management/user-update/' + userId,
                    type: 'PUT',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message, 'Success');
                            $('#editUserModal').modal('hide');
                            location.reload(); // Reload page to show updated data
                        } else {
                            toastr.error(response.message || "Something went wrong", 'Error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('#edit_' + key + '_error').text(value[0]);
                            });
                        } else {
                            toastr.error("An unexpected error occurred.", 'Error');
                        }
                    }
                });
            });

            // Change Password
            $(document).on('click', '.change-password', function() {
                let userId = $(this).data('id');
                $('#change_password_user_id').val(userId);
                $('#new_password').val('');
                $('#confirm_password').val('');
                $('.text-danger').text(''); // Clear previous errors
                $('#changePasswordModal').modal('show');
            });

            // Change Password Form Submission
            $('#changePasswordForm').on('submit', function(e) {
                e.preventDefault();

                let userId = $('#change_password_user_id').val();
                let formData = $(this).serialize();

                // Clear previous errors
                $('.text-danger').text('');

                $.ajax({
                    url: '/role-management/user-change-password/' + userId, // You'll need to define this route
                    type: 'POST', // Or PUT, depending on your API design
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message, 'Success');
                            $('#changePasswordModal').modal('hide');
                        } else {
                            toastr.error(response.message || "Something went wrong", 'Error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('#' + key + '_error').text(value[0]);
                            });
                        } else {
                            toastr.error("An unexpected error occurred.", 'Error');
                        }
                    }
                });
            });
        });
    </script>
@endsection
