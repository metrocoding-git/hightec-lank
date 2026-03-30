<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FeeInvoices;
use App\Models\FeesGroup;
use App\Models\InvoicesPayments;
use App\Models\PaidCourse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeesGroupController extends Controller
{
    // List all fee groups
    public function index()
    {
        $feesGroups = FeesGroup::all();
        return view('pages.student.fees.fee-group', compact('feesGroups'));
    }

    // Store new fee group
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        FeesGroup::create($request->only('name', 'description'));

        return redirect()->back()->with('success', 'Fees group created successfully!');
    }

    // Get fee group data for edit
    public function edit($id)
    {
        $feesGroup = FeesGroup::findOrFail($id);
        $feesGroups = FeesGroup::all(); // to show the list along with edit form
        return view('pages.student.fees.fee-group', compact('feesGroup', 'feesGroups'));
    }

    // Update fee group
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $feesGroup = FeesGroup::findOrFail($id);
        $feesGroup->update($request->only('name', 'description'));

        return redirect()->back()->with('success', 'Fees group updated successfully!');
    }

    // Delete fee group
    public function delete($id)
    {
        $feesGroup = FeesGroup::findOrFail($id);
        $feesGroup->delete();

        return redirect()->back()->with('success', 'Fees group deleted successfully!');
    }

    public function coursePaidView()
    {
        $branches = Auth::user()->branches;
        $students = \App\Models\Student::select(
            'students.id',
            'students.admission_number',
            'students.national_id_number',
            DB::raw("CONCAT(students.student_name) AS student_name"),

            DB::raw("
                CASE 
                    WHEN students.is_old_stu = 1
                        OR EXISTS (
                            SELECT 1
                            FROM fee_invoices fi
                            WHERE fi.student_id = students.id
                        )
                    THEN 1
                    ELSE 0
                END AS is_admission
            
            ")
        )
            ->get();
        $courses = []; // Empty for dynamic loading
        return view('pages.student.fees.course-paid', compact('branches', 'students', 'courses'));
    }

    public function storeCoursePayment(Request $request)
    {
        // Validate request
        $request->validate([
            'branch_id' => 'required',
            'student_id' => 'required',
            'create_date' => 'required|date_format:m/d/Y',
            'due_date' => 'required|date_format:m/d/Y',
            'payment_status' => 'required',
            'course_id' => 'required|exists:courses,id',
            'course_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required',
            'payment_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'bank_paid_amount' => 'nullable|numeric|min:0',
            'bank_paid_date' => 'nullable|date_format:Y-m-d',
            'notes' => 'nullable|string',
            'payment_type' => 'required|in:course,nvq',
        ]);


        try {
            $coursePrice = $request->course_price;
            $paidAmount = $request->paid_amount;
            $paymentMethod = $request->payment_method;
            $dueAmount = $coursePrice - $paidAmount;
            $paymentType = $request->payment_type;

            $course = Course::find($request->course_id);

            if ($paymentType === 'nvq' && $course->has_nvq) {
                $coursePrice = $course->nvq_amount;
            } else {
                $coursePrice = $request->course_price;
            }

            $create_user_id = Auth::check() ? Auth::id() : null;

            $existingInvoice = FeeInvoices::join('paid_courses', 'fee_invoices.id', '=', 'paid_courses.invoice_id')
                ->where('fee_invoices.payment_status', '!=', 'returned')
                ->where('fee_invoices.student_id', $request->student_id)
                ->where('fee_invoices.invoice_type', $paymentType)
                ->where('paid_courses.course_id', $request->course_id)
                ->select('fee_invoices.id as fee_invoice_id', 'paid_courses.id as paid_course_id')
                ->first();

            $fee_invoice = null;
            $paidCourse = null;


            if ($existingInvoice) {

                $fee_invoice = FeeInvoices::find($existingInvoice->fee_invoice_id);

                $paidCourse = PaidCourse::find($existingInvoice->paid_course_id);
                // Update PaidCourse
                $paidCourse->paid_amount += $paidAmount;
                $paidCourse->due_amount -= $paidAmount;
                $paidCourse->payment_status = ($paidCourse->paid_amount >= $paidCourse->course_price) ? 'full' : 'partial';
                $paidCourse->payment_method = $paymentMethod;
                $paidCourse->save();


                // Update Fee_invoices
                $fee_invoice->paid_amount += $paidAmount;
                $fee_invoice->due_amount -= $paidAmount;
                $fee_invoice->payment_status = ($fee_invoice->paid_amount >= $fee_invoice->total_amount) ? 'full' : 'partial';
                $fee_invoice->payment_method = $paymentMethod;
                $fee_invoice->save();
            } else {

                // Case 2: No existing invoice/paid course found, create new ones
                do {
                    $main_invoice_number = 'INV-' . rand(100000, 999999);
                } while (FeeInvoices::where('main_invoice_number', $main_invoice_number)->exists());
                $create_user_id = Auth::id();

                // Create a FeeInvoices record
                $fee_invoice = FeeInvoices::create([
                    'branch_id' => $request->branch_id,
                    'invoice_type' => $paymentType,
                    'student_id' => $request->student_id,
                    'create_date' => Carbon::createFromFormat('m/d/Y', $request->create_date)->format('Y-m-d'),
                    'due_date' => Carbon::createFromFormat('m/d/Y', $request->due_date)->format('Y-m-d'),
                    'payment_status' => $request->payment_status,
                    'payment_method' => $paymentMethod,
                    'total_amount' => $coursePrice,
                    'paid_amount' => $paidAmount,
                    'due_amount' => $dueAmount,
                    'main_invoice_number' => $main_invoice_number,
                    'create_user_id' => $create_user_id,
                    'payment_file' => null,
                    'notes' => $request->notes,
                ]);
                Log::debug('Course Fee_invoice created with main_invoice_number: ' . $fee_invoice->main_invoice_number . ' and create_user_id: ' . $fee_invoice->create_user_id);

                $paidCourse = PaidCourse::create([
                    'invoice_id' => $fee_invoice->id,
                    'course_id' => $request->course_id,
                    'course_price' => $coursePrice,
                    'paid_amount' => $paidAmount,
                    'due_amount' => $dueAmount,
                    'payment_status' => $request->payment_status,
                    'payment_method' => $paymentMethod,
                    'bank_paid_amount' => ($paymentMethod === 'Card' || $paymentMethod === 'Bank Transfer') && $request->bank_paid_amount !== null ? $request->bank_paid_amount : null,
                    'bank_paid_date' => ($paymentMethod === 'Card' || $paymentMethod === 'Bank Transfer') && $request->bank_paid_date
                        ? Carbon::createFromFormat('Y-m-d', $request->bank_paid_date)->format('Y-m-d')
                        : null,
                    'payment_file' => null, // Will be handled below if file exists
                ]);
            }

            $paymentFile = null;
            if ($request->hasFile('payment_file')) {
                $paymentFile = $request->file('payment_file')->store('payments', 'public');
            }

            $bankPaidAmountForPayment = null;
            $bankPaidDateForPayment = null;

            if ($paymentMethod === 'Bank Transfer') {
                $bankPaidAmountForPayment = $request->bank_paid_amount;
                if (!empty($request->bank_paid_date)) {
                    $bankPaidDateForPayment = Carbon::createFromFormat('Y-m-d', $request->bank_paid_date)->format('Y-m-d');
                }
            }

            $receipt_number = $this->generateReceiptNumber($request->branch_id, $paymentType);


            $payment = InvoicesPayments::create([
                'invoice_id' => $fee_invoice->id,
                'amount' => $paidAmount,
                'payment_method' => $paymentMethod,
                'payment_type' => $paymentType,
                'payment_file' => $paymentFile, // Use the newly uploaded file or null
                'receipt_number' => $receipt_number,
                'create_user_id' => $create_user_id,
                'bank_payment_amount' => $bankPaidAmountForPayment,
                'bank_payment_date' => $bankPaidDateForPayment,
                'branch_id' => $request->branch_id,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Course payment saved successfully!',
                // 'paid_course_id' => $paidCourse->id,
                'invoice_id' => $fee_invoice->id,
                'payment_id' => $payment->id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function indexCoursePayment()
    {
        $branches = Auth::user()->branches;
        $students = Student::all();
        $courses = Course::all(); // Assuming Course model exists

        return view('pages.student.fees.course-paid', compact('branches', 'students', 'courses'));
    }


    // public function getCoursePaymentHistory($studentId, $courseId)
    // {
    //     try {
    //         dd('dd');
    //         // Find the Fee_invoices record associated with this student and course
    //         // This assumes there's one primary Fee_invoices of type 'course' per student-course combination
    //         $feeInvoice = Fee_invoices::where('student_id', $studentId)
    //             ->where('invoice_type', ['course', 'nvq'])
    //             ->whereHas('paidCourses', function ($query) use ($courseId) {
    //                 $query->where('course_id', $courseId);
    //             })
    //             ->first();

    //         if (!$feeInvoice) {
    //             // If no invoice exists, it's the first payment. Due amount is the full course price.
    //             $course = Course::find($courseId);
    //             $coursePrice = $course ? $course->price : 0;
    //             return response()->json([
    //                 'success' => true,
    //                 'history' => [],
    //                 'total_paid' => 0,
    //                 'due_amount' => $coursePrice,
    //                 'course_price' => $coursePrice,
    //             ]);
    //         }

    //         // Get all payments for this specific fee invoice, eager load the invoice for status
    //         $payments = Invoices_payments::whereIn('invoice_id', $feeInvoice)
    //             ->orderBy('created_at', 'asc')
    //             ->get();

    //         // Get the course price from the associated PaidCourse
    //         $paidCourse = PaidCourse::whereIn('invoice_id', $feeInvoices)
    //             ->where('course_id', $courseId)
    //             ->latest()
    //             ->first();

    //         $coursePrice = $paidCourse->course_price ?? 0;
    //         $totalPaid = $payments->sum('amount');
    //         $dueAmount = $coursePrice - $totalPaid;
    //         return response()->json([
    //             'success' => true,
    //             'history' => $payments, // Now returning Invoices_payments
    //             'total_paid' => $totalPaid,
    //             'due_amount' => $dueAmount,
    //             'course_price' => $coursePrice,
    //             'invoice_id' => $feeInvoice->id, // Also return invoice_id for potential use
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Error fetching course payment history: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error fetching course payment history',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }


    public function getCoursePaymentHistory($studentId, $courseId)
    {
        try {

            // Get BOTH invoice types
            $invoices = FeeInvoices::where('student_id', $studentId)
                ->where('payment_status', '!=', 'returned')
                ->whereIn('invoice_type', ['course', 'nvq'])
                ->whereHas('paidCourses', function ($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })
                ->get();

            if ($invoices->isEmpty()) {
                $course = Course::find($courseId);

                return response()->json([
                    'success' => true,
                    'history' => [],
                    'course_total_paid' => 0,
                    'course_due_amount' => $course->price ?? 0,
                    'nvq_total_paid' => 0,
                    'nvq_due_amount' => $course->nvq_amount ?? 0,
                ]);
            }

            // Separate invoice IDs by type
            $courseInvoiceIds = $invoices->where('invoice_type', 'course')->pluck('id');
            $nvqInvoiceIds    = $invoices->where('invoice_type', 'nvq')->pluck('id');

            // Get payments
            $payments = InvoicesPayments::whereIn('invoice_id', $invoices->pluck('id'))
                ->orderBy('created_at', 'asc')
                ->get();

            // Split payments
            $coursePayments = $payments->whereIn('invoice_id', $courseInvoiceIds);
            $nvqPayments    = $payments->whereIn('invoice_id', $nvqInvoiceIds);

            // Totals
            $courseTotalPaid = $coursePayments->sum('amount');
            $nvqTotalPaid    = $nvqPayments->sum('amount');

            // Get prices
            $coursePaidCourse = PaidCourse::whereIn('invoice_id', $courseInvoiceIds)
                ->where('course_id', $courseId)
                ->first();

            $nvqPaidCourse = PaidCourse::whereIn('invoice_id', $nvqInvoiceIds)
                ->where('course_id', $courseId)
                ->first();
            if (!$nvqPaidCourse) {
                $course = Course::find($courseId);
                $nvqPrice = $course->nvq_amount;
            } else {
                $nvqPrice = $nvqPaidCourse->course_price ?? 0;
            }

            $coursePrice = $coursePaidCourse->course_price ?? 0;

            // Due calculations
            $courseDue = $coursePrice - $courseTotalPaid;
            $nvqDue    = $nvqPrice - $nvqTotalPaid;

            return response()->json([
                'success' => true,

                'history' => $payments,

                'course_total_paid' => $courseTotalPaid,
                'course_due_amount' => $courseDue,
                'course_amount' => $coursePrice,


                'nvq_total_paid' => $nvqTotalPaid,
                'nvq_due_amount' => $nvqDue,
                'nvq_amount' => $nvqPrice,

            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching course payment history: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching course payment history',
            ], 500);
        }
    }

    private function generateReceiptNumber($branch_id, $paymentType)
    {
        $branch = \App\Models\Branch::findOrFail($branch_id);
        $code = strtolower(substr($branch->name, 0, 2)); // ku / ka

        // Prefix logic
        if ($paymentType === 'nvq') {
            $prefix = 'nvq-' . $code; // nvq-ku
        } else {
            $prefix = $code; // ku / ka
        }

        // Get last number ONLY for this prefix
        $maxNumber = \App\Models\InvoicesPayments::whereHas('invoice', function ($q) use ($branch_id) {
            $q->where('branch_id', $branch_id);
        })
            ->where('receipt_number', 'like', $prefix . '-%')
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(receipt_number, '-', -1) AS UNSIGNED)) as max_no")
            ->value('max_no');

        $next = ((int) $maxNumber) + 1;

        return $prefix . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}
