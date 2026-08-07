<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'vehicle_number',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sessions()
    {
        return $this->hasMany(SubjectAttendanceSession::class, 'vehicle_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
