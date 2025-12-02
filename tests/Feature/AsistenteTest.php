<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\Rol;
use App\Models\SocioTipo;
use App\Models\LibroSocios;
use App\Models\TipoActa;
use App\Models\EstadoActa;
use App\Models\LibroActa;
use App\Models\Asistente;

class AsistenteTest extends TestCase
{
    public function test_crud_asistente()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'ASO_TEST_ASISTENTE_001', 'cif' => 'A000TST11', 'domicilio_social' => 'DIR_TEST_ASISTENTE_001', 'fch_constitucion' => now(), 'alt_usr' => 1]);
        $rol = Rol::create(['aso_id' => $aso->id, 'nombre' => 'ROL_ASISTENTE_001', 'alt_usr' => 1]);
        $tipoSocio = SocioTipo::create(['nombre' => 'TIPO_SOCIO_ASISTENTE_001', 'alt_usr' => 1]);
        $socio1 = LibroSocios::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'rol_id' => 1, 'tipo_socio_id' => $tipoSocio->id, 'pais_id' => 1, 'continente_id' => 1, 'numero_socio' => 'NSOC_ASIS_001', 'nombre' => 'NOMBRE_ASIS_001', 'apellidos' => 'APELLIDOS_ASIS_001', 'dni' => 'DNIASIS001', 'telefono' => '600000010', 'email' => 'asis001@example.test', 'fecha_nacimiento' => '2001-01-01', 'direccion' => 'DIR_SOCIO_ASIS_001', 'cp' => '00001', 'localidad' => 'LOC_ASIS_001', 'nombre_tutor' => null, 'dni_tutor' => null, 'telefono_tutor' => null]);
        $socio2 = LibroSocios::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'rol_id' => 1, 'tipo_socio_id' => $tipoSocio->id, 'pais_id' => 1, 'continente_id' => 1, 'numero_socio' => 'NSOC_ASIS_002', 'nombre' => 'NOMBRE_ASIS_002', 'apellidos' => 'APELLIDOS_ASIS_002', 'dni' => 'DNIASIS002', 'telefono' => '600000011', 'email' => 'asis002@example.test', 'fecha_nacimiento' => '2002-02-02', 'direccion' => 'DIR_SOCIO_ASIS_002', 'cp' => '00002', 'localidad' => 'LOC_ASIS_002', 'nombre_tutor' => null, 'dni_tutor' => null, 'telefono_tutor' => null]);
        $tipoActa = TipoActa::create(['aso_id' => $aso->id, 'nombre' => 'TIPO_ACTA_ASISTENTE_001', 'alt_usr' => 1]);
        $estadoActa = EstadoActa::create(['aso_id' => $aso->id, 'nombre' => 'ESTADO_ACTA_ASISTENTE_001', 'alt_usr' => 1]);

        // Crear acta
        $acta = LibroActa::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'tipo_acta_id' => $tipoActa->id, 'estado_acta_id' => $estadoActa->id, 'titulo' => 'ACTA_ASISTENTE_001', 'fecha' => now()->toDateString(), 'hora_inicio' => '10:00:00', 'hora_fin' => '11:00:00', 'lugar_reunion' => 'LUGAR_ASISTENTE_001', 'contenido_acta' => 'CONTENIDO_ACTA_ASISTENTE_001', 'aprobada' => false, 'fecha_aprobacion' => null]);

        // Crea un asistente
        $asistente = Asistente::create(['alt_usr' => 1, 'acta_id' => $acta->id, 'socio_id' => $socio1->id]);
        $this->assertDatabaseHas('asistentes', [
            'acta_id' => $acta->id,
            'socio_id'=> $socio1->id,
        ]);

        // Actualiza el asistente
        $asistente->update(['socio_id' => $socio2->id]);
        $this->assertDatabaseHas('asistentes', [
            'id'      => $asistente->id,
            'acta_id' => $acta->id,
            'socio_id'=> $socio2->id,
        ]);

        // Elimina el asistente
        $asistente->delete();
        $this->assertSoftDeleted('asistentes', [
            'id' => $asistente->id,
        ]);
    }
}