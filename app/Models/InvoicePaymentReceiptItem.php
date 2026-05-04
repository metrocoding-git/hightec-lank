<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePaymentReceiptItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_payment_receipt_items';

    public $timestamps = false; // only created_at in table

    protected $fillable = [
        'receipt_id',
        'invoice_payment_id',
        'paid_amount',
    ];

    /**
     * Item → Receipt
     */
    public function receipt()
    {
        return $this->belongsTo(InvoicePaymentReceipt::class, 'receipt_id');
    }

    /**
     * Item → Invoice Payment
     */
    public function payment()
    {
        return $this->belongsTo(InvoicesPayments::class, 'invoice_payment_id');
    }
}