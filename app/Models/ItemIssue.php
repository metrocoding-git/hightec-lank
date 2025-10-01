<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemIssue extends Model
{
    protected $fillable = [
        'branch_id',
        'issue_to',
        'item_category_id',
        'item_id',
        'issue_date',
        'due_date',
        'quantity',
        'description',
        'issue_status',
        'student_id',
        'intake_id',
        'course_id',
        'active_status',
    ];

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'item_category_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Inventory_item', 'item_id', 'id');
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
}
