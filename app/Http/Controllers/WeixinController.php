<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Sunra\PhpSimple\HtmlDomParser;
use Cache;
use Config;
use Log;
use App\Models\Post;
use App\Models\User;
use App\Models\Wxmedia;
use App\Libraries\Weixin\Wechat;
use App\Libraries\Weixin\Api;

class WeixinController extends Controller
{
	
	
	// Post count of Per Day Msg (exclude the featured Post)
	const ITEMS_PER_SUBMIT = 3;
	// Keep N newsids on Weixin side
	const ITEMS_KEEP_ON_WEIXIN = 50;
	
	/*
	// 这是使用了Memcached来保存access_token
	$m = new Memcached();
	$m->addServer('localhost', 11211);
	*/

	/**
	 * create wechat object
	 */
	public static function getWechat()
	{	
		// wechat模块 - 处理用户发送的消息和回复消息
		$wechat = new Wechat(array(     
		    'appId' => Config::get("weixin.appid"), 
			// Token(令牌)
		    'token' =>  Config::get("weixin.wxtoken"),
			// EncodingAESKey(消息加解密密钥)
		    'encodingAESKey' => Config::get("weixin.wxaeskey")
		));
		return $wechat;
	}

	/**
	 * create api object
	 */
	public static function getApi()
	{
		// api模块 - 包含各种系统主动发起的功能
		$api = new Api(
		    array(
		        'appId' => Config::get("weixin.appid"),
		        'appSecret' => Config::get("weixin.appsecret"),
		        'get_access_token' => function() {
		            // 用户需要自己实现access_token的返回
		            return Cache::get('access_token');
		        },
		        'save_access_token' => function($token) {
		            // 用户需要自己实现access_token的保存
		        	$minutes = 7200 / 60;
		            Cache::put('access_token', $token, $minutes);
		        }
		        /*
		        'get_jsapi_ticket' => function() use ($m) {
		            // 可选：用户需要自己实现jsapi_ticket的返回（若使用get_jsapi_config，则必须定义）
		            return $m->get('jsapi_ticket');
		        },
		        'save_jsapi_ticket' => function($jsapi_ticket) use ($m) {
		            // 可选：用户需要自己实现jsapi_ticket的保存（若使用get_jsapi_config，则必须定义）
		            $m->set('jsapi_ticket', $jsapi_ticket, 0);
		        }
		        */
		    )
		);

		return $api;
	}
	
	/**
	 * Responds to handle wechat Msgs
	 */
	public function handleWechat()
	{
		$wechat = self::getWechat();
		
	    // 获取微信消息
	    $msg = $wechat->serve();
	    
	
	    // 默认消息
	    $default_msg = "/微笑 多谢您的关注 \n\n［海外视频精选］每天发布最新最有趣的视频和网文，更多内容请访问 ".env("DOMAINNAME")." \n如播放遇到问题，请点击［海外视频］-［播放须知］\n\n请多多转发呦..";
	    
	    // 用户关注微信号后 - 回复用户普通文本消息
	    if ($msg->MsgType == 'event' && $msg->Event == 'subscribe') {
	        $wechat->reply($default_msg);
	        exit();
	    }
	    
	    // 用户回复1 - 回复文本消息
	    if ($msg->MsgType == 'text' && $msg->Content == 'help') {
	        $wechat->reply("系统帮助：\n请访问 ".env("DOMAINNAME"));
	        /* 也可使用这种数组方式回复
	        $wechat->reply(array(
	            'type' => 'text',
	            'content' => 'hello world!'
	        ));
	        */
	        exit();
	    }
	    
	    // 管理员用户回复CMD - 触发 每日图文群发事件
	    // gogo all , 群发全部
	    // gogo test , 群发到测试组
	    //Log::error('openid: '.$msg->FromUserName);
	    if ($msg->FromUserName == Config::get("weixin.adminopenid") && $msg->MsgType == 'text' && stripos($msg->Content, 'gogo') !== false) {
	    	$cmdarr = explode(" ", $msg->Content);
	    	if (isset($cmdarr) && is_array($cmdarr) && count($cmdarr) >= 2) {
		    	if ($cmdarr[1] == "all") {
		    		self::sendPushMsg("", "all", false);
		    		Log::error('Sent news to all users. ');
		    	} else if ($cmdarr[1] == "test") {
		    		self::sendPushMsg("", Config::get("weixin.wxtestergroup"), false);
		    		Log::error('Sent news to test group. ');
		    	}
	    	}
	    	exit();
	    }
	    
	    // 默认回复默认信息
	    $wechat->reply($default_msg);
	}

