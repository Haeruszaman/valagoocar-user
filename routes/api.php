<?php

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

$api = $app->make(Dingo\Api\Routing\Router::class);

$api->version('v1', function ($api) {
    
    $api->post('/auth/login', [
        'uses' => 'App\Http\Controllers\Auth\AuthController@postLogin',
    ]);
    $api->post('/auth/recover', [
        'uses' => 'App\Http\Controllers\Auth\AuthController@postRecover',
    ]);

    $api->post('/auth/register', [
        'uses' => 'App\Http\Controllers\UsersController@postRegister',
    ]);
    $api->post('/auth/request_email', [
        'uses' => 'App\Http\Controllers\UsersController@request_email',
    ]);
    $api->post('/auth/request_phone', [
        'uses' => 'App\Http\Controllers\UsersController@request_phone',
    ]);
    $api->get('/auth/verify_email/{secretcode}', [
        'uses' => 'App\Http\Controllers\UsersController@verify_email',
    ]);
    $api->patch('/auth/verify_phone', [
        'uses' => 'App\Http\Controllers\UsersController@verify_phone',
    ]);
    $api->post('/auth/reset_password', [
        'uses' => 'App\Http\Controllers\UsersController@reset_password',
    ]);
    $api->post('/auth/request_forgot_password', [
        'uses' => 'App\Http\Controllers\UsersController@request_forgot_password',
    ]);
    $api->post('/auth/forgot_password/{secretcode}', [
        'uses' => 'App\Http\Controllers\UsersController@forgot_password',
    ]);

    $api->group([
        'middleware' => 'api.auth',
        
    ], function ($api) {
        $api->get('/', [
            'uses' => 'App\Http\Controllers\APIController@getIndex',
        ]);
        $api->get('/auth/user', [
            'uses' => 'App\Http\Controllers\Auth\AuthController@getUser',
        ]);
        $api->patch('/auth/refresh', [
            'uses' => 'App\Http\Controllers\Auth\AuthController@patchRefresh',
        ]);
        $api->delete('/auth/invalidate', [
            'uses' => 'App\Http\Controllers\Auth\AuthController@deleteInvalidate',
        ]);

//USERS
        $api->get('/profiles', [
            'uses' => 'App\Http\Controllers\UsersController@index',
        ]);
        $api->get('/profile/{username}', [
            'uses' => 'App\Http\Controllers\UsersController@show',
        ]);

        $api->put('/me/update', [
            'uses' => 'App\Http\Controllers\UsersController@update',
        ]);
        $api->patch('/me/update/status', [
            'uses' => 'App\Http\Controllers\UsersController@update_status',
        ]);
        $api->patch('/me/changepassword', [
            'uses' => 'App\Http\Controllers\UsersController@change_password',
        ]);
        $api->post('/me/logout', [
            'uses' => 'App\Http\Controllers\UsersController@logout',
        ]);
        $api->post('/me/upload/image', [
            'uses' => 'App\Http\Controllers\UsersController@uploadImage',
        ]);

//MASTER CAR
        $api->get('/car', [
            'uses' => 'App\Http\Controllers\M_carController@index',
        ]);
        $api->get('/car/{id}', [
            'uses' => 'App\Http\Controllers\M_carController@show',
        ]);

//MASTER CITY
        $api->get('/city', [
            'uses' => 'App\Http\Controllers\M_cityController@index',
        ]);
        $api->get('/city/{id}', [
            'uses' => 'App\Http\Controllers\M_cityController@show',
        ]);

//SERVICE
        $api->get('/service', [
            'uses' => 'App\Http\Controllers\ServiceController@index',
        ]);
        $api->get('/service/{code}', [
            'uses' => 'App\Http\Controllers\ServiceController@show',
        ]);

//ORDER
        $api->get('/order', [
            'uses' => 'App\Http\Controllers\OrderController@index',
        ]);
        $api->get('/order/{code}', [
            'uses' => 'App\Http\Controllers\OrderController@show',
        ]);
        $api->post('/order/store', [
            'uses' => 'App\Http\Controllers\OrderController@store',
        ]);
        $api->post('/order/{code}/upload/image', [
            'uses' => 'App\Http\Controllers\OrderController@uploadImage',
        ]);
        
    });
});