<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $suppliers=Supplier::all();
         return view('pages.account.inventory.supplier', compact('suppliers'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

          $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'contact_person_name' => 'required|string|max:255',
            'contact_person_mobile' => 'required|string|max:20',
            'contact_person_email' => 'required|email|unique:suppliers,contact_person_email',
        ]);

        Supplier::create($request->all());

        return redirect()->back()->with('success', 'Supplier added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier, $id)
    {
        //
         $suppliers=Supplier::all();
        $editData = Supplier::findOrFail($id);
        return view('pages.account.inventory.supplier', compact('editData', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier, $id)
    {
        //
        {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'contact_person_name' => 'required|string|max:255',
            'contact_person_mobile' => 'required|string|max:20',
            'contact_person_email' => 'required|email|unique:suppliers,contact_person_email,'.$id,
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->all());

        return redirect()->back()->with('success', 'Supplier updated successfully!');
    }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier, $id)
    {
        //
         $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->back()->with('success', 'Supplier deleted successfully!');
    }
}
