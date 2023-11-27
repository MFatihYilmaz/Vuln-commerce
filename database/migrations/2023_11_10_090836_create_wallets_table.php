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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id('wallet_id')->startingValue(1000);
            $table->foreignUuid('user_id')->nullable(false);
            $table->decimal('deposit', 8, 2)->nullable(false);
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade')->delete(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
