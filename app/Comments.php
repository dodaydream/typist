<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comments {
    protected $table = 'post_comments';
    protected $fillable = ['commenter_ip', 'content', 'post_id'];
    const UPDATED_AT = null;
}
