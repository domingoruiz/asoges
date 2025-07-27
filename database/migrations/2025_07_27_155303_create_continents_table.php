<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('continents', function (Blueprint $table) {
            $table->id();

            $table->timestamps();
            $table->foreignId('alt_usr')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('mod_usr')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('codigo', 2);
            $table->string('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('continents');
    }
};