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
         Schema::create('video_list_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('video_list_id');
            $table->unsignedBigInteger('course_id');
            $table->timestamps();

            // foreign keys
            $table->foreign('video_list_id')->references('id')->on('video_lists')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_list_courses');
    }
};
