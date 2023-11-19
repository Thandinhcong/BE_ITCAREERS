<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\JobPost;
use App\Models\JobPostType;
use App\Models\ManagementWeb;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::factory(1)->create();
        \App\Models\Candidate::factory(1)->create();
        \App\Models\Company::factory(1)->create();
        $this->call([
            ProvinceSeeder::class,
            AcademicLevelSeeder::class,
            DistrictSeeder::class,
            ExperienceSeeder::class,
            JobPositionSeeder::class,
            LevelSeeder::class,
            MajorSeeder::class,
            SelectSalaryResultSeeder::class,
            WorkingFormSeeder::class,

        ]);
        JobPost::create([
            'title' => 'Bài đăng 1',
            'job_position_id' => 1,
            'exp_id' => 2,
            'quantity' => 4,
            'requirement' => 'Yêu cầu 1',
            'interest' => 'Đãi ngộ1',
            'desc' => 'Mô tả công việc',
            'min_salary' => '20000',
            'max_salary' => '30000',
            'gender' => 0,
            'company_id' => 1,
            'working_form_id' => 3,
            'academic_level_id' => 2,
            'major_id' => 2,
            'area_id' => 2,
            'start_date' => '2023-10-04',
            'end_date' => '2023-10-010',
        ]);
        JobPostType::create([
            'name' => 'Bài đăng 1',
            'salary' => 100,
        ]);JobPostType::create([
            'name' => 'Bài đăng 2',
            'salary' => 150,
        ]);
        ManagementWeb::create([
            'logo' => 'https://res.cloudinary.com/dxzlnojyv/image/upload/v1700241139/essxedc0cwpivyfztsog.png',
            'banner' => 150,
            'name_web' => 'Beework',
            'company_name' => 'Công ty cổ phần bework',
            'address' => 'Hà nội',
            'email' => 'beework@gmail.com',
            'phone' => '09842751073',
            'sdt_lienhe' => '09842751073',
        ]);
    }
}
