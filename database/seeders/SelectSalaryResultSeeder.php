<?php

namespace Database\Seeders;

use App\Models\SelectSalaryResult;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SelectSalaryResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */ protected $select_salary_result = [
        [
            'result_salary' => 'Dưới 10 triệu',
            'min_salary' => 0,
            'max_salary' => 10000000
        ], [
            'result_salary' => '15 - 20 triệu',
            'min_salary' => 15000000,
            'max_salary' => 20000000
        ], [
            'result_salary' => '20 - 25 triệu',
            'min_salary' => 20000000,
            'max_salary' => 25000000
        ], [
            'result_salary' => '25 - 30 triệu',
            'min_salary' => 25000000,
            'max_salary' => 30000000
        ], [
            'result_salary' => 'Trên 30 triệu',
            'min_salary' => 30000000,
            'max_salary' => 0
        ]
    ];
    public function run(): void
    {
        foreach ($this->select_salary_result as $value) {
            SelectSalaryResult::create($value);
        };
    }
}
