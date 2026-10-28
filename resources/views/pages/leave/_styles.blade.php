@push('css')
    <style>
        .white-box { border-radius: 14px; box-shadow: 0 2px 14px rgba(20, 20, 43, 0.05); border: 1px solid #eceef3; }
        .lv-title { font-weight: 700; font-size: 17px; margin: 0 0 14px; display: flex; align-items: center; gap: 8px; }
        .lv-title i { color: var(--gradient_1); }
        .lv-stats { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
        .lv-stat { flex: 1 1 160px; padding: 14px 18px; border-radius: 14px; background: #fff; border: 1px solid #eceef3; box-shadow: 0 2px 14px rgba(20, 20, 43, 0.05); }
        .lv-stat .v { font-size: 22px; font-weight: 700; color: #1c1f2b; font-variant-numeric: tabular-nums; }
        .lv-stat .l { font-size: 12px; color: #8a8fa3; margin-top: 2px; }
        .lv-badge { display: inline-block; padding: 3px 11px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; white-space: nowrap; }
        .lv-pending { background: #fff6e0; color: #9a7300; }
        .lv-approved { background: #e8f6ee; color: #1e7a43; }
        .lv-rejected { background: #fdecea; color: #a12a2a; }
        .lv-cancelled { background: #f0f2f5; color: #6b7784; }
        .lv-table td { vertical-align: middle; }
        .lv-table .num { text-align: right; font-variant-numeric: tabular-nums; }
        .lv-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 16px; }
        .lv-tabs a { padding: 7px 16px; border-radius: 999px; background: #f0f2f5; color: #565d6d; font-weight: 600; font-size: 13px; }
        .lv-tabs a.active { background: linear-gradient(90deg, var(--gradient_1), var(--gradient_2)); color: #fff; }
        .lv-tabs a .c { margin-left: 6px; font-size: 11px; opacity: .85; }
        .lv-dl { display: grid; grid-template-columns: 170px 1fr; gap: 10px 16px; margin: 0; }
        .lv-dl dt { color: #8a8fa3; font-weight: 600; font-size: 13px; }
        .lv-dl dd { margin: 0; color: #1c1f2b; }
        @media (max-width: 576px) { .lv-dl { grid-template-columns: 1fr; gap: 2px; } .lv-dl dd { margin-bottom: 10px; } }
        .lv-reason { white-space: pre-line; background: #fafbfc; border: 1px solid #eceef3; border-radius: 10px; padding: 12px 14px; }
        .lv-days-hint { font-size: 12px; color: #6b7784; margin-top: 4px; }
        .lv-muted { color: #8a8fa3; font-size: 12px; }
    </style>
@endpush
