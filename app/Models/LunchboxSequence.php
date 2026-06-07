<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LunchboxSequence extends Model
{
    protected $table = 'lunchbox_sequences';
 
    public $timestamps = false;
 
    protected $fillable = [
        'branch_id',
        'year',
        'month',
        'letter',
        'last_number',
    ];
}
 