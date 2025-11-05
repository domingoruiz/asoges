<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contabilidad', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('tipo_transaccion_id')->constrained('tipo_transaccion')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('ejercicio_id')->constrained('ejercicio')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('moneda_id')->constrained('currencies')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('entidad_id')->constrained('entidad')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('cuenta_bancaria_id')->constrained('cuentas_bancarias')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('categoria_id')->constrained('categoria_contable')->cascadeOnUpdate()->restrictOnDelete();

            $table->date('fecha_contable');
            $table->decimal('importe', 10, 2);
            $table->string('concepto', 50);
            $table->text('descripcion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contabilidad');
    }
};