<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\LevelPoint;
use App\Models\User;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->first();

        // starting_lives by difficulty: easy=8, medium=7, hard=6, extreme=5
        $games = [
            // Level 1-5: easy
            ['level' => 1,  'name' => 'Briarwood to Blackthorn Castle', 'starting_lives' => 8],
            ['level' => 2,  'name' => 'The Road to Ravenhold',          'starting_lives' => 8],
            ['level' => 3,  'name' => 'The Knight Who Saved Oakridge',  'starting_lives' => 8],
            ['level' => 4,  'name' => 'Secrets of Goldbrook',           'starting_lives' => 8],
            ['level' => 5,  'name' => 'The Siege of Stonebridge',       'starting_lives' => 8],
            // Level 6-10: medium
            ['level' => 6,  'name' => 'The Messenger of Stormwatch',    'starting_lives' => 7],
            ['level' => 7,  'name' => 'Trouble in Oakridge Town',       'starting_lives' => 7],
            ['level' => 8,  'name' => 'The Merchant of Goldbrook',      'starting_lives' => 7],
            ['level' => 9,  'name' => 'Shadows of Willowmere',          'starting_lives' => 7],
            ['level' => 10, 'name' => 'The Bell of Stonebridge',        'starting_lives' => 7],
            // Level 11-15: hard
            ['level' => 11, 'name' => 'The Plague of Black Hollow',     'starting_lives' => 6],
            ['level' => 12, 'name' => 'The Festival of Thorns',         'starting_lives' => 6],
            ['level' => 13, 'name' => 'The Witch of Elderbrook',        'starting_lives' => 6],
            ['level' => 14, 'name' => 'The Bandits of Redvale',         'starting_lives' => 6],
            ['level' => 15, 'name' => 'The Burning of Hollowstead',     'starting_lives' => 6],
            // Level 16-20: extreme
            ['level' => 16, 'name' => 'The Midnight Pact of Crowhaven', 'starting_lives' => 5],
            ['level' => 17, 'name' => 'The Curse of Dreadmoor Keep',    'starting_lives' => 5],
            ['level' => 18, 'name' => "Knight's Oath at Silverwall",    'starting_lives' => 5],
            ['level' => 19, 'name' => 'The Phantom of Ashenfort',       'starting_lives' => 5],
            ['level' => 20, 'name' => 'Fall of Dragonspire Citadel',    'starting_lives' => 5],
        ];

        foreach ($games as $data) {
            $levelPoint = LevelPoint::query()->where('level', $data['level'])->first();

            Game::query()->updateOrCreate(
                ['name' => $data['name']],
                [
                    'starting_lives' => $data['starting_lives'],
                    'level_point_id' => $levelPoint?->id,
                    'user_id'        => $user->id,
                ]
            );
        }
    }
}
