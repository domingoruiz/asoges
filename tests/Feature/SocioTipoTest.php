<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\SocioTipo;

class SocioTipoTest extends TestCase
{
    public function test_crud_socio_tipo()
    {
        // Crea un socio tipo
        $socioTipo = SocioTipo::create([
            'nombre'  => 'Tipo Prueba',
            'alt_usr' => 1,
        ]);
        $this->assertDatabaseHas('socio_tipo', [
            'nombre' => 'Tipo Prueba',
        ]);

        // Actualiza el socio tipo
        $socioTipo->update([
            'nombre' => 'Tipo Prueba Modificado',
        ]);
        $this->assertDatabaseHas('socio_tipo', [
            'nombre' => 'Tipo Prueba Modificado',
        ]);

        // Elimina el socio tipo
        $socioTipo->delete();
        $this->assertSoftDeleted('socio_tipo', [
            'id' => $socioTipo->id,
        ]);
    }
}