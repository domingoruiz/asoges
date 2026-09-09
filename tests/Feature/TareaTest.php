<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\Tarea;
use Illuminate\Support\Carbon;

class TareaTest extends TestCase
{
    public function test_crud_tarea(): void
    {
        $aso = Aso::create([
            'nombre' => 'Asociación Tareas Test',
            'cif' => 'A00000033',
            'domicilio_social' => 'Dirección Tareas',
            'fch_constitucion' => now(),
            'alt_usr' => 1,
        ]);

        $tarea = Tarea::create([
            'aso_id' => $aso->id,
            'nombre' => 'Revisión contable puntual',
            'fecha' => '2024-02-01',
            'frecuencia' => 'puntual',
            'estado' => 'pendiente',
            'descripcion' => 'Revisar extractos bancarios',
            'alt_usr' => 1,
        ]);

        $this->assertDatabaseHas('tareas', [
            'aso_id' => $aso->id,
            'nombre' => 'Revisión contable puntual',
            'codigo' => 1,
        ]);

        // Actualizar
        $tarea->update(['descripcion' => 'Revisar extractos y facturas']);
        $this->assertDatabaseHas('tareas', [
            'id' => $tarea->id,
            'descripcion' => 'Revisar extractos y facturas',
        ]);

        // Borrado suave
        $tarea->delete();
        $this->assertSoftDeleted('tareas', [
            'id' => $tarea->id,
        ]);

        // Siguiente código no colisiona
        $tarea2 = Tarea::create([
            'aso_id' => $aso->id,
            'nombre' => 'Segunda tarea',
            'fecha' => '2024-02-02',
            'frecuencia' => 'puntual',
            'estado' => 'pendiente',
            'alt_usr' => 1,
        ]);

        $this->assertEquals(2, $tarea2->codigo);
    }

    public function test_puntual_task_finalization_does_not_create_recurrence(): void
    {
        $aso = Aso::create([
            'nombre' => 'Aso Tarea Puntual Test',
            'cif' => 'A00000034',
            'domicilio_social' => 'Dir',
            'fch_constitucion' => now(),
            'alt_usr' => 1,
        ]);

        $tarea = Tarea::create([
            'aso_id' => $aso->id,
            'nombre' => 'Tarea Puntual Única',
            'fecha' => '2024-03-01',
            'frecuencia' => 'puntual',
            'estado' => 'pendiente',
            'alt_usr' => 1,
        ]);

        $tarea->update(['estado' => 'finalizado']);

        // Only 1 task should exist in this association
        $this->assertEquals(1, Tarea::where('aso_id', $aso->id)->count());
    }

    public function test_periodic_task_finalization_automatically_generates_next_task(): void
    {
        $aso = Aso::create([
            'nombre' => 'Aso Tarea Periódica Test',
            'cif' => 'A00000035',
            'domicilio_social' => 'Dir',
            'fch_constitucion' => now(),
            'alt_usr' => 1,
        ]);

        $tareaSemanal = Tarea::create([
            'aso_id' => $aso->id,
            'nombre' => 'Limpieza sede semanal',
            'fecha' => '2024-04-01',
            'frecuencia' => 'semanal',
            'estado' => 'en_curso',
            'descripcion' => 'Limpieza general de las oficinas',
            'alt_usr' => 1,
        ]);

        // Finalize the task
        $tareaSemanal->update(['estado' => 'finalizado']);

        // Next task should have been created for 7 days later
        $expectedNextDate = Carbon::parse('2024-04-01')->addWeek()->toDateString();

        $this->assertEquals(2, Tarea::where('aso_id', $aso->id)->count());

        $siguienteTarea = Tarea::where('aso_id', $aso->id)
            ->where('estado', 'pendiente')
            ->first();

        $this->assertNotNull($siguienteTarea);
        $this->assertEquals('Limpieza sede semanal', $siguienteTarea->nombre);
        $this->assertEquals($expectedNextDate, $siguienteTarea->fecha->toDateString());
        $this->assertEquals('semanal', $siguienteTarea->frecuencia);
    }
}
