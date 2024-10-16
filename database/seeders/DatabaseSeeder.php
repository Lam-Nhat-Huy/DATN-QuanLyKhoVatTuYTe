<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            NotificationsSeeder::class,
            ReportsSeeder::class,
            DepartmentsSeeder::class,
            EquipmentTypesSeeder::class,
            UnitsSeeder::class,
            SuppliersSeeder::class,
            EquipmentsSeeder::class,
            ReceiptsSeeder::class,
            ReceiptDetailsSeeder::class,
            ExportsSeeder::class,
            ExportDetailsSeeder::class,
            ImportEquipmentRequestsSeeder::class,
            ImportEquipmentRequestDetailsSeeder::class,
            ExportEquipmentRequestsSeeder::class,
            ExportEquipmentRequestDetailsSeeder::class,
            InventoriesSeeder::class
        ]);
    }
}
