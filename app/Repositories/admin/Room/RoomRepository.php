<?php

namespace App\Repositories\admin\Room;

use App\Models\Room;
use App\Models\Seat;
use Illuminate\Support\Facades\Hash;

class RoomRepository implements RoomInterface{

    function model(){
        return Room::class;
    }
    public function getRoomCheck($request)
    {
        return Room::where('name', $request['name'])
            ->where('cinema_id', $request['cinema_id'])
            ->count();
    }

    public function createRoom($request)
    {
        $data = $request->all();
        return Room::create([
            'cinema_id' => $data['cinema_id'],
            'name' => $data['name'],
            'seat_map' => $data['seat_map']
        ])->id;
    }

    public function getRoom($id)
    {
        $room = Room::where('id', $id)->first();

        $seats = Seat::with('seatType')
            ->where('room_id', $room->id)
            ->get();

        $seatMap = json_decode($room->seat_map);
        $seatId = 0;
        $data = [];

        foreach ($seatMap as $item) {
            $rowData = [];
            foreach ($item as $item2) {
                if ($item2 == 0) {
                    $rowData[] = [
                        'seat_id' => null,
                        'seat_code' => null,
                        'seat_type_id' => null,
                        'seat_type' => null,
                    ];
                } else {
                    $seatData = $seats[$seatId];
                    $rowData[] = [
                        'seat_id' => $seatData->id,
                        'seat_code' => $seatData->seat_code,
                        'seat_type_id' => $seatData->seatType->id,
                        'seat_name' => $seatData->seatType->name,
                    ];
                    $seatId++;
                }
            }
            $data[] = $rowData;
        }

        return [
            'room_name' => $room->name,
            'seat_list' => $data
        ];
    }
}
