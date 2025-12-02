<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\Ubicacion;

class UbicacionTest extends TestCase
{
    public function test_crud_ubicacion()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'ASO_TEST_UBIC_001', 'cif' => 'A000TST17', 'domicilio_social' => 'DIR_TEST_UBIC_001', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crear ubicación padre
        $ubicacionPadre = Ubicacion::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'nombre' => 'UBIC_PADRE_TEST_001', 'descripcion' => 'DESC_UBIC_PADRE_TEST_001', 'categoria_padre_id' => null]);
        $this->assertDatabaseHas('ubicacion', [
            'id'     => $ubicacionPadre->id,
            'nombre' => 'UBIC_PADRE_TEST_001',
        ]);

        // Crear ubicación hija
        $ubicacionHija = Ubicacion::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'nombre' => 'UBIC_HIJO_TEST_001', 'descripcion' => 'DESC_UBIC_HIJO_TEST_001', 'categoria_padre_id' => $ubicacionPadre->id]);
        $this->assertDatabaseHas('ubicacion', [
            'id'     => $ubicacionHija->id,
            'nombre' => 'UBIC_HIJO_TEST_001',
        ]);

        // Actualiza la ubicación hija
        $ubicacionHija->update(['nombre' => 'UBIC_HIJO_TEST_001_MOD']);
        $this->assertDatabaseHas('ubicacion', [
            'id'     => $ubicacionHija->id,
            'nombre' => 'UBIC_HIJO_TEST_001_MOD',
        ]);

        // Elimina la ubicación hija
        $ubicacionHija->delete();
        $this->assertSoftDeleted('ubicacion', [
            'id' => $ubicacionHija->id,
        ]);
    }
}