	/**
	 * Sync user table from Weixin.
	 * if $forceUpdate == true, force sync from remote to local
	 */
	public function syncUser($forceUpdate = FALSE)
	{
		$api = self::getApi();
	
		// TODO: handle getMore Users loop, If having so many users
	
		// get All Users from local DB user table
		$localUserOpenids = array();
		$terms = array();
		$localUsers = UserController::getUserByFilter($terms);
		//var_dump($localUsers[0]["attributes"]);die();
		foreach ($localUsers as $localUser){
			array_push($localUserOpenids, $localUser["attributes"]["openid"]);
		}
	
		// get Weixin User List
		$remoteUsers = $api->get_user_list();
		$usertotalcount = $remoteUsers[1]->total;
		$userbatchcount = $remoteUsers[1]->count;
		$usenextopenid = $remoteUsers[1]->next_openid;
		//var_dump($remoteUsers[1]->data);die();
		$remoteUserOpenids = $remoteUsers[1]->data->openid;
	
		// local yes but remote no, delete user by openid
		foreach(array_diff ($localUserOpenids, $remoteUserOpenids) as $openid){
			UserController::deleteUser($openid);
		}
	
		if ($forceUpdate) {
			// force sync from remote to local
			foreach($remoteUserOpenids as $openid){
				$res = $api->get_user_info($openid);
				// subscribe_time from int to timestamp
				$res[1]->subscribe_time = date("Y-m-d H:i:s", $res[1]->subscribe_time);
				UserController::createUser($res[1]);
			}
				
		} else {
			// remote yes but local no, create user by openid
			foreach(array_diff ($remoteUserOpenids, $localUserOpenids) as $openid){
				$res = $api->get_user_info($openid);
				// subscribe_time from int to timestamp
				$res[1]->subscribe_time = date("Y-m-d H:i:s", $res[1]->subscribe_time);
				UserController::createUser($res[1]);
			}
		}
	}

	/**
	 * Create media to Weixin
	 * 新增略缩图/图片/音频素材 , $type in ['thumb,'image,'voice']
	 * createMedia($url, $type);
	 * 新增视频素材 , $type in ['video']
	 * createMedia($url, $type, '视频素材的标题', '视频素材的描述');
	 * return, array($mediaid, $mediaurl)
	 */
	public static function createMedia($remoteurl, $type = 'thumb', $vidtitle = '', $viddesc = '' )
	{
		$api = self::getApi();
		
		try {
		
			$tmpFile = self::copyResizeRemoteImage($remoteurl);
			
			//check filesize is < 1M (1048576 bytes) , Weixin Limitation
			if (filesize($tmpFile) <= 1000000) {
				
				if ($type == 'video') {
					list($err, $res) = $api->add_material($type, $tmpFile, $vidtitle, $viddesc);
				} else {
					list($err, $res) = $api->add_material($type, $tmpFile);
					//var_dump($res);
					//var_dump($err);
				}
				if ($err == 'NULL') {
					return FALSE;
				} else {
					if ($res != NULL && $res != 'NULL') {
						$res = (array)$res;
						//echo $res['media_id']." \n";
						return array($res['media_id'], $res["url"]);
					} else {
						return FALSE;
					}
				}
				
			} else {
				Log::error('Media size > 1M, skipped: '.$remoteurl);
				return FALSE;
			}
		} catch (\Exception $e) {
			Log::error ("Create media failed : ".$remoteurl." , ".$e->getMessage());
			return FALSE;
		}
	}
	
