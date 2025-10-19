<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices_payments', 'receipt_number')) {
                $table->string('receipt_number')->nullable()->after('id');
            }
            $table->unsignedBigInteger('create_user_id')->nullable()->after('receipt_number');
            $table->foreign('create_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices_payments', function (Blueprint $table) {
            $table->dropForeign(['create_user_id']);
            $table->dropColumn('create_user_id');
            if (Schema::hasColumn('invoices_payments', 'receipt_number')) {
                $table->dropColumn('receipt_number');
            }
        });
    }
};
