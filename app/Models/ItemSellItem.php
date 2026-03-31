<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSellItem extends Model
{
    protected $table = 'item_sell_items'; // Specify the new table name

    protected $fillable = [
        'item_sell_batch_id',
        'item_id',
        'unit_price',
        'quantity',
        'sub_total',
    ];

    // Define relationships if necessary
    public function itemSellBatch()
    {
        return $this->belongsTo(ItemSellBatch::class, 'item_sell_batch_id' );
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}