	/**
	 * Copy and resize image from remote url. Weixin recommendation : <1M , 900 * 500
	 * 
	 */
	public static function copyResizeRemoteImage($remoteurl, $maxWidth = 800)
	{
		$allowMinRatio = 0.25;
		$ext = "";
		
		// judge include Chinese or not
		if (preg_match("/[\x7f-\xff]/", $remoteurl)) {
			$includeChinese = true;
		
		}else{
			$includeChinese = false;
		}
		
		// Not include Chinese, judge Url is valid
		// can not handle url like : http://www.teepr.com/wp-content/uploads/2015/10/模特兒.png
		if(!$includeChinese && filter_var($remoteurl, FILTER_VALIDATE_URL) === FALSE)
		{  
			return FALSE;
		} else{
			try {
				//$ext = pathinfo($remoteurl, PATHINFO_EXTENSION);
				$x = getimagesize($remoteurl);
				
				if ($x != false && is_array($x)) {
					switch ($x['mime']) {
						case "image/gif":
							$ext = "gif";
							break;
						case "image/jpeg":
							$ext = "jpg";
							break;
						case "image/png":
							$ext = "png";
							break;
					}
				}
			} catch (\Exception $e) {
				Log::error ("Got ERR while fetching ".$remoteurl." : ".$e->getMessage());
				return FALSE;
			}
			if ($ext == "") return FALSE;
			
			$tmpFile = "/tmp/".time().".".$ext;
			//echo "Img Url: ".$remoteurl."\n";
			//echo "tmpFile: ".$tmpFile."\n";
			
			try {
				// only resize jpg and png
				if ($ext != "gif") {
					list($width, $height) = getimagesize($remoteurl);
					$ratio = $width / $height;
					// normal case
					if ($width <= $maxWidth) {
							$new_width = $width;
							$new_height = $height;
					} else {
							$new_width = $maxWidth;
							$new_height = round ($new_width / $ratio);
					}
					// long image
					if ($ratio < $allowMinRatio) {
						$new_width = round ($new_width / 2);
						$new_height =  round ($new_height / 2);			
					}
						
					$image_p = imagecreatetruecolor($new_width, $new_height);
					if ($ext == "png") {
						$image = imagecreatefrompng($remoteurl);
					} else if ($ext == "jpg") {
						$image = imagecreatefromjpeg($remoteurl);
					}
					imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
					if ($ext == "png") {
						imagepng($image_p, $tmpFile);
					} else if ($ext == "jpg") {
						imagejpeg($image_p, $tmpFile, 80);
					}
					imagedestroy($image_p);
				} else {
					copy($remoteurl, $tmpFile);
				}
			} catch (\Exception $e) {
			    Log::error ($remoteurl." : ".$e->getMessage());
			    copy($remoteurl, $tmpFile);
			}
		}
		
		return $tmpFile;
	}
	
	/**
	 * Convert content to Weixin allowed format and upload the media to Weixin domains
	 * 
	 * return: array(content, array(mediaids))
	 */
	public static function convertToWxContent($content)
	{
		$mediaids = array();	
		/*
		// delete the \n of mysql
		$content = str_replace(chr(10)," ",$content);
		$content = str_replace(chr(13)," ",$content);
		*/	
		$html = HtmlDomParser::str_get_html($content);
		
		// upload remote images to Weixin domain and replce into the html
		foreach ($html->find("img") as $image) {
			$imageUrl = $image->getAttribute("src");
			$createMediaRes = self::createMedia($imageUrl, 'image');
			if ($createMediaRes != FALSE) {
				$wxMediaid = $createMediaRes[0];
				$wxImageUrl = $createMediaRes[1];
				$mediaids[] = $wxMediaid;
				$image->setAttribute("src", $wxImageUrl);
			}
		}
		
		$content = $html->save();
		// Weixin API not support " , need to switch to '
		$content = str_replace("\"", "'", $content);
		
		return array($content, $mediaids);
	}
	
