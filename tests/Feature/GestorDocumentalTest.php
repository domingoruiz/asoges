<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\Entidad;
use App\Models\TipoDocumento;
use App\Models\Ejercicio;
use App\Models\EstadoDocumento;
use App\Models\GestorDocumental;

class GestorDocumentalTest extends TestCase
{
    public function test_crud_gestor_documental()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'Asociación Gestor Documental Test', 'cif' => 'A00000005', 'domicilio_social' => 'Dirección Test', 'fch_constitucion' => now(), 'alt_usr' => 1]);
        $entidad = Entidad::create(['aso_id' => $aso->id, 'nombre_fiscal' => 'Entidad Gestor Doc', 'cif' => 'P0000001Z', 'direccion' => 'Calle Prueba 1', 'cp' => '29000', 'localidad' => 'Ciudad Prueba', 'provincia' => 'Provincia Prueba', 'pais' => 1, 'continente' => 1, 'telefono' => '600000000', 'email' => 'entidaddoc@example.com', 'web' => 'https://entidaddoc.com', 'swift_bic' => 'ENTDOCMMXXX', 'iban' => 'ZZ00 0000 0000 0000 0000 0001', 'moneda' => 1, 'observaciones' => 'Entidad para gestor documental', 'alt_usr' => 1]);
        $tipoDocumento = TipoDocumento::create(['aso_id' => $aso->id, 'nombre' => 'Factura', 'alt_usr' => 1]);
        $ejercicio = Ejercicio::create(['aso_id' => $aso->id, 'nombre' => 'Ejercicio 2025', 'fch_inicio' => now()->startOfYear(), 'fch_fin' => now()->endOfYear(), 'alt_usr' => 1]);
        $estado = EstadoDocumento::create(['aso_id' => $aso->id, 'nombre' => 'Pendiente', 'alt_usr' => 1]);

        // Crea un documento en el gestor documental
        $documento = GestorDocumental::create([
            'aso_id'              => $aso->id,
            'tipo_documento_id'   => $tipoDocumento->id,
            'entidad_id'          => $entidad->id,
            'ejercicio_id'        => $ejercicio->id,
            'estado_documento'    => $estado->id,
            'direccion_documento' => 'entrada',
            'fecha_documento'     => now()->toDateString(),
            'numero_serie'        => 'DOC-001',
            'ref_externa'         => 'REF-001',
            'nombre'              => 'Factura de prueba',
            'descripcion'         => 'Factura de prueba para el gestor documental',
            'archivo'             => null,
            'alt_usr'             => 1,
        ]);

        $this->assertDatabaseHas('gestor_documental', [
            'aso_id'       => $aso->id,
            'numero_serie' => 'DOC-001',
            'nombre'       => 'Factura de prueba',
        ]);

        // Actualiza el documento del gestor documental
        $documento->update([
            'nombre'      => 'Factura de prueba modificada',
            'ref_externa' => 'REF-002',
        ]);
        $this->assertDatabaseHas('gestor_documental', [
            'id'          => $documento->id,
            'nombre'      => 'Factura de prueba modificada',
            'ref_externa' => 'REF-002',
        ]);

        // Elimina el documento del gestor documental
        $documento->delete();
        $this->assertSoftDeleted('gestor_documental', [
            'id' => $documento->id,
        ]);
    }
}