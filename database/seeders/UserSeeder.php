<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Administrador
        User::create([
            'name'     => 'Carlos Administrador',
            'email'    => 'admin@gimnasio.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'active'   => true,
        ]);

        // Recepcionistas
        User::create([
            'name'     => 'María Recepcionista',
            'email'    => 'maria@gimnasio.com',
            'password' => Hash::make('password'),
            'role'     => 'receptionist',
            'active'   => true,
        ]);

        User::create([
            'name'     => 'Pedro Recepcionista',
            'email'    => 'pedro@gimnasio.com',
            'password' => Hash::make('password'),
            'role'     => 'receptionist',
            'active'   => true,
        ]);

        // Entrenadores
        User::create([
            'name'     => 'Luis Entrenador',
            'email'    => 'luis@gimnasio.com',
            'password' => Hash::make('password'),
            'role'     => 'trainer',
            'active'   => true,
        ]);

        User::create([
            'name'     => 'Ana Entrenadora',
            'email'    => 'ana@gimnasio.com',
            'password' => Hash::make('password'),
            'role'     => 'trainer',
            'active'   => true,
        ]);

        User::create([
            'name'     => 'Emiliano',
            'email'    => 'eeeee@gmail.com',
            'password' => Hash::make('password'),
            'role'     => 'trainer',
            'active'   => true,
        ]);
    }
}
