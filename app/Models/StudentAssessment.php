<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAssessment extends Model
{
    protected $fillable = [
        'student_id',
        'student_course_id',
        'assessment_template_id',
        'vehicle_id',
        'equipment_model',
        'branch_id',
        'assessed_at',
        'total_marks',
        'max_marks',
        'grade',
        'is_qualified',
        'comments',
        'evaluator_id',
        'operator_signature',
        'status',
        'created_by',
        'updated_by',
        'deleted_at'
    ];

    protected $casts = [
        'assessed_at' => 'datetime',
        'is_qualified' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentCourse()
    {
        return $this->belongsTo(StudentCourse::class);
    }

    public function template()
    {
        return $this->belongsTo(AssessmentTemplate::class, 'assessment_template_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(TrainingVehicle::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function marks()
    {
        return $this->hasMany(StudentAssessmentMark::class);
    }
}