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
        Schema::create('address_obj_in_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Address::class);
            $table->foreignIdFor(\Liquetsoft\Fias\Laravel\LiquetsoftFiasBundle\Entity\AddrObj::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address_obj_in_addresses');
    }
};
