<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentTemplate extends Model
{
    protected $fillable = [
        'course_id',
        'module_name',
        'ncs_code',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function titles()
    {
        return $this->hasMany(AssessmentTemplateTitle::class)->orderBy('sort_order');
    }

    public function createUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updateUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Sum of every main title's max_marks — the "... of 100" total for this template. */
    public function getMaxMarksAttribute(): int
    {
        return $this->titles->sum('max_marks');
    }
}