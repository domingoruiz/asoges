<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('libro_interacciones', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();

            $table->integer('codigo')->nullable();
            $table->date('fecha');
            $table->foreignId('libro_proyecto_id')->nullable()->constrained('libro_proyectos')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('socio_id')->nullable()->constrained('socios')->cascadeOnUpdate()->nullOnDelete();
            $table->string('interaccion', 100);
            $table->string('oportunidad', 255)->nullable();
            $table->foreignId('contacto_id')->nullable()->constrained('contactos')->cascadeOnUpdate()->nullOnDelete();
            $table->text('notas')->nullable();

            $table->unique(['aso_id', 'codigo'], 'libro_interacciones_aso_codigo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('libro_interacciones');
    }
};
