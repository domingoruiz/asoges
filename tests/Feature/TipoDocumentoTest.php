<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\TipoDocumento;

class TipoDocumentoTest extends TestCase
{
    public function test_crud_tipo_documento()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'ASO_TEST_TIPODOC_001', 'cif' => 'A000TST15', 'domicilio_social' => 'DIR_TEST_TIPODOC_001', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crear categoría padre
        $categoriaPadre = TipoDocumento::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'nombre' => 'TIPODOC_PADRE_TEST_001', 'categoria_padre_id' => null]);
        $this->assertDatabaseHas('tipo_documento', [
            'id'     => $categoriaPadre->id,
            'nombre' => 'TIPODOC_PADRE_TEST_001',
        ]);

        // Crear subcategoría
        $subcategoria = TipoDocumento::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'nombre' => 'TIPODOC_HIJO_TEST_001', 'categoria_padre_id' => $categoriaPadre->id]);
        $this->assertDatabaseHas('tipo_documento', [
            'id'     => $subcategoria->id,
            'nombre' => 'TIPODOC_HIJO_TEST_001',
        ]);

        // Actualiza la subcategoría
        $subcategoria->update(['nombre' => 'TIPODOC_HIJO_TEST_001_MOD']);
        $this->assertDatabaseHas('tipo_documento', [
            'id'     => $subcategoria->id,
            'nombre' => 'TIPODOC_HIJO_TEST_001_MOD',
        ]);

        // Elimina la subcategoría
        $subcategoria->delete();
        $this->assertSoftDeleted('tipo_documento', [
            'id' => $subcategoria->id,
        ]);
    }
}