<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected  $province = [
        [
            "province" => " Hà Nội",
        ],
        [
            "province" => " Hà Giang",
        ],
        [
            "province" => " Cao Bằng"
        ],
        [
            "province" => " Bắc Kạn"
        ],
        [
            "province" => " Tuyên Quang"
        ],
        [
            "province" => " Lào Cai"
        ],
        [
            "province" => " Điện Biên"
        ],
        [
            "province" => " Lai Châu"
        ],
        [
            "province" => " Sơn La"
        ],
        [
            "province" => " Yên Bái"
        ],
        [
            "province" => " Hoà Bình"
        ],
        [
            "province" => " Thái Nguyên"
        ],
        [
            "province" => " Lạng Sơn"
        ],
        [
            "province" => " Quảng Ninh"
        ],
        [
            "province" => " Bắc Giang"
        ],
        [
            "province" => " Phú Thọ"
        ],
        [
            "province" => " Vĩnh Phúc"
        ],
        [
            "province" => " Bắc Ninh"
        ],
        [
            "province" => " Hải Dương"
        ],
        [
            "province" => " Hải Phòng"
        ],
        [
            "province" => " Hưng Yên"
        ],
        [
            "province" => " Thái Bình"
        ],
        [
            "province" => " Hà Nam"
        ],
        [
            "province" => " Nam Định"
        ],
        [
            "province" => " Ninh Bình"
        ],
        [
            "province" => " Thanh Hóa"
        ],
        [
            "province" => " Nghệ An"
        ],
        [
            "province" => " Hà Tĩnh"
        ],
        [
            "province" => " Quảng Bình"
        ],
        [
            "province" => " Quảng Trị"
        ],
        [
            "province" => " Thừa Thiên Huế"
        ],
        [
            "province" => " Đà Nẵng"
        ],
        [
            "province" => " Quảng Nam"
        ],
        [
            "province" => " Quảng Ngãi"
        ],
        [
            "province" => " Bình Định"
        ],
        [
            "province" => " Phú Yên"
        ],
        [
            "province" => " Khánh Hòa"
        ],
        [
            "province" => " Ninh Thuận"
        ],
        [
            "province" => " Bình Thuận"
        ],
        [
            "province" => " Kon Tum"
        ],
        [
            "province" => " Gia Lai"
        ],
        [
            "province" => " Đắk Lắk"
        ],
        [
            "province" => " Đắk Nông"
        ],
        [
            "province" => " Lâm Đồng"
        ],
        [
            "province" => " Bình Phước"
        ],
        [
            "province" => " Tây Ninh"
        ],
        [
            "province" => " Bình Dương"
        ],
        [
            "province" => " Đồng Nai"
        ],
        [
            "province" => " Bà Rịa - Vũng Tàu"
        ],
        [
            "province" => " Hồ Chí Minh"
        ],
        [
            "province" => " Long An"
        ],
        [
            "province" => " Tiền Giang"
        ],
        [
            "province" => " Bến Tre"
        ],
        [
            "province" => " Trà Vinh"
        ],
        [
            "province" => " Vĩnh Long"
        ],
        [
            "province" => " Đồng Tháp"
        ],
        [
            "province" => " An Giang"
        ],
        [
            "province" => " Kiên Giang"
        ],
        [
            "province" => " Cần Thơ"
        ],
        [
            "province" => " Hậu Giang"
        ],
        [
            "province" => " Sóc Trăng"
        ],
        [
            "province" => " Bạc Liêu"
        ],
        [
            "province" => " Cà Mau"
        ]
    ];
    public function run(): void
    {
        foreach ($this->province as $value) {
            Province::create($value);
        };
    }
}
