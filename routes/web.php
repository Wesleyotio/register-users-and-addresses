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



$router->group(['prefix' => 'api'], function () use ($router) {

    
    $router->post('/register', ['as' => 'register.user', 'uses' => 'AuthController@register']);
    $router->post('/login', ['as' => 'login.user', 'uses' => 'AuthController@login']);
    
    
    $router->group(['middleware' => 'auth:api'], function () use ($router) {
        $router->get('/me', ['as' => 'me.user', 'uses' => 'AuthController@me']);
        $router->get('/refresh', ['as' => 'refresh.user', 'uses' => 'AuthController@refresh']);
        $router->post('/logout', ['as' => 'logout.user', 'uses' => 'AuthController@logout']);
        
        
        $router->get('/user', ['as' => 'index.user', 'uses' => 'UserController@index']);
        $router->put('/user/update', ['as' => 'update.user', 'uses' => 'UserController@update']);
        $router->delete('/user/delete', ['as' => 'delete.user', 'uses' => 'UserController@delete']);

        $router->get('/user/address[/{id}]', ['as' => 'index.address', 'uses' => 'AddressController@index']);
        $router->post('/user/address/create', ['as' => 'create.address', 'uses' => 'AddressController@create']);
        $router->put('/user/address/update/{id}', ['as' => 'update.address', 'uses' => 'AddressController@update']);
        $router->delete('/user/address/delete/{id}', ['as' => 'delete.address', 'uses' => 'AddressController@delete']);

    });
});