	/**
	 * add QRcode to content
	 */
	public static function addQrcode($sourcedomain = "")
	{
		$firstlvldomainarray = array("com","net","org","biz","edu","gov","info","mobi");
		$str = "";
		if($sourcedomain != "") {
			$arr = explode(".", $sourcedomain);
			$idx = count($arr) - 2;
			if ($idx >= 0) {
				// handle com.cn / net.tw, 2 lvl domain cases
				if (in_array($arr[$idx], $firstlvldomainarray)) {
					$idx = $idx - 1;
				}
				
				$str = $str."<p style='text-align:right;color:#d3d3d3;'>来源：".$arr[$idx]."</p>";
			}
		}
		$str = $str."<p><img src='".Config::get("weixin.qrcodeurl")."' class='img-responsive'></p>";
		return $str;
	}

	/**
	 * Submit news to Weixin
	 */
	public function submitNews()
	{
		$api = self::getApi();
		
		// if have issent=0 and inpreview=1 news already, no need to gen news again
		$terms = array(
				'issent' => 0,
				'inpreview' => 1
		);
		$res = WxmediaController::getWxmediaByFilter($terms, 1, 0, 'created_at', 'asc');
		if (!$res->isEmpty() && isset($res[0]['attributes'])) {
			$newsid = $res[0]['attributes']['newsid'];
			// Send preview to @AW
			$res = $api->sendPreview ($newsid, Config::get("weixin.adminopenid"));
			$api->send(Config::get("weixin.adminopenid"), "ERR：仍有未发送图文，请发送");
		} else {
		
			$api->send(Config::get("weixin.adminopenid"), "INFO：生成图文并上传中..");
			
			// get 1 featured post from DB Post table
			$data = array();
			$terms = array(
					'isapproved' => 1,
					'ispublished' => 0,
					'isfeatured' => 1,
					'hasvideo' => 0
			);
			$data1 = PostController::getListByFilter($terms, 1, 0, 'created_at', 'asc');
			if (!$data1->isEmpty() && isset($data1[0]['attributes']))
				array_push($data, $data1[0]['attributes']);
			// get non-featured post from DB Post table
			$terms = array(
					'isapproved' => 1,
					'ispublished' => 0,
					'isfeatured' => 0,
					'hasvideo' => 0
			);
			$data2 = PostController::getListByFilter($terms, self::ITEMS_PER_SUBMIT, 0, 'created_at', 'asc');
			if (!$data2->isEmpty() && isset($data2[0]['attributes'])) {
				foreach ($data2 as $data2item) {
					array_push($data, $data2item['attributes']);
				}
			}	
			
			// Upload thumb, submit news and get newsId
			$postids = array();
			$thumbids = array();
			$mediaids = array();
			$news = array();
			$hashids = new \Hashids\Hashids("", Config::get("weixin.minhashlength"));
				
			foreach ($data as $item) {
				$html = "";
				$itemMediaids = array();
				$contentRes = self::convertToWxContent($item ['content']);
				$html = $contentRes[0];
				$html = $html.self::addQrcode($item ['sourcedomain']);
				$itemMediaids = $contentRes[1];
				// Make a Weixin allowed content format. 
				// Keep <p><img><br><video>only, upload all images to Weixin domains
				// upload thumb
				$createMediaRes = self::createMedia($item ['ogimage']);
				// Judge thumb upload failed or not
				if ($createMediaRes != FALSE) {
					$thumbmediaid = $createMediaRes[0];
					$sourcepageid = $hashids->encode($item ['id']);
					// gen newsitem
					$newsitem = array(
							'title' => $item ['title'],
							'thumb_media_id' => $thumbmediaid,
							'author' => '',
							'digest' => $item ['description'],
							'show_cover_pic' => 0,
							'content' => $html,
							// update later by config
							'content_source_url' => "http://".env('DOMAINNAME')."/post/".$sourcepageid
					);
					
					array_push($postids, $item ['id']);
					array_push($thumbids, $thumbmediaid);
					array_push($news, $newsitem);
					$mediaids = array_merge($mediaids, $itemMediaids);
				} else Log::error("Upload thumb failed on post : ".$item ['id']);
			}
			
			// submit news
			$res = $api->add_news($news);
			//var_dump($res);
			Log::error("Creating News .. ");
			
			// handle result
			if ($res[0] == "NULL") {
				// rollback uploaded thumbnails
				foreach ($thumbids as $thumbmediaid) {
					$api->del_material($thumbmediaid);
				}
				foreach ($mediaids as $mediaid) {
					$api->del_material($mediaid);
				}
				return FALSE;
				
			} else {
				$newsid = $res[1]->media_id;
				// Update wxmedia table
				WxmediaController::createWxmedia($newsid, $postids, $thumbids, $mediaids);
				Log::error("Creating Wxmedia record .. ");
				// Update post table 'isublished'
				$terms = array(
					'ispublished' => 1
				);
				PostController::updateByIds($terms, $postids);
				Log::error("Updateing Posts to ispublished .. ");
				// Send preview to @AW
				$res = $api->sendPreview ($newsid, Config::get("weixin.adminopenid"));
				$api->send(Config::get("weixin.adminopenid"), "INFO：上传图文完成，请预览");
				// Update Wxmedia to 'inpreview = 1'
				$data = array(
					'newsid' => $newsid,
					'inpreview' => 1
				);
				$res = WxmediaController::updateWxmedia($data);
				Log::error("Updateing Wxmedia to inpreview .. ");
				return $newsid;
			}
		}
	}
	
