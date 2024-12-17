<?php

namespace App\Repositories\admin\Seat;

use App\Models\Seat;
use Illuminate\Support\Facades\Hash;

class SeatRepository implements SeatInterface{

    function model(){
        return Seat::class;
    }
    public function creatSeat($request, $roomId)
    {
        return Seat::create([
            'room_id' => $roomId,
            'seat_type_id' => $request['seat_type_id'],
            'seat_code' => $request['seat_code']
        ]);
    }
}
