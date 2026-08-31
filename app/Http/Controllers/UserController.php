<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Student;
use App\Models\Branch;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function createUser()
    {
        $roles = Role::all();
        $students = Student::all(); // Fetch all students
        $branches = Branch::all();
        $courses = Course::all();
        return view('pages.role_management.create-user', compact('roles', 'students', 'branches', 'courses'));
    }

    public function getStudentDetails($id)
    {
        $student = Student::find($id);
        return response()->json([
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'admission_number' => $student->admission_number,
            'national_id_number' => $student->national_id_number,
            'email_address' => $student->email_address,
        ]);
    }

    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'branch_ids' => 'required|array',
            'branch_ids.*' => 'exists:branches,id',
            'email_address' => 'required|email|unique:users,email', // Changed to email_address for input, but maps to 'email' in users table
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'admission_number' => $request->search_student ? 'nullable' : 'nullable|string|max:255|unique:students,admission_number',
            'national_id_number' => $request->search_student ? 'nullable' : 'nullable|string|max:255|unique:students,national_id_number',
            'search_student' => 'nullable|exists:students,id',
            // COURSES (assign-course feature): optional, one or more courses.
            // Backed by the new `user_course` table via User::courses().
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role = Role::findOrFail($request->role_id);

        if ($request->search_student) {
            // Update existing student and create user
            $student = Student::findOrFail($request->search_student);
            $student->update([
                'email_address' => $request->email_address, // Assuming students table has email_address
            ]);

            $user = User::create([
                'name' => $student->first_name, // Save only first name to 'name' column as requested
                'first_name' => $student->first_name, // Populate first_name column
                'last_name' => $student->last_name, // Populate last_name column
                'email' => $request->email_address, // Map email_address to user's email
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            $user->branches()->attach($request->branch_ids);

            $student->update(['user_id' => $user->id]);

        } else {
            // Create new user and student
            $user = User::create([
                'name' => $request->first_name, // Save only first name to 'name' column as requested
                'first_name' => $request->first_name, // Populate first_name column
                'last_name' => $request->last_name, // Populate last_name column
                'email' => $request->email_address, // Map email_address to user's email
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            $user->branches()->attach($request->branch_ids);

            if ($role->name === 'Student') {
                Student::create([
                    'user_id' => $user->id,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email_address' => $request->email_address, // Assuming students table has email_address
                    'admission_number' => $request->admission_number,
                    'national_id_number' => $request->national_id_number,
                    'branch_id' => $request->branch_ids[0], // For student, assign first branch or handle differently
                ]);
            }
        }

        // COURSES (assign-course feature): optional — a user can be created
        // with zero courses. Only attaches when at least one was actually
        // checked; on a brand-new user there's nothing to remove, so
        // attach() (not sync()) is enough here.
        if ($request->filled('course_ids')) {
            $user->courses()->attach($request->course_ids);
        }

        return redirect()->route('roles.index')->with('success', 'User created successfully!');
    }

    public function userList()
    {
        // COURSES (assign-course feature): eager-load courses too, now that
        // the User List table shows each user's assigned courses.
        $users = User::with(['role', 'branches', 'courses'])->get();
        $courses = Course::all();
        return view('pages.role_management.user-list', compact('users', 'courses'));
    }

    public function getUserDetails($id)
    {
        $user = User::with(['role', 'branches', 'courses'])->findOrFail($id);

        $response = $user->toArray();
        // COURSES (assign-course feature): flat array of course ids this
        // user currently has, so the Edit User modal can pre-select them.
        $response['course_ids'] = $user->courses->pluck('id')->toArray();

        return response()->json($response);
    }

    public function updateUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'branch_ids' => 'required|array',
            'branch_ids.*' => 'exists:branches,id',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            // COURSES (assign-course feature): optional, one or more courses.
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->name = $request->first_name; // Update 'name' field as well
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->status = $request->status;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $user->branches()->sync($request->branch_ids);

        // COURSES (assign-course feature): full sync() here, not just
        // attach() — unlike student_courses (which is also depended on by
        // Add Intake & Courses / item-issue eligibility and therefore only
        // ever gets additive updates, per Section 39's flagged limitation),
        // `user_course` is a brand-new, standalone table nothing else reads
        // yet. So it's safe for the Edit User form to behave exactly like a
        // normal checkbox list: whatever is checked on submit is exactly
        // what the user ends up with, including removals.
        $user->courses()->sync($request->course_ids ?? []);

        return response()->json(['success' => true, 'message' => 'User updated successfully!']);
    }

    public function changePassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password updated successfully!']);
    }
}