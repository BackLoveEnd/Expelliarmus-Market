<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->unsignedInteger('id')->generatedAs()->always()->primary();
            $table->string('coupon_id')->unique();
            $table->integer('discount');
            $table->tinyInteger('type');
            $table->timestamp('expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
