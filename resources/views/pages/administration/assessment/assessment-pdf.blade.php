<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        h1.main-title {
            text-align: center;
            font-size: 18px;
            margin: 0;
        }

        p.sub-title {
            text-align: center;
            font-size: 12px;
            margin: 2px 0 10px;
        }

        h2.sheet-title {
            text-align: center;
            font-size: 14px;
            text-decoration: underline;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.meta td {
            padding: 3px 0;
            font-size: 11px;
        }

        table.main-table,
        table.main-table th,
        table.main-table td {
            border: 1px solid #000;
        }

        table.main-table {
            margin-top: 10px;
        }

        table.main-table th {
            background: #eee;
            padding: 4px;
            font-size: 10px;
        }

        table.main-table td {
            padding: 4px;
            font-size: 10px;
            vertical-align: top;
        }

        td.title-cell {
            font-weight: bold;
            font-style: italic;
        }

        td.center {
            text-align: center;
            vertical-align: middle;
        }

        table.boxes td {
            border: 1px solid #000;
            padding: 6px;
            width: 33%;
            vertical-align: top;
            font-size: 10px;
        }

        table.tick td.box {
            border: 1px solid #000;
            width: 20px;
            height: 20px;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
        }

        table.tick td.text {
            padding: 4px 8px;
            font-size: 10px;
        }

        .footer-bar {
            margin-top: 15px;
            font-size: 9px;
        }

        .footer-bar table td {
            border: none;
        }

        .footer-bar .doc-info {
            background: #7a1f1f;
            color: #fff;
            padding: 3px 8px;
        }
    </style>
</head>

<body>

    @php
        $marksBySubTitle = $assessment->marks->keyBy('assessment_template_sub_title_id');
        $template = $assessment->template;
    @endphp

    <h1 class="main-title">HIGHTEC LANKA INTERNATIONAL</h1>
    <p class="sub-title">VOCATIONAL &amp; TECHNICAL TRAINING INSTITUTE(PVT)LTD.</p>
    <h2 class="sheet-title">FINAL ASSESSMENT CHECK SHEET</h2>

    <table class="meta">
        <tr>
            <td style="width:65%;"><strong>Module Name:</strong> {{ $template->course->name ?? $template->module_name }}</td>
            <td><strong>NCS Code:</strong> {{ $template->ncs_code ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Trainee Name:</strong> {{ $assessment->student->student_name ?? '-' }}</td>
            <td><strong>Reg.No:</strong> {{ $assessment->student->admission_number ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>NIC:</strong> {{ $assessment->student->national_id_number ?? '-' }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width:5%;">No</th>
                <th>Performance</th>
                <th style="width:14%;">Competent or Non competent</th>
                <th style="width:12%;">Yield marks</th>
                <th style="width:16%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($template->titles as $titleIndex => $title)
                @php
                    $subCount = $title->subTitles->count();
                    $titleTotal = 0;
                    foreach ($title->subTitles as $sub) {
                        $titleTotal += (float) optional($marksBySubTitle->get($sub->id))->marks;
                    }
                @endphp
                <tr>
                    <td class="center">{{ str_pad($titleIndex + 1, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="title-cell">{{ $title->title }} ({{ $title->max_marks }})</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @foreach ($title->subTitles as $subIndex => $sub)
                    @php $mark = $marksBySubTitle->get($sub->id); @endphp
                    <tr>
                        <td></td>
                        <td>- {{ $sub->sub_title }}</td>
                        <td class="center">{{ $mark->competency ?? '' }}</td>
                        @if ($subIndex === 0)
                            <td class="center" rowspan="{{ $subCount }}">{{ rtrim(rtrim(number_format($titleTotal, 2), '0'), '.') }}
                                of {{ $title->max_marks }}</td>
                        @endif
                        <td>{{ $mark->remarks ?? '' }}</td>
                    </tr>
                @endforeach
            @endforeach

            <tr>
                <td colspan="2"><strong>Comments:</strong> {{ $assessment->comments ?? '' }}</td>
                <td colspan="3"></td>
            </tr>

            <tr>
                <td colspan="2"><strong>Final Results (Total Marks &amp; Grade)</strong></td>
                <td class="center"><strong>{{ $assessment->total_marks }} of {{ $assessment->max_marks }}</strong>
                </td>
                <td colspan="2" class="center"><strong>{{ $assessment->grade }}</strong></td>
            </tr>
        </tbody>
    </table>

    <table class="tick" style="margin-top:10px;">
        <tr>
            <td class="box">{{ $assessment->is_qualified ? 'X' : '' }}</td>
            <td class="text">Based on my evaluation, the operator has successfully completed the evaluation and is
                qualified to operate the above equipment.</td>
        </tr>
        <tr>
            <td class="box">{{ $assessment->is_qualified ? '' : 'X' }}</td>
            <td class="text">Based on my evaluation, the operator has not demonstrated competence in operating the
                equipment.</td>
        </tr>
    </table>

    <table class="boxes" style="margin-top:10px;">
        <tr>
            <td>
                <strong>Equipment Type &amp; Model</strong><br>
                {{ $assessment->vehicle->name ?? $assessment->equipment_model ?? '-' }}
            </td>
            <td>
                <strong>Location:</strong><br>
                {{ $assessment->branch->name ?? '-' }}
            </td>
            <td>
                <strong>Date &amp; Time:</strong><br>
                {{ optional($assessment->assessed_at)->format('Y-m-d h:i A') }}
            </td>
        </tr>
    </table>

    <div class="footer-bar">
        <table>
            <tr>
                <td style="width:70%;">HIGHTEC LANKA INTERNATIONAL VOCATIONAL &amp; TECHNICAL TRAINING INSTITUTE</td>
                <td class="doc-info">Date:2021/07/07&nbsp;&nbsp;&nbsp;Rev.001&nbsp;&nbsp;&nbsp;OP/FL/DI/024</td>
            </tr>
        </table>
    </div>

    <div style="page-break-before: always;"></div>

    <h1 class="main-title">HIGHTEC LANKA INTERNATIONAL</h1>
    <p class="sub-title">VOCATIONAL &amp; TECHNICAL TRAINING INSTITUTE(PVT)LTD.</p>

    <table class="meta" style="margin-top:30px;">
        <tr>
            <td style="width:55%;"><strong>Evaluator Name:</strong> {{ $assessment->evaluator->name ?? '-' }}</td>
            <td>
                <strong>Operator Signature:</strong><br>
                @if ($assessment->operator_signature)
                    <img src="{{ public_path('storage/' . $assessment->operator_signature) }}" style="height:60px;">
                @endif
            </td>
        </tr>
    </table>

    <div class="footer-bar" style="position:fixed; bottom:0; left:0; right:0;">
        <table>
            <tr>
                <td style="width:70%;">HIGHTEC LANKA INTERNATIONAL VOCATIONAL &amp; TECHNICAL TRAINING INSTITUTE</td>
                <td class="doc-info">Date:2021/07/07&nbsp;&nbsp;&nbsp;Rev.001&nbsp;&nbsp;&nbsp;OP/FL/DI/024</td>
            </tr>
        </table>
    </div>

</body>

</html>