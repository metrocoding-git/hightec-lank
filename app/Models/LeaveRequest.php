<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $table = 'leave_requests';

    public const TYPES = [
        'Annual Leave',
        'Casual Leave',
        'Medical Leave',
        'Short Leave',
        'Duty Leave',
        'No Pay Leave',
        'Other',
    ];

    public const DAY_PARTS = [
        'full' => 'Full day(s)',
        'first_half' => 'Half day — morning',
        'second_half' => 'Half day — afternoon',
    ];

    public const STATUSES = ['pending', 'approved', 'rejected', 'cancelled'];

    protected $fillable = [
        'user_id',
        'branch_id',
        'leave_type',
        'from_date',
        'to_date',
        'day_part',
        'days',
        'reason',
        'contact_during_leave',
        'attachment',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'reviewed_at' => 'datetime',
        'days' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Requests a reviewer may see: staff in the same branch(es) as the
     * reviewer, plus requests with no branch. A reviewer with no branch
     * assigned sees everything.
     */
    public function scopeVisibleToReviewer($query, User $reviewer)
    {
        $branchIds = $reviewer->branches()->pluck('branches.id');

        if ($branchIds->isEmpty()) {
            return $query;
        }

        return $query->where(function ($q) use ($branchIds) {
            $q->whereIn('branch_id', $branchIds)->orWhereNull('branch_id');
        });
    }

    public function dayPartLabel(): string
    {
        return self::DAY_PARTS[$this->day_part] ?? $this->day_part;
    }

    public function statusLabel(): string
    {
        return ['approved' => 'Accepted', 'rejected' => 'Not Accepted'][$this->status] ?? ucfirst($this->status);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'approved' => 'lv-approved',
            'rejected' => 'lv-rejected',
            'cancelled' => 'lv-cancelled',
            default => 'lv-pending',
        };
    }
}
