<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => "Giới Hạn, Hàm Số Liên Tục",
                "price" => 10000,
                "image" => "https://api.ductran.site/storage/courses/QoUQmFj0WAS59c9HLGCXRlljw6oQRpc18GuAZJUm.png",
                "description" => "Tuyển tập các tài liệu môn Toán hay nhất về chủ đề GIỚI HẠN – HÀM SỐ LIÊN TỤC trong chương trình môn Toán lớp 11, bao gồm các nội dung: Giới Hạn Của Dãy Số; Giới Hạn Của Hàm Số; Hàm Số Liên Tục.",
            ], [
                "name" => "Phương Pháp Tọa Độ Trong Không Gian",
                "price" => 1000,
                "image" => "https://api.ductran.site/storage/courses/pVT4bUyhMIl0990y1NTVj9GRcb0EVLEmeozDBVD1.png",
                "description" => "Tuyển tập các tài liệu môn Toán hay nhất về chủ đề PHƯƠNG PHÁP TOẠ ĐỘ TRONG KHÔNG GIAN trong chương trình môn Toán lớp 12, bao gồm các nội dung: Toạ Độ Của Vectơ Đối Với Một Hệ Trục Toạ Độ; Biểu Thức Toạ Độ Của Các Phép Toán Vectơ; Phương Trình Mặt Phẳng; Phương Trình Đường Thẳng Trong Không Gian; Phương Trình Mặt Cầu.",
            ], [
                "name" => "Mặt Nón, Mặt Trụ, Mặt Cầu",
                "price" => 0,
                "image" => "https://api.ductran.site/storage/courses/l6aHNL2rvOHqyoPHyP4RgbPNAfUmVdtHUTbL0X90.png",
                "description" => "Tuyển tập các tài liệu môn Toán hay nhất về chủ đề MẶT NÓN – MẶT TRỤ – MẶT CẦU trong chương trình môn Toán lớp 12, bao gồm các nội dung: Mặt Cầu, Khối Cầu; Khái Niệm Về Mặt Tròn Xoay; Mặt Trụ, Hình Trụ Và Khối Trụ; Mặt Nón, Hình Nón Và Khối Nón.",
            ], [
                "name" => "Khối Đa Diện",
                "price" => 0,
                "image" => "https://api.ductran.site/storage/courses/GgD59iovS8Pxp7DFSaKpbQwQdGRg3V3GZU95NLNt.png",
                "description" => "Tuyển tập các tài liệu môn Toán hay nhất về chủ đề KHỐI ĐA DIỆN trong chương trình môn Toán lớp 12, bao gồm các nội dung: Khái Niệm Về Khối Đa Diện; Phép Đối Xứng Qua Mặt Phẳng Và Sự Bằng Nhau Của Các Khối Đa Diện; Phép Vị Tự Và Sự Đồng Dạng Của Các Khối Đa Diện; Các Khối Đa Diện Đều; Thể Tích Của Khối Đa Diện.",
            ], [
                "name" => "Số Phức",
                "price" => 0,
                "image" => "https://api.ductran.site/storage/courses/ftONQT0OObpbhnQnvhClpH91OdKvYMnlYCbPxPoV.png",
                "description" => "Tuyển tập các tài liệu môn Toán hay nhất về chủ đề SỐ PHỨC trong chương trình môn Toán lớp 12, bao gồm các nội dung: Số Phức; Căn Bậc Hai Của Số Phức Và Phương Trình Bậc Hai; Dạng Lượng Giác Của Số Phức Và Ứng Dụng.",
            ], [
                "name" => "Nguyên Hàm, Tích Phân Và Ứng Dụng",
                "price" => 0,
                "image" => "https://api.ductran.site/storage/courses/HzqNoSgF7SmGFlN7kSjjFZCQTpgIMikorcT5oHGy.png",
                "description" => "Tuyển tập các tài liệu môn Toán hay nhất về chủ đề NGUYÊN HÀM – TÍCH PHÂN trong chương trình môn Toán lớp 12",
            ], [
                "name" => "Hàm Số Lũy Thừa, Hàm Số Mũ Và Hàm Số Logarit",
                "price" => 0,
                "image" => "https://api.ductran.site/storage/courses/OUP8NYPeaugrcKauF6OmyS9yBHGksccJwGVJ7u5N.png",
                "description" => "Chủ đề HÀM SỐ MŨ VÀ HÀM SỐ LÔGARIT trong chương trình môn Toán lớp 12, bao gồm các nội dung: Phép Tính Lũy Thừa; Phép Tính Lôgarit; Hàm Số Mũ Và Hàm Số Lôgarit; Phương Trình, Bất Phương Trình Mũ Và Lôgarit.",
            ], [
                "name" => "Ứng Dụng Đạo Hàm Để Khảo Sát Và Vẽ Đồ Thị Của Hàm Số",
                "price" => 0,
                "image" => "https://api.ductran.site/storage/courses/IbKVwqi8gK7b7d9rJImvfEWzfVSmdGo5tdXHbzyj.png",
                "description" => "Toán 12 phần Giải tích chương 1: Ứng Dụng Đạo Hàm Để Khảo Sát Và Vẽ Đồ Thị Của Hàm Số.",
            ]
        ];

        foreach ($data as $item) {
            Course::create($item);
        }

        $course = Course::find(8);
        Lesson::create([
            'user_id' => 1,
            'course_id' => $course->id,
            'name' => 'Bài 1: Đường tiệm cận của đồ thị hàm số',
        ]);
    }
}
