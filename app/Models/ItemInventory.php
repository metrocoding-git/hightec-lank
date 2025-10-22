<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemInventory extends Model
{
    protected $table = 'item_inventory';

    protected $fillable = [
        'item_id',
        'transaction_type',
        'transaction_id',
        'quantity',
        'current_balance',
        'reference_no',
        'transaction_date',
        'description',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'quantity' => 'integer',
        'current_balance' => 'integer',
    ];

    /**
     * Get the inventory item associated with this inventory record
     */
    public function item()
    {
        return $this->belongsTo(Inventory_item::class, 'item_id');
    }

    /**
     * Get the item receive record if this is a receive transaction
     */
    public function itemReceive()
    {
        return $this->belongsTo(ItemReceive::class, 'transaction_id');
    }

    /**
     * Get the item sell record if this is a sell transaction
     */
    public function itemSell()
    {
        return $this->belongsTo(ItemSell::class, 'transaction_id');
    }

    /**
     * Get the item issue batch record if this is an issue transaction
     */
    public function itemIssueBatch()
    {
        return $this->belongsTo(ItemIssueBatch::class, 'transaction_id');
    }

    /**
     * Scope to filter by item
     */
    public function scopeForItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    /**
     * Scope to filter by transaction type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Get the current balance for a specific item
     */
    public static function getCurrentBalance($itemId)
    {
        $latestRecord = static::forItem($itemId)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $latestRecord ? $latestRecord->current_balance : 0;
    }

    /**
     * Get inventory history for a specific item
     */
    public static function getInventoryHistory($itemId, $limit = null)
    {
        $query = static::forItem($itemId)
            ->with('item')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }


}
