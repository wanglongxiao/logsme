<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wxmedia extends Model
{
    //
	protected $table = 'wxmedia';
	protected $fillable = array('id','newsid','created_at','sent_at','inpreview','issent');
}
