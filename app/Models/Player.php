<?php

namespace App\Models;

use Database\Factories\PlayerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Player extends Pivot
{   /** @use HasFactory<PlayerFactory> */
    use HasFactory;

    protected $table = 'players';

    public $incrementing = true;

    protected $fillable = ['game_id', 'user_id', 'level'];

   /**
     * Get the current level of the player based on the latest stage.
     *
     * @return int|null
     */
    public function currentLevel(): ?int
    {
        $latestStage = $this->stages()->latest('created_at')->first();
        if ($latestStage && $latestStage->challenge) {
            return $latestStage->challenge->level;
        }
        return null;
    }

      public function challenges(): BelongsToMany
    {
        return $this->belongsToMany(
            Challenge::class,
            'stages',
            'player_id',
            'challenge_id'
        )->withPivot(['id', 'guesses', 'correct_guesses', 'is_skipped'])
            ->withTimestamps()
            ->using(Stage::class)
            ->as('stage');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class, 'player_id');
    }

    public function stage(): HasOne
    {
        return $this->stages()->one()->latestOfMany('created_at');
    }
}
