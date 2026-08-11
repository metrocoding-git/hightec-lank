<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::get();

        return view('pages.administration.academic.subject', [
            'subjects' => $subjects
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:T,P',
            'demonstrator_time' => 'nullable|integer|min:0',
            'practical_time' => 'nullable|integer|min:0',
        ]);

        $subject = new Subject();
        $subject->name = $request->name;
        $subject->code = $this->generateSubjectCode();
        $subject->type = $request->type;
        $subject->demonstrator_time = $request->demonstrator_time;
        $subject->practical_time = $request->practical_time;
        $subject->save();

        return redirect()->route('subject.index')->with('message-success', 'Subject added successfully.');
    }

    private function generateSubjectCode(): string
    {
        $next = (Subject::max('id') ?? 0) + 1;

        do {
            $code = 'SUB' . str_pad($next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (Subject::where('code', $code)->exists());

        return $code;
    }


    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        if ($subject->isAssigned()) {
            return redirect()->route('subject.index')
                ->with('message-error', 'This subject is assigned to a class/teacher and cannot be edited.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:T,P',
            'demonstrator_time' => 'nullable|integer|min:0',
            'practical_time' => 'nullable|integer|min:0',
        ]);

        $subject->update($request->only(['name', 'code', 'type', 'demonstrator_time', 'practical_time']));

        return redirect()->route('subject.index')->with('message-success', 'Subject updated successfully!');
    }

    public function edit($id)
    {
        $subject = Subject::withCount('assignSubjects')->findOrFail($id);

        if ($subject->isAssigned()) {
            return redirect()->route('subject.index')
                ->with('message-error', 'This subject is assigned to a class/teacher and cannot be edited.');
        }

        $subjects = Subject::withCount('assignSubjects')->get();

        return view('pages.administration.academic.subject', [
            'subjects' => $subjects,
            'subject' => $subject,
            'subjectId' => $id,
        ]);
    }


    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);

        if ($subject->isAssigned()) {
            return redirect()->route('subject.index')
                ->with('message-error', 'This subject is assigned to a class/teacher and cannot be deleted.');
        }

        $subject->delete();

        return redirect()->route('subject.index')->with('message-success-delete', 'Subject deleted successfully.');
    }
}
