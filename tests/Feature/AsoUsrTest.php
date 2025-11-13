<?php

namespace Tests\Feature;

use App\Models\Aso;
use App\Models\User;
use App\Models\Rol;
use App\Models\AsoUsr;
use Tests\TestCase;

class AsoUsrTest extends TestCase
{
    public function test_crud_aso_usr()
    {
        // Crear un usuario
        $user = User::create(['name' => 'Usuario Test', 'email' => 'usuario@test.com', 'password' => bcrypt('password123'), 'alt_usr' => 1]);
        $aso = Aso::create(['nombre' => 'Asociación Test', 'cif' => 'A12345678', 'domicilio_social' => 'Vélez-Málaga', 'fch_constitucion' => now(), 'alt_usr' => 1]);
        $rol = Rol::create(['nombre' => 'Miembro', 'alt_usr' => 1]);

        // Crear un registro en aso_usr
        $asoUsr = AsoUsr::create([
            'aso_id' => $aso->id,
            'usr_id' => $user->id,
            'rol_id' => $rol->id,
            'alt_usr' => 1,
        ]);
        $this->assertDatabaseHas('aso_usr', [
            'aso_id' => $aso->id,
            'usr_id' => $user->id,
            'rol_id' => $rol->id,
        ]);

        // Actualiza el registro
        $asoUsr->update([
            'rol_id' => Rol::create([
                'nombre' => 'Ayudante',
                'alt_usr' => 1,
            ])->id,
        ]);

        $this->assertDatabaseHas('aso_usr', [
            'rol_id' => $asoUsr->rol_id,
        ]);

        // Elimina el registro
        $asoUsr->delete();
        $this->assertDatabaseMissing('aso_usr', [
            'aso_id' => $aso->id,
            'usr_id' => $user->id,
            'rol_id' => $rol->id,
        ]);
    }
}