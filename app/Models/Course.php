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

    protected $fillable = ['name', 'code', 'price', 'description'];

    public function intakes()
    {
        return $this->belongsToMany(Intake::class, 'course_intake', 'course_id', 'intake_id');
    }


}
