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
            $table->date('bank_payment_date')->nullable()->after('payment_status');
            $table->decimal('bank_payment_amount', 10, 2)->nullable()->after('bank_payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices_payments', function (Blueprint $table) {
            $table->dropColumn(['bank_payment_date', 'bank_payment_amount']);
        });
    }
};
