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
            'working_form' => 'Remote',
            'description' => 'test',
        ], [
            'working_form' => 'Online',
            'description' => 'test',
        ], [
            'working_form' => 'Part time',
            'description' => 'test',
        ], [
            'working_form' => 'Flexible hours',
            'description' => 'Mô hình làm việc theo giờ linh hoạt cho phép nhân viên tự quản lý thời gian làm việc của họ trong một phạm vi nhất định',
        ], [
            'working_form' => 'Remote Collaboration',
            'description' => 'Sử dụng công nghệ để hợp tác từ xa, bao gồm việc sử dụng các công cụ như video họp, chat, và các nền tảng hợp tác trực tuyến.',
        ], [
            'working_form' => 'Project based',
            'description' => 'Công việc IT thường được tổ chức thành các dự án cụ thể. Nhân viên có thể làm việc chủ yếu trên một hoặc vài dự án cụ thể.',
        ]
    ];
    public function run(): void
    {
        foreach ($this->working_form as $value) {
            WorkingForm::create($value);
        }
    }
}
