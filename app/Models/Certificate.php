<?php
/**
 * ============================================================================
 * REPLACES the Certificate.php delivered last round — place at:
 * app/Models/Certificate.php
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_assessment_id',
        'certificate_number',
        'status',
        'conductor_signature_path',
        'conductor_signed_by',
        'conductor_signed_at',
        'owner_signature_path',
        'owner_signed_by',
        'owner_signed_at',
        'pdf_path',
        'generated_by',
        'generated_at',
        'downloaded_by',
        'downloaded_at',
        'download_count',
        'certificate_template_id',
        'issue_date'
    ];

    protected $casts = [
        'generated_at'        => 'datetime',
        'conductor_signed_at' => 'datetime',
        'owner_signed_at'     => 'datetime',
        'downloaded_at'       => 'datetime',
    ];

    public function studentAssessment()
    {
        return $this->belongsTo(StudentAssessment::class, 'student_assessment_id');
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function conductorSignedBy()
    {
        return $this->belongsTo(User::class, 'conductor_signed_by');
    }

    public function ownerSignedBy()
    {
        return $this->belongsTo(User::class, 'owner_signed_by');
    }

    public function downloadedByUser()
    {
        return $this->belongsTo(User::class, 'downloaded_by');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function needsConductorSignature(): bool
    {
        return $this->status === 'generated';
    }

    public function needsOwnerSignature(): bool
    {
        return $this->status === 'conductor_signed';
    }

    public function isDownloaded(): bool
    {
        return !is_null($this->downloaded_at);
    }

     public function certificateTemplate()
    {
        return $this->belongsTo(\App\Models\CertificateTemplate::class);
    }
}