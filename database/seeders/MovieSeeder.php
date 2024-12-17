<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Movie_Genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $listMovie = [
            [
                "id" => 1,
                "poster" => "https://iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/3/5/350x495-linhmieu.jpg",
                "name" => "LINH MIÊU",
                "date" => "2024-11-22",
                "duration" => 120,
                "trailerUrl" => "https://youtu.be/4oxoPMxBO6s",
                "director" => "Lưu Thành Luân",
                "movieGenre" => [3],
                "rated" => "18+",
                "country" => 1,
                "description" => "Linh Miêu: Quỷ Nhập Tràng lấy cảm hứng từ truyền thuyết dân gian...",
            ],
            [
                "id" => 2,
                "poster" => "https://iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/a/m/amazon-main-poster-printing.jpg",
                "name" => "CƯỜI XUYÊN BIÊN GIỚI",
                "date" => "2024-11-15",
                "duration" => 120,
                "trailerUrl" => "https://youtu.be/4ALt4VT7grw",
                "director" => "KIM Chang-ju",
                "movieGenre" => [3],
                "rated" => "13+",
                "country" => 6,
                "description" => "Cười Xuyên Biên Giới kể về hành trình của Jin-bong...",
            ],
            [
                "id" => 3,
                "poster" => "https://iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/7/0/700x1000-gladiator.jpg",
                "name" => "VÕ SĨ GIÁC ĐẤU II",
                "date" => "2024-11-15",
                "duration" => 120,
                "trailerUrl" => "https://youtu.be/R4AFSgUGEEs",
                "director" => "Ridley Scott",
                "movieGenre" => [1, 6, 7],
                "rated" => "18+",
                "country" => 3,
                "description" => "Sau khi đánh mất quê hương vào tay hoàng đế bạo chúa...",
            ],
            [
                "id"=> 3,
                "poster"=> "https=>//iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/7/0/700x1000-gladiator.jpg",
                "name"=> "VÕ SĨ GIÁC ĐẤU II",
                "date"=> "2024-11-15",
                "duration"=> 120,
                "trailerUrl"=> "https=>//youtu.be/R4AFSgUGEEs",
                "director"=> "Ridley Scott",
                "movieGenre"=> [1, 6, 7],
                "rated"=> "18+",
                "country"=> 3,
                "description"=> "Sau khi đánh mất quê hương vào tay hoàng đế bạo chúa – người đang cai trị Rome, Lucius trở thành nô lệ giác đấu trong đấu trường Colosseum và phải tìm kiếm sức mạnh từ quá khứ để đưa vinh quang trở lại cho người dân Rome."
            ],
            [
                "id"=> 4,
                "poster"=> "https=>//iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/3/5/350x495-red-one_1.jpg",
                "name"=> "MẬT MÃ ĐỎ",
                "date"=> "2024-11-08",
                "duration"=> 120,
                "trailerUrl"=> "https=>//youtu.be/2T_mKyH17mY",
                "director"=> "Jake Kasdan",
                "movieGenre"=> [1, 3, 6],
                "rated"=> "16+",
                "country"=> 2,
                "description"=>
                    "Sau khi Ông già Noel (mật danh=> Red One) bị bắt cóc, Trưởng An ninh Bắc Cực (Dwayne Johnson) phải hợp tác với thợ săn tiền thưởng khét tiếng nhất thế giới (Chris Evans) trong một nhiệm vụ kịch tính xuyên lục địa để giải cứu Giáng Sinh."
            ],
            [
                "id"=> 5,
                "poster"=> "https=>//iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/l/i/litbc-main-poster-printing.jpg",
                "name"=> "ĐÔI BẠN HỌC YÊU",
                "date"=> "2024-11-08",
                "duration"=> 120,
                "trailerUrl"=> "https=>//youtu.be/EIARKqcXILM",
                "director"=> "E.Oni",
                "movieGenre"=> [3, 4],
                "rated"=> "18+",
                "country"=> 6,
                "description"=>
                    "Bộ phim xoay quanh đôi bạn ngỗ nghịch Jae-hee và Heung-soo cùng những khoảnh khắc “dở khóc dở cười” khi cùng chung sống trong một ngôi nhà. Jae-hee là một cô gái “cờ xanh” với tâm hồn tự do, sống hết mình với tình yêu. Ngược lại, Heung-soo lại là một “cờ đỏ” chính hiệu khi cho rằng tình yêu là sự lãng phí cảm xúc không cần thiết. Bỏ qua những tin đồn lan tràn do người khác tạo ra, Jae-hee và Heung-soo chọn sống chung nhưng yêu theo cách riêng của họ. Hai quan điểm tình yêu trái ngược sẽ đẩy cả hai sang những ngã rẽ và kết cục khác nhau. Sau cùng, Jae-hee hay Heung-soo sẽ về đích trong hành trình “học yêu” này?"
            ],
            [
                "id"=> 6,
                "poster"=> "https=>//iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/_/s/_size_chu_n_nxcmct_main-poster_dctr_1_.jpg",
                "name"=> "NGÀY XƯA CÓ MỘT CHUYỆN TÌNH",
                "date"=> "2024-10-28",
                "duration"=> 120,
                "trailerUrl"=> "https=>//youtu.be/qaeHlk0OXec",
                "director"=> "Trịnh Đình Lê Minh",
                "movieGenre"=> [4],
                "rated"=> "18+",
                "country"=> 1,
                "description"=> "Ngày Xưa Có Một Chuyện Tình xoay quanh câu chuyện tình bạn, tình yêu giữa hai chàng trai và một cô gái từ thuở ấu thơ cho đến khi trưởng thành, phải đối mặt với những thử thách của số phận. Trải dài trong 4 giai đoạn từ năm 1987 - 2000, ba người bạn cùng tuổi - Vinh, Miền, Phúc đã cùng yêu, cùng bỡ ngỡ bước vào đời, va vấp và vượt qua."
            ],
            [
                "id"=> 7,
                "poster"=> "https=>//iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/r/s/rsz_vnm3_intl_online_1080x1350_tsr_01.jpg",
                "name"=> "VENOM=> KÈO CUỐI",
                "date"=> "2024-10-25",
                "duration"=> 120,
                "trailerUrl"=> "https=>//youtu.be/id1rfr_KZWg",
                "director"=> "Kelly Marcel",
                "movieGenre"=> [1, 10, 6, 9],
                "rated"=> "18+",
                "country"=> 2,
                "description"=>
                    "Đây là phần phim cuối cùng và hoành tráng nhất về cặp đôi Venom và Eddie Brock (Tom Hardy). Sau khi dịch chuyển từ Vũ trụ Marvel trong ‘Spider-man=> No way home’ (2021) trở về thực tại, Eddie Brock giờ đây cùng Venom sẽ phải đối mặt với ác thần Knull hùng mạnh - kẻ tạo ra cả chủng tộc Symbiote và những thế lực đang rình rập khác. Cặp đôi Eddie và Venom sẽ phải đưa ra lựa quyết định khốc liệt để hạ màn kèo cuối này."
            ],
            [
                "id"=> 8,
                "poster"=> "https=>//iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/o/f/official_poster_the_substance.jpg",
                "name"=> "THẦN DƯỢC",
                "date"=> "2024-01-22",
                "duration"=> 120,
                "trailerUrl"=> "https=>//youtu.be/zBIDSp17AOo",
                "director"=> "Coralie Fargeat",
                "movieGenre"=> [3],
                "rated"=> "18+",
                "country"=> 2,
                "description"=>
                    "Elizabeth Sparkle, minh tinh sở hữu vẻ đẹp hút hồn cùng với tài năng được mến mộ nồng nhiệt. Khi đã trải qua thời kỳ đỉnh cao, nhan sắc dần tàn phai, cô tìm đến những kẻ buôn lậu để mua một loại thuốc bí hiểm nhằm \"thay da đổi vận\", tạo ra một phiên bản trẻ trung hơn của chính mình."
            ],
            [
                "id"=> 9,
                "poster"=> "https=>//iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/p/o/poster_ngay_ta_da_yeu_6.jpg",
                "name"=> "NGÀY TA ĐÃ YÊU",
                "date"=> "2024-11-15",
                "duration"=> 120,
                "trailerUrl"=> "https=>//youtu.be/lbLk9PzHWfg",
                "director"=> "John Crowley",
                "movieGenre"=> [4, 7],
                "rated"=> "18+",
                "country"=> 3,
                "description"=> "Định mệnh đã đưa một nữ đầu bếp đầy triển vọng và một người đàn ông vừa trải qua hôn nhân đổ vỡ đến với nhau trong tình cảnh đặc biệt. Bộ phim là cuộc tình mười năm sâu đậm của cặp đôi này, từ lúc họ rơi vào lưới tình, xây dựng tổ ấm, cho đến khi một biến cố xảy đến thay đổi hoàn toàn cuộc đời họ."
            ],
            [
                "id"=> 10,
                "poster"=> "https=>//iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/o/z/ozi_poster_single_470x700.jpg",
                "name"=> "OZI=> PHI VỤ RỪNG XANH",
                "date"=> "2024-11-15",
                "duration"=> 120,
                "trailerUrl"=> "https=>//youtu.be/tyHPFFnDuZY",
                "director"=> "Tim Harper",
                "movieGenre"=> [5],
                "rated"=> "18+",
                "country"=> 3,
                "description"=>
                    "Câu chuyện xoay quanh Ozi, một cô đười ươi mồ côi có tầm ảnh hưởng, sử dụng những kỹ năng học được để bảo vệ khu rừng và ngôi nhà của mình khỏi sự tàn phá của nạn phá rừng. Đây là một bộ phim đầy hy vọng, truyền cảm hứng cho thế hệ trẻ mạnh dạn cất tiếng nói và hành động để bảo vệ ngôi nhà chung quý giá."
            ],
            [
                "id"=> 11,
                "poster"=> "https=>//iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/c/l/clnc-digitalposter-vnmarket-2048_1_.jpg",
                "name"=> "CU LI KHÔNG BAO GIỜ KHÓC",
                "date"=> "2024-11-15",
                "duration"=> 120,
                "trailerUrl"=> "https=>//youtu.be/kMjlJkmt5nk",
                "director"=> "Phạm Ngọc Lân",
                "movieGenre"=> [4, 7, 8],
                "rated"=> "18+",
                "country"=> 1,
                "description"=>
                    "Sau đám tang người chồng cũ ở nước ngoài, bà Nguyện quay lại Hà Nội với một bình tro và một con cu li nhỏ - loài linh trưởng đặc hữu của bán đảo Đông Dương. Về tới nơi, bà phát hiện ra cô cháu gái mang bầu đang vội vã chuẩn bị đám cưới. Lo sợ cô đi theo vết xe đổ của đời mình, bà kịch liệt phản đối. Bộ phim Cu Li Không Bao Giờ Khóc khéo léo pha trộn đời sống hiện tại và những dư âm phức tạp của lịch sử Việt Nam bằng cách đan xen hoài niệm về quá khứ của người dì lớn tuổi và dự cảm về tương lai đầy bất định của cặp đôi trẻ."
            ],
            [
                "id"=> 12,
                "poster"=> "https=>//iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/h/o/hon_ma_theo_duoi_-_payoff_poster_-_kc_15.11.2024.jpg",
                "name"=> "HỒN MA THEO ĐUỔI",
                "date"=> "2024-11-15",
                "duration"=> 120,
                "trailerUrl"=> "https=>//youtu.be/B8aGGueNtiE",
                "director"=> "Banjong Pisanthanakun, Parkpoom Wongpoom",
                "movieGenre"=> [3],
                "rated"=> "18+",
                "country"=> 6,
                "description"=>
                    "Nhiếp ảnh gia Tun và bạn gái Jane trong một lần lái xe trên đường đã vô tình gây tai nạn cho một cô gái trẻ rồi bỏ chạy mà không hề quan tâm đến sự sống chết của cô gái đó. Sau vụ tai nạn, Jane mỗi ngày sống trong lo âu, hối hận, còn những tấm ảnh được Tun chụp đều xuất hiện bóng mờ kì lạ. Từ đây, những cơn ác mộng không có hồi kết liên tiếp xảy ra với cặp đôi này, và những bí mật trong quá khứ dần dần được hé mở."
            ],
            [
                "id"=> 13,
                "poster"=> "https=>//iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/k/e/kedongthe_payoff_poster_kc15.11.2024.jpg",
                "name"=> "KẺ ĐÓNG THẾ",
                "date"=> "2024-11-15",
                "duration"=> 120,
                "trailerUrl"=> "https=>//youtu.be/O62t0TMdG4I",
                "director"=> "Lương Quán Nghiêu - Lương Quán Thuấn",
                "movieGenre"=> [1, 11],
                "rated"=> "18+",
                "country"=> 7,
                "description"=>
                    "Một đạo diễn đóng thế hết thời đang vật lộn để tìm lối đi trong ngành công nghiệp điện ảnh nhiều biến động. Ông đánh cược tất cả để tạo nên màn tái xuất cuối cùng, đồng thời cố gắng hàn gắn mối quan hệ với cô con gái xa cách của mình."
            ],
            [
                "id"=> 14,
                "poster"=> "https=>//iguov8nhvyobj.vcdn.cloud/media/catalog/product/cache/1/image/c5f0a1eff4c394a251036189ccddaacd/3/5/350x495-never-let-go.jpg",
                "name"=> "ĐỪNG BUÔNG TAY",
                "date"=> "2024-11-15",
                "duration"=> 120,
                "trailerUrl"=> "https=>//youtu.be/ZlsGSkMIPaU",
                "director"=> "Alexandre Aja",
                "movieGenre"=> [3, 11],
                "rated"=> "18+",
                "country"=> 3,
                "description"=>
                    "Một ngôi nhà chứa đầy bùa chú là nơi an toàn cuối cùng để tránh xa lũ quỷ trong thế giới hậu tận thế đáng sợ. Một người mẹ và 2 đứa con nhỏ phải kết nối với ngôi nhà bằng sợi dây thừng linh thiêng để sinh tồn nơi rừng rậm, nơi hai thực thể ác độc Kẻ Xấu và Kẻ Xa Lạ có thể tước đoạt mạng người trong một phút buông tay."
            ],

        ];

            foreach ($listMovie as $movieData) {
                $movie = Movie::create([
                    'poster' => $movieData['poster'],
                    'name' => $movieData['name'],
                    'date' => $movieData['date'],
                    'duration' => $movieData['duration'],
                    'trailer_url' => $movieData['trailerUrl'],
                    'director' => $movieData['director'],
                    'rated_id' => 1,
                    'performer' => 'performer',
                    'country_id' => $movieData['country'],
                    'genre_id' => 1,
                    'description' => $movieData['description'],
                ]);
                foreach($movieData['movieGenre'] as $id){
                    $data = Movie_Genre::create([
                        'movie_id' => $movie->id,
                        'genre_id' => $id
                    ]);
                }
            }
    }
}
