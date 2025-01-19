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
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Region::class);
            $table->integer('objectid'); // ID ГАР (ФИАС)
            $table->uuid('objectguid'); // Уникальный GUID (ФИАС)
            $table->string('name');
            $table->string('name_full')->nullable();
            $table->string('typename');
            $table->string('typename_full')->nullable();
            $table->timestamp('updatedate')->nullable();
            $table->timestamp('enddate');
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
        Schema::dropIfExists('municipalities');
    }
};
