<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSellBatch extends Model
{
    protected $fillable = [
        'branch_id',
        'payment_method',
        'invoice_number',
        'bank_payment_amount',
        'bank_payment_date',
        'bank_payment_document',
        'role_id',
        'student_id',
        'user_id',
        'others_name',
        'reference_no',
        'sell_date',
        'description',
        'total_quantity',
        'total_sell_price',
        'total_paid',
        'total_due',
        'status',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(ItemSellItem::class, 'item_sell_batch_id');
    }

    // Course and intake relationships removed as they're not available in current structure
}
