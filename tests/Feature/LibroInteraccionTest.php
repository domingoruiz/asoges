<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\Contacto;
use App\Models\LibroInteraccion;
use App\Models\LibroProyecto;

class LibroInteraccionTest extends TestCase
{
    public function test_crud_libro_interaccion(): void
    {
        $aso = Aso::create([
            'nombre' => 'Asociación Interacciones Test',
            'cif' => 'A00000032',
            'domicilio_social' => 'Dirección Interacciones',
            'fch_constitucion' => now(),
            'alt_usr' => 1,
        ]);

        $proyecto = LibroProyecto::create([
            'aso_id' => $aso->id,
            'nombre' => 'Proyecto Aso Interacciones',
            'estado' => 'en_curso',
            'alt_usr' => 1,
        ]);

        $contacto = Contacto::create([
            'aso_id' => $aso->id,
            'nombre_completo' => 'Representante Deportes',
            'tipo' => 'Entidad Pública',
            'alt_usr' => 1,
        ]);

        // Crear interacción
        $interaccion = LibroInteraccion::create([
            'aso_id' => $aso->id,
            'fecha' => '2024-01-15',
            'libro_proyecto_id' => $proyecto->id,
            'contacto_id' => $contacto->id,
            'interaccion' => 'Directo',
            'oportunidad' => 'Mejora instalaciones',
            'notas' => 'Reunión inicial de coordinación',
            'alt_usr' => 1,
        ]);

        $this->assertDatabaseHas('libro_interacciones', [
            'aso_id' => $aso->id,
            'oportunidad' => 'Mejora instalaciones',
            'codigo' => 1,
        ]);

        // Crear segunda interacción y verificar autoincremento
        $interaccion2 = LibroInteraccion::create([
            'aso_id' => $aso->id,
            'fecha' => '2024-01-16',
            'interaccion' => 'Correo Electrónico',
            'oportunidad' => 'Seguimiento',
            'alt_usr' => 1,
        ]);

        $this->assertEquals(2, $interaccion2->codigo);

        // Actualizar
        $interaccion->update(['notas' => 'Reunión finalizada con acuerdos']);
        $this->assertDatabaseHas('libro_interacciones', [
            'id' => $interaccion->id,
            'notas' => 'Reunión finalizada con acuerdos',
        ]);

        // Eliminar
        $interaccion->delete();
        $this->assertSoftDeleted('libro_interacciones', [
            'id' => $interaccion->id,
        ]);

        // Nuevo registro tras borrado no debe colisionar
        $interaccion3 = LibroInteraccion::create([
            'aso_id' => $aso->id,
            'fecha' => '2024-01-17',
            'interaccion' => 'Llamada Telefónica',
            'alt_usr' => 1,
        ]);

        $this->assertEquals(3, $interaccion3->codigo);
    }
}
