<nav id="sidebar" class="sidebar">
    @php use Illuminate\Support\Facades\Auth; @endphp
    <style>
        /* UPDATE (Section 41): belt-and-suspenders for the scroll-containment
           fix below — makes sure the sidebar itself never grows a second
           scrollbar of its own if the base theme's #sidebar rule (defined
           elsewhere, not in this file) turns out to allow it. Submenus
           expand inline rather than flying out, so this doesn't clip
           anything. */
        #sidebar {
            overflow: hidden;
        }

        .sidebar_menu {
            display: block;
            flex-direction: column;
            height: calc(100% - 100px);
            /* Adjust height as needed, considering header/footer */
            /* UPDATE (Section 41): scrolling now happens inside the menu
               list itself instead of the whole sidebar — the height above
               was already capping this element, it just never had overflow
               turned on, so long menus had nowhere to go but to overflow
               past the sidebar. The header stays fixed in place since it's
               a sibling outside this scrollable box, not wrapped by it. */
            overflow-y: auto;
            overflow-x: hidden;
        }

        #sidebar_menu::-webkit-scrollbar {
            width: 5px;
        }

        #sidebar_menu::-webkit-scrollbar-track {
            background: transparent;
        }

        #sidebar_menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.18);
            border-radius: 10px;
        }

        #sidebar_menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.32);
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


        .sidebar-header {
            background-color: var(--sidebar_bg);
        }

        @media (min-width: 992px) {
            .sidebar-header {
                background-color: var(--text_white);
            }

            #sidebar .sidebar-header {
                padding: 0;
            }

            .sidebar-header-sub {
                background-color: var(--sidebar_bg);
                border-top-right-radius: 40px;
                padding: 15px 15px 15px 15px;

            }
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

        /* ===== Modern refresh: sidebar navigation (Section 36) ===== */
        .sidebar-header.update_sidebar {
            padding: 22px 16px;
            /* UPDATE (Section 41): header strip is now white, per request —
               was transparent before, which just showed the dark sidebar
               background behind it. Border color switched to match, since
               the old rgba(255,255,255,.08) divider was tuned for a dark
               background and would have been invisible on white. */
            background: #fff;
            border-bottom: 1px solid #eceef3;
        }

        .sidebar-header .logo-area img {
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.12));
        }

        /* Close ("X") icon in the mobile header now sits on a white
           background instead of dark, so it needs a dark color to stay
           visible — it had no color rule before and was presumably
           inheriting a light color meant for the dark sidebar body. */
        #close_sidebar i {
            color: #000000;
            transition: color 0.2s ease;
        }

        #close_sidebar:hover i {
            color: var(--gradient_1);
        }

        .sidebar-name {
            letter-spacing: 0.4px;
            opacity: 0.92;
        }

        .sidebar_menu .menu_seperator {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            opacity: 0.45;
            padding: 2px 5px 0px;
        }

        .sidebar_menu>li {
            margin: 2px 2px;
            border-radius: 8px;
            overflow: hidden;
        }

        .sidebar_menu>li>a {
            border-radius: 8px;
            transition: background 0.2s ease, color 0.2s ease, padding-left 0.2s ease;
        }

        .sidebar_menu>li>a:hover {
            background: rgba(255, 255, 255, 0.06);
            padding-left: 24px;
        }

        .sidebar_menu>li.mm-active>a,
        .sidebar_menu>li>a.active {
            background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2));
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        .sidebar_menu .has-arrow::after {
            transition: transform 0.25s ease;
        }

        .sidebar_menu li.mm-active>a.has-arrow::after {
            transform: rotate(90deg);
        }

        .sidebar_menu ul.mm-collapse {
            transition: all 0.25s ease;
        }

        .sidebar_menu ul.mm-collapse li a {
            border-radius: 6px;
            transition: background 0.2s ease, padding-left 0.2s ease;
        }

        .sidebar_menu ul.mm-collapse li a:hover,
        .sidebar_menu ul.mm-collapse li a.active {
            background: rgba(255, 255, 255, 0.08);
            padding-left: 4px;
        }
    </style>

    <div class="sidebar-header update_sidebar d-lg-flex d-none">
        <div class="sidebar-header-sub">
            <a href="/" id="admin-dashboard" class="logo-area">
                <img src="{{ asset('uploads/settings/logo.png') }}" alt="logo" style="max-width: 100px;">
            </a>
            <div class="sidebar-name mt-2" style="font-size:14px; font-weight:600;">
                HIGHTEC LANKA INTERNATIONAL
            </div>

            <a id="close_sidebar" class="d-lg-none">
                <i class="ti-close" style="font-size:20px;"></i>
            </a>
        </div>


    </div>

    <div class="sidebar-header update_sidebar d-flex d-lg-none">
            <a href="/" id="admin-dashboard" class="logo-area">
                <img src="{{ asset('uploads/settings/logo.png') }}" alt="logo" style="max-width: 100px;">
            </a>
            <div class="sidebar-name mt-2" style="font-size:14px; font-weight:600;">
                HIGHTEC LANKA INTERNATIONAL
            </div>

            <a id="close_sidebar" class="d-lg-none">
                <i class="ti-close" style="font-size:20px;"></i>
            </a>
    </div>



    <ul class="sidebar_menu list-unstyled" id="sidebar_menu">

        {{-- Dashboard Section --}}
        <span class="menu_seperator" id="seperator_dashboard_section" data-section="dashboard_section">Dashboard
            Section</span>

        @if (Auth::check() && Auth::user()->hasPermission('dashboard'))
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
                    Auth::user()->hasPermission('content-type') ||
                    Auth::user()->hasPermission('download-center.content-list') ||
                    Auth::user()->hasPermission('download-center.shared-content-list') ||
                    Auth::user()->hasPermission('download-center.video-list')))
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
                    @if (Auth::user()->hasPermission('course.index'))
                        <li>
                            <a href="/administration/academic/courses"
                                class="{{ request()->is('administration/academic/courses') ? 'active a' : '' }}">
                                Courses
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('intake.index'))
                        <li>
                            <a href="/administration/academic/intake"
                                class="{{ request()->is('administration/academic/intake') ? 'active a' : '' }}">
                                Intake
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('subject.index'))
                        <li>
                            <a href="/administration/academic/subjects"
                                class="{{ request()->is('administration/academic/subjects') ? 'active a' : '' }}">
                                Subjects
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('assign-subject.index'))
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
                    @if (Auth::user()->hasPermission('lesson.index'))
                        <li>
                            <a href="/administration/lesson-plan/lesson"
                                class="{{ request()->is('administration/lesson-plan/lesson') ? 'active a' : '' }}">
                                Lesson
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('topic.index'))
                        <li>
                            <a href="/administration/lesson-plan/topic"
                                class="{{ request()->is('administration/lesson-plan/topic') ? 'active a' : '' }}">
                                Topic
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('topic-overview.index'))
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
                (Auth::user()->hasPermission('content-type') ||
                    Auth::user()->hasPermission('download-center.content-list') ||
                    Auth::user()->hasPermission('download-center.shared-content-list') ||
                    Auth::user()->hasPermission('download-center.video-list')))
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
                    @if (Auth::user()->hasPermission('content-type'))
                        <li>
                            <a href="/administration/download-center/content-type"
                                class="{{ request()->is('administration/download-center/content-type') ? 'active a' : '' }}">
                                Content-Type
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('download-center.content-list'))
                        <li>
                            <a href="/administration/download-center/content-list"
                                class="{{ request()->is('administration/download-center/content-list') ? 'active a' : '' }}">
                                Content List
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('download-center.shared-content-list'))
                        <li>
                            <a href="/administration/download-center/shared-content-list"
                                class="{{ request()->is('administration/download-center/shared-content-list') ? 'active a' : '' }}">
                                Shared Content List
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('download-center.video-list'))
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
                    Auth::user()->hasPermission('scan-attendance') ||
                    Auth::user()->hasPermission('student.add-intake-with-courses.index') ||
                    Auth::user()->hasPermission('fee-group') ||
                    Auth::user()->hasPermission('fees.fees-type') ||
                    Auth::user()->hasPermission('fees.add-invoice') ||
                    Auth::user()->hasPermission('fees.course-payment') ||
                    Auth::user()->hasPermission('fees.fee-invoice') ||
                    Auth::user()->hasPermission('fees.fee-return.search') ||
                    Auth::user()->hasPermission('invoice-pending-payments.index') ||
                    Auth::user()->hasPermission('invoice-payments')))
            <span class="menu_seperator" id="seperator_student_section" data-section="student_section">Student
                Section</span>
        @endif

        {{-- Student Info Section --}}
        @if (Auth::check() &&
                (Auth::user()->hasPermission('student-category.index') ||
                    Auth::user()->hasPermission('add-student.index') ||
                    Auth::user()->hasPermission('student-list') ||
                    Auth::user()->hasPermission('disabled-students') ||
                    Auth::user()->hasPermission('scan-attendance') ||
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
                    @if (Auth::user()->hasPermission('student-category.index'))
                        <li>
                            <a href="/student/student-info/student-category"
                                class="{{ request()->is('student/student-info/student-category') ? 'active a' : '' }}">
                                Student Category
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('add-student.index'))
                        <li>
                            <a href="/student/student-info/add-student"
                                class="{{ request()->is('student/student-info/add-student') ? 'active a' : '' }}">
                                Add Student
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('student-list'))
                        <li>
                            <a href="/student/student-info/student-list"
                                class="{{ request()->is('student/student-info/student-list') ? 'active a' : '' }}">
                                Student List
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('disabled-students'))
                        <li>
                            <a href="/student/student-info/disabled-student"
                                class="{{ request()->is('student/student-info/disabled-student') ? 'active a' : '' }}">
                                Disabled Students
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('scan-attendance'))
                        <li>
                            <a href="/student/student-info/scan-attendance"
                                class="{{ request()->is('student/student-info/scan-attendance') ? 'active a' : '' }}">
                                Student Attendance
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('student.add-intake-with-courses.index'))
                        <li>
                            <a href="/student/student-info/student-add-intake"
                                class="{{ request()->is('student/student-info/student-add-intake') ? 'active a' : '' }}">
                                Add Intake & Courses
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        {{-- ----- --}}

        {{-- Fees Section --}}
        @if (Auth::check() &&
                (Auth::user()->hasPermission('fee-group') ||
                    Auth::user()->hasPermission('fees.fees-type') ||
                    Auth::user()->hasPermission('fees.add-invoice') ||
                    Auth::user()->hasPermission('fees.course-payment') ||
                    Auth::user()->hasPermission('fees.fee-invoice') ||
                    Auth::user()->hasPermission('fees.fee-return.search') ||
                    Auth::user()->hasPermission('invoice-pending-payments.index') ||
                    Auth::user()->hasPermission('invoice-payments')))
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
                    @if (Auth::user()->hasPermission('fee-group'))
                        <li>
                            <a href="/student/fees/fee-group"
                                class="{{ request()->is('student/fees/fee-group') ? 'active a' : '' }}">
                                Invoice Item Group
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('fees.fees-type'))
                        <li>
                            <a href="/student/fees/fee-type"
                                class="{{ request()->is('student/fees/fee-type') ? 'active a' : '' }}">
                                Invoice Type
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('fees.add-invoice'))
                        <li>
                            <a href="/student/fees/other-payment"
                                class="{{ request()->is('student/fees/other-payment') ? 'active a' : '' }}">
                                Registration & Other Payments
                            </a>
                        </li>
                    @endif
                    {{-- ⚠️ SEE THE FLAG IN permission-naming-plan.md: no route in your web.php matches
                         /student/fees/course-paid, the production URL you showed me. The only real
                         route here is fees.course-payment (/student/fees/course-payment). Using that
                         key below until you confirm which one is actually correct. --}}
                    @if (Auth::user()->hasPermission('fees.course-payment'))
                        <li>
                            <a href="/student/fees/course-payment"
                                class="{{ request()->is('student/fees/course-payment') ? 'active a' : '' }}">
                                Course, NVQ & Food Payment
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('fees.fee-invoice'))
                        <li>
                            <a href="/student/fees/invoice-list"
                                class="{{ request()->is('student/fees/invoice-list') ? 'active a' : '' }}">
                                Invoice List
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->hasPermission('fees.fee-return.search'))
                        <li>
                            <a href="/student/fees/fee-return"
                                class="{{ request()->is('student/fees/fee-return') ? 'active a' : '' }}">
                                Cancelled Invoice
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->hasPermission('invoice-pending-payments.index'))
                        <li>
                            <a href="/student/fees/fee-invoice/invoice-payments/pending"
                                class="{{ request()->is('student/fees/fee-invoice/invoice-payments/pending') ? 'active a' : '' }}">
                                Credit Invoice
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->hasPermission('invoice-payments'))
                        <li>
                            <a href="/student/fees/fee-invoice/invoice-payments"
                                class="{{ request()->is('student/fees/fee-invoice/invoice-payments') ? 'active a' : '' }}">
                                Payment for Credit Invoice
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        {{-- ----- --}}
        {{-- ================== --}}



        @if (Auth::check() &&
                (Auth::user()->hasPermission('item-category') ||
                    Auth::user()->hasPermission('item-list') ||
                    Auth::user()->hasPermission('supplier') ||
                    Auth::user()->hasPermission('item-receive') ||
                    Auth::user()->hasPermission('item-receive-list') ||
                    Auth::user()->hasPermission('item-issue') ||
                    Auth::user()->hasPermission('item-issue-list') ||
                    Auth::user()->hasPermission('item-sell') ||
                    Auth::user()->hasPermission('item-sell-list')))
            <span class="menu_seperator" id="seperator_inventory_section" data-section="inventory_section">Account
                Section
            </span>
        @endif

        @if (Auth::check() &&
                (Auth::user()->hasPermission('item-category') ||
                    Auth::user()->hasPermission('item-list') ||
                    Auth::user()->hasPermission('supplier') ||
                    Auth::user()->hasPermission('item-receive') ||
                    Auth::user()->hasPermission('item-receive-list') ||
                    Auth::user()->hasPermission('item-issue') ||
                    Auth::user()->hasPermission('item-issue-list') ||
                    Auth::user()->hasPermission('item-sell') ||
                    Auth::user()->hasPermission('item-sell-list')))
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
                    @if (Auth::user()->hasPermission('item-category'))
                        <li>
                            <a href="/account/inventory/item-category"
                                class="{{ request()->is('account/inventory/item-category') ? 'active a' : '' }}">
                                Item Category
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('item-list'))
                        <li>
                            <a href="/account/inventory/item-list"
                                class="{{ request()->is('account/inventory/item-list') ? 'active a' : '' }}">
                                Item List
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('supplier'))
                        <li>
                            <a href="/account/inventory/supplier"
                                class="{{ request()->is('account/inventory/supplier') ? 'active a' : '' }}">
                                Supplier
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('item-receive'))
                        <li>
                            <a href="/account/inventory/item-receive"
                                class="{{ request()->is('account/inventory/item-receive') ? 'active a' : '' }}">
                                Item Receive
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('item-receive-list'))
                        <li>
                            <a href="/account/inventory/item-receive/list"
                                class="{{ request()->is('account/inventory/item-receive/list') ? 'active a' : '' }}">
                                Item Receive List
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('item-issue'))
                        <li>
                            <a href="/account/inventory/item-issue"
                                class="{{ request()->is('account/inventory/item-issue') ? 'active a' : '' }}">
                                Item Issue
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('item-issue-list'))
                        <li>
                            <a href="/account/inventory/item-issue/list"
                                class="{{ request()->is('account/inventory/item-issue/list') ? 'active a' : '' }}">
                                Item Issue List
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('item-sell'))
                        <li>
                            <a href="/account/inventory/item-sell"
                                class="{{ request()->is('account/inventory/item-sell') ? 'active a' : '' }}">
                                Item Sell
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('item-sell-list'))
                        <li>
                            <a href="/account/inventory/item-sell/list"
                                class="{{ request()->is('account/inventory/item-sell/list') ? 'active a' : '' }}">
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
                (Auth::user()->hasPermission('reports.sell-items') ||
                    Auth::user()->hasPermission('reports.issue-items') ||
                    Auth::user()->hasPermission('reports.students-paid') ||
                    Auth::user()->hasPermission('reports.course-paid') ||
                    Auth::user()->hasPermission('reports.course-dates-payments') ||
                    Auth::user()->hasPermission('reports.course-intake-payments') ||
                    Auth::user()->hasPermission('reports.students-intake-balance') ||
                    Auth::user()->hasPermission('reports.change-course') ||
                    Auth::user()->hasPermission('reports.invoice-payments') ||
                    Auth::user()->hasPermission('reports.food-payment') ||
                    Auth::user()->hasPermission('reports.scan-attendance')))
            <span class="menu_seperator" id="seperator_reports_section" data-section="reports_section">Reports
                Section
            </span>
        @endif

        @if (Auth::check() &&
                (Auth::user()->hasPermission('reports.sell-items') ||
                    Auth::user()->hasPermission('reports.issue-items') ||
                    Auth::user()->hasPermission('reports.students-paid') ||
                    Auth::user()->hasPermission('reports.course-paid') ||
                    Auth::user()->hasPermission('reports.course-dates-payments') ||
                    Auth::user()->hasPermission('reports.course-intake-payments') ||
                    Auth::user()->hasPermission('reports.students-intake-balance') ||
                    Auth::user()->hasPermission('reports.change-course') ||
                    Auth::user()->hasPermission('reports.invoice-payments') ||
                    Auth::user()->hasPermission('reports.food-payment') ||
                    Auth::user()->hasPermission('reports.scan-attendance')))
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

                    @if (Auth::user()->hasPermission('reports.sell-items'))
                        <li>
                            <a href="/reports/sell-items"
                                class="{{ request()->is('reports/sell-items') ? 'active a' : '' }}">
                                Sell Items
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('reports.issue-items'))
                        <li>
                            <a href="/reports/issue-items"
                                class="{{ request()->is('reports/issue-items') ? 'active a' : '' }}">
                                Issue Items
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('reports.students-paid'))
                        <li>
                            <a href="/reports/students-paid"
                                class="{{ request()->is('reports/students-paid') ? 'active a' : '' }}">
                                Collection Report
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('reports.course-paid'))
                        <li>
                            <a href="/reports/course-paid"
                                class="{{ request()->is('reports/course-paid') ? 'active a' : '' }}">
                                Course Paid
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->hasPermission('reports.course-dates-payments'))
                        <li>
                            <a href="/reports/course-dates-payments"
                                class="{{ request()->is('reports/course-dates-payments') ? 'active a' : '' }}">
                                Course Started & Course Completed
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->hasPermission('reports.course-intake-payments'))
                        <li>
                            <a href="/reports/course-intake-payments"
                                class="{{ request()->is('reports/course-intake-payments') ? 'active a' : '' }}">
                                Course Started & Course Balance Status
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->hasPermission('reports.students-intake-balance'))
                        <li>
                            <a href="/reports/students-intake-balance"
                                class="{{ request()->is('reports/students-intake-balance') ? 'active a' : '' }}">
                                Course Fee Balances as Intake Wise
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->hasPermission('reports.change-course'))
                        <li>
                            <a href="/reports/change-course"
                                class="{{ request()->is('reports/change-course') ? 'active a' : '' }}">
                                Change Course
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->hasPermission('reports.invoice-payments'))
                        <li>
                            <a href="/reports/invoice-payments"
                                class="{{ request()->is('reports/invoice-payments') ? 'active a' : '' }}">
                                Credit Invoice Payments
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->hasPermission('reports.food-payment'))
                        <li>
                            <a href="/reports/food-payment-report"
                                class="{{ request()->is('reports/food-payment-report') ? 'active a' : '' }}">
                                All Food Fee Details
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->hasPermission('reports.scan-attendance'))
                        <li>
                            <a href="/reports/scan-attendance"
                                class="{{ request()->is('reports/scan-attendance') ? 'active a' : '' }}">
                                Student Attendance
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        {{-- ----- --}}
        {{-- ================== --}}

        {{-- Role Management Section --}}
        @if (Auth::check() &&
                (Auth::user()->hasPermission('roles.index') ||
                    Auth::user()->hasPermission('role-management.create-user') ||
                    Auth::user()->hasPermission('role-management.user-list')))
            <span class="menu_seperator" id="seperator_role_management_section"
                data-section="role_management_section">Role Management
                Section
            </span>
        @endif

        @if (Auth::check() &&
                (Auth::user()->hasPermission('roles.index') ||
                    Auth::user()->hasPermission('role-management.create-user') ||
                    Auth::user()->hasPermission('role-management.user-list')))
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
                    @if (Auth::user()->hasPermission('roles.index'))
                        <li>
                            <a href="/role-management/roles"
                                class="{{ request()->is('role-management/roles') ? 'active a' : '' }}">
                                Roles
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('role-management.create-user'))
                        <li>
                            <a href="/role-management/create-user"
                                class="{{ request()->is('role-management/create-user') ? 'active a' : '' }}">
                                Create User
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasPermission('role-management.user-list'))
                        <li>
                            <a href="/role-management/user-list"
                                class="{{ request()->is('role-management/user-list') ? 'active a' : '' }}">
                                User List
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        {{-- ================== --}}

    </ul>
</nav>
