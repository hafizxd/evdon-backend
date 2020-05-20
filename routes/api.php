<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function() {

    Route::group(['prefix' => 'auth'], function() {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
        Route::post('logout', 'AuthController@logout');
    });

    Route::group(['prefix' => 'users'], function() {
        Route::get('/', 'UserController@showUsers');

        Route::put('profile', 'UserController@update');
        Route::put('profile/profile-picture', 'UserController@updateProfilePicture');

        Route::get('{id}/profile', 'UserController@show');
        
        Route::post('{id}/follows', 'UserController@follow');
        Route::delete('{id}/follows', 'UserController@unfollow');

        Route::get('{id}/following', 'UserController@showFollowing');
        Route::get('{id}/followers', 'UserController@showFollowers');
        
        Route::get('{id}/posts', 'PostController@showUserPosts');
    });

    Route::get('timeline-posts', 'PostController@showTimelinePosts');

    Route::group(['prefix' => 'posts'], function() {
        Route::post('/', 'PostController@store');
        Route::put('{id}', 'PostController@update');
        Route::delete('{id}', 'PostController@delete');

        Route::get('{id}/likes', 'PostController@showLikes');
        Route::post('{id}/likes', 'PostController@likePost');
        Route::delete('{id}/likes', 'PostController@unlikePost');

        Route::post('{id}/reports', 'PostController@reportPost');
        Route::delete('{id}/reports', 'PostController@cancelReportPost');
    });

});