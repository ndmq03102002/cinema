<?php

namespace Database\Seeders;

use App\Models\WeeklyTicketPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeeklyTicketPrices extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WeeklyTicketPrice::create([
            'name' => 'Weekdays',
            'extra_fee' => 55000,
            'description' => 'Weekdays',
            'start_time' => '00:00:00',
        ]);
        WeeklyTicketPrice::create([
            'name' => 'Weekdays',
            'extra_fee' => 70000,
            'description' => 'Weekdays',
            'start_time' => '12:00:00',
        ]);
        WeeklyTicketPrice::create([
            'name' => 'Weekdays',
            'extra_fee' => 80000,
            'description' => 'Weekdays',
            'start_time' => '17:00:00',
        ]);
        WeeklyTicketPrice::create([
            'name' => 'Weekdays',
            'extra_fee' => 65000,
            'description' => 'Weekdays',
            'start_time' => '23:00:00',
        ]);
        WeeklyTicketPrice::create([
            'name' => 'Weekend',
            'extra_fee' => 70000,
            'description' => 'Weekend',
            'start_time' => '00:00:00',
        ]);
        WeeklyTicketPrice::create([
            'name' => 'Weekend',
            'extra_fee' => 80000,
            'description' => 'Weekend',
            'start_time' => '12:00:00',
        ]);
        WeeklyTicketPrice::create([
            'name' => 'Weekend',
            'extra_fee' => 90000,
            'description' => 'Weekend',
            'start_time' => '17:00:00',
        ]);
        WeeklyTicketPrice::create([
            'name' => 'Weekend',
            'extra_fee' => 75000,
            'description' => 'Weekend',
            'start_time' => '23:00:00',
        ]);
    }
}
