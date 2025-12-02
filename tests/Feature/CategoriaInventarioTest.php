<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\CategoriaInventario;

class CategoriaInventarioTest extends TestCase
{
    public function test_crud_categoria_inventario()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'ASO_TEST_CATINV_001', 'cif' => 'A000TST13', 'domicilio_social' => 'DIR_TEST_CATINV_001', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crear categoría padre
        $categoriaPadre = CategoriaInventario::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'nombre' => 'CATINV_PADRE_TEST_001', 'categoria_padre_id' => null]);
        $this->assertDatabaseHas('categoria_inventario', [
            'id'     => $categoriaPadre->id,
            'nombre' => 'CATINV_PADRE_TEST_001',
        ]);

        // Crear categoría hija
        $categoriaHija = CategoriaInventario::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'nombre' => 'CATINV_HIJO_TEST_001', 'categoria_padre_id' => $categoriaPadre->id]);
        $this->assertDatabaseHas('categoria_inventario', [
            'id'     => $categoriaHija->id,
            'nombre' => 'CATINV_HIJO_TEST_001',
        ]);

        // Actualiza la categoría hija
        $categoriaHija->update(['nombre' => 'CATINV_HIJO_TEST_001_MOD']);
        $this->assertDatabaseHas('categoria_inventario', [
            'id'     => $categoriaHija->id,
            'nombre' => 'CATINV_HIJO_TEST_001_MOD',
        ]);

        // Elimina la categoría hija
        $categoriaHija->delete();
        $this->assertSoftDeleted('categoria_inventario', [
            'id' => $categoriaHija->id,
        ]);
    }
}