<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 160);
            $table->string('slug', 200)->unique();
            $table->text('description');
            $table->string('image_path');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('discount_price')->nullable();
            $table->timestamps();
        });
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 160)->default('Radeya Glass & Aluminium');
            $table->string('email')->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->text('about')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('store_settings');
    }
};
