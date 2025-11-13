<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_crud_user()
    {
        // Crea el usuario
        $user = User::create([
            'name' => 'Domingo Ruiz Arroyo',
            'email' => 'ordenadordomi@gmail.com',
            'password' => Hash::make('password123'),
            'alt_usr' => 1,
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Domingo Ruiz Arroyo',
            'email' => 'ordenadordomi@gmail.com',
        ]);

        // Actualiza el usuario
        $user->update([
            'name' => 'Domingo Ruiz Arroyo Modificado',
            'email' => 'modificado@ordenadordomi.com',
        ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Domingo Ruiz Arroyo Modificado',
            'email' => 'modificado@ordenadordomi.com',
        ]);

        // Elimina el usuario
        $user->delete();
        $this->assertSoftDeleted($user);
    }
}
