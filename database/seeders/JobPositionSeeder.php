<?php

namespace Database\Seeders;

use App\Models\JobPosition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected $job_position = [
        [
            'job_position' => 'Giám đốc và Cấp cao hơn',
            'description' => 'test',
        ],
        [
            'job_position' => 'Trưởng phòng',
            'description' => 'test',
        ],
        [
            'job_position' => 'Phó phòng',
            'description' => 'test',
        ],
        [
            'job_position' => 'Nhân viên',
            'description' => 'test',
        ],
        [
            'job_position' => 'Thực tập sinh/Sinh Viên',
            'description' => 'test',
        ],
        [
            'job_position' => 'Mới tốt nghiệp',
            'description' => 'test',
        ]
    ];
    public function run(): void
    {
        foreach ($this->job_position as $value) {
            JobPosition::create($value);
        };
    }
}
