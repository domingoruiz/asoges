<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pai;

class PaiTest extends TestCase
{
    public function test_crud_pai()
    {
        // Crea un país
        $pai = Pai::create([
            'nombre'       => 'País Prueba',
            'nombre_en'    => 'Test Country',
            'codigo_iso2'  => 'ZZ',
            'codigo_iso3'  => 'ZZZ',
            'codigo_num'   => 999,
            'prefijo'      => '+999',
            'continente'   => null,
            'moneda'       => 1,
            'alt_usr'      => 1,
        ]);
        $this->assertDatabaseHas('pai', [
            'nombre'      => 'País Prueba',
            'codigo_iso2' => 'ZZ',
            'codigo_iso3' => 'ZZZ',
        ]);

        // Actualiza el país
        $pai->update([
            'nombre'    => 'País Prueba Modificado',
            'nombre_en' => 'Test Country Updated',
        ]);
        $this->assertDatabaseHas('pai', [
            'nombre'    => 'País Prueba Modificado',
            'nombre_en' => 'Test Country Updated',
        ]);

        // Elimina el país
        $pai->delete();
        $this->assertDatabaseMissing('pai', [
            'nombre' => 'País Prueba Modificado',
        ]);
    }
}
