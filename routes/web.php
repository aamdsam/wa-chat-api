<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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


$router->post('login','UserController@login');
$router->post('register','UserController@register');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->post('refresh-token','UserController@refreshToken');

    $router->group(['prefix' => 'message'], function () use ($router) {
        $router->get('outbox','MessageController@outbox');
        $router->post('input','MessageController@input');
        $router->put('status-update/{id}','MessageController@statusUpdate');

        $router->get('template','MessageController@getTemplate');
        $router->post('template','MessageController@setTemplate');

        $router->post('check-message','ListenController@checkMessage');
    });
});
