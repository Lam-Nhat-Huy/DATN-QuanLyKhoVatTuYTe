<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportDetailsSeeder extends Seeder
{
    public function run()
    {
        DB::table('export_details')->insert([
            [
                'export_code' => 'EXP001',
                'equipment_code' => 'E001',
                'quantity' => 3,
                'batch_number' => 'B001',
            ],
        ]);
    }
}
