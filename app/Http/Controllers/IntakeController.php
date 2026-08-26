<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Intake;

class IntakeController extends Controller
{
    public function index()
    {
        $intakes = Intake::with('courses', 'branch')->orderBy('created_at', 'asc')->get();
        $branches = Branch::all();
        $courses = Course::all();

        return view('pages.administration.academic.intake', compact('intakes', 'branches', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'courses' => 'required|array',
            'courses.*' => 'exists:courses,id',
            'date' => 'required|date',
        ]);

        $intake = Intake::create([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
            'start_date' => $request->date,
        ]);

        // Sync courses
        if ($request->courses) {
            $intake->courses()->sync($request->courses);
        }

        // FIX (pagination — same pattern as the Courses page): carry the
        // dt_page value through to the redirect so the list lands back on
        // whatever page you were on instead of always resetting to page 1.
        return redirect()->route('intake.index', ['dt_page' => $request->input('dt_page', 0)])
            ->with('message-success', 'Intake added successfully!');
    }

    public function edit($id)
    {
        $intake = Intake::with('courses')->findOrFail($id);
        $intakes = Intake::with('courses', 'branch')->get();
        $branches = Branch::all();
        $courses = Course::all();

        return view('pages.administration.academic.intake', [
            'intake' => $intake,
            'intakes' => $intakes,
            'branches' => $branches,
            'courses' => $courses,
            'intakeId' => $id,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'courses' => 'required|array',
            'courses.*' => 'exists:courses,id',
            'date' => 'required|date',
        ]);

        $intake = Intake::findOrFail($id);

        $intake->update([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
            'start_date' => $request->date,
        ]);

        // Sync courses
        if ($request->courses) {
            $intake->courses()->sync($request->courses);
        } else {
            $intake->courses()->detach();
        }

        // FIX (pagination): same as store() above.
        return redirect()->route('intake.index', ['dt_page' => $request->input('dt_page', 0)])
            ->with('message-success', 'Intake updated successfully!');
    }


    public function destroy($id)
    {
        $intake = Intake::findOrFail($id);

        $intake->courses()->detach();
        $intake->delete();

        return redirect()->route('intake.index')->with('message-success-delete', 'Intake deleted successfully!');
    }

    /**
     * Replaces Delete in the UI, same reasoning as CourseController's
     * toggleStatus(): intakes are linked to courses (and, via students,
     * to enrollment/payment history), so an actual delete risks breaking
     * historical records. This flips is_active instead of removing the
     * row or detaching its courses.
     *
     * Uses direct property assignment + save() rather than update(), so it
     * doesn't depend on 'is_active' being in the Intake model's $fillable
     * (haven't seen that model) — mass-assignment guarding is bypassed
     * entirely for this one explicit, controller-owned field flip.
     */
    public function toggleStatus(Request $request, $id)
    {
        $intake = Intake::findOrFail($id);
        $intake->is_active = ! $intake->is_active;
        $intake->save();

        return redirect()->route('intake.index', ['dt_page' => $request->input('dt_page', 0)])
            ->with('message-success', $intake->is_active ? 'Intake activated.' : 'Intake deactivated.');
    }

    public function getIntakes($branchId)
    {
        $intakes = Intake::where('branch_id', $branchId)->get(['id', 'name']);
        return response()->json($intakes);
    }

    // Get courses for an intake
    public function getCourses($intakeId)
    {
        $intake = Intake::find($intakeId);

        if (!$intake) {
            return response()->json([]);
        }

        $courses = $intake->courses()->get(['courses.id', 'courses.name']);
        return response()->json($courses);
    }

    public function getCoursesByIntake($intakeId)
    {
        try {
            $intake = Intake::find($intakeId);

            if (!$intake) {
                return response()->json([
                    'success' => false,
                    'error' => 'Intake not found',
                    'intake_id' => $intakeId
                ], 404);
            }

            $courses = $intake->courses()->get(['courses.id', 'courses.name']);

            return response()->json([
                'success' => true,
                'courses' => $courses,
                'intake_id' => $intakeId,
                'count' => $courses->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'intake_id' => $intakeId
            ], 500);
        }
    }

    public function getIntakesByBranch($branchId)
    {
        try {
            $intakes = Intake::where('branch_id', $branchId)->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'intakes' => $intakes,
                'branch_id' => $branchId,
                'count' => $intakes->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'branch_id' => $branchId
            ], 500);
        }
    }

    public function getIntakeByBranchAndStudent($branchId, $studentId)
    {
        try {
            $intakes = Intake::where('branch_id', $branchId)
                ->whereHas('students', function ($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                })
                ->get();

            $formattedIntakes = $intakes->map(function ($intake) {
                return [
                    'id' => $intake->id,
                    'name' => $intake->name,
                    'created_at' => $intake->created_at ? $intake->created_at->toISOString() : null,
                ];
            });

            return response()->json($formattedIntakes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }
}