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
$router->group(['prefix' => 'api'], function () use ($router) {

    $router->get('/', function () use ($router) {
        return $router->app->version();
    });

    $router->post('/register', ['as' => 'register.user', 'uses' => 'AuthController@register']);
    $router->post('/login', ['as' => 'login.user', 'uses' => 'AuthController@login']);
    $router->post('/logout', ['as' => 'logout.user', 'uses' => 'AuthController@logout']);


    $router->group(['middleware' => 'auth:api'], function () use ($router) {
        $router->get('/me', ['as' => 'me.user', 'uses' => 'AuthController@me']);
        $router->get('/refresh', ['as' => 'refresh.user', 'uses' => 'AuthController@refresh']);

    });
});

