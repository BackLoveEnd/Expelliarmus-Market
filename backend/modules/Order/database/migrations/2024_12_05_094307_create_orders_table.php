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
        Schema::create('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->generatedAs('START WITH 1000')->always();
            $table->bigInteger('order_id')->unique();
            $table->morphs('userable');
            $table->tinyInteger('status');
            $table->string('contact_email');
            $table->decimal('total_price');
            $table->timestamp('created_at')->nullable();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
