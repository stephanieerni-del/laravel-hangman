<?php

namespace Database\Seeders;

use App\Models\LevelPoint;
use Illuminate\Database\Seeder;

class LevelPointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levelPoints = [
            ['level' => 1, 'difficulty' => 'easy', 'x' => 13, 'y' => 39],
            ['level' => 2, 'difficulty' => 'easy', 'x' => 16, 'y' => 46],
            ['level' => 3, 'difficulty' => 'easy', 'x' => 22, 'y' => 48],
            ['level' => 4, 'difficulty' => 'easy', 'x' => 29, 'y' => 50],
            ['level' => 5, 'difficulty' => 'easy', 'x' => 24, 'y' => 58],
            ['level' => 6, 'difficulty' => 'medium', 'x' => 25, 'y' => 71],
            ['level' => 7, 'difficulty' => 'medium', 'x' => 32, 'y' => 75],
            ['level' => 8, 'difficulty' => 'medium', 'x' => 36, 'y' => 84],
            ['level' => 9, 'difficulty' => 'medium', 'x' => 44, 'y' => 95],
            ['level' => 10, 'difficulty' => 'medium', 'x' => 52, 'y' => 90],
            ['level' => 11, 'difficulty' => 'hard', 'x' => 59, 'y' => 85],
            ['level' => 12, 'difficulty' => 'hard', 'x' => 54, 'y' => 78],
            ['level' => 13, 'difficulty' => 'hard', 'x' => 47, 'y' => 72],
            ['level' => 14, 'difficulty' => 'hard', 'x' => 52, 'y' => 63],
            ['level' => 15, 'difficulty' => 'hard', 'x' => 58, 'y' => 61],
            ['level' => 16, 'difficulty' => 'extreme', 'x' => 64, 'y' => 57],
            ['level' => 17, 'difficulty' => 'extreme', 'x' => 68, 'y' => 51],
            ['level' => 18, 'difficulty' => 'extreme', 'x' => 71, 'y' => 41],
            ['level' => 19, 'difficulty' => 'extreme', 'x' => 76, 'y' => 35],
            ['level' => 20, 'difficulty' => 'extreme', 'x' => 71, 'y' => 26],
        ];

        foreach ($levelPoints as $levelPoint) {
            LevelPoint::query()->updateOrCreate(
                ['level' => $levelPoint['level']],
                $levelPoint
            );
        }
    }
}
