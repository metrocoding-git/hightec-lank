<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Student;
use App\Models\StudentCourse;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::get();

        return view('pages.administration.academic.course', [
            'courses' => $courses
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'expiration_date' => 'required|numeric',
            'description' => 'nullable|string',
            'duration' => 'nullable|string',
            'has_nvq_payment' => 'required|boolean',
            'nvq_amount' => 'nullable|numeric',
            'has_food_fee'       => 'required|boolean',
            'breakfast_amount'   => 'nullable|numeric|min:0',
            'lunch_amount'       => 'nullable|numeric|min:0',
            'dinner_amount'      => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        if (!isset($data['code']) || empty($data['code'])) {
            $data['code'] = 'co-' . strtoupper(uniqid()); // Generate a unique code
        }

        Course::create($data);

        return redirect()->back()->with('message-success', 'Course added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'expiration_date' => 'required|numeric',
            'description' => 'nullable|string',
            'duration' => 'nullable|string',
            'has_nvq_payment' => 'required|boolean',
            'nvq_amount' => 'nullable|numeric',
            'has_food_fee'       => 'required|boolean',
            'breakfast_amount'   => 'nullable|numeric|min:0',
            'lunch_amount'       => 'nullable|numeric|min:0',
            'dinner_amount'      => 'nullable|numeric|min:0',
        ]);

        $course = Course::findOrFail($id);
        $data = $request->all();
        if (!isset($data['code']) || empty($data['code'])) {
            $data['code'] = 'co-' . strtoupper(uniqid()); // Generate a unique code
        }
        $course->update($data);

        return redirect()->route('course.index')->with('message-success', 'Course updated successfully!');
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $courses = Course::get();
        return view('pages.administration.academic.course', ['course' => $course, 'courses' => $courses, 'courseId' => $id,]);
    }


    public function destroy($id)
    {
        Course::findOrFail($id)->delete();
        return redirect()->route('course.index')->with('message-success-delete', 'Course deleted successfully.');
    }

    public function getCoursesByBranch($branchId)
    {
        try {
            $intakes = Intake::where('branch_id', $branchId)->pluck('id');
            $courses = Course::whereHas('intakes', function ($q) use ($intakes) {
                $q->whereIn('intake_id', $intakes);
            })->get(['id', 'name', 'price']);

            return response()->json([
                'success' => true,
                'courses' => $courses,
                'branch_id' => $branchId,
                'count' => $courses->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'branch_id' => $branchId
            ], 500);
        }
    }

    public function getCoursesByStudent($studentId)
    {
        try {
            $student = Student::find($studentId);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'error' => 'Student not found',
                    'student_id' => $studentId
                ], 404);
            }

            // Step 1 — Get latest record ID for each course
            $latest = StudentCourse::where('student_id', $studentId)
                ->selectRaw('MAX(id) as id')
                ->groupBy('course_id')
                ->pluck('id');

            // Step 2 — Load only these records with relations
            $records = StudentCourse::with(['course', 'intake', 'change_course'])
                ->whereIn('id', $latest)
                ->get();

            // Step 3 — Format response
            $courses = $records->map(function ($item) {
                return [
                    'course_id' => $item->course->id,
                    'course_name' => $item->course->name,
                    'course_price' => $item->course->price,
                    'intake_id' => $item->intake?->id,
                    'intake_name' => $item->intake?->name,
                    'has_nvq_payment' => $item->course->has_nvq_payment,
                    'nvq_amount' => $item->course->nvq_amount,
                    'change_course' => $item->change_course?->name ?? '',
                    'status' => $item->status
                ];
            });

            return response()->json([
                'success' => true,
                'courses' => $courses,
                'count' => $courses->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'student_id' => $studentId
            ], 500);
        }
    }

    public function getCoursesByIntake($intakeId)
    {
        try {
            $courses = Course::whereHas('intakes', function ($query) use ($intakeId) {
                $query->where('intake_id', $intakeId);
            })->get();

            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }

    public function getCoursesByIntakeAndStudent($intakeId, $studentId)
    {
        try {
            $courses = Course::whereHas('intakes', function ($query) use ($intakeId) {
                $query->where('intake_id', $intakeId);
            })
                ->whereHas('students', function ($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                })
                ->get();

            $coursesWithDates = $courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'created_at' => $course->created_at ? $course->created_at->toISOString() : null,
                    'expiration_date' => $course->expiration_date,
                ];
            });

            return response()->json($coursesWithDates);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }
}
