<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Content;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Student;
use App\Models\Supplier;
use App\Models\ReturnFee;
use App\Models\VideoList;
use App\Models\ItemReceive;
use App\Models\ItemInventory;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\ShareContentList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch; // Keep this single import for Branch
use App\Models\FeeInvoices;
use App\Models\StudentCourse;
use App\Models\StudentVerification;
use Yajra\DataTables\DataTables;

class HomeController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function login()
    {
        return view('auth.login');
    }

    // for display dashboard
    public function dashboard()
    {
        $user = Auth::user();

        // Fetch counts
        $studentsCount = Student::count();
        $teachersCount = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Teacher');
        })->count();
        $labourCount = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Labour');
        })->count();
        $adminCount = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Admin');
        })->count();

        // Fetch financial data using existing models
        $totalIncome = FeeInvoices::sum('paid_amount');
        $totalExpenses = 0; // No expense model, set to 0
        $totalProfit = $totalIncome - $totalExpenses;
        $totalRevenue = $totalIncome;

        // Fetch notices using Content model
        $notices = Content::latest()->take(5)->get();

        // Fetch events using Course model
        $events = Course::all();

        // Fetch branches with intakes and courses
        $branches = Branch::with(['intakes.courses'])->get();

        // Fetch todos - using a simple array for now
        $todos = []; // Or create a model later

        return view('pages.dashboard.dashboard', compact(
            'user',
            'studentsCount',
            'teachersCount',
            'labourCount',
            'adminCount',
            'totalIncome',
            'totalExpenses',
            'totalProfit',
            'totalRevenue',
            'notices',
            'events',
            'branches',
            'todos'
        ));
    }




    // public function subjects()
    // {
    //     return view('pages.administration.academic.subject');
    // }




    public function uploadContent()
    {
        return view('pages.administration.study-material.upload-content');
    }

    public function studyMaterialDownload()
    {
        return view('pages.administration.study-material.study-material-download');
    }

    public function otherDownload()
    {
        return view('pages.administration.study-material.other-download');
    }

    public function sharedContentList()
    {
        $sharedContents = ShareContentList::with(['role', 'users'])
            ->orderBy('id')
            // ->paginate(10)
            ->get();

        return view('pages.administration.download-center.shared-content-list', compact('sharedContents'));
    }

    public function videoList(Request $request)
    {
        $intakes = Intake::all();
        $videosQuery = VideoList::with(['intake:id,name', 'courses:id,name'])->orderByDesc('id');

        if ($request->filled('title')) {
            $title = $request->input('title');
            $videosQuery->where('title', 'like', "%{$title}%");
        }

        $videos = $videosQuery->get();
        return view('pages.administration.download-center.video-list', compact('intakes', 'videos'));
    }

    public function videoListStore(Request $request)
    {
        $validated = $request->validate([
            'intake_id' => 'required|exists:intakes,id',
            'title' => 'required|string|max:255',
            'video_link' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'integer|exists:courses,id',
        ]);

        try {
            // Create VideoList entry
            $video = VideoList::create([
                'intake_id' => $validated['intake_id'],
                'title' => $validated['title'],
                'video_link' => $validated['video_link'],
                'description' => $validated['description'] ?? null,
            ]);

            // Attach related courses
            $video->courses()->sync($validated['course_ids']);

            return response()->json([
                'status' => true,
                'message' => 'Video saved successfully',
                'id' => $video->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to save video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function videoListDestroy($id)
    {
        $video = VideoList::findOrFail($id);
        $video->delete();
        return redirect()->route('video-list')->with('success', 'Video deleted successfully');
    }

    /**
     * Update an existing video and sync its related courses.
     * Handles AJAX PUT from the dedicated Edit Video modal.
     */
    public function videoListUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'intake_id' => 'required|exists:intakes,id',
            'title' => 'required|string|max:255',
            'video_link' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'integer|exists:courses,id',
        ]);

        try {
            $video = VideoList::findOrFail($id);
            $video->update([
                'intake_id' => $validated['intake_id'],
                'title' => $validated['title'],
                'video_link' => $validated['video_link'],
                'description' => $validated['description'] ?? null,
            ]);

            $video->courses()->sync($validated['course_ids']);

            return response()->json([
                'status' => true,
                'message' => 'Video updated successfully',
                'id' => $video->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update video',
                'error' => $e->getMessage(),
            ], 500);
        }
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
                return '
                <div class="dropdown">
                    <button class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
                        Action
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" target="_blank" href="' . route('student-details', $s->id) . '">View</a>
                        <a class="dropdown-item" href="' . route('add-student.edit', $s->id) . '">Edit</a>
                        <a class="dropdown-item" target="_blank" href="' . route('add-student.verify', $s->id) . '">Verification</a>
                        <a class="dropdown-item openVerificationModal" href="#" data-id="' . $s->id . '">Verification Check</a>
                        <a class="dropdown-item" data-toggle="modal"
                           data-student-id="' . $s->id . '" data-target="#statusUpdateModal">
                           Disable
                        </a>
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


    // App\Http\Controllers\StudentController.php

    public function verificationHistory($id)
    {
        $verifications = StudentVerification::with('user')
            ->where('student_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($verifications);
    }


    public function getIntakes($branchId)
    {
        $intakes = Intake::where('branch_id', $branchId)->get(['id', 'name']);
        return response()->json($intakes);
    }



    // Get courses for an intake
    public function getCourses($intakeId)
    {
        $intake = Intake::find($intakeId);

        if (!$intake) {
            return response()->json([]);
        }

        $courses = $intake->courses()->get(['courses.id', 'courses.name']); // related courses
        return response()->json($courses);
    }

    public function getCoursesByIntake($intakeId)
    {
        try {
            $intake = Intake::find($intakeId);

            if (!$intake) {
                return response()->json([
                    'success' => false,
                    'error' => 'Intake not found',
                    'intake_id' => $intakeId
                ], 404);
            }

            $courses = $intake->courses()->get(['courses.id', 'courses.name']);

            return response()->json([
                'success' => true,
                'courses' => $courses,
                'intake_id' => $intakeId,
                'count' => $courses->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'intake_id' => $intakeId
            ], 500);
        }
    }

    public function getIntakesByBranch($branchId)
    {
        try {
            $intakes = Intake::where('branch_id', $branchId)->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'intakes' => $intakes,
                'branch_id' => $branchId,
                'count' => $intakes->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'branch_id' => $branchId
            ], 500);
        }
    }

    public function getCoursesByBranch($branchId)
    {
        try {
            $intakes = Intake::where('branch_id', $branchId)->pluck('id');
            $courses = Course::whereHas('intakes', function ($q) use ($intakes) {
                $q->whereIn('intake_id', $intakes);
            })->get(['id', 'name', 'price']);

            return response()->json([
                'success' => true,
                'courses' => $courses,
                'branch_id' => $branchId,
                'count' => $courses->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'branch_id' => $branchId
            ], 500);
        }
    }

    public function getCoursesByStudent($studentId)
    {
        try {
            $student = Student::find($studentId);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'error' => 'Student not found',
                    'student_id' => $studentId
                ], 404);
            }

            // Step 1 — Get latest record ID for each course
            $latest = StudentCourse::where('student_id', $studentId)
                ->selectRaw('MAX(id) as id')
                ->groupBy('course_id')
                ->pluck('id'); // gives [12, 18, 25] (latest rows)

            // Step 2 — Load only these records with relations
            $records = StudentCourse::with(['course', 'intake','change_course'])
                ->whereIn('id', $latest)
                ->get();

            // Step 3 — Format response
            $courses = $records->map(function ($item) {
                return [
                    'course_id' => $item->course->id,
                    'course_name' => $item->course->name,
                    'course_price' => $item->course->price,
                    'intake_id' => $item->intake?->id,
                    'intake_name' => $item->intake?->name,
                    'has_nvq_payment' => $item->course->has_nvq_payment,
                    'nvq_amount' => $item->course->nvq_amount,
                    'change_course' => $item->change_course?->name ?? '',
                    'status' => $item->status

                ];
            });

            return response()->json([
                'success' => true,
                'courses' => $courses,
                'count' => $courses->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'student_id' => $studentId
            ], 500);
        }
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
            // join with courses via course_intake
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
            // join with courses via course_intake
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

    public function disabledStudents()
    {
        $students = $students = Student::with([
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
    public function studentAttendance()
    {
        $courses = Course::all();
        $intakes = Intake::all();
        return view('pages.student.student-info.student-attendance', compact('courses', 'intakes'));
    }
    public function studentExport()
    {
        return view('pages.student.student-info.student-export');
    }

    // public function feeInvoice()
    // {
    //     return view('pages.student.fees.fee-invoice');
    // }

    // public function addInvoice()
    // {
    //     return view('pages.student.fees.add-invoice');
    // }

    public function feeInvoiceSetting()
    {
        return view('pages.student.fees.fee-invoice-setting');
    }

    public function feeReturn(Request $request)
    {
        $branches = Branch::all();
        $coursess = Course::all();
        $intakes = Intake::all();

        $query = ReturnFee::with(['student', 'role', 'invoice']);

        if ($request->filled('branch')) {
            $branchId = $request->input('branch');
            $query->whereHas('student', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        if ($request->filled('intake')) {
            $intakeId = $request->input('intake');
            $query->whereHas('student.intakes', function ($q) use ($intakeId) {
                $q->where('intakes.id', $intakeId);
            });
        }

        if ($request->filled('course')) {
            $courseId = $request->input('course');
            $query->whereHas('student.courses', function ($q) use ($courseId) {
                $q->where('courses.id', $courseId);
            });
        }

        $returnDetails = $query->orderBy('created_at', 'desc')->get();

        $branch_id = $request->input('branch');
        $intake_id = $request->input('intake');
        $course_id = $request->input('course');

        return view('pages.student.fees.fee-return', compact('returnDetails', 'coursess', 'intakes', 'branches', 'branch_id', 'intake_id', 'course_id'));
    }

    public function feeInvoice()
    {
        $roles = Role::all();

        return view('pages.student.fees.fee-invoice', compact('roles'));
    }

    public function invoiceDatatable()
    {
        $query = DB::table('fee_invoices')
            ->leftJoin('students', 'students.id', '=', 'fee_invoices.student_id')
            ->leftJoin('student_categories', 'student_categories.id', '=', 'students.student_category_id' )
            ->leftJoin('branches', 'branches.id', '=', 'fee_invoices.branch_id')
            ->leftJoin('invoice_items', 'invoice_items.invoice_id', '=', 'fee_invoices.id')
            ->leftJoin('fee_types', 'fee_types.id', '=', 'invoice_items.fee_type_id')
            ->leftJoin('paid_courses', 'paid_courses.invoice_id', '=', 'fee_invoices.id')
            ->leftJoin('courses', 'courses.id', '=', 'paid_courses.course_id')
            // ->where('fee_invoices.payment_status', '!=', 'returned')
            ->select([
                'fee_invoices.*',
                'students.student_name',
                'students.admission_number',
                'student_categories.category_name',
                'branches.name as branch_name',
                'fee_types.name as fee_type_name',
                'courses.name as course_name',
            ])
            ->orderBy('fee_invoices.id', 'desc');
    
        return DataTables::of($query)
            ->addIndexColumn()

            ->orderColumn('fee_name', function ($query, $order) {
                $query->orderByRaw("CASE WHEN fee_invoices.invoice_type = 'fee' THEN COALESCE(fee_types.name, '') ELSE CONCAT(COALESCE(fee_invoices.invoice_type, ''), CASE WHEN courses.name IS NOT NULL THEN CONCAT(' - ', courses.name) ELSE '' END) END {$order}");
            })

            ->orderColumn('balance', function ($query, $order) {
                $query->orderByRaw("(fee_invoices.total_amount - fee_invoices.paid_amount) {$order}");
            })

            ->orderColumn('payment_date', function ($query, $order) {
                $query->orderBy('fee_invoices.create_date', $order);
            })

            ->orderColumn('due_date', function ($query, $order) {
                $query->orderBy('fee_invoices.due_date', $order);
            })
    
            ->editColumn('fee_name', function ($row) {
                return $row->invoice_type === 'fee'
                    ? ($row->fee_type_name ?? '-')
                    : ($row->invoice_type . ($row->course_name ? ' - ' . ($row->course_name ?? '-') : '') );
            })
    
            ->addColumn('balance', function ($row) {
                return number_format($row->total_amount - $row->paid_amount, 2);
            })
    
            ->addColumn('payment_date', fn($row) =>
                \Carbon\Carbon::parse($row->create_date)->format('m/d/Y')
            )
    
            ->addColumn('due_date', fn($row) =>
                \Carbon\Carbon::parse($row->due_date)->format('m/d/Y')
            )
    
            ->filter(function ($query) {
                $keyword = request('search.value');
    
                if ($keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('fee_invoices.main_invoice_number', 'like', "%{$keyword}%")
                          ->orWhere('students.student_name', 'like', "%{$keyword}%")
                          ->orWhere('students.admission_number', 'like', "%{$keyword}%")
                          ->orWhere('student_categories.category_name', 'like', "%{$keyword}%")
                          ->orWhere('branches.name', 'like', "%{$keyword}%")
                          ->orWhere('fee_types.name', 'like', "%{$keyword}%")
                          ->orWhere('courses.name', 'like', "%{$keyword}%")
                          ->orWhere('fee_invoices.payment_status', 'like', "%{$keyword}%");
                    });
                }
            })
            ->addColumn('action', function ($row) {

                return '
                <div class="dropdown">
                    <button class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
                        Action
                    </button>
                    <div class="dropdown-menu">
            
                        <a class="dropdown-item"
                           data-toggle="modal"
                           data-target="#viewPaymentHistoryModal"
                           data-invoice-id="' . $row->id . '">
                           View Payment
                        </a>
            
                        <a class="dropdown-item"
                           data-toggle="modal"
                           data-target="#makeInvoiceModal"
                           data-student-id="' . ($row->student_id ?? '') . '"
                           data-invoice-id="' . $row->id . '">
                           Invoice
                        </a>

                                <a class="dropdown-item"
                                    data-toggle="modal"
                                    data-target="#returnFeeModal"
                                    data-invoice-id="' . $row->id . '"
                                    data-student-id="' . ($row->student_id ?? '') . '"
                                    data-student-name="' . e($row->student_name ?? '') . '"
                                    data-admission-number="' . e($row->admission_number ?? '') . '"
                                    data-paid-amount="' . ($row->paid_amount ?? 0) . '">
                                    Return
                                </a>
            
                        <a class="dropdown-item"
                           href="' . route('fees.invoice-print', $row->id) . '"
                           target="_blank">
                           Re-Print
                        </a>
            
                    </div>
                </div>';
            })
            ->rawColumns(['payment_status', 'action'])
            ->make(true);
    }
// public function invoiceDatatable()
// {
//     $query = DB::table('fee_invoices')
//         ->leftJoin('students', 'students.id', '=', 'fee_invoices.student_id')
//         ->leftJoin('student_categories', 'student_categories.id', '=', 'students.student_category_id')
//         ->leftJoin('branches', 'branches.id', '=', 'fee_invoices.branch_id')
//         ->leftJoin('invoice_items', 'invoice_items.invoice_id', '=', 'fee_invoices.id')
//         ->leftJoin('fee_types', 'fee_types.id', '=', 'invoice_items.fee_type_id')
//         ->leftJoin('paid_courses', 'paid_courses.invoice_id', '=', 'fee_invoices.id')
//         ->leftJoin('courses', 'courses.id', '=', 'paid_courses.course_id')
//         ->where('fee_invoices.payment_status', '!=', 'returned')
//         ->select([
//             'fee_invoices.*',
//             'students.student_name',
//             'students.admission_number',
//             'student_categories.category_name',
//             'branches.name as branch_name',
//             'fee_types.name as fee_type_name',
//             'courses.name as course_name',
//         ]);

//     return DataTables::of($query)
//         ->addIndexColumn()

//         ->editColumn('fee_name', function ($row) {
//             return $row->invoice_type === 'fee'
//                 ? ($row->fee_type_name ?? '-')
//                 : ($row->course_name ?? '-');
//         })

//         ->addColumn('balance', function ($row) {
//             return number_format($row->total_amount - $row->paid_amount, 2);
//         })

//         ->addColumn('payment_date', fn($row) =>
//             \Carbon\Carbon::parse($row->create_date)->format('m/d/Y')
//         )

//         ->addColumn('due_date', fn($row) =>
//             \Carbon\Carbon::parse($row->due_date)->format('m/d/Y')
//         )

//         ->filter(function ($query) {
//             $keyword = request('search.value');

//             if ($keyword) {
//                 $query->where(function ($q) use ($keyword) {
//                     $q->where('fee_invoices.main_invoice_number', 'like', "%{$keyword}%")
//                       ->orWhere('students.student_name', 'like', "%{$keyword}%")
//                       ->orWhere('students.admission_number', 'like', "%{$keyword}%")
//                       ->orWhere('student_categories.category_name', 'like', "%{$keyword}%")
//                       ->orWhere('branches.name', 'like', "%{$keyword}%")
//                       ->orWhere('fee_types.name', 'like', "%{$keyword}%")
//                       ->orWhere('courses.name', 'like', "%{$keyword}%")
//                       ->orWhere('fee_invoices.payment_status', 'like', "%{$keyword}%");
//                 });
//             }
//         })

//         ->rawColumns(['payment_status', 'action'])
//         ->make(true);
// }

    public function itemReceive()
    {
        $items = InventoryItem::with('category')->get();
        $suppliers = Supplier::all();
        $branches = Auth::user()->branches;
        // $account_id = BankAccount::all();

        return view('pages.account.inventory.item-receive', compact(
            'items',
            'suppliers',
            'branches',
            // 'account_id'
        ));
    }

    public function itemReceivePost(Request $request)
    {
        // Validate incoming data; arrays for item lines
        $request->validate([
            'payment_method' => 'required|string',
            'supplier_id' => 'required|integer',
            'store_id' => 'required|integer',
            'receive_date' => 'required|string', // will parse m/d/Y -> Y-m-d
            'item_id' => 'required|array|min:1',
            'item_id.*' => 'required|integer',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
            'invoice_number' => 'nullable|string|max:255',
            'bank_payment_amount' => 'nullable|numeric',
            'bank_payment_date' => 'nullable|string',
            'bank_payment_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        // Conditional validation based on payment method
        if ($request->payment_method === 'Bank Transfer') {
            $request->validate([
                'bank_payment_amount' => 'required|numeric|min:0.01',
                'bank_payment_date' => 'required|date',
            ]);

            $subTotal = array_sum(array_map(function ($price, $qty) {
                return (float)$price * (float)$qty;
            }, $request->unit_price, $request->quantity));

            if (($request->bank_payment_amount ?? 0) < $subTotal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank Payment Amount must be greater than the Item Receive Total!',
                ], 422);
            }
        }

        try {
            // Parse receive_date from the UI (YYYY-MM-DD) to database format (Y-m-d)
            $receiveDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->receive_date)->format('Y-m-d');

            // Generate a unique transaction ID
            $transactionId = 'TRX-' . time() . '-' . rand(1000, 9999);

            // Handle file upload for bank_payment_document
            $bankPaymentDocumentPath = null;
            if ($request->hasFile('bank_payment_document')) {
                $bankPaymentDocumentPath = $request->file('bank_payment_document')->store('bank_documents', 'public');
            }

            // Create one row per item, duplicating header fields as per existing schema
            $createdReceives = [];
            foreach ($request->item_id as $index => $itemId) {
                $lineTotal = (float) $request->unit_price[$index] * (float) $request->quantity[$index];

                $totalPaidAmount = 0;
                if ($request->payment_method === 'Bank Transfer') {
                    $totalPaidAmount = $request->bank_payment_amount ?? 0;
                } else {
                    $totalPaidAmount = $request->totalPaid ?? 0;
                }

                $bankPaymentDate = null;
                if ($request->payment_method === 'Bank Transfer' && $request->bank_payment_date) {
                    $bankPaymentDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->bank_payment_date)->format('Y-m-d');
                }

                $itemReceive = ItemReceive::create([
                    'transaction_id' => $transactionId,
                    'payment_method' => $request->payment_method,
                    'bank_payment_amount' => $request->payment_method === 'Bank Transfer' ? $request->bank_payment_amount : null,
                    'bank_payment_date' => $bankPaymentDate,
                    'bank_payment_document' => $request->payment_method === 'Bank Transfer' ? $bankPaymentDocumentPath : null,
                    'supplier_id' => $request->supplier_id,
                    'store_id' => $request->store_id,
                    'reference_no' => $request->reference_no,
                    'receive_date' => $receiveDate,
                    'description' => $request->description,
                    'item_id' => $itemId,
                    'unit_price' => $request->unit_price[$index],
                    'quantity' => $request->quantity[$index],
                    'sub_total' => $lineTotal,
                    'total_paid' => $totalPaidAmount,
                    'created_by' => Auth::id(),
                ]);

                $createdReceives[] = $itemReceive;

                // Track inventory changes for this item receive
                $currentBalance = ItemInventory::getCurrentBalance($itemId);
                $newBalance = $currentBalance + $request->quantity[$index];

                ItemInventory::create([
                    'item_id' => $itemId,
                    'transaction_type' => 'receive',
                    'transaction_id' => $itemReceive->id,
                    'quantity' => $request->quantity[$index],
                    'current_balance' => $newBalance,
                    'reference_no' => $request->reference_no,
                    'transaction_date' => $receiveDate,
                    'description' => $request->description ?? 'Item received from supplier',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item received successfully!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save item receive.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function itemReceivesaveInvoiceNumber(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:item_receive,id',
            'invoice_number' => 'required|string|max:50',
        ]);

        $ItemReceive = ItemReceive::findOrFail($request->id);
        $ItemReceive->invoice_number = $request->invoice_number;
        $ItemReceive->save();
    }
    public function itemReceiveList()
    {
        // Group by transaction_id and get the first record for each transaction
        $itemReceives = ItemReceive::with(['supplier', 'branch', 'item', 'item.category'])
            ->distinct()
            ->get();

        $items = InventoryItem::all();
        $suppliers = Supplier::all();
        $branches = Branch::all();
        return view('pages.account.inventory.item-receive-list', compact('itemReceives', 'items', 'suppliers', 'branches'));
    }

    public function viewItemReceive($transactionId)
    {
        try {
            $transaction = ItemReceive::where('transaction_id', $transactionId)->with(['supplier', 'branch', 'item'])->first();
            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            $items = ItemReceive::where('transaction_id', $transactionId)->with(['item', 'supplier', 'branch'])->get();

            // Calculate totals
            $totalQuantity = $items->sum('quantity');
            $totalSubTotal = $items->sum('sub_total');
            $totalPaid = $transaction->total_paid;
            $totalBalance = $totalSubTotal - $totalPaid;

            return response()->json([
                'transaction' => $transaction,
                'items' => $items,
                'totals' => [
                    'total_quantity' => $totalQuantity,
                    'total_sub_total' => $totalSubTotal,
                    'total_paid' => $totalPaid,
                    'total_balance' => $totalBalance,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch transaction details: ' . $e->getMessage()], 500);
        }
    }

    // public function editItemReceive($transactionId)
    // {
    //     try {
    //         $itemReceives = ItemReceive::where('transaction_id', $transactionId)->with('supplier', 'item', 'branch')->get();
    //         if ($itemReceives->isEmpty()) {
    //             return response()->json(['error' => 'Transaction not found'], 404);
    //         }

    //         $items = Inventory_item::all();
    //         $suppliers = Supplier::all();
    //         $branches = Branch::all();

    //         // Get the first record for common transaction details
    //         $transaction = $itemReceives->first();

    //         return response()->json([
    //             'transaction' => $transaction,
    //             'itemReceives' => $itemReceives,
    //             'items' => $items,
    //             'suppliers' => $suppliers,
    //             'branches' => $branches,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Failed to load transaction for editing: ' . $e->getMessage()], 500);
    //     }
    // }

    // ==============================

    // public function updateItemReceive(Request $request, $transactionId)
    // {
    //     $request->validate([
    //         'payment_method' => 'required|string',
    //         'supplier_id' => 'required|integer',
    //         'store_id' => 'required|integer',
    //         'receive_date' => 'required|string',
    //         'item_id' => 'required|integer',
    //         'unit_price' => 'required|numeric',
    //         'quantity' => 'required|numeric',
    //     ]);

    //     try {
    //         $receiveDate = \Carbon\Carbon::createFromFormat('m/d/Y', $request->receive_date)->format('Y-m-d');
    //     } catch (\Throwable $e) {
    //         try {
    //             $receiveDate = \Carbon\Carbon::parse($request->receive_date)->format('Y-m-d');
    //         } catch (\Throwable $e2) {
    //             return response()->json(['error' => 'Invalid receive date format.'], 422);
    //         }
    //     }

    //     try {
    //         // Update all items in the transaction with common fields
    //         ItemReceive::where('transaction_id', $transactionId)->update([
    //             'payment_method' => $request->payment_method,
    //             'supplier_id' => $request->supplier_id,
    //             'store_id' => $request->store_id,
    //             'reference_no' => $request->reference_no,
    //             'receive_date' => $receiveDate,
    //             'description' => $request->description,
    //             'total_paid' => $request->totalPaid ?? 0,
    //             'bank_id' => $request->bank_id ?? null,
    //         ]);

    //         // Update the first item with specific item details
    //         $firstItem = ItemReceive::where('transaction_id', $transactionId)->first();
    //         if ($firstItem) {
    //             $firstItem->update([
    //                 'item_id' => $request->item_id,
    //                 'unit_price' => $request->unit_price,
    //                 'quantity' => $request->quantity,
    //                 'sub_total' => (float) $request->unit_price * (float) $request->quantity,
    //             ]);
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Transaction updated successfully! Note: Only the first item details were updated. For complex multi-item edits, consider deleting and recreating the transaction.',
    //         ]);
    //     } catch (\Throwable $e) {
    //         return response()->json(['error' => 'Failed to update transaction: ' . $e->getMessage()], 500);
    //     }
    // }

    public function deleteItemReceive($transactionId)
    {
        try {
            $itemReceives = ItemReceive::where('transaction_id', $transactionId)->get();
            foreach ($itemReceives as $itemReceive) {
                $itemReceive->delete();
            }
            return response()->json([
                'status' => true,
                'message' => 'Item receive deleted successfully!',
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to delete item receive: ' . $e->getMessage());
        }
    }

    // public function itemIssue()
    // {
    //     // itemname get using inventerory items table
    //     return view('pages.account.inventory.item-issue');
    // }
}
