@extends('layouts.app')

@push('css')
    <style>
        :root {
            --purple-primary: #800080;
            --purple-dark: #6a006a;
            --purple-light: #f3e6f8;
            --gray-light: #f8f9fa;
            --gray-border: #e3e6f0;

            /* New Blue Colors */
            --blue-primary: #4682B4; /* SteelBlue */
            --blue-dark: #36648B; /* Darker SteelBlue */
            --blue-light: #E0FFFF; /* LightCyan */
        }

        /* Breadcrumb */
        .sms-breadcrumb {
            background-color: var(--blue-primary);
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
            color: var(--blue-primary);
        }

        .primary-btn.fix-gr-bg {
            background: var(--blue-primary);
            border-color: var(--blue-primary);
        }

        .primary-btn.fix-gr-bg:hover {
            background: var(--blue-dark);
            border-color: var(--blue-dark);
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
            background-color: var(--blue-primary);
            color: #fff;
        }

        .card-header button[aria-expanded="true"] {
            background-color: var(--blue-primary);
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

        /* Main Category Toggle Active State */
        .main-category-toggle.active {
            background-color: var(--blue-primary);
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
            background-color: var(--blue-light);
            color: var(--blue-primary);
            font-weight: 500;
        }

        /* Custom Checkboxes (Blue) */
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
            background-color: var(--blue-light);
        }

        .primary_checkbox input:checked~.checkmark {
            background-color: var(--blue-primary);
            border-color: var(--blue-primary);
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
                                        <div class="accordion main-category-accordion" id="mainCategoryAccordion">
                                            @foreach ($menuStructure as $category)
                                                <div class="card">
                                                    <div class="card-header" id="heading{{ Str::slug($category['name']) }}">
                                                        <h5 class="mb-0">
                                                            <button class="btn btn-link btn-block text-left d-flex justify-content-between align-items-center main-category-toggle {{ $loop->first ? '' : 'collapsed' }}" type="button" data-toggle="collapse" data-target="#collapse{{ Str::slug($category['name']) }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ Str::slug($category['name']) }}">
                                                                {{ $category['name'] }}
                                                                <i class="ti-plus"></i>
                                                            </button>
                                                        </h5>
                                                    </div>

                                                    <div id="collapse{{ Str::slug($category['name']) }}" class="collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ Str::slug($category['name']) }}" data-parent="#mainCategoryAccordion">
                                                        <div class="card-body p-0">
                                                            <div class="accordion sub-category-accordion" id="subCategoryAccordion{{ Str::slug($category['name']) }}">
                                                                @foreach ($category['sub_categories'] as $subCategory)
                                                                    <div class="card">
                                                                        <div class="card-header" id="heading{{ Str::slug($category['name']) }}{{ Str::slug($subCategory['name']) }}">
                                                                            <h5 class="mb-0">
                                                                                <button class="btn btn-link btn-block text-left d-flex justify-content-between align-items-center sub-category-toggle {{ $loop->parent->first && $loop->first ? '' : 'collapsed' }}" type="button" data-toggle="collapse" data-target="#collapse{{ Str::slug($category['name']) }}{{ Str::slug($subCategory['name']) }}" aria-expanded="{{ $loop->parent->first && $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ Str::slug($category['name']) }}{{ Str::slug($subCategory['name']) }}">
                                                                                    {{ $subCategory['name'] }}
                                                                                    <i class="ti-plus"></i>
                                                                                </button>
                                                                            </h5>
                                                                        </div>
                                                                        <div id="collapse{{ Str::slug($category['name']) }}{{ Str::slug($subCategory['name']) }}" class="collapse {{ $loop->parent->first && $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ Str::slug($category['name']) }}{{ Str::slug($subCategory['name']) }}" data-parent="#subCategoryAccordion{{ Str::slug($category['name']) }}">
                                                                            <div class="card-body p-0">
                                                                                <div class="list-group list-group-flush">
                                                                                    @foreach ($subCategory['items'] as $item)
                                                                                        <a href="#"
                                                                                            class="list-group-item list-group-item-action item-item"
                                                                                            data-category-name="{{ $category['name'] }}"
                                                                                            data-sub-category-name="{{ $subCategory['name'] }}"
                                                                                            data-item-name="{{ $item['name'] }}">
                                                                                            {{ $item['name'] }}
                                                                                        </a>
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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

            function renderPermissionsForCategory(categoryName, subCategoryName, itemName) {
                const container = $('#permission-checkboxes-container');
                const title = $('#permission-panel-title');
                container.empty();
                title.text('Permissions for ' + itemName);

                const category = menuStructure.find(item => item.name === categoryName);

                if (category) {
                    const subCategory = category.sub_categories.find(item => item.name === subCategoryName);

                    if (subCategory) {
                        const item = subCategory.items.find(i => i.name === itemName);

                        if (item && item.permissions.length > 0) {
                            let html = `<div class="table-responsive">
                                    <table class="table permission-table">
                                        <thead>
                                            <tr>
                                                <th>Permission Name</th>
                                                <th>Access</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                            item.permissions.forEach(permission => {
                                const isChecked = rolePermissions.includes(permission.id);
                                html += `
                            <tr>
                                <td>${permission.name}</td>
                                <td>
                                    <label class="primary_checkbox">
                                        <input type="checkbox" name="permissions[]" value="${permission.id}" ${isChecked ? 'checked' : ''}>
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
                            container.append('<p>No permissions found for this item.</p>');
                        }
                    } else {
                        container.append('<p>Sub-category not found.</p>');
                    }
                } else {
                    container.append('<p>Category not found.</p>');
                }
            }

            $(document).on('click', '.item-item', function(e) {
                e.preventDefault();
                $('.item-item').removeClass('active');
                $(this).addClass('active');
                const categoryName = $(this).data('category-name');
                const subCategoryName = $(this).data('sub-category-name');
                const itemName = $(this).data('item-name');
                renderPermissionsForCategory(categoryName, subCategoryName, itemName);
            });

            $(document).on('click', '.main-category-toggle', function() {
                $('.main-category-toggle').removeClass('active');
                if (!$(this).hasClass('collapsed')) {
                    $(this).addClass('active');
                }
            });

            $(document).on('click', '.sub-category-toggle', function() {
                $('.sub-category-toggle').removeClass('active');
                if (!$(this).hasClass('collapsed')) {
                    $(this).addClass('active');
                }
            });

            // Optionally, load permissions for the first item on page load
            if (menuStructure.length > 0) {
                // Find the first item-item and trigger a click
                const firstItemItem = $('.item-item').first();
                if (firstItemItem.length > 0) {
                    firstItemItem.addClass('active');
                    const categoryName = firstItemItem.data('category-name');
                    const subCategoryName = firstItemItem.data('sub-category-name');
                    const itemName = firstItemItem.data('item-name');
                    renderPermissionsForCategory(categoryName, subCategoryName, itemName);

                    // Also activate the parent main category and sub-category toggles
                    const firstSubCategoryToggle = firstItemItem.closest('.collapse').prev('.card-header').find('.sub-category-toggle');
                    if (firstSubCategoryToggle.length > 0) {
                        firstSubCategoryToggle.addClass('active');
                    }
                    const firstMainCategoryToggle = firstItemItem.closest('.sub-category-accordion').closest('.collapse').prev('.card-header').find('.main-category-toggle');
                    if (firstMainCategoryToggle.length > 0) {
                        firstMainCategoryToggle.addClass('active');
                    }
                }
            }
        });
    </script>
@endpush
