<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'name',
        'course_name_line1',
        'course_name_line2',
        'duration',
        'bottom_image_path',
        'qr_code_image_path',
        'back_side_pdf_path',
        'serial_prefix_id',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Kept for backward compatibility with the old single-course design
     * (the course_id column is still there, just unused by new code) —
     * anything elsewhere in the app still reading ->course on an OLD
     * template row keeps working. New code should use ->courses() below.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * NEW — a template can now be linked to many courses (requirement
     * #1), via the certificate_template_course pivot table. "One course,
     * one active template" (requirement #2) is enforced in
     * CertificateTemplateController::findCourseConflict(), not here.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'certificate_template_course')->withTimestamps();
    }

    public function serialPrefix()
    {
        return $this->belongsTo(CertificateSerialPrefix::class, 'serial_prefix_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}