<?php
namespace  App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Course;

class FoodPayment extends Model
{
    protected $table = 'food_payments';

    protected $fillable = [
        'invoice_id',
        'course_id',
        'from_date',
        'to_date',
        'has_breakfast',
        'has_lunch',
        'has_dinner',
        'breakfast_amount',
        'lunch_amount',
        'dinner_amount',
        'total_food_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'payment_method',
        'lunchbox_number',
    ];

    public function invoice()
    {
        return $this->belongsTo(FeeInvoices::class, 'invoice_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}