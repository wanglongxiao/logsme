<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::get('/', 'PostController@getHome');

Route::get('/list', 'PostController@getList');

Route::get('/tag/{id}/{type?}', 'PostController@getTagList');

Route::get('/post/{id}', 'PostController@getPost');

Route::get('/edit/{id}', 'PostController@getPostToEdit');

Route::get('/delete/{id}', 'PostController@deletePost');

Route::any('/fetch', 'PostController@fetchPost');

Route::post('/create', 'PostController@createPost');
	
Route::post('/update', 'PostController@updatePost');

Route::get('/bookmarklet',  function(){
	return view('bookmarklet');
});

Route::get('/howtoplayvideo',  function(){
	return view('fanqiang');
});

Route::any('/weixinapi/wxhandling', 'WeixinController@handleWechat');
