<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Schema;

class BankAccount extends Model
{
    public function up(): void
{
    Schema::create('bank_accounts', function (Blueprint $table) {
        $table->id();
        $table->string('account_name');
        $table->string('bank_name');
        $table->string('account_number')->unique();
        $table->decimal('balance', 12, 2)->default(0);
        $table->timestamps();
    });
}

}
