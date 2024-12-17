<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        {
            $genres = [
                ['name' => 'Kinh dị', 'description' => 'Phim với nội dung đáng sợ, tạo cảm giác hồi hộp và lo lắng.'],
                ['name' => 'Hài', 'description' => 'Thể loại mang lại tiếng cười và sự giải trí nhẹ nhàng.'],
                ['name' => 'Tình cảm', 'description' => 'Phim xoay quanh các mối quan hệ tình yêu, gia đình và cảm xúc.'],
                ['name' => 'Hoạt hình', 'description' => 'Phim với hình ảnh động, thường dành cho trẻ em và gia đình.'],
                ['name' => 'Phiêu lưu', 'description' => 'Thể loại với nội dung khám phá, mạo hiểm và đầy kịch tính.'],
                ['name' => 'Tâm lý', 'description' => 'Phim tập trung vào cảm xúc, tâm lý và phát triển nhân vật.'],
                ['name' => 'Gia đình', 'description' => 'Phim mang tính giáo dục và giải trí cho các thành viên trong gia đình.'],
                ['name' => 'Thần thoại', 'description' => 'Phim dựa trên các câu chuyện thần thoại và truyền thuyết.'],
                ['name' => 'Khoa học viễn tưởng', 'description' => 'Thể loại với nội dung về công nghệ, không gian và tương lai.'],
                ['name' => 'Hồi hộp', 'description' => 'Phim tạo cảm giác căng thẳng, bí ẩn và đầy bất ngờ.'],
            ];

            foreach ($genres as $genre) {
                Genre::create($genre);
            }
        }
    }
}
