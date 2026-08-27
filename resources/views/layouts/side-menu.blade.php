<nav id="sidebar" class="sidebar">
    @php use Illuminate\Support\Facades\Auth; @endphp
    <style>
        .sidebar_menu {
            display: flex;
            flex-direction: column;
            height: calc(100% - 100px);
            /* Adjust height as needed, considering header/footer */
        }

        .sidebar_menu>li:last-of-type {
            margin-top: auto;
            /* Pushes the last item (logout) to the bottom */
        }

        #logout-form button {
            background: none;
            border: none;
            color: #a0a0a0;
            /* Adjust to match other menu item colors */
            /* padding: 10px 15px; */
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #logout-form button:hover {
            color: #fff;
            /* Hover color */
            background-color: #4682B4;
            /* Blue primary color for hover */
        }

        #logout-form .nav_icon_small {
            margin-right: 10px;
        }


        @media (min-width: 992px) {
            .sidebar-header {
                flex-direction: column !important;
                text-align: center !important;
            }
        }

        @media (max-width: 991px) {
            .sidebar-header {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
                text-align: left !important;
            }

            .sidebar-name {
                width: 100%;
                text-align: center;
                margin-top: 10px;
            }
        }
    </style>

    <div class="sidebar-header update_sidebar d-flex">

        <a href="/" id="admin-dashboard" class="logo-area">
            <img src="{{ asset('uploads/settings/logo.png') }}" alt="logo" style="max-width: 100px;">
        </a>
        <div class="sidebar-name" style="font-size:14px; font-weight:600;">
            HIGHTEC LANKA INTERNATIONAL
        </div>

        <a id="close_sidebar" class="d-lg-none">
            <i class="ti-close" style="font-size:20px;"></i>
        </a>
    </div>

    {{--
        NO PERMISSION GATING IN THIS VERSION.
        Every hasPermission() check (and the demo-panel.view branch that used
        to switch to a separate short "Demo Section" menu) has been removed.
        This is the full admin menu, unconditionally visible to every logged-in
        user. Tell me which links/sections should require which permission and
        I'll re-add @if (Auth::check() && Auth::user()->hasPermission('...'))
        guards for just those.
    --}}

    <ul class="sidebar_menu list-unstyled" id="sidebar_menu">

        {{-- Dashboard Section --}}
        <span class="menu_seperator" id="seperator_dashboard_section" data-section="dashboard_section">Dashboard
            Section</span>

        <li class="dashboard_section {{ request()->is('dashboard') ? 'mm-active' : '' }} ">
            <a href="/dashboard">
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
                    <a href="/student/student-info/scan-attendance"
                        class="{{ request()->is('student/student-info/scan-attendance') ? 'active a' : '' }}">
                        Student Attendance
                    </a>
                </li>
                <li>
                    <a href="/student/student-info/student-add-intake"
                        class="{{ request()->is('student/student-info/student-add-intake') ? 'active a' : '' }}">
                        Add Intake & Courses
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}

        {{-- Fees Section --}}
        <li
            class="fees_section {{ request()->is('fees') || request()->is('student/fees/*') ? 'mm-active' : '' }}">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-money-bill-wave"></span>
                </div>
                <div class="nav_title">
                    <span>Fees</span>
                </div>
            </a>
            <ul
                class="mm-collapse {{ request()->is('fees') || request()->is('student/fees/*') ? 'mm-show' : '' }}">
                <li>
                    <a href="/student/fees/fee-group"
                        class="{{ request()->is('student/fees/fee-group') ? 'active a' : '' }}">
                        Invoice Item Group
                    </a>
                </li>
                <li>
                    <a href="/student/fees/fee-type"
                        class="{{ request()->is('student/fees/fee-type') ? 'active a' : '' }}">
                        Invoice Type
                    </a>
                </li>
                <li>
                    <a href="/student/fees/other-payment"
                        class="{{ request()->is('student/fees/other-payment') ? 'active a' : '' }}">
                        Registration & Other Payments
                    </a>
                </li>
                <li>
                    <a href="/student/fees/course-payment"
                        class="{{ request()->is('student/fees/course-payment') ? 'active a' : '' }}">
                        Course, NVQ & Food Payment
                    </a>
                </li>
                <li>
                    <a href="/student/fees/invoice-list"
                        class="{{ request()->is('student/fees/invoice-list') ? 'active a' : '' }}">
                        Invoice List
                    </a>
                </li>

                <li>
                    <a href="/student/fees/fee-return"
                        class="{{ request()->is('student/fees/fee-return') ? 'active a' : '' }}">
                        Cancelled Invoice
                    </a>
                </li>

                <li>
                    <a href="/student/fees/fee-invoice/invoice-payments/pending"
                        class="{{ request()->is('student/fees/fee-invoice/invoice-payments/pending') ? 'active a' : '' }}">
                        Credit Invoice
                    </a>
                </li>

                <li>
                    <a href="/student/fees/fee-invoice/invoice-payments"
                        class="{{ request()->is('student/fees/fee-invoice/invoice-payments') ? 'active a' : '' }}">
                        Payment for Credit Invoice
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}
        {{-- ================== --}}


        {{--
            NOTE: label text says "Account Section" but every class/id on this
            block says "inventory_section" — looks like a copy-paste label bug
            from wherever this section was cloned from. Left the visible text
            alone; say the word and I'll change it to "Inventory Section" to
            match.
        --}}
        <span class="menu_seperator" id="seperator_inventory_section" data-section="inventory_section">Account
            Section
        </span>

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
                    <a href="/account/inventory/item-receive/list"
                        class="{{ request()->is('account/inventory/item-receive/list') ? 'active a' : '' }}">
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
                    <a href="/account/inventory/item-issue/list"
                        class="{{ request()->is('account/inventory/item-issue/list') ? 'active a' : '' }}">
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
                    <a href="/account/inventory/item-sell/list"
                        class="{{ request()->is('account/inventory/item-sell/list') ? 'active a' : '' }}">
                        Item Sell List
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}
        {{-- Reports Section --}}
        <span class="menu_seperator" id="seperator_reports_section" data-section="reports_section">Reports
            Section
        </span>

        <li
            class="reports_section {{ request()->is('reports') || request()->is('reports/*') ? 'mm-active' : '' }}">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-chart-line"></span>
                </div>
                <div class="nav_title">
                    <span>Reports</span>
                </div>
            </a>
            <ul
                class="mm-collapse {{ request()->is('reports') || request()->is('reports/*') ? 'mm-show' : '' }}">

                <li>
                    <a href="/reports/sell-items"
                        class="{{ request()->is('reports/sell-items') ? 'active a' : '' }}">
                        Sell Items
                    </a>
                </li>
                <li>
                    <a href="/reports/issue-items"
                        class="{{ request()->is('reports/issue-items') ? 'active a' : '' }}">
                        Issue Items
                    </a>
                </li>
                <li>
                    <a href="/reports/students-paid"
                        class="{{ request()->is('reports/students-paid') ? 'active a' : '' }}">
                        Collection Report
                    </a>
                </li>
                <li>
                    <a href="/reports/course-paid"
                        class="{{ request()->is('reports/course-paid') ? 'active a' : '' }}">
                        Course Paid
                    </a>
                </li>

                <li>
                    <a href="/reports/course-dates-payments"
                        class="{{ request()->is('reports/course-dates-payments') ? 'active a' : '' }}">
                        Course Started & Course Completed
                    </a>
                </li>

                <li>
                    <a href="/reports/course-intake-payments"
                        class="{{ request()->is('reports/course-intake-payments') ? 'active a' : '' }}">
                        Course Started & Course Balance Status
                    </a>
                </li>

                <li>
                    <a href="/reports/students-intake-balance"
                        class="{{ request()->is('reports/students-intake-balance') ? 'active a' : '' }}">
                        Course Fee Balances as Intake Wise
                    </a>
                </li>

                <li>
                    <a href="/reports/change-course"
                        class="{{ request()->is('reports/change-course') ? 'active a' : '' }}">
                        Change Course
                    </a>
                </li>

                <li>
                    <a href="/reports/invoice-payments"
                        class="{{ request()->is('reports/invoice-payments') ? 'active a' : '' }}">
                        Credit Invoice Payments
                    </a>
                </li>

                <li>
                    <a href="/reports/food-payment-report"
                        class="{{ request()->is('reports/food-payment-report') ? 'active a' : '' }}">
                        All Food Fee Details
                    </a>
                </li>

                <li>
                    <a href="/reports/student-attendance"
                        class="{{ request()->is('reports/student-attendance') ? 'active a' : '' }}">
                        Student Attendance
                    </a>
                </li>
            </ul>
        </li>
        {{-- ----- --}}
        {{-- ================== --}}

        {{-- Role Management Section --}}
        <span class="menu_seperator" id="seperator_role_management_section"
            data-section="role_management_section">Role Management
            Section
        </span>

        <li
            class="role_management_section {{ request()->is('role-management') || request()->is('role-management/*') ? 'mm-active' : '' }}">
            <a href="javascript:void(0)" class="has-arrow" aria-expanded="false">
                <div class="nav_icon_small">
                    <span class="fas fa-user-tag"></span>
                </div>
                <div class="nav_title">
                    <span>Role Management</span>
                </div>
            </a>
            <ul
                class="mm-collapse {{ request()->is('role-management') || request()->is('role-management/*') ? 'mm-show' : '' }}">
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
                <li>
                    <a href="/role-management/user-list"
                        class="{{ request()->is('role-management/user-list') ? 'active a' : '' }}">
                        User List
                    </a>
                </li>
            </ul>
        </li>
        {{-- ================== --}}

    </ul>
</nav>