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


    <ul class="sidebar_menu list-unstyled" id="sidebar_menu">


        {{-- Dashboard Section --}}
        @if (Auth::check() && Auth::user()->hasPermission('dashboard'))
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
        @endif
        {{-- ================== --}}


        {{-- Adminstration Section --}}
        @if (Auth::check() &&
                (Auth::user()->hasPermission('course.index') ||
                    Auth::user()->hasPermission('intake.index') ||
                    Auth::user()->hasPermission('subject.index') ||
                    Auth::user()->hasPermission('assign-subject.index') ||
                    Auth::user()->hasPermission('lesson.index') ||
                    Auth::user()->hasPermission('topic.index') ||
                    Auth::user()->hasPermission('topic-overview.index') ||
                    Auth::user()->hasPermission('upload-content') ||
                    Auth::user()->hasPermission('study-material-download') ||
                    Auth::user()->hasPermission('other-download') ||
                    Auth::user()->hasPermission('shared-content-list') ||
                    Auth::user()->hasPermission('video-list')))
            <span class="menu_seperator" id="seperator_administration_section"
                data-section="administration_section">Adminstration Section
            </span>
        @endif

        {{-- Academics --}}
        @if (Auth::check() &&
                (Auth::user()->hasPermission('course.index') ||
                    Auth::user()->hasPermission('intake.index') ||
                    Auth::user()->hasPermission('subject.index') ||
                    Auth::user()->hasPermission('assign-subject.index')))
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
                    @if (Auth::check() && Auth::user()->hasPermission('course.index'))
                        <li>
                            <a href="/administration/academic/courses"
                                class="{{ request()->is('administration/academic/courses') ? 'active a' : '' }}">
                                Courses
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('intake.index'))
                        <li>
                            <a href="/administration/academic/intake"
                                class="{{ request()->is('administration/academic/intake') ? 'active a' : '' }}">
                                Intake
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('subject.index'))
                        <li>
                            <a href="/administration/academic/subjects"
                                class="{{ request()->is('administration/academic/subjects') ? 'active a' : '' }}">
                                Subjects
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('assign-subject.index'))
                        <li>
                            <a href="/administration/academic/assign-subject"
                                class="{{ request()->is('administration/academic/assign-subject') ? 'active a' : '' }}">
                                Assign Subject
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
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
        @if (Auth::check() &&
                (Auth::user()->hasPermission('lesson.index') ||
                    Auth::user()->hasPermission('topic.index') ||
                    Auth::user()->hasPermission('topic-overview.index')))
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
                    @if (Auth::check() && Auth::user()->hasPermission('lesson.index'))
                        <li>
                            <a href="/administration/lesson-plan/lesson"
                                class="{{ request()->is('administration/lesson-plan/lesson') ? 'active a' : '' }}">
                                Lesson
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('topic.index'))
                        <li>
                            <a href="/administration/lesson-plan/topic"
                                class="{{ request()->is('administration/lesson-plan/topic') ? 'active a' : '' }}">
                                Topic
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('topic-overview.index'))
                        <li>
                            <a href="/administration/lesson-plan/topic-overview"
                                class="{{ request()->is('administration/lesson-plan/topic-overview') ? 'active a' : '' }}">
                                Topic Overview
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        {{-- ----- --}}

        {{-- Download Center --}}
        @if (Auth::check() &&
                (Auth::user()->hasPermission('upload-content') ||
                    Auth::user()->hasPermission('study-material-download') ||
                    Auth::user()->hasPermission('other-download') ||
                    Auth::user()->hasPermission('shared-content-list') ||
                    Auth::user()->hasPermission('video-list')))
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
                    {{-- Content-Type - No explicit permission found, assuming it's generally accessible or covered by broader 'Download Center' access --}}
                    <li>
                        <a href="/administration/download-center/content-type"
                            class="{{ request()->is('administration/download-center/content-type') ? 'active a' : '' }}">
                            Content-Type
                        </a>
                    </li>
                    @if (Auth::check() && Auth::user()->hasPermission('shared-content-list'))
                        {{-- Assuming 'Content List' maps to 'shared-content-list' --}}
                        <li>
                            <a href="/administration/download-center/content-list"
                                class="{{ request()->is('administration/download-center/content-list') ? 'active a' : '' }}">
                                Content List
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('shared-content-list'))
                        <li>
                            <a href="/administration/download-center/shared-content-list"
                                class="{{ request()->is('administration/download-center/shared-content-list') ? 'active a' : '' }}">
                                Shared Content List
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('video-list'))
                        <li>
                            <a href="/administration/download-center/video-list"
                                class="{{ request()->is('administration/download-center/video-list') ? 'active a' : '' }}">
                                Video List
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        {{-- ----- --}}
        {{-- ================== --}}


        {{-- Student Section --}}
        @if (Auth::check() &&
                (Auth::user()->hasPermission('student-category.index') ||
                    Auth::user()->hasPermission('add-student.index') ||
                    Auth::user()->hasPermission('student-list') ||
                    Auth::user()->hasPermission('disabled-students') ||
                    Auth::user()->hasPermission('student-attendance') ||
                    Auth::user()->hasPermission('student.add-intake-with-courses.index') ||
                    Auth::user()->hasPermission('fee-group') ||
                    Auth::user()->hasPermission('fees.fees-type') ||
                    Auth::user()->hasPermission('fees.fee-invoice.add') ||
                    Auth::user()->hasPermission('fees.course-paid.index') ||
                    Auth::user()->hasPermission('fees.fee-invoice.index') ||
                    Auth::user()->hasPermission('fee-invoice-setting') ||
                    Auth::user()->hasPermission('fee-return')))
            <span class="menu_seperator" id="seperator_student_section" data-section="student_section">Student
                Section</span>
            <span class="menu_seperator" id="seperator_student_section" data-section="student_section">Student
                Section</span>
        @endif


        {{-- Student Info Section --}}
        @if (Auth::check() &&
                (Auth::user()->hasPermission('student-category.index') ||
                    Auth::user()->hasPermission('add-student.index') ||
                    Auth::user()->hasPermission('student-list') ||
                    Auth::user()->hasPermission('disabled-students') ||
                    Auth::user()->hasPermission('student-attendance') ||
                    Auth::user()->hasPermission('student.add-intake-with-courses.index')))
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
                    @if (Auth::check() && Auth::user()->hasPermission('student-category.index'))
                        <li>
                            <a href="/student/student-info/student-category"
                                class="{{ request()->is('student/student-info/student-category') ? 'active a' : '' }}">
                                Student Category
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('add-student.index'))
                        <li>
                            <a href="/student/student-info/add-student"
                                class="{{ request()->is('student/student-info/add-student') ? 'active a' : '' }}">
                                Add Student
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('student-list'))
                        <li>
                            <a href="/student/student-info/student-list"
                                class="{{ request()->is('student/student-info/student-list') ? 'active a' : '' }}">
                                Student List
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('disabled-students'))
                        <li>
                            <a href="/student/student-info/disabled-student"
                                class="{{ request()->is('student/student-info/disabled-student') ? 'active a' : '' }}">
                                Disabled Students
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('student-attendance'))
                        <li>
                            <a href="/student/student-info/student-attendance"
                                class="{{ request()->is('student/student-info/student-attendance') ? 'active a' : '' }}">
                                Student Attendance
                            </a>
                        </li>
                    @endif
                    {{-- <li>
                    <a href="/student/student-info/student-add-course"
                        class="{{ request()->is('student/student-info/student-add-course') ? 'active a' : '' }}">
                        Student Add Course
                    </a>
                </li> --}}
                    @if (Auth::check() && Auth::user()->hasPermission('student.add-intake-with-courses.index'))
                        <li>
                            <a href="/student/student-info/student-add-intake"
                                class="{{ request()->is('student/student-info/student-add-intake') ? 'active a' : '' }}">
                                Add Intake & Courses
                            </a>
                        </li>
                    @endif
                    {{-- <li>
                    <a href="/student/student-info/student-export" class="{{ request()->is('student/student-info/student-export') ? 'active a' : '' }}">
                        Student Export
                    </a>
                </li> --}}
                </ul>
            </li>
        @endif
        {{-- ----- --}}

        {{-- Fees Section --}}
        @if (Auth::check() &&
                (Auth::user()->hasPermission('fee-group') ||
                    Auth::user()->hasPermission('fees.fees-type') ||
                    Auth::user()->hasPermission('fees.fee-invoice.add') ||
                    Auth::user()->hasPermission('fees.course-paid.index') ||
                    Auth::user()->hasPermission('fees.fee-invoice.index') ||
                    Auth::user()->hasPermission('fee-invoice-setting') ||
                    Auth::user()->hasPermission('fee-return')))
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
                    @if (Auth::check() && Auth::user()->hasPermission('fee-group'))
                        <li>
                            <a href="/student/fees/fee-group"
                                class="{{ request()->is('student/fees/fee-group') ? 'active a' : '' }}">
                                Fee Group
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('fees.fees-type'))
                        <li>
                            <a href="/student/fees/fee-type"
                                class="{{ request()->is('student/fees/fee-type') ? 'active a' : '' }}">
                                Fee Type
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('fees.add-invoice'))
                        <li>
                            <a href="/student/fees/add-invoice"
                                class="{{ request()->is('student/fees/add-invoice') ? 'active a' : '' }}">
                                Add Invoice
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('fees.course-paid'))
                        <li>
                            <a href="/student/fees/course-paid"
                                class="{{ request()->is('student/fees/course-paid') ? 'active a' : '' }}">
                                Course Paid
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('fees.fee-invoice'))
                        <li>
                            <a href="/student/fees/fee-invoice"
                                class="{{ request()->is('student/fees/fee-invoice') ? 'active a' : '' }}">
                                Fee Invoice
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('fee-invoice-setting'))
                        <li>
                            <a href="/student/fees/fee-invoice-setting"
                                class="{{ request()->is('student/fees/fee-invoice-setting') ? 'active a' : '' }}">
                                Fee Invoice Setting
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('fee-return'))
                        <li>
                            <a href="/student/fees/fee-return"
                                class="{{ request()->is('student/fees/fee-return') ? 'active a' : '' }}">
                                Fee Return
                            </a>
                        </li>
                    @endif
                    {{-- @if (Auth::check() && Auth::user()->hasPermission('fee-return')) --}}
                    <li>
                        <a href="/student/fees/fee-invoice/invoice-payments/pending"
                            class="{{ request()->is('student/fees/fee-invoice/invoice-payments/pending') ? 'active a' : '' }}">
                            Fee Invoice Pending Payment
                        </a>
                    </li>
                    {{-- @endif --}}

                    {{-- @if (Auth::check() && Auth::user()->hasPermission('fee-return')) --}}
                    <li>
                        <a href="/student/fees/fee-invoice/invoice-payments"
                            class="{{ request()->is('student/fees/fee-invoice/invoice-payments') ? 'active a' : '' }}">
                            Fee Invoice Payment
                        </a>
                    </li>
                    {{-- @endif --}}
                </ul>
            </li>
        @endif
        {{-- ----- --}}
        {{-- ================== --}}


        {{-- Inventory Section --}}
        @if (Auth::check() &&
                (Auth::user()->hasPermission('item-category') ||
                    Auth::user()->hasPermission('item-list') ||
                    Auth::user()->hasPermission('supplier') ||
                    Auth::user()->hasPermission('item-receive') ||
                    Auth::user()->hasPermission('item-issue') ||
                    Auth::user()->hasPermission('item-sell')))
            <span class="menu_seperator" id="seperator_inventory_section" data-section="inventory_section">Account
                Section
            </span>
        @endif

        {{-- Inventory Section --}}
        @if (Auth::check() &&
                (Auth::user()->hasPermission('item-category') ||
                    Auth::user()->hasPermission('item-list') ||
                    Auth::user()->hasPermission('supplier') ||
                    Auth::user()->hasPermission('item-receive') ||
                    Auth::user()->hasPermission('item-issue') ||
                    Auth::user()->hasPermission('item-sell')))
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
                    @if (Auth::check() && Auth::user()->hasPermission('item-category'))
                        <li>
                            <a href="/account/inventory/item-category"
                                class="{{ request()->is('account/inventory/item-category') ? 'active a' : '' }}">
                                Item Category
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('item-list'))
                        <li>
                            <a href="/account/inventory/item-list"
                                class="{{ request()->is('account/inventory/item-list') ? 'active a' : '' }}">
                                Item List
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('supplier'))
                        <li>
                            <a href="/account/inventory/supplier"
                                class="{{ request()->is('account/inventory/supplier') ? 'active a' : '' }}">
                                Supplier
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('item-receive'))
                        <li>
                            <a href="/account/inventory/item-receive"
                                class="{{ request()->is('account/inventory/item-receive') ? 'active a' : '' }}">
                                Item Receive
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('item-receive'))
                        {{-- Using item-receive for Item Receive List --}}
                        <li>
                            <a href="/account/inventory/item-receive-list"
                                class="{{ request()->is('account/inventory/item-receive-list') ? 'active a' : '' }}">
                                Item Receive List
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('item-issue'))
                        <li>
                            <a href="/account/inventory/item-issue"
                                class="{{ request()->is('account/inventory/item-issue') ? 'active a' : '' }}">
                                Item Issue
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('item-issue'))
                        {{-- Using item-issue for Item Issue List --}}
                        <li>
                            <a href="/account/inventory/item-issue-list"
                                class="{{ request()->is('account/inventory/item-issue-list') ? 'active a' : '' }}">
                                Item Issue List
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('item-sell'))
                        <li>
                            <a href="/account/inventory/item-sell"
                                class="{{ request()->is('account/inventory/item-sell') ? 'active a' : '' }}">
                                Item Sell
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('item-sell'))
                        {{-- Using item-sell for Item Sell List --}}
                        <li>
                            <a href="/account/inventory/item-sell-list"
                                class="{{ request()->is('account/inventory/item-sell-list') ? 'active a' : '' }}">
                                Item Sell List
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        {{-- ----- --}}
        {{-- Reports Section --}}
        @if (Auth::check() &&
                (Auth::user()->hasPermission('fees.course-paid') ||
                    Auth::user()->hasPermission('item-sell') ||
                    Auth::user()->hasPermission('item-issue') ||
                    Auth::user()->hasPermission('fees.fee-invoice')))
            <span class="menu_seperator" id="seperator_reports_section" data-section="reports_section">Reports
                Section
            </span>
        @endif

        @if (Auth::check() &&
                (Auth::user()->hasPermission('fees.course-paid') ||
                    Auth::user()->hasPermission('item-sell') ||
                    Auth::user()->hasPermission('item-issue') ||
                    Auth::user()->hasPermission('fees.fee-invoice')))
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
                <ul class="mm-collapse {{ request()->is('reports') || request()->is('reports/*') ? 'mm-show' : '' }}">

                    @if (Auth::check() && Auth::user()->hasPermission('item-sell'))
                        <li>
                            <a href="/reports/sell-items"
                                class="{{ request()->is('reports/sell-items') ? 'active a' : '' }}">
                                Sell Items
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('item-issue'))
                        <li>
                            <a href="/reports/issue-items"
                                class="{{ request()->is('reports/issue-items') ? 'active a' : '' }}">
                                Issue Items
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('fees.fee-invoice'))
                        <li>
                            <a href="/reports/students-paid"
                                class="{{ request()->is('reports/students-paid') ? 'active a' : '' }}">
                                Payments List
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('fees.course-paid'))
                        <li>
                            <a href="/reports/course-paid"
                                class="{{ request()->is('reports/course-paid') ? 'active a' : '' }}">
                                Course Paid
                            </a>
                        </li>
                    @endif
                    {{-- @if (Auth::check() && Auth::user()->hasPermission('fees.course-intake-payments')) --}}
                    <li>
                        <a href="/reports/course-dates-payments"
                            class="{{ request()->is('reports/course-dates-payments') ? 'active a' : '' }}">
                            Course Dates Payments
                        </a>
                    </li>

                    <li>
                        <a href="/reports/course-intake-payments"
                            class="{{ request()->is('reports/course-intake-payments') ? 'active a' : '' }}">
                            Course Intake Payments
                        </a>
                    </li>

                    <li>
                        <a href="/reports/students-intake-balance"
                            class="{{ request()->is('reports/students-intake-balance') ? 'active a' : '' }}">
                            Students Intake Balance
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
                            Invoice Payments
                        </a>
                    </li>
                    {{-- @endif --}}
                </ul>
            </li>
        @endif
        {{-- ----- --}}
        {{-- ================== --}}

        {{-- Role Management Section --}}
        @if (Auth::check() &&
                (Auth::user()->hasPermission('roles.index') || Auth::user()->hasPermission('role-management.create-user')))
            <span class="menu_seperator" id="seperator_role_management_section"
                data-section="role_management_section">Role Management
                Section
            </span>
        @endif

        @if (Auth::check() &&
                (Auth::user()->hasPermission('roles.index') || Auth::user()->hasPermission('role-management.create-user')))
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
                    @if (Auth::check() && Auth::user()->hasPermission('roles.index'))
                        <li>
                            <a href="/role-management/roles"
                                class="{{ request()->is('role-management/roles') ? 'active a' : '' }}">
                                Roles
                            </a>
                        </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('role-management.create-user'))
                        <li>
                            <a href="/role-management/create-user"
                                class="{{ request()->is('role-management/create-user') ? 'active a' : '' }}">
                                Create User
                            </a>
                        </li>
                    @endif
                    {{-- @if (Auth::check() && Auth::user()->hasPermission('role-management.user-list')) --}}
                    <li>
                        <a href="/role-management/user-list"
                            class="{{ request()->is('role-management/user-list') ? 'active a' : '' }}">
                            User List
                        </a>
                    </li>
                    {{-- @endif --}}
                </ul>
            </li>
        @endif
        {{-- ================== --}}

        {{-- Logout Button --}}
        {{-- <li>
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
        </li> --}}
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
