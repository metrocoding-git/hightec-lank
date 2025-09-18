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
        Schema::table('share_content_lists', function (Blueprint $table) {
            $table->string('url')->nullable()->after('description');
            $table->foreignId('intake_id')->nullable()->constrained('intakes')->onDelete('cascade')->after('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('share_content_lists', function (Blueprint $table) {
            $table->dropForeign(['intake_id']);
            $table->dropColumn('intake_id');
            $table->dropColumn('url');
        });
    }
};
