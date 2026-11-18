<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'userna me' => 'Darmadi',
            'code'     => 'L910190',
            'role'     => 'leader',
        ]);

        User::create([
            'username' => 'Risman',
            'code'     => 'L910290',
            'role'     => 'leader',
        ]);

         User::create([
            'username' => 'Rafi',
            'code'     => 'OP922508',
            'role'     => 'operator',
        ]);
    }
}
