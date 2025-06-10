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
        Schema::create('content_sliders', function (Blueprint $table) {
            $table->uuid('slide_id')->unique();
            $table->string('image_url');
            $table->string('image_source');
            $table->smallInteger('order');
            $table->string('content_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_slider');
    }
};
