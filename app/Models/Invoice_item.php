<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice_item extends Model
{
    //
    protected $table = 'invoice_items';
    protected $fillable = [
        'invoice_id', 'fee_type_id', 'amount','paid_amount'
    ];

    public function fee_invoice()
    {
        return $this->belongsTo(Fee_invoices::class);
    }
    public function fee_type()
    {
        return $this->belongsTo(FeeType::class);
    }
}
