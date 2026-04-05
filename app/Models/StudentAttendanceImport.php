<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendanceImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'intake_id',
        'course_id',
        'attendance_date',
        'file_name',
        'file_path',
        'uploaded_by',
    ];

    public function intake()
    {
        return $this->belongsTo(Intake::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
