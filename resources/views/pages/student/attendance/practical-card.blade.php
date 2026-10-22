<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $student->student_name }} - Attendance Cards</title>
    <style>
        :root {
            --brand: #1d4e89;
            --brand-dark: #123a66;
            --brand-light: #eaf1fb;
            --brand2: #6a3fb5;
            --brand2-dark: #4b2a82;
            --brand2-light: #f1eafb;
            --ok: #1f8a4c;
            --ok-bg: #e8f7ee;
            --warn: #b93a3a;
            --warn-bg: #fbeaea;
            --border: #d8dfe8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            font-size: 12.5px;
            color: #222;
            margin: 0;
            padding: 24px;
            background: #eef1f5;
        }

        .cards-page-wrap {
            max-width: 1150px;
            margin: 0 auto;
        }

        /* ---------- shared toolbar (course selector + both print buttons) ---------- */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 14px;
            flex-wrap: wrap;
            padding: 16px 24px;
            background: #f6f8fb;
            border: 1px solid var(--border);
            border-radius: 10px 10px 0 0;
        }

        .toolbar .field label {
            display: block;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #667;
            margin-bottom: 4px;
        }

        .toolbar select {
            min-width: 260px;
            padding: 9px 12px;
            font-size: 13px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: #fff;
            color: #222;
        }

        .toolbar .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            font-size: 13px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: #fff;
            color: #333;
            cursor: pointer;
            text-decoration: none;
        }

        .btn svg {
            width: 14px;
            height: 14px;
        }

        .btn-primary {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .btn-secondary {
            background: var(--brand2);
            border-color: var(--brand2);
            color: #fff;
        }

        /* ---------- card ---------- */
        .card-wrap {
            background: #fff;
            overflow: hidden;
            box-shadow: 0 2px 14px rgba(20, 30, 60, 0.08);
            margin-bottom: 22px;
        }

        .card-wrap:first-of-type {
            border-radius: 0;
        }

        .card-wrap.theory .header {
            background: linear-gradient(135deg, var(--brand2) 0%, var(--brand2-dark) 100%);
        }



        .card-title-bar {
            padding: 10px 24px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 700;
            color: #667;
            background: #f6f8fb;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        /* ---------- header ---------- */
        .header {
            padding: 22px 24px 18px;
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: #fff;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .header .photo {
            width: 76px;
            height: 86px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, .8);
            background: #fff;
            flex-shrink: 0;
        }

        .header .photo-placeholder {
            width: 76px;
            height: 86px;
            border-radius: 8px;
            border: 2px dashed rgba(255, 255, 255, .6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: rgba(255, 255, 255, .8);
            flex-shrink: 0;
        }

        .header .titles {
            flex: 1;
        }

        .header h1 {
            font-size: 18px;
            margin: 0 0 2px;
            font-weight: 600;
        }

        .header .sub {
            font-size: 11.5px;
            opacity: .85;
            margin-bottom: 12px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(140px, 1fr));
            gap: 8px 22px;
        }

        .meta-grid .item .label {
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: .07em;
            opacity: .75;
            margin-bottom: 1px;
        }

        .meta-grid .item .value {
            font-size: 13px;
            font-weight: 600;
        }

        /* ---------- summary strip ---------- */
        .summary-strip {
            display: flex;
            gap: 24px;
            padding: 14px 24px;
            border-bottom: 1px solid var(--border);
            background: #fbfcfe;
        }

        .summary-item .num {
            font-size: 20px;
            font-weight: 700;
            color: var(--brand);
        }

        .summary-item .lbl {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #667;
        }

        /* ---------- section (one per subject / checkpoint group) ---------- */
        .table-area {
            padding: 20px 24px 26px;
        }

        .section-block {
            margin-bottom: 22px;
        }

        .section-block:last-child {
            margin-bottom: 0;
        }

        .section-heading {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--brand-dark);
            margin: 0 0 8px;
            padding-bottom: 6px;
            border-bottom: 2px solid var(--brand-light);
        }

        .card-wrap.theory .section-heading {
            color: var(--brand2-dark);
            border-bottom-color: var(--brand2-light);
        }

        .total-chip {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .03em;
            padding: 4px 11px;
            border-radius: 999px;
            background: var(--brand-light);
            color: var(--brand-dark);
        }

        table.attendance {
            width: 100%;
            border-collapse: collapse;
        }

        table.attendance th {
            background: #f1f4f9;
            border: 1px solid var(--border);
            padding: 8px 8px;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #445;
            text-align: center;
        }

        table.attendance td {
            border: 1px solid var(--border);
            padding: 7px 8px;
            text-align: center;
            vertical-align: middle;
        }

        tr.subject-row td {
            background: var(--brand-light);
            text-align: left;
            font-weight: 700;
            font-size: 13px;
            color: var(--brand-dark);
            padding: 9px 10px;
        }

        .card-wrap.theory tr.subject-row td {
            background: var(--brand2-light);
            color: var(--brand2-dark);
        }

        tr.subject-row .badge {
            float: right;
            font-weight: 600;
            font-size: 10.5px;
            letter-spacing: .03em;
            padding: 3px 10px;
            border-radius: 999px;
            margin-left: 8px;
        }

        tr.subject-row .planned-times {
            font-weight: 500;
            font-size: 11px;
            color: #556;
            margin-left: 10px;
        }

        tr.subject-row .planned-times .chip {
            display: inline-block;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 2px 9px;
            margin-right: 6px;
        }

        tr.subject-row .planned-times .chip.chip-total {
            background: var(--brand-dark);
            color: #fff;
            border-color: var(--brand-dark);
        }

        .badge-ok {
            background: var(--ok-bg);
            color: var(--ok);
        }

        .badge-warn {
            background: var(--warn-bg);
            color: var(--warn);
        }

        tr.instance-row:nth-child(even) td {
            background: #fafbfd;
        }

        tr.not-attended td {
            color: #9aa4b2;
            font-style: italic;
            padding: 10px;
        }

        .empty-state {
            padding: 40px;
            text-align: center;
            color: #9aa4b2;
            font-size: 13.5px;
        }

        .footer-note {
            padding: 12px 24px 20px;
            font-size: 10.5px;
            color: #9aa4b2;
            text-align: right;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .card-wrap {
                box-shadow: none;
                max-width: 100%;
                margin-bottom: 0;
            }

            .no-print {
                display: none !important;
            }

            table.attendance {
                font-size: 10.5px;
            }

            /* Each card prints on its own — printOnly() adds one of these
               two classes to <body> right before window.print(), so the
               OTHER card is hidden for that particular print job. */
            body.printing-practical #theoryCard {
                display: none !important;
            }

            body.printing-theory #practicalCard {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="cards-page-wrap">

        <div class="toolbar no-print">
            <div class="field">
                <label for="course_id">Viewing Course</label>
                <form method="GET" action="{{ route('student-practical-card.show', $student->id) }}" id="courseForm">
                    <select name="course_id" id="course_id" onchange="document.getElementById('courseForm').submit();">
                        @foreach ($courses as $sc)
                            <option value="{{ $sc->course_id }}" @selected($sc->course_id == $selectedCourseId)>
                                {{ $sc->course->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="actions">
                <a href="{{ route('student-practical-card.index') }}" class="btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7" />
                    </svg>
                    Back to List
                </a>
                <button type="button" class="btn btn-primary" onclick="printOnly('practical')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z" />
                    </svg>
                    Print Practical Card
                </button>
                <button type="button" class="btn btn-secondary" onclick="printOnly('theory')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z" />
                    </svg>
                    Print Theory Card
                </button>
            </div>
        </div>

        {{-- =======================================================
             PRACTICAL CARD — subjects, then Before Checking,
             After Checking and Course Attendance as their own
             subject-style sections (every instance listed).
             ======================================================= --}}
        <div class="card-wrap" id="practicalCard">
            <div
                style="text-align: center; font-size: 11px; color: rgb(38, 38, 38); padding-bottom: 10px; padding-top: 10px; background-color: #dee2e7;">
                <span style="font-weight: bold; font-size: 20px;">HIGHTEC LANKA INTERNATIONAL</span>
                <span style="display: block;">Vocational & Technical Training Institute (Pvt) Ltd.</span>
                <span style="display: block; margin-top: 5px;">No 549, Kandy Road, Mawathagama.</span>
            </div>

            <div class="card-title-bar">Practical Attendance Card</div>

            @php
                $totalPracticalSubjects = count($practicalSubjects);
                $attendedPracticalSubjects = collect($practicalSubjects)
                    ->filter(fn($s) => $s['attended_count'] > 0)
                    ->count();
                $totalPracticalInstances = collect($practicalSubjects)->sum('attended_count');
            @endphp

            <div class="header">
                @if ($student->photo)
                    <img class="photo" src="{{ asset('storage/' . $student->photo) }}" alt="photo">
                @else
                    <div class="photo-placeholder">{{ strtoupper(substr($student->student_name, 0, 1)) }}</div>
                @endif

                <div class="titles">
                    <h1>{{ $student->student_name }}</h1>
                    <div class="sub">Practical Attendance Card</div>

                    <div class="meta-grid">
                        <div class="item">
                            <div class="label">Admission No</div>
                            <div class="value">{{ $student->admission_number ?? '-' }}</div>
                        </div>
                        <div class="item">
                            <div class="label">NIC</div>
                            <div class="value">{{ $student->national_id_number ?? '-' }}</div>
                        </div>
                        <div class="item">
                            <div class="label">Branch</div>
                            <div class="value">{{ $student->branch->name ?? '-' }}</div>
                        </div>
                        <div class="item">
                            <div class="label">Course</div>
                            <div class="value">{{ $selectedCourse->name ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="summary-strip">
                <div class="summary-item">
                    <div class="num">{{ $attendedPracticalSubjects }} / {{ $totalPracticalSubjects }}</div>
                    <div class="lbl">Subjects Attended</div>
                </div>
                <div class="summary-item">
                    <div class="num">{{ $totalPracticalInstances }}</div>
                    <div class="lbl">Total Practical Sessions</div>
                </div>
            </div>

            <div class="table-area">

                {{-- ---------- Practical Subjects ----------
                     UPDATED — the Before Checking / After Checking group
                     totals used to show in their OWN section headings
                     further down; moved up here instead, alongside a new
                     "Total Completed Duration" chip (the actual attended
                     total across all practical subjects, next to the
                     existing planned "Practical Time (Total)" chip). --}}
                <div class="section-block">
                    <div class="section-heading">
                        Practical Subjects
                        <span class="total-chip">Total Required Practical Time:
                            {{ $practicalTotalPracticalTime }}</span>
                        <span class="total-chip">Total Completed Practical Time: {{ $practicalTotalActualTime }}</span>
                        @if ($beforeChecking['attended_count'] > 0)
                            <span class="total-chip">පෙර පණගැවුම් Total Time:
                                {{ $beforeChecking['total_duration'] }}</span>
                        @endif
                        @if ($afterChecking['attended_count'] > 0)
                            <span class="total-chip">පසු පණගැවුම් Total Time:
                                {{ $afterChecking['total_duration'] }}</span>
                        @endif
                        <small style="margin-left: 8px; font-size: 11px; color: #667;">Total Required Practical Time and
                            Total Completed Practical Time excluding Pre-startup time and Post-startup time.</small>
                    </div>

                    @if (empty($practicalSubjects))
                        <div class="empty-state">No practical subjects are assigned to this course{{ $selectedIntakeName ? ' for intake ' . $selectedIntakeName : '' }}. Assign subjects to this intake &amp; course first.</div>
                    @else
                        <table class="attendance">
                            <thead>
                                <tr>
                                    <th style="width:4%">No</th>
                                    <th style="width:14%">Date</th>
                                    <th style="width:11%">Time In</th>
                                    <th style="width:11%">Time Out</th>
                                    <th style="width:10%">Duration</th>
                                    <th style="width:20%">Vehicle</th>
                                    <th style="width:15%">Marked By</th>
                                </tr>
                            </thead>
                            <tbody>
                                 @foreach ($practicalSubjects as $index => $subject)
                                    <tr class="subject-row">
                                        <td colspan="7">
                                            {{ $index + 1 }}. {{ $subject['subject'] }}
                                            <span class="planned-times">
                                                <span class="chip">Demo Time:
                                                    {{ $subject['demonstrator_time'] }}</span>
                                                <span class="chip">Practical Time:
                                                    {{ $subject['practical_time'] }}</span>
                                                {{-- "if has subject 2 or more attends want total duration...
                                                     with that subject Practical Time" --}}
                                                @if ($subject['total_duration'] !== null)
                                                    <span class="chip chip-total">Total Duration:
                                                        {{ $subject['total_duration'] }}</span>
                                                @endif
                                            </span>
                                            @if ($subject['attended_count'] > 0)
                                                <span class="badge badge-ok">Attended
                                                    {{ $subject['attended_count'] }}x</span>
                                            @else
                                                <span class="badge badge-warn">Not Attended</span>
                                            @endif
                                            {{-- "...then if complete that time or not" --}}
                                            @if ($subject['completed'] === true)
                                                <span class="badge badge-ok">Practical Time Completed</span>
                                            @elseif ($subject['completed'] === false)
                                                <span class="badge badge-warn">Practical Time Not Completed</span>
                                            @endif
                                        </td>
                                    </tr>

                                    @if ($subject['attended_count'] === 0)
                                        <tr class="not-attended">
                                            <td colspan="7">No practical attendance recorded yet for this subject.
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($subject['instances'] as $index => $instance)
                                            <tr class="instance-row">
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $instance['date'] }}</td>
                                                <td>{{ $instance['time_in']?->format('h:i A') ?? '-' }}</td>
                                                <td>{{ $instance['time_out']?->format('h:i A') ?? '-' }}</td>
                                                <td>{{ $instance['duration'] }}</td>
                                                <td>{{ $instance['vehicle'] }}</td>
                                                <td>{{ $instance['marked_by'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- ---------- Before Checking / After Checking ----------
                     Same subject-style block, reused for both groups.
                     UPDATED — the "Total Time" chip that used to sit here
                     has been moved up into the Practical Subjects heading
                     above (as පෙර/පසු පණගැවුම් Total Time), so this
                     heading is just the group title now. --}}
                @foreach ([$beforeChecking, $afterChecking] as $group)
                    <div class="section-block">
                        <div class="section-heading">
                            {{ $group['title'] }}
                        </div>

                        @if ($group['attended_count'] === 0)
                            <div class="empty-state">No {{ strtolower($group['title']) }} records for this course.
                            </div>
                        @else
                            <table class="attendance">
                                <thead>
                                    <tr>
                                        <th style="width:4%">No</th>
                                        <th style="width:14%">Date</th>
                                        <th style="width:11%">Time In</th>
                                        <th style="width:11%">Time Out</th>
                                        <th style="width:10%">Duration</th>
                                        <th style="width:15%">Marked By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group['instances'] as $index => $instance)
                                        <tr class="instance-row">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $instance['date'] }}</td>
                                            <td>{{ $instance['time_in']?->format('h:i A') ?? '-' }}</td>
                                            <td>{{ $instance['time_out']?->format('h:i A') ?? '-' }}</td>
                                            <td>{{ $instance['duration'] }}</td>
                                            <td>{{ $instance['marked_by'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                @endforeach

                {{-- ---------- Course Attendance ----------
                     UPDATED — Demo Time chip, Completed Time chip, and the
                     Completed/Not Completed badge all removed. Replaced
                     with a single "Total Time" chip showing this group's
                     own (already net-of-checking/subjects) total
                     duration — same pattern Before/After Checking used to
                     use for their own totals. --}}
                <div class="section-block">
                    <div class="section-heading">
                        {{ $courseAttendance['title'] }}
                        @if ($courseAttendance['attended_count'] > 0)
                            <span class="total-chip">Total Time: {{ $courseAttendance['total_duration'] }}</span>
                        @endif
                    </div>

                    @if ($courseAttendance['attended_count'] === 0)
                        <div class="empty-state">No course attendance records for this course.</div>
                    @else
                        <table class="attendance">
                            <thead>
                                <tr>
                                    <th style="width:4%">No</th>
                                    <th style="width:14%">Date</th>
                                    <th style="width:11%">Time In</th>
                                    <th style="width:11%">Time Out</th>
                                    <th style="width:10%">Duration</th>
                                    <th style="width:15%">Marked By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($courseAttendance['instances'] as $index => $instance)
                                    <tr class="instance-row">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $instance['date'] }}</td>
                                        <td>{{ $instance['time_in']?->format('h:i A') ?? '-' }}</td>
                                        <td>{{ $instance['time_out']?->format('h:i A') ?? '-' }}</td>
                                        <td>{{ $instance['duration'] }}</td>
                                        <td>{{ $instance['marked_by'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>

            <div class="footer-note">Generated {{ now()->format('Y-m-d h:i A') }}</div>
        </div>

        {{-- =======================================================
             THEORY CARD — mirrors the Practical card's subject
             section but for Subject::type = 'T', no Vehicle column.
             ======================================================= --}}
        <div class="card-wrap theory" id="theoryCard">
            <div
                style="text-align: center; font-size: 11px; color: rgb(38, 38, 38); padding-bottom: 10px; padding-top: 10px; background-color: #dee2e7;">
                <span style="font-weight: bold; font-size: 20px;">HIGHTEC LANKA INTERNATIONAL</span>
                <span style="display: block;">Vocational & Technical Training Institute (Pvt) Ltd.</span>
                <span style="display: block; margin-top: 5px;">No 549, Kandy Road, Mawathagama.</span>
            </div>
            <div class="card-title-bar">Theory Attendance Card</div>

            @php
                $totalTheorySubjects = count($theorySubjects);
                $attendedTheorySubjects = collect($theorySubjects)->filter(fn($s) => $s['attended_count'] > 0)->count();
                $totalTheoryInstances = collect($theorySubjects)->sum('attended_count');
            @endphp

            <div class="header">
                @if ($student->photo)
                    <img class="photo" src="{{ asset('storage/' . $student->photo) }}" alt="photo">
                @else
                    <div class="photo-placeholder">{{ strtoupper(substr($student->student_name, 0, 1)) }}</div>
                @endif

                <div class="titles">
                    <h1>{{ $student->student_name }}</h1>
                    <div class="sub">Theory Attendance Card</div>

                    <div class="meta-grid">
                        <div class="item">
                            <div class="label">Admission No</div>
                            <div class="value">{{ $student->admission_number ?? '-' }}</div>
                        </div>
                        <div class="item">
                            <div class="label">NIC</div>
                            <div class="value">{{ $student->national_id_number ?? '-' }}</div>
                        </div>
                        <div class="item">
                            <div class="label">Branch</div>
                            <div class="value">{{ $student->branch->name ?? '-' }}</div>
                        </div>
                        <div class="item">
                            <div class="label">Course</div>
                            <div class="value">{{ $selectedCourse->name ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="summary-strip">
                <div class="summary-item">
                    <div class="num">{{ $attendedTheorySubjects }} / {{ $totalTheorySubjects }}</div>
                    <div class="lbl">Subjects Attended</div>
                </div>
                <div class="summary-item">
                    <div class="num">{{ $totalTheoryInstances }}</div>
                    <div class="lbl">Total Theory Sessions</div>
                </div>
            </div>

            <div class="table-area">
                <div class="section-block">
                    {{-- UPDATED — added the total completed duration chip
                         in front of "Theory Subjects", same idea as the
                         Practical card's new chip. --}}
                    <div class="section-heading">
                        Theory Subjects
                        <span class="total-chip">Total Completed Theory Time: {{ $theoryTotalActualTime }}</span>
                    </div>

                    @if (empty($theorySubjects))
                        <div class="empty-state">No theory subjects are assigned to this course{{ $selectedIntakeName ? ' for intake ' . $selectedIntakeName : '' }}. Assign subjects to this intake &amp; course first.</div>
                    @else
                        <table class="attendance">
                            <thead>
                                <tr>
                                    <th style="width:5%">No</th>
                                    <th style="width:17%">Date</th>
                                    <th style="width:14%">Time In</th>
                                    <th style="width:14%">Time Out</th>
                                    <th style="width:12%">Duration</th>
                                    <th style="width:18%">Marked By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($theorySubjects as $index => $subject)
                                    <tr class="subject-row">
                                        <td colspan="6">
                                            {{ $index + 1 }}. {{ $subject['subject'] }}
                                            @if ($subject['attended_count'] > 0)
                                                <span class="badge badge-ok">Attended
                                                    {{ $subject['attended_count'] }}x</span>
                                            @else
                                                <span class="badge badge-warn">Not Attended</span>
                                            @endif
                                        </td>
                                    </tr>

                                    @if ($subject['attended_count'] === 0)
                                        <tr class="not-attended">
                                            <td colspan="6">No theory attendance recorded yet for this subject.</td>
                                        </tr>
                                    @else
                                        @foreach ($subject['instances'] as $index => $instance)
                                            <tr class="instance-row">
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $instance['date'] }}</td>
                                                <td>{{ $instance['time_in']?->format('h:i A') ?? '-' }}</td>
                                                <td>{{ $instance['time_out']?->format('h:i A') ?? '-' }}</td>
                                                <td>{{ $instance['duration'] }}</td>
                                                <td>{{ $instance['marked_by'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="footer-note">Generated {{ now()->format('Y-m-d h:i A') }}</div>
        </div>

    </div>

    <script>
        function printOnly(which) {
            document.body.classList.remove('printing-practical', 'printing-theory');
            document.body.classList.add('printing-' + which);
            window.print();
        }

        window.addEventListener('afterprint', function() {
            document.body.classList.remove('printing-practical', 'printing-theory');
        });
    </script>
</body>

</html>