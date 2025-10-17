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
        Schema::table('fee_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_invoices', 'main_invoice_number')) {
                $table->string('main_invoice_number')->nullable()->after('id');
            }
            if (!Schema::hasColumn('fee_invoices', 'create_user_id')) {
                $table->unsignedBigInteger('create_user_id')->nullable()->after('main_invoice_number');
                $table->foreign('create_user_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('fee_invoices', 'create_user_id')) {
                $table->dropForeign(['create_user_id']);
                $table->dropColumn('create_user_id');
            }
            if (Schema::hasColumn('fee_invoices', 'main_invoice_number')) {
                $table->dropColumn('main_invoice_number');
            }
        });
    }
};
