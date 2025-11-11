<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pai', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreignId('continente')->nullable()->constrained('continents')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('moneda')->constrained('currencies')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('nombre');
            $table->string('nombre_en')->nullable();
            $table->char('codigo_iso2', 2);
            $table->char('codigo_iso3', 3);
            $table->smallInteger('codigo_num');
            $table->string('prefijo');

            $table->unique(['nombre'], 'pai_nombre_unique');
            $table->unique(['codigo_iso2'], 'pai_codigo_iso2_unique');
            $table->unique(['codigo_iso3'], 'pai_codigo_iso3_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pai');
    }
};