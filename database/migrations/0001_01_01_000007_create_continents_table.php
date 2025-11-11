<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('continents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('codigo', 2);
            $table->string('nombre');

            $table->unique(['codigo'], 'continents_codigo_unique');
            $table->unique(['nombre'], 'continents_nombre_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('continents');
    }
};