<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\Ejercicio;

class EjercicioTest extends TestCase
{
    public function test_crud_ejercicio()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'Asociación Ejercicio Test', 'cif' => 'A00000001', 'domicilio_social' => 'Dirección Test', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crea un ejercicio
        $ejercicio = Ejercicio::create([
            'aso_id'     => $aso->id,
            'nombre'     => 'Ejercicio Prueba',
            'fch_inicio' => '2025-01-01',
            'fch_fin'    => '2025-12-31',
            'alt_usr'    => 1,
        ]);
        $this->assertDatabaseHas('ejercicio', [
            'nombre' => 'Ejercicio Prueba',
        ]);

        // Actualiza el ejercicio
        $ejercicio->update([
            'nombre' => 'Ejercicio Prueba Modificado',
        ]);
        $this->assertDatabaseHas('ejercicio', [
            'nombre' => 'Ejercicio Prueba Modificado',
        ]);

        // Elimina el ejercicio
        $ejercicio->delete();
        $this->assertSoftDeleted('ejercicio', [
            'id' => $ejercicio->id,
        ]);
    }
}
