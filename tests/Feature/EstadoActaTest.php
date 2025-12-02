<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\EstadoActa;

class EstadoActaTest extends TestCase
{
    public function test_crud_estado_acta()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'Asociación Estado Acta Test', 'cif' => 'A00000003', 'domicilio_social' => 'Dirección Test', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crea un estado de acta
        $estado = EstadoActa::create(['aso_id' => $aso->id, 'nombre' => 'Estado Inicial', 'alt_usr' => 1]);
        $this->assertDatabaseHas('estado_acta', [
            'aso_id' => $aso->id,
            'nombre' => 'Estado Inicial',
        ]);

        // Actualiza el estado de acta
        $estado->update(['nombre' => 'Estado Modificado']);
        $this->assertDatabaseHas('estado_acta', [
            'aso_id' => $aso->id,
            'nombre' => 'Estado Modificado',
        ]);

        // Elimina el estado de acta
        $estado->delete();
        $this->assertSoftDeleted('estado_acta', [
            'id' => $estado->id,
        ]);
    }
}