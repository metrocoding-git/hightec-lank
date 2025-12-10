<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnFee extends Model
{
    protected $table = 'return_fees';

    protected $fillable = [
        'student_id',
        'admission_number',
        'role_id',
        'return_user_id',
        'balance',
        'return_amount',
        'invoice_id',
        'short_note',
        'status'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'return_amount' => 'decimal:2',
        'status' => 'integer'
    ];

    /**
     * Get the student that owns the return fee.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the role that owns the return fee.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user who processed the return fee.
     */
    public function returnUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'return_user_id');
    }

    /**
     * Scope a query to only include active return fees.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to only include inactive return fees.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(FeeInvoice::class, 'invoice_id');
    }
}
