<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemIssue extends Model
{
    protected $fillable = [
        'role_id',
        'issue_to',
        'item_category_id',
        'item_id',
        'issue_date',
        'due_date',
        'quantity',
        'description',
        'issue_status',
        'student_id',
        'section_id',
        'class_id',
        'active_status',
    ];

    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id', 'id');
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

    public function section()
    {
        return $this->belongsTo('App\Models\Intake', 'section_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'class_id', 'id');
    }
}
