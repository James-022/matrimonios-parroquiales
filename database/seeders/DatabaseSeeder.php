<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $administrador = Role::firstOrCreate(
            ['nombre' => 'Administrador'],
            ['descripcion' => 'Administración del sistema']
        );

        Role::firstOrCreate(
            ['nombre' => 'Encargado'],
            ['descripcion' => 'Gestión de expedientes y documentos']
        );

        Role::firstOrCreate(
            ['nombre' => 'Contrayente'],
            ['descripcion' => 'Consulta de información personal autorizada']
        );

        User::firstOrCreate(
            ['email' => 'admin@matrimonios.local'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Admin12345!'),
                'role_id' => $administrador->id,
                'activo' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
