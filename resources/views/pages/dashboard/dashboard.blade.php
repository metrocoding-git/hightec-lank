@extends('layouts.app')

@push('css')
    <style>
        /* ===== Dashboard modern refresh (Section 40) ===== */
        .dash-welcome {
            position: relative;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            padding: 26px 30px;
            overflow: hidden;
            margin-bottom: 28px;
        }

        .dash-welcome::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            background: linear-gradient(180deg, var(--gradient_1), var(--gradient_2));
        }

        .dash-welcome h1 {
            font-size: 21px;
            font-weight: 700;
            margin: 0;
            color: #1c1c1c;
        }

        .dash-welcome p {
            margin: 4px 0 0;
            color: #9aa1ac;
            font-size: 13px;
        }

        .white-box {
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        .dash-section-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 0 0 22px;
        }

        .dash-section-header .dsh-icon {
            flex: 0 0 auto;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: color-mix(in srgb, var(--gradient_1) 12%, #ffffff);
            color: var(--gradient_1);
            font-size: 16px;
        }

        .dash-section-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #1c1c1c;
        }

        .dash-section-header .dsh-sub {
            display: block;
            margin-top: 2px;
            font-size: 12px;
            font-weight: 400;
            color: #9aa1ac;
        }

        /* Stat cards */
        .stat-card {
            padding: 22px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            height: 100%;
        }

        .stat-card .stat-icon {
            flex: 0 0 auto;
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: linear-gradient(135deg, var(--gradient_1), var(--gradient_2));
            color: #fff;
            box-shadow: 0 6px 16px color-mix(in srgb, var(--gradient_1) 35%, transparent);
        }

        .stat-card .stat-value {
            font-size: 26px;
            font-weight: 700;
            color: #1c1c1c;
            line-height: 1.1;
        }

        .stat-card .stat-label {
            margin: 2px 0 0;
            font-size: 13px;
            color: #9aa1ac;
        }

        /* Chart cards */
        .chart-card {
            padding: 26px 26px 18px;
            height: 100%;
        }

        .chart-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 260px;
            color: #b3b8c2;
            font-size: 13px;
            text-align: center;
        }

        /* Finance mini stats inside the finance chart card */
        .finance-mini-stats {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .finance-mini-stats .fms-item {
            flex: 1 1 140px;
        }

        .finance-mini-stats .fms-value {
            font-size: 20px;
            font-weight: 700;
            color: #1c1c1c;
        }

        .finance-mini-stats .fms-value.due {
            color: #e0653f;
        }

        .finance-mini-stats .fms-label {
            font-size: 12px;
            color: #9aa1ac;
        }

        /* Branch / Intake / Course cards */
        .branch-card {
            padding: 24px 24px 20px;
            height: 100%;
            border-top: 4px solid var(--gradient_1);
        }

        .branch-card .branch-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .branch-card .branch-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: color-mix(in srgb, var(--gradient_1) 12%, #ffffff);
            color: var(--gradient_1);
        }

        .branch-card h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #1c1c1c;
        }

        .branch-card .intake-block {
            margin-bottom: 14px;
            padding-bottom: 14px;
            border-bottom: 1px dashed #edeff3;
        }

        .branch-card .intake-block:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .branch-card .intake-block h5 {
            font-size: 13px;
            font-weight: 600;
            color: #4b5160;
            margin-bottom: 8px;
        }

        .branch-card ul.course-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .branch-card ul.course-list li {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
            color: #5c6270;
        }

        .branch-card ul.course-list li strong {
            color: #1c1c1c;
        }

        .no-access-card {
            padding: 60px 30px;
            text-align: center;
            color: #9aa1ac;
        }

        .no-access-card i {
            font-size: 34px;
            color: #d6d9e0;
            margin-bottom: 12px;
            display: block;
        }

        /* ===== Section 42 additions ===== */

        /* Manual legend for the donut charts — Morris's classic build has no
           built-in legend, just hover tooltips, which isn't "easily
           analyzed" at a glance. Dot colors are set inline per-item so they
           always match the chart's own color order without duplicating a
           color scheme in JS. */
        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 18px;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid #edeff3;
        }

        .chart-legend .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #5c6270;
        }

        .chart-legend .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex: 0 0 auto;
        }

        .chart-legend .legend-item strong {
            color: #1c1c1c;
        }

        /* Academic overview: stat cards above the smart table */
        .mini-stat-card {
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .mini-stat-card .mini-stat-icon {
            flex: 0 0 auto;
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: color-mix(in srgb, var(--gradient_1) 12%, #ffffff);
            color: var(--gradient_1);
            font-size: 16px;
        }

        .mini-stat-card .mini-stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #1c1c1c;
            line-height: 1.1;
        }

        .mini-stat-card .mini-stat-label {
            margin: 2px 0 0;
            font-size: 12px;
            color: #9aa1ac;
        }

        /* Smart academic table card + DataTable styling, matching the
           gradient-header table convention used across the rest of the app */
        .table-card {
            padding: 26px 26px 8px;
        }

        #table_id thead tr th {
            background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2));
            color: #fff;
            border: none;
            white-space: nowrap;
        }

        #table_id tbody tr:hover {
            background-color: #f7f9fc;
        }

        #table_id tbody tr td {
            vertical-align: middle;
        }

        .empty-section-note {
            padding: 30px;
            text-align: center;
            color: #9aa1ac;
        }
    </style>
