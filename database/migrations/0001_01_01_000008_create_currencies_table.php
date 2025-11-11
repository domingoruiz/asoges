<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();

            $table->char('codigo_iso', 3);
            $table->string('nombre');
            $table->string('nombre_en');
            $table->string('simbolo', 5);

            $table->unique(['codigo_iso'], 'currencies_codigo_iso_unique');
            $table->unique(['nombre'], 'currencies_nombre_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};