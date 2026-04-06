<?php

namespace App\Http\Controllers;

use App\Models\Intake;
use App\Models\VideoList;
use Illuminate\Http\Request;

class VideoListController extends Controller
{
    public function videoList(Request $request)
    {
        $intakes = Intake::all();
        $videosQuery = VideoList::with(['intake:id,name', 'courses:id,name'])->orderByDesc('id');

        if ($request->filled('title')) {
            $title = $request->input('title');
            $videosQuery->where('title', 'like', "%{$title}%");
        }

        $videos = $videosQuery->get();
        return view('pages.administration.download-center.video-list', compact('intakes', 'videos'));
    }

    public function videoListStore(Request $request)
    {
        $validated = $request->validate([
            'intake_id' => 'required|exists:intakes,id',
            'title' => 'required|string|max:255',
            'video_link' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'integer|exists:courses,id',
        ]);

        try {
            $video = VideoList::create([
                'intake_id' => $validated['intake_id'],
                'title' => $validated['title'],
                'video_link' => $validated['video_link'],
                'description' => $validated['description'] ?? null,
            ]);

            $video->courses()->sync($validated['course_ids']);

            return response()->json([
                'status' => true,
                'message' => 'Video saved successfully',
                'id' => $video->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to save video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function videoListDestroy($id)
    {
        $video = VideoList::findOrFail($id);
        $video->delete();
        return redirect()->route('video-list')->with('success', 'Video deleted successfully');
    }

    public function videoListUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'intake_id' => 'required|exists:intakes,id',
            'title' => 'required|string|max:255',
            'video_link' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'integer|exists:courses,id',
        ]);

        try {
            $video = VideoList::findOrFail($id);
            $video->update([
                'intake_id' => $validated['intake_id'],
                'title' => $validated['title'],
                'video_link' => $validated['video_link'],
                'description' => $validated['description'] ?? null,
            ]);

            $video->courses()->sync($validated['course_ids']);

            return response()->json([
                'status' => true,
                'message' => 'Video updated successfully',
                'id' => $video->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
