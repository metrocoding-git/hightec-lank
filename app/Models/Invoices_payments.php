<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoices_payments extends Model
{
    protected $fillable = ['invoice_id', 'amount', 'payment_method', 'payment_file'];

    public function invoice()
    {
        return $this->belongsTo(Fee_invoices::class);
    }
}
