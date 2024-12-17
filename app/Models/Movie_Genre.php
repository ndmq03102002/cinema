<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Movie_Genre extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ci_movie_genre';
    protected $fillable = ['movie_id', 'genre_id'];
    
}
