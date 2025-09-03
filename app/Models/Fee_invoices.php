<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee_invoices extends Model
{
    //
    protected $table = 'fee_invoices';
     protected $fillable = [
        'branch_id',
        'student_id',
        'fee_type_id',
        'total_amount',
        'paid_amount',
        'due_amount',
        'create_date',
        'due_date',
        'payment_status',
        'payment_amount',
        'payment_method',
        'payment_file',
        'single_invoice',
        'clone_amount',
    ];

    protected $casts = [
        'create_date' => 'date',
        'due_date' => 'date',
        'single_invoice' => 'boolean',
        'clone_amount' => 'boolean',
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function invoice_item()
    {
        return $this->hasMany(Invoice_item::class);
    }
}
