<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\EstadoDocumento;

class EstadoDocumentoTest extends TestCase
{
    public function test_crud_estado_documento()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'Asociación Estado Documento Test', 'cif' => 'A00000004', 'domicilio_social' => 'Dirección Test', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crea un estado de documento
        $estado = EstadoDocumento::create(['aso_id' => $aso->id, 'nombre' => 'Estado Doc Inicial', 'alt_usr' => 1]);
        $this->assertDatabaseHas('estado_documento', [
            'aso_id' => $aso->id,
            'nombre' => 'Estado Doc Inicial',
        ]);

        // Actualiza el estado de documento
        $estado->update(['nombre' => 'Estado Doc Modificado']);
        $this->assertDatabaseHas('estado_documento', [
            'aso_id' => $aso->id,
            'nombre' => 'Estado Doc Modificado',
        ]);

        // Elimina el estado de documento
        $estado->delete();
        $this->assertSoftDeleted('estado_documento', [
            'id' => $estado->id,
        ]);
    }
}