	/**
	 * Send msg to user
	 * get not-sent newsID from Wxmedia table 
	 * -> sent 
	 * -> update Wxmedia record 
	 * -> update user lastpush_time 
	 * -> tell AW daily pushed by Weixin
	 * 
	 * $newsid = "", push the oldest "issent = 0" news to Weixin
	 * $groupid = "all", push to all users ; tester groupid , Config::get("weixin.wxtestergroup")
	 * $triggerFromCron = true, cronjob triggered once per day ; set to false can be trigger maually
	 */
	public function sendPushMsg($newsid = "", $groupid = "all", $triggerFromCron = true)
	{
		$api = self::getApi();
		$pushed = false;
		$canPush = false;
		$day =  date('Y-m-d');
		
		// judge issent today or not
		$terms = array(
				'issent' => 1,
				'sent_at' => $day." 00:00:00"
		);
		$res = WxmediaController::getWxmediaByFilter($terms);
		if ($res->isEmpty()) {
			$canPush = true;
		}
		
		// if not given newsid, get the oldest 'isset = 0' newsid
		if (!isset($newsid) || $newsid == "") {
			$terms = array(
					'issent' => 0,
					'inpreview' => 1
			);
			$res = WxmediaController::getWxmediaByFilter($terms, 1, 0, 'created_at', 'asc');
			if (!$res->isEmpty() && isset($res[0]['attributes'])) {
				$newsid = $res[0]['attributes']['newsid'];
			} else {
				// send Msg to @AW, no news can be pushed
				if ($canPush) $api->send(Config::get("weixin.adminopenid"), "ERR：没有可用图文");
				return false;
			}
		}
		
		if ($triggerFromCron) {
			// 0-6 => Sun-Sat
			$weekday = date('w');
			$schedule = Config::get("weixin.schedule.".$weekday);
			//echo $day." ".$schedule." \n";
			$date = new \DateTime(null, new \DateTimeZone(Config::get("weixin.localtimezone")));
			$currentTime = ($date->getTimestamp() + $date->getOffset());
			$scheduledTime = strtotime($day." ".$schedule);
			//Log::error("newsid: ".$newsid." | canpush: ".$canPush." | day: ".$day." | schedule: ".$schedule." | currenttime: ".$currentTime." | scheduletime: ".$scheduledTime);
			
			// judge reach the sending schedule
			if ($canPush && $currentTime > $scheduledTime) {
				$res = $api->sendMsgToGroup ($newsid, $groupid);
				$pushed = true;
				Log::error ("auto push done");
			} else {
				return false;
			}
		} else {
			$res = $api->sendMsgToGroup ($newsid, $groupid);
			$pushed = true;
			Log::error ("manual push done");
		}
		
		// if push msg to 'all', update wxmedia table. Set this newsid can not be pushed again
		if ($pushed) {
			if ($groupid == "all") {
				$data = array(
						'newsid' => $newsid,
						'inpreview' => 0,
						'issent' => 1,
						'sent_at' => $day." 00:00:00"
				);
				$res = WxmediaController::updateWxmedia($data);
				// default : 0000-00-00 00:00:00
			}
			
			//update user table lastpush_time
			$data = array(
				'lastpush_time' => $day." 00:00:00"
			);
			if ($groupid == "all") {
				UserController::updateByGroups($data);
				echo "group all \n";
			} else {
				UserController::updateByGroups($data, array($groupid));
				echo "group $groupid \n";
			}
			
			// send Msg to @AW, daily push done
			$api->send(Config::get("weixin.adminopenid"), "INFO：图文消息已发送，".$day);
		}
		return true;
	}

