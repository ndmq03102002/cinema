<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql';

    protected $table = 'ci_room';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'cinema_id',
        'name',
        'seat_map',
    ];
    public $timestamps = true;

    public function cinema()
    {
        return $this->belongsTo('App\Models\Cinema', 'cinema_id', 'id');
    }

    public function movieShowTime()
    {
        return $this->hasMany('App\Models\MovieShowtime', 'room_id', 'id');
    }

    public function seat()
    {
        return $this->hasMany('App\Models\Seat', 'room_id', 'id');
    }
}
