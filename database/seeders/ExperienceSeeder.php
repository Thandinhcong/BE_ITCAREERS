<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */ protected $experience = [
        [
            'experience' => 'Chưa có kinh nghiệm',
            'description' => 'test',
        ], [
            'experience' => 'Dưới 1 năm kinh nghiệm',
            'description' => 'test',
        ], [
            'experience' => '1 năm kinh nghiệm',
            'description' => 'test',
        ], [
            'experience' => '2 năm kinh nghiệm',
            'description' => 'test',
        ], [
            'experience' => '3 năm kinh nghiệm',
            'description' => 'test',
        ], [
            'experience' => '4 năm kinh nghiệm',
            'description' => 'test',
        ], [
            'experience' => '5 năm kinh nghiệm',
            'description' => 'test',
        ], [
            'experience' => 'Trên 5 năm kinh nghiệm',
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
