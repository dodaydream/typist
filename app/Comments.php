<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $table = 'comments';
    protected $fillable = ['commenter_ip', 'content', 'post_id'];
    protected $hidden = ['commenter_ip'];
    const UPDATED_AT = null;
}
