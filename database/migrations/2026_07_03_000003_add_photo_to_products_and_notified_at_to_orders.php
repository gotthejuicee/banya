<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Фото, завантажене з адмінки (public disk). Якщо порожнє —
            // картка показує стандартну пару image.webp/.jpg
            $table->string('photo')->nullable()->after('image');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('notified_at')->nullable()->after('user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('photo');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('notified_at');
        });
    }
};
