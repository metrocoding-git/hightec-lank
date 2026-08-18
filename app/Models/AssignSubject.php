<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignSubject extends Model
{
    use HasFactory;
    //
    protected $table = 'assign_subjects';

    protected $fillable = ['intake_id', 'subject_id', 'course_id', 'demonstrator_time', 'practical_time'];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class, 'intake_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

  

}
