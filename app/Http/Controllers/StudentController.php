<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\FeeInvoice;
use App\Models\BloodGroup;
use App\Models\Branch;
use App\Models\Course;
use App\Models\District;
use App\Models\FeeInvoices;
use App\Models\Gender;
use App\Models\Intake;
use App\Models\StudentCategory;
use App\Models\StudentCourse;
use App\Models\StudentVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StudentController extends Controller
{
    // ================= AddStudentController methods =================
      public function index()
    {
        $branches = Auth::user()->branches;
        $genders = Gender::all();
        $categories = StudentCategory::all();
        $bloodGroups = BloodGroup::all();
        $intakes = Intake::all();
        $districts = District::all();

        return view('pages.student.student-info.add-student', compact('branches',  'genders', 'categories', 'bloodGroups', 'intakes', 'districts'));
    }

    public function store(Request $request)
    {
        // dd($request);
        $validatedData = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            // 'admission_number' => 'required|string|unique:students,admission_number',
            'admission_date' => 'required|date',
            'student_category_id' => 'required|exists:student_categories,id',
            'gender_id' => 'required|exists:genders,id',
            'student_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'photo' => 'nullable|image|max:2048',
            'email_address' => 'nullable|email|max:255|unique:students,email_address',
            'phone_number' => 'required|digits:10',
            'phone_number_2' => 'nullable|digits:10',
            'stu_age' => 'nullable|integer',
            'district_id' => 'nullable|exists:districts,id',

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

            'national_id_number' => ['required', 'string', 'regex:/^([0-9]{9}[vV]|[0-9]{12})$/', 'unique:students,national_id_number'],
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
            'sibling_id' => 'nullable|exists:students,id',
            'admission_number' => 'nullable|string|unique:students,admission_number',
        ]);

        $validatedData['is_old_stu'] = 0;
        if (!$validatedData['admission_number']) {
            $validatedData['admission_number'] = $this->generateNextAdmissionNumber();
        } else {
            $validatedData['is_old_stu'] = 1;
        }

        $validatedData['create_user_id'] = Auth::id();


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

        $validatedData['create_user_id'] = Auth::id();

        Student::create($validatedData);

   

        if ($validatedData['is_old_stu'] == 1) {
            return redirect()->route('student.add-intake-with-courses.index')->with('success', 'Student add successfully');
        } else {
            return redirect()->route('fees.add-invoice')->with('success', 'Student add successfully');
        }
    }


    public function edit(Student $student)
    {
        $branches = Auth::user()->branches;
        $genders = Gender::all();
        $categories = StudentCategory::all();
        $bloodGroups = BloodGroup::all();
        $intakes = Intake::all();
        $districts = District::all();
        $student = Student::findOrFail($student->id);
        

        return view('pages.student.student-info.edit-student', compact([
            'student',
            'branches',
            'genders',
            'categories',
            'bloodGroups',
            'intakes',
            'districts',
            'student',
        ]));
    }

    public function update(Request $request, Student $student)
    {
        $validatedData = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'admission_number' => 'required|string',
            'admission_date' => 'required|date',
            'student_category_id' => 'required|exists:student_categories,id',
            'gender_id' => 'required|exists:genders,id',
            'student_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'photo' => 'nullable|image|max:2048',
            'email_address' => 'nullable|email|max:255',
            'phone_number' => 'required|digits:10',
            'phone_number_2' => 'nullable|digits:10',
            'stu_age' => 'nullable|integer',
            'district_id' => 'nullable|exists:districts,id',

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

            'national_id_number' => ['required', 'string', 'regex:/^([0-9]{9}[vV]|[0-9]{12})$/', 'unique:students,national_id_number,' . $student->id],
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

        $validatedData['update_user_id'] = Auth::id();

        $student->update($validatedData);
        return redirect()->route('student-list')->with('success', 'Student edit successfully');
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
            'status' => 'required',
            'notes' => 'nullable|string',
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
            'status' => 'required',
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
        $students = Student::with('verifications')->get();
        $branches = Branch::all();
        $intakes = Intake::with('branch')->get();
        $studentIntakes = DB::table('student_intakes')
            ->join('students', 'student_intakes.student_id', '=', 'students.id')
            ->join('intakes', 'student_intakes.intake_id', '=', 'intakes.id')
            ->leftJoin('branches', 'intakes.branch_id', '=', 'branches.id')
            ->leftJoin('student_verifications', 'student_verifications.student_id', '=', 'students.id')
            ->select(
                'student_intakes.*',
                'students.student_name',
                'students.admission_number',
                'intakes.name as intake_name',
                'branches.name as branch_name'
            )
            ->orderBy('student_intakes.id', 'desc')
            ->get();

        return view('pages.student.student-info.student-add-intake', compact('students', 'branches', 'intakes', 'studentIntakes'));
    }

    public function storeStudentIntake(Request $request)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'intake_id' => 'required|exists:intakes,id',
            'status' => 'required',
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
            ->select(
                'student_intakes.*',
                'students.student_name',
                'students.admission_number',
                'intakes.name as intake_name',
                'branches.name as branch_name'
            )
            ->orderBy('student_intakes.id', 'desc')
            ->get();

        if (!$studentIntake) {
            return redirect()->route('student.add-intake-with-courses.index')->with('error', 'Enrollment not found.');
        }

        return view('pages.student.student-info.student-add-intake', compact('studentIntake', 'students', 'intakes', 'studentIntakes'));
    }

    public function updateStudentIntake(Request $request, $id)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'intake_id' => 'required|exists:intakes,id',
            'status' => 'required',
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
                'status' => $validatedData['status'],
                'notes' => $validatedData['notes'],
                'updated_at' => now()
            ]);

        return back()->with('success', 'Intake enrollment updated successfully.');
    }


    // New method to handle intake with courses assignment
    public function studentAddIntakeWithCourses(Request $request)
    {
        $students = Student::all();
        $branches = Branch::all();
        $intakes = Intake::with('branch')->get();
        $courses = Course::all();

        return view('pages.student.student-info.student-add-intake', compact(
            'students',
            'branches',
            'intakes',
            'courses',
        ));
    }

    public function studentAddIntakeWithCoursesData(Request $request)
    {
        $query = StudentCourse::with([
            'student',
            'intake',
            'course',
            'branch',
            'student.studentCategory',
            'change_course'
        ]);


        // Filters
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->intake_id) {
            $query->where('intake_id', $request->intake_id);
        }

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($search = $request->input('search.value')) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%")
                    ->orWhere('national_id_number', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('phone_number_2', 'like', "%{$search}%");
            })
                ->orWhereHas('course', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('intake', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('branch', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        }


        return datatables()->of($query)

            ->addColumn('student_name', fn($row) => $row->student->student_name ?? '-')

            ->addColumn('name_verified', function ($row) {
                return $row->student->isVerified('Full Name Verified')
                    ? '<span class="text-success">YES</span>' : 'NO';
            })

            ->addColumn('category', fn($row) => $row->student->studentCategory->category_name ?? '-')

            ->addColumn('admission_no', fn($row) => $row->student->admission_number)

            ->addColumn('nic', fn($row) => $row->student->national_id_number)

            ->addColumn('nic_verified', function ($row) {
                return $row->student->isVerified('NIC Verified')
                    ? '<span class="text-success">YES</span>' : 'NO';
            })

            ->addColumn('intake', fn($row) => $row->intake->name ?? '-')

            ->addColumn('course', fn($row) => $row->course->name ?? '-')

            ->addColumn('change_course', fn($row) => $row->change_course->name ?? '-')

            ->addColumn('mobile1', fn($row) => $row->student->phone_number)

            ->addColumn('mobile2', fn($row) => $row->student->phone_number_2 ?? '-')

            ->addColumn('mobile1_verified', function ($row) {
                return $row->student->isVerified('phone_number')
                    ? '<span class="text-success">YES</span>' : 'NO';
            })

            ->addColumn('mobile2_verified', function ($row) {
                return $row->student->isVerified('Mobile Number 2 Verified')
                    ? '<span class="text-success">YES</span>' : 'NO';
            })

            ->addColumn('branch', fn($row) => $row->branch->name ?? '-')

            ->addColumn('address', fn($row) => $row->student->permanent_address ?? '-')

            ->addColumn('address_verified', function ($row) {
                return $row->student->isVerified('permanent_address')
                    ? '<span class="text-success">YES</span>' : 'NO';
            })

            ->addColumn('enroll_date', fn($row) => $row->created_at ? $row->created_at->format('Y-m-d') : '-')

            ->addColumn('status', function ($row) {
                return '<span class="badge badge-info">' . ucfirst($row->status) . '</span>';
            })

            ->addColumn('note', fn($row) => $row->notes)

            ->addColumn('actions', function ($row) {

                $buttons = '<div class="dropdown">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                    Action
                </button>
                <div class="dropdown-menu">';

                // PENDING
                if ($row->status == 'pending') {
                    $buttons .= '
                    <a class="dropdown-item" href="#" onclick="changeStatus(' . $row->id . ', \'start\')">
                        <span class="text-info">Start</span>
                    </a>

                    <a class="dropdown-item text-danger" href="#" onclick="deleteIntake(' . $row->id . ')">
                        Delete
                    </a>

                    <a class="dropdown-item" href="#"
                        onclick="openChangeCourseModal(' . $row->id . ', ' . $row->course_id . ', ' . $row->intake_id . ')">
                        <span class="text-primary">Change Course</span>
                    </a>';
                }

                // START
                if ($row->status == 'start') {
                    $buttons .= '
                    <a class="dropdown-item" href="#" onclick="changeStatus(' . $row->id . ', \'return\')">
                        <span class="text-warning">Return</span>
                    </a>

                    <a class="dropdown-item" href="#" onclick="changeStatus(' . $row->id . ', \'complete\')">
                        <span class="text-success">Complete</span>
                    </a>

                    <a class="dropdown-item" href="#"
                        onclick="openChangeCourseModal(' . $row->id . ', ' . $row->course_id . ', ' . $row->intake_id . ')">
                        <span class="text-primary">Change Course</span>
                    </a>';
                }

                // RETURN
                if ($row->status == 'return') {
                    $buttons .= '
                    <a class="dropdown-item" href="#" onclick="changeStatus(' . $row->id . ', \'start\')">
                        <span class="text-info">Restart</span>
                    </a>

                    <a class="dropdown-item" href="#" onclick="changeStatus(' . $row->id . ', \'complete\')">
                        <span class="text-success">Complete</span>
                    </a>';
                }

                $buttons .= '</div></div>';

                return $buttons;
            })

            ->rawColumns([
                'name_verified',
                'nic_verified',
                'mobile1_verified',
                'mobile2_verified',
                'address_verified',
                'status',
                'actions'
            ])

            ->make(true);
    }


    public function storeStudentIntakeWithCourses(Request $request)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'branch_id' => 'required|exists:branches,id',
            'intake_id' => 'required|exists:intakes,id',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
            'notes' => 'nullable|string'
        ]);

        // Check if student is already enrolled in this intake
        $courseExists = StudentCourse::where('student_id', $validatedData['student_id'])
            ->where('intake_id', $validatedData['intake_id'])
            ->whereIn('course_id', $validatedData['course_ids'])
            ->exists();

        if ($courseExists) {
            return back()->withErrors(['intake_id' => 'Student is already enrolled in this intake this course. Please edit the existing enrollment instead.']);
        }


        // Start transaction
        DB::beginTransaction();

        try {
            // Insert course enrollments
            foreach ($validatedData['course_ids'] as $courseId) {
                StudentCourse::create([
                    'student_id' => $validatedData['student_id'],
                    'branch_id' => $validatedData['branch_id'],
                    'course_id' => $courseId,
                    'intake_id' =>  $validatedData['intake_id'],
                    'status' => 'pending',
                    'notes' => $validatedData['notes'],
                    'user_id' => Auth::id(),
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

    public function updateStatus(Request $request, $id)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'status' => 'required|in:pending,start,return,complete',
            ]);

            // Also update the status in related student_courses records
            DB::table('student_courses')
                ->where('id', $id)
                ->update([
                    'status' => $validatedData['status'],
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status value provided.'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status. Please try again.'
            ], 500);
        }
    }

    public function destroyStudentAddIntakeWithCourses($id)
    {
        StudentCourse::where('id', $id)->delete();
        return back()->with('success', 'Intake enrollment removed successfully.');
    }

    public function intakeCourseSearch(Request $request)
    {
        $intakeId = $request->input('intake_id');
        $courseId = $request->input('course_id');

        if (!$intakeId || !$courseId) {
            return response()->json(['count' => 0]);
        }

        $intake = Intake::find($intakeId);
        $course = Course::find($courseId);
        $expireDate = Carbon::parse($intake->start_date)->addDays((int)$course->expiration_date);

        $canAdd = now()->lte($expireDate);
        $message = $canAdd
            ? 'Course is available for adding students.'
            : 'Course period has expired. Cannot add new students.';


        $count = StudentCourse::where('intake_id', $intakeId)
            ->where('course_id', $courseId)
            ->count();

        return response()->json([
            'count' => $count,
            'can_add' => $canAdd,
            'message' => $message,
            'expire_date' => $expireDate->toDateString(),
        ]);
    }
    // ===============


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

    public function verify($id)
    {
        $student = Student::findOrFail($id);
        return view('pages.student.student-info.verify', compact('student'));
    }



    public function verifySubmit(Request $request, $studentId)
    {
        $request->validate([
            'verification_items' => 'required|array',
            'signature_data' => 'required|string',
        ]);

        $student = Student::findOrFail($studentId);

        // ---- SAVE SIGNATURE ----
        $folder = storage_path('app/public/signatures/students/');

        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0777, true);
        }

        $signature = $request->signature_data;
        $image = str_replace('data:image/png;base64,', '', $signature);
        $image = str_replace(' ', '+', $image);

        $imageName = 'signature_' . time() . '.png';

        File::put($folder . $imageName, base64_decode($image));

        // Save relative path for browser
        $signaturePath = 'signatures/students/' . $imageName;

        // -------- SAVE EACH VERIFICATION ITEM --------
        foreach ($request->verification_items as $item) {
            $newValue = $request->verification_values[$item] ?? null;

            $oldValue = match ($item) {
                'Full Name Verified' => $student->student_name,
                'NIC Verified' => $student->national_id_number,
                'Address Verified' => $student->permanent_address,
                'Mobile Number Verified' => $student->phone_number,
                'Mobile Number 2 Verified' => $student->phone_number_2,
                default => null,
            };

            StudentVerification::create([
                'student_id'        => $student->id,
                'verification_item' => $item,
                'old_value'         => $oldValue,
                'new_value'         => $newValue,
                'signature_path'    => $signaturePath,
                'updated_by'        => auth()->id(),
            ]);
        }

        // UPDATE STUDENT
        $student->update([
            'student_name'       => $request->verification_values['Full Name Verified'] ?? null,
            'national_id_number' => $request->verification_values['NIC Verified'] ?? null,
            'permanent_address'  => $request->verification_values['Address Verified'] ?? null,
            'phone_number'       => $request->verification_values['Mobile Number Verified'] ?? null,
            'phone_number_2'     => $request->verification_values['Mobile Number 2 Verified'] ?? null,
        ]);

        return back()->with('success', 'Verification saved successfully!');
    }

    public function checkAdmission(Request $request)
    {
        $exists = Student::where('admission_number', $request->admission_number)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }


    public function studentImport(Request $request)
    {
        $request->validate([
            'students_file' => 'required|file|mimes:xlsx,xls',
        ]);

        $data = Excel::toArray([], $request->file('students_file'));
        $rows = $data[0]; // first sheet

        if (count($rows) < 2) {
            return back()->with('error', 'No data found in the file.');
        }

        // First row = headers
        $headers = $rows[0];

        // Remaining rows = data
        $dataRows = array_slice($rows, 1);

        foreach ($dataRows as $row) {

            // Combine header + row to make associative array
            $cleanRow = array_combine($headers, $row);

            // Handle DOB: Excel numeric → date
            $dob = $cleanRow['Date of Birth:'] ?? null;
            if (is_numeric($dob)) {
                $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dob);
                $dob = $excelDate->format('Y-m-d');
            }

            $nic = $cleanRow['NIC Number:'] ?? null;
            if (!$nic) {
                continue; // skip empty NIC
            }

            // Skip if NIC already exists
            if (Student::where('national_id_number', $nic)->exists()) {
                continue;
            }

            $admissionNumber = $this->generateNextAdmissionNumber();

            Student::create([
                'branch_id' => 1,
                'admission_number' => $admissionNumber,
                'student_name' => $cleanRow['Student Full Name:'] ?? '',
                'permanent_address' => $cleanRow['Permanent address:'] ?? '',
                'phone_number' => $cleanRow['Phone number :'] ?? '',
                'guardians_name' => $cleanRow['Name of guardian:'] ?? '',
                'guardians_phone' => $cleanRow["Guardian's Phone Number:"] ?? '',
                'date_of_birth' => $dob,
                'national_id_number' => $nic,
                'district' => $cleanRow['District:'] ?? '',
                'admission_date' => now(),
                'is_old_stu' => 0,
                'create_user_id' => Auth::id(),
            ]);
        }

        return redirect()->route('fees.add-invoice')->with('success', 'Student add successfully');
    }


    public function checkNic(Request $request)
    {
        $nics = $request->input('nics', []);
        $existing = Student::whereIn('national_id_number', $nics)->pluck('national_id_number')->toArray();

        return response()->json([
            'existing' => $existing
        ]);
    }


    public function changeCourse(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        try {
            $studentCourse = StudentCourse::findOrFail($id);

            // OPTIONAL: Prevent duplicate same course
            if ($studentCourse->course_id == $request->course_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This course is already assigned.'
                ]);
            }

            $studentCourse->change_course_id = $request->course_id;
            $studentCourse->save();

            return response()->json([
                'success' => true,
                'message' => 'Course updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    // ================= StudentViewController methods =================
    public function studentView($id)
    {
        $student = Student::with(['branch', 'gender', 'studentCategory', 'bloodGroup', 'courses', 'intakes','courseWithIntake'])->findOrFail($id);

        // Fetch payment history
        $payments = FeeInvoices::where('student_id', $id)->with(['invoice_item.fee_type'])->get();

        // Fetch detailed course information for the student
        $courses = $student->courses()->with(['intakes'])->get();

        // Fetch detailed intake information for the student
        $intakes = $student->intakes()->with(['branch', 'courses'])->get();

        return view('pages.student.student-info.student-view', compact('student', 'payments', 'courses', 'intakes'));
    }
}
