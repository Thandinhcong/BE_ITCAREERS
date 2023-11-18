<?php

namespace Database\Seeders;

use App\Models\WorkingForm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkingFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */ protected $working_form = [
        [
            'working_form' => 'OnLine',
            'description' => 'test',
        ], [
            'working_form' => 'Ofline',
            'description' => 'test',
        ], [
            'working_form' => 'Bán thời gian',
            'description' => 'test',
        ]
    ];
    public function run(): void
    {
        foreach ($this->working_form as $value) {
            WorkingForm::create($value);
        }
    }
}
