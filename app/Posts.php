<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Posts extends Model
{
    use SoftDeletes;
    protected $table = 'posts';
    protected $fillable = ['title', 'revision_id', 'category_id'];
    protected $hidden = ['deleted_at', 'revision', 'category'];
    protected $dates = [];

    public function revision()
    {
        return $this->belongsTo('App\Revisions', 'revision_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Categories');
    }

    public function revisions()
    {
        return $this->hasMany('App\Revisions', 'post_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Comments', 'post_id');
    }
}
