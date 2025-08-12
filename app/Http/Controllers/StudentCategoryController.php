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

            return redirect()->route('student-category.index')->with('message-success', 'Category added successfully!');
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

        return redirect()->route('student-category.index')
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
}
