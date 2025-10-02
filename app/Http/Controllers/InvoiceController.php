<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Fee_invoices;
use App\Models\FeesGroup;
use App\Models\FeeType;
use App\Models\Invoice_item;
use App\Models\Invoices_payments;
use App\Models\PaidCourse; // Import the new PaidCourse model
use App\Models\Student;
use App\Models\Course; // Import the Course model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fee_invoices = Fee_invoices::with('student', 'branch', 'invoice_item')->get();
        return view('pages.student.fees.fee-invoice', compact('fee_invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::all();
        $feesgroups = FeesGroup::all();
        $feeTypes = FeeType::all();
        $students = Student::all();
        return view('pages.student.fees.add-invoice', compact('branches', 'feesgroups', 'feeTypes', 'students'));
    }

    public function store(Request $request)
    {
        // Temporarily dump all request data for debugging
        // dd($request->all());

        // Validate request
        $request->validate([
            'branch_id' => 'required',
            'student_id' => 'required',
            'create_date' => 'required|date_format:m/d/Y',
            'due_date' => 'required|date_format:m/d/Y',
            'payment_status' => 'required',
            'fee_type_id' => 'required|array',
            'price' => 'required|array',
            'paidPrice' => 'required|array',
            'payment_method' => 'required',
            'payment_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'bank_paid_amount' => 'nullable|numeric|min:0',
            'bank_paid_date' => 'nullable|date_format:Y-m-d',
        ]);

        // Conditionally make bank details required if payment method is Bank Transfer
        if ($request->payment_method === 'Bank Transfer') {
            $request->validate([
                'bank_paid_amount' => 'required|numeric|min:0',
                'bank_paid_date' => 'required|date_format:Y-m-d',
            ]);
        }

        try {
            $total_amount = array_sum($request->price);
            $total_paid_amount = array_sum($request->paidPrice);

            Log::info('Payment Method: ' . $request->payment_method);
            // Removed specific bank_paid_amount and bank_paid_date logs, dd() will show all

            // Create Fee Invoice
            $fee_invoice = Fee_invoices::create([
                'branch_id' => $request->branch_id,
                'student_id' => $request->student_id,
                'create_date' => Carbon::createFromFormat('m/d/Y', $request->create_date)->format('Y-m-d'),
                'due_date' => Carbon::createFromFormat('m/d/Y', $request->due_date)->format('Y-m-d'),
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_method,
                'total_amount' => $total_amount,
                'paid_amount' => $total_paid_amount,
                'bank_paid_amount' => (trim($request->payment_method) === 'Bank Transfer') ? $request->bank_paid_amount : null,
                'bank_paid_date' => (trim($request->payment_method) === 'Bank Transfer' && !empty($request->bank_paid_date))
                    ? Carbon::createFromFormat('Y-m-d', $request->bank_paid_date)->format('Y-m-d')
                    : null,
            ]);

            // Save Invoice Items
            foreach ($request->fee_type_id as $feetypeId) {
                $amount = $request->price[$feetypeId] ?? (FeeType::where('id', $feetypeId)->value('price') ?? 0);
                $paid_price = $request->paidPrice[$feetypeId] ?? 0;

                $invoiceItem = Invoice_item::create([
                    'invoice_id' => $fee_invoice->id,
                    'fee_type_id' => $feetypeId,
                    'amount' => $amount,
                    'paid_amount' => $paid_price,
                ]);

                // Save initial payment in invoices_payments table if any
                if ($paid_price > 0) {
                    Invoices_payments::create([
                        'invoice_id' => $fee_invoice->id,
                        'amount' => $paid_price,
                        'payment_method' => $request->payment_method,
                        'payment_file' => ($request->payment_method === 'Card' || $request->payment_method === 'Bank Transfer')
                            && $request->file('payment_file')
                            ? $request->file('payment_file')->store('invoices', 'public')
                            : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice, items, and initial payments created successfully',
                'invoice_id' => $fee_invoice->id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function show(Fee_invoices $fee_invoicese, $branchId)
    {
        $student = Student::where('branch_id', $branchId)
            ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as name"))
            ->get();
        return response()->json($student);
    }

    public function dropdown_fee_types(Fee_invoices $e, $groupId)
    {
        $feetypes = FeeType::where('fee_group_id', $groupId)->get(['id', 'name']);
        return response()->json($feetypes);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoice = Fee_invoices::with('invoice_item.fee_type', 'student')->findOrFail($id);
        $branches = Branch::all();
        $feesgroups = FeesGroup::all();
        $feeTypes = FeeType::all();

        return view('pages.student.fees.edit-invoice', compact(
            'invoice',
            'branches',
            'feesgroups',
            'feeTypes'
        ));
    }

    public function viewFeeInvoice($id)
    {
        $invoice = Fee_invoices::with('invoice_item.fee_type', 'student', 'branch')->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return view('pages.student.fees.partials.invoice-view-content', compact('invoice'));
        }

        return view('pages.student.fees.fee-invoice-view', compact('invoice'));
    }

    public function getStudentDetails($studentId)
    {
        $student = Student::with(['branch', 'gender', 'bloodGroup', 'courses'])
            ->findOrFail($studentId);

        // Use the accessor method for consistent data structure
        $studentData = $student->student_details;

        // Add courses data separately since the accessor might not include it properly
        $studentData['courses'] = $student->courses->map(function ($course) {
            return ['name' => $course->name];
        })->toArray();

        return response()->json($studentData);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $invoice = Fee_invoices::findOrFail($id);

        $request->validate([
            'branch_id' => 'required',
            'student_id' => 'required',
            'create_date' => 'required|date_format:m/d/Y',
            'due_date' => 'required|date_format:m/d/Y',
            'payment_status' => 'required',
            'payment_method' => 'required',
            'price' => 'array',
            'paidPrice' => 'array',
            'payment_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        try {
            $total_amount = array_sum($request->price);
            $total_paid_amount = array_sum($request->paidPrice);

            $invoice->update([
                'branch_id' => $request->branch_id,
                'student_id' => $request->student_id,
                'create_date' => Carbon::createFromFormat('m/d/Y', $request->create_date)->format('Y-m-d'),
                'due_date' => Carbon::createFromFormat('m/d/Y', $request->due_date)->format('Y-m-d'),
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_method,
                'total_amount' => $total_amount,
                'paid_amount' => $total_paid_amount,
                'payment_file' => $request->hasFile('payment_file') ? $request->file('payment_file')->store('invoices', 'public') : $invoice->payment_file,
            ]);

            // Delete old items
            Invoice_item::where('invoice_id', $invoice->id)->delete();

            // Add updated items
            foreach ($request->price as $feeTypeId => $amount) {
                $paid_amount = $request->paidPrice[$feeTypeId] ?? 0;

                Invoice_item::create([
                    'invoice_id' => $invoice->id,
                    'fee_type_id' => $feeTypeId,
                    'amount' => $amount,
                    'paid_amount' => $paid_amount,
                ]);
            }

            // Check if it's an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice updated successfully!',
                    'invoice_id' => $invoice->id,
                ], 200);
            }

            return redirect()->route('fees.fee-invoice')->with('success', 'Invoice updated successfully!');
        } catch (\Exception $e) {
            // Check if it's an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Find invoice
            $invoice = Fee_invoices::findOrFail($id);

            // Delete related invoice items
            Invoice_item::where('invoice_id', $invoice->id)->delete();

            // Delete related invoice payments
            Invoices_payments::where('invoice_id', $invoice->id)->delete();

            // Delete uploaded payment file if exists
            if ($invoice->payment_file && Storage::disk('public')->exists($invoice->payment_file)) {
                Storage::disk('public')->delete($invoice->payment_file);
            }

            // Delete the invoice
            $invoice->delete();

            return redirect()->route('fees.fee-invoice')->with('message-success-delete', 'Invoice, items, and related payments deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('fees.fee-invoice')->with('message-danger', $e->getMessage());
        }
    }


    public function addPayment(Request $request, $invoiceId)
    {
        $invoice = Fee_invoices::with('invoice_item')->findOrFail($invoiceId);

        $payAmounts = $request->input('pay_amount', []); // array: [item_id => amount]

        $totalPaidNow = 0;

        foreach ($invoice->invoice_item as $item) {
            if (isset($payAmounts[$item->id]) && $payAmounts[$item->id] > 0) {
                $payNow = min($payAmounts[$item->id], $item->amount - $item->paid_amount);

                $item->paid_amount += $payNow;
                $item->save();

                $totalPaidNow += $payNow;
            }
        }
        $paymentFile = $request->file('payment_file') ? $request->file('payment_file')->store('payments', 'public') : null;

        Invoices_payments::create([
            'invoice_id' => $invoice->id,
            'amount' => $totalPaidNow,
            'payment_method' => $request->payment_method,
            'payment_file' => $paymentFile,
        ]);

        // Update invoice summary
        $invoice->paid_amount += $totalPaidNow;
        $invoice->payment_status = $invoice->paid_amount >= $invoice->total_amount ? 'full' : 'partial';
        $invoice->payment_method = $request->payment_method;
        $invoice->save();

        return redirect()->back()->with('success', 'Payment added successfully!');
    }

    public function indexCoursePayment()
    {
        $branches = Branch::all();
        $students = Student::all();
        $courses = \App\Models\Course::all(); // Assuming Course model exists

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
            'bank_paid_amount' => 'nullable|numeric|min:0', // Conditional validation
            'bank_paid_date' => 'nullable|date_format:Y-m-d', // Conditional validation
        ]);

        try {
            $coursePrice = $request->course_price;
            $paidAmount = $request->paid_amount;
            $paymentMethod = $request->payment_method;
            $dueAmount = $coursePrice - $paidAmount;

            // Create a Fee_invoices record
            $fee_invoice = Fee_invoices::create([
                'invoice_type' => 'course',
                'branch_id' => $request->branch_id,
                'student_id' => $request->student_id,
                'create_date' => Carbon::createFromFormat('m/d/Y', $request->create_date)->format('Y-m-d'),
                'due_date' => Carbon::createFromFormat('m/d/Y', $request->due_date)->format('Y-m-d'),
                'payment_status' => $request->payment_status,
                'payment_method' => $paymentMethod,
                'total_amount' => $coursePrice,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_file' => null, // Will be handled below if file exists
            ]);

            // Save data to the new paid_courses table
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

            // Handle payment file upload for paid_courses
            if ($request->hasFile('payment_file')) {
                $paymentFile = $request->file('payment_file')->store('paid_courses', 'public');
                $paidCourse->update(['payment_file' => $paymentFile]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Course payment saved successfully!',
                'paid_course_id' => $paidCourse->id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
