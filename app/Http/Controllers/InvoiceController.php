<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\FeesGroup;
use App\Models\FeeType;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return view('pages.student.fees.add-invoice', compact('branches', 'feesgroups' , 'feeTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice, $branchId)
    {
        //

        $student = Student::where('branch_id', $branchId)
            ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as name"))
            ->get();
        return response()->json($student);
    }

    public function dropdown_fee_types(Invoice $invoice, $groupId)
    {
        $feetypes = FeeType::where('fee_group_id', $groupId)->get(['id', 'name']); 
        return response()->json($feetypes);
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
