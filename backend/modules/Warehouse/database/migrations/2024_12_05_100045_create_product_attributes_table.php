<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Warehouse\Enums\ProductAttributeViewTypeEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->generatedAs()->always()->primary();
            $table->string('name');
            $table->tinyInteger('type');
            $table->boolean('required')->default(false);
            $table->tinyInteger('view_type')->default(ProductAttributeViewTypeEnum::RADIO_BUTTON->value);
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
        });

        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->generatedAs()->always()->primary();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attribute_id')->nullable()->constrained('product_attributes')->nullOnDelete();
            $table->integer('quantity')->default(0);
            $table->decimal('price')->nullable();
            $table->string('value');
            //$table->index(['attribute_id', 'value', 'product_id'], 'product_attribute_values_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
