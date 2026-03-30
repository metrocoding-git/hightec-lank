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
            'code' => 'required|string|max:50|unique:subjects,code',
            'type' => 'required|in:T,P',
        ]);

        $subject = new Subject();
        $subject->name = $request->name;
        $subject->code = $request->code;
        $subject->type = $request->type;
        $subject->save();

        return redirect()->route('subject.index')->with('message-success', 'Subject added successfully.');
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $id,
            'type' => 'required|in:T,P',
        ]);

        $subject = Subject::findOrFail($id);
        $subject->update($request->all());

        return redirect()->route('subject.index')->with('message-success', 'Subject updated successfully!');
    }

    public function edit($id)
    {
        $subjects = Subject::get();
        $subject = Subject::findOrFail($id);
        return view('pages.administration.academic.subject', ['subjects' => $subjects, 'subject' => $subject, 'subjectId' => $id,]);
    }


    public function destroy($id)
    {
        Subject::findOrFail($id)->delete();
        return redirect()->route('subject.index')->with('message-success-delete', 'Subject deleted successfully.');
    }
}
