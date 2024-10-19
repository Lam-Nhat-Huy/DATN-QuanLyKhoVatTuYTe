<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'code' => 'U001',
                'first_name' => 'Huy',
                'last_name' => 'Lu Phat',
                'email' => 'lphdev04@gmail.com',
                'phone' => '0945567048',
                'password' => bcrypt('khongcomatkhau'),
                'birth_day' => '2004-12-12',
                'gender' => 'Nam',
                'address' => 'Kiên Giang',
                'isAdmin' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'code' => 'U002',
                'first_name' => 'Huy',
                'last_name' => 'Lam Nhat',
                'email' => 'lamnhathuy0393418721@gmail.com',
                'phone' => '0393379824',
                'password' => bcrypt('khongcomatkhau'),
                'birth_day' => '2024-01-01',
                'gender' => 'Nam',
                'address' => 'Sóc Trăng',
                'isAdmin' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'code' => 'U003',
                'first_name' => 'Huy',
                'last_name' => 'Nguyen Quoc',
                'email' => 'nguyenquochuy9602@gmail.com',
                'phone' => '0869119602',
                'password' => bcrypt('khongcomatkhau'),
                'birth_day' => '2024-01-01',
                'gender' => 'Nam',
                'address' => 'Sóc Trăng',
                'isAdmin' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'code' => 'U004',
                'first_name' => 'Hoài',
                'last_name' => 'Phạm Anh',
                'email' => 'phamanhhoaipl@gmail.com',
                'phone' => '0375527037',
                'password' => bcrypt('khongcomatkhau'),
                'birth_day' => '2024-01-01',
                'gender' => 'Nam',
                'address' => 'Sóc Trăng',
                'isAdmin' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'code' => 'U005',
                'first_name' => 'Thái',
                'last_name' => 'Lê Nhựt',
                'email' => 'nhutthai2018@gmail.com',
                'phone' => '0969245242',
                'password' => bcrypt('khongcomatkhau'),
                'birth_day' => '2024-01-01',
                'gender' => 'Nam',
                'address' => 'Sóc Trăng',
                'isAdmin' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);
    }
}
