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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('objectid');
            $table->integer('parentobjid');
            $table->string('path');
            $table->string('full_name');
            $table->integer('isactive');
            $table->string('regioncode')->nullable();
            $table->string('citycode')->nullable();
            $table->string('placecode')->nullable();
            $table->string('plancode')->nullable();
            $table->string('streetcode')->nullable();
            $table->timestamp('updatedate')->nullable();
            $table->timestamp('startdate')->nullable();
            $table->timestamp('enddate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
