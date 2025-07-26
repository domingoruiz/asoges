<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rol')->insert([
            ['nombre' => 'Presidente'],
            ['nombre' => 'Secretario'],
            ['nombre' => 'Tesorero'],
            ['nombre' => 'Vocal'],
        ]);
    }
}
