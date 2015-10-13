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
	 | Settings
	 |--------------------------------------------------------------------------
	 */
		
	'minhashlength' => 8,
	//'translateapi' => 'https://translate.googleapis.com/translate_a/single',
	'translateapi' => 'http://openapi.baidu.com/public/2.0/bmt/translate',
	
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
		'boyandgirl' => '男神女神',
		'coolstory' => '世界奇闻',
		'funnyent' => '搞笑娱乐',
		'cuteanimal' => '可爱动物',
		'lifestyle' => '时尚生活',
	],
	

	/*
	 |--------------------------------------------------------------------------
	 | Weixin Related
	 |--------------------------------------------------------------------------
	 */
	
	'historymsgurl' => 'http://mp.weixin.qq.com/mp/getmasssendmsg?__biz=MzA3MDMyNDI1Nw==#wechat_webview_type=1&wechat_redirect',
	'qrcodeurl' => 'https://mmbiz.qlogo.cn/mmbiz/6xo6WHaicfzaGszuNnfPyJdvxLws9icdvEb228roB4aA445FkBMqrtDeKqUDM4zGRPbAvwJcY8l6EablGnFSLqww/0?wx_fmt=png',
	'promourl' => '<a href="weixin://contacts/profile/hotfeed">点击关注 [海外视频精选]</a>',
		
];
