<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Revisions extends Model
{
    protected $table = 'revisions';
    protected $fillable = ['title', 'content', 'user_id', 'post_id'];
    protected $hidden = ['author'];
    const UPDATED_AT = null;
    protected $dates = [];

    public function author()
    {
        return $this->belongsTo('App\Users', 'user_id');
    }
}
