<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Revisions extends Model
{
    protected $table = 'revisions';
    protected $fillable = ['title', 'content', 'user_id', 'post_id'];
    const UPDATED_AT = null;

    public function author()
    {
        return $this->belongsTo('App\Users', 'user_id');
    }
}
