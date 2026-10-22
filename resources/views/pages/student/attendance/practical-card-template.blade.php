<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Attendance Card Template</title>
    <style>
        /* Same palette/spacing as the real per-student card
           (practical-card.blade.php), reused here so the template looks
           like the same product — just without a student header block,
           attendance instance rows, or the checkpoint sections, since none
           of those exist without a specific student. */
        :root {
            --brand: #1d4e89;
            --brand-dark: #123a66;
            --brand-light: #eaf1fb;
            --brand2: #6a3fb5;
            --brand2-dark: #4b2a82;
            --brand2-light: #f1eafb;
            --border: #d8dfe8;
            --ok: #1f8a4c;
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

        .btn-excel {
            background: var(--ok);
            border-color: var(--ok);
            color: #fff;
        }

        .card-wrap {
            background: #fff;
            overflow: hidden;
            box-shadow: 0 2px 14px rgba(20, 30, 60, 0.08);
            margin-bottom: 22px;
        }

        .card-wrap:first-of-type {
            border-radius: 0;
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
            padding: 8px 10px;
            text-align: left;
            vertical-align: middle;
        }

        table.attendance td.center {
            text-align: center;
        }

        table.attendance tr:nth-child(even) td {
            background: #fafbfd;
        }

        .chip {
            display: inline-block;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 2px 9px;
            margin-right: 6px;
            font-size: 11px;
            color: #556;
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

            body.printing-practical #theoryTemplateCard {
                display: none !important;
            }

            body.printing-theory #practicalTemplateCard {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="cards-page-wrap">

        <div class="toolbar no-print">
            {{-- Template is chosen by Course AND Intake (subjects are assigned per intake + course). --}}
            <form method="GET" action="{{ route('student-practical-card.template') }}" id="templateCourseForm"
                style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
                <div class="field">
                    <label for="course_id">Course</label>
                    <select name="course_id" id="course_id"
                        onchange="document.getElementById('intake_id').value=''; document.getElementById('templateCourseForm').submit();">
                        @forelse ($courses as $course)
                            <option value="{{ $course->id }}" @selected($course->id == $selectedCourseId)>
                                {{ $course->name }}
                            </option>
                        @empty
                            <option value="">No courses found</option>
                        @endforelse
                    </select>
                </div>
                <div class="field">
                    <label for="intake_id">Intake</label>
                    <select name="intake_id" id="intake_id" onchange="document.getElementById('templateCourseForm').submit();">
                        @forelse ($intakes as $intake)
                            <option value="{{ $intake->id }}" @selected($intake->id == $selectedIntakeId)>
                                {{ $intake->name }}
                            </option>
                        @empty
                            <option value="">No intake has subjects for this course</option>
                        @endforelse
                    </select>
                </div>
            </form>

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
                    Print Practical Template
                </button>
                <button type="button" class="btn btn-secondary" onclick="printOnly('theory')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z" />
                    </svg>
                    Print Theory Template
                </button>
                @if ($selectedCourseId)
                    <a href="{{ route('student-practical-card.template.export.practical', ['course_id' => $selectedCourseId, 'intake_id' => $selectedIntakeId]) }}"
                        class="btn btn-excel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                        </svg>
                        Export Practical Excel
                    </a>
                    <a href="{{ route('student-practical-card.template.export.theory', ['course_id' => $selectedCourseId, 'intake_id' => $selectedIntakeId]) }}"
                        class="btn btn-excel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                        </svg>
                        Export Theory Excel
                    </a>
                @endif
            </div>
        </div>

        {{-- =======================================================
             PRACTICAL TEMPLATE — just the subjects a course covers
             (name + planned Demo/Practical time). No student, no
             attendance instances, no Before/After Checking or Course
             Attendance sections — none of those exist without a
             specific student's attendance data.
             ======================================================= --}}
        <div class="card-wrap" id="practicalTemplateCard">
            <div
                style="text-align: center; font-size: 11px; color: rgb(38, 38, 38); padding-bottom: 10px; padding-top: 10px; background-color: #dee2e7;">
                <span style="font-weight: bold; font-size: 20px;">HIGHTEC LANKA INTERNATIONAL</span>
                <span style="display: block;">Vocational & Technical Training Institute (Pvt) Ltd.</span>
                <span style="display: block; margin-top: 5px;">No 549, Kandy Road, Mawathagama.</span>
            </div>

            <div class="card-title-bar">Practical Attendance Card Template</div>
            @php
                $tplCourseName = optional($courses->firstWhere('id', $selectedCourseId))->name;
                $tplIntakeName = optional($intakes->firstWhere('id', $selectedIntakeId))->name;
            @endphp
            <div style="text-align:center; font-size:12px; padding:6px 0; color:#334;">
                <strong>Course:</strong> {{ $tplCourseName ?? '-' }} &nbsp;|&nbsp; <strong>Intake:</strong> {{ $tplIntakeName ?? '-' }}
            </div>

            <div class="table-area">
                <div class="section-block">
                    <div class="section-heading">Practical Subjects</div>

                    @if (empty($practicalSubjects))
                        <div class="empty-state">No practical subjects are assigned to this intake &amp; course.</div>
                    @else
                        <table class="attendance">
                            <thead>
                                <tr>
                                    <th style="width:6%">No</th>
                                    <th>Subject</th>
                                    <th style="width:20%">Demonstrator Time</th>
                                    <th style="width:20%">Practical Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($practicalSubjects as $index => $subject)
                                    <tr>
                                        <td class="center">{{ $index + 1 }}</td>
                                        <td>{{ $subject['subject'] }}</td>
                                        <td class="center"><span class="chip">{{ $subject['demonstrator_time'] }}</span></td>
                                        <td class="center"><span class="chip">{{ $subject['practical_time'] }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="font-weight:bold; background:#eef2f7;">
                                    <td></td>
                                    <td style="text-align:right;">Total ({{ $practicalTotals['subjects'] }} subjects) &nbsp; hh:mm</td>
                                    <td class="center"><span class="chip">{{ $practicalTotals['demonstrator'] }}</span></td>
                                    <td class="center"><span class="chip">{{ $practicalTotals['practical'] }}</span></td>
                                </tr>
                                <tr style="font-weight:bold; background:#e3e9f2;">
                                    <td></td>
                                    <td style="text-align:right;">Total Demonstrator + Practical Time</td>
                                    <td class="center" colspan="2"><span class="chip">{{ $practicalTotals['overall'] }}</span></td>
                                </tr>
                            </tfoot>
                        </table>
                    @endif
                </div>
            </div>

            <div class="footer-note">Generated {{ now()->format('Y-m-d h:i A') }}</div>
        </div>

        {{-- =======================================================
             THEORY TEMPLATE — just the subject names.
             ======================================================= --}}
        <div class="card-wrap theory" id="theoryTemplateCard">
            <div
                style="text-align: center; font-size: 11px; color: rgb(38, 38, 38); padding-bottom: 10px; padding-top: 10px; background-color: #dee2e7;">
                <span style="font-weight: bold; font-size: 20px;">HIGHTEC LANKA INTERNATIONAL</span>
                <span style="display: block;">Vocational & Technical Training Institute (Pvt) Ltd.</span>
                <span style="display: block; margin-top: 5px;">No 549, Kandy Road, Mawathagama.</span>
            </div>

            <div class="card-title-bar">Theory Attendance Card Template</div>
            @php
                $tplCourseName = optional($courses->firstWhere('id', $selectedCourseId))->name;
                $tplIntakeName = optional($intakes->firstWhere('id', $selectedIntakeId))->name;
            @endphp
            <div style="text-align:center; font-size:12px; padding:6px 0; color:#334;">
                <strong>Course:</strong> {{ $tplCourseName ?? '-' }} &nbsp;|&nbsp; <strong>Intake:</strong> {{ $tplIntakeName ?? '-' }}
            </div>

            <div class="table-area">
                <div class="section-block">
                    <div class="section-heading">Theory Subjects</div>

                    @if (empty($theorySubjects))
                        <div class="empty-state">No theory subjects are assigned to this intake &amp; course.</div>
                    @else
                        <table class="attendance">
                            <thead>
                                <tr>
                                    <th style="width:6%">No</th>
                                    <th>Subject</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($theorySubjects as $index => $subject)
                                    <tr>
                                        <td class="center">{{ $index + 1 }}</td>
                                        <td>{{ $subject['subject'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="font-weight:bold; background:#eef2f7;">
                                    <td></td>
                                    <td>Total Theory Subjects: {{ count($theorySubjects) }}</td>
                                </tr>
                            </tfoot>
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
