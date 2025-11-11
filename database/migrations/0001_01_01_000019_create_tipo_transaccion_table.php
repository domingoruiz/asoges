<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_transaccion', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('nombre', 255);

            $table->unique(['aso_id', 'nombre'], 'tipo_transaccion_aso_nombre_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_transaccion');
    }
};