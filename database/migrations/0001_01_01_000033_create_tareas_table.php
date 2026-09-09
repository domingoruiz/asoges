<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('socio_id')->nullable()->constrained('socios')->cascadeOnUpdate()->nullOnDelete();

            $table->integer('codigo')->nullable();
            $table->string('nombre', 255);
            $table->date('fecha');
            $table->enum('frecuencia', ['puntual', 'diaria', 'semanal', 'mensual', 'trimestral', 'semestral', 'anual'])->default('puntual');
            $table->enum('estado', ['pendiente', 'en_curso', 'finalizado'])->default('pendiente');
            $table->text('descripcion')->nullable();

            $table->unique(['aso_id', 'codigo'], 'tareas_aso_codigo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
