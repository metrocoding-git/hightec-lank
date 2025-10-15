<?php

namespace App\Http\Controllers;

use App\Models\BloodGroup;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Gender;
use App\Models\Intake;
use App\Models\Student;
use App\Models\StudentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AddStudentController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        $genders = Gender::all();
        $categories = StudentCategory::all();
        $bloodGroups = BloodGroup::all();
        $intakes = Intake::all();

        // Generate next admission number
        $nextAdmissionNumber = $this->generateNextAdmissionNumber();

        return view('pages.student.student-info.add-student', compact('branches', 'genders', 'categories', 'bloodGroups', 'intakes', 'nextAdmissionNumber'));
    }

    public function store(Request $request)
    {
        // dd($request);
        $validatedData = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'admission_number' => 'required|string|unique:students,admission_number',
            'admission_date' => 'required|date',
            'student_category_id' => 'nullable|exists:student_categories,id',
            'gender_id' => 'nullable|exists:genders,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
            'email_address' => 'nullable|email|max:255|unique:students,email_address',
            'phone_number' => 'required|numeric|max_digits:10|unique:students,phone_number',
            'phone_number_2' => 'nullable|numeric|max_digits:10',
            'stu_age' => 'nullable|integer',

            'current_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'blood_group_id' => 'nullable|string',
            'height' => 'nullable|string',
            'weight' => 'nullable|string',
            'guardians_name' => 'required|string|max:255',
            'relation_guardian_id' => 'nullable|string|max:50',
            'guardians_email' => 'nullable|email|max:255',
            'guardians_photo' => 'nullable|image|max:2048',
            'guardians_phone' => 'nullable|string|max:20',
            'guardians_occupation' => 'nullable|string|max:255',
            'guardians_address' => 'nullable|string',
           
            'national_id_number' => ['required', 'string', 'regex:/^([0-9]{9}[vV]|[0-9]{12})$/'],
            'additional_notes' => 'nullable|string',
            'document_title_1' => 'nullable|string|max:255',
            'document_file_1' => 'nullable|file|max:2048',
            'document_title_2' => 'nullable|string|max:255',
            'document_file_2' => 'nullable|file|max:2048',
            'document_title_3' => 'nullable|string|max:255',
            'document_file_3' => 'nullable|file|max:2048',
            'document_title_4' => 'nullable|string|max:255',
            'document_file_4' => 'nullable|file|max:2048',
            'previous_institute_details' => 'nullable|string',
            'dormitory_id' => 'nullable|string|max:100',
            'sibling_id' => 'nullable|exists:students,id'
        ]);

        // Handle file uploads
        $fileFields = [
            'photo' => 'student_photos',
            'guardians_photo' => 'guardian_photos',
            'document_file_1' => 'documents',
            'document_file_2' => 'documents',
            'document_file_3' => 'documents',
            'document_file_4' => 'documents'
        ];

        foreach ($fileFields as $field => $folder) {
            if ($request->hasFile($field)) {
                $validatedData[$field] = $request->file($field)->store($folder, 'public');
            }
        }

        // Combine first and last name
        $validatedData['student_name'] = $validatedData['first_name'] . ' ' . $validatedData['last_name'];

        Student::create($validatedData);
        return redirect()->route('student.add-intake.index')->with('success', 'Student created successfully');
    }


    public function edit(Student $student)
    {
        $branches = Branch::all();
        $genders = Gender::all();
        $categories = StudentCategory::all();
        $bloodGroups = BloodGroup::all();
        $intakes = Intake::all();
        $student = Student::findOrFail($student->id);
        // ds($student);

        return view('pages.student.student-info.edit-student', compact([
            'student',
            'branches',
            'genders',
            'categories',
            'bloodGroups',
            'intakes',
            'student',
        ]));
    }

    public function update(Request $request, Student $student)
    {
        $validatedData = $request->validate([
             'branch_id' => 'required|exists:branches,id',
            'admission_number' => 'required|string',
            'admission_date' => 'required|date',
            'student_category_id' => 'nullable|exists:student_categories,id',
            'gender_id' => 'nullable|exists:genders,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
            'email_address' => 'nullable|email|max:255',
            'phone_number' => 'required|numeric|max_digits:10',
            'phone_number_2' => 'nullable|numeric|max_digits:10',
            'stu_age' => 'nullable|integer',

            'current_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'blood_group_id' => 'nullable|string',
            'height' => 'nullable|string',
            'weight' => 'nullable|string',
            'guardians_name' => 'required|string|max:255',
            'relation_guardian_id' => 'nullable|string|max:50',
            'guardians_email' => 'nullable|email|max:255',
            'guardians_photo' => 'nullable|image|max:2048',
            'guardians_phone' => 'nullable|string|max:20',
            'guardians_occupation' => 'nullable|string|max:255',
            'guardians_address' => 'nullable|string',
           
            'national_id_number' => ['required', 'string', 'regex:/^([0-9]{9}[vV]|[0-9]{12})$/'],
            'additional_notes' => 'nullable|string',
            'document_title_1' => 'nullable|string|max:255',
            'document_file_1' => 'nullable|file|max:2048',
            'document_title_2' => 'nullable|string|max:255',
            'document_file_2' => 'nullable|file|max:2048',
            'document_title_3' => 'nullable|string|max:255',
            'document_file_3' => 'nullable|file|max:2048',
            'document_title_4' => 'nullable|string|max:255',
            'document_file_4' => 'nullable|file|max:2048',
            'previous_institute_details' => 'nullable|string',
            'dormitory_id' => 'nullable|string|max:100',
            'sibling_id' => 'nullable|exists:students,id'
        ]);

        // Handle file uploads
        $fileFields = [
            'photo' => 'student_photos',
            'guardians_photo' => 'guardian_photos',
            'document_file_1' => 'documents',
            'document_file_2' => 'documents',
            'document_file_3' => 'documents',
            'document_file_4' => 'documents'
        ];

        foreach ($fileFields as $field => $folder) {
            if ($request->hasFile($field)) {
                if ($student->$field) {
                    Storage::disk('public')->delete($student->$field);
                }
                $validatedData[$field] = $request->file($field)->store($folder, 'public');
            }
        }

        // Combine first and last name
        $validatedData['student_name'] = $validatedData['first_name'] . ' ' . $validatedData['last_name'];

        $student->update($validatedData);
        return back()->with('success', 'Student updated successfully');
    }

    // Student Course Management Methods
    public function studentAddCourse()
    {
        $students = Student::all();
        $courses = Course::all();
        $studentCourses = DB::table('student_courses')
            ->join('students', 'student_courses.student_id', '=', 'students.id')
            ->join('courses', 'student_courses.course_id', '=', 'courses.id')
            ->select('student_courses.*', 'students.student_name', 'students.admission_number', 'courses.name as course_name')
            ->get();

        return view('pages.student.student-info.student-add-course', compact('students', 'courses', 'studentCourses'));
    }

    public function storeStudentCourse(Request $request)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'enrolled_at' => 'required|date',
            'status' => 'required|in:active,completed,dropped',
            'notes' => 'nullable|string'
        ]);

        // Check if student is already enrolled in this course
        $exists = DB::table('student_courses')
            ->where('student_id', $validatedData['student_id'])
            ->where('course_id', $validatedData['course_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['course_id' => 'Student is already enrolled in this course.']);
        }

        DB::table('student_courses')->insert([
            'student_id' => $validatedData['student_id'],
            'course_id' => $validatedData['course_id'],
            'enrolled_at' => $validatedData['enrolled_at'],
            'status' => $validatedData['status'],
            'notes' => $validatedData['notes'],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Student enrolled in course successfully.');
    }

    public function editStudentCourse($id)
    {
        $studentCourse = DB::table('student_courses')->where('id', $id)->first();
        $students = Student::all();
        $courses = Course::all();
        $studentCourses = DB::table('student_courses')
            ->join('students', 'student_courses.student_id', '=', 'students.id')
            ->join('courses', 'student_courses.course_id', '=', 'courses.id')
            ->select('student_courses.*', 'students.student_name', 'students.admission_number', 'courses.name as course_name')
            ->get();

        if (!$studentCourse) {
            return redirect()->route('student.add-course.index')->with('error', 'Enrollment not found.');
        }

        return view('pages.student.student-info.student-add-course', compact('studentCourse', 'students', 'courses', 'studentCourses'));
    }

    public function updateStudentCourse(Request $request, $id)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'enrolled_at' => 'required|date',
            'status' => 'required|in:active,completed,dropped',
            'notes' => 'nullable|string'
        ]);

        // Check if student is already enrolled in this course (excluding current enrollment)
        $exists = DB::table('student_courses')
            ->where('student_id', $validatedData['student_id'])
            ->where('course_id', $validatedData['course_id'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['course_id' => 'Student is already enrolled in this course.']);
        }

        DB::table('student_courses')
            ->where('id', $id)
            ->update([
                'student_id' => $validatedData['student_id'],
                'course_id' => $validatedData['course_id'],
                'enrolled_at' => $validatedData['enrolled_at'],
                'status' => $validatedData['status'],
                'notes' => $validatedData['notes'],
                'updated_at' => now()
            ]);

        return back()->with('success', 'Course enrollment updated successfully.');
    }

    public function destroyStudentCourse($id)
    {
        DB::table('student_courses')->where('id', $id)->delete();
        return back()->with('success', 'Course enrollment removed successfully.');
    }

    // Student Intake Management Methods
    public function studentAddIntake()
    {
        $students = Student::all();
        $branches = Branch::all();
        $intakes = Intake::with('branch')->get();
        $studentIntakes = DB::table('student_intakes')
            ->join('students', 'student_intakes.student_id', '=', 'students.id')
            ->join('intakes', 'student_intakes.intake_id', '=', 'intakes.id')
            ->leftJoin('branches', 'intakes.branch_id', '=', 'branches.id')
            ->select('student_intakes.*', 'students.student_name', 'students.admission_number',
                     'intakes.name as intake_name', 'branches.name as branch_name')
            ->get();

        return view('pages.student.student-info.student-add-intake', compact('students', 'branches', 'intakes', 'studentIntakes'));
    }

    public function storeStudentIntake(Request $request)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'intake_id' => 'required|exists:intakes,id',
            'enrolled_at' => 'required|date',
            'status' => 'required|in:active,completed,dropped',
            'notes' => 'nullable|string'
        ]);

        // Check if student is already enrolled in this intake
        $exists = DB::table('student_intakes')
            ->where('student_id', $validatedData['student_id'])
            ->where('intake_id', $validatedData['intake_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['intake_id' => 'Student is already enrolled in this intake.']);
        }

        DB::table('student_intakes')->insert([
            'student_id' => $validatedData['student_id'],
            'intake_id' => $validatedData['intake_id'],
            'enrolled_at' => $validatedData['enrolled_at'],
            'status' => $validatedData['status'],
            'notes' => $validatedData['notes'],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Student enrolled in intake successfully.');
    }

    public function editStudentIntake($id)
    {
        $studentIntake = DB::table('student_intakes')->where('id', $id)->first();
        $students = Student::all();
        $intakes = Intake::with('branch')->get();
        $studentIntakes = DB::table('student_intakes')
            ->join('students', 'student_intakes.student_id', '=', 'students.id')
            ->join('intakes', 'student_intakes.intake_id', '=', 'intakes.id')
            ->leftJoin('branches', 'intakes.branch_id', '=', 'branches.id')
            ->select('student_intakes.*', 'students.student_name', 'students.admission_number',
                     'intakes.name as intake_name', 'branches.name as branch_name')
            ->get();

        if (!$studentIntake) {
            return redirect()->route('student.add-intake.index')->with('error', 'Enrollment not found.');
        }

        return view('pages.student.student-info.student-add-intake', compact('studentIntake', 'students', 'intakes', 'studentIntakes'));
    }

    public function updateStudentIntake(Request $request, $id)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'intake_id' => 'required|exists:intakes,id',
            'enrolled_at' => 'required|date',
            'status' => 'required|in:active,completed,dropped',
            'notes' => 'nullable|string'
        ]);

        // Check if student is already enrolled in this intake (excluding current enrollment)
        $exists = DB::table('student_intakes')
            ->where('student_id', $validatedData['student_id'])
            ->where('intake_id', $validatedData['intake_id'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['intake_id' => 'Student is already enrolled in this intake.']);
        }

        DB::table('student_intakes')
            ->where('id', $id)
            ->update([
                'student_id' => $validatedData['student_id'],
                'intake_id' => $validatedData['intake_id'],
                'enrolled_at' => $validatedData['enrolled_at'],
                'status' => $validatedData['status'],
                'notes' => $validatedData['notes'],
                'updated_at' => now()
            ]);

        return back()->with('success', 'Intake enrollment updated successfully.');
    }

    public function destroyStudentIntake($id)
    {
        DB::table('student_intakes')->where('id', $id)->delete();
        return back()->with('success', 'Intake enrollment removed successfully.');
    }

    // New method to handle intake with courses assignment
    public function studentAddIntakeWithCourses()
    {
        $students = Student::all();
        $branches = Branch::all();
        $intakes = Intake::with('branch')->get();
        $courses = Course::all();

        // Get student intakes with courses
        $studentIntakes = DB::table('student_intakes')
            ->join('students', 'student_intakes.student_id', '=', 'students.id')
            ->join('intakes', 'student_intakes.intake_id', '=', 'intakes.id')
            ->leftJoin('branches', 'intakes.branch_id', '=', 'branches.id')
            ->select('student_intakes.*', 'students.student_name', 'students.admission_number',
                     'intakes.name as intake_name', 'branches.name as branch_name')
            ->get();

        // Get related courses for each intake
        foreach ($studentIntakes as $intake) {
            $intake->courses = DB::table('student_courses')
                ->join('courses', 'student_courses.course_id', '=', 'courses.id')
                ->where('student_courses.student_id', $intake->student_id)
                ->whereIn('student_courses.course_id', function($query) use ($intake) {
                    $query->select('course_id')
                          ->from('course_intake')
                          ->where('intake_id', $intake->intake_id);
                })
                ->select('courses.id', 'courses.name', 'courses.code')
                ->get();
        }

        return view('pages.student.student-info.student-add-intake',
            compact('students', 'branches', 'intakes', 'courses', 'studentIntakes'));
    }

    public function storeStudentIntakeWithCourses(Request $request)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'intake_id' => 'required|exists:intakes,id',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
            'enrolled_at' => 'required|date',
            'status' => 'required|in:active,pending,asign,completed,dropped',
            'notes' => 'nullable|string'
        ]);

        // Check if student is already enrolled in this intake
        $intakeExists = DB::table('student_intakes')
            ->where('student_id', $validatedData['student_id'])
            ->where('intake_id', $validatedData['intake_id'])
            ->exists();

        if ($intakeExists) {
            return back()->withErrors(['intake_id' => 'Student is already enrolled in this intake. Please edit the existing enrollment instead.']);
        }

        // Check for duplicate courses in the selection
        $uniqueCourses = array_unique($validatedData['course_ids']);
        if (count($uniqueCourses) !== count($validatedData['course_ids'])) {
            return back()->withErrors(['course_ids' => 'Duplicate courses selected. Please select each course only once.']);
        }

        // Check if student is already enrolled in any of the selected courses
        $existingCourses = DB::table('student_courses')
            ->where('student_id', $validatedData['student_id'])
            ->whereIn('course_id', $validatedData['course_ids'])
            ->pluck('course_id')
            ->toArray();

        if (!empty($existingCourses)) {
            $existingCourseNames = DB::table('courses')
                ->whereIn('id', $existingCourses)
                ->pluck('name')
                ->toArray();
            return back()->withErrors(['course_ids' => 'Student is already enrolled in the following courses: ' . implode(', ', $existingCourseNames) . '. Please remove these from selection or edit existing enrollments.']);
        }

        // Start transaction
        DB::beginTransaction();

        try {
            // Insert intake enrollment
            $intakeId = DB::table('student_intakes')->insertGetId([
                'student_id' => $validatedData['student_id'],
                'intake_id' => $validatedData['intake_id'],
                'enrolled_at' => $validatedData['enrolled_at'],
                'status' => $validatedData['status'],
                'notes' => $validatedData['notes'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Insert course enrollments
            foreach ($validatedData['course_ids'] as $courseId) {
                DB::table('student_courses')->insert([
                    'student_id' => $validatedData['student_id'],
                    'course_id' => $courseId,
                    'enrolled_at' => $validatedData['enrolled_at'],
                    'status' => $validatedData['status'],
                    'notes' => $validatedData['notes'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            return back()->with('success', 'Student enrolled in intake and courses successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to enroll student. Please try again.']);
        }
    }

    /**
     * Generate the next admission number in the format "stu-XXX"
     * where XXX is a zero-padded number starting from 001
     */
    private function generateNextAdmissionNumber()
    {
        // Get the last student by admission number
        $lastStudent = Student::where('admission_number', 'like', 'stu-%')
            ->orderBy('admission_number', 'desc')
            ->first();

        if ($lastStudent) {
            // Extract the numeric part from the last admission number
            $lastNumber = intval(str_replace('stu-', '', $lastStudent->admission_number));
            $nextNumber = $lastNumber + 1;
        } else {
            // If no students exist, start with 001
            $nextNumber = 1;
        }

        // Format as stu-XXX with zero padding
        return 'stu-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // AJAX methods for dependent dropdowns
    public function getIntakesByBranch($branchId)
    {
        $intakes = Intake::where('branch_id', $branchId)->get();

        return response()->json([
            'intakes' => $intakes
        ]);
    }

    public function getCoursesByIntake($intakeId)
    {
        $courses = DB::table('course_intake')
            ->join('courses', 'course_intake.course_id', '=', 'courses.id')
            ->where('course_intake.intake_id', $intakeId)
            ->select('courses.id', 'courses.name', 'courses.code')
            ->get();

        return response()->json([
            'courses' => $courses
        ]);
    }

    public function searchStudentIntake(Request $request)
    {
        $query = DB::table('student_intakes')
            ->join('students', 'student_intakes.student_id', '=', 'students.id')
            ->join('intakes', 'student_intakes.intake_id', '=', 'intakes.id')
            ->leftJoin('branches', 'intakes.branch_id', '=', 'branches.id')
            ->select('student_intakes.*', 'students.student_name', 'students.admission_number',
                     'intakes.name as intake_name', 'branches.name as branch_name');

        if ($request->filled('branch_id')) {
            $query->where('intakes.branch_id', $request->branch_id);
        }
        if ($request->filled('intake_id')) {
            $query->where('student_intakes.intake_id', $request->intake_id);
        }
        if ($request->filled('course_id')) {
            $query->whereExists(function ($subquery) use ($request) {
                $subquery->select(DB::raw(1))
                         ->from('student_courses')
                         ->whereColumn('student_courses.student_id', 'student_intakes.student_id')
                         ->where('student_courses.course_id', $request->course_id);
            });
        }

        $studentIntakes = $query->get();

        // Get related courses for each intake
        foreach ($studentIntakes as $intake) {
            $intake->courses = DB::table('student_courses')
                ->join('courses', 'student_courses.course_id', '=', 'courses.id')
                ->where('student_courses.student_id', $intake->student_id)
                ->whereIn('student_courses.course_id', function($subquery) use ($intake) {
                    $subquery->select('course_id')
                          ->from('course_intake')
                          ->where('intake_id', $intake->intake_id);
                })
                ->select('courses.id', 'courses.name', 'courses.code')
                ->get();
        }

        $students = Student::all();
        $branches = Branch::all();
        $intakes = Intake::with('branch')->get(); // All intakes for the modal
        $courses = Course::all(); // All courses for the modal

        // Pass search parameters back to the view to retain selected values
        $search_branch_id = $request->branch_id;
        $search_intake_id = $request->intake_id;
        $search_course_id = $request->course_id;

        // Load intakes and courses for the search dropdowns if a branch/intake was selected
        $search_intakes = collect();
        $search_courses = collect();

        if ($search_branch_id) {
            $search_intakes = Intake::where('branch_id', $search_branch_id)->get();
        }
        if ($search_intake_id) {
            $search_courses = DB::table('course_intake')
                ->join('courses', 'course_intake.course_id', '=', 'courses.id')
                ->where('course_intake.intake_id', $search_intake_id)
                ->select('courses.id', 'courses.name', 'courses.code')
                ->get();
        }

        return view('pages.student.student-info.student-add-intake',
            compact('students', 'branches', 'intakes', 'courses', 'studentIntakes',
                    'search_branch_id', 'search_intake_id', 'search_course_id',
                    'search_intakes', 'search_courses'));
    }

    public function getSearchIntakesByBranch($branchId)
    {
        $intakes = Intake::where('branch_id', $branchId)->get();
        return response()->json(['intakes' => $intakes]);
    }

    public function getSearchCoursesByIntake($intakeId)
    {
        $courses = DB::table('course_intake')
            ->join('courses', 'course_intake.course_id', '=', 'courses.id')
            ->where('course_intake.intake_id', $intakeId)
            ->select('courses.id', 'courses.name', 'courses.code')
            ->get();
        return response()->json(['courses' => $courses]);
    }
}
