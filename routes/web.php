<?php

use App\Models\User;

/* @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

Route::group([
    'prefix' => 'api',
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@register');
    Route::post('password/reset_request', 'RequestPasswordController@sendResetLinkEmail');
    Route::get('password/reset', ['as' => 'password.reset', 'uses' => 'ResetPasswordController@reset']);
    Route::get('refresh', 'AuthController@refresh');

    Route::group([
        'middleware' => 'auth',
    ], function ($router) {
        Route::get('chapters', 'ChapterController@index');
        Route::post('chapters', 'ChapterController@store');
        Route::get('chapters/{id}', 'ChapterController@show');
        Route::put('chapters/{id}', 'ChapterController@update');
        Route::delete('chapters/{id}', 'ChapterController@destroy');

        Route::get('sections', 'SectionController@index');
        Route::post('sections', 'SectionController@store');
        Route::get('sections/{id}', 'SectionController@show');
        Route::put('sections/{id}', 'SectionController@update');
        Route::delete('sections/{id}', 'SectionController@destroy');

        Route::get('bookmarks', 'BookmarkController@index');
        Route::post('bookmarks/{id}', 'BookmarkController@bookmark');
        Route::delete('bookmarks/{id}', 'BookmarkController@destroy');

        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::get('account', 'AccountController@show');
        Route::put('account', 'AccountController@update');
        // Route::post('content', 'AuthController@me')->middleware('roles:' . User::ROLE_ADMIN);
    });
});
