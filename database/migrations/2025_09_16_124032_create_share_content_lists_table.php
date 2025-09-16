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
        Schema::create('share_content_lists', function (Blueprint $table) {
           $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->string('title', 255);
            $table->text('send_to')->nullable();
            $table->text('description')->nullable();
            $table->date('save_date');
            $table->date('valid_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_content_lists');
    }
};
