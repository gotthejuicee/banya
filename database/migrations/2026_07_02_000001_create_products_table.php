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
            $table->string('slug')->unique();
            $table->string('category')->index(); // male | female
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->json('contents')->nullable(); // склад набору, список рядків
            $table->unsignedInteger('price'); // грн
            $table->unsignedInteger('old_price')->nullable();
            $table->string('badge')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
