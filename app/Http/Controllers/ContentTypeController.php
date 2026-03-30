<?php

namespace App\Http\Controllers;

use App\Models\ContentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class ContentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $content_Types = ContentType::latest()->get();
        return view('pages.administration.download-center.content-type', compact('content_Types'));
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
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
        ]);

        ContentType::create($request->only('name', 'description'));

        return redirect()->back()->with('success', 'Content Type added successfully.');
    }

   
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContentType $content_Type, $id)
    {
        //
        $type = ContentType::findOrFail($id);
        $content_Types = ContentType::latest()->get();
        return view('pages.administration.download-center.content-type', compact('content_Types', 'type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContentType $content_Type, $id)
    {
        //
        $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
        ]);

        $type = ContentType::findOrFail($id);
        $type->update($request->only('name', 'description'));

        return redirect()->route('content-type')->with('success', 'Content Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContentType $content_Type, $id)
    {
        //
        $type = ContentType::findOrFail($id);
        $type->delete();

        return redirect()->back()->with('success', 'Content Type deleted successfully.');
    }
}
