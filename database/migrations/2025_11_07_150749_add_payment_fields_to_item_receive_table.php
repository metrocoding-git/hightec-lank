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
        Schema::table('item_receive', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('payment_method');
            $table->decimal('bank_payment_amount', 10, 2)->nullable()->after('invoice_number');
            $table->date('bank_payment_date')->nullable()->after('bank_payment_amount');
            $table->string('bank_payment_document')->nullable()->after('bank_payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_receive', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'bank_payment_amount', 'bank_payment_date', 'bank_payment_document']);
        });
    }
};
