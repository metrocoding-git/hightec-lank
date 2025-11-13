<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaidCourse extends Model
{
    protected $fillable = [
        '',
        'course_id',
        'course_price',
        'paid_amount',
        'due_amount',
        'payment_status',
        'payment_method',
        'bank_paid_amount',
        'bank_paid_date',
        'payment_file',
    ];

    /**
     * Get the invoice that owns the PaidCourse.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Fee_invoices::class, 'invoice_id','id');
    }

    /**
     * Get the course that owns the PaidCourse.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
