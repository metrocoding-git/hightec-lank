<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $student->student_name }} - Practical Attendance Card</title>
    <style>
        :root {
            --brand: #1d4e89;
            --brand-dark: #123a66;
            --brand-light: #eaf1fb;
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

        .card-wrap {
            max-width: 1150px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 14px rgba(20, 30, 60, 0.08);
        }

        /* ---------- toolbar ---------- */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 14px;
            flex-wrap: wrap;
            padding: 16px 24px;
            background: #f6f8fb;
            border-bottom: 1px solid var(--border);
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

        /* ---------- table ---------- */
        .table-area {
            padding: 20px 24px 26px;
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

        .checkpoint-cell {
            font-size: 11.5px;
            line-height: 1.35;
        }

        .checkpoint-cell .rng {
            color: #333;
        }

        .checkpoint-cell .dur {
            color: #888;
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
                border-radius: 0;
                box-shadow: none;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }

            table.attendance {
                font-size: 10.5px;
            }
        }
    </style>
</head>

<body>
    <div class="card-wrap">

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
                <a href="javascript:history.back()" class="btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z" />
                    </svg>
                    Print Card
                </button>
            </div>
        </div>

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

        @php
            $totalSubjects = count($subjects);
            $attendedSubjects = collect($subjects)->filter(fn($s) => $s['attended_count'] > 0)->count();
            $totalInstances = collect($subjects)->sum('attended_count');
        @endphp

        <div class="summary-strip">
            <div class="summary-item">
                <div class="num">{{ $attendedSubjects }} / {{ $totalSubjects }}</div>
                <div class="lbl">Subjects Attended</div>
            </div>
            <div class="summary-item">
                <div class="num">{{ $totalInstances }}</div>
                <div class="lbl">Total Practical Sessions</div>
            </div>
        </div>

        <div class="table-area">
            @if (empty($subjects))
                <div class="empty-state">No practical subjects are assigned to this course.</div>
            @else
                <table class="attendance">
                    <thead>
                        <tr>
                            <th style="width:4%">No</th>
                            <th style="width:11%">Date</th>
                            <th style="width:14%">Before Checking</th>
                            <th style="width:9%">Time In</th>
                            <th style="width:9%">Time Out</th>
                            <th style="width:8%">Duration</th>
                            <th style="width:13%">Vehicle</th>
                            <th style="width:11%">Marked By</th>
                            <th style="width:14%">After Checking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjects as $subject)
                            <tr class="subject-row">
                                <td colspan="9">
                                    {{ $subject['subject'] }}
                                    <span class="planned-times">
                                        <span class="chip">Demo Time: {{ $subject['demonstrator_time'] }}</span>
                                        <span class="chip">Practical Time: {{ $subject['practical_time'] }}</span>
                                    </span>
                                    @if ($subject['attended_count'] > 0)
                                        <span class="badge badge-ok">Attended {{ $subject['attended_count'] }}x</span>
                                    @else
                                        <span class="badge badge-warn">Not Attended</span>
                                    @endif
                                </td>
                            </tr>

                            @if ($subject['attended_count'] === 0)
                                <tr class="not-attended">
                                    <td colspan="9">No practical attendance recorded yet for this subject.</td>
                                </tr>
                            @else
                                @foreach ($subject['instances'] as $index => $instance)
                                    <tr class="instance-row">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $instance['date'] }}</td>
                                        <td class="checkpoint-cell">
                                            @if ($instance['before'])
                                                <div class="rng">{{ $instance['before']['time_in']?->format('h:i A') ?? '-' }}
                                                    &ndash;
                                                    {{ $instance['before']['time_out']?->format('h:i A') ?? '-' }}
                                                </div>
                                                <div class="dur">{{ $instance['before']['duration'] }}</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $instance['time_in']?->format('h:i A') ?? '-' }}</td>
                                        <td>{{ $instance['time_out']?->format('h:i A') ?? '-' }}</td>
                                        <td>{{ $instance['duration'] }}</td>
                                        <td>{{ $instance['vehicle'] }}</td>
                                        <td>{{ $instance['marked_by'] }}</td>
                                        <td class="checkpoint-cell">
                                            @if ($instance['after'])
                                                <div class="rng">{{ $instance['after']['time_in']?->format('h:i A') ?? '-' }}
                                                    &ndash;
                                                    {{ $instance['after']['time_out']?->format('h:i A') ?? '-' }}
                                                </div>
                                                <div class="dur">{{ $instance['after']['duration'] }}</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="footer-note">Generated {{ now()->format('Y-m-d h:i A') }}</div>
    </div>
</body>

</html>