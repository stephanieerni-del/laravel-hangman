<?php

namespace Database\Factories;

use App\Classes\LocalChallengeGenerator;
use App\Models\Challenge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Challenge>
 */
class ChallengeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $challenge = app(LocalChallengeGenerator::class)->generate();

        return [
            'category' => $challenge->category,
            'word' => $challenge->word,
            'description' => $challenge->description,
        ];
    }
}
