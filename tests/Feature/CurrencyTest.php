<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Currency;

class CurrencyTest extends TestCase
{
    public function test_crud_currency()
    {
        // Crea una moneda
        $currency = Currency::create([
            'codigo_iso' => 'ZZZ',
            'nombre'     => 'Moneda Prueba',
            'nombre_en'  => 'Test Coin',
            'simbolo'    => '¤',
            'alt_usr'    => 1,
        ]);
        $this->assertDatabaseHas('currencies', [
            'codigo_iso' => 'ZZZ',
            'nombre'     => 'Moneda Prueba',
        ]);

        // Actualiza la moneda
        $currency->update([
            'nombre'    => 'Moneda Prueba 2',
            'nombre_en' => 'Test Coin Updated',
        ]);
        $this->assertDatabaseHas('currencies', [
            'codigo_iso' => 'ZZZ',
            'nombre'     => 'Moneda Prueba 2',
            'nombre_en'  => 'Test Coin Updated',
        ]);

        // Elimina la moneda
        $currency->delete();
        $this->assertDatabaseMissing('currencies', [
            'codigo_iso' => 'ZZZ',
            'nombre'     => 'Moneda Prueba 2',
        ]);
    }
}