<?php

use App\Models\Challenge;
use App\Models\Game;
use App\Models\LevelPoint;
use App\Models\Player;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createStageForLevel(int $level, string $word = 'cat'): Stage
{
    $difficulty = match (true) {
        $level <= 5 => 'Easy',
        $level <= 10 => 'Medium',
        $level <= 15 => 'Hard',
        default => 'Extreme',
    };

    $user = User::factory()->create();
    $levelPoint = LevelPoint::query()->create([
        'level' => $level,
        'difficulty' => $difficulty,
        'x' => $level,
        'y' => $level,
    ]);

    $game = Game::factory()->create([
        'user_id' => $user->id,
        'level_point_id' => $levelPoint->id,
        'starting_lives' => 6,
    ]);

    $challenge = Challenge::factory()->create([
        'game_id' => $game->id,
        'word' => $word,
        'category' => 'animals',
        'description' => 'test challenge',
    ]);

    $player = Player::query()->create([
        'game_id' => $game->id,
        'user_id' => $user->id,
        'score' => 0,
    ]);

    return Stage::query()->create([
        'player_id' => $player->id,
        'challenge_id' => $challenge->id,
        'guesses' => [],
        'correct_guesses' => [],
        'is_skipped' => false,
    ])->fresh(['player', 'player.game.levelPoint', 'challenge']);
}

it('awards next challenge points based on difficulty and remaining lives', function (int $level, int $expectedScore) {
    $stage = createStageForLevel($level);

    $stage->guess('c');
    $stage->guess('a');
    $stage->guess('t');

    expect($stage->player->fresh()->score)->toBe($expectedScore);
})->with([
            'easy' => [1, 29],
            'medium' => [6, 35],
            'hard' => [11, 41],
            'extreme' => [16, 47],
        ]);

it('subtracts play again penalty by level band', function (int $level, int $startingScore, int $expectedScore) {
    $stage = createStageForLevel($level);
    $stage->player()->update(['score' => $startingScore]);
    $stage = $stage->fresh(['player', 'player.game.levelPoint', 'challenge']);

    $stage->skip();

    expect($stage->player->fresh()->score)->toBe($expectedScore);
})->with([
            'easy' => [1, 10, 8],
            'medium' => [6, 10, 7],
            'hard' => [11, 10, 6],
            'extreme' => [16, 10, 5],
        ]);

it('subtracts lose penalty but never below zero', function (int $level, int $startingScore, int $expectedScore) {
    $stage = createStageForLevel($level, 'abcdef');
    $stage->player()->update(['score' => $startingScore]);
    $stage = $stage->fresh(['player', 'player.game.levelPoint', 'challenge']);

    $stage->guess('x');
    $stage->guess('y');
    $stage->guess('z');
    $stage->guess('q');
    $stage->guess('w');
    $stage->guess('r');

    expect($stage->player->fresh()->score)->toBe($expectedScore);
})->with([
            'easy' => [1, 3, 0],
            'medium' => [6, 8, 3],
            'hard' => [11, 2, 0],
            'extreme' => [16, 20, 13],
        ]);