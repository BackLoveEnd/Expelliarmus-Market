<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->unsignedInteger('id')->generatedAs()->always()->primary();
            $table->string('coupon_id', 8)->unique();
            $table->integer('discount');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('email')->nullable();
            $table->tinyInteger('type');
            $table->timestamp('expires_at')->nullable();
        });
    }
s
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
