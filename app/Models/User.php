<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
	protected $table = 'user';
	protected $fillable = array('id','openid','nickname','sex','lastpush_time','role','status','groupid');
}
