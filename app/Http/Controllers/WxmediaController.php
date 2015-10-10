<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Config;
use Log;
use App\Models\Wxmedia;
use App\Libraries\Weixin\Wechat;
use App\Libraries\Weixin\Api;

class WxmediaController extends Controller
{	
	
	const WXMEDIA_PER_PAGE = 50;
	
	/**
     * Get data from DB
     */
    public static function getWxmediaByFilter($terms, $limit = self::WXMEDIA_PER_PAGE, $offset = 0, $orderby = 'id', $order = 'desc')
    {
    	$data = array();
    	
    	$query = Wxmedia::select();
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
     * Get one Wxmedia info
     */
    public static function getWxmedia($newsid)
    {
    	$wxmedia = array();
    	if ($newsid != null || $newsid != "") {
    		$term = array(
    			'newsid' => $newsid
    		);
    		$res = self::getWxmediaByFilter($term);
    		if (!$res->isEmpty() && isset($res[0]['attributes']))
			$wxmedia = $res[0]['attributes'];
    	}
    	return $wxmedia;
    }
    
    /**
     * Delete one Wxmedia
     */
    public static function deleteWxmedia($newsid)
    {
    	if ($newsid != null || $newsid != "") {
    		
    		// get this wxmedia and remove it from Weixin
    		$res = self::getWxmedia($newsid);
    		if (isset($res) && is_array($res)) {
    			$postids = (array) json_decode($res['postids']);
    			$thumbids = (array) json_decode($res['thumbids']);
    			$mediaids = (array) json_decode($res['mediaids']);
    			
	    		// remove mediaids (thumb / image / news) from Weixin
    			$api = WeixinController::getApi();
    			
    			foreach ($thumbids as $thumbmediaid) {
    				$api->del_material($thumbmediaid);
    			}
    			foreach ($mediaids as $mediaid) {
    				$api->del_material($mediaid);
    			}
    			$api->del_material($newsid);
	    		
	    		// recover the posts relates this newsid to 'ispublished' false
    			$terms = array(
    					'ispublished' => 0
    			);
	    		PostController::updateByIds($terms, $postids);
	    		
	    		// remove the record from table 'wxmedia'
	    		$wxmedia = Wxmedia::where(['newsid' => $newsid]);
	    		$wxmedia->delete();
	    		
    			return true;
    		} else {
    			return false;
    		}
    	} else {
    		return false;
    	}
    }
    
    /**
     * Hide newsid on Weixin. For housekeeping, as 5000 mediaids limitation
     */
    public static function HideWxmedia($newsid)
    {
    	if ($newsid != null || $newsid != "") {
    		
	    	// get this wxmedia and remove it from Weixin
	    	$res = self::getWxmedia($newsid);
	   		if (isset($res) && is_array($res)) {
	   			$postids = (array) json_decode($res['postids']);
	   			$thumbids = (array) json_decode($res['thumbids']);
    			$mediaids = (array) json_decode($res['mediaids']);
	    			 
	    		// remove mediaids (thumb / image / news) from Weixin
	    		$api = WeixinController::getApi();
	   			 
	   			foreach ($thumbids as $thumbmediaid) {
	   				$api->del_material($thumbmediaid);
	   			}
    			foreach ($mediaids as $mediaid) {
	    			$api->del_material($mediaid);
	    		}
	    		$api->del_material($newsid);
	       
	    		// update the record from table 'wxmedia'
	    		$data = array(
					'newsid' => $newsid,
					'hideonwx' => 1
				);
				self::updateWxmedia($data);
	    	} 
	    } 
    	return true;
    }
    
    /**
     * Delete Multi rows
     */
    public static function deleteByIds($newsids)
    {
    	if (isset($newsids) && is_array($newsids)) {
    		foreach ($newsids as $newsid){
    			self::deleteWxmedia($newsid);
    		}
    		return true;
    	} else {
    		return false;
    	}
    }
    
	/**
	 * Add record for created Wxmedia news
	 */
	public static function createWxmedia($newsid, $postids, $thumbids, $mediaids)
	{
		$wxmedia = Wxmedia::firstOrNew(['newsid' => $newsid]);
		 
		$wxmedia->newsid = $newsid;
		$wxmedia->postids = json_encode($postids);
		$wxmedia->thumbids = json_encode($thumbids);
		$wxmedia->mediaids = json_encode($mediaids);
		
		$wxmedia->save();
	}

    /**
     * Update Wxmedia Info
     */
    public static function updateWxmedia($data)
    {
    	if (isset($data['newsid']) && $data['newsid'] != "") {
    		Wxmedia::where('newsid', $data['newsid'])->update($data);
    		return true;
    	} else {
    		return false;
    	}
    }
    
    /**
     * Update Multi rows
     */
    public static function updateByNewsids($terms, $newsids)
    {
    	if (isset($terms) && is_array($terms) && isset($newsids) && is_array($newsids)) {
    		Wxmedia::whereIn('newsid', $newsids)->update($terms);
    		return true;
    	} else {
    		return false;
    	}
    }

}