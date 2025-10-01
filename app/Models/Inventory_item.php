<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory_item extends Model
{
    //
    protected $table = 'inventory_items';
     protected $fillable = ['item_name', 'item_category_id', 'description'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'item_category_id');
    }
}
