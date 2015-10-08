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

Route::get('/', 'PostController@getList');

Route::get('/post/{id}', 'PostController@getPost');

Route::any('/fetch', 'PostController@fetchPost');

Route::get('/delete/{id}', 'PostController@deletePost');

Route::get('/bookmarklet',  function(){
   return view('bookmarklet');
});

Route::post('/create', 'PostController@createPost');
	
Route::post('/update', 'PostController@updatePost');

Route::any('/weixin/wxhandling', 'WeixinController@handleWechat');
