<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemIssueItem extends Model
{
    protected $fillable = [
        'batch_id',
        'item_category_id',
        'item_id',
        'quantity',
    ];

    // Relationships
    public function batch()
    {
        return $this->belongsTo('App\Models\ItemIssueBatch', 'batch_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'item_category_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Inventory_item', 'item_id', 'id');
    }

    // Helper methods
    public function getCategoryNameAttribute()
    {
        return $this->category ? $this->category->name : 'N/A';
    }

    public function getItemNameAttribute()
    {
        return $this->item ? $this->item->item_name : 'N/A';
    }
}
