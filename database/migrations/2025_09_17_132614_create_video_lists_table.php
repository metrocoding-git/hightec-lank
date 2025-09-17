<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('video_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('intake_id');
            $table->string('title');
            $table->string('video_link');
            $table->text('description')->nullable();
            $table->timestamps();

            // foreign key to intakes table
            $table->foreign('intake_id')->references('id')->on('intakes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_lists');
    }
};
