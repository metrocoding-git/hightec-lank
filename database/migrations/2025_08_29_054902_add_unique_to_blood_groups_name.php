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
               Schema::table('blood_groups', function (Blueprint $table) {
            // set nullable + unique
            $table->string('name')->nullable()->unique()->change();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_groups', function (Blueprint $table) {
            // revert to previous state (nullable without unique)
            $table->string('name')->nullable(false)->change();
            $table->dropUnique(['name']); // remove unique index
        });
    }
};
