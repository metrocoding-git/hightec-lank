@extends('layouts.app')
@section('title', 'Leave Request Details')

@section('mainContent')
    @include('pages.leave._styles')

    @php $fmt = fn($n) => rtrim(rtrim(number_format((float) $n, 1), '0'), '.') ?: '0'; @endphp

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Leave Request #{{ $leave->id }}</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    @if ($isOwner)
                        <a href="{{ route('leave.my') }}">My Leaves</a>
                    @else
                        <a href="{{ route('leave.review-list') }}">Leave Requests</a>
                    @endif
                    <a href="#">Details</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-7 mb-20">
                    <div class="white-box h-100">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-15" style="gap:10px;">
                            <h3 class="lv-title mb-0"><i class="fa-solid fa-file-lines"></i> Leave Details</h3>
                            <span class="lv-badge {{ $leave->statusBadgeClass() }}" style="font-size:13px;">
                                {{ $leave->statusLabel() }}</span>
                        </div>

                        <dl class="lv-dl">
                            <dt>Staff</dt>
                            <dd><strong>{{ $leave->user->name ?? '-' }}</strong>
                                <span class="lv-muted">· {{ $leave->user->role->role_name ?? '-' }}</span></dd>
                            <dt>Branch</dt>
                            <dd>{{ $leave->branch->name ?? '-' }}</dd>
                            <dt>Leave Type</dt>
                            <dd>{{ $leave->leave_type }}</dd>
                            <dt>Dates</dt>
                            <dd>{{ $leave->from_date->format('Y-m-d (l)') }}
                                @unless ($leave->from_date->equalTo($leave->to_date))
                                    → {{ $leave->to_date->format('Y-m-d (l)') }}
                                @endunless
                            </dd>
                            <dt>Day</dt>
                            <dd>{{ $leave->dayPartLabel() }}</dd>
                            <dt>Number of Days</dt>
                            <dd><strong>{{ $fmt($leave->days) }}</strong> <span class="lv-muted">(Sundays not counted)</span></dd>
                            <dt>Contact During Leave</dt>
                            <dd>{{ $leave->contact_during_leave ?: '-' }}</dd>
                            <dt>Attachment</dt>
                            <dd>
                                @if ($leave->attachment)
                                    <a href="{{ route('leave.attachment', $leave->id) }}" target="_blank">
                                        <i class="fa-solid fa-paperclip"></i> Open attachment</a>
                                @else
                                    -
                                @endif
                            </dd>
                            <dt>Applied On</dt>
                            <dd>{{ $leave->created_at?->format('Y-m-d h:i A') }}</dd>
                            <dt>Reason</dt>
                            <dd><div class="lv-reason">{{ $leave->reason }}</div></dd>

                            @if ($leave->reviewed_at)
                                <dt>{{ $leave->status === 'approved' ? 'Accepted' : 'Not accepted' }} By</dt>
                                <dd>{{ $leave->reviewer->name ?? '-' }}
                                    <span class="lv-muted">· {{ $leave->reviewed_at->format('Y-m-d h:i A') }}</span></dd>
                                <dt>Review Note</dt>
                                <dd><div class="lv-reason">{{ $leave->review_note ?: '-' }}</div></dd>
                            @endif
                        </dl>

                        @if ($canCancel)
                            <form method="POST" action="{{ route('leave.cancel', $leave->id) }}" class="mt-20 mb-0"
                                onsubmit="return confirm('Cancel this leave request?');">
                                @csrf
                                <button class="btn btn-outline-danger"><i class="fa-solid fa-xmark"></i> Cancel this request</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="col-lg-5 mb-20">
                    @if ($canReview)
                        <div class="white-box mb-20">
                            <h3 class="lv-title"><i class="fa-solid fa-clipboard-check"></i> Review</h3>
                            <form method="POST" action="{{ route('leave.review', $leave->id) }}" class="mb-0" id="review-form">
                                @csrf
                                <input type="hidden" name="decision" id="decision">
                                <label class="primary_input_label">Note to staff
                                    <span class="lv-muted">(required when not accepting)</span></label>
                                <textarea name="review_note" id="review_note" rows="3" class="form-control mb-15"
                                    maxlength="2000">{{ old('review_note') }}</textarea>
                                <div class="d-flex" style="gap:10px;">
                                    <button type="button" class="btn btn-success flex-fill" data-decision="approved">
                                        <i class="fa-solid fa-check"></i> Accept</button>
                                    <button type="button" class="btn btn-danger flex-fill" data-decision="rejected">
                                        <i class="fa-solid fa-xmark"></i> Not Accept</button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="white-box mb-20">
                        <h3 class="lv-title"><i class="fa-solid fa-chart-simple"></i> {{ $isOwner ? 'My' : 'Staff' }} Leave in {{ $year }}</h3>
                        <div class="lv-stats mb-10">
                            <div class="lv-stat">
                                <div class="v">{{ $fmt($summary['approved_total']) }}</div>
                                <div class="l">Days approved</div>
                            </div>
                            <div class="lv-stat">
                                <div class="v">{{ $fmt($summary['pending_total']) }}</div>
                                <div class="l">Days pending</div>
                            </div>
                        </div>
                        @if (!empty($summary['by_type']))
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr><th>Type</th><th class="text-right">Approved</th><th class="text-right">Pending</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($summary['by_type'] as $type => $s)
                                        <tr>
                                            <td>{{ $type }}</td>
                                            <td class="text-right">{{ $fmt($s['approved'] ?? 0) }}</td>
                                            <td class="text-right">{{ $fmt($s['pending'] ?? 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    @if (!$isOwner && $overlapping->isNotEmpty())
                        <div class="white-box mb-20" style="border-color:#f5e3ab;">
                            <h3 class="lv-title"><i class="fa-solid fa-users"></i> Also on leave these days</h3>
                            <ul class="mb-0 pl-3">
                                @foreach ($overlapping as $o)
                                    <li>{{ $o->user->name ?? '-' }} — {{ $o->leave_type }},
                                        {{ $o->from_date->format('Y-m-d') }} → {{ $o->to_date->format('Y-m-d') }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <div class="white-box mb-20">
                <h3 class="lv-title"><i class="fa-solid fa-clock-rotate-left"></i> Other leave requests in {{ $year }}</h3>
                <x-table>
                    <table class="table lv-table" width="100%">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th class="num">Days</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($history as $h)
                                <tr>
                                    <td>{{ $h->leave_type }}</td>
                                    <td>{{ $h->from_date->format('Y-m-d') }}</td>
                                    <td>{{ $h->to_date->format('Y-m-d') }}</td>
                                    <td class="num">{{ $fmt($h->days) }}</td>
                                    <td><span class="lv-badge {{ $h->statusBadgeClass() }}">{{ $h->statusLabel() }}</span></td>
                                    <td>{{ \Illuminate\Support\Str::limit($h->reason, 60) }}</td>
                                    <td><a href="{{ route('leave.show', $h->id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted">No other leave requests this year.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-table>
            </div>
        </div>
    </section>
@endsection

@if ($canReview)
    @push('script')
        <script>
            $('#review-form [data-decision]').on('click', function() {
                var decision = $(this).data('decision');
                var note = $.trim($('#review_note').val());
                if (decision === 'rejected' && !note) {
                    toastr.error('Please write a reason before not accepting.');
                    $('#review_note').focus();
                    return;
                }
                var msg = decision === 'approved' ? 'Accept this leave request?' : 'Not accept this leave request?';
                if (!confirm(msg)) return;
                $('#decision').val(decision);
                $('#review-form').trigger('submit');
            });
        </script>
    @endpush
@endif
