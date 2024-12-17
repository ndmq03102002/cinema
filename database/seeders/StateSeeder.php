<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        State::insert([
            'name' => 'TP Hồ Chí Minh',
            'key' => 'HCM',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        State::insert([
            'name' => 'Bình Dương',
            'key' => 'BD',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        State::insert([
            'name' => 'Bình Phước',
            'key' => 'BP',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        State::insert([
            'name' => 'Bà rịa - Vũng tàu',
            'key' => 'VT',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
