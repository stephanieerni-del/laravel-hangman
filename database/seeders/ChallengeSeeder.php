<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Challenge::query()->delete();
        $game = \App\Models\Game::first();
        if (!$game) {
            $this->command->warn('No game found. Run GameSeeder first.');
            return;
        }

        $levels = [
            [1, 5, 4, 'easy'],
            [6, 10, 5, 'medium'],
            [11, 15, 6, 'hard'],
            [16, 20, 7, 'extreme'],
        ];

        $categories = ['animals', 'countries', 'programming_languages'];
        $words = [
            4 => ['LION', 'BEAR', 'FROG', 'WOLF', 'GOAT', 'PERU', 'MALI', 'OMAN', 'LAOS', 'CUBA', 'JAVA', 'RUBY', 'PERL', 'LISP', 'BASH'],
            5 => ['TIGER', 'HORSE', 'SHEEP', 'ZEBRA', 'SPAIN', 'CHILE', 'INDIA', 'ITALY', 'JAPAN', 'EGYPT', 'SWIFT', 'SCALA', 'ELIXI', 'GOANG', 'PYTHON'],
            6 => ['MONKEY', 'DONKEY', 'GIRAFF', 'PANDAA', 'BRAZIL', 'FRANCE', 'GERMANY', 'CANADA', 'HASKEL', 'OBJECT', 'KOTLIN', 'CSHARP', 'GOLANG', 'PYTHON', 'SWIFTT'],
            7 => ['PANTHER', 'GIRAFFE', 'ELEPHAN', 'JAVASCR', 'GERMANY', 'FINLAND', 'DENMARK', 'HUNGARY', 'VENEZUE', 'PORTUGA', 'KOTLINN', 'SCALAAA', 'PYTHONS', 'SWIFTER', 'OBJECTS'],
        ];

        $levelIndex = 0;
        foreach ($levels as [$start, $end, $length, $difficulty]) {
            for ($level = $start; $level <= $end; $level++) {
                $category = $categories[array_rand($categories)];
                $wordList = $words[$length];
                $word = $wordList[$levelIndex % count($wordList)];
                \App\Models\Challenge::create([
                    'game_id' => $game->id,
                    'level' => $level,
                    'category' => $category,
                    'word' => $word,
                ]);
                $levelIndex++;
            }
        }
    }
}
