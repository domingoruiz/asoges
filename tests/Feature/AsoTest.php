<?php

namespace Tests\Feature;

use App\Models\Aso;
use Tests\TestCase;
use Carbon\Carbon;

class AsoTest extends TestCase
{
    public function test_crud_aso()
    {
        // Crea una asociación
        $aso = Aso::create([
            'nombre' => 'Asociación Unbroken Workout',
            'cif' => 'G75442228',
            'domicilio_social' => 'Vélez-Málaga',
            'fch_constitucion' => Carbon::now(),
            'alt_usr' => 1,
            'mod_usr' => 1,
        ]);
        $this->assertDatabaseHas('aso', [
            'nombre' => 'Asociación Unbroken Workout',
        ]);

        // Actualiza la asociación
        $aso->update([
            'nombre' => 'Asociación Unbroken Workout Modificada',
        ]);
        $this->assertDatabaseHas('aso', [
            'nombre' => 'Asociación Unbroken Workout Modificada',
        ]);

        // Elimina la asociación
        $aso->delete();
        $this->assertDatabaseMissing('aso', [
            'nombre' => 'Asociación Unbroken Workout Modificada',
        ]);
    }
}