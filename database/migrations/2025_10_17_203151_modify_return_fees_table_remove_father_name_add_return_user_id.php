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
        Schema::table('return_fees', function (Blueprint $table) {
            // Remove father_name column
            $table->dropColumn('father_name');

            // Add return_user_id column
            $table->unsignedBigInteger('return_user_id')->nullable()->after('role_id');

            // Add foreign key constraint
            $table->foreign('return_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_fees', function (Blueprint $table) {
            // Add back father_name column
            $table->string('father_name')->nullable()->after('role_id');

            // Remove return_user_id column and its foreign key
            $table->dropForeign(['return_user_id']);
            $table->dropColumn('return_user_id');
        });
    }
};
