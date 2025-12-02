<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\TipoActa;

class TipoActaTest extends TestCase
{
    public function test_crud_tipo_acta()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'ASO_TEST_TIPOACTA_001', 'cif' => 'A000TST14', 'domicilio_social' => 'DIR_TEST_TIPOACTA_001', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crear un tipo de acta
        $tipoActa = TipoActa::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'nombre' => 'TIPO_ACTA_TEST_001']);
        $this->assertDatabaseHas('tipo_acta', [
            'id'     => $tipoActa->id,
            'nombre' => 'TIPO_ACTA_TEST_001',
        ]);

        // Actualizar tipo de acta
        $tipoActa->update(['nombre' => 'TIPO_ACTA_TEST_001_MOD']);
        $this->assertDatabaseHas('tipo_acta', [
            'id'     => $tipoActa->id,
            'nombre' => 'TIPO_ACTA_TEST_001_MOD',
        ]);

        // Eliminar tipo de acta
        $tipoActa->delete();
        $this->assertSoftDeleted('tipo_acta', [
            'id' => $tipoActa->id,
        ]);
    }
}