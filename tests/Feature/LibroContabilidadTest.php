<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\Currency;
use App\Models\Entidad;
use App\Models\Ejercicio;
use App\Models\CuentaBancaria;
use App\Models\CategoriaContable;
use App\Models\TipoTransaccion;
use App\Models\LibroContabilidad;

class LibroContabilidadTest extends TestCase
{
    public function test_crud_libro_contabilidad()
    {
        // Crear maestros
        $aso = Aso::create(['nombre' => 'Asociación Contabilidad Test', 'cif' => 'A00000007', 'domicilio_social' => 'Dirección Test', 'fch_constitucion' => now(), 'alt_usr' => 1]);
        $moneda = Currency::create(['codigo_iso' => 'ZZZ', 'nombre' => 'Moneda Contabilidad Test', 'nombre_en' => 'Accounting Test Coin', 'simbolo' => '¤', 'alt_usr' => 1]);
        $entidad = Entidad::create(['aso_id' => $aso->id, 'nombre_fiscal' => 'Entidad Contabilidad', 'cif' => 'P0000002Z', 'direccion' => 'Calle Contabilidad 1', 'cp' => '29001', 'localidad' => 'Ciudad Contable', 'provincia' => 'Provincia Contable', 'pais' => 1, 'continente' => 1, 'telefono' => '600000001', 'email' => 'contabilidad@example.com', 'web' => 'https://contabilidad.com', 'swift_bic' => 'CONTBESMXXX', 'iban' => 'ZZ00 0000 0000 0000 0000 0002', 'moneda' => $moneda->id, 'observaciones' => 'Entidad para pruebas de contabilidad', 'alt_usr' => 1]);
        $ejercicio = Ejercicio::create(['aso_id' => $aso->id, 'nombre' => 'Ejercicio 2026', 'fch_inicio' => now()->startOfYear(), 'fch_fin' => now()->endOfYear(), 'alt_usr' => 1]);
        $cuentaBancaria = CuentaBancaria::create(['aso_id' => $aso->id, 'entidad_id' => $entidad->id, 'moneda_id' => $moneda->id, 'nombre' => 'cuenta', 'descripcion' => 'Cuenta Principal', 'iban' => 'ZZ00 0000 0000 0000 0000 0003', 'swift_bic' => 'CBANBESMXXX', 'alt_usr' => 1]);
        $categoria = CategoriaContable::create(['aso_id' => $aso->id, 'nombre' => 'Cuotas Socios', 'alt_usr' => 1]);
        $tipoTransaccion = TipoTransaccion::create(['aso_id' => $aso->id, 'nombre' => 'Ingreso', 'alt_usr' => 1]);

        // Crea un asiento contable
        $asiento = LibroContabilidad::create(['alt_usr' => 1, 'aso_id' => $aso->id, 'tipo_transaccion_id' => $tipoTransaccion->id, 'ejercicio_id' => $ejercicio->id, 'moneda_id' => $moneda->id, 'entidad_id' => $entidad->id, 'cuenta_bancaria_id' => $cuentaBancaria->id, 'categoria_id' => $categoria->id, 'fecha_contable' => now()->toDateString(), 'importe' => 100.00, 'concepto' => 'Cuota socio enero', 'descripcion' => 'Cuota de socio correspondiente al mes de enero.']);
        $this->assertDatabaseHas('contabilidad', [
            'aso_id'   => $aso->id,
            'concepto' => 'Cuota socio enero',
        ]);

        // Actualiza el asiento contable
        $asiento->update(['concepto' => 'Cuota socio enero modificada', 'importe' => 150.50]);
        $this->assertDatabaseHas('contabilidad', [
            'id'       => $asiento->id,
            'concepto' => 'Cuota socio enero modificada',
        ]);

        // Elimina el asiento contable
        $asiento->delete();
        $this->assertSoftDeleted('contabilidad', [
            'id' => $asiento->id,
        ]);
    }
}