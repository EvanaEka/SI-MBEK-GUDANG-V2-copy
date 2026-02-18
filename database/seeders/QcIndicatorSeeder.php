<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QcIndicator;

class QcIndicatorSeeder extends Seeder
{
    public function run(): void
    {
        $indicators = [
            // KRITIS
            ['name' => 'Bau tidak normal', 'is_critical' => true],
            ['name' => 'Terdapat jamur', 'is_critical' => true],
            ['name' => 'Kontaminasi benda asing', 'is_critical' => true],

            // NON KRITIS
            ['name' => 'Warna sesuai standar', 'is_critical' => false],
            ['name' => 'Tekstur sesuai standar', 'is_critical' => false],
            ['name' => 'Kadar air sesuai', 'is_critical' => false],
        ];

        foreach ($indicators as $item) {
            QcIndicator::create([
                'name' => $item['name'],
                'is_critical' => $item['is_critical'],
                'is_active' => true,
            ]);
        }
    }
}
