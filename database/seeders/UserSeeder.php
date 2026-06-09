<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Administrador entra con el usuario: ramiro
        User::create([
            'username' => 'ramiro',
            'name'     => 'Ramiro Admin',
            'email'    => 'admin@correo.com',
            'role'     => 'admin',
            'password' => Hash::make('123456'),
        ]);

        // 2. Digitador entra con el usuario: juan
        User::create([
            'username' => 'juan',
            'name'     => 'Juan Digitador',
            'email'    => 'digitador@correo.com',
            'role'     => 'digitador',
            'password' => Hash::make('123456'),
        ]);
    }
}
