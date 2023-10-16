<?php

namespace Database\Seeders;

use App\Models\Candidates;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Candidates::factory()->count(1)->create();
    }
}
