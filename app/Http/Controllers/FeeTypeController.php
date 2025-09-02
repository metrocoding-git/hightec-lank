<?php

namespace App\Http\Controllers;

use App\Models\FeeType;
use App\Models\FeesGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
     
   
       $feesTypes = FeeType::all();
        $feesGroups = FeesGroup::all();
        return view('pages.student.fees.fee-type', compact('feesTypes', 'feesGroups'));
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create( Request $request)
    {
        //
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
       // dd($request->all());
         $request->validate([
            'name' => 'required|string|max:255',
            'fee_group_id' => 'required',
            'price' => 'min:0',
            'description' => 'nullable|string',
        ]);

        FeeType::create($request->all());
        return redirect()->route('fees.fees-type')->with('success', 'Fee Type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FeeType $feeType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeeType $feeType, $id)
    {
        //
        $feesType = FeeType::findOrFail($id);
        $feesGroups = FeesGroup::all();
        $feesTypes = FeeType::with('feeGroup')->get();

       
        return view('pages.student.fees.fee-type', compact('feesType', 'feesGroups', 'feesTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FeeType $feeType, $id)
    {
        //
    
      
        $request->validate([
            'name' => 'required|string|max:255',
            'fee_group_id' => 'required|exists:fees_groups,id',
            'price' => 'min:0',
            'description' => 'nullable|string',
        ]);

        $feesType = FeeType::findOrFail($request->id);
        $feesType->update($request->all());

        return redirect()->route('fees.fees-type')->with('success', 'Fee Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeeType $feeType , Request $request, $id )
    {
        //
         $feesType = FeeType::findOrFail($request->id);
        $feesType->delete();
        return redirect()->route('fees.fees-type')->with('success', 'Fee Type deleted successfully.');
    }
}
