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
        // Main batch table for item issues
        Schema::create('item_issue_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('issue_to')->nullable(); // staff user_id
            $table->unsignedBigInteger('intake_id')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->date('issue_date');
            $table->date('due_date');
            $table->text('description')->nullable();
            $table->char('issue_status', 1)->default('I'); // I for Issued, R for Returned
            $table->boolean('active_status')->default(1);
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('issue_to')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('intake_id')->references('id')->on('intakes')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        // Items within each batch
        Schema::create('item_issue_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('item_category_id');
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('batch_id')->references('id')->on('item_issue_batches')->onDelete('cascade');
            $table->foreign('item_category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('inventory_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_issue_items');
        Schema::dropIfExists('item_issue_batches');
    }
};
