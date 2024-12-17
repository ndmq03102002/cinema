<?php

namespace App\Repositories\user\WeeklyTicketPrices;

use App\Models\WeeklyTicketPrice;
use Carbon\Carbon;

class WeeklyTicketPricesRepository
{
    public static function getTicketPrice($date, $time){
        $date = Carbon::parse($date)->dayOfWeek;
        if($date >= 1 && $date <=5)
            $data = WeeklyTicketPrice::where([
                'name' => 'Weekdays',
                ])->where('start_time', '<=', $time)
                ->orderBy('start_time', 'desc')
                ->first();
        else{
            $data = WeeklyTicketPrice::where([
                'name' => 'Weekend',
                ])->where('start_time', '<=', $time)
                ->orderBy('start_time', 'desc')
                ->first();
        }
        return $data->extra_fee;
    }
}
