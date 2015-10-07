<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
	protected $table = 'post';
	protected $fillable = array('id','url','tags','isfeatured','ispublished','isapproved','hasvideo');
}
