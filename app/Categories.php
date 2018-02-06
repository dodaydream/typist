<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categories';
    protected $fillable = ['name', 'description'];

    public function hasManyPosts()
    {
        return $this->hasMany('Posts', 'category_id', 'id');
    }
}
