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
            'password' => bcrypt('123123'),
            'name' => 'Admin',
            'avatar' => 'https://cdn.thoitiet247.edu.vn/wp-content/uploads/2024/03/avatar-xam-1.jpg',
            'role_id' => User::ROLE_ADMIN
        ]);

        User::updateOrCreate([
            'email' => 'giaovien@gmail.com'
            ], [
            'password' => bcrypt('123123'),
            'name' => 'Lê Đình Trung',
            'avatar' => 'https://dulich3mien.vn/wp-content/uploads/2023/04/Anh-Avatar-doi-63.jpg',
            'role_id' => User::ROLE_TEACHER
        ]);

        User::updateOrCreate([
            'email' => 'hocsinh@gmail.com'
            ], [
            'password' => bcrypt('123123'),
            'name' => 'Nguyễn Văn Anh',
            'avatar' => 'https://cdn.vn.alongwalk.info/wp-content/uploads/2023/04/10070836/image-thu-vien-tong-hop-nhung-avatar-doi-chat-ngau-nhu-trai-bau-168106011654404.jpg',
            'role_id' => User::ROLE_USER
        ]);
    }
}
