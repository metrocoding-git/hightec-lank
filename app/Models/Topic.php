<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    //
     protected $table = 'topics';

    protected $fillable = ['name','intake_id', 'subject_id', 'course_id', 'lesson_id'];
    
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class, 'intake_id');
    }

   public function lesson()
{
    return $this->belongsTo(Lesson::class, 'lesson_id');
}

}

