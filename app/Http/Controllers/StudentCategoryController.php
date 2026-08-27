<?php

namespace App\Http\Controllers;

use App\Models\StudentCategory;
use Illuminate\Http\Request;

class StudentCategoryController extends Controller
{
    public function index()
    {
        $categories = StudentCategory::all();
        return view('pages.student.student-info.student-category', compact('categories'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'category' => 'required|string|max:255|unique:student_categories,category_name',
        ]);

        try {
            $category = new StudentCategory();
            $category->category_name = $request->category;
            $category->save();

            // FIX (pagination — same pattern as the other academic/student
            // pages): carry the dt_page value through to the redirect so
            // the list lands back on whatever page you were on instead of
            // always resetting to page 1.
            return redirect()->route('student-category.index', ['dt_page' => $request->input('dt_page', 0)])
                ->with('message-success', 'Category added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('message-error', __('common.operation_failed'));
        }
    }


    public function edit($id)
    {
        $categories = StudentCategory::all();
        $student_type = StudentCategory::findOrFail($id);
        return view('pages.student.student-info.student-category', compact('categories', 'student_type'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category' => 'required|string|max:255|unique:student_categories,category_name,' . $id
        ]);

        $category = StudentCategory::findOrFail($id);
        $category->category_name = $request->category;
        $category->save();

        // FIX (pagination): same as store() above.
        return redirect()->route('student-category.index', ['dt_page' => $request->input('dt_page', 0)])
            ->with('message-success', __('student.category_updated_successfully'));
    }

    public function destroy($id)
    {
        try {
            $category = StudentCategory::findOrFail($id);
            $category->delete();

            return redirect()->route('student-category.index')
                ->with('message-success', __('student.category_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->route('student-category.index')
                ->with('message-error', __('common.operation_failed'));
        }
    }

    /**
     * Replaces Delete in the UI, same reasoning as the other pages'
     * toggleStatus() methods: student categories are very likely
     * referenced by existing Student records, so an actual delete risks
     * breaking historical data. Flips is_active instead of removing the
     * row.
     *
     * Uses direct property assignment + save() rather than update(), so it
     * doesn't depend on 'is_active' being in the StudentCategory model's
     * $fillable (haven't seen that model) — mass-assignment guarding is
     * bypassed entirely for this one explicit, controller-owned field flip.
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $category = StudentCategory::findOrFail($id);
            $category->is_active = ! $category->is_active;
            $category->save();

            return redirect()->route('student-category.index', ['dt_page' => $request->input('dt_page', 0)])
                ->with('message-success', $category->is_active ? 'Category activated.' : 'Category deactivated.');
        } catch (\Exception $e) {
            return redirect()->route('student-category.index', ['dt_page' => $request->input('dt_page', 0)])
                ->with('message-error', __('common.operation_failed'));
        }
    }
}