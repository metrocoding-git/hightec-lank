@extends('layouts.app')

@section('title', 'Student Attendance Cards')

@push('styles')
    @include('components.partials.data_table_css')
@endpush

@section('mainContent')
    <div class="ampl-page">
        <div class="ampl-page-header">
            <h1>Student Attendance Cards</h1>
        </div>

        <form method="GET" action="{{ route('student-practical-card.index') }}" class="ampl-filterform row" style="margin-bottom: 16px;">
            <div class="col-md-4">
                <label>Course</label>
                <select name="course_id" class="form-control">
                    <option value="">All Courses</option>
                    @foreach ($courseOptions as $course)
                        <option value="{{ $course->id }}" @selected((string) $selectedCourseId === (string) $course->id)>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Search (Name / Admission No / NIC)</label>
                <input type="text" name="q" class="form-control" value="{{ $q }}" placeholder="Search...">
            </div>
            <div class="col-md-4" style="display:flex; align-items:flex-end; gap:8px;">
                <button type="submit" class="ampl-btn ampl-btn-primary">Filter</button>
                <a href="{{ route('student-practical-card.index') }}" class="ampl-btn ampl-btn-outline">Reset</a>
            </div>
        </form>

        <table id="attendance-cards-table" class="table table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Admission No</th>
                    <th>Student Name</th>
                    <th>NIC</th>
                    <th>Branch</th>
                    <th>Card</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $index => $student)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $student->admission_number ?? '-' }}</td>
                        <td>{{ $student->student_name }}</td>
                        <td>{{ $student->national_id_number ?? '-' }}</td>
                        <td>{{ $student->branch->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('student-practical-card.show', $student->id) }}" target="_blank" rel="noopener" class="ampl-btn ampl-btn-primary">
                                <i class="fa-solid fa-id-card"></i> View Card
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    @include('components.partials.data_table_js')
    <script>
        $(function () {
            $('#attendance-cards-table').DataTable({
                order: [[0, 'asc']],
            });
        });
    </script>
@endpush
