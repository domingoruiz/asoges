<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('socios', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('usr_id')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('rol_id')->constrained('rol')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('tipo_socio_id')->constrained('socio_tipo')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('pais_id')->constrained('pai')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('continente_id')->constrained('continents')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('numero_socio', 30);
            $table->string('nombre', 255);
            $table->string('apellidos', 255);
            $table->string('dni', 20);
            $table->string('telefono', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->date('fecha_nacimiento');
            $table->string('direccion', 255)->nullable();
            $table->string('cp', 10)->nullable();
            $table->string('localidad', 100)->nullable();
            $table->string('nombre_tutor', 255)->nullable();
            $table->string('dni_tutor', 20)->nullable();
            $table->string('telefono_tutor', 255)->nullable();

            $table->unique(['aso_id', 'numero_socio'], 'socios_aso_numero_unique');
            $table->unique(['aso_id', 'dni'], 'socios_aso_dni_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('socios');
    }
};