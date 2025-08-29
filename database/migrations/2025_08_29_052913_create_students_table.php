<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
              $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('admission_number')->nullable();
            $table->date('admission_date')->nullable();
            $table->unsignedBigInteger('student_category_id')->nullable();
            $table->unsignedBigInteger('gender_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('student_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('photo')->nullable();
            $table->string('email_address')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->unsignedBigInteger('blood_group_id')->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('guardians_name')->nullable();
            $table->unsignedBigInteger('relation_guardian_id')->nullable();
            $table->string('guardians_email')->nullable();
            $table->string('guardians_photo')->nullable();
            $table->string('guardians_phone')->nullable();
            $table->string('guardians_occupation')->nullable();
            $table->text('guardians_address')->nullable();
            $table->string('national_id_number')->nullable();
            $table->text('additional_notes')->nullable();
            $table->string('document_title_1')->nullable();
            $table->string('document_file_1')->nullable();
            $table->string('document_title_2')->nullable();
            $table->string('document_file_2')->nullable();
            $table->string('document_title_3')->nullable();
            $table->string('document_file_3')->nullable();
            $table->string('document_title_4')->nullable();
            $table->string('document_file_4')->nullable();
            $table->text('previous_institute_details')->nullable();
            $table->unsignedBigInteger('dormitory_id')->nullable();
            $table->unsignedBigInteger('sibling_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
