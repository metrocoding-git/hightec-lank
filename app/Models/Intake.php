<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intake extends Model
{
    use HasFactory;
    //
    protected $table = 'intakes';

    protected $fillable = ['name', 'branch_id', 'student_count'];

     // Relationship: Intake belongs to a Branch
     public function branch()
     {
         return $this->belongsTo(Branch::class, 'branch_id');
     }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_intake', 'intake_id', 'course_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_courses');
    }

    public function assignSubjects()
    {
        return $this->hasMany(AssignSubject::class, 'intake_id');
    }

}
