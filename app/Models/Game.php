<?php

namespace App\Models;

use App\Interfaces\ChallengeGenerator;
use Database\Factories\GameFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Game extends Model
{
    /** @use HasFactory<GameFactory> */
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $fillable = ['name', 'starting_lives'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class);
    }

    public function gamers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'players',
            'game_id',
            'user_id'
        )->withPivot(['id', 'is_active', 'score'])
            ->withTimestamps()
            ->using(Player::class)
            ->as('player');
    }

    public function getChallenge(?Challenge $after = null): Challenge
    {
        return DB::transaction(function () use ($after) {
            // Dependency Injection
            $challengeGenerator = app(ChallengeGenerator::class);

            $next = $this->challenges()
                ->when(
                    $after,
                    fn ($query) => $query->where('created_at', '>', $after->created_at)
                )
                ->orderBy('created_at')
                ->first();

            if (! $next) {
                $newChallenge = $challengeGenerator->generate();
                // Use previous challenge's level if available, otherwise default to 1
                $level = $after ? $after->level : 1;
                $next = $this->challenges()->create([
                    'level' => $level,
                    'category' => $newChallenge->category,
                    'word' => $newChallenge->word,
                ]);
            }

            return $next;
        });
    }

    public function play(User $user, bool $next = false): Stage
    {
        return DB::transaction(function () use ($user, $next) {
            $player = $this->gamers()->find($user->id)?->player;

            if ($player) {
                return $next ? $player->stage->next() : $player->stage;
            }

            $player = Player::create([
                'game_id' => $this->id,
                'user_id' => $user->id,
            ]);

            return Stage::create([
                'player_id' => $player->id,
                'challenge_id' => $this->getChallenge()->id,
                'guesses' => [],
                'correct_guesses' => [],
            ]);
        });
    }

    public function getTopGamers(int $n = 5): Collection
    {
        return $this->gamers()
            ->where('score', '>', 0)
            ->orderByPivotDesc('score')
            ->limit($n)
            ->get();
    }
}
