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

            $table->foreignId('alt_usr')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('mod_usr')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('nombre');
            $table->string('nombre_en')->nullable();
            $table->char('codigo_iso2', 2);
            $table->char('codigo_iso3', 3);
            $table->smallInteger('codigo_num');
            $table->string('prefijo');

            $table->foreignId('continente')
                ->nullable()
                ->constrained('continents')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('moneda')
                ->constrained('currencies')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pai');
    }
};