@extends('layouts.app')
@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Grant Permissions</h1>
                <div class="bc-pages">
                    <a href="{{url('dashboard')}}">Dashboard</a>
                    <a href="{{route('roles.index')}}">Role Management</a>
                    <a href="#">Grant Permissions</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title">
                            <h3 class="mb-20">Grant Permissions for Role: {{ $role->role_name }}</h3>
                        </div>
                        <form action="{{ route('roles.savePermissions', $role->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label">Permissions</label>
                                        <div class="d-flex flex-column">
                                            @foreach($permissions as $permission)
                                                <label class="primary_checkbox d-flex mr-12">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                        {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                    <span class="checkmark"></span>
                                                    <span class="ml-2">{{ $permission->permission_name }}</span>
                                                </label>
                                            @endforeach
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
    </section>
@endsection
