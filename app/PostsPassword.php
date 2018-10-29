<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostsPassword extends Model
{
    public $timestamps = false;

    protected $table = 'posts_password';
    protected $fillable = ['password'];
    protected $hidden = ['password'];
    protected $dates = [];
}
