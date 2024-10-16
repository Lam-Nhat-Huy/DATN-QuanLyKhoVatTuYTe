<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsSeeder extends Seeder
{
    public function run()
    {
        DB::table('departments')->insert([
            [
                'code' => 'DEP001',
                'name' => 'Khoa Nội',
                'description' => 'Chăm sóc và điều trị bệnh nhân nội trú tại tòa nhà A.',
                'location' => 'Tòa nhà A, Tầng 1',
                'created_by' => 'U001',
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'code' => 'DEP002',
                'name' => 'Khoa Ngoại',
                'description' => 'Phục vụ bệnh nhân ngoại trú và các phẫu thuật nhỏ.',
                'location' => 'Tòa nhà B, Tầng 2',
                'created_by' => 'U001',
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'code' => 'DEP003',
                'name' => 'Khoa Cấp cứu',
                'description' => 'Đối phó nhanh chóng với các trường hợp khẩn cấp.',
                'location' => 'Gần lối vào chính của bệnh viện',
                'created_by' => 'U001',
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'code' => 'DEP004',
                'name' => 'Khoa Phục hồi chức năng',
                'description' => 'Hỗ trợ phục hồi chức năng cho bệnh nhân sau điều trị.',
                'location' => 'Tòa nhà D, Tầng 3',
                'created_by' => 'U001',
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'code' => 'DEP005',
                'name' => 'Khoa Xét nghiệm',
                'description' => 'Tiến hành xét nghiệm và phân tích mẫu bệnh phẩm.',
                'location' => 'Tòa nhà E, Tầng trệt',
                'created_by' => 'U001',
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);
    }
}