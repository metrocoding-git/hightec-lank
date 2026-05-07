<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicesPayments extends Model
{
    protected $fillable = ['invoice_id', 'payment_invoice_number', 'amount', 'payment_method', 'payment_type', 'payment_file', 'receipt_number', 'create_user_id', 'update_user_id','bank_payment_date', 'bank_payment_amount','branch_id','notes','invoice_payment_confirm','customer_name','invoice_payment_note', 'payment_status','invoice_payment_cheque_number','invoice_payment_amount','invoice_payment_confirm_note'];

    public function invoice()
    {
        return $this->belongsTo(FeeInvoices::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }


}
