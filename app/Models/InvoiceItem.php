<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    //
    protected $table = 'invoice_items';
    protected $fillable = [
        'invoice_id', 'fee_type_id', 'amount','paid_amount'
    ];

    public function fee_invoice()
    {
        return $this->belongsTo(FeeInvoices::class, 'invoice_id');
    }
    // public function feeType()
    // {
    //     return $this->belongsTo(FeeType::class, 'fee_type_id');
    // }
    public function fee_type()
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id');
    }
}
