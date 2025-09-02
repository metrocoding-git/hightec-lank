<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    //
    protected $table = 'fee_types';

    protected $fillable = [
        'name',
        'description',
        'price',
        'fee_group_id',
    ];

        public function feeGroup()
    {
        return $this->belongsTo(FeesGroup::class);
    }
}
