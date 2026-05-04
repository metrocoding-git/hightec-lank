<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePaymentReceipt extends Model
{
    use HasFactory;

    protected $table = 'invoice_payment_receipts';

    protected $fillable = [
        'receipt_number',
        'cheque_number',
        'total_amount',
        'note',
        'created_by',
    ];

    /**
     * One Receipt → Many Items
     */
    public function items()
    {
        return $this->hasMany(InvoicePaymentReceiptItem::class, 'receipt_id');
    }

    /**
     * Receipt → Invoice Payments (through items)
     */
    public function payments()
    {
        return $this->belongsToMany(
            InvoicesPayments::class,
            'invoice_payment_receipt_items',
            'receipt_id',
            'invoice_payment_id'
        )->withPivot('paid_amount')
         ->withTimestamps();
    }

    /**
     * Creator (optional)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
