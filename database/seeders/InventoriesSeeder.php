<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoriesSeeder extends Seeder
{
    public function run()
    {
        DB::table('inventories')->insert([
            [
                'code' => 'INV001',
                'equipment_code' => 'E001',
                'batch_number' => 'B001',
                'current_quantity' => 52,
                'import_code' => 'REC0001',
                'export_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'code' => 'INV002',
                'equipment_code' => 'E002',
                'batch_number' => 'V001',
                'current_quantity' => 52,
                'import_code' => 'REC0001',
                'export_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'code' => 'INV003',
                'equipment_code' => 'E005',
                'batch_number' => 'P001',
                'current_quantity' => 52,
                'import_code' => 'REC0001',
                'export_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]
        ]);
    }
}
