<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content_Type;
use Illuminate\Support\Facades\Storage;
use App\Models\Role;
use App\Models\User;
use App\Models\ShareContentList;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $roles = Role::all();
        $users = User::all();
      
        return view('pages.administration.download-center.content-list', compact('content_Types','contents','roles','users'));
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
            $roles = Role::all();
        $users = User::all();
        return view('pages.administration.download-center.content-list', compact('editContent', 'content_Types','contents','roles','users'));
        
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

    /**
     * Save shared content list (Share modal)
     */
    public function shareListSave(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'shareDate' => 'required|string',
            'validUpto' => 'required|string',
            'content_ids' => 'required|array|min:1',
            'content_ids.*' => 'integer|exists:contents,id',
            'role_id' => 'required|integer|exists:roles,id',
            'individual_content_user' => 'required|array|min:1',
            'individual_content_user.*' => 'integer|exists:users,id',
        ]);

        // Parse dates (incoming as m/d/Y)
        $saveDate = Carbon::createFromFormat('m/d/Y', $validated['shareDate'])->format('Y-m-d');
        $validDate = Carbon::createFromFormat('m/d/Y', $validated['validUpto'])->format('Y-m-d');

        DB::beginTransaction();
        try {
            $share = ShareContentList::create([
                'role_id' => $validated['role_id'],
                'send_to' => 'I', 
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'save_date' => $saveDate,
                'valid_date' => $validDate,
            ]);

            // Attach users and contents via pivot tables
            $share->users()->sync($validated['individual_content_user']);
            $share->contents()->sync($validated['content_ids']);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Shared content saved successfully',
                'id' => $share->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to save shared content',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
