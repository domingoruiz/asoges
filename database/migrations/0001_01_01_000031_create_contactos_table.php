<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('socio_id')->nullable()->constrained('socios')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('entidad_id')->nullable()->constrained('entidad')->cascadeOnUpdate()->nullOnDelete();

            $table->integer('codigo')->nullable();
            $table->string('nombre_completo', 255);
            $table->string('tipo', 100)->nullable();
            $table->string('posicion', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->text('notas')->nullable();

            $table->unique(['aso_id', 'codigo'], 'contactos_aso_codigo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};
