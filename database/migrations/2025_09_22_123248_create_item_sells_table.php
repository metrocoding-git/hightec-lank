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
        Schema::create('item_sells', function (Blueprint $table) {
            $table->id();
            $table->string('payment_method');
            $table->unsignedBigInteger('role_id')->nullable(); // sell_to
            $table->unsignedBigInteger('course_id')->nullable(); // class
            $table->unsignedBigInteger('intake_id')->nullable(); // section
            $table->unsignedBigInteger('student_id')->nullable(); // student
            $table->unsignedBigInteger('user_id')->nullable(); // name (staff)
            $table->string('reference_no')->nullable();
            $table->date('sell_date');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('item_id')->nullable(); //inventory item
            $table->decimal('total_quantity', 10, 2)->default(0.00);
            $table->decimal('sell_price', 10, 2)->default(0.00);
            $table->decimal('total_paid', 10, 2)->default(0.00);
            $table->decimal('total_due', 10, 2)->default(0.00);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
            $table->foreign('intake_id')->references('id')->on('intakes')->onDelete('set null');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('item_id')->references('id')->on('inventory_items')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_sells');
    }
};
