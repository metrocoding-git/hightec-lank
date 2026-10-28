@extends('layouts.app')

@section('title')
    Role Management
@endsection

@push('css')
    <style>
        #table_id thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

        .student_rec_card {
            border-radius: 6px;
            border: 1px solid var(--border_color);
            width: 100%;
        }

        .student_rec_header {
            padding: 12px;
            background: -webkit-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -moz-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -o-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: -ms-linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 51%, var(--gradient_2) 100%);
        }

        .student_rec_footer {
            padding: 12px;
            margin-top: 16px;
            border-top: 1px solid var(--border_color);
        }

        .student_rec_content {
            padding: 16px;
            max-height: 300px;
            min-height: 300px;
        }

        /* Keeps horizontal scroll on the wide courses table from also
                       dragging the left-side Add/Edit form sideways on large screens. */
        .course-table-scroll {
            overflow-x: auto;
            max-width: 100%;
        }

        /* ---- Modern refresh: cards, table, status badges (Section 20) ---- */
        .white-box {
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
            transition: box-shadow .2s ease;
        }

        .white-box .main-title h3 {
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
        select.primary_select,
        textarea.form-control {
            border-radius: 8px !important;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .primary_input_field:focus,
        select.primary_select:focus,
        textarea.form-control:focus {
            box-shadow: 0 0 0 3px rgba(var(--gradient_1-rgb, 90, 103, 216), 0.12);
        }

        .course-table-scroll table.Crm_table_active3 thead tr th {
            background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_2) 100%);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
            border: none;
        }

        .course-table-scroll table.Crm_table_active3 tbody tr {
            transition: background-color .12s ease;
        }

        .course-table-scroll table.Crm_table_active3 tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        .course-table-scroll table.Crm_table_active3 tbody td {
            vertical-align: middle;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .3px;
            white-space: nowrap;
        }

        .status-badge.status-active {
            background: #e6f7ec;
            color: #1e8e4f;
        }

        .status-badge.status-inactive {
            background: #fdecec;
            color: #d64545;
        }

        .dropdown-item.text-danger:hover {
            background-color: #fdecec;
        }
    </style>
@endpush

@section('mainContent')
    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Role Management</h1>
                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">Dashboard</a>
                    <a href="#">Roles</a>
                </div>
            </div>
        </div>
    </section>

    @php
        $canAddRole = Auth::user()->hasPermission('roles.store');
        $canEditRole = Auth::user()->hasPermission('roles.update');
        $canDeleteRole = Auth::user()->hasPermission('roles.destroy');
        $canGrantPermission = Auth::user()->hasPermission('roles.grantPermission');
    @endphp

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            <div class="row">
                @if ($canAddRole)
                    <div class="col-lg-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <form method="POST"
                                    action="{{ isset($editRole) ? route('roles.update', $editRole->id) : route('roles.store') }}"
                                    id="roleForm">
                                    @csrf
                                    @if (isset($editRole))
                                        @method('PUT')
                                    @endif
                                    <div class="white-box">
                                        <div class="main-title">
                                            <h3 class="mb-20" id="formTitle">
                                                {{ isset($editRole) ? 'Edit Role' : 'Add Role' }}
                                            </h3>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="primary_input">
                                                    <label class="primary_input_label" for="role_name">Role Name</label>
                                                    <input class="primary_input_field" type="text" id="role_name"
                                                        name="name" value="{{ old('name', $editRole->role_name ?? '') }}"
                                                        placeholder="Role Name">
                                                    <span class="text-danger" id="name-error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-20">
                                            <div class="col-lg-12 text-center">
                                                @if (isset($editRole) ? $canEditRole : $canAddRole)
                                                    <button class="primary-btn fix-gr-bg col-12" type="submit">
                                                        <span class="ti-check"></span>
                                                        <span
                                                            id="submitButtonText">{{ isset($editRole) ? 'Update Role' : 'Save Role' }}</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-lg-{{ $canAddRole ? '9' : '12' }}">

                    <div class="row white-box">
                        <div class="col-lg-12">
                            <div class="main-title d-flex justify-content-between align-items-center">
                                <h3 class="mb-20">Role List</h3>
                               
                            </div>

                            <div class="course-table-scroll">
                                <x-table>
                                    <table id="table_id" class="table Crm_table_active3" cellspacing="0" width="100%">

                                        <thead>

                                            <tr>
                                                <th>ID</th>
                                                <th>Role Name</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($roles as $key => $role)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $role->role_name }}</td>
                                                    <td valign="top">
                                                        @php
                                                            $routeList = [];

                    //                                         if ($canEditRole) {
                    //                                             $routeList[] =
                    //                                                 '<a class="dropdown-item edit-role-btn" href="#"
                    // data-id="' .
                    //                                                 $role->id .
                    //                                                 '"
                    // data-name="' .
                    //                                                 $role->role_name .
                    //                                                 '">Edit</a>';
                    //                                         }

                                                            if ($canGrantPermission) {
                                                                $routeList[] =
                                                                    '<a class="dropdown-item"
                    href="' .
                                                                    route('roles.grantPermission', $role->id) .
                                                                    '">
                    Grant Permissions
                </a>';
                                                            }

                                                            if ($canDeleteRole) {
                                                                $routeList[] =
                                                                    '<a class="dropdown-item delete-role-btn" href="#"
                    data-id="' .
                                                                    $role->id .
                                                                    '">
                    Delete
                </a>';
                                                            }
                                                        @endphp

                                                        @if (count($routeList) > 0)
                                                            <x-drop-down-action-component :routeList="$routeList" />
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>

                                                </tr>
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
                        toastr.success(response.message, 'Success');
                        $('#role_name').val(''); // Clear the input field
                        $('#name-error').text(''); // Clear any previous errors
                        updateRoleTable(); // Refresh the table
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        if (errors && errors.name) {
                            $('#name-error').text(errors.name[0]);
                        } else {
                            toastr.error('Operation Failed: ' + xhr.responseJSON.message,
                                'Error');
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
                $('#roleForm').prepend(
                    '<input type="hidden" name="_method" value="PUT">'); // Set method to PUT for update
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
                            toastr.success(response.message, 'Success');
                            updateRoleTable(); // Refresh the table
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                            toastr.error('Failed to delete role: ' + xhr.responseJSON.message,
                                'Error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
