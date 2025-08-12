<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
   protected $fillable = [
   
    'branch', 'admission_number', 'admission_date', 'student_category_id', 'gender',
    'first_name', 'last_name',
    'date_of_birth', 'photo', 'email_address', 'phone_number',
    'current_address', 'permanent_address', 'blood_group', 'height', 'weight',
    'guardians_name', 'relation_with_guardian', 'guardians_email', 'guardians_photo',
    'guardians_phone', 'guardians_occupation', 'guardians_address', 'national_id_number',
    'birth_certificate_number', 'additional_notes', 'document_title_1', 'document_file_1',
    'document_title_2', 'document_file_2', 'document_title_3', 'document_file_3',
    'document_title_4', 'document_file_4', 'previous_school_details', 'dormitory_name',
    'sibling_id'
];
}
