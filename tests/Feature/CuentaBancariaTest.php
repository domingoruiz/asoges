<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\CuentaBancaria;

class CuentaBancariaTest extends TestCase
{
    public function test_crud_cuentas_bancarias()
    {
        // Crear una asociación
        $aso = Aso::create(['nombre' => 'Asociación Cuenta Test', 'cif' => 'A00000003', 'domicilio_social' => 'Dirección Test', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        // Crea una cuenta bancaria
        $cuenta = new CuentaBancaria([
            'aso_id'         => $aso->id,
            'entidad_id'     => null,
            'moneda_id'      => null,
            'pais_id'        => null,
            'nombre'         => 'Cuenta Prueba',
            'numero_cuenta'  => 'ES00 0000 0000 0000 0000 0000',
            'swift_bic'      => 'PRUBESMMXXX',
            'fecha_apertura' => '2025-01-01',
            'observaciones'  => 'Cuenta de prueba',
            'direccion'      => 'Calle Prueba 1',
            'cp'             => '99999',
            'localidad'      => 'Ciudad Prueba',
            'provincia'      => 'Provincia Prueba',
            'telefono'       => '999999999',
            'email'          => 'cuenta@prueba.com',
            'alt_usr'        => 1,
        ]);
        $cuenta->save();

        $this->assertDatabaseHas('cuentas_bancarias', [
            'aso_id' => $aso->id,
            'nombre' => 'Cuenta Prueba',
        ]);

        // Actualiza la cuenta bancaria
        $cuenta->update([
            'nombre' => 'Cuenta Prueba Modificada',
        ]);
        $this->assertDatabaseHas('cuentas_bancarias', [
            'aso_id' => $aso->id,
            'nombre' => 'Cuenta Prueba Modificada',
        ]);

        // Elimina la cuenta bancaria
        $cuenta->delete();
        $this->assertSoftDeleted('cuentas_bancarias', [
            'id' => $cuenta->id,
        ]);
    }
}