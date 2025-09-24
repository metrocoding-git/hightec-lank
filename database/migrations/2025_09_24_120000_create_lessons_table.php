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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id(); // auto-increment primary key
            $table->string('name');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('intake_id');
            $table->unsignedBigInteger('subject_id');
            $table->timestamps();

            // If you want foreign keys:
             $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
             $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
             $table->foreign('intake_id')->references('id')->on('intakes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
