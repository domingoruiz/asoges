<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categoria_contable', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();

            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('categoria_padre_id')->nullable()->constrained('categoria_contable')->cascadeOnUpdate()->nullOnDelete();

            $table->string('nombre', 255);

            $table->unique(['aso_id', 'nombre'], 'categoria_contable_aso_nombre_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categoria_contable');
    }
};