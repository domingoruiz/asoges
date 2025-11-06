<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('categoria_id')->constrained('categoria_inventario')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('entidad_id')->constrained('entidad')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('ubicacion_id')->constrained('ubicacion')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->integer('cantidad');
            $table->decimal('valor', 10, 2);
            $table->date('fecha_adquisicion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};