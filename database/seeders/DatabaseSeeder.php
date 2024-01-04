<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\JobPost;
use App\Models\JobPostType;
use App\Models\ManagementWeb;
use Illuminate\Database\Seeder;
use App\Models\Packages;

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
            'title' => 'Senior ReactJS',
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
            "desc" => "anh thản cho em làm với",
            "type_job_post_id" => 1
        ]);
        JobPostType::create([
            'name' => 'Bài đăng thường',
            'desc' => '<p>-&nbsp;Tiêu đề tin: chữ in thường, có màu đen</p><p>-&nbsp;Hiển thị phía dưới cùng kết quả tìm kiếm&nbsp;và dưới tin vip</p><p>-&nbsp;Lựa chọn thời gian đăng tin tối thiểu 10 ngày</p><p>-&nbsp;Tin được hệ thống duyệt sau 2 tiếng đăng tin</p><p>-&nbsp;Mức giá từ 1.000đ/ngày</p>',
            'salary' => 1000,
        ]);
        JobPostType::create([
            'name' => 'Bài đăng Vip',
            'desc' => '<p>-&nbsp;Tiêu đề tin: chữ in thường, có màu cam nổi bật</p><p>-&nbsp;Hiển thị vị trí tìm kiếm trên tin thường</p><p>-&nbsp;Lựa chọn thời gian đăng tin tối thiểu 10 ngày</p><p>-&nbsp;Nhãn nổi bật (nếu có) có background màu đỏ nổi bật thu hút sự chú ý.</p><p>-&nbsp;Tin được hệ thống duyệt nhanh chóng, tự động sau 30 phút đăng tin</p><p>-&nbsp;Mức giá từ 2.000đ/ngày</p>',
            'salary' => 2000,
        ]);
        ManagementWeb::create([
            'logo' => 'https://res.cloudinary.com/dxzlnojyv/image/upload/v1700241139/essxedc0cwpivyfztsog.png',
            'banner' => 150,
            'name_web' => 'BEWORK',
            'company_name' => 'Công ty cổ phần bework',
            'address' => 'Hà nội',
            'email' => 'bework@gmail.com',
            'phone' => '09842751073',
            'sdt_lienhe' => '09842751073',
        ]);
        Packages::create(
            [
                'title' => 'Gói nạp công ty',
                'coin' => 100000,
                'price' => 100000,
                'reduced_price' => 1,
                'status' => 1,
                'type_account' => 0,
            ]
        );
        Packages::create(
            [
                'title' => 'Gói nạp ứng viên',
                'coin' => 100000,
                'price' => 100000,
                'reduced_price' => 1,
                'status' => 1,
                'type_account' => 1,
            ]
        );
    }
}
