<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\CategoriaContable;

class CategoriaContableTest extends TestCase
{
    public function test_crud_categoria_contable()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'ASO_TEST_CATCON_001', 'cif' => 'A000TST12', 'domicilio_social' => 'DIR_TEST_CATCON_001', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crear categoría padre
        $categoriaPadre = CategoriaContable::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'nombre' => 'CATCON_PADRE_TEST_001', 'categoria_padre_id' => null]);
        $this->assertDatabaseHas('categoria_contable', [
            'id'     => $categoriaPadre->id,
            'nombre' => 'CATCON_PADRE_TEST_001',
        ]);

        // Crear categoría hija
        $categoriaHija = CategoriaContable::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'nombre' => 'CATCON_HIJO_TEST_001', 'categoria_padre_id' => $categoriaPadre->id]);
        $this->assertDatabaseHas('categoria_contable', [
            'id'     => $categoriaHija->id,
            'nombre' => 'CATCON_HIJO_TEST_001',
        ]);

        // Actualiza la categoría hija
        $categoriaHija->update(['nombre' => 'CATCON_HIJO_TEST_001_MOD']);
        $this->assertDatabaseHas('categoria_contable', [
            'id'     => $categoriaHija->id,
            'nombre' => 'CATCON_HIJO_TEST_001_MOD',
        ]);

        // Elimina la categoría hija
        $categoriaHija->delete();
        $this->assertSoftDeleted('categoria_contable', [
            'id' => $categoriaHija->id,
        ]);
    }
}