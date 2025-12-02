<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\TipoActa;
use App\Models\EstadoActa;
use App\Models\LibroActa;

class LibroActaTest extends TestCase
{
    public function test_crud_libro_acta()
    {
        // Crear maestros
        $aso = Aso::create(['nombre' => 'Asociación Libro Acta Test', 'cif' => 'A00000006', 'domicilio_social' => 'Dirección Test', 'fch_constitucion' => now(), 'alt_usr' => 1]);
        $tipoActa = TipoActa::create(['aso_id' => $aso->id, 'nombre' => 'Junta Directiva', 'alt_usr' => 1]);
        $estadoActa = EstadoActa::create(['aso_id' => $aso->id, 'nombre' => 'Borrador', 'alt_usr' => 1]);

        // Crea un acta
        $acta = LibroActa::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'tipo_acta_id' => $tipoActa->id, 'estado_acta_id' => $estadoActa->id, 'titulo' => 'Acta de Junta Directiva', 'fecha' => now()->toDateString(), 'hora_inicio' => '10:00:00', 'hora_fin' => '12:00:00', 'lugar_reunion' => 'Sala de reuniones', 'contenido_acta' => 'Contenido inicial del acta.', 'aprobada' => false, 'fecha_aprobacion' => null]);
        $this->assertDatabaseHas('actas', [
            'aso_id' => $aso->id,
            'titulo' => 'Acta de Junta Directiva',
        ]);

        // Actualiza el acta
        $acta->update(['titulo' => 'Acta de Junta Directiva Modificada', 'aprobada' => true, 'fecha_aprobacion' => now()]);
        $this->assertDatabaseHas('actas', [
            'id'      => $acta->id,
            'titulo'  => 'Acta de Junta Directiva Modificada',
            'aprobada'=> 1,
        ]);

        // Elimina el acta
        $acta->delete();
        $this->assertSoftDeleted('actas', [
            'id' => $acta->id,
        ]);
    }
}