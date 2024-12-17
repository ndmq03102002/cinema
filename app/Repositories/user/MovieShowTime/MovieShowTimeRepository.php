<?php

namespace App\Repositories\user\MovieShowTime;

use App\Models\Movie;
use App\Models\MovieShowtime;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Support\Facades\Redis;

class MovieShowTimeRepository{
    public function getShowTime($cinemaId, $movieId, $date)
    {
        $data = [];
        if($movieId != null) {
            $movie = Movie::select('ci_movie.*')->join('ci_movie_show_time', 'ci_movie_show_time.movie_id', '=', 'ci_movie.id')
                ->join('ci_room', 'ci_movie_show_time.room_id', '=', 'ci_room.id')
                ->where('ci_movie.id', '=', $movieId)
                ->where('ci_room.cinema_id', '=', $cinemaId)
                ->where('ci_movie_show_time.start_date', $date)
                ->groupBy('ci_movie.id')
                ->first();
            $showTime = MovieShowtime::select('ci_movie_show_time.*', 'ci_room.name as room_name', 'ci_room.id as room_id')
                ->join('ci_room', 'ci_movie_show_time.room_id', '=', 'ci_room.id')
                ->where('ci_room.cinema_id', '=', $cinemaId)
                ->where('ci_movie_show_time.movie_id', '=', $movieId)
                ->where('ci_movie_show_time.start_date', '=', $date)
                ->orderBy('ci_movie_show_time.start_time', 'ASC')
                ->get();
            if($movie && $showTime)
                $data[] = self::mapData($movie, $showTime, $cinemaId);
        return $data;
        }else{
            $movie = Movie::select('ci_movie.*')->join('ci_movie_show_time', 'ci_movie_show_time.movie_id', '=', 'ci_movie.id')
                ->join('ci_room', 'ci_movie_show_time.room_id', '=', 'ci_room.id')
                ->where('ci_room.cinema_id', '=', $cinemaId)
                ->orderBy('ci_movie.date', 'DESC')
                ->where('ci_movie_show_time.start_date', $date)
                ->groupBy('ci_movie.id')
                ->get();
            foreach ($movie as $key){
                $showTime = MovieShowtime::select('ci_movie_show_time.*', 'ci_room.name as room_name', 'ci_room.id as room_id')
                    ->join('ci_room', 'ci_movie_show_time.room_id', '=', 'ci_room.id')
                    ->where('ci_room.cinema_id', '=', $cinemaId)
                    ->where('ci_movie_show_time.movie_id' , '=', $key->id)
                    ->where('ci_movie_show_time.start_date', '=', $date)
                    ->orderBy('ci_movie_show_time.start_time', 'ASC')
                    ->get();
                $data[] = self::mapData($key, $showTime, $cinemaId);
            }
        }
        return $data;
    }
    public function mapData($movie, $showTime, $cinemaId)
    {
        $response = $movie->toArray();
        $response['show_time'] = [];
        foreach ($showTime as $key) {
            $seatCount = Seat::where('room_id', $key['room_id'])->count();
            $seatReserved = Ticket::where('movie_showtime_id', $key['id'])->count();
            $pattern = $cinemaId . '_' . $key['id'] . '_*';
            $keys = Redis::keys($pattern);
            $reservation = 0;
            foreach ($keys as $x) {
                $x = str_replace('laravel_database_', '', $x);
                $seatIdsJson = Redis::get($x);
                $seatIdsArray = json_decode($seatIdsJson, true);
                if (is_array($seatIdsArray)) {
                    $reservation += count($seatIdsArray);
                }
            }

            $seatReserved += $reservation;
            $data = [
                'id' => $key['id'],
                'room_name' => $key['room_name'],
                'start_time' => $key['start_time'],
                'end_time' => $key['end_time'],
                'start_date' => $key['start_date'],
                'seat_count' => ($seatCount - $seatReserved) . '/' . $seatCount
            ];
            $response['show_time'][] = $data;
        }
        return $response;
    }
}
