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
        Schema::create('fee_invoices', function (Blueprint $table) {
           $table->id();

            // Basic info
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('student_id');

            // Amounts
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->default(0);

            // Dates
            $table->date('create_date');
            $table->date('due_date');

            // Payment info
            $table->enum('payment_status', ['not', 'partial', 'full'])->default('not');
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->string('payment_method')->nullable();          // Cash / Card / Bank Transfer
            $table->string('payment_file')->nullable();            // uploaded document

            // Optional flags
            $table->boolean('single_invoice')->default(false);
            $table->boolean('clone_amount')->default(false);

            $table->timestamps();

            // Foreign keys
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_invoices');
    }
};
