<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReceiptDetailsSeeder extends Seeder
{
    public function run()
    {
        DB::table('receipt_details')->insert([]);
    }
}
