<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\FeeInvoices;
use App\Models\Role;
use App\Models\FeesGroup;
use App\Models\FeeType;
use App\Models\InvoiceItem;
use App\Models\InvoicesPayments;
use App\Models\PaidCourse; // Import the new PaidCourse model
use App\Models\Student;
use App\Models\Course; // Import the Course model
use App\Models\FoodPayment;
use App\Models\StudentCourse;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class InvoiceController extends Controller
{


    public function feeInvoice()
    {
        $roles = Role::all();
        $fee_invoices = FeeInvoices::with(['student', 'branch', 'invoice_item.fee_type', 'student.studentCategory'])
            ->where('payment_status', '!=', 'returned')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.student.fees.fee-invoice', compact('fee_invoices', 'roles'));
    }

    public function invoiceDatatable(Request $request)
    {
        $keyword = request('search.value');

        $query = DB::table('fee_invoices')
            ->leftJoin('students', 'students.id', '=', 'fee_invoices.student_id')
            ->leftJoin('student_categories', function ($join) {
                $join->on(
                    'student_categories.id',
                    '=',
                    DB::raw('COALESCE(fee_invoices.student_category_id, students.student_category_id)')
                );
            })
            ->leftJoin('branches', 'branches.id', '=', 'fee_invoices.branch_id')
            ->leftJoin('invoice_items', 'invoice_items.invoice_id', '=', 'fee_invoices.id')
            ->leftJoin('fee_types', 'fee_types.id', '=', 'invoice_items.fee_type_id')
            ->leftJoin('paid_courses', 'paid_courses.invoice_id', '=', 'fee_invoices.id')
            ->leftJoin('courses', 'courses.id', '=', 'paid_courses.course_id')
            ->leftJoin('invoices_payments', function ($join) {
                $join->on('invoices_payments.invoice_id', '=', 'fee_invoices.id')
                    ->whereRaw('invoices_payments.id = (
                    SELECT id FROM invoices_payments ip2
                    WHERE ip2.invoice_id = fee_invoices.id
                    ORDER BY ip2.created_at DESC
                    LIMIT 1
                )');
            })
            ->select([
                'fee_invoices.*',
                'students.student_name',
                'students.admission_number',
                'student_categories.category_name',
                'branches.name as branch_name',
                'fee_types.name as fee_type_name',
                'courses.name as course_name',
                'invoices_payments.payment_invoice_number',
                'invoices_payments.amount as invoice_payment_amount',
                'students.national_id_number as nic_number',
                'invoices_payments.invoice_payment_confirm',
                'invoices_payments.payment_method as inv_payment_method',
                DB::raw('fee_invoices.total_amount as fee_total_amount'),
                DB::raw('fee_invoices.paid_amount as fee_paid_amount'),
                DB::raw('(
                    SELECT COALESCE(SUM(ip.amount), 0)
                    FROM invoices_payments ip
                    WHERE ip.invoice_id = fee_invoices.id
                    AND ip.payment_method = "Invoice"
                ) as invoice_only_paid_amount'),
                ('invoices_payments.invoice_payment_amount as invoice_paid_amount'),
            ])
            ->where('fee_invoices.payment_status', '!=', 'returned');

        // ── Only apply filters when NOT searching ──────────────────
        // When a search keyword is present, skip all filters so the
        // user gets results from the entire dataset.
        if (empty($keyword)) {

            // Date Range Filter
            $from = now()->subDays(30)->format('Y-m-d');
            $to   = now()->format('Y-m-d');

            if ($request->date_range) {
                if (str_contains($request->date_range, ' to ')) {
                    [$from, $to] = explode(' to ', $request->date_range);
                } else {
                    $from = $to = $request->date_range;
                }
            }
            $query->whereBetween(DB::raw('DATE(fee_invoices.create_date)'), [$from, $to]);

            // Branch Filter
            if ($request->branch_id) {
                $query->where('fee_invoices.branch_id', $request->branch_id);
            }

            // Payment Type / Fee Type Filter
            if ($request->payment_type) {
                if ($request->payment_type === 'course_fee') {
                    $query->whereIn('fee_invoices.invoice_type', ['course', 'fee']);
                } else {
                    $query->where('fee_invoices.invoice_type', $request->payment_type);
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()

            ->editColumn('fee_name', function ($row) {
                return $row->invoice_type === 'fee'
                    ? ($row->fee_type_name ?? '-')
                    : ($row->invoice_type . ($row->course_name ? ' - ' . $row->course_name : ''));
            })

            ->addColumn('balance', function ($row) {
                return number_format($row->fee_total_amount - $row->fee_paid_amount, 2);
            })

            ->addColumn('nic_number', function ($row) {
                return $row->nic_number ?? '-';
            })
            ->addColumn('updated_date', fn($row) =>
            \Carbon\Carbon::parse($row->updated_at)->format('m/d/Y'))
            ->addColumn('payment_date', fn($row) =>
            \Carbon\Carbon::parse($row->create_date)->format('m/d/Y'))

            ->addColumn('due_date', fn($row) =>
            \Carbon\Carbon::parse($row->due_date)->format('m/d/Y'))

            ->addColumn('inv_payment_number', fn($row) =>
            $row->payment_invoice_number ?? '-')

            ->addColumn('inv_total_amount', fn($row) =>
            $row->invoice_only_paid_amount
                ? number_format($row->invoice_only_paid_amount, 2)
                : '-')
            ->addColumn('inv_paid_amount', fn($row) =>
            $row->invoice_paid_amount > 0
                ? number_format($row->invoice_paid_amount, 2)
                : '-')



            ->addColumn('paid_amount', fn($row) =>
            $row->fee_paid_amount
                ? number_format($row->fee_paid_amount, 2)
                : '-')

            ->addColumn('inv_payment_status', function ($row) {
                if ($row->inv_payment_method !== 'Invoice') {
                    return '<span class="badge badge-secondary">-</span>';
                }
                if ($row->invoice_payment_confirm == 1) {
                    return '<span class="badge badge-success">Completed</span>';
                }
                if (!is_null($row->payment_invoice_number)) {
                    return '<span class="badge badge-warning text-dark">Pending</span>';
                }
                return '<span class="badge badge-danger">Not Opened</span>';
            })

            ->filter(function ($query) {
                $keyword = request('search.value');
                if ($keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('fee_invoices.main_invoice_number',          'like', "%{$keyword}%")
                            ->orWhere('fee_invoices.payment_status',           'like', "%{$keyword}%")
                            ->orWhere('fee_invoices.invoice_type',             'like', "%{$keyword}%")
                            ->orWhere('fee_invoices.notes',                    'like', "%{$keyword}%")
                            ->orWhere('students.student_name',                 'like', "%{$keyword}%")
                            ->orWhere('students.admission_number',             'like', "%{$keyword}%")
                            ->orWhere('student_categories.category_name',      'like', "%{$keyword}%")
                            ->orWhere('branches.name',                         'like', "%{$keyword}%")
                            ->orWhere('fee_types.name',                        'like', "%{$keyword}%")
                            ->orWhere('courses.name',                          'like', "%{$keyword}%")
                            ->orWhere('invoices_payments.payment_invoice_number', 'like', "%{$keyword}%");
                    });
                }
            }, true)

            ->addColumn('action', function ($row) {
                $returnButton = '';
                if ($row->invoice_type !== 'item_sell') {
                    $returnButton = '<a class="dropdown-item" data-toggle="modal" data-target="#returnFeeModal"
                       data-invoice-id="' . $row->id . '"
                       data-student-id="' . ($row->student_id ?? '') . '"
                       data-student-name="' . ($row->student_name ?? '') . '"
                       data-admission-number="' . ($row->admission_number ?? '') . '"
                       data-paid-amount="' . ($row->paid_amount ?? 0) . '">Return</a>';
                }

                return '
            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">Action</button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" data-toggle="modal" data-target="#viewPaymentHistoryModal"
                       data-invoice-id="' . $row->id . '">View Payment</a>
                    <a class="dropdown-item" data-toggle="modal" data-target="#makeInvoiceModal"
                       data-student-id="' . ($row->student_id ?? '') . '"
                       data-invoice-id="' . $row->id . '">Invoice</a>
                    ' . $returnButton . '
                    <a class="dropdown-item" href="' . route('fees.invoice-print', $row->id) . '"
                       target="_blank">Re-Print</a>
                </div>
            </div>';
            })
            ->rawColumns(['payment_status', 'action', 'inv_payment_status'])
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fee_invoices = FeeInvoices::with('student', 'branch', 'invoice_item', 'paidCourses.course')
            ->whereHas('paidCourses') // Only include invoices that have associated paid courses
            ->get()
            ->groupBy(function ($invoice) {
                $studentId = $invoice->student_id;
                $courseId = optional(optional($invoice->paidCourses->first())->course)->id ?? 'no_course';
                return "{$studentId}-{$courseId}";
            })
            ->map(function ($group) {
                // Get the latest invoice from each group
                return $group->sortByDesc('id')->first();
            });

        return view('pages.student.fees.fee-invoice', compact('fee_invoices'));
    }

    /**
     * Display the invoice report.
     */
    public function invoiceReport()
    {
        $fee_invoices = FeeInvoices::with('student', 'branch', 'invoice_item', 'payments')->get();
        return view('pages.student.fees.fee-invoice-report', compact('fee_invoices'));
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Find invoice
            $invoice = FeeInvoices::findOrFail($id);

            // Delete related invoice items
            InvoiceItem::where('invoice_id', $invoice->id)->delete();

            // Delete related invoice payments
            InvoicesPayments::where('invoice_id', $invoice->id)->delete();

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
            $invoice = FeeInvoices::with('invoice_item', 'payments')->findOrFail($invoiceId); // Eager load payments

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
                'bank_payment_date' => 'nullable|date',
                'bank_payment_amount' => 'nullable|numeric|min:0',
            ]);

            // Additional validation for Bank Transfer
            if ($request->payment_method === 'Bank Transfer') {
                $request->validate([
                    'bank_payment_date' => 'required|date',
                    'bank_payment_amount' => 'required|numeric|min:0',
                ]);
            }

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
            // $receipt_number = 'REC-' . time() . '-' . uniqid();
            $receipt_number = $this->generateReceiptNumber($invoice->branch_id);

            Log::debug('Generated receipt_number (addPayment): ' . $receipt_number);

            $payment_create_user_id = $request->input('create_user_id');
            if (!$payment_create_user_id && Auth::check()) {
                $payment_create_user_id = Auth::id();
            }
            Log::debug('Auth::id() for payment (addPayment): ' . $payment_create_user_id);


            InvoicesPayments::create([
                'invoice_id' => $invoice->id,
                'amount' => $totalPaidNow,
                'payment_method' => $request->payment_method,
                'payment_type' => $invoice->invoice_type,
                'payment_file' => $paymentFile,
                'receipt_number' => $receipt_number,
                'create_user_id' => $payment_create_user_id, // Save the authenticated user's ID
                'bank_payment_date' => $request->bank_payment_date,
                'bank_payment_amount' => $request->bank_payment_amount,
                'branch_id' => $invoice->branch_id,
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
        $invoice = FeeInvoices::with('payments')->findOrFail($invoiceId);
        $invoice->refresh(); // Ensure the invoice and its relationships are reloaded from the database
        $payments = $invoice->payments;

        $totalPaidAmount = $payments->sum('amount');
        $dueAmount = $invoice->total_amount - $totalPaidAmount;

        Log::debug('getPaymentHistory: Payments array before returning:', ['payments' => $payments->toArray()]); // Debug log

        return response()->json([
            'total_amount' => $invoice->total_amount,
            'paid_amount_sum' => $totalPaidAmount, 
            'due_amount' => $dueAmount,
            'main_invoice_number' => $invoice->main_invoice_number, 
            'invoice_type' => $invoice->invoice_type,
            'payments' => $payments,
        ]);
    }

    public function refundPayment(Request $request, $paymentId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);



        try {
            $originalPayment = InvoicesPayments::findOrFail($paymentId);
            $invoice = FeeInvoices::findOrFail($originalPayment->invoice_id);

            $create_user_id = Auth::check() ? Auth::id() : null;
            $user_branch_ids = UserBranch::where('user_id', $create_user_id)
                ->pluck('branch_id')
                ->toArray();

            if (count($user_branch_ids) == 1) {
                if ($user_branch_ids[0] != $originalPayment->branch_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized: You do not have permission to refund this payment from a different branch.',
                    ], 403);
                }
            } else {
                // User has MULTIPLE branches → use invoice branch
                $branch_id = $originalPayment->branch_id;
            }


            // Calculate the maximum possible refund amount based on various constraints
            $requestedRefundAmount = $request->amount;
            $maxRefundFromOriginalPayment = $originalPayment->amount;
            $maxRefundFromInvoicePaid = $invoice->paid_amount;
            $maxRefundToReachTotalDue = $invoice->total_amount - $invoice->due_amount; // Amount that can be added to due without exceeding total

            // The actual refund amount should be the minimum of all these constraints
            $refundAmount = min(
                $requestedRefundAmount,
                $maxRefundFromOriginalPayment,
                $maxRefundFromInvoicePaid,
                $maxRefundToReachTotalDue
            );

            // Ensure refundAmount is not negative or zero after min calculation
            if ($refundAmount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid refund amount. Cannot refund zero or negative amount, or refund exceeds available balance.',
                ], 400);
            }

            // Create a new payment record for the refund with a negative amount
            $receipt_number = 'REF-' . time() . '-' . uniqid();


            $user_branch_ids = UserBranch::where('user_id', $create_user_id)
                ->pluck('branch_id')
                ->toArray();

            if (count($user_branch_ids) == 1) {
                // User has only ONE branch → use that branch
                $branch_id = $user_branch_ids[0];
            } else {
                // User has MULTIPLE branches → use invoice branch
                $branch_id = $invoice->branch_id;
            }
            InvoicesPayments::create([
                'invoice_id' => $invoice->id,
                'amount' => -$refundAmount, // Store as a negative amount
                'payment_method' => $originalPayment->payment_method ?? '-', // Or a specific refund method
                'payment_type' => 'refund', // Mark as a refund
                'receipt_number' => $originalPayment->receipt_number ?? $receipt_number,
                'create_user_id' => $create_user_id,
                'payment_file' => $originalPayment->payment_file,
                'bank_payment_date' => $originalPayment->bank_payment_date,
                'bank_payment_amount' => $originalPayment->bank_payment_amount,
                'payment_invoice_number' => $originalPayment->payment_invoice_number,
                'created_at' => now(),
                'updated_at' => now(),
                'branch_id' => $branch_id
            ]);

            // Update the original payment record's status to 'refunded' when a refund is processed
            $originalPayment->payment_status = 'refunded';
            $originalPayment->save();

            // Recalculate invoice's paid_amount and due_amount based on all payments
            $allPayments = InvoicesPayments::where('invoice_id', $invoice->id)->get();
            $newNetPaidAmount = $allPayments->sum('amount'); // Sum of all payments (positive and negative)

            // The invoice's total_amount should NOT be changed. Use its original value.
            $originalInvoiceTotalAmount = $invoice->total_amount;

            $invoice->paid_amount = $newNetPaidAmount;
            $invoice->due_amount = $originalInvoiceTotalAmount - $newNetPaidAmount;

            // Ensure paid_amount does not go below zero
            $invoice->paid_amount = max(0, $invoice->paid_amount);
            // Ensure due_amount does not go below zero or exceed original total
            $invoice->due_amount = max(0, $invoice->due_amount);
            $invoice->due_amount = min($originalInvoiceTotalAmount, $invoice->due_amount);

            // Update payment status based on new paid_amount relative to original total_amount
            if ($invoice->paid_amount <= 0) {
                $invoice->payment_status = 'not';
            } elseif ($invoice->paid_amount < $originalInvoiceTotalAmount) {
                $invoice->payment_status = 'partial';
            } else {
                $invoice->payment_status = 'full';
            }
            // IMPORTANT: Do NOT update $invoice->total_amount here. It should remain the original value.
            $invoice->save();

            if ($invoice->invoice_type == 'food') {
                $food_payment = FoodPayment::where('invoice_id', $invoice->id)->first();

                $food_payment->paid_amount = $newNetPaidAmount;
                $food_payment->due_amount = $food_payment->total_food_amount - $newNetPaidAmount;
                $food_payment->status = $newNetPaidAmount <= 0 ? 0 : 1;
                $food_payment->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment refunded successfully!',
                'refund_amount' => $refundAmount,
                'original_payment_amount_in_record' => $originalPayment->amount, // This will be the original amount
                'new_invoice_paid_amount' => $invoice->paid_amount,
                'new_invoice_due_amount' => $invoice->due_amount,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error processing refund: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
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
            $invoice = FeeInvoices::findOrFail($request->invoice_id);
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
            $payment = InvoicesPayments::findOrFail($request->payment_id);
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


    public function markAsPaid(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:fee_invoices,id',
            'paid_amount' => 'required|numeric|min:0',
            'paid_date' => 'required|date_format:Y-m-d',
        ]);

        try {
            $invoice = FeeInvoices::findOrFail($request->invoice_id);

            // Update invoice details
            $invoice->paid_amount = $invoice->total_amount; // Set paid_amount to total_amount
            $invoice->payment_status = 'full'; // Set status to 'full'
            $invoice->paid_date = Carbon::createFromFormat('Y-m-d', $request->paid_date)->format('Y-m-d');
            $invoice->save();

            // Create a new payment record
            $payment_create_user_id = Auth::check() ? Auth::id() : null; // Get authenticated user ID
            InvoicesPayments::create([
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



    /**
     * Get return fee data for a specific invoice
     */
    public function getReturnFeeData($invoiceId)
    {
        try {
            $returnFees = \App\Models\ReturnFee::with('returnUser', 'invoice')
                ->where('invoice_id', $invoiceId)
                ->get();

            return response()->json([
                'success' => true,
                'return_fees' => $returnFees,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching return fee data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching return fee data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get invoice report data for a specific invoice
     */
    public function invoicePrint($invoiceId)
    {
        try {
            $invoice = FeeInvoices::with(['student', 'branch', 'invoice_item.fee_type', 'payments', 'paidCourses.course'])
                ->findOrFail($invoiceId);


            $courseName = 'N/A';
            $created_at = 'N/A';
            $expiration_date = 'N/A';
            $created_to_due = 'N/A';

            try {
                if ($invoice->paidCourses && $invoice->paidCourses->first()) {
                    $course = $invoice->paidCourses->first()->course;

                    $courseName = $course->name ?? 'N/A';

                    // Format created_at as date
                    $created_at = $course->created_at
                        ? Carbon::parse($course->created_at)->format('Y-m-d')
                        : 'N/A';

                    // Get expiration_date (integer number of days)
                    $expiration_date = is_numeric($course->expiration_date)
                        ? (int)$course->expiration_date
                        : 'N/A';

                    // Add expiration days to created_at
                    if ($created_at !== 'N/A' && is_int($expiration_date)) {
                        $created_to_due = Carbon::parse($created_at)
                            ->addDays($expiration_date)
                            ->format('Y-m-d');
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error calculating due date: ' . $e->getMessage());
            }

            return view('pages.student.fees.invoice', [
                'success' => true,
                'invoice' => $invoice,
                'invoice_items' => $invoice->invoice_item,
                'payments' => $invoice->payments,
                'course_name' => $courseName,
                'created_at' => $created_at,
                'expiration_date' => $expiration_date,
                'created_to_due' => $created_to_due,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching invoice report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching invoice report',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function invoiceItemPrint($invoiceId, $paymentId)
    {
        try {
            $invoice = InvoicesPayments::with(['invoice', 'invoice.student', 'invoice.branch', 'invoice.paidCourses.course', 'invoice.student.studentCategory'])
                ->where('id', $paymentId)
                ->where('invoice_id', $invoiceId)
                ->firstOrFail();

            $courseName = 'N/A';
            $created_at = 'N/A';
            $expiration_date = 'N/A';
            $created_to_due = 'N/A';

            try {

                if ($invoice->invoice->paidCourses && $invoice->invoice->paidCourses->first()) {
                    $course = $invoice->invoice->paidCourses->first()->course;

                    $courseName = $course->name ?? 'N/A';

                    // Format created_at as date
                    $created_at = $course->created_at
                        ? Carbon::parse($course->created_at)->format('Y-m-d')
                        : 'N/A';

                    // Get expiration_date (integer number of days)
                    $expiration_date = is_numeric($course->expiration_date)
                        ? (int)$course->expiration_date
                        : 'N/A';

                    // Add expiration days to created_at
                    if ($created_at !== 'N/A' && is_int($expiration_date)) {
                        $created_to_due = Carbon::parse($created_at)
                            ->addDays($expiration_date)
                            ->format('Y-m-d');
                    }
                }

                if ($invoice->invoice->invoice_type == 'food') {

                    $food_payment = FoodPayment::where('invoice_id', $invoice->invoice_id)->first();

                    $is_food_payment =  true;
                    $has_breakfast = $food_payment->has_breakfast ? true : false;
                    $has_lunch = $food_payment->has_lunch ? true : false;
                    $has_dinner = $food_payment->has_dinner ? true : false;
                    $lunchbox_number = $food_payment->lunchbox_number ?? 'N/A';
                } else {
                    $is_food_payment = false;
                    $has_breakfast = false;
                    $has_lunch = false;
                    $has_dinner = false;
                    $lunchbox_number = '-';
                }
            } catch (\Exception $e) {
                Log::error('Error calculating due date: ' . $e->getMessage());
            }

            return view('pages.student.fees.item-invoice', [
                'success' => true,
                'invoice' => $invoice,
                'invoice_items' => $invoice->invoice_item,
                'payments' => $invoice->payments,
                'course_name' => $courseName,
                'created_at' => $created_at,
                'expiration_date' => $expiration_date,
                'created_to_due' => $created_to_due,
                'is_food_payment' => $is_food_payment,
                'has_breakfast' => $has_breakfast,
                'has_lunch' => $has_lunch,
                'has_dinner' => $has_dinner,
                'lunchbox_number' => $lunchbox_number,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching invoice report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching invoice report',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    private function generateReceiptNumber($branch_id)
    {
        $branch = \App\Models\Branch::findOrFail($branch_id);
        $code = strtolower(substr($branch->name, 0, 2)); // ka

        // Extract ONLY the numeric part after "ka-"
        $maxNumber = \App\Models\InvoicesPayments::whereHas('invoice', function ($q) use ($branch_id) {
            $q->where('branch_id', $branch_id);
        })
            ->whereIn('payment_type', ['course', 'fee'])
            ->where('receipt_number', 'like', $code . '-%')
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(receipt_number, '-', -1) AS UNSIGNED)) as max_no")
            ->value('max_no');

        $next = ((int) $maxNumber) + 1;

        return $code . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }


    public function confirmInvoicePayment($paymentId)
    {
        $payment = InvoicesPayments::findOrFail($paymentId);
        $payment->invoice_payment_confirm = 1;
        $payment->save();

        return response()->json([
            'success' => true,
            'payment_id' => $payment->id,
            'course_name' => $payment->course->name ?? null
        ]);
    }
}
