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
        Schema::create('product_single_variation_discounts', function (Blueprint $table) {
            $table->foreignId('s_variation_id')->constrained('product_attribute_values')->cascadeOnDelete();
            $table->foreignId('discount_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_single_variation_discounts');
    }
};
