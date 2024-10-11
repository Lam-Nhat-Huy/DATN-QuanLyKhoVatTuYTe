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
        ]);
    }
}