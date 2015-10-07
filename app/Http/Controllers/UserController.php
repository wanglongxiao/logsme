<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Config;
use App\Models\User;

class UserController extends Controller
{
	
	const USER_PER_PAGE = 5000;
	
	/**
     * Get data from DB
     */
    public static function getUserByFilter($terms, $limit = self::USER_PER_PAGE, $offset = 0, $orderby = 'id', $order = 'desc')
    {
    	$data = array();
    	
    	$query = User::select();
    	if (isset($terms) && is_array($terms) ) {
    		foreach($terms as $column => $value)
    		{
    			$query->where($column, '=', $value);
    		}
    	}
    	$query->orderBy($orderby, $order);
    	$query->take($limit)->skip($offset);
	    $data = $query->get();
    	return $data;
    }
    
    /**
     * Get one user info
     */
    public static function getUser($openid)
    {
    	$user = array();
    	if ($openid != null || $openid != "") {
    		$term = array(
    				'openid' => $openid
    		);
    		$res = self::getUserByFilter($term);
    		if (!$res->isEmpty() && isset($res[0]['attributes']))
    			$user = $res[0]['attributes'];
    	}
    	return $user;
    }
    
    /**
     * Delete User
     */
    public static function deleteUser($openid)
    {
    	if ($openid != null || $openid != "") {
    		$user = User::where(['openid' => $openid]);
    		$user->delete();
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * Delete Multi rows
     */
    public static function deleteByIds($openids)
    {
    	if (isset($openids) && is_array($openids)) {
    		User::whereIn('openid', $openids)->delete();
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * Create user
     */
    public static function createUser($data)
    {
    	$user = User::firstOrNew(['openid' => $data->openid]);
  		
    	$user->openid = $data->openid;
    	$user->nickname = $data->nickname;
    	$user->sex = $data->sex;
    	$user->language = $data->language;
    	$user->headimgurl = $data->headimgurl;
    	$user->groupid = $data->groupid;
    	$user->subscribe_time = $data->subscribe_time;
    	
    	if (isset($data->unionid))
    		$user->unionid = $data->unionid;
    	
    	//set ADMIN role
    	// `role` tinyint(1)  unsigned NOT NULL DEFAULT 1. 0 => ADMIN , 1 => NORMALUSER (default)
    	if ($data->openid == Config::get("weixin.adminopenid"))
    		$user->role = 0;
    	 
    	$user->save();
    
    	return true;
    }

    /**
     * Update User Info
     */
    public static function updateUser($data)
    {
    	if ($data->openid != null && $data->openid != "") {
    		User::where('openid', $data->openid)->update($data);
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * Update Multi rows by ids
     */
    public static function updateByOpenids($terms, $openids)
    {
    	if (isset($terms) && is_array($terms) && isset($openids) && is_array($openids)) {
    		User::whereIn('openid', $openids)->update($terms);
    		return true;
    	} else {
    		return false;
    	}
    }

    /**
     * Update Multi rows by groupids
     * $groupids = "all", then update all records
     */
    public static function updateByGroups($terms, $groupids = "all")
    {
    	if (isset($terms) && is_array($terms)) {
    		if (is_array($groupids)) {
    			User::whereIn('groupid', $groupids)->update($terms);
    		} else if ($groupids == "all") {
    			$user = new User;
    			$user->update($terms);
    		}
    		return true;
    	} else {
    		return false;
    	}
    }

}