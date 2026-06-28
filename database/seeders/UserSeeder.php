<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoUsers = [[
            'name' => 'John Doe',
            'email' => 'demo1@demo1.com',
            'password' => 'demo1@demo1.com',
        ], [
            'name' => 'Lorem Ipsum',
            'email' => 'demo2@demo2.com',
            'password' => 'demo2@demo2.com',
        ]];

        if (User::count() == 0) {
            User::insert($demoUsers);
        }
    }
}