@endpush

@section('mainContent')

    <div class="dash-welcome">
        <h1>Welcome{{ Auth::user() ? ', ' . Auth::user()->name . ' ' . Auth::user()->last_name : '' }}</h1>
        <p>Here's what's happening across your branches today.</p>
    </div>

    @if (!$canViewStudents && !$canViewStaff && !$canViewFinance && !$canViewAcademic && !$canViewInventory && !$canViewAttendance)
        <div class="white-box no-access-card">
            <i class="fas fa-lock"></i>
            You don't have permission to view any dashboard widgets yet. Contact an administrator if you believe this is a mistake.
        </div>
    @endif

    @if ($canViewStudents || $canViewStaff)
        <section class="mb-30">
            <div class="row row-gap-24">
                @if ($canViewStudents)
                    <div class="col-lg-3 col-sm-6">
                        <div class="white-box stat-card">
                            <span class="stat-icon"><i class="fas fa-user-graduate"></i></span>
                            <div>
                                <div class="stat-value">{{ number_format($studentsCount) }}</div>
                                <p class="stat-label">Students</p>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($canViewStaff)
                    <div class="col-lg-3 col-sm-6">
                        <div class="white-box stat-card">
                            <span class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></span>
                            <div>
                                <div class="stat-value">{{ number_format($teachersCount) }}</div>
                                <p class="stat-label">Teachers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="white-box stat-card">
                            <span class="stat-icon"><i class="fas fa-hard-hat"></i></span>
                            <div>
                                <div class="stat-value">{{ number_format($labourCount) }}</div>
                                <p class="stat-label">Labour</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="white-box stat-card">
                            <span class="stat-icon"><i class="fas fa-user-shield"></i></span>
                            <div>
                                <div class="stat-value">{{ number_format($adminCount) }}</div>
                                <p class="stat-label">Admin</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if ($canViewFinance || $canViewStudents || $canViewStaff)
        <section class="mb-30">
            <div class="row row-gap-24">
                @if ($canViewFinance)
                    <div class="col-lg-6">
                        <div class="white-box chart-card">
                            <div class="dash-section-header">
                                <span class="dsh-icon"><i class="fas fa-chart-line"></i></span>
                                <div>
                                    <h3>Income Trend</h3>
                                    <span class="dsh-sub">Payments collected over the last 6 months</span>
                                </div>
                            </div>
                            {{-- PERMISSION (Section 43): amount figures need Reports access
                                 on top of Fees access ("Amount only display all report
                                 access pertions only, count display no problem") --}}
                            @if ($canViewAmounts)
                                <div class="finance-mini-stats">
                                    <div class="fms-item">
                                        <div class="fms-value">Rs. {{ number_format($totalIncome, 2) }}</div>
                                        <div class="fms-label">Total Collected</div>
                                    </div>
                                    <div class="fms-item">
                                        <div class="fms-value">Rs. {{ number_format($totalInvoiced, 2) }}</div>
                                        <div class="fms-label">Total Invoiced</div>
                                    </div>
                                    <div class="fms-item">
                                        <div class="fms-value due">Rs. {{ number_format($totalOutstanding, 2) }}</div>
                                        <div class="fms-label">Outstanding</div>
                                    </div>
                                </div>
                                <div id="incomeTrendChart" style="height:260px;"></div>
                            @else
                                <div class="chart-empty">Amount figures need Reports access.</div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($canViewStudents)
                    <div class="col-lg-3 col-sm-6">
                        <div class="white-box chart-card">
                            <div class="dash-section-header">
                                <span class="dsh-icon"><i class="fas fa-code-branch"></i></span>
                                <div>
                                    <h3>Students by Branch</h3>
                                </div>
                            </div>
                            @if ($studentsPerBranch->count() > 0)
                                <div id="studentsPerBranchChart" style="height:260px;"></div>
                            @else
                                <div class="chart-empty">No branches to show yet.</div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($canViewStaff)
                    <div class="col-lg-3 col-sm-6">
                        <div class="white-box chart-card">
                            <div class="dash-section-header">
                                <span class="dsh-icon"><i class="fas fa-users"></i></span>
                                <div>
                                    <h3>Staff Mix</h3>
                                </div>
                            </div>
                            @if ($staffTotal > 0)
                                <div id="staffCompositionChart" style="height:220px;"></div>
                                <div class="chart-legend">
                                    <span class="legend-item"><span class="legend-dot" style="background:var(--gradient_1);"></span> Teachers <strong>{{ $teachersCount }}</strong></span>
                                    <span class="legend-item"><span class="legend-dot" style="background:var(--gradient_2);"></span> Labour <strong>{{ $labourCount }}</strong></span>
                                    <span class="legend-item"><span class="legend-dot" style="background:#c9cdd6;"></span> Admin <strong>{{ $adminCount }}</strong></span>
                                </div>
                            @else
                                <div class="chart-empty">No staff accounts yet.</div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if ($canViewFinance || $canViewInventory)
        <section class="mb-30">
            <div class="dash-section-header">
                <span class="dsh-icon"><i class="fas fa-boxes-stacked"></i></span>
                <div>
                    <h3>Sales, Inventory &amp; Payments</h3>
                    <span class="dsh-sub">Stock movement and invoice health</span>
                </div>
            </div>

            @if ($canViewInventory)
                <div class="row row-gap-24 mb-30">
                    <div class="col-lg-3 col-sm-6">
                        <div class="white-box mini-stat-card">
                            <span class="mini-stat-icon"><i class="fas fa-cubes"></i></span>
                            <div>
                                <div class="mini-stat-value">{{ number_format($totalInventoryItems) }}</div>
                                <p class="mini-stat-label">Items Tracked</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="white-box mini-stat-card">
                            <span class="mini-stat-icon"><i class="fas fa-tags"></i></span>
                            <div>
                                <div class="mini-stat-value">{{ number_format($totalItemCategories) }}</div>
                                <p class="mini-stat-label">Item Categories</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="white-box mini-stat-card">
                            <span class="mini-stat-icon"><i class="fas fa-cart-arrow-down"></i></span>
                            <div>
                                <div class="mini-stat-value">{{ number_format($itemSellCountThisMonth) }}</div>
                                <p class="mini-stat-label">Sales This Month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="white-box mini-stat-card">
                            <span class="mini-stat-icon"><i class="fas fa-dolly"></i></span>
                            <div>
                                <div class="mini-stat-value">{{ number_format($itemIssueCountThisMonth) }}</div>
                                <p class="mini-stat-label">Items Issued This Month</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row row-gap-24">
                @if ($canViewInventory)
                    <div class="col-lg-6">
                        <div class="white-box chart-card">
                            <div class="dash-section-header">
                                <span class="dsh-icon"><i class="fas fa-chart-bar"></i></span>
                                <div>
                                    <h3>Item Sell Revenue</h3>
                                    <span class="dsh-sub">Last 6 months</span>
                                </div>
                            </div>
                            {{-- PERMISSION (Section 43): revenue is an amount, gated on
                                 Reports access same as the Income Trend chart above. --}}
                            @if (!$canViewAmounts)
                                <div class="chart-empty">Amount figures need Reports access.</div>
                            @elseif ($itemSellTrend->sum('revenue') > 0)
                                <div id="itemSellTrendChart" style="height:260px;"></div>
                            @else
                                <div class="chart-empty">No item sales recorded yet.</div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($canViewFinance)
                    <div class="col-lg-6">
                        <div class="white-box chart-card">
                            <div class="dash-section-header">
                                <span class="dsh-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                                <div>
                                    <h3>Invoice Status Breakdown</h3>
                                    <span class="dsh-sub">All fee invoices, by current status</span>
                                </div>
                            </div>
                            @if ($invoiceStatusTotal > 0)
                                <div id="invoiceStatusChart" style="height:220px;"></div>
                                <div class="chart-legend">
                                    @php $legendColors = ['var(--gradient_1)', 'var(--gradient_2)', '#c9cdd6', '#e0653f']; @endphp
                                    @foreach ($invoiceStatusBreakdown as $i => $row)
                                        <span class="legend-item"><span class="legend-dot" style="background:{{ $legendColors[$i] ?? '#c9cdd6' }};"></span> {{ $row['label'] }} <strong>{{ $row['value'] }}</strong></span>
                                    @endforeach
                                </div>
                            @else
                                <div class="chart-empty">No invoices recorded yet.</div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif


    @if ($canViewAcademic)
        <section class="mb-30">
            <div class="dash-section-header">
                <span class="dsh-icon"><i class="fas fa-layer-group"></i></span>
                <div>
                    <h3>Branches, Intakes &amp; Courses</h3>
                    <span class="dsh-sub">Search or sort instead of scrolling through every branch</span>
                </div>
            </div>

            <div class="row row-gap-24 mb-30">
                <div class="col-lg-4 col-sm-6">
                    <div class="white-box mini-stat-card">
                        <span class="mini-stat-icon"><i class="fas fa-building"></i></span>
                        <div>
                            <div class="mini-stat-value">{{ number_format($totalBranchesCount) }}</div>
                            <p class="mini-stat-label">Branches</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="white-box mini-stat-card">
                        <span class="mini-stat-icon"><i class="fas fa-calendar-alt"></i></span>
                        <div>
                            <div class="mini-stat-value">{{ number_format($totalIntakesCount) }}</div>
                            <p class="mini-stat-label">Intakes</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="white-box mini-stat-card">
                        <span class="mini-stat-icon"><i class="fas fa-book-open"></i></span>
                        <div>
                            <div class="mini-stat-value">{{ number_format($totalCoursesCount) }}</div>
                            <p class="mini-stat-label">Courses</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="white-box table-card">
                @if ($academicRows->count() > 0)
                    <div class="table-responsive">
                        <table id="table_id" class="table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Branch</th>
                                    <th>Intake</th>
                                    <th>Course</th>
                                    {{-- PERMISSION (Section 43): Price is an amount, not a
                                         count, so it needs Reports access too. --}}
                                    @if ($canViewAmounts)
                                        <th>Price</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($academicRows as $row)
                                    <tr>
                                        <td>{{ $row['branch'] }}</td>
                                        <td>{{ $row['intake'] }}</td>
                                        <td>{{ $row['course'] }}</td>
                                        @if ($canViewAmounts)
                                            <td>Rs. {{ number_format($row['price'], 2) }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-section-note">No branches, intakes, or courses set up yet.</div>
                @endif
            </div>
        </section>
    @endif

    {{-- Student Attendance (new): reuses the confirmed SubjectAttendanceSession
         model behind ReportController::studentAttendanceQuery() — the same
         model, the same notCheckpoint()/open()/type-T-P scopes/filters already
         proven working on the Student Attendance report page, just scoped to
         "today" and "last 7 days" instead of a user-chosen filter range, so
         nothing here is a new guess about schema. --}}
    @if ($canViewAttendance)
        <section class="mb-30">
            <div class="dash-section-header">
                <span class="dsh-icon"><i class="fas fa-user-check"></i></span>
                <div>
                    <h3>Student Attendance</h3>
                    <span class="dsh-sub">Today's scan activity across Course / Subject / Practical sessions</span>
                </div>
            </div>

            <div class="row row-gap-24 mb-30">
                <div class="col-lg-4 col-sm-6">
                    <div class="white-box mini-stat-card">
                        <span class="mini-stat-icon"><i class="fas fa-clipboard-list"></i></span>
                        <div>
                            <div class="mini-stat-value">{{ number_format($attendanceTodayCount) }}</div>
                            <p class="mini-stat-label">Sessions Today</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="white-box mini-stat-card">
                        <span class="mini-stat-icon"><i class="fas fa-door-open"></i></span>
                        <div>
                            <div class="mini-stat-value">{{ number_format($attendanceOpenCount) }}</div>
                            <p class="mini-stat-label">Currently Checked In</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="white-box mini-stat-card">
                        <span class="mini-stat-icon"><i class="fas fa-check-circle"></i></span>
                        <div>
                            <div class="mini-stat-value">{{ number_format($attendanceCompletedTodayCount) }}</div>
                            <p class="mini-stat-label">Completed Today</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-gap-24">
                <div class="col-lg-5">
                    <div class="white-box chart-card">
                        <div class="dash-section-header">
                            <span class="dsh-icon"><i class="fas fa-chart-pie"></i></span>
                            <div>
                                <h3>Today's Sessions by Type</h3>
                            </div>
                        </div>
                        @if ($attendanceTypeBreakdown->sum('value') > 0)
                            <div id="attendanceTypeChart" style="height:220px;"></div>
                            <div class="chart-legend">
                                @php $atColors = ['var(--gradient_1)', 'var(--gradient_2)', '#c9cdd6']; @endphp
                                @foreach ($attendanceTypeBreakdown as $i => $row)
                                    <span class="legend-item"><span class="legend-dot" style="background:{{ $atColors[$i] ?? '#c9cdd6' }};"></span> {{ $row['label'] }} <strong>{{ $row['value'] }}</strong></span>
                                @endforeach
                            </div>
                        @else
                            <div class="chart-empty">No attendance scans recorded yet today.</div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="white-box chart-card">
                        <div class="dash-section-header">
                            <span class="dsh-icon"><i class="fas fa-chart-line"></i></span>
                            <div>
                                <h3>Attendance Trend</h3>
                                <span class="dsh-sub">Last 7 days</span>
                            </div>
                        </div>
                        @if ($attendanceTrend->sum('count') > 0)
                            <div id="attendanceTrendChart" style="height:220px;"></div>
                        @else
                            <div class="chart-empty">No attendance scans in the last 7 days.</div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($canViewFinance || $canViewStudents || $canViewStaff || $canViewInventory || $canViewAttendance)
        <script>
            $(document).ready(function() {
                var gradient1 = getComputedStyle(document.documentElement).getPropertyValue('--gradient_1').trim() || '#6a5af9';
                var gradient2 = getComputedStyle(document.documentElement).getPropertyValue('--gradient_2').trim() || '#8763f4';

                {{-- PERMISSION (Section 43): don't even emit the income figures into
                     the page source for a user without Reports access — hiding the
                     container div alone would still leak the numbers via view-source. --}}
                @if ($canViewFinance && $canViewAmounts)
                    if ($('#incomeTrendChart').length) {
                        Morris.Line({
                            element: 'incomeTrendChart',
                            data: @json($incomeTrend),
                            xkey: 'month',
                            ykeys: ['income'],
                            labels: ['Income'],
                            lineColors: [gradient1],
                            resize: true,
                            hideHover: 'auto'
                        });
                    }
                @endif

                @if ($canViewStudents && $studentsPerBranch->count() > 0)
                    if ($('#studentsPerBranchChart').length) {
                        Morris.Bar({
                            element: 'studentsPerBranchChart',
                            data: @json($studentsPerBranch),
                            xkey: 'branch',
                            ykeys: ['count'],
                            labels: ['Students'],
                            barColors: [gradient1],
                            resize: true,
                            hideHover: 'auto'
                        });
                    }
                @endif

                @if ($canViewStaff && $staffTotal > 0)
                    if ($('#staffCompositionChart').length) {
                        Morris.Donut({
                            element: 'staffCompositionChart',
                            data: @json($staffComposition),
                            colors: [gradient1, gradient2, '#c9cdd6'],
                            resize: true
                        });
                    }
                @endif

                {{-- PERMISSION (Section 43): same reasoning as the income chart above —
                     revenue figures need Reports access, not just Inventory access. --}}
                @if ($canViewInventory && $canViewAmounts && $itemSellTrend->sum('revenue') > 0)
                    if ($('#itemSellTrendChart').length) {
                        Morris.Bar({
                            element: 'itemSellTrendChart',
                            data: @json($itemSellTrend),
                            xkey: 'month',
                            ykeys: ['revenue'],
                            labels: ['Revenue'],
                            barColors: [gradient2],
                            resize: true,
                            hideHover: 'auto'
                        });
                    }
                @endif

                @if ($canViewFinance && $invoiceStatusTotal > 0)
                    if ($('#invoiceStatusChart').length) {
                        Morris.Donut({
                            element: 'invoiceStatusChart',
                            data: @json($invoiceStatusBreakdown),
                            colors: [gradient1, gradient2, '#c9cdd6', '#e0653f'],
                            resize: true
                        });
                    }
                @endif

                {{-- Student Attendance charts (new) --}}
                @if ($canViewAttendance && $attendanceTypeBreakdown->sum('value') > 0)
                    if ($('#attendanceTypeChart').length) {
                        Morris.Donut({
                            element: 'attendanceTypeChart',
                            data: @json($attendanceTypeBreakdown),
                            colors: [gradient1, gradient2, '#c9cdd6'],
                            resize: true
                        });
                    }
                @endif

                @if ($canViewAttendance && $attendanceTrend->sum('count') > 0)
                    if ($('#attendanceTrendChart').length) {
                        Morris.Line({
                            element: 'attendanceTrendChart',
                            data: @json($attendanceTrend),
                            xkey: 'day',
                            ykeys: ['count'],
                            labels: ['Sessions'],
                            lineColors: [gradient1],
                            resize: true,
                            hideHover: 'auto'
                        });
                    }
                @endif
            });
        </script>
    @endif
@endsection

{{-- Smart academic table (Section 42): reusing the exact DataTables partial
     pattern already used across this app (e.g. course.blade.php,
     content-list.blade.php) rather than a bespoke init, so it inherits the
     same base styling/behavior as every other list in the system. --}}
@if ($canViewAcademic && $academicRows->count() > 0)
    @include('components.partials.data_table_css')
    @include('components.partials.data_table_js')
    @push('script')
        <script>
            $('#table_id').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'print'],
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                pageLength: 8
            });
        </script>
    @endpush
@endif