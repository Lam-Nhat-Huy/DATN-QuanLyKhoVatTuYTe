<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTypesSeeder extends Seeder
{
    public function run()
    {
        DB::table('notification_types')->insert([
            [
                'name' => 'Lưu ý',
            ],
            [
                'name' => 'Thông báo',
            ],
            [
                'name' => 'Cảnh báo',
            ],
            [
                'name' => 'Cập nhật',
            ],
            [
                'name' => 'Khuyến mãi',
            ],
            [
                'name' => 'Bảo trì',
            ],
        ]);
    }
}