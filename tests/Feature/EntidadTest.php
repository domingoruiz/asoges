<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\Entidad;

class EntidadTest extends TestCase
{
    public function test_crud_entidad()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'Asociación Entidad Test', 'cif' => 'A00000002', 'domicilio_social' => 'Dirección Test', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crea una entidad
        $entidad = Entidad::create([
            'aso_id'        => $aso->id,
            'nombre_fiscal' => 'Entidad Prueba',
            'cif'           => 'P0000000Z',
            'direccion'     => 'Calle Prueba 123',
            'cp'            => '99999',
            'localidad'     => 'Ciudad Prueba',
            'provincia'     => 'Provincia Prueba',
            'pais'          => 1,
            'continente'    => 1,
            'telefono'      => '999999999',
            'email'         => 'prueba@example.com',
            'web'           => 'https://prueba.com',
            'swift_bic'     => 'PRUBESMMXXX',
            'iban'          => 'ZZ00 0000 0000 0000 0000 0000',
            'moneda'        => 1,
            'observaciones' => 'Observaciones de prueba',
            'alt_usr'       => 1,
        ]);
        $this->assertDatabaseHas('entidad', [
            'nombre_fiscal' => 'Entidad Prueba',
        ]);

        // Actualiza la entidad
        $entidad->update([
            'nombre_fiscal' => 'Entidad Prueba Modificada',
        ]);
        $this->assertDatabaseHas('entidad', [
            'nombre_fiscal' => 'Entidad Prueba Modificada',
        ]);

        // Elimina la entidad
        $entidad->delete();
        $this->assertSoftDeleted('entidad', [
            'id' => $entidad->id,
        ]);
    }
}