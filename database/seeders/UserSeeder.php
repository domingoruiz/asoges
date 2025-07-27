<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        User::create([
            'name' => 'SYSTEM',
            'email' => 'system@asoges.local',
            'password' => bin2hex(random_bytes(10 / 2)),
            'alt_usr' => 1,
            'mod_usr' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@asoges.local',
            'password' => Hash::make('12345678'),
            'is_superadmin' => 1,
            'alt_usr' => 1,
            'mod_usr' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
