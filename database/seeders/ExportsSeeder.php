<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportsSeeder extends Seeder
{
    public function run()
    {
        DB::table('exports')->insert([]);
    }
}