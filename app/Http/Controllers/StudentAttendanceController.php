<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Intake;
use App\Models\Student;
use App\Models\StudentAttendanceImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class StudentAttendanceController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        $intakes = Intake::all();
        $attendanceStudents = collect();
        $selectedIntakeId = request('intake_id');
        $selectedCourseId = request('course_id');
        $selectedAttendanceDate = request('attendance_date');

        if ($selectedIntakeId && $selectedCourseId && $selectedAttendanceDate) {
            $attendanceStudents = Student::with(['branch:id,name'])
                ->whereHas('courseWithIntake', function ($query) use ($selectedIntakeId, $selectedCourseId) {
                    $query->where('intake_id', $selectedIntakeId)
                        ->where('course_id', $selectedCourseId);
                })
                ->active()
                ->orderBy('student_name')
                ->get(['id', 'admission_number', 'student_name', 'phone_number', 'branch_id']);
        }

        return view('pages.student.student-info.student-attendance', compact(
            'courses',
            'intakes',
            'attendanceStudents',
            'selectedIntakeId',
            'selectedCourseId',
            'selectedAttendanceDate'
        ));
    }

    public function studentAttendanceImport()
    {
        $intakes = Intake::all();
        $attendanceImports = collect();

        if (Schema::hasTable('student_attendance_imports')) {
            $attendanceImports = StudentAttendanceImport::with(['intake:id,name', 'course:id,name', 'uploader:id,name'])
                ->latest()
                ->get();
        }

        return view('pages.student.student-info.student-attendance-import', compact('intakes', 'attendanceImports'));
    }

    public function studentAttendanceImportStore(Request $request)
    {
        $request->validate([
            'intake_id' => 'required|exists:intakes,id',
            'course_id' => 'required|exists:courses,id',
            'attendance_date' => 'required|date',
            'attendance_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('attendance_file');
        $fileName = 'attendance_' . date('Ymd_His') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $storedPath = $file->storeAs('attendance-imports', $fileName, 'public');

        StudentAttendanceImport::create([
            'intake_id' => $request->intake_id,
            'course_id' => $request->course_id,
            'attendance_date' => $request->attendance_date,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $storedPath,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()
            ->route('student-attendance-import')
            ->with('message-success', 'Attendance file uploaded successfully: ' . basename($storedPath));
    }

    public function studentAttendanceImportDownload($id)
    {
        if (!Schema::hasTable('student_attendance_imports')) {
            return redirect()->back()->with('message-danger', 'Attendance import table not found.');
        }

        $import = StudentAttendanceImport::findOrFail($id);

        if (!Storage::disk('public')->exists($import->file_path)) {
            return redirect()->back()->with('message-danger', 'File not found.');
        }

        return response()->download(Storage::disk('public')->path($import->file_path), $import->file_name);
    }
}
