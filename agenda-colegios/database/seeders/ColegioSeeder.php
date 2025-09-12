<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Colegio;

class ColegioSeeder extends Seeder
{
    public function run()
    {
        $colegios = [
            ['nombre' => 'Colegio San Juan', 'direccion' => 'Calle Falsa 123'],
            ['nombre' => 'Colegio Santa María', 'direccion' => 'Av. Libertador 456'],
            ['nombre' => 'Colegio Nuestra Señora', 'direccion' => 'Calle Real 789'],
        ];

        foreach ($colegios as $c) {
            Colegio::create($c);
        }
    }
}
