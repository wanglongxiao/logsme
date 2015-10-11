<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Weixin Msg Push Time
    |--------------------------------------------------------------------------
    |
    | 0-6 => Sun-Sat
    |
    */
	
	'schedule' => [
	    '0' => '09:00:00',
		'1' => '08:00:00',
		'2' => '20:00:00',
		'3' => '20:00:00',
		'4' => '20:00:00',
		'5' => '22:00:00',
		'6' => '09:00:00',
	],

	/*
	|--------------------------------------------------------------------------
	| Weixin API Related
	|--------------------------------------------------------------------------
	*/
		
	// AppID
	'appid' => env('WX_APPID', ''),

	// AppSecret
	'appsecret' => env('WX_APPSECRET', ''),
	
	// Weixin admin openid
	'adminopenid' => env('WX_ADMINOPENID', ''),

	// Weixin callback token
	'wxtoken' => env('WX_TOKEN', ''),
		
	// Weixin callback AESKey
	'wxaeskey' => env('WX_AESKEY', ''),
	
	// Weixin callback AESKey
	'wxtestergroup' => env('WX_TESTERGROUP', 100),


	/*
	 |--------------------------------------------------------------------------
	 | Weixin Tags
	 |--------------------------------------------------------------------------
	 */
	
	'tags' => [
		'a' => '女神男神',
		'b' => '世界奇闻',
		'c' => '可爱动物',
		'd' => '酷炫艺术',
		'e' => '电影娱乐',
		'f' => '时尚生活',
		'g' => '爆笑恶搞',
		'h' => '撼动人心',
	],
		

	/*
	 |--------------------------------------------------------------------------
	 | Weixin Menu
	 |--------------------------------------------------------------------------
	 */
	
	'menu' => [
		'button' => [
			[
				'name' => '海外视频',
				'sub_button' => [
					[
						'type' => 'view',
						'name' => '最新视频',
						'url' => 'http://'.env('DOMAINNAME').'/list?type=vid',
					],
					[
						'type' => 'view',
						'name' => '播放须知',
						'url' => 'http://'.env('DOMAINNAME').'/howtoplayvideo',
					],
				],
			],
			[
				'name' => '今日热门',
				'sub_button' => [
					[
						'type' => 'view',
						'name' => '女神男神',
						'url' => 'http://'.env('DOMAINNAME').'/tag/a/img',
					],
					[
						'type' => 'view',
						'name' => '世界奇闻',
						'url' => 'http://'.env('DOMAINNAME').'/tag/b/img',
					],
					[
						'type' => 'view',
						'name' => '可爱动物',
						'url' => 'http://'.env('DOMAINNAME').'/tag/c/img',
					],
					[
						'type' => 'view',
						'name' => '酷炫艺术',
						'url' => 'http://'.env('DOMAINNAME').'/tag/d/img',
					],
					[
						'type' => 'view',
						'name' => '电影娱乐',
						'url' => 'http://'.env('DOMAINNAME').'/tag/e/img',
					],
					[
						'type' => 'view',
						'name' => '时尚生活',
						'url' => 'http://'.env('DOMAINNAME').'/tag/f/img',
					],
					[
						'type' => 'view',
						'name' => '爆笑恶搞',
						'url' => 'http://'.env('DOMAINNAME').'/tag/g/img',
					],
					[
						'type' => 'view',
						'name' => '撼动人心',
						'url' => 'http://'.env('DOMAINNAME').'/tag/h/img',
					],
				],
			],
			[
				'type' => 'view',
				'name' => '精彩回顾',
				'url' => 'http://mp.weixin.qq.com/mp/getmasssendmsg?__biz=MzA3MDMyNDI1Nw==#wechat_webview_type=1&wechat_redirect',
			],				
		],
	],

];
