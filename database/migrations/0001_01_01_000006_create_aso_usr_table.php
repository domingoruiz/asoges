<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aso_usr', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('alt_usr')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('mod_usr')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreignId('aso_id')->constrained('aso')->restrictOnDelete();
            $table->foreignId('usr_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('rol_id')->constrained('rol')->restrictOnDelete();

            $table->unique(['aso_id', 'usr_id', 'rol_id'], 'aso_usr_aso_usr_rol_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aso_usr');
    }
};