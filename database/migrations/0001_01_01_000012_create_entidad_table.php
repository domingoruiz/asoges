<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entidad', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('continente')->nullable()->constrained('continents')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('pais')->nullable()->constrained('pai')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('moneda')->nullable()->constrained('currencies')->cascadeOnUpdate()->nullOnDelete();

            $table->string('nombre_fiscal');
            $table->string('cif', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->string('cp', 10)->nullable();
            $table->string('localidad', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('web')->nullable();
            $table->string('swift_bic', 50)->nullable();
            $table->string('iban', 50)->nullable();
            $table->text('observaciones')->nullable();

            $table->unique(['aso_id', 'nombre_fiscal'], 'entidad_aso_nombre_unique');
            $table->unique(['aso_id', 'cif'], 'entidad_aso_cif_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entidad');
    }
};