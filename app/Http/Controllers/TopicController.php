<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Lesson;
use App\Models\Subject;

class TopicController extends Controller
{
    public function index()
    {
        $courses = Course::get();
        $subjects = Subject::get();
        $intakes = Intake::get();
        $lessons = Lesson::get();

        return view('pages.administration.lesson-plan.topic', [
            'courses' => $courses,
            'subjects' => $subjects,
            'intakes' => $intakes,
            'lessons' => $lessons
        ]);
    }
    public function store(Request $request)
    {
       
    }


    public function update(Request $request, $id)
    {
        
    }


    public function edit($id)
    {

       
    }


    public function destroy($id)
    {
       
    }
}
