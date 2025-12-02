<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\TipoTransaccion;

class TipoTransaccionTest extends TestCase
{
    public function test_crud_tipo_transaccion()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'ASO_TEST_TIPOTRANS_001', 'cif' => 'A000TST16', 'domicilio_social' => 'DIR_TEST_TIPOTRANS_001', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crear un tipo de transacción
        $tipo = TipoTransaccion::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'nombre' => 'TIPOTRANS_TEST_001']);
        $this->assertDatabaseHas('tipo_transaccion', [
            'id'     => $tipo->id,
            'nombre' => 'TIPOTRANS_TEST_001',
        ]);

        // Actualizar tipo de transacción
        $tipo->update(['nombre' => 'TIPOTRANS_TEST_001_MOD']);
        $this->assertDatabaseHas('tipo_transaccion', [
            'id'     => $tipo->id,
            'nombre' => 'TIPOTRANS_TEST_001_MOD',
        ]);

        // Eliminar tipo de transacción
        $tipo->delete();
        $this->assertSoftDeleted('tipo_transaccion', [
            'id' => $tipo->id,
        ]);
    }
}