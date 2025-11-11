<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aso', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();

            $table->date('fch_constitucion');
            $table->string('nombre');
            $table->string('cif');
            $table->string('domicilio_social');
            $table->string('nro_registro')->nullable();
            $table->string('nro_registro_municipal')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('web')->nullable();

            $table->unique(['nombre'], 'aso_nombre_unique');
            $table->unique(['cif'], 'aso_cif_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aso');
    }
};