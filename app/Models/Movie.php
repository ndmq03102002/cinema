<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Movie extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql';

    protected $table = 'ci_movie';

    const COMING_SOON = 0;

    const SHOWING = 1;

    const EXPIRED = 2;

    const HIDDEN = 3;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'slug',
        'genre_id',
        'country_id',
        'rated_id',
        'avatar',
        'poster',
        'trailer_url',
        'duration',
        'date',
        'performer',
        'director',
        'description',
        'status',
        'voting',
        'vote_total'
    ];
    public $timestamps = true;

    public function rated()
    {
        return $this->belongsTo('App\Models\Rated', 'rated_id', 'id');
    }
    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }
    public function genre()
    {
        return $this->belongsTo('App\Models\Genre', 'genre_id', 'id');
    }

    public function movie_genre()
    {
        return $this->belongsToMany(Genre::class, 'ci_movie_genre', 'movie_id', 'genre_id');
    }


}
