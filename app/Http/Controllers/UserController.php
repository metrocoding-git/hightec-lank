<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Student;
use App\Models\Branch;
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
        return view('pages.role_management.create-user', compact('roles', 'students', 'branches'));
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

        return redirect()->route('roles.index')->with('success', 'User created successfully!');
    }

    public function userList()
    {
        $users = User::with(['role', 'branches'])->get();
        return view('pages.role_management.user-list', compact('users'));
    }

    public function getUserDetails($id)
    {
        $user = User::with(['role', 'branches'])->findOrFail($id);
        return response()->json($user);
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
