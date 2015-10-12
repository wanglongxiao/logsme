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

Route::get('/howtoplayvideo',  function(){
	return view('fanqiang');
});


Route::any('/admin/fetch', 'PostController@fetchPost');

Route::get('/admin/edit/{id}', 'PostController@getPostToEdit');

Route::post('/admin/create', 'PostController@createPost');
	
Route::post('/admin/update', 'PostController@updatePost');

Route::get('/admin/delete/{id}', 'PostController@deletePost');

Route::get('/admin/bookmarklet',  function(){
	return view('bookmarklet');
});


Route::get('/auth/facebook', 'Auth\AuthController@redirectToProvider');
Route::get('/auth/facebook/callback', 'Auth\AuthController@handleProviderCallback');


Route::any('/weixinapi/wxhandling', 'WeixinController@handleWechat');

