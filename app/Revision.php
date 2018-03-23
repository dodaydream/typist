<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
	protected $table = 'revision';
	protected $fillable = ['title', 'content', 'users_id'];
	public $timestamps = false;
}
