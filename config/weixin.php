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
		'b' => '娱乐搞笑',
		'c' => '世界奇闻',
		'd' => '可爱动物',
		'e' => '生活时尚',
	],
	

	/*
	 |--------------------------------------------------------------------------
	 | Weixin Related
	 |--------------------------------------------------------------------------
	 */
	
	'historymsgurl' => 'http://mp.weixin.qq.com/mp/getmasssendmsg?__biz=MzA3MDMyNDI1Nw==#wechat_webview_type=1&wechat_redirect',
	'qrcodeurl' => 'https://mmbiz.qlogo.cn/mmbiz/6xo6WHaicfzaGszuNnfPyJdvxLws9icdvEb228roB4aA445FkBMqrtDeKqUDM4zGRPbAvwJcY8l6EablGnFSLqww/0?wx_fmt=png',
		
];
