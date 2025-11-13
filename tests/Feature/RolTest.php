<?php

namespace Tests\Feature;

use App\Models\Rol;
use Tests\TestCase;

class RolTest extends TestCase
{
    public function test_crud_rol()
    {
        // Crea un rol
        $rol = Rol::create([
            'nombre' => 'Administrador',
            'alt_usr' => 1,
            'mod_usr' => 1,
        ]);
        $this->assertDatabaseHas('rol', [
            'nombre' => 'Administrador',
        ]);

        // Actualiza el rol
        $rol->update([
            'nombre' => 'Administrador Modificado',
        ]);
        $this->assertDatabaseHas('rol', [
            'nombre' => 'Administrador Modificado',
        ]);

        // Elimina el rol
        $rol->delete();
        $this->assertDatabaseMissing('rol', [
            'nombre' => 'Administrador Modificado',
        ]);
    }
}