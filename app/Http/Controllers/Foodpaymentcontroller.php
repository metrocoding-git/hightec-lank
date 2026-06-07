<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\FeeInvoices;
use App\Models\FoodPayment;
use App\Models\Intake;
use App\Models\InvoicesPayments;
use App\Models\LunchboxSequence;
use App\Models\Student;
use App\Models\StudentCourse;

class Foodpaymentcontroller extends Controller
{
    public function storeFoodPayment(Request $request)
    {
        $request->validate([
            'branch_id'        => 'required',
            'student_id'       => 'required',
            'create_date'      => 'required|date_format:m/d/Y',
            'due_date'         => 'required|date_format:m/d/Y',
            'payment_status'   => 'required',
            'course_id'        => 'required|exists:courses,id',
            'payment_method'   => 'required',
            'food_from_date'   => 'required|date_format:Y-m-d',
            'food_to_date'     => 'required|date_format:Y-m-d|after_or_equal:food_from_date',
            'has_breakfast'    => 'nullable|boolean',
            'has_lunch'        => 'nullable|boolean',
            'has_dinner'       => 'nullable|boolean',
            'breakfast_amount' => 'nullable|numeric|min:0',
            'lunch_amount'     => 'nullable|numeric|min:0',
            'dinner_amount'    => 'nullable|numeric|min:0',
            'paid_amount'      => 'required|numeric|min:0',
            'payment_file'     => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'bank_paid_amount' => 'nullable|numeric|min:0',
            'bank_paid_date'   => 'nullable|date_format:Y-m-d',
            'notes'            => 'nullable|string',
        ]);

        // Must have at least one meal selected
        if (!$request->has_breakfast && !$request->has_lunch && !$request->has_dinner) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one meal (breakfast, lunch, or dinner).',
            ], 422);
        }

        try {
            $course = Course::findOrFail($request->course_id);

            // Verify course has food fee enabled
            if (!$course->has_food_fee) {
                return response()->json([
                    'success' => false,
                    'message' => 'This course does not have food fee enabled.',
                ], 422);
            }

            $paymentMethod   = $request->payment_method;
            $paidAmount      = (float) $request->paid_amount;
            $hasBreakfast    = (bool) $request->has_breakfast;
            $hasLunch        = (bool) $request->has_lunch;
            $hasDinner       = (bool) $request->has_dinner;
            $breakfastAmount = $hasBreakfast ? (float) ($request->breakfast_amount ?? $course->breakfast_amount ?? 0) : 0;
            $lunchAmount     = $hasLunch     ? (float) ($request->lunch_amount     ?? $course->lunch_amount     ?? 0) : 0;
            $dinnerAmount    = $hasDinner    ? (float) ($request->dinner_amount    ?? $course->dinner_amount    ?? 0) : 0;

            // Calculate number of days in the range
            $fromDate  = \Carbon\Carbon::createFromFormat('Y-m-d', $request->food_from_date);
            $toDate    = \Carbon\Carbon::createFromFormat('Y-m-d', $request->food_to_date);
            $days      = $fromDate->diffInDays($toDate) + 1; // inclusive

            $totalFoodAmount = ($breakfastAmount + $lunchAmount + $dinnerAmount) * $days;
            $dueAmount       = $totalFoodAmount - $paidAmount;

            $createUserId  = \Auth::id();
            $student       = \App\Models\Student::findOrFail($request->student_id);

            // ------------------------------------------------------------------
            // Check for existing food invoice for this student + course
            // ------------------------------------------------------------------
            $existingInvoice = FeeInvoices::join('food_payments', 'fee_invoices.id', '=', 'food_payments.invoice_id')
                ->where('fee_invoices.student_id', $request->student_id)
                ->where('fee_invoices.invoice_type', 'food')
                ->where('fee_invoices.payment_status', '!=', 'returned')
                ->where('food_payments.course_id', $request->course_id)
                ->select('fee_invoices.id as fee_invoice_id', 'food_payments.id as food_payment_id')
                ->first();

            $lunchboxNumber = null;

            if ($existingInvoice) {
                // --- UPDATE existing records ---
                $feeInvoice  = FeeInvoices::findOrFail($existingInvoice->fee_invoice_id);
                $foodPayment = FoodPayment::findOrFail($existingInvoice->food_payment_id);

                $foodPayment->paid_amount    += $paidAmount;
                $foodPayment->due_amount     -= $paidAmount;
                $foodPayment->payment_status  = $foodPayment->paid_amount >= $foodPayment->total_food_amount ? 'full' : 'partial';
                $foodPayment->payment_method  = $paymentMethod;
                $foodPayment->save();

                $feeInvoice->paid_amount    += $paidAmount;
                $feeInvoice->due_amount     -= $paidAmount;
                $feeInvoice->payment_status  = $feeInvoice->paid_amount >= $feeInvoice->total_amount ? 'full' : 'partial';
                $feeInvoice->payment_method  = $paymentMethod;
                $feeInvoice->save();

                $lunchboxNumber = $foodPayment->lunchbox_number;
            } else {
                // --- CREATE new invoice + food_payment ---
                do {
                    $mainInvoiceNumber = 'INV-' . rand(100000, 999999);
                } while (FeeInvoices::where('main_invoice_number', $mainInvoiceNumber)->exists());

                $feeInvoice = FeeInvoices::create([
                    'branch_id'           => $request->branch_id,
                    'invoice_type'        => 'food',
                    'student_id'          => $request->student_id,
                    'student_category_id' => $student->student_category_id,
                    'create_date'         => \Carbon\Carbon::createFromFormat('m/d/Y', $request->create_date)->format('Y-m-d'),
                    'due_date'            => \Carbon\Carbon::createFromFormat('m/d/Y', $request->due_date)->format('Y-m-d'),
                    'payment_status'      => $request->payment_status,
                    'payment_method'      => $paymentMethod,
                    'total_amount'        => $totalFoodAmount,
                    'paid_amount'         => $paidAmount,
                    'due_amount'          => $dueAmount,
                    'main_invoice_number' => $mainInvoiceNumber,
                    'create_user_id'      => $createUserId,
                    'payment_file'        => null,
                    'notes'               => $request->notes,
                ]);

                // Assign lunchbox number at creation time only
                $lunchboxNumber = $this->generateLunchboxNumber($request->branch_id);

                FoodPayment::create([
                    'invoice_id'        => $feeInvoice->id,
                    'course_id'         => $request->course_id,
                    'from_date'         => $fromDate->format('Y-m-d'),
                    'to_date'           => $toDate->format('Y-m-d'),
                    'has_breakfast'     => $hasBreakfast,
                    'has_lunch'         => $hasLunch,
                    'has_dinner'        => $hasDinner,
                    'breakfast_amount'  => $breakfastAmount,
                    'lunch_amount'      => $lunchAmount,
                    'dinner_amount'     => $dinnerAmount,
                    'total_food_amount' => $totalFoodAmount,
                    'paid_amount'       => $paidAmount,
                    'due_amount'        => $dueAmount,
                    'payment_status'    => $request->payment_status,
                    'payment_method'    => $paymentMethod,
                    'lunchbox_number'   => $lunchboxNumber,
                ]);
            }

            // ------------------------------------------------------------------
            // Handle file upload
            // ------------------------------------------------------------------
            $paymentFile = null;
            if ($request->hasFile('payment_file')) {
                $paymentFile = $request->file('payment_file')->store('payments', 'public');
            }

            // ------------------------------------------------------------------
            // Bank payment fields
            // ------------------------------------------------------------------
            $bankPaidAmount = null;
            $bankPaidDate   = null;
            if ($paymentMethod === 'Bank Transfer') {
                $bankPaidAmount = $request->bank_paid_amount;
                if (!empty($request->bank_paid_date)) {
                    $bankPaidDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->bank_paid_date)->format('Y-m-d');
                }
            }

            $receiptNumber = $this->generateLunchboxNumber($request->branch_id, 'food');

            $payment = InvoicesPayments::create([
                'invoice_id'          => $feeInvoice->id,
                'amount'              => $paidAmount,
                'payment_method'      => $paymentMethod,
                'payment_type'        => 'food',
                'payment_file'        => $paymentFile,
                'receipt_number'      => $receiptNumber,
                'create_user_id'      => $createUserId,
                'bank_payment_amount' => $bankPaidAmount,
                'bank_payment_date'   => $bankPaidDate,
                'branch_id'           => $request->branch_id,
                'notes'               => $request->notes,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            return response()->json([
                'success'         => true,
                'message'         => 'Food payment saved successfully!',
                'invoice_id'      => $feeInvoice->id,
                'payment_id'      => $payment->id,
                'lunchbox_number' => $lunchboxNumber,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Food payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function generateLunchboxNumber(int $branchId): string
    {
        $now   = now();
        $year  = (int) $now->format('Y');
        $month = (int) $now->format('n');  // 1-12

        // Lock the row so concurrent requests don't issue the same number
        $seq = \DB::transaction(function () use ($branchId, $year, $month) {
            $seq = LunchboxSequence::lockForUpdate()
                ->where('branch_id', $branchId)
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            if (!$seq) {
                // Determine letter: find the most recent sequence for this branch
                // to carry the letter forward month-to-month.
                $previous = LunchboxSequence::where('branch_id', $branchId)
                    ->where(function ($q) use ($year, $month) {
                        $q->where('year', '<', $year)
                            ->orWhere(function ($q2) use ($year, $month) {
                                $q2->where('year', $year)->where('month', '<', $month);
                            });
                    })
                    ->orderByDesc('year')
                    ->orderByDesc('month')
                    ->first();

                // Advance letter from previous month, or start at A
                if ($previous) {
                    $nextLetter = $previous->letter === 'Z' ? 'A' : chr(ord($previous->letter) + 1);
                } else {
                    $nextLetter = 'A';
                }

                $seq = LunchboxSequence::create([
                    'branch_id'   => $branchId,
                    'year'        => $year,
                    'month'       => $month,
                    'letter'      => $nextLetter,
                    'last_number' => 0,
                ]);
            }

            $seq->last_number += 1;
            $seq->save();

            return $seq;
        });

        return $seq->letter . '-' . $seq->last_number;
    }
}
