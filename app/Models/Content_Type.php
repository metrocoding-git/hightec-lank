<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content_Type extends Model
{
    //
    protected $table = 'content__types';
    protected $fillable = [
        'name',
        'description',
    ];

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
