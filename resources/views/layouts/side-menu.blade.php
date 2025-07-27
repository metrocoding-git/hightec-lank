<nav id="sidebar" class="sidebar">

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
            <a href="/">
                <div class="nav_icon_small">
                    <span class="fas fa-th"></span>
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
            class="administration_section {{ request()->is('administration') || request()->is('administration/*') ? 'mm-active' : '' }}">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-bars"></span>
                </div>
                <div class="nav_title">
                    <span>Academic</span>
                </div>
            </a>
            <ul
                class="mm-collapse {{ request()->is('administration') || request()->is('administration/*') ? 'mm-show' : '' }} ">
                <li>
                    <a href="/administration/courses"
                        class="{{ request()->is('administration/courses') ? 'active a' : '' }}">
                        Courses
                    </a>
                </li>

                <li>
                    <a href="/administration/intake"
                        class="{{ request()->is('administration/intake') ? 'active a' : '' }}">
                        Intake
                    </a>
                </li>
                <li>
                    <a href="/administration/subjects"
                        class="{{ request()->is('administration/subjects') ? 'active a' : '' }}">
                        Subjects
                    </a>
                </li>

                <li>
                    <a href="/administration/assign-subject"
                        class="{{ request()->is('administration/assign-subject') ? 'active a' : '' }}">
                        Assign Subject
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}

        {{-- Study Material --}}
        <li class="administration_section">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-bars"></span>
                </div>
                <div class="nav_title">
                    <span>Study Material</span>
                </div>
            </a>
            <ul class="mm-collapse">
                <li>
                    <a href="upload-content" class="">
                        Upload Content
                    </a>
                </li>
                <li>
                    <a href="other-downloads" class="">
                        Other Downloads
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}

        {{-- Lesson Plane --}}
        <li class="administration_section">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-bars"></span>
                </div>
                <div class="nav_title">
                    <span>Lesson Plane</span>
                </div>
            </a>
            <ul class="mm-collapse ">
                <li>
                    <a href="lesson" class="">
                        Lesson
                    </a>
                </li>
                <li>
                    <a href="topic" class="">
                        Topic
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}

        {{-- Download Center --}}
        <li class="administration_section">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-bars"></span>
                </div>
                <div class="nav_title">
                    <span>Download Center</span>
                </div>
            </a>
            <ul class="mm-collapse">
                <li>
                    <a href="content-type" class="">
                        Content-Type
                    </a>
                </li>
                <li>
                    <a href="content-list" class="">
                        Content List
                    </a>
                </li>
                <li>
                    <a href="shared-content-list" class="">
                        Shared Content List
                    </a>
                </li>
                <li>
                    <a href="video-list" class="">
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
        <li class="studentInfo_section {{ request()->is('student') || request()->is('student/*') ? 'mm-active' : '' }}">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-bars"></span>
                </div>
                <div class="nav_title">
                    <span>Student Info</span>
                </div>
            </a>
            <ul class="mm-collapse {{ request()->is('student') || request()->is('student/*') ? 'mm-show' : '' }}">
                <li>
                    <a href="/student/student-category"
                        class="{{ request()->is('student/student-category') ? 'active a' : '' }}">
                        Student Category
                    </a>
                </li>
                <li>
                    <a href="/student/add-student"
                        class="{{ request()->is('student/add-student') ? 'active a' : '' }}">
                        Add Student
                    </a>
                </li>
                <li>
                    <a href="disabled-students" class="">
                        Disabled Students
                    </a>
                </li>
                <li>
                    <a href="student-attendance" class="">
                        Student Attendance
                    </a>
                </li>
                <li>
                    <a href="student-export" class="">
                        Student Export
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}

        {{-- Fees Section --}}
        <li class="fees_section">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-bars"></span>
                </div>
                <div class="nav_title">
                    <span>Fees</span>
                </div>
            </a>
            <ul class="mm-collapse">
                <li>
                    <a href="fee-group" class="">
                        Fee Group
                    </a>
                </li>
                <li>
                    <a href="fee-type" class="">
                        Fee Type
                    </a>
                </li>
                <li>
                    <a href="fee-invoice" class="">
                        Fee Invoice
                    </a>
                </li>
                <li>
                    <a href="add-invoice" class="">
                        Add Invoice
                    </a>
                </li>
                <li>
                    <a href="fee-invoice-setting" class="">
                        Fee Invoice Setting
                    </a>
                </li>
                <li>
                    <a href="fee-return" class="">
                        Fee Return
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}
        {{-- ================== --}}


        {{-- Inventory Section --}}
        <span class="menu_seperator" id="seperator_inventory_section" data-section="inventory_section">Inventory
            Section
        </span>

        {{-- Inventory Section --}}
        <li class="inventory_section">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-bars"></span>
                </div>
                <div class="nav_title">
                    <span>Inventory</span>
                </div>
            </a>
            <ul class="mm-collapse">
                <li>
                    <a href="item-category" class="">
                        Item Category
                    </a>
                </li>
                <li>
                    <a href="item-list" class="">
                        Item List
                    </a>
                </li>
                <li>
                    <a href="supplier" class="">
                        Supplier
                    </a>
                </li>
                <li>
                    <a href="item-receive" class="">
                        Item Receive
                    </a>
                </li>
                <li>
                    <a href="item-receive-list" class="">
                        Item Receive List
                    </a>
                </li>
                <li>
                    <a href="item-issue" class="">
                        Item Issue
                    </a>
                </li>
                <li>
                    <a href="item-sell" class="">
                        Item Sell
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}
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
