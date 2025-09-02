<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeesGroup extends Model
{
    //
    protected $table = 'fees_groups';
    protected $fillable = [
        'name',
        'description',
    ];
         public function feeType()
    {
        return $this->hasMany(FeeType::class , 'fee_group_id'   );
    }
    
    
}
