<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AcademicLevel;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(1)->create();
<<<<<<< HEAD

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
=======
        
>>>>>>> 9f53be4c1bb035eb6d005ab511fee6109337eda0
        $this->call([
            CandidatesSeeder::class,
            CompaniesSeeder::class
        ]);
        // \App\Models\User::factory(1)->create();
        AcademicLevel::factory()->create(
            [
                'academic_level' => 'Đại học',
                'description' => 'test',
            ],
        );AcademicLevel::factory()->create(
            [
                'academic_level' => 'Cao đẳng',
                'description' => 'test',
            ],
        );AcademicLevel::factory()->create(
            [
                'academic_level' => 'Trên đại học',
                'description' => 'test',
            ],
        );
       
    }
}