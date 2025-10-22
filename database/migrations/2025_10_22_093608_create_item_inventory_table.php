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
        Schema::create('item_inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->enum('transaction_type', ['receive', 'sell', 'issue', 'sell_return', 'issue_return']);
            $table->unsignedBigInteger('transaction_id');
            $table->integer('quantity');
            $table->integer('current_balance');
            $table->string('reference_no')->nullable();
            $table->date('transaction_date');
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('item_id')->references('id')->on('inventory_items')->onDelete('cascade');

            // Indexes for better performance
            $table->index(['item_id', 'transaction_date']);
            $table->index(['transaction_type', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_inventory');
    }
};
