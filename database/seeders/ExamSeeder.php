<?php

namespace Database\Seeders;

use App\Models\Exam;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => "Đề thi thử tốt nghiệp THPT năm 2023 môn Toán sở GD&ĐT Hậu Giang",
                "time" => 90,
                "user_id" => 1
            ],
            [
                "name" => "Đề thi thử tốt nghiệp THPT năm 2023 môn Toán sở GD&ĐT Sóc Trăng",
                "time" => 90,
                "user_id" => 1
            ],
            [
                "name" => "Đề thi thử tốt nghiệp THPT 2023 môn Toán lần 2 sở GD&ĐT Kiên Giang",
                "time" => 90,
                "user_id" => 1
            ],
            [
                "name" => "Đề thi thử TN THPT 2023 môn Toán lần 3 trường chuyên Hạ Long – Quảng Ninh",
                "time" => 90,
                "user_id" => 1
            ]
        ];
        foreach ($data as $item) {
            Exam::create($item);
        }
    }
}
