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

    public function getIntakesByCourse($courseId)
    {
        try {
            $intakes = Intake::whereHas('courses', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })->get();

            return response()->json([
                'success' => true,
                'intakes' => $intakes,
                'course_id' => $courseId,
                'count' => $intakes->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'course_id' => $courseId
            ], 500);
        }
    }

    public function getSubjectsByIntake($intakeId)
    {
        try {
            $subjects = Subject::whereHas('assignSubjects', function ($query) use ($intakeId) {
                $query->where('intake_id', $intakeId);
            })->get();

            return response()->json([
                'success' => true,
                'subjects' => $subjects,
                'intake_id' => $intakeId,
                'count' => $subjects->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'intake_id' => $intakeId
            ], 500);
        }
    }
}