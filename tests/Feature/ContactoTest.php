<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Aso;
use App\Models\Contacto;

class ContactoTest extends TestCase
{
    public function test_crud_contacto(): void
    {
        $aso = Aso::create([
            'nombre' => 'Asociación Contactos Test',
            'cif' => 'A00000031',
            'domicilio_social' => 'Dirección Contactos',
            'fch_constitucion' => now(),
            'alt_usr' => 1,
        ]);

        // Crear contacto
        $contacto = Contacto::create([
            'aso_id' => $aso->id,
            'nombre_completo' => 'Juan Pérez Gómez',
            'tipo' => 'Proveedor',
            'posicion' => 'Gerente',
            'email' => 'juan@empresa.com',
            'telefono' => '600123456',
            'notas' => 'Contacto inicial',
            'alt_usr' => 1,
        ]);

        $this->assertDatabaseHas('contactos', [
            'aso_id' => $aso->id,
            'nombre_completo' => 'Juan Pérez Gómez',
            'codigo' => 1,
        ]);

        // Crear segundo contacto y verificar autoincremento de código
        $contacto2 = Contacto::create([
            'aso_id' => $aso->id,
            'nombre_completo' => 'María López García',
            'tipo' => 'Patrocinador',
            'alt_usr' => 1,
        ]);

        $this->assertEquals(2, $contacto2->codigo);

        // Actualizar contacto
        $contacto->update(['posicion' => 'Director General']);
        $this->assertDatabaseHas('contactos', [
            'id' => $contacto->id,
            'posicion' => 'Director General',
        ]);

        // Eliminar contacto
        $contacto->delete();
        $this->assertSoftDeleted('contactos', [
            'id' => $contacto->id,
        ]);

        // Nuevo contacto tras borrado suave no debe colisionar en código
        $contacto3 = Contacto::create([
            'aso_id' => $aso->id,
            'nombre_completo' => 'Carlos Ruiz',
            'alt_usr' => 1,
        ]);

        $this->assertEquals(3, $contacto3->codigo);
    }
}
