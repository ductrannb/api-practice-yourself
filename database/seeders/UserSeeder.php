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
        User::updateOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123123'),
            'name' => 'Admin',
            'avatar' => 'https://cdn.thoitiet247.edu.vn/wp-content/uploads/2024/03/avatar-xam-1.jpg',
            'role_id' => User::ROLE_ADMIN
        ]);
    }
}
