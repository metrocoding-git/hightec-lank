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
        FIXED: was `Auth::user()->id == 31 || ... == 34` — four raw user IDs
        hardcoded into the view. Replaced with a real permission check,
        'demo-panel.view'. To migrate: create that permission, put it on a
        role (e.g. "Demo"), and assign that role to the same 4 accounts —
        removing/adding someone from this view is then a role change, not a
        code change. Until you create + assign this permission, nobody will
        match this branch (hasPermission() should return false for a
        permission that doesn't exist yet) — so do that before removing the
        old ID check from production, or you'll temporarily send those 4
        users into the @else branch below instead.
    --}}
    @if (Auth::check() && Auth::user()->hasPermission('demo-panel.view'))
        {{-- FIXED: this <ul> was missing the "sidebar_menu" class the @else
             branch has, so the flex layout / bottom-pinned last-item CSS
             above never applied here — added it back for visual consistency. --}}
        <ul class="sidebar_menu list-unstyled" id="sidebar_menu">

            {{-- Dashboard Section --}}
            @if (Auth::check() && Auth::user()->hasPermission('dashboard'))
                <span class="menu_seperator" id="seperator_dashboard_section" data-section="dashboard_section">Demo
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

                <li
                    class="administration_section {{ request()->is('student/student-info/scan-attendance') ? 'mm-active' : '' }} ">
                    <a href="/student/student-info/scan-attendance">
                        <div class="nav_icon_small">
                            <span class="fas fa-user"></span>
                        </div>
                        <div class="nav_title">
                            <span>Student Attendance</span>
                        </div>
                    </a>
                </li>

                <li class="reports_section {{ request()->is('reports/student-attendance') ? 'mm-active' : '' }} ">
                    <a href="/reports/student-attendance">
                        <div class="nav_icon_small">
                            <span class="fas fa-chart-line"></span>
                        </div>
                        <div class="nav_title">
                            <span> Student Attendance Report</span>
                        </div>
                    </a>

                </li>
            @endif

        </ul>
    @else
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
                        Auth::user()->hasPermission('content-type.index') ||
                        Auth::user()->hasPermission('download-center.shared-content-list') ||
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
                    (Auth::user()->hasPermission('content-type.index') ||
                        Auth::user()->hasPermission('download-center.shared-content-list') ||
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
                        {{--
                            FIXED: was completely ungated ("no explicit permission found").
                            Added a permission check using 'content-type.index'. This is a
                            NEW permission key — create it in your permissions table and
                            assign it to whichever roles should see this, or every user
                            will lose access to this link until you do.
                        --}}
                        @if (Auth::check() && Auth::user()->hasPermission('content-type.index'))
                            <li>
                                <a href="/administration/download-center/content-type"
                                    class="{{ request()->is('administration/download-center/content-type') ? 'active a' : '' }}">
                                    Content-Type
                                </a>
                            </li>
                        @endif
                        {{--
                            NOTE (not changed): "Content List" and "Shared Content List" both
                            check 'shared-content-list' — same permission gates two different
                            links. Left as-is since I don't know if that's intentional; give me
                            a distinct permission key (e.g. 'content-list') if they should be
                            separated.
                        --}}
                        @if (Auth::check() && Auth::user()->hasPermission('download-center.shared-content-list'))
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


            {{--
                FIXED: this separator was duplicated (rendered twice back-to-back
                in the original). Also fixed the permission list to match what the
                Fees items below actually check — the original said
                'fees.fee-invoice.add' / 'fees.course-paid.index' / 'fees.fee-invoice.index',
                but the real links check 'fees.add-invoice' / 'fees.course-paid' /
                'fees.fee-invoice' (no .add/.index suffix). With the mismatched keys,
                a user who could see individual Fees links could still have had this
                whole section header hidden. Added the two new Credit Invoice
                permissions too (see the Fees block below) so the header can't hide
                a section that has visible items.
            --}}
            @if (Auth::check() &&
                    (Auth::user()->hasPermission('student-category.index') ||
                        Auth::user()->hasPermission('add-student.index') ||
                        Auth::user()->hasPermission('student-list') ||
                        Auth::user()->hasPermission('disabled-students') ||
                        Auth::user()->hasPermission('student-attendance') ||
                        Auth::user()->hasPermission('student.add-intake-with-courses.index') ||
                        Auth::user()->hasPermission('fee-group') ||
                        Auth::user()->hasPermission('fees.fees-type') ||
                        Auth::user()->hasPermission('fees.add-invoice') ||
                        Auth::user()->hasPermission('fees.course-paid') ||
                        Auth::user()->hasPermission('fees.fee-invoice') ||
                        Auth::user()->hasPermission('fee-return') ||
                        Auth::user()->hasPermission('fees.credit-invoice') ||
                        Auth::user()->hasPermission('fees.credit-invoice-payment')))
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
                                <a href="/student/student-info/scan-attendance"
                                    class="{{ request()->is('student/student-info/scan-attendance') ? 'active a' : '' }}">
                                    Student Attendance
                                </a>
                            </li>
                        @endif
                        @if (Auth::check() && Auth::user()->hasPermission('student.add-intake-with-courses.index'))
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
            {{--
                FIXED: wrapper condition used to check 'fees.fee-invoice.add' /
                'fees.course-paid.index' / 'fees.fee-invoice.index' (permissions
                that no link below actually uses) — swapped for the real keys the
                items check, same fix as the Student Section separator above.
            --}}
            @if (Auth::check() &&
                    (Auth::user()->hasPermission('fee-group') ||
                        Auth::user()->hasPermission('fees.fees-type') ||
                        Auth::user()->hasPermission('fees.add-invoice') ||
                        Auth::user()->hasPermission('fees.course-paid') ||
                        Auth::user()->hasPermission('fees.fee-invoice') ||
                        Auth::user()->hasPermission('fee-return') ||
                        Auth::user()->hasPermission('fees.credit-invoice') ||
                        Auth::user()->hasPermission('fees.credit-invoice-payment')))
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
                                    Invoice Item Group
                                </a>
                            </li>
                        @endif
                        @if (Auth::check() && Auth::user()->hasPermission('fees.fees-type'))
                            <li>
                                <a href="/student/fees/fee-type"
                                    class="{{ request()->is('student/fees/fee-type') ? 'active a' : '' }}">
                                    Invoice Type
                                </a>
                            </li>
                        @endif
                        @if (Auth::check() && Auth::user()->hasPermission('fees.add-invoice'))
                            <li>
                                <a href="/student/fees/other-payment"
                                    class="{{ request()->is('student/fees/other-payment') ? 'active a' : '' }}">
                                    Registration & Other Payments
                                </a>
                            </li>
                        @endif
                        @if (Auth::check() && Auth::user()->hasPermission('fees.course-paid'))
                            <li>
                                <a href="/student/fees/course-payment"
                                    class="{{ request()->is('student/fees/course-payment') ? 'active a' : '' }}">
                                    Course, NVQ & Food Payment
                                </a>
                            </li>
                        @endif
                        @if (Auth::check() && Auth::user()->hasPermission('fees.fee-invoice'))
                            <li>
                                <a href="/student/fees/invoice-list"
                                    class="{{ request()->is('student/fees/invoice-list') ? 'active a' : '' }}">
                                    Invoice List
                                </a>
                            </li>
                        @endif

                        @if (Auth::check() && Auth::user()->hasPermission('fee-return'))
                            <li>
                                <a href="/student/fees/fee-return"
                                    class="{{ request()->is('student/fees/fee-return') ? 'active a' : '' }}">
                                    Cancelled Invoice
                                </a>
                            </li>
                        @endif


                        @if (Auth::check() && Auth::user()->hasPermission('fees.credit-invoice'))
                            <li>
                                <a href="/student/fees/fee-invoice/invoice-payments/pending"
                                    class="{{ request()->is('student/fees/fee-invoice/invoice-payments/pending') ? 'active a' : '' }}">
                                    Credit Invoice
                                </a>
                            </li>
                        @endif

                        @if (Auth::check() && Auth::user()->hasPermission('fees.credit-invoice-payment'))
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


            {{--
                NOTE (not changed): label text says "Account Section" but every
                class/id on this block says "inventory_section" — looks like a
                copy-paste label bug from wherever this section was cloned from.
                Left the visible text alone since you said don't change the
                design; say the word and I'll change it to "Inventory Section"
                to match.
            --}}
            @if (Auth::check() &&
                    (Auth::user()->hasPermission('item-category') ||
                        Auth::user()->hasPermission('item-list') ||
                        Auth::user()->hasPermission('supplier') ||
                        Auth::user()->hasPermission('item-receive') ||
                        Auth::user()->hasPermission('item-issue') ||
                        Auth::user()->hasPermission('item-sell')))
                <span class="menu_seperator" id="seperator_inventory_section"
                    data-section="inventory_section">Account
                    Section
                </span>
            @endif

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
                                <a href="/account/inventory/item-receive/list"
                                    class="{{ request()->is('account/inventory/item-receive/list') ? 'active a' : '' }}">
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
                                <a href="/account/inventory/item-issue/list"
                                    class="{{ request()->is('account/inventory/item-issue/list') ? 'active a' : '' }}">
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
            {{--
                FIXED: header used to check only 4 permissions
                (fees.course-paid, item-sell, item-issue, fees.fee-invoice) even
                though the menu has 11 items. The other 7 were completely
                ungated below (permission checks commented out) — gated them
                behind NEW permission keys and added those keys here too, so
                the header can't hide a section that actually has visible items.
                Create + assign these 6 new keys before relying on the gates:
                reports.course-dates-payments, reports.course-intake-payments,
                reports.students-intake-balance, reports.change-course,
                reports.invoice-payments, reports.food-payment.
                (reports.student-attendance already existed from the Demo menu.)
            --}}
            @if (Auth::check() &&
                    (Auth::user()->hasPermission('fees.course-paid') ||
                        Auth::user()->hasPermission('item-sell') ||
                        Auth::user()->hasPermission('item-issue') ||
                        Auth::user()->hasPermission('fees.fee-invoice') ||
                        Auth::user()->hasPermission('reports.course-dates-payments') ||
                        Auth::user()->hasPermission('reports.course-intake-payments') ||
                        Auth::user()->hasPermission('reports.students-intake-balance') ||
                        Auth::user()->hasPermission('reports.change-course') ||
                        Auth::user()->hasPermission('reports.invoice-payments') ||
                        Auth::user()->hasPermission('reports.food-payment') ||
                        Auth::user()->hasPermission('reports.student-attendance')))
                <span class="menu_seperator" id="seperator_reports_section" data-section="reports_section">Reports
                    Section
                </span>
            @endif

            @if (Auth::check() &&
                    (Auth::user()->hasPermission('fees.course-paid') ||
                        Auth::user()->hasPermission('item-sell') ||
                        Auth::user()->hasPermission('item-issue') ||
                        Auth::user()->hasPermission('fees.fee-invoice') ||
                        Auth::user()->hasPermission('reports.course-dates-payments') ||
                        Auth::user()->hasPermission('reports.course-intake-payments') ||
                        Auth::user()->hasPermission('reports.students-intake-balance') ||
                        Auth::user()->hasPermission('reports.change-course') ||
                        Auth::user()->hasPermission('reports.invoice-payments') ||
                        Auth::user()->hasPermission('reports.food-payment') ||
                        Auth::user()->hasPermission('reports.student-attendance')))
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
                                    Collection Report
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

                        @if (Auth::check() && Auth::user()->hasPermission('reports.course-dates-payments'))
                            <li>
                                <a href="/reports/course-dates-payments"
                                    class="{{ request()->is('reports/course-dates-payments') ? 'active a' : '' }}">
                                    Course Started & Course Completed
                                </a>
                            </li>
                        @endif

                        @if (Auth::check() && Auth::user()->hasPermission('reports.course-intake-payments'))
                            <li>
                                <a href="/reports/course-intake-payments"
                                    class="{{ request()->is('reports/course-intake-payments') ? 'active a' : '' }}">
                                    Course Started & Course Balance Status
                                </a>
                            </li>
                        @endif

                        @if (Auth::check() && Auth::user()->hasPermission('reports.students-intake-balance'))
                            <li>
                                <a href="/reports/students-intake-balance"
                                    class="{{ request()->is('reports/students-intake-balance') ? 'active a' : '' }}">
                                    Course Fee Balances as Intake Wise
                                </a>
                            </li>
                        @endif

                        @if (Auth::check() && Auth::user()->hasPermission('reports.change-course'))
                            <li>
                                <a href="/reports/change-course"
                                    class="{{ request()->is('reports/change-course') ? 'active a' : '' }}">
                                    Change Course
                                </a>
                            </li>
                        @endif

                        @if (Auth::check() && Auth::user()->hasPermission('reports.invoice-payments'))
                            <li>
                                <a href="/reports/invoice-payments"
                                    class="{{ request()->is('reports/invoice-payments') ? 'active a' : '' }}">
                                    Credit Invoice Payments
                                </a>
                            </li>
                        @endif

                        @if (Auth::check() && Auth::user()->hasPermission('reports.food-payment'))
                            <li>
                                <a href="/reports/food-payment-report"
                                    class="{{ request()->is('reports/food-payment-report') ? 'active a' : '' }}">
                                    All Food Fee Details
                                </a>
                            </li>
                        @endif

                        @if (Auth::check() && Auth::user()->hasPermission('reports.student-attendance'))
                            <li>
                                <a href="/reports/student-attendance"
                                    class="{{ request()->is('reports/student-attendance') ? 'active a' : '' }}">
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
            {{--
                FIXED: header didn't include the User List permission (it was
                ungated before), so it's added here too now that the item below
                is gated. NEW permission key: role-management.user-list —
                this page lists every user account in the system, so it's
                worth creating and assigning this one carefully.
            --}}
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
                        @if (Auth::check() && Auth::user()->hasPermission('role-management.user-list'))
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
    @endif
</nav>
