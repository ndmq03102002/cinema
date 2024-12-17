<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql';

    protected $table = 'ci_bill';

    const PENDING = 0;

    const PAID = 1;

    const EXPIRED = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'ticket_code',
        'account_id',
        'cinema_id',
        'movie_show_time_id',
        'staff_check',
        'total',
        'status',
        'extra_id'
    ];
    public $timestamps = true;

    public function ticket(){
        return $this->hasMany('App\Models\Ticket', 'bill_id', 'id');
    }

    public function account(){
        return $this->belongsTo('App\Models\Account', 'account_id', 'id');
    }

    public function cinema()
    {
        return $this->belongsTo('App\Models\Cinema', 'cinema_id', 'id');
    }

    public function movieShowTime()
    {
        return $this->belongsTo('App\Models\MovieShowTime', 'movie_show_time_id', 'id');
    }
}
