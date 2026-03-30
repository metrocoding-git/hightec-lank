<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
  //
  protected $table = 'suppliers';

  protected $fillable = [
    'company_name',
    'company_address',
    'contact_person_name',
    'contact_person_mobile',
    'contact_person_email',
    'description',
  ];
}
