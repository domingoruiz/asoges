<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_bancarias', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreignId('entidad_id')->nullable()->constrained('entidad')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('moneda_id')->nullable()->constrained('currencies')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('pais_id')->nullable()->constrained('pai')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('nombre');
            $table->string('numero_cuenta')->nullable();
            $table->string('swift_bic')->nullable();
            $table->date('fecha_apertura')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('direccion')->nullable();
            $table->string('cp')->nullable();
            $table->string('localidad')->nullable();
            $table->string('provincia')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();

            $table->unique(['aso_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_bancarias');
    }
};