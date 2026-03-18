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
        ['category' => 'animals', 'word' => 'HORSE'],
        ['category' => 'animals', 'word' => 'ELEPHANT'],
        ['category' => 'animals', 'word' => 'GIRAFFE'],
        ['category' => 'animals', 'word' => 'RABBIT'],
        ['category' => 'animals', 'word' => 'ZEBRA'],
        ['category' => 'animals', 'word' => 'MONKEY'],
        ['category' => 'animals', 'word' => 'TIGER'],
        ['category' => 'animals', 'word' => 'DONKEY'],
        ['category' => 'animals', 'word' => 'PANDA'],
        ['category' => 'animals', 'word' => 'SHEEP'],
        ['category' => 'countries', 'word' => 'CANADA'],
        ['category' => 'countries', 'word' => 'BRAZIL'],
        ['category' => 'countries', 'word' => 'GERMANY'],
        ['category' => 'countries', 'word' => 'JAPAN'],
        ['category' => 'countries', 'word' => 'FRANCE'],
        ['category' => 'countries', 'word' => 'INDIA'],
        ['category' => 'countries', 'word' => 'EGYPT'],
        ['category' => 'countries', 'word' => 'SPAIN'],
        ['category' => 'countries', 'word' => 'CHINA'],
        ['category' => 'countries', 'word' => 'ITALY'],
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

    /**
     * Human-readable descriptions for fixed challenge words.
     * Keyed by word for easy lookup while seeding.
     */
    private array $wordDescriptions = [
        'HORSE' => 'A strong domesticated animal often used for riding and farm work.',
        'SHEEP' => 'A woolly farm animal known for producing wool and living in flocks.',
        'RABBIT' => 'A small mammal with long ears that is known for hopping quickly.',
        'DONKEY' => 'A hardy working animal often used to carry loads in rural areas.',
        'GIRAFFE' => 'The tallest land animal, famous for its very long neck.',
        'TIGER' => 'A large wild cat recognized by its orange coat and black stripes.',
        'PANDA' => 'A black-and-white bear native to China that mostly eats bamboo.',
        'ZEBRA' => 'An African animal related to horses, known for its black-and-white stripes.',
        'ELEPHANT' => 'The largest land mammal, with a trunk and large ears.',
        'MONKEY' => 'A clever primate often seen climbing trees and living in groups.',
        'CANADA' => 'A North American country known for vast forests and cold winters.',
        'JAPAN' => 'An island nation in East Asia known for technology and rich traditions.',
        'BRAZIL' => 'The largest country in South America, home to the Amazon rainforest.',
        'FRANCE' => 'A European country famous for art, cuisine, and the Eiffel Tower.',
        'GERMANY' => 'A central European country known for engineering and historic cities.',
        'INDIA' => 'A large South Asian country known for diverse cultures and languages.',
        'CHINA' => 'A populous East Asian country with one of the world oldest civilizations.',
        'EGYPT' => 'A North African country known for the Nile River and ancient pyramids.',
        'SPAIN' => 'A southwestern European country known for flamenco and Mediterranean coasts.',
        'ITALY' => 'A southern European country known for Roman history and famous cuisine.',
        'JAVASCRIPT' => 'A popular scripting language used to build interactive web applications.',
        'PYTHON' => 'A beginner-friendly language used in web development, automation, and AI.',
        'SWIFT' => 'A programming language created by Apple for iOS and macOS apps.',
        'KOTLIN' => 'A modern language commonly used for Android app development.',
        'CSHARP' => 'A Microsoft language used for enterprise software and game development.',
        'GOLANG' => 'A compiled language by Google known for simplicity and concurrency.',
        'SCALA' => 'A JVM language blending object-oriented and functional programming.',
        'ELIXIR' => 'A functional language built on Erlang for scalable systems.',
        'HASKELL' => 'A pure functional language known for strong typing and immutability.',
        'OBJECTIVEC' => 'An older Apple language used before Swift for iOS and macOS apps.',
    ];

    public function run(): void
    {
        $user = User::query()->first();


        // Game::all()->each(function (Game $game) use ($user) { replace if rekta lv20
        Game::query()
            ->whereHas('levelPoint', fn($q) => $q->where('level', '<', 20))
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
                            'guesses' => $allGuesses,
                            'correct_guesses' => $correctGuesses,
                            'is_skipped' => false,
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
        $challenges = [];
        $poolSize = count($this->challengePool);
        $gameIndex = Game::query()
            ->orderBy('created_at')
            ->pluck('id')
            ->search($game->id);

        for ($i = 0; $i < 5; $i++) {
            $entry = $this->challengePool[($gameIndex * 5 + $i) % $poolSize];
            $description = $this->wordDescriptions[$entry['word']]
                ?? 'A random ' . str_replace('_', ' ', $entry['category']) . ' word.';

            $challenges[] = Challenge::query()->updateOrCreate(
                ['game_id' => $game->id, 'word' => $entry['word']],
                [
                    'category' => $entry['category'],
                    'description' => $description,
                ]
            );
        }

        return $challenges;
    }
}
