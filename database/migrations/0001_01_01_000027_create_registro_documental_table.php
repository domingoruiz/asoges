<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gestor_documental', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('tipo_documento_id')->constrained('tipo_documento')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('entidad_id')->constrained('entidad')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('ejercicio_id')->constrained('ejercicio')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('estado_documento')->constrained('estado_documento')->cascadeOnUpdate()->restrictOnDelete();

            $table->enum('direccion_documento', ['entrada', 'salida']);
            $table->date('fecha_documento');
            $table->string('numero_serie', 50);
            $table->string('ref_externa', 50)->nullable();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();

            $table->string('archivo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gestor_documental');
    }
};