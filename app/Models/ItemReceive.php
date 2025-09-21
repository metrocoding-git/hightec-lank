<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemReceive extends Model
{
    use HasFactory;

    protected $table = 'item_receive'; 

    protected $fillable = [
        'supplier_id',
        'store_id',
        'item_id',
        'unit_price',
        'quantity',
        'sub_total',
        'reference_no',
        'receive_date',
        'description',
        'payment_method',
        'bank_id',
        'total_paid',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function item()
    {
        return $this->belongsTo(Inventory_item::class, 'item_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'store_id');
    }
}
