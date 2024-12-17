<?php

namespace App\Repositories\admin\Room;

interface RoomInterface
{
    public function getRoomCheck($request);

    public function createRoom($request);

    public function getRoom($id);

}
