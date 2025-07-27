<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContinentsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('continents')->insert([
            ['codigo' => 'AF', 'nombre' => 'África', 'alt_usr' => 1, 'mod_usr' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => 'AN', 'nombre' => 'Antártida', 'alt_usr' => 1, 'mod_usr' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => 'AS', 'nombre' => 'Asia', 'alt_usr' => 1, 'mod_usr' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => 'EU', 'nombre' => 'Europa', 'alt_usr' => 1, 'mod_usr' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => 'AM', 'nombre' => 'América', 'alt_usr' => 1, 'mod_usr' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['codigo' => 'OC', 'nombre' => 'Oceanía', 'alt_usr' => 1, 'mod_usr' => 1, 'created_at' => $now, 'updated_at' => $now]
        ]);
    }
}