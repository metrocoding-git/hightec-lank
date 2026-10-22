<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\FeeInvoice;
use App\Models\BloodGroup;
use App\Models\Branch;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\District;
use App\Models\FeeInvoices;
use App\Models\Gender;
use App\Models\Intake;
use App\Models\StudentAttendanceImport;
use App\Models\StudentCategory;
use App\Models\StudentCourse;
use App\Models\StudentVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Yajra\DataTables\DataTables;
use App\Exports\StudentExport;

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

    public function studentCategory()
    {
        return view('pages.student.student-info.student-category');
    }

    public function addStudent()
    {
        return view('pages.student.student-info.add-student');
    }

    public function studentList()
    {
        $students = Student::with([
            'branch',
            'gender',
            'studentCategory',
            'verifications',
            'district',
            'createUser',
            'updateUser'
        ])->active()->latest()->get();
        $branches = Branch::all();

        return view('pages.student.student-info.student-list', compact('students', 'branches'));
    }

    public function studentDatatable()
    {
        $query = Student::with([
            'branch:id,name',
            'gender:id,name',
            'studentCategory:id,category_name',
            'district:id,name',
            'createUser:id,name',
            'updateUser:id,name',
            'verifications'
        ])->active();

        return DataTables::of($query)

            ->addIndexColumn()

            // ======================
            // DISPLAY COLUMNS
            // ======================

            ->editColumn('admission_number', fn($s) => $s->admission_number ?? '-')

            ->editColumn('student_name', function ($s) {
                $class = $s->isVerified('Full Name Verified') ? 'text-success' : '';
                return '<span class="' . $class . '">' . $s->student_name . '</span>';
            })

            ->editColumn('phone_number', function ($s) {
                $class = $s->isVerified('Mobile Number Verified') ? 'text-success' : '';
                return '<span class="' . $class . '">' . $s->phone_number . '</span>';
            })

            ->editColumn('phone_number_2', function ($s) {
                $class = $s->isVerified('Mobile Number 2 Verified') ? 'text-success' : '';
                return '<span class="' . $class . '">' . $s->phone_number_2 . '</span>';
            })

            ->editColumn('nic', function ($s) {
                $class = $s->isVerified('NIC Verified') ? 'text-success' : '';
                return '<span class="' . $class . '">' . $s->national_id_number . '</span>';
            })

            ->editColumn('address', function ($s) {
                $class = $s->isVerified('Address Verified') ? 'text-success' : '';
                return '<span class="' . $class . '">' . $s->permanent_address . '</span>';
            })

            ->editColumn('branch', fn($s) => $s->branch->name ?? '-')
            ->addColumn('admission_date', fn($s) => $s->admission_date ?? '-')
            ->editColumn('category', fn($s) => $s->studentCategory->category_name ?? '-')
            ->addColumn('gender', fn($s) => $s->gender->name ?? '-')
            ->addColumn('dob', fn($s) => $s->date_of_birth ?? '-')
            ->addColumn('age', fn($s) => $s->stu_age ?? '-')

            ->addColumn('photo', function ($s) {
                if (!$s->photo) return '-';
                $url = asset('storage/' . $s->photo);
                return '<a href="' . $url . '" target="_blank">
                        <img src="' . $url . '" width="40">
                    </a>';
            })

            ->editColumn('email', fn($s) => $s->email_address ?? '-')
            ->editColumn('district', fn($s) => $s->district->name ?? '-')

            ->addColumn(
                'full_name_verification',
                fn($s) =>
                $s->isVerified('Full Name Verified')
                    ? '<span class="text-success">YES</span>'
                    : 'NO'
            )

            ->addColumn(
                'phone_verification',
                fn($s) =>
                $s->isVerified('Mobile Number Verified')
                    ? '<span class="text-success">YES</span>'
                    : 'NO'
            )

            ->addColumn(
                'phone2_verification',
                fn($s) =>
                $s->isVerified('Mobile Number 2 Verified')
                    ? '<span class="text-success">YES</span>'
                    : 'NO'
            )

            ->addColumn(
                'nic_verification',
                fn($s) =>
                $s->isVerified('NIC Verified')
                    ? '<span class="text-success">YES</span>'
                    : 'NO'
            )

            ->addColumn(
                'address_verification',
                fn($s) =>
                $s->isVerified('Address Verified')
                    ? '<span class="text-success">YES</span>'
                    : 'NO'
            )

            ->addColumn('notes', fn($s) => $s->additional_notes ?? '-')
            ->addColumn('previous_institute', fn($s) => $s->previous_institute_details ?? '-')
            ->editColumn('created_by', fn($s) => $s->createUser->name ?? '-')
            ->editColumn('updated_by', fn($s) => $s->updateUser->name ?? '-')

            ->addColumn('action', function ($s) {
                $user = Auth::user();
                $items = [];

                if ($user->hasPermission('student-card.show')) {
                    $items[] = '<a class="dropdown-item" target="_blank" href="' . route('student-card.show', $s->id) . '">
                                <i class="fa-solid fa-id-card text-muted"></i> Generate Card
                            </a>';
                }

                if ($user->hasPermission('student-details')) {
                    $items[] = '<a class="dropdown-item" target="_blank" href="' . route('student-details', $s->id) . '">
                                <i class="fa-solid fa-eye text-muted"></i> View
                            </a>';
                }

                if ($user->hasPermission('add-student.update')) {
                    $items[] = '<a class="dropdown-item" href="' . route('add-student.edit', $s->id) . '">
                                <i class="fa-solid fa-pen text-muted"></i> Edit
                            </a>';
                }

                if ($user->hasPermission('add-student.verify')) {
                    $items[] = '<a class="dropdown-item" target="_blank" href="' . route('add-student.verify', $s->id) . '">
                                <i class="fa-solid fa-signature text-muted"></i> Verification
                            </a>';
                }

                if ($user->hasPermission('student-list.verification-history')) {
                    $items[] = '<a class="dropdown-item openVerificationModal" href="#" data-id="' . $s->id . '">
                                <i class="fa-solid fa-clock-rotate-left text-muted"></i> Verification Check
                            </a>';
                }

                if ($user->hasPermission('student.update-status')) {
                    $items[] = '<a class="dropdown-item text-danger" data-toggle="modal"
                               data-student-id="' . $s->id . '" data-target="#statusUpdateModal" href="#">
                                <i class="fa-solid fa-ban"></i> Disable
                            </a>';
                }

                if ($user->hasPermission('student-practical-card.show')) {
                    $items[] = '<a class="dropdown-item" target="_blank" href="' . route('student-practical-card.show', $s->id) . '">
                <i class="fa-solid fa-clipboard-list text-muted"></i> Practical Card
            </a>';
                }

                if (empty($items)) {
                    return '<span class="text-muted">&mdash;</span>';
                }

                return '
            <div class="dropdown modern-row-actions">
                <button class="btn-row-action" type="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    ' . implode('', $items) . '
                </div>
            </div>';
            })

            // ======================
            // GLOBAL SEARCH (ALL FIELDS)
            // ======================

            ->filter(function ($query) {
                if ($keyword = request('search.value')) {
                    $query->where(function ($q) use ($keyword) {

                        $q->where('student_name', 'like', "%{$keyword}%")
                            ->orWhere('admission_number', 'like', "%{$keyword}%")
                            ->orWhere('phone_number', 'like', "%{$keyword}%")
                            ->orWhere('phone_number_2', 'like', "%{$keyword}%")
                            ->orWhere('national_id_number', 'like', "%{$keyword}%")
                            ->orWhere('email_address', 'like', "%{$keyword}%")
                            ->orWhere('permanent_address', 'like', "%{$keyword}%")
                            ->orWhere('date_of_birth', 'like', "%{$keyword}%");

                        $q->orWhereHas(
                            'branch',
                            fn($b) =>
                            $b->where('name', 'like', "%{$keyword}%")
                        );

                        $q->orWhereHas(
                            'studentCategory',
                            fn($c) =>
                            $c->where('category_name', 'like', "%{$keyword}%")
                        );

                        $q->orWhereHas(
                            'gender',
                            fn($g) =>
                            $g->where('name', 'like', "%{$keyword}%")
                        );

                        $q->orWhereHas(
                            'district',
                            fn($d) =>
                            $d->where('name', 'like', "%{$keyword}%")
                        );

                        $q->orWhereHas(
                            'createUser',
                            fn($u) =>
                            $u->where('name', 'like', "%{$keyword}%")
                        );

                        $q->orWhereHas(
                            'updateUser',
                            fn($u) =>
                            $u->where('name', 'like', "%{$keyword}%")
                        );
                    });
                }
            })

            // ======================
            // RAW HTML COLUMNS
            // ======================

            ->rawColumns([
                'student_name',
                'phone_number',
                'phone_number_2',
                'nic',
                'address',
                'photo',
                'full_name_verification',
                'phone_verification',
                'phone2_verification',
                'nic_verification',
                'address_verification',
                'action'
            ])

            ->make(true);
    }

    public function exportAllStudents()
    {
        return Excel::download(
            new StudentExport(),
            'Students_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportFilteredStudents(Request $request)
    {
        $filename = 'Students_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new StudentExport(
                $request->get('branch_id'),
                $request->get('intake_id'),
                $request->get('course_id')
            ),
            $filename
        );
    }

    public function exportAllStudentsCsv()
    {
        $students = Student::with([
            'branch',
            'gender',
            'studentCategory',
            'district',
            'createUser',
            'updateUser'
        ])->active()->get();

        // Headers
        $headings = [
            'ID',
            'Admission Number',
            'Full Name',
            'Phone 1',
            'Phone 2',
            'NIC',
            'Address',
            'Branch',
            'Admission Date',
            'Category',
            'Gender',
            'Date of Birth',
            'Age',
            'Email',
            'District',
            'Full Name Verified',
            'Phone 1 Verified',
            'Phone 2 Verified',
            'NIC Verified',
            'Address Verified',
            'Notes',
            'Previous Institute',
            'Created By',
            'Updated By',
            'Created At',
            'Updated At'
        ];

        // Prepare data
        $data = [];
        foreach ($students as $student) {
            $data[] = [
                $student->id,
                $student->admission_number ?? '-',
                $student->student_name ?? '-',
                $student->phone_number ?? '-',
                $student->phone_number_2 ?? '-',
                $student->national_id_number ?? '-',
                $student->permanent_address ?? '-',
                $student->branch->name ?? '-',
                $student->admission_date ?? '-',
                $student->studentCategory->category_name ?? '-',
                $student->gender->name ?? '-',
                $student->date_of_birth ?? '-',
                $student->stu_age ?? '-',
                $student->email_address ?? '-',
                $student->district->name ?? '-',
                $student->isVerified('Full Name Verified') ? 'YES' : 'NO',
                $student->isVerified('Mobile Number Verified') ? 'YES' : 'NO',
                $student->isVerified('Mobile Number 2 Verified') ? 'YES' : 'NO',
                $student->isVerified('NIC Verified') ? 'YES' : 'NO',
                $student->isVerified('Address Verified') ? 'YES' : 'NO',
                $student->additional_notes ?? '-',
                $student->previous_institute_details ?? '-',
                $student->createUser->name ?? '-',
                $student->updateUser->name ?? '-',
                $student->created_at ?? '-',
                $student->updated_at ?? '-'
            ];
        }

        // Generate filename
        $filename = "Students_" . now()->format('Y-m-d_H-i-s') . ".csv";

        // Create response headers
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // Create callback for streaming
        $callback = function () use ($headings, $data) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 CSV (needed for Excel to display special characters correctly)
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write headers
            fputcsv($file, $headings);

            // Write data
            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
    //Student Attendence Methods



    // Student Intake Management Methods
    public function studentAddIntake()
    {
        $students = Student::with('verifications')->get();
        $branches = Branch::all();
        $intakes = Intake::with('branch')->get();
        $studentCategories = StudentCategory::all();

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

        return view('pages.student.student-info.student-add-intake', compact('students', 'branches', 'intakes', 'studentCategories', 'studentIntakes'));
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
        $studentCategories = StudentCategory::all();

        return view('pages.student.student-info.student-add-intake', compact(
            'students',
            'branches',
            'intakes',
            'courses',
            'studentCategories'
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
            'change_course',
            'studentCategory',
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

            ->addColumn('category', fn($row) => $row->studentCategory->category_name ?? $row->student->studentCategory->category_name ?? '-')

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

            // "if genarated certificate cant be return or change course or
            // change status" — a small lock badge is appended to the status
            // pill so it's visible at a glance which rows are locked, before
            // the user even opens the row's action menu (which also hides
            // the locked actions server-side below).
            ->addColumn('status', function ($row) {
                $badge = '<span class="badge badge-info">' . ucfirst($row->status) . '</span>';
                if ($this->studentCourseHasCertificate($row)) {
                    $badge .= ' <span class="cert-lock-badge" title="Certificate already generated — locked"><i class="fa-solid fa-lock"></i> Certified</span>';
                }
                return $badge;
            })

            ->addColumn('note', fn($row) => $row->notes)

            // ======================
            // ACTIONS COLUMN (Section 26: modernized + permission-gated;
            // this round adds a certificate lock — "if genarated certificate
            // cant be return or change course or change status. only can be
            // change student category only")
            // ======================

            ->addColumn('actions', function ($row) {
                $user = Auth::user();
                $items = [];
                $certLocked = $this->studentCourseHasCertificate($row);

                if ($user->hasPermission('student.add-intake.update-category')) {
                    $items[] = '<a class="dropdown-item" href="javascript:void(0);"
                                onclick="openEditCategoryModal(' . $row->id . ', ' . $row->student_category_id . '); return false;">
                                <i class="fa-solid fa-layer-group text-warning"></i> Edit Category
                            </a>';
                }

                if ($certLocked) {
                    // Certificate already generated for this enrollment — status,
                    // course and delete are all frozen. Category is still editable
                    // (handled above, unconditionally).
                    $items[] = '<span class="dropdown-item-text text-muted small">
                                <i class="fa-solid fa-lock"></i> Certificate generated — locked
                            </span>';

                    if (empty($items)) {
                        return '<span class="text-muted">&mdash;</span>';
                    }

                    return '
                <div class="dropdown modern-row-actions">
                    <button class="btn-row-action" type="button" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        ' . implode('', $items) . '
                    </div>
                </div>';
                }

                if ($row->status == 'pending') {
                    if ($user->hasPermission('student.add-intake.update-status')) {
                        $items[] = '<a class="dropdown-item" href="#" onclick="changeStatus(' . $row->id . ', \'start\')">
                                    <i class="fa-solid fa-play text-info"></i> Start
                                </a>';
                    }

                    if ($user->hasPermission('student.add-intake.destroy')) {
                        $items[] = '<a class="dropdown-item text-danger" href="#" onclick="deleteIntake(' . $row->id . ')">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>';
                    }

                    if ($user->hasPermission('student.add-intake-with-courses.change-course')) {
                        $items[] = '<a class="dropdown-item" href="#"
                                    onclick="openChangeCourseModal(' . $row->id . ', ' . $row->course_id . ', ' . $row->intake_id . ')">
                                    <i class="fa-solid fa-shuffle text-primary"></i> Change Course
                                </a>';
                    }
                }

                if ($row->status == 'start') {
                    if ($user->hasPermission('student.add-intake.update-status')) {
                        $items[] = '<a class="dropdown-item" href="#" onclick="changeStatus(' . $row->id . ', \'return\')">
                                    <i class="fa-solid fa-arrow-rotate-left text-warning"></i> Return
                                </a>';
                        $items[] = '<a class="dropdown-item" href="#" onclick="changeStatus(' . $row->id . ', \'complete\')">
                                    <i class="fa-solid fa-check text-success"></i> Complete
                                </a>';
                    }

                    if ($user->hasPermission('student.add-intake-with-courses.change-course')) {
                        $items[] = '<a class="dropdown-item" href="#"
                                    onclick="openChangeCourseModal(' . $row->id . ', ' . $row->course_id . ', ' . $row->intake_id . ')">
                                    <i class="fa-solid fa-shuffle text-primary"></i> Change Course
                                </a>';
                    }
                }

                if ($row->status == 'return') {
                    if ($user->hasPermission('student.add-intake.update-status')) {
                        $items[] = '<a class="dropdown-item" href="#" onclick="changeStatus(' . $row->id . ', \'start\')">
                                    <i class="fa-solid fa-rotate-left text-info"></i> Restart
                                </a>';
                        $items[] = '<a class="dropdown-item" href="#" onclick="changeStatus(' . $row->id . ', \'complete\')">
                                    <i class="fa-solid fa-check text-success"></i> Complete
                                </a>';
                    }
                }

                if (empty($items)) {
                    return '<span class="text-muted">&mdash;</span>';
                }

                return '
            <div class="dropdown modern-row-actions">
                <button class="btn-row-action" type="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    ' . implode('', $items) . '
                </div>
            </div>';
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


    public function updateStudentCategory(Request $request, $id)
    {
        $request->validate([
            'student_category_id' => 'required|exists:student_categories,id',
        ]);

        try {
            // "only can be change student category only" — category stays
            // editable even after a certificate has been generated, so no
            // lock check here (deliberately, unlike changeCourse/updateStatus/
            // destroy below).
            $studentCourse = StudentCourse::findOrFail($id);
            $studentCourse->student_category_id = $request->student_category_id;
            $studentCourse->updated_by = Auth::id();
            $studentCourse->save();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // "same student same intake same course can't be create 2 time / if add
    // same course another intake ... want to show now active one for that
    // course and ask ... if ok then old one return and add new one / if
    // genarated certificate for some course can't be again add" — all three
    // rules are enforced here, server-side, regardless of what the browser
    // already checked via checkCourseConflicts() below (that endpoint exists
    // purely so the modal can warn the user *before* they submit; it is not
    // trusted on its own).
    public function storeStudentIntakeWithCourses(Request $request)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|exists:students,id',
            'branch_id' => 'required|exists:branches,id',
            'intake_id' => 'required|exists:intakes,id',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
            'notes' => 'nullable|string',
            // Course ids the user has explicitly confirmed, in the modal,
            // should have their existing (different-intake) enrollment
            // returned so this new one can be added.
            'confirm_return_course_ids' => 'nullable|array',
            'confirm_return_course_ids.*' => 'nullable|integer',
        ]);

        $studentId = $validatedData['student_id'];
        $intakeId = $validatedData['intake_id'];
        $confirmReturnIds = collect($request->input('confirm_return_course_ids', []))
            ->filter() // drops null/empty entries from the always-present hidden input
            ->map(fn($v) => (int) $v)
            ->all();

        $toReturn = [];
        $blocked = [];

        foreach ($validatedData['course_ids'] as $courseId) {
            // BUG FIX: course_ids arrives from the <select> as a STRING
            // ("12"), while $confirmReturnIds below is built as ints. The
            // in_array(..., true) check further down uses strict ===
            // comparison, so "12" === 12 was always false — the "Yes,
            // return the old enrollment" checkbox was silently ignored no
            // matter what the user ticked, and every confirmed submission
            // still got rejected with the "Confirm in the form..." error.
            // Casting once here keeps $courseId an int for the rest of this
            // loop (the confirm check, StudentCourse::create() below, etc).
            $courseId = (int) $courseId;
            $course = Course::find($courseId);
            $courseName = $course->name ?? ('Course #' . $courseId);

            // Rule 1: exact duplicate — same student + same intake + same course.
            $duplicate = StudentCourse::where('student_id', $studentId)
                ->where('intake_id', $intakeId)
                ->where('course_id', $courseId)
                ->exists();

            if ($duplicate) {
                $blocked[] = "Student is already enrolled in \"{$courseName}\" for this intake.";
                continue;
            }

            // Rule 3 checked before rule 2's "return old" flow, so a
            // certificate always wins even if the user already ticked the
            // confirm box before the page had a chance to tell them.
            if ($this->courseHasCertificateForStudent($studentId, $courseId)) {
                $blocked[] = "A certificate has already been generated for \"{$courseName}\" for this student — this course can't be added again.";
                continue;
            }

            // Rule 2: student already has a live (non-returned) enrollment in
            // this same course under a DIFFERENT intake.
            $activeElsewhere = StudentCourse::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->where('intake_id', '!=', $intakeId)
                ->where('status', '!=', 'return')
                ->first();

            if ($activeElsewhere) {
                if (!in_array($courseId, $confirmReturnIds, true)) {
                    $blocked[] = "Student already has an active enrollment in \"{$courseName}\" under another intake. Confirm in the form to return that one and add this new enrollment.";
                    continue;
                }

                $toReturn[] = $activeElsewhere;
            }
        }

        if (!empty($blocked)) {
            return back()->withErrors(['course_ids' => $blocked])->withInput();
        }

        DB::beginTransaction();

        $student = Student::find($studentId);
        try {
            foreach ($toReturn as $old) {
                $old->status = 'return';
                $old->updated_by = Auth::id();
                $old->save();
            }

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
                    'student_category_id' => $student->student_category_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            $message = 'Student enrolled in intake and courses successfully.';
            if (!empty($toReturn)) {
                $message .= ' ' . count($toReturn) . ' previous enrollment(s) in the same course were marked as returned.';
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to enroll student. Please try again.']);
        }
    }

    // Read-only pre-flight check the Add Student Course modal calls (via
    // AJAX, on course selection) so it can warn the user — or ask them to
    // confirm returning an old enrollment — BEFORE they hit Assign. The
    // real, authoritative enforcement of the same three rules lives in
    // storeStudentIntakeWithCourses() above; this endpoint is purely UX.
    public function checkCourseConflicts(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'intake_id' => 'required|exists:intakes,id',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $studentId = $validated['student_id'];
        $intakeId = $validated['intake_id'];
        $results = [];

        foreach ($validated['course_ids'] as $courseId) {
            $course = Course::find($courseId);
            $courseName = $course->name ?? ('Course #' . $courseId);

            $duplicate = StudentCourse::where('student_id', $studentId)
                ->where('intake_id', $intakeId)
                ->where('course_id', $courseId)
                ->exists();

            if ($duplicate) {
                $results[] = [
                    'course_id' => $courseId,
                    'course_name' => $courseName,
                    'status' => 'duplicate',
                    'message' => "Student is already enrolled in \"{$courseName}\" for this intake.",
                ];
                continue;
            }

            if ($this->courseHasCertificateForStudent($studentId, $courseId)) {
                $results[] = [
                    'course_id' => $courseId,
                    'course_name' => $courseName,
                    'status' => 'certificate_locked',
                    'message' => "A certificate has already been generated for \"{$courseName}\" for this student — this course can't be added again.",
                ];
                continue;
            }

            $activeElsewhere = StudentCourse::with('intake')
                ->where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->where('intake_id', '!=', $intakeId)
                ->where('status', '!=', 'return')
                ->first();

            if ($activeElsewhere) {
                $oldIntakeName = optional($activeElsewhere->intake)->name ?? 'another intake';
                $results[] = [
                    'course_id' => $courseId,
                    'course_name' => $courseName,
                    'status' => 'needs_confirmation',
                    'old_student_course_id' => $activeElsewhere->id,
                    'old_intake_name' => $oldIntakeName,
                    'old_status' => $activeElsewhere->status,
                    'message' => "Student already has an active enrollment in \"{$courseName}\" under intake \"{$oldIntakeName}\" (status: " . ucfirst($activeElsewhere->status) . '). Return that one and add this new enrollment instead?',
                ];
                continue;
            }

            $results[] = [
                'course_id' => $courseId,
                'course_name' => $courseName,
                'status' => 'ok',
            ];
        }

        return response()->json(['results' => $results]);
    }


    public function updateStatus(Request $request, $id)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'status' => 'required|in:pending,start,return,complete',
            ]);

            $studentCourse = StudentCourse::find($id);
            if (!$studentCourse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enrollment not found.'
                ], 404);
            }

            // "if genarated certificate cant be return or change course or
            // change status"
            if ($this->studentCourseHasCertificate($studentCourse)) {
                return response()->json([
                    'success' => false,
                    'message' => 'A certificate has already been generated for this enrollment — its status can no longer be changed.',
                ], 422);
            }

            $studentCourse->status = $validatedData['status'];
            $studentCourse->updated_at = now();
            $studentCourse->save();

            // Keep the student_courses record's own status in sync (the
            // model above IS the student_courses row already, so this used
            // to be a redundant second query against the same table under a
            // different name — kept as a plain save() instead).

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
        $studentCourse = StudentCourse::find($id);

        if (!$studentCourse) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found.',
            ], 404);
        }

        // "if genarated certificate cant be return or change course or
        // change status" — delete is included under this same lock.
        if ($this->studentCourseHasCertificate($studentCourse)) {
            return response()->json([
                'success' => false,
                'message' => 'A certificate has already been generated for this enrollment — it can no longer be removed.',
            ], 422);
        }

        $studentCourse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Intake enrollment removed successfully.',
        ]);
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

    // "if genarated certificate for some course can't be again add" / "if
    // genarated certificate cant be return or change course or change
    // status" — both rules boil down to "has a Certificate row ever been
    // generated for this student in this course". Certificate belongs to
    // StudentAssessment, and StudentAssessment belongs to StudentCourse via
    // student_course_id (confirmed by the existing
    // StudentAssessmentController — e.g. its store() sets
    // 'student_course_id' => $validated['student_course_id'] and its
    // existingForCourse() looks assessments up the same way), so this reads
    // that same chain rather than guessing at a different one.
    private function studentCourseHasCertificate(StudentCourse $studentCourse): bool
    {
        return Certificate::whereHas('studentAssessment', function ($q) use ($studentCourse) {
            $q->where('student_course_id', $studentCourse->id);
        })->exists();
    }

    // Same check as above, but keyed by student + course rather than a
    // specific StudentCourse row — used when deciding whether a *new*
    // enrollment (which doesn't have a student_courses row yet) is allowed
    // to be created at all for that course.
    private function courseHasCertificateForStudent(int $studentId, int $courseId): bool
    {
        return Certificate::whereHas('studentAssessment', function ($q) use ($studentId, $courseId) {
            $q->where('student_id', $studentId)
                ->whereHas('studentCourse', function ($q2) use ($courseId) {
                    $q2->where('course_id', $courseId);
                });
        })->exists();
    }

    // "if student genarated any one cerificate that stundet Verify Submit
    // can be do that permition only" — same relation chain as the two
    // helpers above, but NOT scoped to one course: this is true the moment
    // the student has a certificate generated for ANY of their courses,
    // since Verify Submit edits core student fields (name/NIC/address/
    // phone) that an already-issued certificate depends on.
    private function studentHasAnyCertificate(int $studentId): bool
    {
        return Certificate::whereHas('studentAssessment', function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        })->exists();
    }


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

    public function getStudentsByBranch($branchId)
    {
        try {
            $students = StudentCourse::where('branch_id', $branchId)
                ->select('student_id')
                ->distinct()
                ->with('student')
                ->get()
                ->pluck('student');

            $formattedStudents = $students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'admission_number' => $student->admission_number,
                    'student_name' => $student->student_name,
                ];
            });

            return response()->json($formattedStudents);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }

    public function getStudentFeeData(Request $request)
    {
        try {
            $student_id = $request->student_id;
            $branch_id = $request->branch_id;
            $course_id = $request->course_id;

            $student = Student::find($student_id);
            if (!$student) {
                return response()->json([
                    'student_name' => 'Student not found',
                    'total_invoices' => 0,
                    'total_fees' => 0,
                    'total_paid' => 0,
                    'total_due' => 0,
                    'fee_invoices' => []
                ]);
            }

            $feeInvoices = FeeInvoices::with(['paidCourses'])
                ->where('student_id', $student_id)
                ->where('branch_id', $branch_id)
                ->whereHas('paidCourses', function ($query) use ($course_id) {
                    $query->where('course_id', $course_id);
                })
                ->get();

            $totalFees = $feeInvoices->sum('total_amount');
            $totalPaid = $feeInvoices->sum('paid_amount');
            $totalDue = $totalFees - $totalPaid;

            $formattedInvoices = [];
            foreach ($feeInvoices as $invoice) {
                foreach ($invoice->invoice_item as $item) {
                    $formattedInvoices[] = [
                        'fee_type_name' => $item->feeType->name ?? 'N/A',
                        'amount' => $item->amount ?? 0,
                        'paid_amount' => $item->paid_amount ?? 0,
                    ];
                }
            }

            return response()->json([
                'total_fees' => $totalFees,
                'total_paid' => $totalPaid,
                'total_due' => $totalDue,
                'fee_invoices' => $formattedInvoices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch fee data',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentDetails($studentId)
    {
        try {
            $student = Student::find($studentId);

            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }

            $studentCourses = StudentCourse::with(['course', 'intake', 'branch'])
                ->where('student_id', $studentId)
                ->get();

            $branches = $studentCourses->pluck('branch')->unique('id')->values();
            $intakes  = $studentCourses->pluck('intake')->unique('id')->values();
            $courses  = $studentCourses->pluck('course')->unique('id')->values();

            return response()->json([
                'branches' => $branches,
                'branch_id' => $student->branch ? $student->branch->id : null,
                'intakes' => $intakes,
                'courses' => $courses,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }



    public function verify($id)
    {
        $student = Student::findOrFail($id);

        // "then want alert there has certificated genarated pleace check" —
        // computed here (not in the view) so the same flag also drives which
        // permission verifySubmit() below requires, keeping the view and the
        // authoritative server-side check reading from one definition.
        $hasCertificate = $this->studentHasAnyCertificate($student->id);

        return view('pages.student.student-info.verify', compact('student', 'hasCertificate'));
    }



    public function verifySubmit(Request $request, $studentId)
    {
        $request->validate([
            'verification_items' => 'required|array',
            'signature_data' => 'required|string',
        ]);

        $student = Student::findOrFail($studentId);

        // "if student genarated any one cerificate that stundet Verify
        // Submit can be do that permition only" — the plain add-student.
        // verify.submit permission (already checked by route middleware /
        // the button-level check in verify.blade.php) is NOT enough once
        // this student has any certificate generated; a separate, stricter
        // permission takes over. Enforced here authoritatively, same
        // defense-in-depth pattern as studentCourseHasCertificate() etc.
        // above — the blade hides the form for this case too, but this
        // check does not trust that.
        if ($this->studentHasAnyCertificate($student->id) && !Auth::user()->hasPermission('add-student.verify.submit.certified')) {
            return back()->withErrors([
                'verification_items' => 'This student already has a certificate generated. You do not have permission to submit verification for a student in this state — please check.',
            ]);
        }

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
                'updated_by'        => Auth::id(),
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

            // "if genarated certificate cant be return or change course or
            // change status"
            if ($this->studentCourseHasCertificate($studentCourse)) {
                return response()->json([
                    'success' => false,
                    'message' => 'A certificate has already been generated for this enrollment — the course can no longer be changed.'
                ]);
            }

            // OPTIONAL: Prevent duplicate same course
            if ($studentCourse->course_id == $request->course_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This course is already assigned.'
                ]);
            }

            // "course change : that time want check invoice has to change new
            // course. if not invoice do show alert" — the student must already
            // have a (not returned) course invoice for the NEW course.
            $hasNewCourseInvoice = DB::table('fee_invoices')
                ->join('paid_courses', 'paid_courses.invoice_id', '=', 'fee_invoices.id')
                ->where('fee_invoices.student_id', $studentCourse->student_id)
                ->where('fee_invoices.invoice_type', 'course')
                ->where('fee_invoices.payment_status', '!=', 'returned')
                ->where('paid_courses.course_id', $request->course_id)
                ->exists();

            if (!$hasNewCourseInvoice) {
                $newCourseName = optional(\App\Models\Course::find($request->course_id))->name ?? 'the selected course';

                return response()->json([
                    'success' => false,
                    'code' => 'no_invoice',
                    'message' => "No invoice found for {$newCourseName}. Create the course invoice for this student first, then change the course."
                ]);
            }

            $studentCourse->change_course_id = $request->course_id;
            $studentCourse->updated_by = Auth::id();
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
        $student = Student::with(['branch', 'gender', 'studentCategory', 'bloodGroup', 'courses', 'intakes', 'courseWithIntake'])->findOrFail($id);

        // Fetch payment history
        $payments = FeeInvoices::where('student_id', $id)->with(['invoice_item.fee_type'])->get();

        // Fetch detailed course information for the student
        $courses = $student->courses()->with(['intakes'])->get();

        // Fetch detailed intake information for the student
        $intakes = $student->intakes()->with(['branch', 'courses'])->get();

        return view('pages.student.student-info.student-view', compact('student', 'payments', 'courses', 'intakes'));
    }

    public function disabledStudents()
    {
        $students = Student::with([
            'branch',
            'gender',
            'studentCategory',
        ])->select([
            'id',
            'admission_number',
            'branch_id',
            'student_category_id',
            'gender_id',
            'student_name',
            'date_of_birth',
            'phone_number',
        ])->inactive()->orderBy('id', 'desc')->get();

        $branches = Branch::all();

        return view('pages.student.student-info.disabled-students', compact('students', 'branches'));
    }

    public function filterStudentList(Request $request)
    {
        $branchId = $request->input('branch_id');
        $intakeId = $request->input('intake_id');
        $courseId = $request->input('course_id');

        $query = Student::with([
            'branch',
            'gender',
            'studentCategory',
            'verifications',
            'district',
            'createUser',
            'updateUser'
        ])->select([
            'id',
            'admission_number',
            'branch_id',
            'student_category_id',
            'gender_id',
            'student_name',
            'date_of_birth',
            'phone_number',
            'national_id_number',
            'permanent_address',
        ]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($intakeId) {
            $query->whereHas('intakes', function ($q) use ($intakeId) {
                $q->where('intakes.id', $intakeId);
            });
        }

        if ($courseId) {
            $query->whereHas('courses', function ($q) use ($courseId) {
                $q->where('courses.id', $courseId);
            });
        }

        $branches = Branch::all();
        $students = $query->active()->orderBy('id', 'desc')->get();

        return view('pages.student.student-info.student-list', compact('students', 'branches'));
    }

    public function filter_disabled_StudentList(Request $request)
    {
        $branchId = $request->input('branch_id');
        $intakeId = $request->input('intake_id');
        $courseId = $request->input('course_id');

        $query = Student::with([
            'branch',
            'gender',
            'studentCategory',
        ])->select([
            'id',
            'admission_number',
            'branch_id',
            'student_category_id',
            'gender_id',
            'student_name',
            'date_of_birth',
            'phone_number',
        ]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($intakeId) {
            $query->whereHas('intakes', function ($q) use ($intakeId) {
                $q->where('intakes.id', $intakeId);
            });
        }

        if ($courseId) {
            $query->whereHas('courses', function ($q) use ($courseId) {
                $q->where('courses.id', $courseId);
            });
        }

        $branches = Branch::all();
        $students = $query->inactive()->orderBy('id', 'desc')->get();

        return view('pages.student.student-info.disabled-students', compact('students', 'branches'));
    }

    public function updateStudetStatus(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::findOrFail($request->student_id);
        $student->update([
            'is_active' => !$student->is_active
        ]);

        return redirect()->back()->with('message-success', 'Student status update successfully!');
    }


    public function verificationHistory($id)
    {
        $verifications = StudentVerification::with('user')
            ->where('student_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($verifications);
    }
}