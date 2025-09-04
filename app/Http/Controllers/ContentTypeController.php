<?php

namespace App\Http\Controllers;

use App\Models\Content_Type;
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
        $content_Types = Content_Type::latest()->get();
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

        Content_Type::create($request->only('name', 'description'));

        return redirect()->back()->with('success', 'Content Type added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Content_Type $content_Type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content_Type $content_Type, $id)
    {
        //
        $type = Content_Type::findOrFail($id);
        $content_Types = Content_Type::latest()->get();
        return view('pages.administration.download-center.content-type', compact('content_Types', 'type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content_Type $content_Type, $id)
    {
        //
        $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
        ]);

        $type = Content_Type::findOrFail($id);
        $type->update($request->only('name', 'description'));

        return redirect()->back()->with('success', 'Content Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content_Type $content_Type, $id)
    {
        //
        $type = Content_Type::findOrFail($id);
        $type->delete();

        return redirect()->back()->with('success', 'Content Type deleted successfully.');
    }
}
