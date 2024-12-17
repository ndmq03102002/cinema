<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
//            ['name' => 'Việt Nam', 'description' => 'Quốc gia Đông Nam Á với nền văn hóa phong phú.'],
            ['name' => 'Hoa Kỳ', 'description' => 'Quốc gia Bắc Mỹ, có nền kinh tế hàng đầu thế giới.'],
            ['name' => 'Anh', 'description' => 'Quốc gia châu Âu với lịch sử lâu đời và văn hóa đa dạng.'],
            ['name' => 'Pháp', 'description' => 'Quốc gia nổi tiếng với văn hóa, nghệ thuật và ẩm thực.'],
            ['name' => 'Nhật Bản', 'description' => 'Quốc gia châu Á với công nghệ tiên tiến và truyền thống độc đáo.'],
            ['name' => 'Hàn Quốc', 'description' => 'Quốc gia nổi tiếng với âm nhạc K-pop và công nghệ phát triển.'],
            ['name' => 'Trung Quốc', 'description' => 'Quốc gia đông dân nhất thế giới với lịch sử hơn 5000 năm.'],
            ['name' => 'Đức', 'description' => 'Quốc gia châu Âu với nền kinh tế mạnh và công nghiệp phát triển.'],
            ['name' => 'Ấn Độ', 'description' => 'Quốc gia Nam Á với nền văn hóa và tôn giáo đa dạng.'],
            ['name' => 'Canada', 'description' => 'Quốc gia Bắc Mỹ với thiên nhiên hùng vĩ và hệ thống giáo dục tốt.'],
            ['name' => 'Úc', 'description' => 'Quốc gia châu Đại Dương với động vật độc đáo và phong cảnh đẹp.'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
