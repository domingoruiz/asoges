<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\SocioTipo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SocioTipoSeeder extends Seeder
{

    public function run(): void
    {
        $now = Carbon::now();

        SocioTipo::create([
            'nombre' => 'Socio fundador',
            'alt_usr' => 1,
            'mod_usr' => 1,
            'created_at' => $now,
            'updated_at' => $now
        ]);

        SocioTipo::create([
            'nombre' => 'Socio de número',
            'alt_usr' => 1,
            'mod_usr' => 1,
            'created_at' => $now,
            'updated_at' => $now
        ]);

        SocioTipo::create([
            'nombre' => 'Socio de honor',
            'alt_usr' => 1,
            'mod_usr' => 1,
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}