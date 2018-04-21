<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/','StaticPagesController@home')->name('home');
Route::get('/help','StaticPagesController@help')->name('help');
Route::get('/about','StaticPagesController@about')->name('about');

//用户注册页面
Route::get('signup','UsersController@create')->name('signup');
//资源路由 遵循RESTful架构为用户资源生成路由
Route::resource('users','UsersController');

//显示登录页面
Route::get('login','SessionsController@create')->name('login');
//创建新会话（登录）
Route::post('login','SessionsController@store')->name('login');
//销毁会话（退出登录）
Route::delete('logout','SessionsController@destory')->name('logout');

Route::get('signup/confirm/{token}','UserController@confirmEmail')->name('confirm_email');

//Auth::routes();



//忘记密码

//显示重置密码的邮箱发送页面
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
//邮箱发送重新设链接
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
//密码更新页面
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
//执行密码更新操作
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

Route::resource('statuses','StatusesController',['only' => ['store','destroy']]);

//显示关注人
Route::get('users/{user}/followings','UsersController@followings')->name('users.followings');
//显示粉丝
Route::get('users/{user}/followers','UsersController@followers')->name('users.followers');

//实现关注
Route::post('/users/followers/{user}','FollowersController@store')->name('followers.store');
//实现取消关注
Route::delete('/users/followers/{user}','FollowersController@destroy')->name('followers.destroy');


//秒杀测试

Route::group(['prefix' => 'orders'],function(){

      Route::get('/spike','OrderController@spike');
      Route::get('/spike/run','OrderController@run');

});
