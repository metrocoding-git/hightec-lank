<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Topic;

class TopicController extends Controller
{
    public function index()
    {
        $courses = Course::get();
        $subjects = Subject::get();
        $intakes = Intake::get();
        $lessons = Lesson::get();
        $topics= Topic::get();
        $topics = $topics->groupBy(function (Topic $item) {
            return $item->intake_id . '-' . $item->course_id . '-' . $item->subject_id . '-' . $item->lesson_id;
        })->values();

        return view('pages.administration.lesson-plan.topic', [
            'courses' => $courses,
            'subjects' => $subjects,
            'intakes' => $intakes,
            'lessons' => $lessons,
            'topics' => $topics
        ]);
    }
   public function store(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id',
        'intake_id' => 'required|exists:intakes,id',
        'subject_id' => 'required|exists:subjects,id',
        'lesson_id' => 'required|exists:lessons,id',
        'topic' => 'required|array|min:1',
        'topic.*' => 'required|string|max:255',
    ]);


   $exists = Topic::where('intake_id', $request->intake_id)
    ->where('course_id', $request->course_id)
    ->where('subject_id', $request->subject_id)
    ->where('lesson_id', $request->lesson_id)
    ->exists();

if (!$exists) {
    foreach ($request->topic as $topicName) {
        Topic::create([
            'course_id' => $request->course_id,
            'intake_id' => $request->intake_id,
            'subject_id' => $request->subject_id,
            'lesson_id' => $request->lesson_id,
            'name' => $topicName,
        ]);
    }
    return redirect()->route('topic.index')->with('message-success', 'Topics added successfully!');
} else {
    return redirect()->back()->with('message-danger', 'One or more topics with the same Subject, Course, Intake, and Lesson already exist.');
}

}



    public function update(Request $request, $id)
{
    $request->validate([
        'course_id'   => 'required|exists:courses,id',
        'intake_id'   => 'required|exists:intakes,id',
        'subject_id'  => 'required|exists:subjects,id',
        'lesson_id'   => 'required|exists:lessons,id',
        'topic'       => 'required|array|min:1',
        'topic.*'     => 'required|string|max:255',
    ]);

    // Find the original topic (just to confirm it exists)
    $topic = Topic::findOrFail($id);

    // Get all existing topics for the same course + intake + subject + lesson
    $existingTopics = Topic::where('course_id', $topic->course_id)
        ->where('intake_id', $topic->intake_id)
        ->where('subject_id', $topic->subject_id)
        ->where('lesson_id', $topic->lesson_id)
        ->get();

    $newTopics = $request->topic;
    $existingCount = $existingTopics->count();
    $newCount = count($newTopics);

    // Update existing topics
    foreach ($existingTopics as $index => $existingTopic) {
        if (isset($newTopics[$index])) {
            $existingTopic->update([
                'name'       => $newTopics[$index],
                'course_id'  => $request->course_id,
                'intake_id'  => $request->intake_id,
                'subject_id' => $request->subject_id,
                'lesson_id'  => $request->lesson_id,
            ]);
        } else {
            // Remove extra old topics not in new list
            $existingTopic->delete();
        }
    }

    // Add any new topics if there are more than existing
    if ($newCount > $existingCount) {
        for ($i = $existingCount; $i < $newCount; $i++) {
            Topic::create([
                'name'       => $newTopics[$i],
                'course_id'  => $request->course_id,
                'intake_id'  => $request->intake_id,
                'subject_id' => $request->subject_id,
                'lesson_id'  => $request->lesson_id,
            ]);
        }
    }

    return redirect()->route('topic.index')->with('message-success', 'Topics updated successfully!');
}



 public function edit($id)
{
  
    $topic = Topic::findOrFail($id);

    $courses = Course::all();
    $subjects = Subject::all();
    $intakes = Intake::all();
    $lessons = Lesson::all();



    // topics grouped by intake_id, course_id, subject_id, lesson_id
    $topics = Topic::all()->groupBy(function (Topic $item) {
        return $item->intake_id . '-' . $item->course_id . '-' . $item->subject_id . '-' . $item->lesson_id;
    })->values();

    // all topics that belong to the same group as the topic we want to edit
    $groupTopics = Topic::where('course_id', $topic->course_id)
        ->where('intake_id', $topic->intake_id)
        ->where('subject_id', $topic->subject_id)
        ->where('lesson_id', $topic->lesson_id)
        ->get();

    

    return view('pages.administration.lesson-plan.topic', [
        'courses' => $courses,
        'subjects' => $subjects,
        'intakes' => $intakes,
        'lessons' => $lessons,
        'topics' => $topics,
        'topic' => $topic,
        'groupTopics' => $groupTopics
    ]);
}

    public function destroy($id)
{
    // Find the topic first to get related IDs
    $topic = Topic::findOrFail($id);

    $selectedIntake = $topic->intake_id;
    $selectedCourse = $topic->course_id;
    $selectedSubject = $topic->subject_id;
    $selectedLesson = $topic->lesson_id;

    // Delete all topics with the same intake, course, subject and lesson
    Topic::where('intake_id', $selectedIntake)
        ->where('course_id', $selectedCourse)
        ->where('subject_id', $selectedSubject)
        ->where('lesson_id', $selectedLesson)
        ->delete();

    return redirect()->route('topic.index')->with('message-success-delete', 'Topics deleted successfully.');
}





}
