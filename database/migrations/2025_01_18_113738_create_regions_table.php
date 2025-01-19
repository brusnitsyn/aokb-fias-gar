<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Таблица Регионы
     */
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->integer('objectid'); // ID ГАР (ФИАС)
            $table->uuid('objectguid'); // Уникальный GUID (ФИАС)
            $table->string('name');
            $table->string('name_full');
            $table->string('typename');
            $table->timestamp('updatedate')->nullable();
            $table->timestamp('enddate')->nullable();
            $table->integer('isactual');
            $table->integer('isactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
