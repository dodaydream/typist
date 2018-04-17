<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Posts extends Model
{
    use SoftDeletes;
    protected $table = 'posts';
    protected $fillable = ['title', 'revision_id', 'category_id', 'updated_at'];
    protected $hidden = ['deleted_at'];
    public $timestamps = false;

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
}
