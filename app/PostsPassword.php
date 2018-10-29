<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostsPassword extends Model
{
    protected $table = 'posts_password';
    protected $fillable = ['password'];
    protected $hidden = ['password'];
    protected $dates = [];
}
