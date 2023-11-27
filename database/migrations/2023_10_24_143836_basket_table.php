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
        Schema::create('basket', function (Blueprint $table) {
            $table->bigIncrements('basket_id')->unsigned()->nullable(false); 
            $table->foreignUuid('user_id');
            $table->json('orders')->nullable(false); 
            $table->timestamp('basket_date')->nullable(false);;
            $table->decimal('basket_total', 8, 2)->nullable(false);;
            $table->boolean('basket_status')->nullable(false)->default(0);
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade')->delete(); 
            
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basket');
    }
};
