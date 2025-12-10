<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoices_payments extends Model
{
    protected $fillable = ['invoice_id', 'payment_invoice_number', 'amount', 'payment_method', 'payment_type', 'payment_file', 'receipt_number', 'create_user_id', 'bank_payment_date', 'bank_payment_amount','branch_id'];

    public function invoice()
    {
        return $this->belongsTo(Fee_invoices::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
