<?php

namespace App\Http\Controllers;

use App\Models\BloodGroup;
use App\Models\Branch;
use App\Models\Gender;
use App\Models\Intake;
use App\Models\Student;
use App\Models\StudentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AddStudentController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        $genders = Gender::all();
        $categories = StudentCategory::all();
        $bloodGroups = BloodGroup::all();
        $intakes = Intake::all();
        return view('pages.student.student-info.add-student', compact('branches', 'genders', 'categories', 'bloodGroups', 'intakes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'admission_number' => 'required|string|unique:students,admission_number',
            'admission_date' => 'required|date',
            'student_category_id' => 'required|exists:student_categories,id',
            'gender_id' => 'required|exists:genders,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'photo' => 'nullable|image|max:2048',
            'email_address' => 'required|email|max:255|unique:students,email_address',
            'phone_number' => 'required|string|max:20|unique:students,phone_number',
            'current_address' => 'required|string',
            'permanent_address' => 'required|string',
            'blood_group_id' => 'nullable|string|max:10',
            'height' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'guardians_name' => 'required|string|max:255',
            'relation_guardian_id' => 'nullable|string|max:50',
            'guardians_email' => 'required|email|max:255',
            'guardians_photo' => 'nullable|image|max:2048',
            'guardians_phone' => 'required|string|max:20',
            'guardians_occupation' => 'required|string|max:255',
            'guardians_address' => 'required|string',
            'national_id_number' => 'required|string|max:12',
            'additional_notes' => 'nullable|string',
            'document_title_1' => 'nullable|string|max:255',
            'document_file_1' => 'nullable|file|max:2048',
            'document_title_2' => 'nullable|string|max:255',
            'document_file_2' => 'nullable|file|max:2048',
            'document_title_3' => 'nullable|string|max:255',
            'document_file_3' => 'nullable|file|max:2048',
            'document_title_4' => 'nullable|string|max:255',
            'document_file_4' => 'nullable|file|max:2048',
            'previous_institute_details' => 'nullable|string',
            'dormitory_id' => 'nullable|string|max:100',
            'sibling_id' => 'nullable|exists:students,id'
        ]);

        // Handle file uploads
        $fileFields = [
            'photo' => 'student_photos',
            'guardians_photo' => 'guardian_photos',
            'document_file_1' => 'documents',
            'document_file_2' => 'documents',
            'document_file_3' => 'documents',
            'document_file_4' => 'documents'
        ];

        foreach ($fileFields as $field => $folder) {
            if ($request->hasFile($field)) {
                $validatedData[$field] = $request->file($field)->store($folder, 'public');
            }
        }

        // Combine first and last name
        $validatedData['student_name'] = $validatedData['first_name'] . ' ' . $validatedData['last_name'];

        Student::create($validatedData);
        return redirect()->route('student.index')->with('success', 'Student created successfully');
    }


    public function edit(Student $student)
    {
        return view('pages.student.student-info.edit-student', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validatedData = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'admission_number' => 'required|string|unique:students,admission_number',
            'admission_date' => 'required|date',
            'student_category_id' => 'required|exists:student_categories,id',
            'gender_id' => 'required|exists:genders,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'photo' => 'nullable|image|max:2048',
            'email_address' => 'required|email|max:255|unique:students,email_address',
            'phone_number' => 'required|string|max:20|unique:students,phone_number',
            'current_address' => 'required|string',
            'permanent_address' => 'required|string',
            'blood_group_id' => 'nullable|string|max:10',
            'height' => 'nullable|string|max:10',
            'weight' => 'nullable|string|max:10',
            'guardians_name' => 'required|string|max:255',
            'relation_guardian_id' => 'nullable|string|max:50',
            'guardians_email' => 'required|email|max:255',
            'guardians_photo' => 'nullable|image|max:2048',
            'guardians_phone' => 'required|string|max:20',
            'guardians_occupation' => 'required|string|max:255',
            'guardians_address' => 'required|string',
            'national_id_number' => 'required|string|max:12',
            'additional_notes' => 'nullable|string',
            'document_title_1' => 'nullable|string|max:255',
            'document_file_1' => 'nullable|file|max:2048',
            'document_title_2' => 'nullable|string|max:255',
            'document_file_2' => 'nullable|file|max:2048',
            'document_title_3' => 'nullable|string|max:255',
            'document_file_3' => 'nullable|file|max:2048',
            'document_title_4' => 'nullable|string|max:255',
            'document_file_4' => 'nullable|file|max:2048',
            'previous_institute_details' => 'nullable|string',
            'dormitory_id' => 'nullable|string|max:100',
            'sibling_id' => 'nullable|exists:students,id'
        ]);

        // Handle file uploads
        $fileFields = [
            'photo' => 'student_photos',
            'guardians_photo' => 'guardian_photos',
            'document_file_1' => 'documents',
            'document_file_2' => 'documents',
            'document_file_3' => 'documents',
            'document_file_4' => 'documents'
        ];

        foreach ($fileFields as $field => $folder) {
            if ($request->hasFile($field)) {
                if ($student->$field) {
                    Storage::disk('public')->delete($student->$field);
                }
                $validatedData[$field] = $request->file($field)->store($folder, 'public');
            }
        }

        // Combine first and last name
        $validatedData['student_name'] = $validatedData['first_name'] . ' ' . $validatedData['last_name'];

        $student->update($validatedData);
        return redirect()->route('student.index')->with('success', 'Student updated successfully');
    }
}
