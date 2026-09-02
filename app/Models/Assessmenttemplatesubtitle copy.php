<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentTemplateSubTitle extends Model
{
    protected $fillable = [
        'assessment_template_title_id',
        'sub_title',
        'sort_order',
    ];

    public function title()
    {
        return $this->belongsTo(AssessmentTemplateTitle::class, 'assessment_template_title_id');
    }
}