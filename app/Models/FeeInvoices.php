<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeInvoices extends Model
{
    //
    protected $table = 'fee_invoices';
    protected $fillable = [
        'branch_id',
        'student_id',
        'total_amount',
        'paid_amount',
        'create_user_id',
        'create_date',
        'due_date',
        'payment_status',
        'payment_method',
        'payment_file',
        'bank_paid_amount', // Added for mass assignment
        'bank_paid_date',
        'invoice_type',   // Added for mass assignment
        'paid_date',
        'invoice_number',
        'main_invoice_number',
        'create_user_id',
        'notes',
      
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
        return $this->belongsTo(Student::class,'student_id','id');
    }
    // Fee_invoices.php
    public function invoice_item()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(InvoicesPayments::class, 'invoice_id', 'id');
    }

    public function paidCourses()
    {
        return $this->hasOne(PaidCourse::class, 'invoice_id');
    }

    public function createUser()
    {
        return $this->belongsTo(User::class, 'create_user_id');
    }
}
