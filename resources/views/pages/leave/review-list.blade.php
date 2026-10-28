@extends('layouts.app')
@section('title', 'Leave Requests')

@section('mainContent')
    @include('pages.leave._styles')

    @php
        $tabs = ['pending' => 'Pending', 'approved' => 'Accepted', 'rejected' => 'Not Accepted', 'cancelled' => 'Cancelled', 'all' => 'All'];
        $tabQuery = request()->except(['status', 'page']);
    @endphp

    <section class="sms-breadcrumb mb-20">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Leave Requests</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="#">Leave</a>
                    <a href="#">Leave Requests</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="white-box p-20 mb-20">
                <form method="GET" action="{{ route('leave.review-list') }}" class="mb-0">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <input type="text" id="date_range" name="date_range" class="form-control"
                                placeholder="Leave dates (range)" value="{{ request('date_range') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="branch_id" class="form-control primary_select">
                                <option value="">All Branches</option>
                                @foreach ($branches as $b)
                                    <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="user_id" class="form-control primary_select">
                                <option value="">All Staff</option>
                                @foreach ($applicants as $u)
                                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="leave_type" class="form-control primary_select">
                                <option value="">All Leave Types</option>
                                @foreach ($types as $t)
                                    <option value="{{ $t }}" {{ request('leave_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-10">
                            <div class="row align-items-center">
                                <div class="col-10">
                                    <button type="submit" class="primary-btn small fix-gr-bg col-12 p-2">
                                        <span class="ti-search pr-2"></span> Search
                                    </button>
                                </div>
                                <div class="col-2">
                                    <a href="{{ route('leave.review-list') }}"
                                        class="primary-btn small fix-gr-bg col-12 p-2 text-white text-center d-block">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="white-box mb-20">
                <div class="lv-tabs">
                    @foreach ($tabs as $key => $label)
                        <a href="{{ route('leave.review-list', array_merge($tabQuery, ['status' => $key])) }}"
                            class="{{ $status === $key ? 'active' : '' }}">{{ $label }}
                            <span class="c">{{ $key === 'all' ? $counts->sum() : $counts[$key] ?? 0 }}</span></a>
                    @endforeach
                </div>

                <x-table>
                    <table class="table lv-table" width="100%">
                        <thead>
                            <tr>
                                <th>Staff</th>
                                <th>Role</th>
                                <th>Branch</th>
                                <th>Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th class="num">Days</th>
                                <th>Reason</th>
                                <th>Applied On</th>
                                <th>Status</th>
                                <th>Reviewed By</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leaves as $lv)
                                <tr>
                                    <td><strong>{{ $lv->user->name ?? '-' }}</strong></td>
                                    <td>{{ $lv->user->role->role_name ?? '-' }}</td>
                                    <td>{{ $lv->branch->name ?? '-' }}</td>
                                    <td>{{ $lv->leave_type }}</td>
                                    <td class="text-nowrap">{{ $lv->from_date->format('Y-m-d (D)') }}</td>
                                    <td class="text-nowrap">{{ $lv->to_date->format('Y-m-d (D)') }}</td>
                                    <td class="num">{{ rtrim(rtrim(number_format($lv->days, 1), '0'), '.') }}
                                        @if ($lv->day_part !== 'full')
                                            <div class="lv-muted">{{ $lv->day_part === 'first_half' ? 'Morning' : 'Afternoon' }}</div>
                                        @endif
                                    </td>
                                    <td style="max-width:240px;">{{ \Illuminate\Support\Str::limit($lv->reason, 70) }}</td>
                                    <td>{{ $lv->created_at?->format('Y-m-d') }}</td>
                                    <td><span class="lv-badge {{ $lv->statusBadgeClass() }}">{{ $lv->statusLabel() }}</span></td>
                                    <td>{{ $lv->reviewer->name ?? '-' }}</td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('leave.show', $lv->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye"></i>
                                            {{ $canReview && $lv->status === 'pending' && $lv->user_id != auth()->id() ? 'Review' : 'View' }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted">No leave requests here.</td>
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

@include('components.partials.date_picker_css_js')
@push('script')
    <script>
        flatpickr("#date_range", { mode: "range", dateFormat: "Y-m-d", allowInput: true });
    </script>
@endpush
