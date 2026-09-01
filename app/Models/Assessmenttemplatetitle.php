<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentTemplateTitle extends Model
{
    protected $fillable = [
        'assessment_template_id',
        'title',
        'max_marks',
        'sort_order',
    ];

    public function template()
    {
        return $this->belongsTo(AssessmentTemplate::class, 'assessment_template_id');
    }

    public function subTitles()
    {
        return $this->hasMany(AssessmentTemplateSubTitle::class, 'assessment_template_title_id')->orderBy('sort_order');
    }
}