<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Posts extends Model
{
    use SoftDeletes;
    protected $table = 'posts';
    protected $fillable = ['title', 'user_id', 'content', 'category_id'];
    protected $dates = ['deleted_at'];
}
