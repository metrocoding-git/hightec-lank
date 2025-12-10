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
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'student_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\User', 'issue_to', 'id');
    }

    public function intake()
    {
        return $this->belongsTo('App\Models\Intake', 'intake_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id', 'id');
    }

    public function items()
    {
        return $this->hasMany('App\Models\ItemIssueItem', 'batch_id', 'id');
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
