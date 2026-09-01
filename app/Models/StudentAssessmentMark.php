<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAssessmentMark extends Model
{
    protected $fillable = [
        'student_assessment_id',
        'assessment_template_title_id',
        'assessment_template_sub_title_id',
        'competency',
        'marks',
        'remarks',
    ];

    public function assessment()
    {
        return $this->belongsTo(StudentAssessment::class, 'student_assessment_id');
    }

    public function title()
    {
        return $this->belongsTo(AssessmentTemplateTitle::class, 'assessment_template_title_id');
    }

    public function subTitle()
    {
        return $this->belongsTo(AssessmentTemplateSubTitle::class, 'assessment_template_sub_title_id');
    }
}