<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\Entidad;
use App\Models\CategoriaInventario;
use App\Models\Ubicacion;
use App\Models\LibroInventario;

class LibroInventarioTest extends TestCase
{
    public function test_crud_libro_inventario()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'Asociación Inventario Test', 'cif' => 'A00000008', 'domicilio_social' => 'Dirección Test', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crear una entidad
        $entidad = Entidad::create(['aso_id' => $aso->id, 'nombre_fiscal' => 'Entidad Inventario', 'cif' => 'P0000003Z', 'direccion' => 'Calle Inventario 1', 'cp' => '29002', 'localidad' => 'Ciudad Inventario', 'provincia' => 'Provincia Inventario', 'pais' => 1, 'continente' => 1, 'telefono' => '600000002', 'email' => 'inventario@example.com', 'web' => 'https://inventario.com', 'swift_bic' => 'INVNBESMXXX', 'iban' => 'ZZ00 0000 0000 0000 0000 0004', 'moneda' => 1, 'observaciones' => 'Entidad para pruebas de inventario', 'alt_usr' => 1]);

        // Crear una categoría de inventario
        $categoria = CategoriaInventario::create(['aso_id' => $aso->id, 'nombre' => 'Material Informático', 'alt_usr' => 1]);

        // Crear una ubicación
        $ubicacion = Ubicacion::create(['aso_id' => $aso->id, 'nombre' => 'Almacén Principal', 'alt_usr' => 1]);

        // Crea un elemento de inventario
        $item = LibroInventario::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'fecha_adquisicion' => now()->toDateString(), 'nombre' => 'Portátil HP', 'categoria_id' => $categoria->id, 'ubicacion_id' => $ubicacion->id, 'entidad_id' => $entidad->id, 'cantidad' => 10, 'valor' => 1000.00, 'descripcion' => 'Lote de portátiles HP para aula de informática.']);
        $this->assertDatabaseHas('inventario', [
            'aso_id' => $aso->id,
            'nombre' => 'Portátil HP',
        ]);

        // Actualiza el elemento de inventario
        $item->update(['nombre' => 'Portátil HP Actualizado', 'cantidad' => 15]);
        $this->assertDatabaseHas('inventario', [
            'id'     => $item->id,
            'nombre' => 'Portátil HP Actualizado',
        ]);

        // Elimina el elemento de inventario
        $item->delete();
        $this->assertSoftDeleted('inventario', [
            'id' => $item->id,
        ]);
    }
}