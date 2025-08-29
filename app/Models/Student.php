<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'branch_id',
        'admission_number',
        'admission_date',
        'student_category_id',
        'gender_id',
        'first_name',
        'last_name',
        'student_name',
        'date_of_birth',
        'photo',
        'email_address',
        'phone_number',
        'current_address',
        'permanent_address',
        'blood_group_id',
        'height',
        'weight',
        'guardians_name',
        'relation_guardian_id',
        'guardians_email',
        'guardians_photo',
        'guardians_phone',
        'guardians_occupation',
        'guardians_address',
        'national_id_number',
        'additional_notes',
        'document_title_1',
        'document_file_1',
        'document_title_2',
        'document_file_2',
        'document_title_3',
        'document_file_3',
        'document_title_4',
        'document_file_4',
        'previous_institute_details',
        'dormitory_id',
        'sibling_id',
        'is_active'
    ];

    public function scopeActive(Builder $query)
    {
        $query->where('is_active', true);
    }

    public function scopeInactive(Builder $query)
    {
        $query->where('is_active', false);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function studentCategory(): BelongsTo
    {
        return $this->belongsTo(StudentCategory::class);
    }

    public function bloodGroup(): BelongsTo
    {
        return $this->belongsTo(BloodGroup::class);
    }

public function intakes()
{
    return $this->belongsToMany(Intake::class, 'course_intake', 'course_id', 'intake_id')
                ->where('branch_id', $this->branch_id);
}

public function courses()
{
    return $this->belongsToMany(Course::class, 'course_intake', 'intake_id', 'course_id');
}
}
