<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        ]);
    }
}