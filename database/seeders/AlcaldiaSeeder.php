<?php

namespace Database\Seeders;

use App\Models\Alcaldia;
use Illuminate\Database\Seeder;

class AlcaldiaSeeder extends Seeder
{
    public function run(): void
    {
        $alcaldias = [
            'Álvaro Obregón', 'Azcapotzalco', 'Benito Juárez', 'Coyoacán',
            'Cuajimalpa de Morelos', 'Cuauhtémoc', 'Gustavo A. Madero', 'Iztacalco',
            'Iztapalapa', 'La Magdalena Contreras', 'Miguel Hidalgo', 'Milpa Alta',
            'Tláhuac', 'Tlalpan', 'Venustiano Carranza', 'Xochimilco',
        ];

        foreach ($alcaldias as $nombre) {
            Alcaldia::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
