<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportsSeeder extends Seeder
{
    public function run()
    {
        DB::table('reports')->insert([
            [
                'code' => 'RP59JF423K',
                'user_code' => 'U001',
                'report_type' => 'Nhập kho',
                'content' => 'Welcome to the system!',
                'file' => 'abc.com',
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);
    }
}
