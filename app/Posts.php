<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Posts extends Model
{
    protected $table = 'posts';
    protected $fillable = ['revision_id', 'category_id', 'updated_at'];
	public $timestamps = false;

	public function revision()
	{
		return $this->hasOne('App\Revision');
	}
}
