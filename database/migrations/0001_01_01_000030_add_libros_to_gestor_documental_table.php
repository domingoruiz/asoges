<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gestor_documental', function (Blueprint $table) {
            $table->unsignedBigInteger('libro_actas_id')->nullable()->after('estado_documento');
            $table->unsignedBigInteger('socio_id')->nullable()->after('libro_actas_id');
            $table->unsignedBigInteger('contabilidad_id')->nullable()->after('socio_id');
            $table->unsignedBigInteger('inventario_id')->nullable()->after('contabilidad_id');
            $table->unsignedBigInteger('libro_proyecto_id')->nullable()->after('inventario_id');
        });
    }

    public function down(): void
    {
        Schema::table('gestor_documental', function (Blueprint $table) {
            $table->dropColumn([
                'libro_actas_id',
                'socio_id',
                'contabilidad_id',
                'inventario_id',
                'libro_proyecto_id',
            ]);
        });
    }
};