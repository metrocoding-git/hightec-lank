<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCourse extends Model
{
    protected $table = 'student_courses';

    protected $fillable = [
        'student_id',
        'course_id',
        'intake_id',
        'branch_id',
        'status',
        'notes',
        'user_id',
        'change_course_id',
        'updated_by',
        'student_category_id'
    ];


    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function change_course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'change_course_id');
    }

    public function intake(): BelongsTo
    {
        return $this->belongsTo(Intake::class, 'intake_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function studentCategory(): BelongsTo
    {
        return $this->belongsTo(StudentCategory::class, 'student_category_id');
    }
}
