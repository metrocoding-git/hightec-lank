<?php

namespace App\Models;

use App\Models\Intake;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    //
    protected $table = 'courses';

    protected $fillable = ['name', 'code', 'price', 'description','duration','expiration_date','has_nvq_payment', 'nvq_amount'];


    public function intakes()
    {
        return $this->belongsToMany(Intake::class, 'course_intake', 'course_id', 'intake_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_courses', 'course_id', 'student_id')
                    ->withPivot('enrolled_at', 'status', 'notes')
                    ->withTimestamps();
    }
    public function videoLists()
    {
        return $this->belongsToMany(VideoList::class, 'video_list_courses');
    }



}
