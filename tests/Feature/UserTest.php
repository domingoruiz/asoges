<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');
    }

    public function test_crud_user()
    {
        $user = User::create([
            'name' => 'Domingo Ruiz Arroyo',
            'email' => 'ordenadordomi@gmail.com',
            'password' => Hash::make('password123'),
            'alt_usr' => 1,
            'mod_usr' => 1,
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

        // Elimina el usuario (soft delete)
        $user->delete();
        $this->assertSoftDeleted($user);
    }
}
