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
        Schema::create('item_sell_batches', function (Blueprint $table) {
            $table->id();
            $table->string('payment_method');
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // For staff
            $table->string('others_name')->nullable(); // For other customers
            $table->string('reference_no')->nullable();
            $table->date('sell_date');
            $table->text('description')->nullable();
            $table->integer('total_quantity')->default(0);
            $table->decimal('total_sell_price', 10, 2)->default(0.00); // Total price of all items in the batch
            $table->decimal('total_paid', 10, 2)->default(0.00);
            $table->decimal('total_due', 10, 2)->default(0.00);
            $table->string('status')->default('U'); // P: Paid, PP: Partial Paid, U: Unpaid, R: Returned
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_sell_batches');
    }
};
