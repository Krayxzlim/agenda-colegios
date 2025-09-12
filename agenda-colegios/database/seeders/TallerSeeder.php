<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Taller;

class TallerSeeder extends Seeder
{
    public function run()
    {
        $talleres = [
            ['nombre' => 'Taller de Robótica', 'descripcion' => 'Introducción a la robótica', 'capacidad' => 10],
            ['nombre' => 'Taller de Música', 'descripcion' => 'Aprender a tocar instrumentos', 'capacidad' => 15],
            ['nombre' => 'Taller de Arte', 'descripcion' => 'Pintura y dibujo', 'capacidad' => 12],
            ['nombre' => 'Taller de Deportes', 'descripcion' => 'Fútbol, básquet y vóley', 'capacidad' => 20],
        ];

        foreach ($talleres as $t) {
            Taller::create($t);
        }
    }
}
