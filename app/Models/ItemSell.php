<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSell extends Model
{
    protected $fillable = [
        'payment_method',
        'role_id',
        'course_id',
        'intake_id',
        'student_id',
        'user_id',
        'others_name',
        'reference_no',
        'sell_date',
        'description',
        'item_id',
        'total_quantity',
        'sell_price',
        'total_paid',
        'total_due',
    ];

    // Define relationships if necessary
    public function role()
    {
        return $this->belongsTo(Role::class , 'role_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class , 'course_id');
    }

    public function intake()
    {
        return $this->belongsTo(Intake::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Inventory_item::class, 'item_id');
    }
}
