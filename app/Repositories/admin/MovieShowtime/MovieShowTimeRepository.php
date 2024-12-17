<?php

namespace App\Repositories\admin\MovieShowtime;

use App\Models\MovieShowtime;
use App\Models\Seat;
use Illuminate\Support\Facades\Hash;

class MovieShowTimeRepository{

    public function getShowtimeCheck($request)
    {
        $data = $request->all();
        $showTimeStart = MovieShowtime::where([
            'room_id' => $request['room_id'],
            'start_date' => $request['start_date']
        ])->where(
            'start_time', '<=', $request['start_time']
        )->where(
            'end_time', '>=', $request['start_time']
        )
            ->count();
        $showTimeEnd = MovieShowtime::where([
            'room_id' => $request['room_id'],
            'start_date' => $request['start_date']
        ])->where(
            'start_time', '<=', $request['end_time']
        )->where(
            'end_time', '>=', $request['end_time']
        )
            ->count();
        if(max($showTimeStart, $showTimeEnd) > 0){
            return max($showTimeStart, $showTimeEnd);
        }else{
            return 0;
        }
    }
    public function createShowTime($request)
    {
        $request = $request->all();
        $data = MovieShowtime::create($request);
        return $data;
    }
}
