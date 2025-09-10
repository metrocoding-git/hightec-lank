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
        Schema::create('item_receive', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('sub_total', 10, 2);
            $table->string('reference_no')->nullable();
            $table->date('receive_date')->nullable();
            $table->text('description')->nullable();
            $table->string('payment_method')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_receive');
    }
};
