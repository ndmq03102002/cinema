<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Seat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $matrix = [
            [0, 1, 1, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 0, 0],
            [1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 0],
            [1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 0],
            [1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 0],
            [1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1],
            [1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1],
            [1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1],
            [1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1],
            [1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1],
            [1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1]
        ];
        $matrix2 = '[[0, 1, 1, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 0, 0],[1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 0],[1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 0],[1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 0],[1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1],[1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1],[1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1],[1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1],[1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1],[1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1]]';
        $room = Room::create([
            'name' => 'Cinema 1',
            'cinema_id' => '1',
            'seat_map' => $matrix2
        ]);
        $rows = range('A', 'Z');

        foreach ($matrix as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                if ($value === 1) {
                    if($rowIndex < 3 || $rowIndex > 8) {
                        $seat_list = [
                            'seat_type_id' => 2,
                            'seat_code' => $rows[$rowIndex] . ($colIndex + 1),
                            'room_id' => $room->id
                        ];
                        Seat::create($seat_list);
                    }
                    else {
                        if($colIndex < 3 || $colIndex >= 12 ){
                            $seat_list = [
                                'seat_type_id' => 1,
                                'seat_code' => $rows[$rowIndex] . ($colIndex + 1),
                                'room_id' => $room->id
                            ];
                            Seat::create($seat_list);
                        }else{
                            $seat_list = [
                                'seat_type_id' => 2,
                                'seat_code' => $rows[$rowIndex] . ($colIndex + 1),
                                'room_id' => $room->id
                            ];
                            Seat::create($seat_list);
                        }

                    }
                }
            }
        }
        $room = Room::create([
            'name' => 'Cinema 2',
            'cinema_id' => '1',
            'seat_map' => $matrix2
        ]);
        $rows = range('A', 'Z');

        foreach ($matrix as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                if ($value === 1) {
                    if($rowIndex < 3 || $rowIndex > 8) {
                        $seat_list = [
                            'seat_type_id' => 2,
                            'seat_code' => $rows[$rowIndex] . ($colIndex + 1),
                            'room_id' => $room->id
                        ];
                        Seat::create($seat_list);
                    }
                    else {
                        if($colIndex < 3 || $colIndex >= 12 ){
                            $seat_list = [
                                'seat_type_id' => 1,
                                'seat_code' => $rows[$rowIndex] . ($colIndex + 1),
                                'room_id' => $room->id
                            ];
                            Seat::create($seat_list);
                        }else{
                            $seat_list = [
                                'seat_type_id' => 2,
                                'seat_code' => $rows[$rowIndex] . ($colIndex + 1),
                                'room_id' => $room->id
                            ];
                            Seat::create($seat_list);
                        }
                    }
                }
            }
        }
    }
}
