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
            $table->string('invoice_type')->default('fee')->after('id');
            $table->decimal('bank_paid_amount', 10, 2)->nullable()->after('invoice_type');
            $table->date('bank_paid_date')->nullable()->after('bank_paid_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_invoices', function (Blueprint $table) {
            $table->dropColumn(['invoice_type', 'bank_paid_amount', 'bank_paid_date']);
        });
    }
};
