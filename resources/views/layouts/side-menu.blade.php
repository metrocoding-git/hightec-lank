<nav id="sidebar" class="sidebar">
    <style>
        .sidebar_menu {
            display: flex;
            flex-direction: column;
            height: calc(100% - 100px); /* Adjust height as needed, considering header/footer */
        }

        .sidebar_menu > li:last-of-type {
            margin-top: auto; /* Pushes the last item (logout) to the bottom */
        }

        #logout-form button {
            background: none;
            border: none;
            color: #a0a0a0; /* Adjust to match other menu item colors */
            padding: 10px 15px;
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #logout-form button:hover {
            color: #fff; /* Hover color */
            background-color: #4682B4; /* Blue primary color for hover */
        }

        #logout-form .nav_icon_small {
            margin-right: 10px;
        }
    </style>

    <div class="sidebar-header update_sidebar">

        <a href="/" id="admin-dashboard">
            <img src="{{ asset('uploads/settings/logo.png') }}" alt="logo">
        </a>

        <a id="close_sidebar" class="d-lg-none">
            <i class="ti-close"></i>
        </a>
    </div>
    <ul class="sidebar_menu list-unstyled" id="sidebar_menu">


        {{-- Dashboard Section --}}
        <span class="menu_seperator" id="seperator_dashboard_section" data-section="dashboard_section">Dashboard
            Section</span>

        <li class="dashboard_section {{ request()->is('dashboard') ? 'mm-active' : '' }} ">
            <a href="dashboard">
                <div class="nav_icon_small">
                    <span class="fas fa-tachometer-alt"></span>
                </div>
                <div class="nav_title">
                    <span>Dashboard</span>
                </div>
            </a>

        </li>
        {{-- ================== --}}


        {{-- Adminstration Section --}}
        <span class="menu_seperator" id="seperator_administration_section"
            data-section="administration_section">Adminstration Section
        </span>

        {{-- Academics --}}
        <li
            class="administration_section {{ request()->is('academic') || request()->is('administration/academic/*') ? 'mm-active' : '' }}">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="nav_title">
                    <span>Academic</span>
                </div>
            </a>
            <ul
                class="mm-collapse {{ request()->is('academic') || request()->is('administration/academic/*') ? 'mm-show' : '' }} ">
                <li>
                    <a href="/administration/academic/courses"
                        class="{{ request()->is('administration/academic/courses') ? 'active a' : '' }}">
                        Courses
                    </a>
                </li>
                <li>
                    <a href="/administration/academic/intake"
                        class="{{ request()->is('administration/academic/intake') ? 'active a' : '' }}">
                        Intake
                    </a>
                </li>
                <li>
                    <a href="/administration/academic/subjects"
                        class="{{ request()->is('administration/academic/subjects') ? 'active a' : '' }}">
                        Subjects
                    </a>
                </li>
                <li>
                    <a href="/administration/academic/assign-subject"
                        class="{{ request()->is('administration/academic/assign-subject') ? 'active a' : '' }}">
                        Assign Subject
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}

        {{-- Study Material --}}
        {{-- <li
            class="administration_section {{ request()->is('study-material') || request()->is('administration/study-material/*') ? 'mm-active' : '' }}">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-bars"></span>
                </div>
                <div class="nav_title">
                    <span>Study Material</span>
                </div>
            </a>
            <ul
                class="mm-collapse {{ request()->is('study-material') || request()->is('administration/study-material/*') ? 'mm-show' : '' }}">
                <li>
                    <a href="/administration/study-material/upload-content"
                        class="{{ request()->is('administration/study-material/upload-content') ? 'active a' : '' }}">
                        Upload Content
                    </a>
                </li>
                <li>
                    <a href="/administration/study-material/study-material-download"
                        class="{{ request()->is('administration/study-material/study-material-download') ? 'active a' : '' }}">
                        Study Material Download
                    </a>
                </li>
                <li>
                    <a href="/administration/study-material/other-download"
                        class="{{ request()->is('administration/study-material/other-download') ? 'active a' : '' }}">
                        Other Download
                    </a>
                </li>
            </ul>
        </li> --}}
        {{-- ----- --}}

        {{-- Lesson Plane --}}
        <li
            class="administration_section {{ request()->is('lesson-plan') || request()->is('administration/lesson-plan/*') ? 'mm-active' : '' }}">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-tasks"></span>
                </div>
                <div class="nav_title">
                    <span>Lesson Plane</span>
                </div>
            </a>
            <ul
                class="mm-collapse {{ request()->is('lesson-plan') || request()->is('administration/lesson-plan/*') ? 'mm-show' : '' }} ">
                <li>
                    <a href="/administration/lesson-plan/lesson"
                        class="{{ request()->is('administration/lesson-plan/lesson') ? 'active a' : '' }}">
                        Lesson
                    </a>
                </li>
                <li>
                    <a href="/administration/lesson-plan/topic"
                        class="{{ request()->is('administration/lesson-plan/topic') ? 'active a' : '' }}">
                        Topic
                    </a>
                </li>

                <li>
                    <a href="/administration/lesson-plan/topic-overview"
                        class="{{ request()->is('administration/lesson-plan/topic-overview') ? 'active a' : '' }}">
                        Topic Overview
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}

        {{-- Download Center --}}
        <li
            class="administration_section {{ request()->is('download-center') || request()->is('administration/download-center/*') ? 'mm-active' : '' }}">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-download"></span>
                </div>
                <div class="nav_title">
                    <span>Download Center</span>
                </div>
            </a>
            <ul
                class="mm-collapse {{ request()->is('download-center') || request()->is('administration/download-center/*') ? 'mm-show' : '' }} ">
                <li>
                    <a href="/administration/download-center/content-type"
                        class="{{ request()->is('administration/download-center/content-type') ? 'active a' : '' }}">
                        Content-Type
                    </a>
                </li>
                <li>
                    <a href="/administration/download-center/content-list"
                        class="{{ request()->is('administration/download-center/content-list') ? 'active a' : '' }}">
                        Content List
                    </a>
                </li>
                <li>
                    <a href="/administration/download-center/shared-content-list"
                        class="{{ request()->is('administration/download-center/shared-content-list') ? 'active a' : '' }}">
                        Shared Content List
                    </a>
                </li>
                <li>
                    <a href="/administration/download-center/video-list"
                        class="{{ request()->is('administration/download-center/video-list') ? 'active a' : '' }}">
                        Video List
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}
        {{-- ================== --}}


        {{-- Student Section --}}
        <span class="menu_seperator" id="seperator_student_section" data-section="student_section">Student
            Section</span>
        <span class="menu_seperator" id="seperator_student_section" data-section="student_section">Student
            Section</span>


        {{-- Student Info Section --}}
        <li
            class="studentInfo_section {{ request()->is('student-info') || request()->is('student/student-info/*') ? 'mm-active' : '' }}">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-user"></span>
                </div>
                <div class="nav_title">
                    <span>Student Info</span>
                </div>
            </a>
            <ul
                class="mm-collapse {{ request()->is('student-info') || request()->is('student/student-info/*') ? 'mm-show' : '' }}">
                <li>
                    <a href="/student/student-info/student-category"
                        class="{{ request()->is('student/student-info/student-category') ? 'active a' : '' }}">
                        Student Category
                    </a>
                </li>
                <li>
                    <a href="/student/student-info/add-student"
                        class="{{ request()->is('student/student-info/add-student') ? 'active a' : '' }}">
                        Add Student
                    </a>
                </li>
                <li>
                    <a href="/student/student-info/student-list"
                        class="{{ request()->is('student/student-info/student-list') ? 'active a' : '' }}">
                        Student List
                    </a>
                </li>
                <li>
                    <a href="/student/student-info/disabled-student"
                        class="{{ request()->is('student/student-info/disabled-student') ? 'active a' : '' }}">
                        Disabled Students
                    </a>
                </li>
                <li>
                    <a href="/student/student-info/student-attendance"
                        class="{{ request()->is('student/student-info/student-attendance') ? 'active a' : '' }}">
                        Student Attendance
                    </a>
                </li>
                {{-- <li>
                    <a href="/student/student-info/student-add-course"
                        class="{{ request()->is('student/student-info/student-add-course') ? 'active a' : '' }}">
                        Student Add Course
                    </a>
                </li> --}}
                <li>
                    <a href="/student/student-info/student-add-intake"
                        class="{{ request()->is('student/student-info/student-add-intake') ? 'active a' : '' }}">
                        Add Intake & Courses
                    </a>
                </li>
                {{-- <li>
                    <a href="/student/student-info/student-export" class="{{ request()->is('student/student-info/student-export') ? 'active a' : '' }}">
                        Student Export
                    </a>
                </li> --}}
            </ul>
        </li>
        {{-- ----- --}}

        {{-- Fees Section --}}
        <li class="fees_section {{ request()->is('fees') || request()->is('student/fees/*') ? 'mm-active' : '' }}">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-money-bill-wave"></span>
                </div>
                <div class="nav_title">
                    <span>Fees</span>
                </div>
            </a>
            <ul class="mm-collapse {{ request()->is('fees') || request()->is('student/fees/*') ? 'mm-show' : '' }}">
                <li>
                    <a href="/student/fees/fee-group"
                        class="{{ request()->is('student/fees/fee-group') ? 'active a' : '' }}">
                        Fee Group
                    </a>
                </li>
                <li>
                    <a href="/student/fees/fee-type"
                        class="{{ request()->is('student/fees/fee-type') ? 'active a' : '' }}">
                        Fee Type
                    </a>
                </li>
                <li>
                    <a href="/student/fees/add-invoice"
                    class="{{ request()->is('student/fees/add-invoice') ? 'active a' : '' }}">
                    Add Invoice
                </a>
            </li>
             <li>
                <a href="/student/fees/course-paid"
                    class="{{ request()->is('student/fees/course-paid') ? 'active a' : '' }}">
                    Course Paid
                </a>
            </li>
            <li>
                <a href="/student/fees/fee-invoice"
                    class="{{ request()->is('student/fees/fee-invoice') ? 'active a' : '' }}">
                    Fee Invoice
                </a>
            </li>
                <li>
                    <a href="/student/fees/fee-invoice-setting"
                        class="{{ request()->is('student/fees/fee-invoice-setting') ? 'active a' : '' }}">
                        Fee Invoice Setting
                    </a>
                </li>
                <li>
                    <a href="/student/fees/fee-return"
                        class="{{ request()->is('student/fees/fee-return') ? 'active a' : '' }}">
                        Fee Return
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}
        {{-- ================== --}}


        {{-- Inventory Section --}}
        <span class="menu_seperator" id="seperator_inventory_section" data-section="inventory_section">Account
            Section
        </span>

        {{-- Inventory Section --}}
        <li
            class="inventory_section {{ request()->is('inventory') || request()->is('account/inventory/*') ? 'mm-active' : '' }}">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-chart-bar"></span>
                </div>
                <div class="nav_title">
                    <span>Inventory</span>
                </div>
            </a>
            <ul
                class="mm-collapse {{ request()->is('inventory') || request()->is('account/inventory/*') ? 'mm-show' : '' }}">
                <li>
                    <a href="/account/inventory/item-category"
                        class="{{ request()->is('account/inventory/item-category') ? 'active a' : '' }}">
                        Item Category
                    </a>
                </li>
                <li>
                    <a href="/account/inventory/item-list"
                        class="{{ request()->is('account/inventory/item-list') ? 'active a' : '' }}">
                        Item List
                    </a>
                </li>
                <li>
                    <a href="/account/inventory/supplier"
                        class="{{ request()->is('account/inventory/supplier') ? 'active a' : '' }}">
                        Supplier
                    </a>
                </li>
                <li>
                    <a href="/account/inventory/item-receive"
                        class="{{ request()->is('account/inventory/item-receive') ? 'active a' : '' }}">
                        Item Receive
                    </a>
                </li>
                <li>
                    <a href="/account/inventory/item-receive-list"
                        class="{{ request()->is('account/inventory/item-receive-list') ? 'active a' : '' }}">
                        Item Receive List
                    </a>
                </li>
                <li>
                    <a href="/account/inventory/item-issue"
                        class="{{ request()->is('account/inventory/item-issue') ? 'active a' : '' }}">
                        Item Issue
                    </a>
                </li>
                <li>
                    <a href="/account/inventory/item-issue-list"
                        class="{{ request()->is('account/inventory/item-issue-list') ? 'active a' : '' }}">
                        Item Issue List
                    </a>
                </li>
                <li>
                    <a href="/account/inventory/item-sell"
                        class="{{ request()->is('account/inventory/item-sell') ? 'active a' : '' }}">
                        Item Sell
                    </a>
                </li>
                <li>
                    <a href="/account/inventory/item-sell-list"
                        class="{{ request()->is('account/inventory/item-sell-list') ? 'active a' : '' }}">
                        Item Sell List
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}
        {{-- ================== --}}

        {{-- Role Management Section --}}
        <span class="menu_seperator" id="seperator_role_management_section" data-section="role_management_section">Role Management
            Section
        </span>

        <li class="role_management_section {{ request()->is('role-management') || request()->is('role-management/*') ? 'mm-active' : '' }}">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-user-tag"></span>
                </div>
                <div class="nav_title">
                    <span>Role Management</span>
                </div>
            </a>
            <ul class="mm-collapse {{ request()->is('role-management') || request()->is('role-management/*') ? 'mm-show' : '' }}">
                <li>
                    <a href="/role-management/roles"
                        class="{{ request()->is('role-management/roles') ? 'active a' : '' }}">
                        Roles
                    </a>
                </li>
                <li>
                    <a href="/role-management/create-user"
                        class="{{ request()->is('role-management/create-user') ? 'active a' : '' }}">
                        Create User
                    </a>
                </li>
            </ul>
        </li>
        {{-- ================== --}}

        {{-- Logout Button --}}
        <li>
            <form action="{{ route('logout') }}" method="POST" id="logout-form" >
                @csrf
                <button type="submit" class="dropdown-item ai-icon" style="background-color: rgb(116, 46, 46)">
                    <div class="nav_icon_small">
                        <span class="fas fa-sign-out-alt"></span>
                    </div>
                    <div class="nav_title">
                        <span>Logout</span>
                    </div>
                </button>
            </form>
        </li>
        {{-- ================== --}}

    </ul>

    {{-- @isset($sidebar_menus)
        @foreach ($sidebar_menus as $sidebar_menu)
            @if (sidebarPermission($sidebar_menu->permissionInfo) == true)
                @if ($sidebar_menu->permissionInfo->lang_name)
                    <span class="menu_seperator">{{ __($sidebar_menu->permissionInfo->lang_name) }}</span>
                @endif
                @foreach ($sidebar_menu->subModule as $item)
                    @if (sidebarPermission($item->permissionInfo) == true)
                        <li class="{{ spn_active_link(subModuleRoute($item), 'mm-active') }}">

                            @if (($item->subModule->count() > 0 && $item->permissionInfo->route != 'dashboard') || $item->permissionInfo->relate_to_child == 1)
                                <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                                @else
                                    <a href="{{ validRouteUrl($item->permissionInfo->route) }}">
                            @endif
                            <div class="nav_icon_small">
                                <span class="{{ $item->permissionInfo->icon }}"></span>
                            </div>
                            <div class="nav_title">
                                <span>{{ __($item->permissionInfo->lang_name ?? $item->permissionInfo->name) }}</span>
                                @if (config('app.app_sync') && $item->permissionInfo->module && in_array($item->permissionInfo->module, $paid_modules))
                                    @if (config('app.app_sync'))
                                        <span class="demo_addons">Addon</span>
                                    @endif
                                @endif
                            </div>
                            </a>
                            <ul class="mm-collapse">
                                @if (@$item->subModule)
                                    @foreach (@$item->subModule as $key => $sub)
                                        @if ($sub->permissionInfo->relate_to_child == 1 && $item->permissionInfo->is_parent == 1 && sidebarPermission($sub->permissionInfo))
                                            @foreach ($childrens as $children)
                                                @if (!in_array($item->permissionInfo->module, ['fees_collection', 'Fees']) && hasDueFees($children->id))
                                                    @continue
                                                @endif

                                                <li>
                                                    <a href="{{ validRouteUrl($sub->permissionInfo->route, $children->id) }}"
                                                        class="{{ spn_active_link(subModuleRoute($sub), 'active') }}">

                                                        {{ __($sub->permissionInfo->lang_name) }} -
                                                        {{ $children->full_name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        @else
                                            @if (sidebarPermission($sub->permissionInfo))
                                                <li>
                                                    <a href="{{ validRouteUrl($sub->permissionInfo->route) }}"
                                                        class="{{ spn_active_link(subModuleRoute($sub), 'active') }}">
                                                        {{ __($sub->permissionInfo->lang_name ?? $sub->permissionInfo->name) }}
                                                    </a>

                                                </li>
                                            @endif
                                        @endforeach
                                @endif
                                @if ($item->permissionInfo->relate_to_child == 1 && $item->permissionInfo->is_parent == 1 && count($item->subModule) == 0 && sidebarPermission($item->permissionInfo))
                                    @foreach ($childrens as $children)
                                        @if (!in_array($item->permissionInfo->module, ['fees_collection', 'Fees']) && hasDueFees($children->id))
                                            @continue
                                        @endif

                                        <li>
                                            <a href="{{ validRouteUrl($item->permissionInfo->route, $children->id) }}"
                                                class="{{ spn_active_link(subModuleRoute($item), 'active') }}">

                                                {{ __($item->permissionInfo->lang_name) }} -
                                                {{ $children->full_name }}
                                                {{ $item->permissionInfo->route }}</a>

                                        </li>
                                    @endforeach
                                @endif

                            </ul>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach
    @endisset --}}
</nav>
