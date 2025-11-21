<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Continent;
use Carbon\Carbon;

class ContinentTest extends TestCase
{
    public function test_crud_continent()
    {
        // Crea un continente
        $continent = Continent::create([
            'codigo' => 'TE',
            'nombre' => 'Test',
            'alt_usr' => 1,
        ]);
        $this->assertDatabaseHas('continents', [
            'codigo' => 'TE',
            'nombre' => 'Test',
        ]);

        // Actualiza el continente
        $continent->update([
            'nombre' => 'Test Modificada',
        ]);
        $this->assertDatabaseHas('continents', [
            'codigo' => 'TE',
            'nombre' => 'Test Modificada',
        ]);

        // Elimina el continente
        $continent->delete();
        $this->assertDatabaseMissing('continents', [
            'codigo' => 'TE',
            'nombre' => 'Test Modificada',
        ]);
    }
}
