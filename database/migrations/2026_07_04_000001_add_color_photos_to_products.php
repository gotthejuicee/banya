<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Базові шляхи стандартних фото в public (без розширення, як image):
            // окремо для темного і світлого боксу — перемикач на картці їх гортає.
            $table->string('image_dark')->nullable()->after('image');
            $table->string('image_light')->nullable()->after('image_dark');
            // Завантажені з адмінки фото (storage), перекривають стандартні.
            $table->string('photo_dark')->nullable()->after('photo');
            $table->string('photo_light')->nullable()->after('photo_dark');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['image_dark', 'image_light', 'photo_dark', 'photo_light']);
        });
    }
};
