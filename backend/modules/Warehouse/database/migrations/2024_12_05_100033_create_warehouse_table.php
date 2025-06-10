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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->generatedAs()->always();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('total_quantity');
            $table->decimal('default_price')->nullable();
            $table->tinyInteger('status');
            $table->timestamp('arrived_at')->nullable();
            $table->primary('id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse');
    }
};
