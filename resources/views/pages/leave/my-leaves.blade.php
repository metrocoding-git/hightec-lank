@extends('layouts.app')
@section('title', 'My Leaves')

@section('mainContent')
    @include('pages.leave._styles')

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>My Leaves</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Leave</a>
                    <a href="#">My Leaves</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">

            <div class="lv-stats">
                <div class="lv-stat">
                    <div class="v">{{ rtrim(rtrim(number_format($summary['approved_total'], 1), '0'), '.') ?: 0 }}</div>
                    <div class="l">Approved leave days in {{ $year }}</div>
                </div>
                <div class="lv-stat">
                    <div class="v">{{ rtrim(rtrim(number_format($summary['pending_total'], 1), '0'), '.') ?: 0 }}</div>
                    <div class="l">Days waiting for review ({{ $summary['pending_count'] }} request(s))</div>
                </div>
                @foreach ($summary['by_type'] as $type => $s)
                    <div class="lv-stat">
                        <div class="v">{{ rtrim(rtrim(number_format($s['approved'] ?? 0, 1), '0'), '.') ?: 0 }}</div>
                        <div class="l">{{ $type }} approved
                            @if (!empty($s['pending']))
                                · {{ rtrim(rtrim(number_format($s['pending'], 1), '0'), '.') }} pending
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($canApply)
                <div class="white-box p-20 mb-20">
                    <h3 class="lv-title"><i class="fa-solid fa-calendar-plus"></i> Apply for Leave</h3>
                    <form method="POST" action="{{ route('leave.store') }}" enctype="multipart/form-data" class="mb-0"
                        id="leave-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="primary_input_label">Leave Type <span class="text-danger">*</span></label>
                                <select name="leave_type" class="primary_select form-control" required>
                                    <option value="">Select leave type</option>
                                    @foreach ($types as $t)
                                        <option value="{{ $t }}" {{ old('leave_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="primary_input_label">From <span class="text-danger">*</span></label>
                                <input type="date" name="from_date" id="lv_from" class="form-control" required
                                    value="{{ old('from_date', now()->toDateString()) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="primary_input_label">To <span class="text-danger">*</span></label>
                                <input type="date" name="to_date" id="lv_to" class="form-control" required
                                    value="{{ old('to_date', now()->toDateString()) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="primary_input_label">Day</label>
                                <select name="day_part" id="lv_part" class="primary_select form-control">
                                    @foreach ($dayParts as $k => $label)
                                        <option value="{{ $k }}" {{ old('day_part', 'full') === $k ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="lv-days-hint" id="lv_days_hint"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="primary_input_label">Contact number during leave</label>
                                <input type="text" name="contact_during_leave" class="form-control" maxlength="255"
                                    value="{{ old('contact_during_leave') }}" placeholder="Optional">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="primary_input_label">Attachment (PDF / image, max 4 MB)</label>
                                <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="primary_input_label">Reason <span class="text-danger">*</span></label>
                                <textarea name="reason" rows="3" class="form-control" required maxlength="2000"
                                    placeholder="Why do you need this leave?">{{ old('reason') }}</textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="primary-btn fix-gr-bg col-12 p-2">
                                    <i class="fa-solid fa-paper-plane mr-1"></i> Submit Leave Request
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            <div class="white-box mb-20">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-10" style="gap:10px;">
                    <h3 class="lv-title mb-0"><i class="fa-solid fa-list"></i> My Leave Requests</h3>
                    <form method="GET" action="{{ route('leave.my') }}" class="d-flex mb-0" style="gap:8px;">
                        <select name="year" class="form-control" style="min-width:100px;" onchange="this.form.submit()">
                            @for ($y = now()->year + 1; $y >= now()->year - 3; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <select name="status" class="form-control" style="min-width:150px;" onchange="this.form.submit()">
                            <option value="">All statuses</option>
                            @foreach (['pending', 'approved', 'rejected', 'cancelled'] as $st)
                                <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <x-table>
                    <table class="table lv-table" width="100%">
                        <thead>
                            <tr>
                                <th>Applied On</th>
                                <th>Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Day</th>
                                <th class="num">Days</th>
                                <th>Status</th>
                                <th>Reviewed By</th>
                                <th>Review Note</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leaves as $lv)
                                <tr>
                                    <td>{{ $lv->created_at?->format('Y-m-d') }}</td>
                                    <td>{{ $lv->leave_type }}</td>
                                    <td>{{ $lv->from_date->format('Y-m-d (D)') }}</td>
                                    <td>{{ $lv->to_date->format('Y-m-d (D)') }}</td>
                                    <td>{{ $lv->dayPartLabel() }}</td>
                                    <td class="num">{{ rtrim(rtrim(number_format($lv->days, 1), '0'), '.') }}</td>
                                    <td><span class="lv-badge {{ $lv->statusBadgeClass() }}">{{ $lv->statusLabel() }}</span></td>
                                    <td>{{ $lv->reviewer->name ?? '-' }}
                                        @if ($lv->reviewed_at)
                                            <div class="lv-muted">{{ $lv->reviewed_at->format('Y-m-d h:i A') }}</div>
                                        @endif
                                    </td>
                                    <td style="max-width:260px;">{{ \Illuminate\Support\Str::limit($lv->review_note, 80) ?: '-' }}</td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('leave.show', $lv->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye"></i> View</a>
                                        @if ($canCancel && $lv->status === 'pending')
                                            <form method="POST" action="{{ route('leave.cancel', $lv->id) }}" class="d-inline"
                                                onsubmit="return confirm('Cancel this leave request?');">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-xmark"></i> Cancel</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No leave requests for {{ $year }}.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-table>
                <div class="mt-10">{{ $leaves->links('pagination::bootstrap-4') }}</div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        (function() {
            var from = document.getElementById('lv_from'),
                to = document.getElementById('lv_to'),
                part = document.getElementById('lv_part'),
                hint = document.getElementById('lv_days_hint');
            if (!from || !to || !part) return;

            function calc() {
                if (!from.value || !to.value) { hint.textContent = ''; return; }
                var a = new Date(from.value + 'T00:00:00'), b = new Date(to.value + 'T00:00:00');
                if (b < a) { hint.textContent = '"To" date is before "From" date.'; return; }
                if (part.value !== 'full') {
                    if (from.value !== to.value) { hint.textContent = 'Half day: From and To must be the same date.'; return; }
                    hint.textContent = a.getDay() === 0 ? 'Sunday — no working day.' : '0.5 day';
                    return;
                }
                var n = 0;
                for (var d = new Date(a); d <= b; d.setDate(d.getDate() + 1)) { if (d.getDay() !== 0) n++; }
                hint.textContent = n + ' working day(s) — Sundays are not counted.';
            }
            from.addEventListener('change', function() { if (!to.value || to.value < from.value) to.value = from.value; calc(); });
            to.addEventListener('change', calc);
            $(part).on('change', calc);
            calc();
        })();
    </script>
@endpush
