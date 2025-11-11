<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ejercicio', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('nombre');
            $table->date('fch_inicio');
            $table->date('fch_fin');

            $table->unique(['aso_id', 'nombre'], 'ejercicio_aso_nombre_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ejercicio');
    }
};