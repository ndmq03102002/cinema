<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'ci_seat';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'room_id',
        'seat_type_id',
        'seat_code'
    ];
    public $timestamps = true;

    public function room()
    {
        return $this->belongsTo('App\Models\Room', 'room_id', 'id');
    }

    public function seatType()
    {
        return $this->belongsTo('App\Models\SeatType', 'seat_type_id', 'id');
    }
}
