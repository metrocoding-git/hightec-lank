<?php

namespace App\Models;

use App\Models\Course;
use App\Models\Intake;
use Illuminate\Database\Eloquent\Model;

class VideoList extends Model
{
    protected $fillable = ['intake_id', 'title', 'video_link', 'description'];

    public function intake()
    {
        return $this->belongsTo(Intake::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'video_list_courses');
    }
}
