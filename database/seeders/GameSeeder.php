<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Game::query()->delete();
        $user = \App\Models\User::first();
        if (!$user) {
            $user = \App\Models\User::factory()->create([
                'name' => 'Game Master',
                'email' => 'master@example.com',
            ]);
        }
        \App\Models\Game::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'name' => 'Main Game',
            'starting_lives' => 6,
        ]);
    }
}
