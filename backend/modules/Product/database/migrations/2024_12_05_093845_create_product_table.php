<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Warehouse\Enums\ProductStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->generatedAs('START WITH 1000')->always();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('product_article')->unique();
            $table->foreignId('category_id')->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->constrained()->nullOnDelete();
            $table->text('title_description');
            $table->longText('main_description_markdown');
            $table->longText('main_description_html');
            $table->json('images')->nullable();
            $table->string('preview_image')->nullable();
            $table->string('preview_image_source')->nullable();
            $table->tinyInteger('status')->default(ProductStatusEnum::NOT_PUBLISHED);
            $table->boolean('with_attribute_combinations')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->primary('id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
