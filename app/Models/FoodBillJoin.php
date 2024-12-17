<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodBillJoin extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'ci_food_bill_join';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bill_id',
        'food_id',
        'quantity',
        'total',
    ];
    public $timestamps = true;
}
