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

        // FIX (pagination — same pattern as Courses/Intake): carry the
        // dt_page value through to the redirect so the list lands back on
        // whatever page you were on instead of always resetting to page 1.
        return redirect()->route('subject.index', ['dt_page' => $request->input('dt_page', 0)])
            ->with('message-success', 'Subject added successfully.');
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

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:T,P',
            'demonstrator_time' => 'nullable|integer|min:0',
            'practical_time' => 'nullable|integer|min:0',
        ]);

        $subject->update($request->only(['name', 'code', 'type', 'demonstrator_time', 'practical_time']));

        // FIX (pagination): same as store() above.
        return redirect()->route('subject.index', ['dt_page' => $request->input('dt_page', 0)])
            ->with('message-success', 'Subject updated successfully!');
    }

    public function edit($id)
    {
        $subject = Subject::withCount('assignSubjects')->findOrFail($id);

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

    /**
     * Replaces Delete in the UI, same reasoning as CourseController /
     * IntakeController's toggleStatus(): flips is_active instead of
     * removing the row. The view only ever shows this action for subjects
     * that are NOT currently assigned (isAssigned() == false) — the
     * existing "In Use" badge/gating for assigned subjects is left exactly
     * as it was, so this method is unreachable for an assigned subject
     * through the normal UI. Guarded here too in case the route is hit
     * directly.
     *
     * Uses direct property assignment + save() rather than update(), so it
     * doesn't depend on 'is_active' being in the Subject model's
     * $fillable (haven't seen that model) — mass-assignment guarding is
     * bypassed entirely for this one explicit, controller-owned field flip.
     */
    public function toggleStatus(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        if ($subject->isAssigned()) {
            return redirect()->route('subject.index', ['dt_page' => $request->input('dt_page', 0)])
                ->with('message-error', 'This subject is assigned to a class/teacher and cannot be deactivated.');
        }

        $subject->is_active = ! $subject->is_active;
        $subject->save();

        return redirect()->route('subject.index', ['dt_page' => $request->input('dt_page', 0)])
            ->with('message-success', $subject->is_active ? 'Subject activated.' : 'Subject deactivated.');
    }
}