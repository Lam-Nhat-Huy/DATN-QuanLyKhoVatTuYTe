<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportDetailsSeeder extends Seeder
{
    public function run()
    {
        DB::table('export_details')->insert([]);
    }
}