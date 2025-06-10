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
        Schema::create('content_new_arrivals', function (Blueprint $table) {
            $table->uuid('arrival_id')->unique();
            $table->string('image_url');
            $table->string('image_source');
            $table->string('arrival_url');
            $table->json('content');
            $table->tinyInteger('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_arrivals');
    }
};