	/**
	 * Weixin housekeeping, as 5000 mediaids limitation
	 */
	public function wxHouseKeeping($count = self::ITEMS_KEEP_ON_WEIXIN)	
	{
		$terms = array(
			'issent' => 1,
			'hideonwx' => 0
		);
		$res = WxmediaController::getWxmediaByFilter($terms, 100, $count, 'sent_at', 'desc');
		if (!$res->isEmpty() && isset($res[0]['attributes']['newsid'])) {
			foreach ($res as $newitem) {
				$newsid = $newitem['attributes']['newsid'];
				WxmediaController::HideWxmedia($newsid);
			}
		}
		return true;
	}
	
	/**
	 * create wechat menu (delete existed one first)
	 */
	public function createMenu()
	{
		$api = self::getApi();
		
		/*
		//自定义菜单查询接口
		$api->get_menu();
		//获取自定义菜单配置接口	
		$api->get_selfmenu();
		*/	
			
		// 删除自定义菜单
		$api->delete_menu();
		
		// 重新创建自定义菜单
		// Max 3 1st lvl menu , max 5 2nd lvl menu
		// Json_encode the menu array with Chinese directly, will trigger Weixin 40033 Err
		//$menujson = json_encode(Config::get("weixin.menu"));
		
		$menujson = '
		{
			"button":[ 
		';
		// 1st
		$menujson = $menujson.'
		{
			"name":"今日热门",
		    "sub_button":[
		';
		$i = 0;
		$count = count(Config::get("weixin.tags"));
		foreach(Config::get("weixin.tags") as $key => $val) {
			$i++;
			$menujson = $menujson.'
				{
			    	"type":"view",
			        "name":"'.$val.'",
			        "url":"http://'.env("DOMAINNAME").'/tag/'.$key.'/img"
			    }
			';
			if ($i < $count) $menujson = $menujson.',';
		}
		$menujson = $menujson.'
			]
		},
		';
		// 2nd
		$menujson = $menujson.'
		{
			"name":"海外视频",
		    "sub_button":[
				{
			    	"type":"view",
			        "name":"最新视频",
			        "url":"http://'.env('DOMAINNAME').'/list?type=vid"
			    },
			    {
			    	"type":"view",
					"name":"播放须知",
			        "url":"http://'.env('DOMAINNAME').'/howtoplayvideo"
			    }
			]
		},
		';
		// 3rd
		$menujson = $menujson.'
		{
			"type":"view",
			"name":"精彩回顾",
		    "url":"'.Config::get("weixin.historymsgurl").'"
		}
		';
		$menujson = $menujson.'
			]
		}
		';
		//echo $menujson;
		
		$res = $api->create_menu($menujson);
		var_dump($res);
		
		return true;
	}
	
	/*
	// 获取素材总数
	$api->get_material_count();
	
	// 获取图文素材
	// 返回图文的json字符串
	list($err, $data) = $api->get_material('BZ-ih-dnjWDyNXjai6i6sdvxOoXOHr9wO0pgMhcZR8g');
	var_dump(json_decode($data));
	
	// 更新永久素材
	list($err, $res) = $api->update_news('BZ-ih-dnjWDyNXjai6i6sZp22xhHu6twVYKNPyl77Ms', array(
			'title' => '标题',
			'thumb_media_id' => 'BZ-ih-dnjWDyNXjai6i6sdvxOoXOHr9wO0pgMhcZR8g',
			'author' => '作者',
			'digest' => '图文消息的摘要',
			'show_cover_pic' => true,
			'content' => '图文消息的具体内容',
			'content_source_url' => 'http://www.diandian.com/'
	), 1);
	*/

}