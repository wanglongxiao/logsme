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
		'g' => '无聊搞笑',
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
				'type' => 'click',
				'name' => 'menu1',
				'key' => 'click_event1',
			],
			[
				'name' => 'menu2',
				'sub_button' => [
					[
						'type' => 'view',
						'name' => 'yahoo',
						'url' => 'http://yahoo.com',
					],
					[
						'type' => 'view',
						'name' => 'google',
						'url' => 'http://google.com',
					],
				],
			],
			[
				'type' => 'view',
				'name' => '9gag',
				'url' => 'http://9gag.com',
			],				
		],
	],

];
