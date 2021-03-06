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
		'1' => '09:00:00',
		'2' => '19:00:00',
		'3' => '19:00:00',
		'4' => '19:00:00',
		'5' => '21:00:00',
		'6' => '09:00:00',
	],

		
	/*
	 |--------------------------------------------------------------------------
	 | Site/Html Settings
	 |--------------------------------------------------------------------------
	 */
		
	'defaulthtmltitle' => 'LOGSME 海外精选',
	
	'defaulthtmlkeywords' => '海外,娱乐,搞笑,美女,时尚,视频,动图,可爱,动物,最新,科技,恶搞,oversea,global,girl,video,fun,funny,geeky,cute,gif',
	
	'defaulthtmldescription' => '海外视频,酷炫组图,可爱动物,科技潮物,恶搞GIF,每天更新,刷屏一分钟开心一整天',

	'defaulthtmlogimage' => 'http://'.env("DOMAINNAME").'/img/320_320.png',
		
	'minhashlength' => 8,
	
	'localtimezone' => 'Asia/Shanghai',
	
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
		'boyandgirl' => '型男美女',
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
