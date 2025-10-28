<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\FeeInvoice;
use Illuminate\Http\Request;

class StudentViewController extends Controller
{
    public function studentView($id)
    {
        $student = Student::with(['branch', 'gender', 'studentCategory', 'bloodGroup', 'courses', 'intakes'])->findOrFail($id);

        // Fetch payment history
        $payments = FeeInvoice::where('student_id', $id)->with(['invoice_item.fee_type'])->get();

        // Fetch detailed course information for the student
        $courses = $student->courses()->with(['intakes'])->get();

        // Fetch detailed intake information for the student
        $intakes = $student->intakes()->with(['branch', 'courses'])->get();

        return view('pages.student.student-info.student-view', compact('student', 'payments', 'courses', 'intakes'));
    }
}
