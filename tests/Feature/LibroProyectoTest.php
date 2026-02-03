<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\LibroProyecto;

class LibroProyectoTest extends TestCase
{
    public function test_crud_libro_proyecto()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'Asociación Proyecto Test', 'cif' => 'A00000009', 'domicilio_social' => 'Dirección Test', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crea un proyecto
        $proyecto = LibroProyecto::create(['aso_id' => $aso->id, 'nombre' => 'Proyecto de Innovación', 'estado' => 'pendiente', 'fecha_inicio' => now()->toDateString(), 'fecha_fin' => now()->addMonths(6)->toDateString(), 'observaciones' => 'Proyecto de prueba en el libro de proyectos.', 'alt_usr' => 1]);
        $this->assertDatabaseHas('libro_proyectos', [
            'aso_id' => $aso->id,
            'nombre' => 'Proyecto de Innovación',
        ]);

        // Actualiza el proyecto
        $proyecto->update(['nombre' => 'Proyecto de Innovación Actualizado', 'estado' => 'finalizado']);
        $this->assertDatabaseHas('libro_proyectos', [
            'id'     => $proyecto->id,
            'nombre' => 'Proyecto de Innovación Actualizado',
            'estado' => 'finalizado',
        ]);

        // Elimina el proyecto
        $proyecto->delete();
        $this->assertSoftDeleted('libro_proyectos', [
            'id' => $proyecto->id,
        ]);
    }
}