<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
use Illuminate\Database\Eloquent\SoftDeletes;
class Promotion extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'ci_promotions';

    protected $fillable = [
        'promo_name',
        'avatar',
        'discount',
        'start_date',
        'end_date',
        'description',
        'quantity',
        'status',
    ];
    public function users()
    {
        return $this->belongsToMany(Account::class, 'ci_promotion_user', 'promotion_id', 'account_id')->withTimestamps();
    }
}
