<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCategory extends Model
{
      protected $table = 'student_categories';

    protected $fillable = ['category'];
}
