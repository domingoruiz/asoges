<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsoUsrTable extends Migration
{
    public function up()
    {
        Schema::create('aso_usr', function (Blueprint $table) {
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

            $table->foreignId('aso_id')->constrained('aso')->cascadeOnDelete();
            $table->foreignId('usr_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rol_id')->constrained('rol')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('aso_usr');
    }
}
