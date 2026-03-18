<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Interfaces\ChallengeGenerator;
use App\Models\Game;
use App\Models\LevelPoint;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class GameController extends Controller
{
    public function __construct(private readonly ChallengeGenerator $challengeGenerator)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $owned = $request->query('owned', false);
        if ($owned) {
            $games = $request->user()->created_games()->with('levelPoint')->get();
        } else {
            $games = Game::query()->with('levelPoint')->get();
        }

        $currentUnlockedLevel = $this->resolveCurrentUnlockedLevel($request->user());

        // dd($currentUnlockedLevel);
        $completedGameIds = [];

        $stages = Stage::query()
            ->where('is_skipped', false)
            ->whereHas('player', fn($query) => $query->where('user_id', $request->user()->id))
            ->with([
                'challenge:id,game_id,word',
                'player:id,game_id,user_id',
                'player.game:id,level_point_id',
                'player.game.levelPoint:id,level',
            ])
            ->get();

        foreach ($stages as $stage) {
            $game = $stage->player?->game;
            $level = $game?->levelPoint?->level;

            if (!$stage->challenge) {
                continue;
            }

            if (!$stage->isCompleted()) {
                continue;
            }

            if ($game?->id) {
                $completedGameIds[$game->id] = true;
            }
        }

        $levelPoints = LevelPoint::query()
            ->withExists('game')
            ->with('game:id,level_point_id')
            ->orderBy('level')
            ->get(['id', 'level', 'difficulty', 'x', 'y'])
            ->map(fn(LevelPoint $point) => [
                'level' => $point->level,
                'difficulty' => $point->difficulty,
                'x' => $point->x,
                'y' => $point->y,
                'game_id' => $point->game?->id,
                'has_game' => (bool) $point->game_exists, //based on Games table
                'is_locked' => $point->level > $currentUnlockedLevel,
                'is_current_level' => $currentUnlockedLevel === $point->level,
            ])->all();

        $scoreGuide = $this->buildScoreGuide();

        return view('games.index', compact('games', 'owned', 'levelPoints', 'completedGameIds', 'currentUnlockedLevel', 'scoreGuide'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $remainingSlots = LevelPoint::query()
            ->leftJoin('games', 'games.level_point_id', '=', 'level_points.id')
            ->selectRaw('level_points.difficulty, COUNT(level_points.id) - COUNT(games.id) as remaining')
            ->groupBy('level_points.difficulty')
            ->pluck('remaining', 'difficulty')
            ->all();

        return view('games.create', compact('remainingSlots'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameRequest $request)
    {
        $data = $request->safe()->only('name', 'difficulty');
        $user = $request->user();

        $levelPoint = DB::transaction(function () use ($data) {
            return LevelPoint::query()
                ->where('difficulty', $data['difficulty'])
                ->whereDoesntHave('game')
                ->orderBy('level')
                ->lockForUpdate()
                ->first();
        });

        if (!$levelPoint) {
            return back()
                ->withInput()
                ->withErrors([
                    'difficulty' => "No available slots left for {$data['difficulty']} level.",
                ]);
        }

        $user->created_games()->create([
            'name' => $data['name'],
            'level_point_id' => $levelPoint->id,
        ]);

        return redirect()->route('games.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Game $game)
    {
        if ($this->isLevelLocked($request->user(), $game)) {
            return redirect()->route('games.index')->withErrors([
                'game' => "Level {$game->levelPoint?->level} is locked. Finish lower levels first.",
            ]);
        }

        $isCreator = !is_null($request->user()->created_games->find($game->id));

        // if (! Gate::allows('view', [$game, $isCreator])) {
        //     abort(403);
        // }

        $stage = $game->play($request->user(), boolval($request->input('next', false)));

        $maxLevel = (int) (LevelPoint::query()->max('level') ?? 20);
        $isMaxLevelComplete = ($game->levelPoint?->level ?? 0) >= $maxLevel && $stage->isCompleted();

        $leaderboard = null;
        if ($isMaxLevelComplete) {
            $leaderboard = DB::table('users')
                ->join('players', 'players.user_id', '=', 'users.id')
                ->selectRaw('users.name, SUM(players.score) as total_score')
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_score')
                ->get();
        }

        $disabledKeys = $stage->isOver() ? true : $stage->getGuesses()->all();
        // $scoreGuide = $this->buildScoreGuide($game->levelPoint?->level);

        return view('games.show', compact('game', 'stage', 'disabledKeys', 'isMaxLevelComplete', 'leaderboard', ));
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
        if ($this->isLevelLocked($request->user(), $game)) {
            return redirect()->route('games.index')->withErrors([
                'game' => "Level {$game->levelPoint?->level} is locked. Finish lower levels first.",
            ]);
        }

        $isCreator = !is_null($request->user()->created_games->find($game->id));

        if (!Gate::allows('update', [$game, $isCreator])) {
            abort(403);
        }

        $stage = $game->play($request->user());

        if ($request->input('skip')) {
            $stage->skip();
        } else {
            $guess = $request->safe()->guess;
            $stage->guess($guess);
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

    public function leaderboard(Request $request)
    {
        $leaderboard = DB::table('users')
            ->join('players', 'players.user_id', '=', 'users.id')
            ->selectRaw('users.name, SUM(players.score) as total_score')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_score')
            ->get();

        return view('games.leaderboard', compact('leaderboard'));
    }

    private function resolveCurrentUnlockedLevel(User $user): int
    {
        $highestCompletedLevel = 0;

        $stages = Stage::query()
            ->where('is_skipped', false)
            ->whereHas('player', fn($query) => $query->where('user_id', $user->id))
            ->with([
                'challenge:id,game_id,word',
                'player:id,game_id,user_id',
                'player.game:id,level_point_id',
                'player.game.levelPoint:id,level',
            ])
            ->get();

        foreach ($stages as $stage) {
            $level = $stage->player?->game?->levelPoint?->level;

            if (!$level || !$stage->challenge || $stage->is_skipped) {
                continue;
            }

            $correctGuesses = collect($stage->correct_guesses ?? []);
            $wordCharacters = collect(mb_str_split(mb_strtolower($stage->challenge->word)))->unique();
            $isCompleted = $wordCharacters->every(fn(string $character) => $correctGuesses->contains($character));

            if (!$isCompleted) {
                continue;
            }

            $highestCompletedLevel = max($highestCompletedLevel, $level);
        }

        $maxLevel = (int) (LevelPoint::query()->max('level') ?? 1);

        return min($highestCompletedLevel + 1, $maxLevel);
    }

    private function isLevelLocked(User $user, Game $game): bool
    {
        $game->loadMissing('levelPoint');

        $targetLevel = $game->levelPoint?->level;
        if (!$targetLevel) {
            return false;
        }

        return $targetLevel > $this->resolveCurrentUnlockedLevel($user);
    }

    private function buildScoreGuide(): array
    {
        $bands = [
            [
                'label' => 'Easy',
                'levels' => '1-5',
                'points' => 4,
                'play_again_penalty' => 2,
                'lose_penalty' => 4,
            ],
            [
                'label' => 'Medium',
                'levels' => '6-10',
                'points' => 5,
                'play_again_penalty' => 3,
                'lose_penalty' => 5,
            ],
            [
                'label' => 'Hard',
                'levels' => '11-15',
                'points' => 6,
                'play_again_penalty' => 4,
                'lose_penalty' => 6,
            ],
            [
                'label' => 'Extreme',
                'levels' => '16-20',
                'points' => 7,
                'play_again_penalty' => 5,
                'lose_penalty' => 7,
            ],
        ];

        return [
            'bands' => $bands,
            'perfect_lives_bonus' => 5,
            'starting_lives' => 6,
        ];
    }
}
