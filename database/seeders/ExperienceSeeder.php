<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */protected $experience = [
        [
            'experience' => '1 năm',
            'description' => 'test',
        ], [
            'experience' => '2 năm',
            'description' => 'test',
        ], [
            'experience' => '3 năm',
            'description' => 'test',
        ], [
            'experience' => '5 năm',
            'description' => 'test',
        ]
    ];
    public function run(): void
    {
        foreach ($this->experience as $value) {
            Experience::create($value);
        };
    }
}
