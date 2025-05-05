<?php

namespace Database\Seeders;

use App\Models\Loket;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LoketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lokets = [
            ['nama' => 'A', 'aktif' => true],
            ['nama' => 'B', 'aktif' => true],
            ['nama' => 'C', 'aktif' => false],
        ];

        foreach ($lokets as $loket) {
            Loket::create($loket);
        }
    }
}
