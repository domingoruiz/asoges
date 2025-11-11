<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RolSeeder extends Seeder
{

    public function run(): void
    {
        $now = Carbon::now();
        
        Rol::create([
                'nombre' => 'Presidente',
                'alt_usr' => 1,
                'mod_usr' => 1,
                'created_at' => $now,
                'updated_at' => $now
        ]);

        Rol::create([
                'nombre' => 'Secretario',
                'alt_usr' => 1,
                'mod_usr' => 1,
                'created_at' => $now,
                'updated_at' => $now
        ]);

        Rol::create([
                'nombre' => 'Tesorero',
                'alt_usr' => 1,
                'mod_usr' => 1,
                'created_at' => $now,
                'updated_at' => $now,
        ]);

        Rol::create([
                'nombre' => 'Vocal',
                'alt_usr' => 1,
                'mod_usr' => 1,
                'created_at' => $now,
                'updated_at' => $now,
        ]);

        Rol::create([
            'nombre' => 'Socio',
            'alt_usr' => 1,
            'mod_usr' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
