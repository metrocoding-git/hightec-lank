<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('item_receive', function (Blueprint $table) {
            $table->string('transaction_id', 50)->after('id');
        });

        // Generate transaction IDs for existing records
        $records = DB::table('item_receive')->get();
        foreach ($records as $record) {
            $transactionId = 'TRX-' . str_pad($record->id, 6, '0', STR_PAD_LEFT);
            DB::table('item_receive')->where('id', $record->id)->update(['transaction_id' => $transactionId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_receive', function (Blueprint $table) {
            $table->dropColumn('transaction_id');
        });
    }
};
