<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FragmentComments {
    protected $table = 'fragment_comments';
    protected $fillable = ['commenter_ip', 'content', 'fragment_id'];
    const UPDATED_AT = null;
}
