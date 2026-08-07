<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignSubjects extends Model
{
    use HasFactory;
    //
    protected $table = 'assign_subjects';

    protected $fillable = ['id', 'intake_id', 'subject_id','course_id', 'created_by', 'updated_by'];


}
