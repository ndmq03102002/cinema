<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cinema extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql';

    protected $table = 'ci_cinema';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'address',
        'avatar',
        'latitute',
        'longitute',
    ];
    public $timestamps = true;

    public function room()
    {
        return $this->hasMany('App\Models\Room', 'cinema_id', 'id');
    }

    public function bill()
    {
        return $this->hasMany('App\Models\Bill', 'cinema_id', 'id');
    }
}
