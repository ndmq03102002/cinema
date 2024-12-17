<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SpecialDay extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'ci_special_days';

    protected $fillable = [
        'day_type',
        'description',
        'special_day',
        'extra_fee',
    ];
}
