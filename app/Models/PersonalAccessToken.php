<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'personal_access_tokens';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'tokenable_id',
        'name',
        'token',
    ];
    public $timestamps = true;

    public function account()
    {
        return $this->belongsTo('App\Models\Account', 'tokenable_id', 'id');
    }
}
