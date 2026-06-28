<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'password' => Hash::make('demo1@demo1.com'),
        ], [
            'name' => 'Lorem Ipsum',
            'email' => 'demo2@demo2.com',
            'password' => Hash::make('demo2@demo2.com'),
        ]];

        if (User::count() == 0) {
            User::insert($demoUsers);
        }
    }
}
