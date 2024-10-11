<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    public function run()
    {
        DB::table('notifications')->insert([
            [
                'code' => 'TBY2O5ALAM',
                'user_code' => 'U002',
                'notification_type' => 1,
                'content' => 'Vui lòng không thực hiện nhập xuất kho trong vòng 24h tới. Đây là thông báo quan trọng, để quán trình kiểm kho được chính xác nhất!',
                'important' => 1,
                'status' => 1,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'code' => 'N002',
                'user_code' => 'U001',
                'notification_type' => 1,
                'content' => 'Welcome to the system!',
                'important' => 0,
                'status' => 1,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);
    }
}