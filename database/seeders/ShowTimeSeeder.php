<?php

namespace Database\Seeders;

use App\Models\MovieShowtime;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShowTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startDate = Carbon::create('2024', '11', '28');
        $time = [
            [
                'movie_id' => 9,
                'start_time' => '08:00',
                'end_time' => '10:00',
                'room_id' => 7,
            ],
            [
                'movie_id' => 9,
                'start_time' => '11:00',
                'end_time' => '13:00',
                'room_id' => 7,
            ],
            [
                'movie_id' => 9,
                'start_time' => '15:30',
                'end_time' => '17:30',
                'room_id' => 7,
            ],
            [
                'movie_id' => 9,
                'start_time' => '18:00',
                'end_time' => '20:00',
                'room_id' => 7,
            ],
            [
                'movie_id' => 8,
                'start_time' => '08:00',
                'end_time' => '10:00',
                'room_id' => 8,
            ],
            [
                'movie_id' => 8,
                'start_time' => '11:00',
                'end_time' => '13:00',
                'room_id' => 8,
            ],
            [
                'movie_id' => 8,
                'start_time' => '15:30',
                'end_time' => '17:30',
                'room_id' => 8,
            ],
            [
                'movie_id' => 8,
                'start_time' => '18:00',
                'end_time' => '20:00',
                'room_id' => 8,
            ],
        ];
        for($i = 0; $i<5; $i++) {
            foreach ($time as $key => $showtime) {
                MovieShowTime::create([
                    'movie_id' => $showtime['movie_id'],
                    'start_time' => $showtime['start_time'],
                    'end_time' => $showtime['end_time'],
                    'room_id' => $showtime['room_id'],
                    'start_date' => $startDate->format('Y-m-d'),
                ]);

                $startDate->addDay();
            }
        }
    }
}
