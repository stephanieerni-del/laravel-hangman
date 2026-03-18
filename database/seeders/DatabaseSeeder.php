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

        $this->call(LevelPointSeeder::class);
        $this->call(GameSeeder::class);
        $this->call(CompleteGameSeeder::class); // proceed to level 19 or 20
    }
}
