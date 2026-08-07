<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectAttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'intake_id',
        'course_id',
        'subject_id',
        'attendance_date',
        'time_in',
        'time_out',
        'vehicle_id',
        'note',
        'marked_in_by',
        'marked_out_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(TrainingVehicle::class, 'vehicle_id');
    }

    public function markedInBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'marked_in_by');
    }

    public function markedOutBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'marked_out_by');
    }

    public function scopeOpen($query)
    {
        return $query->whereNull('time_out');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', now()->toDateString());
    }

    /** Duration in minutes, null while still open (no time_out yet). */
    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->time_in || !$this->time_out) {
            return null;
        }

        return Carbon::parse($this->time_in)->diffInMinutes(Carbon::parse($this->time_out));
    }

    /** Human readable "Hh Mm" duration string. */
    public function getDurationLabelAttribute(): string
    {
        $minutes = $this->duration_minutes;

        if ($minutes === null) {
            return '—';
        }

        $hours = intdiv($minutes, 60);
        $remaining = $minutes % 60;

        return ($hours > 0 ? $hours . 'h ' : '') . $remaining . 'm';
    }
}
