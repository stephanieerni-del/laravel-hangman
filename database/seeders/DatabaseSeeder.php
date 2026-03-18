<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            User::factory()->raw([
                'name' => 'test',
            ])
        );

        //use 'test' for username ...'password' for password 
        $this->call(LevelPointSeeder::class); //default.. defined x , y ,difficulty and level
        $this->call(GameSeeder::class); // can hide this if you want to create own game
        $this->call(CompleteGameSeeder::class); // proceed to level 20 (HACKER!!!!) , can hide this din kung tapat ka at ayaw ng Hesoyam
    }
}
