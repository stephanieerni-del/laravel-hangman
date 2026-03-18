<?php

namespace App\Models;

use Database\Factories\StageFactory;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Stage extends Pivot
{
    /** @use HasFactory<StageFactory> */
    use HasFactory;

    protected $table = 'stages';

    public $incrementing = true;

    protected $fillable = [
        'player_id',
        'challenge_id',
        'guesses',
        'correct_guesses',
        'is_skipped',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function lives(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->challenge->lives
            - $this->getGuesses()->count()
            + collect($this->correct_guesses ?? [])->count()
        );
    }

    public function guess(string $guess)
    {
        DB::transaction(function () use ($guess) {
            if ($this->isOver()) {
                return;
            }

            $normalizedGuess = mb_strtolower(trim($guess));
            $guesses = $this->getGuesses();
            $correctGuesses = collect($this->correct_guesses ?? []);

            $guesses->push($normalizedGuess);
            if ($this->challenge->contains($normalizedGuess)) {
                $correctGuesses->push($normalizedGuess);
            }

            $this->guesses = $guesses;
            $this->correct_guesses = $correctGuesses;
            $this->save();

            if ($this->isCompleted()) {
                $this->applyScoreDelta($this->winScore());

                return;
            }

            if ($this->isFailed()) {
                $this->applyScoreDelta(-$this->losePenalty());
            }
        });

    }

    public function skip()
    {
        if ($this->isOver()) {
            return;
        }

        DB::transaction(function () {
            $this->update(['is_skipped' => true]);
            $this->applyScoreDelta(-$this->playAgainPenalty());
        });
    }

    public function isCompleted(): bool
    {
        $correctGuesses = collect($this->correct_guesses ?? []);
        $wordCharacters = collect(mb_str_split(mb_strtolower($this->challenge->word)))->unique();

        return $wordCharacters->every(fn(string $character) => $correctGuesses->contains($character));
    }

    public function isFailed(): bool
    {
        return $this->lives <= 0 || $this->is_skipped;
    }

    public function isCritical(): bool
    {
        return $this->lives < $this->challenge->lives / 2;
    }

    public function isOver(): bool
    {
        return $this->isCompleted() || $this->isFailed();
    }

    public function getGuesses(): Collection
    {
        return collect($this->guesses ?? []);
    }

    public function isAlreadyUsed(string $guess): bool
    {
        return $this->getGuesses()->contains($guess);
    }

    public function __toString(): string
    {
        $correctGuesses = collect($this->correct_guesses ?? []);

        return collect(mb_str_split($this->challenge->word))
            ->map(fn(string $char) => $correctGuesses->contains(mb_strtolower($char)) ? mb_strtoupper($char) : '_')
            ->implode(' ');
    }

    public function next(): Stage
    {
        if ($this->isOver()) {
            return Stage::create([
                'player_id' => $this->player->id,
                'challenge_id' => $this->challenge->next()->id,
                'guesses' => [],
                'correct_guesses' => [],
            ]);
        }

        return $this;
    }

    private function applyScoreDelta(int $delta): void
    {
        if ($delta === 0) {
            return;
        }

        $player = Player::query()->lockForUpdate()->find($this->player_id);

        if (!$player) {
            return;
        }

        Player::query()
            ->whereKey($player->getKey())
            ->update([
                'score' => max(0, $player->score + $delta),
            ]);
    }

    private function difficultyPoints(): int
    {
        $level = $this->player?->game?->levelPoint?->level;
        if ($level) {
            return match (true) {
                $level <= 5 => 4,
                $level <= 10 => 5,
                $level <= 15 => 6,
                default => 7,
            };
        }

        $difficulty = mb_strtolower((string) ($this->player?->game?->levelPoint?->difficulty ?? ''));

        return match ($difficulty) {
            'easy' => 4,
            'medium' => 5,
            'hard' => 6,
            'extreme' => 7,
            default => 4,
        };
    }

    private function playAgainPenalty(): int
    {
        return max(0, $this->difficultyPoints() - 2);
    }

    private function losePenalty(): int
    {
        return $this->difficultyPoints();
    }

    private function winScore(): int
    {
        $remainingLives = max(0, $this->lives);
        $baseScore = $this->difficultyPoints() * $remainingLives;
        $perfectLifeBonus = $remainingLives === (int) $this->challenge->lives ? 5 : 0;

        return $baseScore + $perfectLifeBonus;
    }

    protected function casts(): array
    {
        return [
            'guesses' => AsCollection::class,
            'correct_guesses' => AsCollection::class,
            'is_skipped' => 'boolean',
        ];
    }
}
