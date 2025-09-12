<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        $usuarios = [
            ['nombre' => 'Juan', 'apellido' => 'Pérez', 'email' => 'juan@example.com', 'rol' => 'tallerista'],
            ['nombre' => 'María', 'apellido' => 'González', 'email' => 'maria@example.com', 'rol' => 'tallerista'],
            ['nombre' => 'Carlos', 'apellido' => 'Rodríguez', 'email' => 'carlos@example.com', 'rol' => 'admin'],
            ['nombre' => 'Laura', 'apellido' => 'Martínez', 'email' => 'laura@example.com', 'rol' => 'tallerista'],
        ];

        foreach ($usuarios as $u) {
            Usuario::create($u);
        }
    }
}
