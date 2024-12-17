<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Promotion_User extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ci_promotion_user';
    protected $fillable = ['promotion_id', 'account_id'];
    
}
