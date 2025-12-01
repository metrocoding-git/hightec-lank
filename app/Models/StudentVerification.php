<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentVerification extends Model
{
    protected $fillable = [
        'student_id',
        'verification_item',
        'old_value',
        'new_value',
        'signature_path',
        'updated_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
