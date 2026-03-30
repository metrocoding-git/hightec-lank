<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    //
    protected $table = 'inventory_items';
     protected $fillable = ['item_name', 'item_category_id', 'item_price', 'description'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'item_category_id');
    }
}
