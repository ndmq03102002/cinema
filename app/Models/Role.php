<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'ci_role';

    const ADMIN = 1;

    const MANAGE = 2;

    const STAFF = 3;

    const USER = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'description',
    ];
    public $timestamps = true;

    public function account()
    {
        return $this->hasMany('App\Models\Account', 'role_id', 'id');
    }
}
