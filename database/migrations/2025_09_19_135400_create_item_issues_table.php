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
        Schema::create('item_issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('issue_to')->nullable(); // user_id
            $table->unsignedBigInteger('item_category_id');
            $table->unsignedBigInteger('item_id');//inventery_item_id
            $table->unsignedBigInteger('student_id')->nullable();//Student
            $table->unsignedBigInteger('section_id')->nullable();//intake
            $table->unsignedBigInteger('class_id')->nullable();//course
            $table->date('issue_date');
            $table->date('due_date');
            $table->integer('quantity');
            $table->text('description')->nullable();
            $table->char('issue_status', 1)->default('I'); // I for Issued, R for Returned
            $table->boolean('active_status')->default(1);
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('item_category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('inventory_items')->onDelete('cascade');
            $table->foreign('issue_to')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('intakes')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('courses')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_issues');
    }
};
