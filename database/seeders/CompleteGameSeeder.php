<?php

namespace Database\Seeders;

use App\Models\Challenge;
use App\Models\Game;
use App\Models\Player;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompleteGameSeeder extends Seeder
{
    /**
     * A fixed pool of challenges (category => words) used across games.
     * 5 challenges are assigned per game by cycling through the pool.
     */
    private array $challengePool = [
        ['category' => 'animals',              'word' => 'HORSE'],
        ['category' => 'animals',              'word' => 'ELEPHANT'],
        ['category' => 'animals',              'word' => 'GIRAFFE'],
        ['category' => 'animals',              'word' => 'RABBIT'],
        ['category' => 'animals',              'word' => 'ZEBRA'],
        ['category' => 'animals',              'word' => 'MONKEY'],
        ['category' => 'animals',              'word' => 'TIGER'],
        ['category' => 'animals',              'word' => 'DONKEY'],
        ['category' => 'animals',              'word' => 'PANDA'],
        ['category' => 'animals',              'word' => 'SHEEP'],
        ['category' => 'countries',            'word' => 'CANADA'],
        ['category' => 'countries',            'word' => 'BRAZIL'],
        ['category' => 'countries',            'word' => 'GERMANY'],
        ['category' => 'countries',            'word' => 'JAPAN'],
        ['category' => 'countries',            'word' => 'FRANCE'],
        ['category' => 'countries',            'word' => 'INDIA'],
        ['category' => 'countries',            'word' => 'EGYPT'],
        ['category' => 'countries',            'word' => 'SPAIN'],
        ['category' => 'countries',            'word' => 'CHINA'],
        ['category' => 'countries',            'word' => 'ITALY'],
        ['category' => 'programming_languages', 'word' => 'PYTHON'],
        ['category' => 'programming_languages', 'word' => 'SWIFT'],
        ['category' => 'programming_languages', 'word' => 'KOTLIN'],
        ['category' => 'programming_languages', 'word' => 'GOLANG'],
        ['category' => 'programming_languages', 'word' => 'SCALA'],
        ['category' => 'programming_languages', 'word' => 'ELIXIR'],
        ['category' => 'programming_languages', 'word' => 'HASKELL'],
        ['category' => 'programming_languages', 'word' => 'CSHARP'],
        ['category' => 'programming_languages', 'word' => 'JAVASCRIPT'],
        ['category' => 'programming_languages', 'word' => 'OBJECTIVEC'],
    ];

    /** Wrong guesses appended to every stage to simulate real play. */
    private array $wrongGuesses = ['x', 'q', 'z'];

    public function run(): void
    {
        $user = User::query()->first();


        // Game::all()->each(function (Game $game) use ($user) { replace if rekta lv20
        Game::query()
            ->whereHas('levelPoint', fn ($q) => $q->where('level', '<', 20))
            ->get()
            ->each(function (Game $game) use ($user) {
            // ── 1. Challenges ──────────────────────────────────────────────
            $challenges = $this->createChallenges($game);

            // ── 2. Player ──────────────────────────────────────────────────
            $player = Player::query()->firstOrCreate(
                ['game_id' => $game->id, 'user_id' => $user->id],
                ['is_active' => false, 'score' => 0]
            );

            // ── 3. Completed stages ────────────────────────────────────────
            $score = 0;

            foreach ($challenges as $challenge) {
                $correctGuesses = collect(mb_str_split(mb_strtolower($challenge->word)))
                    ->unique()
                    ->values()
                    ->all();

                // All correct letters + a few wrong ones = realistic guess history
                $allGuesses = array_merge($correctGuesses, $this->wrongGuesses);

                Stage::query()->firstOrCreate(
                    ['player_id' => $player->id, 'challenge_id' => $challenge->id],
                    [
                        'guesses'         => $allGuesses,
                        'correct_guesses' => $correctGuesses,
                        'is_skipped'      => false,
                    ]
                );

                $score++;
            }

            // ── 4. Update player score & mark inactive (game over) ─────────
            $player->update(['score' => $score, 'is_active' => false]);
        });
    }

    /**
     * Create 5 challenges for a game using the fixed pool.
     * Uses updateOrCreate so re-running the seeder is safe.
     *
     * @return Challenge[]
     */
    private function createChallenges(Game $game): array
    {
        $challenges   = [];
        $poolSize     = count($this->challengePool);
        $gameIndex    = Game::query()
            ->orderBy('created_at')
            ->pluck('id')
            ->search($game->id);

        for ($i = 0; $i < 5; $i++) {
            $entry = $this->challengePool[($gameIndex * 5 + $i) % $poolSize];

            $challenges[] = Challenge::query()->firstOrCreate(
                ['game_id' => $game->id, 'word' => $entry['word']],
                ['category' => $entry['category']]
            );
        }

        return $challenges;
    }
}
