<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    //
    protected $table = 'branches';

    protected $fillable = ['name'];

    public function intakes()
    {
        return $this->hasMany(Intake::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_branches');
    }

}
