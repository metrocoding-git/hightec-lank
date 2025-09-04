<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content_Type;
use Illuminate\Support\Facades\Storage;
class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

     $content_Types = Content_Type::all();
       $contents=Content::all();
        
      
        return view('pages.administration.download-center.content-list', compact('content_Types','contents'));
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
        'file_name'       => 'required|string|max:255',
        'content_type_id' => 'required',
        'upload_file'     => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,txt,xlsx,rar,zip',
        'youtube_link'    => 'nullable|string|max:255',
    ]);

    $content = Content::create([
        'file_name'       => $request->file_name,
        'content_type_id' => $request->content_type_id,
        'upload_file'     => $request->hasFile('upload_file') 
                                ? $request->file('upload_file')->store('contents', 'public') 
                                : null,
        'file_size'       => $request->hasFile('upload_file') 
                                ? round($request->file('upload_file')->getSize() / 1024, 2) 
                                : null,
        'youtube_link'    => $request->youtube_link,
    ]);

    return redirect()->back()->with('success', 'Content added successfully!');

     
    }

    /**
     * Display the specified resource.
     */
    public function show(Content $content)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $content, $id)
    {
        //

        $editContent = Content::findOrFail($id);
        $content_Types = Content_Type::all();
            $contents=Content::all();
        
        return view('pages.administration.download-center.content-list', compact('editContent', 'content_Types','contents'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $content, $id)
    {
        //

        $content = Content::findOrFail($id);

        $request->validate([
            'file_name' => 'required|string|max:255',
            'content_type_id' => 'required',
            'youtube_link' => 'nullable|url',
            'upload_file' => 'nullable|file|mimes:jpg,png,jpeg,pdf,doc,docx,txt,xlsx,rar,zip',
        ]);

        $data = $request->only(['file_name', 'content_type_id', 'youtube_link']);

        if ($request->hasFile('upload_file')) {
            // Delete old file if exists
            if ($content->upload_file && Storage::disk('public')->exists($content->upload_file)) {
                Storage::disk('public')->delete($content->upload_file);
            }

            $file = $request->file('upload_file');
            $path = $file->store('contents', 'public');
            $data['upload_file'] = $path;
            $data['file_size'] = round($file->getSize() / 1024);
        }

        $content->update($data);

        return redirect()->route('content-list')->with('success', 'Content updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content, $id)
    {
        //

        $content = Content::findOrFail($id);

        if ($content->upload_file && Storage::disk('public')->exists($content->upload_file)) {
            Storage::disk('public')->delete($content->upload_file);
        }

        $content->delete();

        return redirect()->route('content-list')->with('success', 'Content deleted successfully!');
    }
}
