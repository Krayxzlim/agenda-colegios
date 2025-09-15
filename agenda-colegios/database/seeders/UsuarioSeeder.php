<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        $usuarios = [
            ['nombre' => 'Juan', 'apellido' => 'Pérez', 'email' => 'juan@example.com','password' => Hash::make('admin123'),'rol' => 'tallerista'],
            ['nombre' => 'María', 'apellido' => 'González', 'email' => 'maria@example.com','password' => Hash::make('admin123'), 'rol' => 'tallerista'],
            ['nombre' => 'Carlos', 'apellido' => 'Rodríguez', 'email' => 'carlos@example.com','password' => Hash::make('admin123'), 'rol' => 'admin'],
            ['nombre' => 'Laura', 'apellido' => 'Martínez', 'email' => 'laura@example.com','password' => Hash::make('admin123'), 'rol' => 'tallerista'],
        ];

        foreach ($usuarios as $u) {
            Usuario::create($u);
        }
    }
}
