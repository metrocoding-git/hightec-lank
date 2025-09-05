<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Fee_invoices;
use App\Models\FeesGroup;
use App\Models\FeeType;
use App\Models\Invoice_item;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //




        return view('pages.student.fees.fee-invoice');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $branches = Branch::all();
        $feesgroups = FeesGroup::all();
        $feeTypes = FeeType::all();
        return view('pages.student.fees.add-invoice', compact('branches', 'feesgroups', 'feeTypes'));
    }

   


    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'branch_id'      => 'required',
            'student_id'     => 'required',
            'create_date'    => 'required|date_format:m/d/Y',
            'due_date'       => 'required|date_format:m/d/Y',
            'payment_status' => 'required',
            'fee_type_id'    => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            // Total paid amount
            // $paidAmount = $request->payment_amount ?? 0;

            // Create Invoice
            $fee_invoice = Fee_invoices::create([
                'branch_id'      => $request->branch_id,
                'student_id'     => $request->student_id,
                'create_date'    => Carbon::createFromFormat('m/d/Y', $request->create_date)->format('Y-m-d'),
                'due_date'       => Carbon::createFromFormat('m/d/Y', $request->due_date)->format('Y-m-d'),
                'payment_status' => $request->payment_status,
                //'payment_amount' => $request->payment_amount,
                'payment_method' => $request->payment_method,
                'payment_file'   => ($request->payment_method === 'Card' || $request->payment_method === 'Bank Transfer')
                    && $request->file('payment_file')
                    ? $request->file('payment_file')->store('invoices', 'public')
                    : null,
            ]);

            // Save Invoice Items
            foreach ($request->fee_type_id as $index => $feetypeId) {
                // If amount entered use it, else fetch from DB
                $amount = !empty($request->price[$index])
                    ? $request->price[$index]
                    : (FeeType::where('id', $feetypeId)->value('price') ?? 0);

                // If paid_amount entered use it, else fetch from DB
                $paid_price = !empty($request->paid_amount[$index])
                    ? $request->paid_amount[$index]
                    : (FeeType::where('id', $feetypeId)->value('price') ?? null);

                Invoice_item::create([
                    'invoice_id'  => $fee_invoice->id,
                    'fee_type_id' => $feetypeId,
                    'amount'      => $amount,
                    'paid_amount' => $paid_price,
                ]);
            }

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => 'Invoice and items created successfully',
                'invoice_id' => $fee_invoice->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function show(Fee_invoices $fee_invoicese, $branchId)
    {
        //

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
    public function edit(Fee_invoices $fee_invoices)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fee_invoices $fee_invoices, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fee_invoices $fee_invoices, $invoice)
    {
        //
    }
}
