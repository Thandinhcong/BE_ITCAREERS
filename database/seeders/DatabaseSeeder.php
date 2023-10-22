<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AcademicLevel;
use App\Models\Experience;
use App\Models\JobPosition;
use App\Models\JobPost;
use App\Models\Level;
use App\Models\Major;
use App\Models\WorkingForm;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(1)->create();
        \App\Models\Candidate::factory(1)->create();
        \App\Models\Company::factory(1)->create();

        AcademicLevel::create(
            [
                'academic_level' => 'Đại học',
            ],
        );
        AcademicLevel::create(
            [
                'academic_level' => 'Cao đẳng',
            ],
        );
        AcademicLevel::create(
            [
                'academic_level' => 'Trên đại học',
                'description' => 'test',
            ]
        );
        JobPosition::create([
            'job_position' => 'trưởng phòng',
            'description' => 'test',
        ]);
        JobPosition::create([
            'job_position' => 'Phó phòng',
            'description' => 'test',
        ]);
        JobPosition::create([
            'job_position' => 'Thực tập sinh',
            'description' => 'test',
        ]);
        Experience::create([
            'experience' => '1 năm',
            'description' => 'test',
        ]);
        Experience::create([
            'experience' => '2 năm',
            'description' => 'test',
        ]);
        Experience::create([
            'experience' => '3 năm',
            'description' => 'test',
        ]);
        Experience::create([
            'experience' => '5 năm',
            'description' => 'test',
        ]);
        Level::create([
            'level' => 'Fresher',
            'description' => 'test',
        ]);
        Level::create([
            'level' => 'Senior',
            'description' => 'test',
        ]);
        Level::create([
            'level' => 'Junior',
            'description' => 'test',
        ]);
        Major::create([
            'major' => 'Thiết kế web',
            'description' => 'test',
        ]);
        Major::create([
            'major' => 'Thiết kế game 3d',
            'description' => 'test',
        ]);
        Major::create([
            'major' => 'Thiết game 2d',
            'description' => 'test',
        ]);
        WorkingForm::create([
            'working_form' => 'OnLine',
            'description' => 'test',
        ]);
        WorkingForm::create([
            'working_form' => 'Ofline',
            'description' => 'test',
        ]);
        WorkingForm::create([
            'working_form' => 'Bán thời gian',
            'description' => 'test',
        ]);
        JobPost::create([
            'title' => 'Bài đăng 1',
            'job_position_id' => 1,
            'exp_id' => 2,
            'quantity' => 4,
            'require' => 'Yêu cầu 1',
            'interest' => 'Đãi ngộ1',
            'min_salary' => '20000',
            'max_salary' => '30000',
            'salary_type' => 12,
            'level_id' => 3,
            'company_id' => 1,
            'working_form_id' => 3,
            'academic_level_id' => 2,
            'major_id' => 2,
            'area_id' => 2,
            'start_date' => '2023-10-04',
            'end_date' => '2023-10-010',
        ]);
    }
}
