@extends('layouts.app')
@push('css')
    <style>
        /* ---- Modern refresh: cards, breadcrumb (Section 25) ----
             BUG FIX: this page already used the sms-breadcrumb markup below, but never
             defined its CSS anywhere, so the banner rendered completely unstyled
             (no card, no gradient stripe, no crumb-trail styling). Adding the same
             breadcrumb + card CSS used on every other module page. ---- */
        .white-box {
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
        }

        .sms-breadcrumb {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 14px rgba(20, 20, 43, 0.06);
            border: 1px solid var(--border_color);
            padding: 18px 24px;
            position: relative;
            overflow: hidden;
        }

        .sms-breadcrumb::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--gradient_1), var(--gradient_2));
        }

        .sms-breadcrumb .row {
            align-items: center;
        }

        .sms-breadcrumb h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .sms-breadcrumb .bc-pages {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .sms-breadcrumb .bc-pages a {
            font-size: 13px;
            font-weight: 500;
            color: #8a90a2;
            text-decoration: none;
            transition: color .15s ease;
        }

        .sms-breadcrumb .bc-pages a:hover {
            color: var(--gradient_1);
        }

        .sms-breadcrumb .bc-pages a:not(:last-child)::after {
            content: '\203A';
            margin: 0 8px;
            color: #c7cbd6;
            font-weight: 400;
        }

        .sms-breadcrumb .bc-pages a:last-child {
            color: var(--gradient_1);
            font-weight: 700;
            pointer-events: none;
        }
    </style>
@endpush
@section('title')
    Student Details
@endsection

@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>Student Details</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ route('student-list') }}">Student List</a>
                    <a href="#">Student Details</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                {{-- Left Side Student Details --}}
                <div class="col-lg-3">
                    <div class="student-details-container">
                        <div class="student-details-header">
                            <div class="student-photo">
                                @if ($student->photo)
                                    <img src="{{ asset('storage/' . $student->photo) }}" alt="Student Photo">
                                @else
                                        <img src="{{ asset('backEnd/assets/img/icons/profile.svg') }}" alt="Student Photo">
                                @endif
                            </div>
                        </div>
                        <div class="student-info-details">
                            <div class="info-row">
                                <span>Student Name</span>
                                <strong>{{ $student->student_name }}</strong>
                            </div>
                            <div class="info-row">
                                <span>Admission No</span>
                                <strong>{{ $student->admission_number }}</strong>
                            </div>
                            <div class="info-row">
                                <span>Roll No</span>
                                <strong>2</strong>
                            </div>
                            <div class="info-row">
                                <span>Branch</span>
                                <strong>{{ $student->branch->name ?? '-' }}</strong>
                            </div>
                            <div class="info-row">
                                <span>Gender</span>
                                <strong>{{ $student->gender->name ?? '-' }}</strong>
                            </div>
                            <div class="info-row">
                                <span>Student Age</span>
                                <strong>{{ $student->stu_age ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side Tabs --}}
                <div class="col-lg-9">
                    <div class="white-box">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs nav-fill student-details-tabs" id="studentTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="true">Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="document-tab" data-toggle="tab" href="#document" role="tab"
                                    aria-controls="document" aria-selected="false">Document</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="record-tab" data-toggle="tab" href="#record" role="tab"
                                    aria-controls="record" aria-selected="false">Record</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="payment-tab" data-toggle="tab" href="#payment" role="tab"
                                    aria-controls="payment" aria-selected="false">Payment</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="other-tab" data-toggle="tab" href="#other" role="tab"
                                    aria-controls="other" aria-selected="false">Other</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content" id="studentTabContent">
                            <!-- Profile Tab -->
                            <div class="tab-pane fade show active" id="profile" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h3>Students Personal Info</h3>
                                        <table class="table table-bordered">
                                            {{-- <tr>
                                                <th>First Name</th>
                                                <td>{{ $student->first_name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Last Name</th>
                                                <td>{{ $student->last_name ?? 'N/A' }}</td>
                                            </tr> --}}
                                            <tr>
                                                <th>Full Name</th>
                                                <td>{{ $student->student_name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Date of Birth</th>
                                                <td>{{ $student->date_of_birth ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Gender</th>
                                                <td>{{ $student->gender->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Phone Number</th>
                                                <td>{{ $student->phone_number ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Phone number (optional)</th>
                                                <td>{{ $student->phone_number_2 ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email Address</th>
                                                <td>{{ $student->email_address ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>National ID Number</th>
                                                <td>{{ $student->national_id_number ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Age</th>
                                                <td>{{ $student->stu_age ?? 'N/A' }}</td>
                                            </tr>
                                        </table>

                                        <h5>Address Information</h5>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Permanent Address</th>
                                                <td>{{ $student->permanent_address ?? 'N/A' }}</td>
                                            </tr>
                                        </table>

                                        <h5>Guardian Information</h5>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Guardian's Name</th>
                                                <td>{{ $student->guardians_name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Guardian's Phone</th>
                                                <td>{{ $student->guardians_phone ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Guardian's Email</th>
                                                <td>{{ $student->guardians_email ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Guardian's Occupation</th>
                                                <td>{{ $student->guardians_occupation ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Guardian's Address</th>
                                                <td>{{ $student->guardians_address ?? 'N/A' }}</td>
                                            </tr>
                                        </table>

                                        <h5>Academic Information</h5>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Branch</th>
                                                <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Student Category</th>
                                                <td>{{ $student->studentCategory->category_name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Admission Date</th>
                                                <td>{{ $student->admission_date ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Previous Institute Details</th>
                                                <td>{{ $student->previous_institute_details ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Tab -->
                            <div class="tab-pane fade" id="document" role="tabpanel" aria-labelledby="document-tab">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Document Title</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($student->document_title_1 || $student->document_file_1)
                                                    <tr>
                                                        <td>{{ $student->document_title_1 ?? 'N/A' }}</td>
                                                        <td>
                                                            @if ($student->document_file_1)
                                                                <a href="{{ asset('storage/' . $student->document_file_1) }}"
                                                                    target="_blank" class="btn btn-sm btn-primary">View</a>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if ($student->document_title_2 || $student->document_file_2)
                                                    <tr>
                                                        <td>{{ $student->document_title_2 ?? 'N/A' }}</td>
                                                        <td>
                                                            @if ($student->document_file_2)
                                                                <a href="{{ asset('storage/' . $student->document_file_2) }}"
                                                                    target="_blank" class="btn btn-sm btn-primary">View</a>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if ($student->document_title_3 || $student->document_file_3)
                                                    <tr>
                                                        <td>{{ $student->document_title_3 ?? 'N/A' }}</td>
                                                        <td>
                                                            @if ($student->document_file_3)
                                                                <a href="{{ asset('storage/' . $student->document_file_3) }}"
                                                                    target="_blank" class="btn btn-sm btn-primary">View</a>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if ($student->document_title_4 || $student->document_file_4)
                                                    <tr>
                                                        <td>{{ $student->document_title_4 ?? 'N/A' }}</td>
                                                        <td>
                                                            @if ($student->document_file_4)
                                                                <a href="{{ asset('storage/' . $student->document_file_4) }}"
                                                                    target="_blank" class="btn btn-sm btn-primary">View</a>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Record Tab -->
                            <div class="tab-pane fade" id="record" role="tabpanel" aria-labelledby="record-tab">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h5>Course Details</h5>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Branch Name</th>
                                                    <th>Intake Name</th>
                                                    <th>Course Name</th>
                                                    <th>Enrolled At</th>
                                                    <th>Start Date</th>
                                                    <th>Expiration Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($student->courseWithIntake as $course)
                                                    <tr>
                                                        <td>{{ $course->branch->name ?? 'N/A' }}</td>
                                                        <td>{{ $course->intake->name ?? 'N/A' }}</td>
                                                        <td>{{ $course->course->name ?? 'N/A' }}</td>
                                                        <td>{{ $course->created_at ? \Carbon\Carbon::parse($course->created_at)->format('Y-m-d') : 'N/A' }}
                                                        </td>
                                                        <td>{{ $course->intake->start_date ? \Carbon\Carbon::parse($course->intake->start_date)->format('Y-m-d') : 'N/A' }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $intake = $course->intake ?? null;
                                                                $courseData = $course->course ?? null;

                                                                if (
                                                                    $intake &&
                                                                    $courseData &&
                                                                    $intake->start_date &&
                                                                    $courseData->expiration_date
                                                                ) {
                                                                    $start = \Carbon\Carbon::parse($intake->start_date);
                                                                    $days = (int) $courseData->expiration_date;

                                                                    $expiration = $start
                                                                        ->addDays($days)
                                                                        ->format('Y-m-d');
                                                                } else {
                                                                    $expiration = 'N/A';
                                                                }
                                                            @endphp

                                                            {{ $expiration }}

                                                        </td>
                                                        <td>{{ $course->status ?? 'N/A' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center">N/A</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>

                            <!-- Payment Tab -->
                            <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h5>Payment History</h5>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Invoice Number</th>
                                                    <th>Total Amount</th>
                                                    <th>Paid Amount</th>
                                                    <th>Due Amount</th>
                                                    <th>Payment Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($payments as $payment)
                                                    @php
                                                        $dueAmount =
                                                            ($payment->total_amount ?? 0) -
                                                            ($payment->paid_amount ?? 0);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $payment->invoice_number ?? 'N/A' }}</td>
                                                        <td>{{ $payment->total_amount ?? 'N/A' }}</td>
                                                        <td>{{ $payment->paid_amount ?? 'N/A' }}</td>
                                                        <td>{{ $dueAmount }}</td>
                                                        <td>{{ $payment->created_at ? \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d') : 'N/A' }}
                                                        </td>
                                                        <td>{{ $payment->payment_status ?? 'N/A' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">N/A</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Other Tab -->
                            <div class="tab-pane fade" id="other" role="tabpanel" aria-labelledby="other-tab">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h5>Additional Notes</h5>
                                        <p>{{ $student->additional_notes ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .student-details-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .student-details-header {
            background-color: #673ab7;
            padding: 20px;
            text-align: center;
            position: relative;
            min-height: 120px;
        }

        .student-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #fff;
            margin: 0 auto;
            position: absolute;
            bottom: -75px;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid #fff;
            overflow: hidden;
        }

        .student-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .student-info-details {
            padding: 70px 20px 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 5px;
            margin: 5px 0;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row span {
            color: #555;
            font-size: 1.1em;
            opacity: 0.9;
        }

        .info-row strong {
            color: #000;
            font-size: 1.1em;
            opacity: 1;
        }

        .student-details-tabs {
            border-bottom: 1px solid #dee2e6;
        }

        .student-details-tabs .nav-item .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #888;
            font-weight: 500;
            padding: 15px;
        }

        .student-details-tabs .nav-item .nav-link.active {
            border-bottom-color: #673ab7;
            color: #fff;
            background-color: #673ab7;
        }

        .profile-details .profile-info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .profile-details .profile-info-row span:first-child {
            font-weight: 500;
            color: #555;
        }

        .table-bordered {
            margin-bottom: 25px;
            width: 100%;
            table-layout: fixed;
        }

        /* .table-bordered th,
                .table-bordered td {
                    padding: 20px;
                    font-size: 18px;
                } */

        .table-bordered td {
            opacity: 0.9;
            color: #333;

        }

        .table tr td {
            padding-left: 20px !important;
        }
    </style>
@endsection