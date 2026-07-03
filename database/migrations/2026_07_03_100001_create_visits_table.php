<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            // day як рядок 'Y-m-d': на SQLite date-каст пише '...00:00:00'
            // і firstOrNew перестає знаходити рядок
            $table->string('day', 10)->index();
            $table->string('visitor_id', 40); // sha1(app_key|ip|ua) — анонімний
            $table->string('path', 191);
            $table->unsignedInteger('hits')->default(1);
            $table->timestamps();

            $table->unique(['day', 'visitor_id', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
