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
        Schema::create('addresses',function (Blueprint $table) {
            $table->id('address_id');
            $table->foreignUuid('user_id');
            $table->string('address_header');
            $table->string('address');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade')->index(); 
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
