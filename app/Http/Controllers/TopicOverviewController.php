<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Intake;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicOverviewController extends Controller
{
  public function index(Request $request)
{
    $courses = Course::all();
    $subjects = Subject::all();
    $intakes = Intake::all();

    $query = Topic::with('lesson');

    if ($request->filled('course_id')) {
        $query->where('course_id', $request->course_id);
    }
    if ($request->filled('intake_id')) {
        $query->where('intake_id', $request->intake_id);
    }
    if ($request->filled('subject_id')) {
        $query->where('subject_id', $request->subject_id);
    }

    $topics_detail = $query->get();

    // detect if request is AJAX and return JSON only
    if ($request->ajax()) {
        return response()->json($topics_detail);
    }

    // normal page load
    return view('pages.administration.lesson-plan.topic-overview', [
        'courses' => $courses,
        'subjects' => $subjects,
        'intakes' => $intakes,
        'topics_detail' => $topics_detail,
    ]);
}


    }
