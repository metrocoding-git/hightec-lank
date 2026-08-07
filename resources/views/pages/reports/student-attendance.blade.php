@extends('layouts.app')
@section('title')
    Attendance Report
@endsection

@section('mainContent')
    {{-- Inline (not @push('css')) — this layout doesn't reliably output
         @stack('css'); a <style> tag here always renders regardless. --}}
    <style>
        .summary-cards { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
        .summary-card {
            flex: 1 1 200px; padding: 16px 18px; border-radius: 12px;
            background: #f5f5ff; border: 1px solid #e9ecf2;
        }
        .summary-card .summary-value { font-size: 24px; font-weight: 700; color: var(--tp-theme-primary, #5150e9); }
        .summary-card .summary-label { font-size: 12px; color: #8a8fa3; margin-top: 2px; }
        .status-pill { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .status-pill.pill-out { background: #ffe6bf; color: #a25a00; }
        .status-pill.pill-in { background: #d3f5df; color: #1a7f45; }
    </style>

    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Scan Attendance Report</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">Student Information</a>
                    <a href="#">Attendance Report</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">

            {{-- Summary --}}
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="summary-value">{{ number_format($totalCount) }}</div>
                    <div class="summary-label">Sessions matching filter</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">{{ number_format($inProgressCount) }}</div>
                    <div class="summary-label">Still checked in (no time out yet)</div>
                </div>
                <div class="summary-card">
                    <div class="summary-value">{{ intdiv($totalMinutes, 60) }}h {{ $totalMinutes % 60 }}m</div>
                    <div class="summary-label">Total duration (completed sessions)</div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="main-title mb-15"><h3>Filter</h3></div>
                        <form method="GET" action="{{ route('reports.student-attendance') }}">
                            <div class="row">
                                <div class="col-lg-3 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label">Register No.</label>
                                        <input type="text" name="admission_number" class="primary_input_field form-control" value="{{ request('admission_number') }}" placeholder="Admission / register no.">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label">Student Name</label>
                                        <input type="text" name="student_name" class="primary_input_field form-control" value="{{ request('student_name') }}" placeholder="Student name">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label">Intake</label>
                                        <select name="intake_id" class="primary_select form-control">
                                            <option value="">All Intakes</option>
                                            @foreach ($intakes as $intake)
                                                <option value="{{ $intake->id }}" {{ request('intake_id') == $intake->id ? 'selected' : '' }}>{{ $intake->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label">Course</label>
                                        <select name="course_id" class="primary_select form-control">
                                            <option value="">All Courses</option>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label">Subject</label>
                                        <select name="subject_id" class="primary_select form-control">
                                            <option value="">All Subjects</option>
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label">Vehicle</label>
                                        <select name="vehicle_id" class="primary_select form-control">
                                            <option value="">All Vehicles</option>
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label">In User</label>
                                        <select name="marked_in_by" class="primary_select form-control">
                                            <option value="">Anyone</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" {{ request('marked_in_by') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label">Out User</label>
                                        <select name="marked_out_by" class="primary_select form-control">
                                            <option value="">Anyone</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" {{ request('marked_out_by') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-2 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label">Status</label>
                                        <select name="status" class="primary_select form-control">
                                            <option value="">All</option>
                                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Still checked in</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label">Date From</label>
                                        <input type="date" name="date_from" class="primary_input_field form-control" value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-15">
                                    <div class="primary_input">
                                        <label class="primary_input_label">Date To</label>
                                        <input type="date" name="date_to" class="primary_input_field form-control" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-15 d-flex align-items-end justify-content-end">
                                    <a href="{{ route('reports.student-attendance') }}" class="btn btn-outline-secondary mr-10">Clear</a>
                                    <a href="{{ route('reports.student-attendance.export', request()->query()) }}" class="btn btn-outline-primary mr-10">
                                        <span class="ti-download pr-2"></span>Export CSV
                                    </a>
                                    <button type="submit" class="primary-btn small fix-gr-bg">
                                        <span class="ti-search pr-2"></span>@lang('common.search')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row mt-20">
                <div class="col-lg-12">
                    <div class="white-box">
                        <div class="d-flex justify-content-between align-items-center mb-10">
                            <small class="text-muted">Export CSV downloads every matching row — not just the page shown below.</small>
                        </div>
                        <div class="table-responsive">
                            <table class="table school-table-style" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Register No.</th>
                                        <th>Student</th>
                                        <th>Intake</th>
                                        <th>Course</th>
                                        <th>Subject</th>
                                        <th>Time In</th>
                                        <th>In By</th>
                                        <th>Time Out</th>
                                        <th>Out By</th>
                                        <th>Duration</th>
                                        <th>Vehicle</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sessions as $session)
                                        <tr>
                                            <td>{{ $session->attendance_date->format('Y-m-d') }}</td>
                                            <td>{{ $session->student->admission_number ?? '-' }}</td>
                                            <td>{{ $session->student->student_name ?? '-' }}</td>
                                            <td>{{ $session->intake->name ?? '-' }}</td>
                                            <td>{{ $session->course->name ?? '-' }}</td>
                                            <td>{{ $session->subject->name ?? 'Course-wise' }}</td>
                                            <td>{{ $session->time_in ? $session->time_in->format('h:i A') : '-' }}</td>
                                            <td>{{ $session->markedInBy->name ?? '-' }}</td>
                                            <td>
                                                @if ($session->time_out)
                                                    {{ $session->time_out->format('h:i A') }}
                                                @else
                                                    <span class="status-pill pill-in">Still in</span>
                                                @endif
                                            </td>
                                            <td>{{ $session->markedOutBy->name ?? '-' }}</td>
                                            <td>{{ $session->duration_label }}</td>
                                            <td>{{ $session->vehicle->name ?? '-' }}</td>
                                            <td>{{ $session->note ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="13" class="text-center">No records found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-20">
                            <small class="text-muted">
                                Showing {{ $sessions->firstItem() ?? 0 }}–{{ $sessions->lastItem() ?? 0 }}
                                of {{ number_format($sessions->total()) }}
                            </small>
                            {{-- Laravel's default pagination view is Tailwind-based since v8 and won't
                                 match this Bootstrap 4 theme — bootstrap-4 renders correctly here. --}}
                            {{ $sessions->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection