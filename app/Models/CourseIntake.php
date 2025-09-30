<?php

namespace App\Models;

use App\Models\Course;
use App\Models\Intake;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseIntake extends Model
{

    use HasFactory;
    //
    protected $table = 'course_intake';

    protected $fillable = ['course_id', 'intake_id', 'start_date', 'end_date'];


    public function course() {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function intake() {
        return $this->belongsTo(Intake::class, 'intake_id');
    }

}
