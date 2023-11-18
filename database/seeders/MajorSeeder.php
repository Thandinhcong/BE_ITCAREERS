<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */ protected $major = [
        [
            'major' => 'Thiết kế web',
            'description' => 'test',
        ], [
            'major' => 'Thiết kế game 3d',
            'description' => 'test',
        ], [
            'major' => 'Thiết game 2d',
            'description' => 'test',
        ]
    ];
    public function run(): void
    {
        foreach ($this->major as $value) {
            Major::create($value);
        };
    }
}
