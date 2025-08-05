<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    //
    protected $table = 'lessons';

    protected $fillable = ['name','intake_id', 'subject_id', 'course_id'];

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
