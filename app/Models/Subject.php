<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    //
    protected $table = 'subjects';

    protected $fillable = ['name', 'code', 'type', 'demonstrator_time', 'practical_time'];

    public function assignSubjects()
    {
        return $this->hasMany(AssignSubject::class, 'subject_id');
    }

    public function isAssigned(): bool
    {
        return $this->assignSubjects()->exists();
    }
}
