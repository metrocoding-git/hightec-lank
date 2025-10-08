@extends('layouts.app')
@push('css')
    <style>
        /* Custom styles for checkboxes to match the image's aesthetic */
        .primary_checkbox {
            position: relative;
            display: flex; /* Use flexbox for alignment */
            align-items: center; /* Vertically align items */
            padding-left: 30px; /* Increased space for the custom checkbox */
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            margin-bottom: 10px; /* Add some spacing between checkboxes */
            margin-right: 20px; /* Space between multiple checkboxes in a row */
        }

        .primary_checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            position: absolute;
            top: 50%; /* Center vertically */
            left: 0; /* Position at the start of the padding */
            transform: translateY(-50%); /* Adjust for vertical centering */
            height: 18px;
            width: 18px;
            background-color: #eee; /* Light background for unchecked state */
            border-radius: 50%; /* Make it round like a radio button */
            border: 1px solid #ccc;
        }

        .primary_checkbox:hover input ~ .checkmark {
            background-color: #ccc;
        }

        .primary_checkbox input:checked ~ .checkmark {
            background-color: #6576ff; /* Primary color for checked state */
            border-color: #6576ff;
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        .primary_checkbox input:checked ~ .checkmark:after {
            display: block;
        }

        .primary_checkbox .checkmark:after {
            top: 5px;
            left: 5px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white; /* White dot for checked state */
        }

        /* Adjust accordion header for better visual alignment */
        .card-header button {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: .75rem 1.25rem;
            font-size: 1rem;
            color: #495057;
            text-decoration: none;
            background-color: #f8f9fa;
            border: 1px solid rgba(0,0,0,.125);
            border-radius: .25rem;
        }
        .card-header button:hover {
            color: #0056b3;
            text-decoration: none;
        }
        .card-header button[aria-expanded="true"] {
            background-color: #6576ff; /* Active header background */
            color: white;
        }
        .card-header button[aria-expanded="true"] .ti-plus,
        .card-header button[aria-expanded="true"] .ti-minus {
            color: white;
        }
        .card-header .ti-plus, .card-header .ti-minus {
            font-size: 1rem;
            transition: transform 0.3s ease-in-out;
        }
        .card-header button[aria-expanded="true"] .ti-plus {
            transform: rotate(45deg); /* Rotate plus to become cross */
        }
        .card-body ul {
            list-style: none;
            padding-left: 1px;
        }
        .card-body .list-group-item {
            cursor: pointer;
            padding: .75rem 1.25rem;
            border-left: 0;
            border-right: 0;
        }
        .card-body .list-group-item:hover,
        .card-body .list-group-item.active {
            background-color: #e9ecef;
        }
        /* Specific style for the text next to the checkbox */
        .primary_checkbox .permission-label-text {
            /* margin-left is no longer needed here as padding-left on parent handles spacing */
        }
        /* Table specific styles */
        .permission-table th, .permission-table td {
            vertical-align: middle;
            padding: 0.75rem;
            border-top: 1px solid #e3e6f0;
        }
        .permission-table th {
            width: 200px; /* Adjust as needed for permission names */
        }
        .permission-table td {
            text-align: center;
            padding-left:2px;
        }
    </style>
@endpush
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
                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="mb-0">Main Sections</h4>
                                        </div>
                                        <div class="accordion" id="mainMenuAccordion">
                                            @foreach($menuStructure as $mainItem)
                                                <div class="card">
                                                    <div class="card-header" id="heading{{ Str::slug($mainItem['name']) }}">
                                                        <h2 class="mb-0">
                                                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse{{ Str::slug($mainItem['name']) }}" aria-expanded="false" aria-controls="collapse{{ Str::slug($mainItem['name']) }}">
                                                                {{ $mainItem['name'] }} <i class="float-right ti-plus"></i>
                                                            </button>
                                                        </h2>
                                                    </div>

                                                    <div id="collapse{{ Str::slug($mainItem['name']) }}" class="collapse" aria-labelledby="heading{{ Str::slug($mainItem['name']) }}" data-parent="#mainMenuAccordion">
                                                        <div class="card-body p-0">
                                                            <ul class="list-group list-group-flush">
                                                                @foreach($mainItem['sub_items'] as $subItem)
                                                                    <li class="list-group-item sub-menu-item"
                                                                        data-main-item-name="{{ Str::slug($mainItem['name']) }}"
                                                                        data-sub-item-name="{{ $subItem['name'] }}"
                                                                        data-sub-item-route="{{ $subItem['route'] }}"
                                                                        data-permission-id="{{ $subItem['permission_id'] }}">
                                                                        {{ $subItem['name'] }}
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="mb-0" id="permission-panel-title">Select a sub-section to manage permissions</h4>
                                        </div>
                                        <div class="card-body" id="permission-checkboxes-container">
                                            <p>Click on a sub-section from the left to manage its permissions.</p>
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
    const allPermissions = @json($allPermissions->keyBy('id')); // Key by ID for easy lookup
    const rolePermissionsMap = @json($rolePermissionsMap); // Associative array of permission IDs and their view/add/edit/delete flags

    // Function to render permission checkboxes for a selected sub-item
    function renderPermissionCheckboxes(subItemName, resourcePermissionId) {
        const container = $('#permission-checkboxes-container');
        const title = $('#permission-panel-title');

        container.empty();
        title.text('Permissions for ' + subItemName);

        let tableHtml = `
            <div class="table-responsive">
                <table class="table permission-table">
                    <thead>
                        <tr>
                            <th>Permission</th>
                            <th>View</th>
                            <th>Add</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        const permissionActions = ['view', 'add', 'edit', 'delete'];

        tableHtml += `<tr><td>${subItemName}</td>`; // Sub-section name as the first column

        $.each(permissionActions, function(index, actionName) {
            // Check if this resourcePermissionId exists in rolePermissionsMap and if the specific action is true
            const isChecked = rolePermissionsMap[resourcePermissionId] && rolePermissionsMap[resourcePermissionId][actionName] === 1;
            tableHtml += `
                <td>
                    <label class="primary_checkbox d-flex justify-content-center ">
                        <input type="checkbox" name="permissions[${resourcePermissionId}][${actionName}]" value="1" ${isChecked ? 'checked' : ''}>
                        <span class="checkmark"></span>
                    </label>
                </td>
            `;
        });
        tableHtml += `</tr>`;

        tableHtml += `
                    </tbody>
                </table>
            </div>
        `;

        if (resourcePermissionId === null) {
            container.append('<p>No specific permissions defined for this sub-section.</p>');
        } else {
            container.append(tableHtml);
        }
    }

    // Handle click on sub-menu items
    $(document).on('click', '.sub-menu-item', function() {
        $('.sub-menu-item').removeClass('active');
        $(this).addClass('active');

        const subItemName = $(this).data('sub-item-name');
        const resourcePermissionId = $(this).data('permission-id');

        renderPermissionCheckboxes(subItemName, resourcePermissionId);
    });

    // Ensure the accordion works correctly
    $('.collapse').on('show.bs.collapse', function () {
        $(this).prev('.card-header').find('.ti-plus').removeClass('ti-plus').addClass('ti-minus');
    }).on('hide.bs.collapse', function () {
        $(this).prev('.card-header').find('.ti-minus').removeClass('ti-minus').addClass('ti-plus');
    });

    // Initial state: set the first main item as active and expand it
    // Optionally, you can trigger a click on the first sub-item to show its permissions
    // if ($('.main-menu-item').length > 0) {
    //     $('.main-menu-item').first().addClass('active');
    //     $('#collapse' + $('.main-menu-item').first().data('main-item-name')).collapse('show');
    // }
});
</script>
@endpush
