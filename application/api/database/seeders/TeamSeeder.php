<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = collect([
            'Chelsea',
            'Arsenal',
            'Liverpool',
            'Manchester United'
        ]);

        $teams->map(fn (string $team) => Team::query()->firstOrCreate(['name' => $team]));
    }
}
