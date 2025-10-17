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
use Illuminate\Support\Facades\Auth;

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
        // dd($request->all());

        // Validate request
        $request->validate([
            'branch_id' => 'required',
            'student_id' => 'required',
            'create_date' => 'required|date_format:m/d/Y',
            'due_date' => 'required|date_format:m/d/Y',
            'fee_type_id' => 'required|array',
            'price' => 'required|array',
            'paidPrice' => 'required|array',
            'payment_method' => 'nullable',
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
            $due_amount = $total_amount - $total_paid_amount;

            // Determine payment status
            if ($total_paid_amount == 0) {
                $payment_status = 'not';
            } elseif ($total_paid_amount < $total_amount) {
                $payment_status = 'partial';
            } else {
                $payment_status = 'full';
            }

            $main_invoice_number = 'INV-' . time() . '-' . uniqid();
            $create_user_id = Auth::check() ? Auth::id() : null;
            Log::debug('Generated main_invoice_number (store): ' . $main_invoice_number);
            Log::debug('Auth::id() (store): ' . $create_user_id);

            // Create Fee Invoice
            $fee_invoice = Fee_invoices::create([
                'branch_id' => $request->branch_id,
                'student_id' => $request->student_id,
                'create_date' => Carbon::createFromFormat('m/d/Y', $request->create_date)->format('Y-m-d'),
                'due_date' => Carbon::createFromFormat('m/d/Y', $request->due_date)->format('Y-m-d'),
                'payment_status' => $payment_status,
                'payment_method' => $request->payment_method,
                'total_amount' => $total_amount,
                'paid_amount' => $total_paid_amount,
                'due_amount' => $due_amount,
                'main_invoice_number' => $main_invoice_number,
                'create_user_id' => $create_user_id, // Save the authenticated user's ID
                'bank_paid_amount' => (trim($request->payment_method) === 'Bank Transfer') ? $request->bank_paid_amount : null,
                'bank_paid_date' => (trim($request->payment_method) === 'Bank Transfer' && !empty($request->bank_paid_date))
                    ? Carbon::createFromFormat('Y-m-d', $request->bank_paid_date)->format('Y-m-d')
                    : null,
            ]);
            Log::debug('Fee_invoice created with main_invoice_number: ' . $fee_invoice->main_invoice_number . ' and create_user_id: ' . $fee_invoice->create_user_id);


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
                    // Generate a unique receipt number
                    $receipt_number = 'REC-' . time() . '-' . uniqid();
                    Log::debug('Generated receipt_number (store): ' . $receipt_number);

                    $payment_create_user_id = null;
                    if (Auth::check()) {
                        $payment_create_user_id = Auth::id();
                        Log::debug('Auth::id() for payment (store): ' . $payment_create_user_id);
                    } else {
                        Log::warning('No authenticated user found when creating Invoices_payments in store method.');
                    }

                    Invoices_payments::create([
                        'invoice_id' => $fee_invoice->id,
                        // 'payment_invoice_number' => , add by modal
                        'amount' => $paid_price,
                        'payment_method' => $request->payment_method,
                        'payment_type' => 'fee',
                        'payment_file' => ($request->payment_method === 'Card' || $request->payment_method === 'Bank Transfer')
                            && $request->file('payment_file')
                            ? $request->file('payment_file')->store('invoices', 'public')
                            : null,
                        'receipt_number' => $receipt_number,
                        'create_user_id' => $payment_create_user_id, // Save the authenticated user's ID
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
            'payment_method' => 'nullable',
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
        try {
            $invoice = Fee_invoices::with('invoice_item', 'payments')->findOrFail($invoiceId); // Eager load payments

            // Calculate the current due amount based on actual payments
            $totalPaidAmountFromPayments = $invoice->payments->sum('amount');
            $currentDueAmount = $invoice->total_amount - $totalPaidAmountFromPayments;

            // Log the calculated due amount for debugging
            Log::debug('addPayment: Calculated currentDueAmount: ' . $currentDueAmount);

            // Validate the request, including the payment amount
            $request->validate([
                'pay_amount' => 'required|numeric|min:0.01|max:' . max(0.01, $currentDueAmount), // Ensure max is at least 0.01
                'payment_method' => 'required|string',
                'payment_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
                'create_user_id' => 'nullable|integer', // Validate create_user_id
            ]);

            $totalPaidNow = $request->input('pay_amount');
            $remainingPayment = $totalPaidNow;

            Log::debug('addPayment: Incoming pay_amount: ' . $totalPaidNow);

            // Distribute the payment across invoice items
            foreach ($invoice->invoice_item as $item) {
                if ($remainingPayment <= 0) {
                    break;
                }

                $itemDue = $item->amount - $item->paid_amount;
                $payForItem = min($remainingPayment, $itemDue);

                if ($payForItem > 0) {
                    $item->paid_amount += $payForItem;
                    $item->save();
                    $remainingPayment -= $payForItem;
                    Log::debug("addPayment: Item {$item->id} paid {$payForItem}. Remaining payment: {$remainingPayment}");
                }
            }

            $paymentFile = null;
            if ($request->hasFile('payment_file')) {
                $paymentFile = $request->file('payment_file')->store('payments', 'public');
                Log::debug('addPayment: Payment file uploaded: ' . $paymentFile);
            }

            // Generate a unique receipt number for the payment
            $receipt_number = 'REC-' . time() . '-' . uniqid();
            Log::debug('Generated receipt_number (addPayment): ' . $receipt_number);

            $payment_create_user_id = $request->input('create_user_id');
            if (!$payment_create_user_id && Auth::check()) {
                $payment_create_user_id = Auth::id();
            }
            Log::debug('Auth::id() for payment (addPayment): ' . $payment_create_user_id);


            Invoices_payments::create([
                'invoice_id' => $invoice->id,
                'amount' => $totalPaidNow,
                'payment_method' => $request->payment_method,
                'payment_type' => 'fee',
                'payment_file' => $paymentFile,
                'receipt_number' => $receipt_number,
                'create_user_id' => $payment_create_user_id, // Save the authenticated user's ID
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Log::debug('Invoices_payments record created for invoice_id: ' . $invoice->id . ' amount: ' . $totalPaidNow);

            // Update invoice summary
            $invoice->paid_amount += $totalPaidNow;
            $invoice->due_amount -= $totalPaidNow; // Update due_amount
            $invoice->payment_status = $invoice->paid_amount >= $invoice->total_amount ? 'full' : 'partial';
            $invoice->payment_method = $request->payment_method;
            $invoice->create_user_id = $payment_create_user_id; // Ensure this is updated if needed
            $invoice->save();
            Log::debug('Fee_invoice updated with paid_amount: ' . $invoice->paid_amount . ', due_amount: ' . $invoice->due_amount . ', payment_status: ' . $invoice->payment_status . ' and create_user_id: ' . $invoice->create_user_id . ' AFTER SAVE');

            return response()->json([
                'success' => true,
                'message' => 'Payment added successfully!',
                'invoice_id' => $invoice->id,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error in addPayment: ' . $e->getMessage() . ' Errors: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in addPayment: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPaymentHistory($invoiceId)
    {
        $invoice = Fee_invoices::with('payments')->findOrFail($invoiceId);
        $payments = $invoice->payments;

        $totalPaidAmount = $payments->sum('amount');
        $dueAmount = $invoice->total_amount - $totalPaidAmount;

        return response()->json([
            'total_amount' => $invoice->total_amount,
            'paid_amount_sum' => $totalPaidAmount, // Sum of all payments
            'due_amount' => $dueAmount,
            'main_invoice_number' => $invoice->main_invoice_number, // Add main_invoice_number
            'payments' => $payments,
        ]);
    }

    public function refundPayment(Request $request, $paymentId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $payment = Invoices_payments::findOrFail($paymentId);
            $invoice = Fee_invoices::findOrFail($payment->invoice_id);

            // Ensure the refund amount does not exceed the paid amount for this specific payment
            $refundAmount = min($request->amount, $payment->amount);

            // Update the payment record (e.g., mark as refunded, adjust amount)
            // For simplicity, we'll just reduce the amount and mark it as refunded.
            // In a real application, you might create a new 'refund' transaction.
            $payment->amount -= $refundAmount;
            $payment->payment_status = ($payment->amount <= 0) ? 'refunded' : 'partial_refund';
            $payment->save();

            // Update the main invoice's paid amount and status
            $invoice->paid_amount -= $refundAmount;
            $invoice->due_amount += $refundAmount;
            $invoice->payment_status = ($invoice->paid_amount <= 0) ? 'not' : 'partial';
            $invoice->save();

            return response()->json([
                'success' => true,
                'message' => 'Payment refunded successfully!',
                'new_payment_amount' => $payment->amount,
                'new_invoice_paid_amount' => $invoice->paid_amount,
                'new_invoice_due_amount' => $invoice->due_amount,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing refund: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateInvoiceNumber(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:fee_invoices,id',
            'invoice_number' => 'required|string|max:255',
        ]);

        try {
            $invoice = Fee_invoices::findOrFail($request->invoice_id);
            $invoice->invoice_number = $request->invoice_number;
            $invoice->save();

            return response()->json([
                'success' => true,
                'message' => 'Invoice number updated successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating invoice number: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function addPaymentInvoiceNumber(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:invoices_payments,id',
            'invoice_number' => 'required|string|max:255',
        ]);

        try {
            $payment = Invoices_payments::findOrFail($request->payment_id);
            $payment->payment_invoice_number = $request->invoice_number;
            $payment->save();

            return response()->json([
                'success' => true,
                'message' => 'Payment invoice number updated successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating payment invoice number: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function indexCoursePayment()
    {
        $branches = Branch::all();
        $students = Student::all();
        $courses = Course::all(); // Assuming Course model exists

        return view('pages.student.fees.course-paid', compact('branches', 'students', 'courses'));
    }

    public function markAsPaid(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:fee_invoices,id',
            'paid_amount' => 'required|numeric|min:0',
            'paid_date' => 'required|date_format:Y-m-d',
        ]);

        try {
            $invoice = Fee_invoices::findOrFail($request->invoice_id);

            // Update invoice details
            $invoice->paid_amount = $invoice->total_amount; // Set paid_amount to total_amount
            $invoice->payment_status = 'full'; // Set status to 'full'
            $invoice->paid_date = Carbon::createFromFormat('Y-m-d', $request->paid_date)->format('Y-m-d');
            $invoice->save();

            // Create a new payment record
            $payment_create_user_id = Auth::check() ? Auth::id() : null; // Get authenticated user ID
            Invoices_payments::create([
                'invoice_id' => $invoice->id,
                // 'payment_invoice_number' => ,this is add by using modal
                'amount' => $request->paid_amount,
                'payment_method' => 'Paid', // Assuming 'Paid' is a valid method or adjust as needed
                'payment_type' => 'fee',
                'create_user_id' => $payment_create_user_id, // Save the authenticated user's ID
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice marked as paid successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking invoice as paid: ' . $e->getMessage(),
            ], 500);
        }
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

            $main_invoice_number = 'INV-' . time() . '-' . uniqid();
            $create_user_id = Auth::check() ? Auth::id() : null;
            Log::debug('Generated main_invoice_number (storeCoursePayment): ' . $main_invoice_number);
            Log::debug('Auth::id() (storeCoursePayment): ' . $create_user_id);

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
                'main_invoice_number' => $main_invoice_number,
                'create_user_id' => $create_user_id,
                'payment_file' => null, // Will be handled below if file exists
            ]);
            Log::debug('Course Fee_invoice created with main_invoice_number: ' . $fee_invoice->main_invoice_number . ' and create_user_id: ' . $fee_invoice->create_user_id);


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
            // Create a new payment record in invoices_payments table
            Invoices_payments::create([
                'invoice_id' => $fee_invoice->id,
                // 'payment_invoice_number' => , this is add by using modal
                'amount' => $paidAmount,
                'payment_method' => $paymentMethod,
                'payment_type' => 'course',
                'payment_file' => $paidCourse->payment_file, // Use the file path from paidCourse
                'receipt_number' => 'REC-' . time() . '-' . uniqid(), // Generate a unique receipt number
                'create_user_id' => $create_user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

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
