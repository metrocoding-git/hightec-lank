<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemIssueBatch extends Model
{
    protected $fillable = [
        'branch_id',
        'student_id',
        'issue_to',
        'intake_id',
        'course_id',
        'issue_date',
        'due_date',
        'description',
        'issue_status',
        'active_status',
        'created_by',
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'issue_to', 'id');
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class, 'intake_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(ItemIssueItem::class, 'batch_id', 'id');
    }

    // Helper methods
    public function getTotalItemsAttribute()
    {
        return $this->items()->sum('quantity');
    }

    public function getStatusTextAttribute()
    {
        return $this->issue_status === 'I' ? 'Issued' : 'Returned';
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
