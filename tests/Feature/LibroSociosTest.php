<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\SocioTipo;
use App\Models\LibroSocios;

class LibroSociosTest extends TestCase
{
    public function test_crud_libro_socios()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'Asociación Socios Test', 'cif' => 'A00000010', 'domicilio_social' => 'Dirección Test', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crear tipo de socio
        $tipoSocio = SocioTipo::create(['nombre' => 'General', 'alt_usr' => 1]);

        // Crea un socio
        $socio = LibroSocios::create([
            'alt_usr'          => 1,
            'aso_id'           => $aso->id,
            'rol_id'           => 1,
            'tipo_socio_id'    => $tipoSocio->id,
            'pais_id'          => 1,
            'continente_id'    => 1,
            'numero_socio'     => 'S001',
            'nombre'           => 'Juan',
            'apellidos'        => 'Pérez López',
            'dni'              => '12345678A',
            'telefono'         => '600000003',
            'email'            => 'juan@example.com',
            'fecha_nacimiento' => '2000-01-01',
            'direccion'        => 'Calle Socios 1',
            'cp'               => '29003',
            'localidad'        => 'Ciudad Socios',
            'nombre_tutor'     => null,
            'dni_tutor'        => null,
            'telefono_tutor'   => null,
        ]);

        $this->assertDatabaseHas('socios', [
            'aso_id'   => $aso->id,
            'dni'      => '12345678A',
            'nombre'   => 'Juan',
        ]);

        // Actualiza el socio
        $socio->update(['nombre' => 'Juan Actualizado', 'telefono' => '600000999']);
        $this->assertDatabaseHas('socios', [
            'id'     => $socio->id,
            'nombre' => 'Juan Actualizado',
        ]);

        // Elimina el socio
        $socio->delete();
        $this->assertSoftDeleted('socios', [
            'id' => $socio->id,
        ]);
    }
}