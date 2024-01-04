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
        ], [
            'major' => 'Phát triển Phần mềm',
            'description' => 'test',
        ], [
            'major' => 'Quản lý Dự án IT',
            'description' => 'test',
        ], [
            'major' => 'Phân tích Dữ liệu và Khoa học Dữ liệu',
            'description' => 'test',
        ], [
            'major' => 'Phát triển Ứng dụng di động',
            'description' => 'test',
        ], [
            'major' => 'An ninh Thông tin',
            'description' => 'test',
        ], [
            'major' => 'Quản lý Dữ liệu và Cơ sở dữ liệu',
            'description' => 'test',
        ], [
            'major' => 'Phần mềm Kế toán',
            'description' => 'test',
        ], [
            'major' => 'Thiết kế Trò chơi',
            'description' => 'test',
        ], [
            'major' => 'Trí tuệ nhân tạo',
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
