<?php

namespace Database\Seeders;

use App\Models\AcademicLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademicLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected $academic_level = [
        [
            'academic_level' => 'Đại học',
        ], [
            'academic_level' => 'Cao đẳng',
        ], [
            'academic_level' => 'Trên đại học',
        ], [
            'academic_level' => 'Khác',
        ]
    ];
    public function run(): void
    {
        foreach ($this->academic_level as $value) {
            AcademicLevel::create($value);
        };
    }
}
