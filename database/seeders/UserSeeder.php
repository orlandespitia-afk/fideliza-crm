<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 👑 0. El Super Admin (El único que podrá crear a otros desde la interfaz)
        User::updateOrCreate(
            ['username' => 'superadmin'], // Lo busca por username para no duplicarlo
            [
                'name'     => 'Super Administrador',
                'email'    => 'superadmin@correo.com',
                'role'     => 'super_admin',
                'password' => Hash::make('123456'), // Puedes cambiar esta clave
            ]
        );

        // 1. Administrador entra con el usuario: ramiro
        User::updateOrCreate(
            ['username' => 'ramiro'],
            [
                'name'     => 'Ramiro Admin',
                'email'    => 'admin@correo.com',
                'role'     => 'admin',
                'password' => Hash::make('123456'),
            ]
        );

        // 2. Digitador entra con el usuario: juan
        User::updateOrCreate(
            ['username' => 'juan'],
            [
                'name'     => 'Juan Digitador',
                'email'    => 'digitador@correo.com',
                'role'     => 'digitador',
                'password' => Hash::make('123456'),
            ]
        );
    }
}
