<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ubicacion', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('aso_id')->constrained('aso')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_padre_id')->nullable()->constrained('ubicacion')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ubicacion');
    }
};