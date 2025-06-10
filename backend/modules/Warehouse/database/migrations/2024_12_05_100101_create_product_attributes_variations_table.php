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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->generatedAs()->always()->primary();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->integer('quantity')->default(0);
            $table->decimal('price')->nullable();
        });

        Schema::create('variation_attribute_values', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->generatedAs()->always()->primary();
            $table->foreignId('variation_id')->constrained('product_variations')->cascadeOnDelete();
            $table->foreignId('attribute_id')->nullable()->constrained('product_attributes')->nullOnDelete();
            $table->string('value');
            $table->unique(['variation_id', 'attribute_id']);
            //$table->index(['attribute_id', 'value', 'variation_id'], 'variation_attribute_values_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes_combo');
    }
};
