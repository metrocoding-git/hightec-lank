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
        Schema::create('item_sell_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_sell_batch_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('sub_total', 10, 2);
            $table->timestamps();

            $table->foreign('item_sell_batch_id')->references('id')->on('item_sell_batches')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('inventory_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_sell_items');
    }
};
