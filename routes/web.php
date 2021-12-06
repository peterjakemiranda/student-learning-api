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
    'middleware' => 'cors'
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@register');
    Route::post('password/reset_request', 'RequestPasswordController@sendResetLinkEmail');
    Route::get('password/reset', ['as' => 'password.reset', 'uses' => 'ResetPasswordController@reset']);
    Route::get('refresh', 'AuthController@refresh');

    Route::group([
        'middleware' => 'auth',
    ], function ($router) {
        Route::get('courses', 'CourseController@index');
        Route::post('courses', 'CourseController@store');
        Route::get('courses/{id}', 'CourseController@show');
        Route::put('courses/{id}', 'CourseController@update');
        Route::delete('courses/{id}', 'CourseController@destroy');

        Route::get('activity', 'ActivityController@index');
        Route::post('activity', 'ActivityController@store');
        Route::get('activity/{id}', 'ActivityController@show');
        Route::post('activity/{id}', 'ActivityController@update');
        Route::delete('activity/{id}', 'ActivityController@destroy');

        Route::post('activity/{id}/answer', 'ActivityAnswerController@store');
        Route::post('activity/{id}/answer/{answerId}/score', 'ActivityAnswerController@score');

        Route::get('quizzes', 'QuizController@index');
        Route::post('quizzes', 'QuizController@store');
        Route::get('quizzes/{id}', 'QuizController@show');
        Route::post('quizzes/{id}', 'QuizController@update');
        Route::delete('quizzes/{id}', 'QuizController@destroy');
        Route::put('quizzes/{id}/toggle', 'QuizController@toggle');

        Route::post('quizzes/{id}/answer', 'QuizAnswerController@store');
        Route::post('quizzes/{id}/score', 'QuizAnswerController@score');

        Route::get('quiz_questions', 'QuizQuestionController@index');
        Route::post('quiz_questions', 'QuizQuestionController@store');
        Route::get('quiz_questions/{id}', 'QuizQuestionController@show');
        Route::post('quiz_questions/{id}', 'QuizQuestionController@update');
        Route::delete('quiz_questions/{id}', 'QuizQuestionController@destroy');

        Route::get('students', 'StudentController@index');
        Route::post('students/invite', 'StudentController@invite');
        Route::post('students/add', 'StudentController@add');

        Route::get('announcements', 'AnnouncementController@index');
        Route::post('announcements', 'AnnouncementController@store');
        Route::get('announcements/{id}', 'AnnouncementController@show');
        Route::post('announcements/{id}', 'AnnouncementController@update');
        Route::delete('announcements/{id}', 'AnnouncementController@destroy');

        Route::get('bookmarks', 'BookmarkController@index');
        Route::post('bookmarks/{id}', 'BookmarkController@bookmark');
        Route::delete('bookmarks/{id}', 'BookmarkController@destroy');

        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::get('account', 'AccountController@show');
        Route::put('account', 'AccountController@update');
        Route::put('account/token', 'AccountController@storeToken');

        Route::get('users', 'UserController@index');
        Route::post('users', 'UserController@store');
        Route::get('users/{id}', 'UserController@show');
        Route::put('users/{id}', 'UserController@update');
        Route::delete('users/{id}', 'UserController@destroy');

        Route::get('notifications', 'NotificationController@index');
        Route::get('notifications/count', 'NotificationController@count');
        Route::post('notifications/{id}/read', 'NotificationController@read');
    });
});
