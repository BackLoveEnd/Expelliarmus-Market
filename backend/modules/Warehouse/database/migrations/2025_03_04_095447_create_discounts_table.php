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
        Schema::create('discounts', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->generatedAs()->always();
            $table->tinyInteger('percentage');
            $table->decimal('original_price');
            $table->decimal('discount_price');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->tinyInteger('status');
            $table->morphs('discountable');
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
