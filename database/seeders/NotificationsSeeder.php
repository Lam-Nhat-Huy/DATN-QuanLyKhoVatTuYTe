<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    public function run()
    {
        DB::table('notifications')->insert([
            [
                'code' => 'TB89KNAKSDK',
                'user_code' => 'U001',
                'notification_type' => 1,
                'content' => 'Chào mừng đến Hệ thống Quản lý Thiết bị Y tế Beesoft!',
                'important' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'code' => 'TB89KNAK001',
                'user_code' => 'U002',
                'notification_type' => 2,
                'content' => 'Thông báo bảo trì hệ thống từ 22:00 đến 23:00 ngày 16/10.',
                'important' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'code' => 'TB89KNAK002',
                'user_code' => 'U003',
                'notification_type' => 4,
                'content' => 'Cập nhật phiên bản mới của phần mềm vào ngày 17/10.',
                'important' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'code' => 'TB89KNAK003',
                'user_code' => 'U005',
                'notification_type' => 6,
                'content' => 'Lịch bảo trì thiết bị: 19/10, 14:00 đến 15:00.',
                'important' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'code' => 'TB89KNAK004',
                'user_code' => 'U004',
                'notification_type' => 1,
                'content' => 'Hãy đảm bảo rằng các thiết bị đều được kiểm tra trước khi sử dụng.',
                'important' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);
    }
}