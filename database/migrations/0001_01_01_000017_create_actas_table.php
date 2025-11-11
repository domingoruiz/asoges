<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('tipo_acta_id')->constrained('tipo_acta')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('estado_acta_id')->constrained('estado_acta')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('titulo', 255);
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('lugar_reunion', 255);
            $table->text('contenido_acta');
            $table->boolean('aprobada');
            $table->timestamp('fecha_aprobacion')->nullable();

            $table->unique(['aso_id', 'titulo'], 'actas_aso_titulo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actas');
    }
};