<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function createUser()
    {
        $roles = Role::all();
        $students = Student::all(); // Fetch all students
        return view('pages.role_management.create-user', compact('roles', 'students'));
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
            'email_address' => 'required|email|unique:users,email', // Changed to email_address for input, but maps to 'email' in users table
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'admission_number' => 'nullable|string|max:255|unique:students,admission_number,' . $request->search_student,
            'national_id_number' => 'nullable|string|max:255|unique:students,national_id_number,' . $request->search_student,
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
                'name' => $student->first_name . ' ' . $student->last_name,
                'email' => $request->email_address, // Map email_address to user's email
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            $student->update(['user_id' => $user->id]);

        } else {
            // Create new user and student
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email_address, // Map email_address to user's email
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            if ($role->name === 'Student') {
                Student::create([
                    'user_id' => $user->id,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email_address' => $request->email_address, // Assuming students table has email_address
                    'admission_number' => $request->admission_number,
                    'national_id_number' => $request->national_id_number,
                ]);
            }
        }

        return redirect()->route('roles.index')->with('success', 'User created successfully!');
    }
}
