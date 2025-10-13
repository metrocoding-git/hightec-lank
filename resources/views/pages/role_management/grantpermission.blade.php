@extends('layouts.app')

@push('css')
    <style>
        :root {
            --purple-primary: #800080;
            --purple-dark: #6a006a;
            --purple-light: #f3e6f8;
            --gray-light: #f8f9fa;
            --gray-border: #e3e6f0;
        }

        /* Breadcrumb */
        .sms-breadcrumb {
            background-color: var(--purple-primary);
            color: #fff;
        }

        .sms-breadcrumb a {
            color: #fff;
        }

        .sms-breadcrumb a:hover {
            color: #e6e6e6;
        }

        /* Titles & Buttons */
        .main-title h3 {
            color: var(--purple-primary);
        }

        .primary-btn.fix-gr-bg {
            background: var(--purple-primary);
            border-color: var(--purple-primary);
        }

        .primary-btn.fix-gr-bg:hover {
            background: var(--purple-dark);
            border-color: var(--purple-dark);
        }

        /* Accordion Header */
        .card-header button {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: .75rem 1.25rem;
            background-color: var(--gray-light);
            border: 1px solid rgba(0, 0, 0, .125);
            border-radius: .25rem;
            color: #495057;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .card-header button:hover {
            background-color: var(--purple-primary);
            color: #fff;
        }

        .card-header button[aria-expanded="true"] {
            background-color: var(--purple-primary);
            color: #fff;
        }

        .card-header .ti-plus,
        .card-header .ti-minus {
            font-size: 1rem;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .card-header button[aria-expanded="true"] .ti-plus {
            transform: rotate(45deg);
            color: #fff;
        }

        /* Submenu Items */
        .list-group-item {
            cursor: pointer;
            padding: .75rem 1.25rem;
            transition: background-color 0.25s, color 0.25s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .list-group-item:hover,
        .list-group-item.active {
            background-color: var(--purple-light);
            color: var(--purple-primary);
            font-weight: 500;
        }

        /* Custom Checkboxes (Purple) */
        .primary_checkbox {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        .primary_checkbox input {
            opacity: 0;
            position: absolute;
        }

        .checkmark {
            height: 18px;
            width: 18px;
            background-color: #eee;
            border-radius: 50%;
            border: 1px solid #ccc;
            transition: all 0.25s ease;
        }

        .primary_checkbox:hover .checkmark {
            background-color: var(--purple-light);
        }

        .primary_checkbox input:checked~.checkmark {
            background-color: var(--purple-primary);
            border-color: var(--purple-primary);
        }

        .checkmark::after {
            content: "";
            position: absolute;
            display: none;
        }

        .primary_checkbox input:checked~.checkmark::after {
            display: block;
            top: 5px;
            left: 5px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #fff;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .permission-table {
                font-size: 0.875rem;
            }
        }
    </style>
@endpush

@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Grant Permissions</h1>
                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">Dashboard</a>
                    <a href="{{ route('roles.index') }}">Role Management</a>
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
                                <!-- LEFT SIDE: MAIN CATEGORIES -->
                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="mb-0">Main Categories</h4>
                                        </div>
                                        <div class="list-group list-group-flush">
                                            @foreach ($menuStructure as $mainItem)
                                                <a href="#"
                                                    class="list-group-item list-group-item-action main-category-item"
                                                    data-category-name="{{ $mainItem['name'] }}">
                                                    {{ $mainItem['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT SIDE: PERMISSIONS FOR SELECTED CATEGORY -->
                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="mb-0" id="permission-panel-title">
                                                Select a category from the left to manage permissions
                                            </h4>
                                        </div>
                                        <div class="card-body" id="permission-checkboxes-container">
                                            <p>Click on a main category from the left to view and manage its permissions.
                                            </p>
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
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const menuStructure = @json($menuStructure);
            const rolePermissions = @json($rolePermissions); // Array of permission IDs the role currently has

            function renderPermissionsForCategory(categoryName) {
                const container = $('#permission-checkboxes-container');
                const title = $('#permission-panel-title');
                container.empty();
                title.text('Permissions for ' + categoryName);

                const category = menuStructure.find(item => item.name === categoryName);

                if (category && category.sub_items.length > 0) {
                    let html = `<div class="table-responsive">
                            <table class="table permission-table">
                                <thead>
                                    <tr>
                                        <th>Permission Name</th>
                                        <th>Access</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                    category.sub_items.forEach(subItem => {
                        const isChecked = rolePermissions.includes(subItem.permission_id);
                        html += `
                    <tr>
                        <td>${subItem.permission_name}</td>
                        <td>
                            <label class="primary_checkbox">
                                <input type="checkbox" name="permissions[]" value="${subItem.permission_id}" ${isChecked ? 'checked' : ''}>
                                <span class="checkmark"></span>
                            </label>
                        </td>
                    </tr>`;
                    });
                    html += `       </tbody>
                        </table>
                    </div>`;
                    container.append(html);
                } else {
                    container.append('<p>No permissions found for this category.</p>');
                }
            }

            $(document).on('click', '.main-category-item', function(e) {
                e.preventDefault();
                $('.main-category-item').removeClass('active');
                $(this).addClass('active');
                const categoryName = $(this).data('category-name');
                renderPermissionsForCategory(categoryName);
            });

            // Optionally, load permissions for the first category on page load
            if (menuStructure.length > 0) {
                $('.main-category-item').first().addClass('active');
                renderPermissionsForCategory(menuStructure[0].name);
            }
        });
    </script>
@endpush
