<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Interfaces\ChallengeGenerator;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GameController extends Controller
{
    public function __construct(private readonly ChallengeGenerator $challengeGenerator) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $owned = $request->query('owned', false);
        if ($owned) {
            $games = $request->user()->created_games;
        } else {
            $games = Game::get();
        }

        return view('games.index', compact('games', 'owned'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('games.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameRequest $request)
    {
        $data = $request->safe()->only('name');
        $user = $request->user();

        $user->created_games()
            ->create(['name' => $data['name']]);

        return redirect()->route('games.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Game $game)
    {
        $isCreator = ! is_null($request->user()->created_games->find($game->id));

        if (! Gate::allows('view', [$game, $isCreator])) {
            abort(403);
        }

        $user = $request->user();
        // Always fetch Player model directly
        $player = \App\Models\Player::where('game_id', $game->id)->where('user_id', $user->id)->first();
        $nextLevel = $request->input('next', false);

        if ($nextLevel && $player) {
            // Increment player level
            $player->level = $player->level + 1;
            $player->save();

            // For levels 1-5, use 4-letter words; else, use player's level as length
            if ($player->level >= 1 && $player->level <= 5) {
                $length = 4;
            } elseif ($player->level >= 6 && $player->level <= 10) {
                $length = 5;
            } elseif ($player->level >= 11 && $player->level <= 15) {
                $length = 6;
            } elseif ($player->level >= 16 && $player->level <= 20) {
                $length = rand(7, 8);
            } else {
                $length = 8;
            }
            $generator = app(\App\Interfaces\ChallengeGenerator::class);
            $randomWordObj = $generator->generate($length);
            $newWord = $randomWordObj->word;
            $newChallenge = $game->challenges()->create([
                'level' => $player->level,
                'category' => $randomWordObj->category ?? 'random',
                'word' => $newWord,
            ]);
            $player->stages()->create([
                'challenge_id' => $newChallenge->id,
                'guesses' => [],
                'correct_guesses' => [],
                'is_skipped' => false,
            ]);
        }

        $stage = $game->play($user);
        $disabledKeys = $stage->isOver() ? true : $stage->getGuesses()->all();

        return view('games.show', compact('game', 'stage', 'disabledKeys'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        // Not Implemented
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameRequest $request, Game $game)
    {
        $isCreator = ! is_null($request->user()->created_games->find($game->id));

        if (! Gate::allows('update', [$game, $isCreator])) {
            abort(403);
        }

        $stage = $game->play($request->user());

        if ($request->input('skip')) {
            // Persist current lives before skipping
            $currentLives = $stage->lives;
            $stage->skip();
            // Fetch Player model directly
            $user = $request->user();
            $player = \App\Models\Player::where('game_id', $game->id)->where('user_id', $user->id)->first();
            if ($player) {
                // Cap level at 20
                if ($player->level > 20) {
                    $player->level = 20;
                    $player->save();
                }
                if ($player->level >= 1 && $player->level <= 5) {
                    $length = 4;
                } elseif ($player->level >= 6 && $player->level <= 10) {
                    $length = 5;
                } elseif ($player->level >= 11 && $player->level <= 15) {
                    $length = 6;
                } elseif ($player->level >= 16 && $player->level <= 20) {
                    $length = rand(7, 8);
                } else {
                    $length = 8;
                }
                $generator = app(\App\Interfaces\ChallengeGenerator::class);
                $randomWordObj = $generator->generate($length);
                $newWord = $randomWordObj->word;
                $newChallenge = $game->challenges()->create([
                    'level' => $player->level,
                    'category' => $randomWordObj->category ?? 'random',
                    'word' => $newWord,
                ]);
                $player->stages()->create([
                    'challenge_id' => $newChallenge->id,
                    'guesses' => [],
                    'correct_guesses' => [],
                    'is_skipped' => false,
                    'remaining_lives' => $currentLives,
                ]);
            }
        } else {
            $guess = $request->safe()->guess;
            if ($guess !== null) {
                $stage->guess($guess);
            }
        }

        return redirect()->route('games.show', compact('game'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        //
    }

        /**
     * Retry the current level with a new word of the same difficulty.
     */
    public function retry(Request $request, Game $game)
    {
        $user = $request->user();
        $player = $game->gamers()->where('user_id', $user->id)->first()?->player;
        if (!$player) {
            abort(404, 'Player not found');
        }

        $latestStage = $player->stages()->latest('created_at')->first();
        if (!$latestStage || !$latestStage->challenge) {
            abort(404, 'No stage or challenge found');
        }

        $level = $latestStage->challenge->level;

        // Mark the previous stage as skipped (for history)
        $latestStage->update(['is_skipped' => true]);

        // Word length = current level
        $length = $level;
        $generator = app(\App\Interfaces\ChallengeGenerator::class);
        $randomWordObj = $generator->generate($length);
        $newWord = $randomWordObj->word;
        // Create a new challenge for this level, category 'random'
        $newChallenge = $game->challenges()->create([
            'level' => $level,
            'category' => $randomWordObj->category ?? 'random',
            'word' => $newWord,
        ]);

        // Create a new stage for the player with the new challenge
        $player->stages()->create([
            'challenge_id' => $newChallenge->id,
            'guesses' => [],
            'correct_guesses' => [],
            'is_skipped' => false,
        ]);

        return redirect()->route('games.show', ['game' => $game->id]);
    }
}
