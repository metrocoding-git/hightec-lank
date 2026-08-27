<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FeeInvoices;
use App\Models\FeesGroup;
use App\Models\FeeType;
use App\Models\FoodPayment;
use App\Models\InvoiceItem;
use App\Models\InvoicesPayments;
use App\Models\LunchboxSequence;
use App\Models\PaidCourse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OtherPaymentController extends Controller
{
    public function create()
    {
        $branches = Auth::user()->branches;
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
            'notes' => 'nullable|string',
        ]);

        if (in_array(1, $request->fee_type_id)) {
            $existingFeeTypes = InvoiceItem::whereIn('fee_type_id', $request->fee_type_id)
                ->whereHas('fee_invoice', function ($q) use ($request) {
                    $q->where('student_id', $request->student_id);
                    $q->where('payment_status', '!=', 'returned');
                    // $q->where('paid_amount', '>', '0');


                })
                ->pluck('fee_type_id')
                ->toArray();

            if (!empty($existingFeeTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admission fee already exist.',
                    'existing_fee_type_ids' => $existingFeeTypes
                ], 422);
            }
        }


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

            do {
                $main_invoice_number = 'INV-' . rand(100000, 999999);
            } while (\App\Models\FeeInvoices::where('main_invoice_number', $main_invoice_number)->exists());
            $create_user_id = Auth::check() ? Auth::id() : null;
            Log::debug('Generated main_invoice_number (store): ' . $main_invoice_number);
            $create_user_id = Auth::id();

            // Create Fee Invoice
            $fee_invoice = FeeInvoices::create([
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
                'bank_paid_amount' => ($request->payment_method) === 'Bank Transfer' ? $request->bank_paid_amount : null,
                'bank_paid_date' => ($request->payment_method) === 'Bank Transfer' && !empty($request->bank_paid_date)
                    ? Carbon::createFromFormat('Y-m-d', $request->bank_paid_date)->format('Y-m-d')
                    : null,
                'notes' => $request->notes,

            ]);
            Log::debug('Fee_invoice created with main_invoice_number: ' . $fee_invoice->main_invoice_number . ' and create_user_id: ' . $fee_invoice->create_user_id);



            // Save Invoice Items
            foreach ($request->fee_type_id as $feetypeId) {
                $amount = $request->price[$feetypeId] ?? (FeeType::where('id', $feetypeId)->value('price') ?? 0);
                $paid_price = $request->paidPrice[$feetypeId] ?? 0;

                $invoiceItem = InvoiceItem::create([
                    'invoice_id' => $fee_invoice->id,
                    'fee_type_id' => $feetypeId,
                    'amount' => $amount,
                    'paid_amount' => $paid_price,
                ]);

                // Save initial payment in invoices_payments table if any
                if ($paid_price > 0) {
                    // Generate a unique receipt number
                    // $receipt_number = 'REC-' . time() . '-' . uniqid();
                    $receipt_number = $this->generateReceiptNumber($request->branch_id);

                    Log::debug('Generated receipt_number (store): ' . $receipt_number);

                    $payment_create_user_id = null;
                    if (Auth::check()) {
                        $payment_create_user_id = Auth::id();
                        Log::debug('Auth::id() for payment (store): ' . $payment_create_user_id);
                    } else {
                        Log::warning('No authenticated user found when creating Invoices_payments in store method.');
                    }



                    $payment = InvoicesPayments::create([
                        'invoice_id' => $fee_invoice->id,
                        // 'payment_invoice_number' => , add by modal
                        'amount' => $paid_price,
                        'payment_method' => $request->payment_method,
                        'payment_type' => 'fee',
                        'payment_file' => ($request->payment_method === 'Card' || $request->payment_method === 'Bank Transfer')
                            && $request->file('payment_file')
                            ? $request->file('payment_file')->store('invoices', 'public')
                            : null,
                        'bank_payment_date' => ($request->payment_method === 'Bank Transfer' && !empty($request->bank_paid_date))
                            ? Carbon::createFromFormat('Y-m-d', $request->bank_paid_date)->format('Y-m-d')
                            : null,
                        'bank_payment_amount' => ($request->payment_method === 'Bank Transfer') ? $request->bank_paid_amount : null,
                        'receipt_number' => $receipt_number,
                        'create_user_id' => $payment_create_user_id, // Save the authenticated user's ID
                        'notes' => $request->notes,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'branch_id' => $request->branch_id,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please enter paid amount to generate payment record.',
                    ], 500);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice, items, and initial payments created successfully',
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

    public function show(FeeInvoices $fee_invoicese, $branchId)
    {
        $student = Student::where('branch_id', $branchId)
            ->select('id', DB::raw("CONCAT(student_name) as name"), 'admission_number')
            ->get();
        return response()->json($student);
    }
}
