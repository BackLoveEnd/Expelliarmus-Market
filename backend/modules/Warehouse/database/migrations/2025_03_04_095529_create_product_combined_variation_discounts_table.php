<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*Schema::create('product_combined_variation_discounts', function (Blueprint $table) {
            $table->foreignId('c_variation_id')->constrained('product_variations')->cascadeOnDelete();
            $table->foreignId('discount_id')->constrained()->cascadeOnDelete();
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_combined_variation_discounts');
    }
};
