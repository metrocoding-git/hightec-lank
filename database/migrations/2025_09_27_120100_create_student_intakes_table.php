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
        Schema::create('student_intakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('intake_id')->constrained('intakes')->onDelete('cascade');
            $table->timestamp('enrolled_at')->nullable();
            $table->string('status')->default('active'); // active, completed, dropped, etc.
            $table->text('notes')->nullable();
            $table->timestamps();

            // Ensure a student can't enroll in the same intake twice
            $table->unique(['student_id', 'intake_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_intakes');
    }
};
