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
        Schema::table('invoices_payments', function (Blueprint $table) {
            $table->string('payment_invoice_number')->nullable()->after('invoice_id');
            $table->string('payment_type')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices_payments', function (Blueprint $table) {
            $table->dropColumn('payment_invoice_number');
            $table->dropColumn('payment_type');
        });
    }
};
